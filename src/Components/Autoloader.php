<?php
/*
 * The Autoloader finds all component.php files in theme
 * foldeder and require them
 */
namespace DigitalUnited\Components;

class Autoloader
{
    private $files;

    public function __construct()
    {
        $files = $this->indexFiles();
    }

    private function indexFiles()
    {
        $this->files = $this->getComponentFilePaths();
    }

    private function getComponentFilePaths()
    {
        $componentsBasePath = $this->getComponentsPath();
        $componentFolders = scandir($componentsBasePath);

        // Removes . and .. from scandir result
        $componentFolders = array_slice($componentFolders, 2);

        return array_map(function ($componentFolder) use ($componentsBasePath){
            return implode('/', [$componentsBasePath,$componentFolder,'component.php']);
        }, $componentFolders);
    }


    private function getComponentsPath()
    {
        $themeDir = get_template_directory();
        $componentsBasePath = $themeDir.'/'.'components';

        return $componentsBasePath;
    }


    public function requireFiles()
    {
        foreach ($this->files as $file) {
            require_once $file;
        }
    }
}
?>
