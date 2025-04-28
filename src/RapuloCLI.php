<?php
namespace Rapulo\CLI;

use Rapulo\CLI\Commands\CreateProjectCommand;
use Rapulo\CLI\Commands\MakeFeatureCommand;
use Rapulo\CLI\Commands\MakeComponentCommand;
use Rapulo\CLI\Commands\MakeControllerCommand;
use Rapulo\CLI\Commands\MakeModelCommand;
use Rapulo\CLI\Commands\MakeMiddlewareCommand;
use Rapulo\CLI\Commands\MakeMigrationCommand;
use Rapulo\CLI\Commands\MakeSeedCommand;
use Rapulo\CLI\Commands\MigrateCommand;
use Rapulo\CLI\Commands\SeedCommand;
use Rapulo\CLI\Commands\ServeCommand;

class RapuloCLI
{
    private $commands = [
        'create:project' => CreateProjectCommand::class,
        'make:feature' => MakeFeatureCommand::class,
        'make:component' => MakeComponentCommand::class,
        'make:controller' => MakeControllerCommand::class,
        'make:model' => MakeModelCommand::class,
        'make:middleware' => MakeMiddlewareCommand::class,
        'make:migration' => MakeMigrationCommand::class,
        'make:seed' => MakeSeedCommand::class,
        'migrate' => MigrateCommand::class,
        'seed' => SeedCommand::class,
        'serve' => ServeCommand::class,
    ];

    public function run()
    {
        global $argv;
        $command = $argv[1] ?? '';

        if (!isset($this->commands[$command])) {
            $this->showHelp();
            exit(1);
        }

        $commandClass = $this->commands[$command];
        $commandInstance = new $commandClass();
        $commandInstance->execute(array_slice($argv, 2));
    }

    private function showHelp()
    {
        echo <<<EOT
Rapulo CLI (Enhanced PHP Framework)
Available commands:
  create:project <name>            Create a new Rapulo project
  make:feature <name>              Create a new feature directory
  make:component <name> <feature>  Create a new component in the specified feature
  make:controller <name> <feature> Create a new controller in the specified feature
  make:model <name> <feature>      Create a new model in the specified feature
  make:middleware <name>           Create a new middleware
  make:migration <name>            Create a new database migration
  make:seed <name>                 Create a new database seed
  migrate                         Run all database migrations
  seed                            Run all database seeds
  serve [host:port]                Run the development server
EOT;
    }
}
?>