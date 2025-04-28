<?php
namespace Rapulo\CLI\Templates;

class ModelTemplates
{
    public function getModelTemplate(string $name, string $feature): string
    {
        return <<<EOT
<?php
namespace Rapulo\\Features\\$feature;
use Rapulo\\Core\\ORM;

class {$name}Model extends ORM {
    public function __construct() {
        parent::__construct('$name');
    }
}
EOT;
    }
}
?>