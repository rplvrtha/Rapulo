<?php
namespace Rapulo\CLI\Templates;

class ComponentTemplates
{
    public function getComponentTemplate(string $name, string $feature): string
    {
        return <<<EOT
<?php
namespace Rapulo\\Features\\$feature;
use Rapulo\\Core\\Component;

class {$name}Component extends Component {
    public function view() {
        parent::view();
    }
}
EOT;
    }

    public function getViewTemplate(string $name): string
    {
        return <<<EOT
<div>
    <h1>$name Component</h1>
</div>
EOT;
    }
}
?>