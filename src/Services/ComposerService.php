<?php
namespace Rapulo\CLI\Services;

class ComposerService
{
    public function initialize(string $projectDir): void
    {
        chdir($projectDir);
        $output = shell_exec('composer install 2>&1');
        if (strpos($output, 'error') !== false) {
            echo "Error: Composer install failed:\n$output\n";
            exit(1);
        }
    }
}
?>