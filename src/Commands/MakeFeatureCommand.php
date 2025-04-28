<?php
namespace Rapulo\CLI\Commands;

use Rapulo\CLI\Services\FileSystemService;

class MakeFeatureCommand
{
    private $fileSystemService;

    public function __construct()
    {
        $this->fileSystemService = new FileSystemService();
    }

    public function execute(array $args)
    {
        if (count($args) < 1) {
            echo "Error: Please provide feature name (e.g., make:feature Dashboard)\n";
            exit(1);
        }

        $feature = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $args[0]));
        if (empty($feature)) {
            echo "Error: Invalid feature name\n";
            exit(1);
        }

        $featureDir = "app/Features/$feature";

        if (is_dir($featureDir)) {
            echo "Error: Feature '$feature' already exists\n";
            exit(1);
        }

        $this->fileSystemService->createDirectory($featureDir);
        echo "Feature '$feature' created\n";
    }
}
?>