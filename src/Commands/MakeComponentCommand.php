<?php
namespace Rapulo\CLI\Commands;

use Rapulo\CLI\Services\FileSystemService;
use Rapulo\CLI\Templates\ComponentTemplates;

class MakeComponentCommand
{
    private $fileSystemService;
    private $componentTemplates;

    public function __construct()
    {
        $this->fileSystemService = new FileSystemService();
        $this->componentTemplates = new ComponentTemplates();
    }

    public function execute(array $args)
    {
        if (count($args) < 2) {
            echo "Error: Please provide component name and feature (e.g., make:component Dashboard Dashboard)\n";
            exit(1);
        }

        $name = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $args[0]));
        $feature = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $args[1]));
        
        if (empty($name) || empty($feature)) {
            echo "Error: Invalid component or feature name\n";
            exit(1);
        }

        $featureDir = "app/Features/$feature";
        $componentPath = "$featureDir/{$name}Component.php";
        $viewPath = "$featureDir/{$name}.view.php";

        if (!is_dir($featureDir)) {
            echo "Error: Feature '$feature' does not exist. Create it with 'make:feature $feature'\n";
            exit(1);
        }

        $this->fileSystemService->writeFile($componentPath, $this->componentTemplates->getComponentTemplate($name, $feature));
        $this->fileSystemService->writeFile($viewPath, $this->componentTemplates->getViewTemplate($name));

        echo "Component '$name' created in feature '$feature'\n";
    }
}
?>