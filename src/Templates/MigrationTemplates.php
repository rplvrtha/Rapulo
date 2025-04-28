<?php
namespace Rapulo\CLI\Templates;

class MigrationTemplates
{
    public function getMigrationTemplate(string $name, string $timestamp): string
    {
        return <<<EOT
<?php
use Rapulo\\Core\\ORM;

class Migration_{$timestamp}_{$name} {
    public function up() {
        \$pdo = (new ORM('migrations'))->getPdo();
        \$pdo->exec("
            -- Add your table schema here
        ");
    }

    public function down() {
        \$pdo = (new ORM('migrations'))->getPdo();
        \$pdo->exec("
            -- Drop your table here
        ");
    }
}
EOT;
    }
}
?>