<?php
namespace Rapulo\CLI\Services;

class FileSystemService
{
    public function writeFile(string $path, string $content): void
    {
        if (file_exists($path)) {
            echo "Error: File '$path' already exists\n";
            exit(1);
        }

        if (pathinfo($path, PATHINFO_EXTENSION) === 'json') {
            $decoded = json_decode($content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "Error: Invalid JSON in '$path': " . json_last_error_msg() . "\n";
                echo "Content: $content\n";
                exit(1);
            }
        }

        file_put_contents($path, $content);
    }

    public function createDirectory(string $path, int $permissions = 0755): void
    {
        mkdir($path, $permissions, true);
    }
}
?>