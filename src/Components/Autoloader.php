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
    private $componentClassNames;

    public function __construct()
    {
        $this->componentsBasePath = $this->getComponentsBasePath();
        $this->files = $this->getComponentFilePaths();
        $this->componentClassNames = $this->generateComponentClassNames();
    }

    private function getComponentsBasePath()
    {
        $themeDir = get_template_directory();
        $componentsBasePath = $themeDir.'/'.'components';

        return $componentsBasePath;
    }

    private function getComponentFilePaths()
    {
        return array_map(function ($componentFolder){
            return implode('/', [$this->componentsBasePath,$componentFolder,'component.php']);
        }, $this->getComponentFolderNames());
    }

    private function getComponentFolderNames()
    {
        $componentFolders = scandir($this->componentsBasePath);

        // Removes . and .. from scandir result
        $componentFolders = array_slice($componentFolders, 2);

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
        while($line = fgets($fileHandler)) {
            if (!$nameSpace) {
                if(preg_match('/namespace\s+([^;]+)/i', $line, $matches)){
                    $nameSpace = $matches[1];
                }
            }

            if(preg_match('/class\s+([^\s]+)/i', $line, $matches)){
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

    public function registerComponents() {
        foreach($this->componentClassNames as $className) {
            $component = new $className;
            $component->register();
        }
    }
}
?>
