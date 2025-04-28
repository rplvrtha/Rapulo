<?php
namespace Rapulo\CLI\Commands;

class ServeCommand
{
    public function execute(array $args)
    {
        $hostPort = $args[0] ?? 'localhost:8000';
        [$host, $port] = explode(':', $hostPort) + [1 => '8000'];

        if (!file_exists('public/index.php')) {
            echo "Error: Not in a Rapulo project directory\n";
            exit(1);
        }

        echo "Starting Rapulo development server at http://$host:$port\n";
        exec("php -S $host:$port -t public");
    }
}
?>