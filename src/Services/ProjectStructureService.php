<?php
namespace Rapulo\CLI\Services;

class ProjectStructureService
{
    private $directories = [
        'app/Features/Auth',
        'app/Core',
        'app/Config',
        'app/Middleware',
        'app/Migrations',
        'app/Seeds',
        'public/assets',
        'resources/views',
        'resources/styles',
        'routes',
        'storage/cache',
        'storage/queue',
        'storage/logs',
    ];

    private $fileSystemService;

    public function __construct()
    {
        $this->fileSystemService = new FileSystemService();
    }

    public function createDirectories(string $baseDir): void
    {
        foreach ($this->directories as $dir) {
            $this->fileSystemService->createDirectory("$baseDir/$dir");
        }
    }
}
?>