<?php

namespace Rapulo\CLI\Commands;

use Rapulo\CLI\Services\FileSystemService;
use Rapulo\CLI\Templates\MiddlewareTemplates;

class MakeMiddlewareCommand
{
    private $fileSystemService;
    private $middlewareTemplates;

    public function __construct()
    {
        $this->fileSystemService = new FileSystemService();
        $this->middlewareTemplates = new MiddlewareTemplates();
    }

    public function execute(array $args)
    {
        if (count($args) < 1) {
            echo "Error: Please provide middleware name (e.g., make:middleware Admin)\n";
            exit(1);
        }

        $name = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $args[0]));
        if (empty($name)) {
            echo "Error: Invalid middleware name\n";
            exit(1);
        }

        $middlewarePath = "app/Middleware/{$name}Middleware.php";

        $this->fileSystemService->writeFile($middlewarePath, $this->middlewareTemplates->getMiddlewareTemplate($name));
        echo "Middleware '$name' created\n";
    }
}
