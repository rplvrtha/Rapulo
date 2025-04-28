<?php
namespace Rapulo\CLI\Templates;

class ControllerTemplates
{
    public function getControllerTemplate(string $name, string $feature): string
    {
        return <<<EOT
<?php
namespace Rapulo\\Features\\$feature;

class {$name}Controller {
    public function index() {
        echo 'Welcome to $name Controller';
    }
}
EOT;
    }
}
?>