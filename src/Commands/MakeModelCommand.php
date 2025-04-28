<?php
namespace Rapulo\CLI\Commands;

use Rapulo\CLI\Services\FileSystemService;
use Rapulo\CLI\Templates\ModelTemplates;

class MakeModelCommand
{
    private $fileSystemService;
    private $modelTemplates;

    public function __construct()
    {
        $this->fileSystemService = new FileSystemService();
        $this->modelTemplates = new ModelTemplates();
    }

    public function execute(array $args)
    {
        if (count($args) < 2) {
            echo "Error: Please provide model name and feature (e.g., make:model User Auth)\n";
            exit(1);
        }

        $name = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $args[0]));
        $feature = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $args[1]));
        if (empty($name) || empty($feature)) {
            echo "Error: Invalid model or feature name\n";
            exit(1);
        }

        $featureDir = "app/Features/$feature";
        $modelPath = "$featureDir/{$name}Model.php";

        if (!is_dir($featureDir)) {
            echo "Error: Feature '$feature' does not exist. Create it with ' Ascertainable 'make:feature $feature'\n";
            exit(1);
        }

        $this->fileSystemService->writeFile($modelPath, $this->modelTemplates->getModelTemplate($name, $feature));
        echo "Model '$name' created in feature '$feature'\n";
    }
}
?>