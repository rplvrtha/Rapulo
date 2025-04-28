<?php
namespace Rapulo\CLI\Commands;

use Rapulo\CLI\Services\FileSystemService;
use Rapulo\CLI\Templates\MigrationTemplates;

class MakeMigrationCommand
{
    private $fileSystemService;
    private $migrationTemplates;

    public function __construct()
    {
        $this->fileSystemService = new FileSystemService();
        $this->migrationTemplates = new MigrationTemplates();
    }

    public function execute(array $args)
    {
        if (count($args) < 1) {
            echo "Error: Please provide migration name (e.g., make:migration create_posts_table)\n";
            exit(1);
        }

        $name = preg_replace('/[^a-zA-Z0-9_]/', '', $args[0]);
        if (empty($name)) {
            echo "Error: Invalid migration name\n";
            exit(1);
        }

        $timestamp = date('Y_m_d_His');
        $migrationPath = "app/Migrations/{$timestamp}_{$name}.php";

        $this->fileSystemService->writeFile($migrationPath, $this->migrationTemplates->getMigrationTemplate($name, $timestamp));
        echo "Migration '$name' created\n";
    }
}
?>