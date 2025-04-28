<?php
namespace Rapulo\CLI\Templates;

class SeedTemplates
{
    public function getSeedTemplate(string $name): string
    {
        return <<<EOT
<?php
use Rapulo\\Core\\ORM;

class {$name}Seed {
    public function run() {
        // Add seeding logic here
    }
}
EOT;
    }
}
?>