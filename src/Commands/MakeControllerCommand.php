<?php
namespace Rapulo\CLI\Commands;

use Rapulo\CLI\Services\FileSystemService;
use Rapulo\CLI\Templates\ControllerTemplates;

class MakeControllerCommand
{
    private $fileSystemService;
    private $controllerTemplates;

    public function __construct()
    {
        $this->fileSystemService = new FileSystemService();
        $this->controllerTemplates = new ControllerTemplates();
    }

    public function execute(array $args)
    {
        if (count($args) < 2) {
            echo "Error: Please provide controller name and feature (e.g., make:controller Dashboard Dashboard)\n";
            exit(1);
        }

        $name = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $args[0]));
        $feature = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $args[1]));
        if (empty($name) || empty($feature)) {
            echo "Error: Invalid controller or feature name\n";
            exit(1);
        }

        $featureDir = "app/Features/$feature";
        $controllerPath = "$featureDir/{$name}Controller.php";

        if (!is_dir($featureDir)) {
            echo "Error: Feature '$feature' does not exist. Create it with 'make:feature $feature'\n";
            exit(1);
        }

        $this->fileSystemService->writeFile($controllerPath, $this->controllerTemplates->getControllerTemplate($name, $feature));
        echo "Controller '$name' created in feature '$feature'\n";
    }
}
?>