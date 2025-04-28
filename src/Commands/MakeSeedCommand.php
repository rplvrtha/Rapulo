<?php
namespace Rapulo\CLI\Commands;

use Rapulo\CLI\Services\FileSystemService;
use Rapulo\CLI\Templates\SeedTemplates;

class MakeSeedCommand
{
    private $fileSystemService;
    private $seedTemplates;

    public function __construct()
    {
        $this->fileSystemService = new FileSystemService();
        $this->seedTemplates = new SeedTemplates();
    }

    public function execute(array $args)
    {
        if (count($args) < 1) {
            echo "Error: Please provide seed name (e.g., make:seed PostSeed)\n";
            exit(1);
        }

        $name = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $args[0]));
        if (empty($name)) {
            echo "Error: Invalid seed name\n";
            exit(1);
        }

        $seedPath = "app/Seeds/{$name}Seed.php";

        $this->fileSystemService->writeFile($seedPath, $this->seedTemplates->getSeedTemplate($name));
        echo "Seed '$name' created\n";
    }
}
?>