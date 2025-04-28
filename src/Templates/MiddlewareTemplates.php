<?php
namespace Rapulo\CLI\Templates;

class MiddlewareTemplates
{
    public function getMiddlewareTemplate(string $name): string
    {
        return <<<EOT
<?php
namespace Rapulo\\Middleware;

class {$name}Middleware {
    public function handle() {
        // Add middleware logic here
        return true;
    }
}
EOT;
    }
}
?>