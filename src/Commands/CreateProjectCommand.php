<?php
namespace Rapulo\CLI\Commands;

use Rapulo\CLI\Services\FileSystemService;
use Rapulo\CLI\Services\ProjectStructureService;
use Rapulo\CLI\Services\ComposerService;
use Rapulo\CLI\Templates\ProjectTemplates;

class CreateProjectCommand
{
    private $fileSystemService;
    private $projectStructureService;
    private $composerService;
    private $projectTemplates;

    public function __construct()
    {
        $this->fileSystemService = new FileSystemService();
        $this->projectStructureService = new ProjectStructureService();
        $this->composerService = new ComposerService();
        $this->projectTemplates = new ProjectTemplates();
    }

    public function execute(array $args)
    {
        if (count($args) < 1) {
            echo "Error: Please provide project name (e.g., create:project my-app)\n";
            exit(1);
        }

        $projectName = preg_replace('/[^a-zA-Z0-9_-]/', '', $args[0]);
        if (empty($projectName)) {
            echo "Error: Invalid project name\n";
            exit(1);
        }

        $projectDir = getcwd() . '/' . $projectName;

        if (is_dir($projectDir)) {
            echo "Error: Directory '$projectName' already exists\n";
            exit(1);
        }

        $this->projectStructureService->createDirectories($projectDir);
        $this->createProjectFiles($projectDir, $projectName);
        $this->setupPermissions($projectDir);
        $this->composerService->initialize($projectDir);

        echo "Rapulo project '$projectName' created successfully!\n";
        echo "To start the project:\n";
        echo "  cd $projectName\n";
        echo "  php rapulo migrate\n";
        echo "  php rapulo seed\n";
        echo "  php rapulo serve\n";
    }

    private function createProjectFiles(string $projectDir, string $projectName)
    {
        $files = $this->projectTemplates->getProjectFiles($projectName);
        foreach ($files as $path => $content) {
            $this->fileSystemService->writeFile("$projectDir/$path", $content);
        }
    }

    private function setupPermissions(string $projectDir)
    {
        chmod("$projectDir/rapulo", 0755);
        chmod("$projectDir/storage", 0775);
        chmod("$projectDir/storage/cache", 0775);
        chmod("$projectDir/storage/queue", 0775);
        chmod("$projectDir/storage/logs", 0775);
    }
}
?>