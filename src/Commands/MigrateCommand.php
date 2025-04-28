<?php
namespace Rapulo\CLI\Commands;

class MigrateCommand
{
    public function execute(array $args)
    {
        $migrationDir = 'app/Migrations';
        if (!is_dir($migrationDir)) {
            echo "Error: Migrations directory not found\n";
            exit(1);
        }

        $files = glob("$migrationDir/*.php");
        foreach ($files as $file) {
            require_once $file;
            $className = 'Migration_' . basename($file, '.php');
            if (class_exists($className)) {
                try {
                    $migration = new $className();
                    $migration->up();
                    echo "Applied migration: " . basename($file) . "\n";
                } catch (\Exception $e) {
                    echo "Error applying migration " . basename($file) . ": " . $e->getMessage() . "\n";
                    exit(1);
                }
            }
        }

        echo "All migrations applied successfully\n";
    }
}
?>