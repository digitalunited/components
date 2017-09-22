<?php
/*
 * The Autoloader finds all component.php files in theme
 * foldeder and require them
 */
namespace DigitalUnited\Components;

class Autoloader
{
    private $files;
    private $componentsBasePath;
    private $childComponentsBasePath;
    private $componentClassNames;

    public function __construct()
    {
        $this->componentsBasePath = $this->getComponentsBasePath();
        $this->childComponentsBasePath = $this->getChildComponentsBasePath();
        $this->files = $this->getComponentFilePaths();
        $this->componentClassNames = $this->generateComponentClassNames();
    }

    private function getComponentsBasePath()
    {
        $themeDir = get_template_directory();
        $componentsBasePath = $themeDir.'/'.'components';

        return $componentsBasePath;
    }

    private function getChildComponentsBasePath()
    {
        // Check if child theme is active
        if (get_stylesheet_directory() === get_template_directory()) {
            return false;
        }

        $childThemeDir = get_stylesheet_directory();
        $childComponentsBasePath = $childThemeDir.'/components';

        return $childComponentsBasePath;
    }

    private function getComponentFilePaths()
    {
        $componentFolderNames = $this->getComponentFolderNames();

        // If child theme is active, check for components and replace with base-theme component
        if ($this->childComponentsBasePath) {
            $componentFilePaths = $this->getComponentFilePathsWithChilds($componentFolderNames);
        } else {
            $componentFilePaths = array_map(function ($componentFolder) {
                return implode('/', [$this->componentsBasePath,$componentFolder,'component.php']);
            }, $componentFolderNames);
        }

        return $componentFilePaths;
    }

    private function getComponentFilePathsWithChilds($baseFileFolderNames)
    {
        $childComponentFolderNames = $this->getChildComponentFolderNames();
        $componentFolderNames = array_diff($baseFileFolderNames, $childComponentFolderNames);

        $baseFilePaths = array_map(function ($componentFolder) {
            return implode('/', [$this->componentsBasePath,$componentFolder,'component.php']);
        }, $componentFolderNames);

        $childFilePaths = array_map(function ($componentFolder) {
            return implode('/', [$this->childComponentsBasePath, $componentFolder, 'component.php']);
        }, $childComponentFolderNames);

        return array_merge($baseFilePaths, $childFilePaths);
    }

    private function getComponentFolderNames()
    {
        $componentFolders = [];
        foreach (glob($this->componentsBasePath.'/*', GLOB_ONLYDIR) as $absPathToFolder) {
            $componentFolders[] = basename($absPathToFolder);
        }

        return $componentFolders;
    }

    private function getChildComponentFolderNames()
    {
        $componentFolders = [];
        foreach (glob($this->childComponentsBasePath.'/*', GLOB_ONLYDIR) as $absPathToFolder) {
            if (file_exists(implode('/', [$absPathToFolder, 'component.php']))) {
                $componentFolders[] = basename($absPathToFolder);
            }
        }

        return $componentFolders;
    }

    private function generateComponentClassNames()
    {
        $classNames = [];
        foreach ($this->files as $filePath) {
            $classNames[] = $this->extractClassNameFromFile($filePath);
        }

        return $classNames;
    }

    private function extractClassNameFromFile($filePath)
    {
        $nameSpace = '';
        $className = '';

        $fileHandler = fopen($filePath, 'r');
        while ($line = fgets($fileHandler)) {
            if (!$nameSpace) {
                if (preg_match('/namespace\s+([^;]+)/i', $line, $matches)) {
                    $nameSpace = $matches[1];
                }
            }

            if (preg_match('/class\s+([^\s]+)/i', $line, $matches)) {
                $className = $matches[1];
                break;
            }
        }

        return $nameSpace ? '\\'.$nameSpace.'\\'.$className : $className;
    }


    public function requireFiles()
    {
        foreach ($this->files as $file) {
            require_once $file;
        }
    }

    public function registerComponents()
    {
        foreach ($this->componentClassNames as $className) {
            VcParamProfiler::startTimer();
            $component = new $className;
            $component->register();
            VcParamProfiler::stopTimer($className);
        }
    }
}
