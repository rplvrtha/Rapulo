<?php
namespace Rapulo\CLI\Commands;

class SeedCommand
{
    public function execute(array $args)
    {
        $seedDir = 'app/Seeds';
        if (!is_dir($seedDir)) {
            echo "Error: Seeds directory not found\n";
            exit(1);
        }

        $files = glob("$seedDir/*.php");
        foreach ($files as $file) {
            require_once $file;
            $className = basename($file, '.php');
            if (class_exists($className)) {
                try {
                    $seed = new $className();
                    $seed->run();
                    echo "Applied seed: " . basename($file) . "\n";
                } catch (\Exception $e) {
                    echo "Error applying seed " . basename($file) . ": " . $e->getMessage() . "\n";
                    exit(1);
                }
            }
        }

        echo "All seeds applied successfully\n";
    }
}
?>