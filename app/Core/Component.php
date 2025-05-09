<?php
namespace Rapulo\Core;

class Component {
    protected $props = [];
    protected $cacheKey;

    public function __construct($props = []) {
        $this->props = $props;
        $this->cacheKey = md5(serialize([get_class($this), $props]));
    }

    public function render() {
        $cached = Cache::get($this->cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        ob_start();
        $this->view();
        $output = ob_get_clean();

        Cache::set($this->cacheKey, $output, 3600);
        return $output;
    }

    protected function view() {
        $view = str_replace('Rapulo\\Features\\', '', get_class($this));
        $view = str_replace('\\', '/', $view);
        $view = strtolower(preg_replace('/([A-Z])/', '-', $view)) . '.view.php';
        $viewPath = __DIR__ . '/../Features/' . $view;
        if (file_exists($viewPath)) {
            extract($this->props);
            require $viewPath;
        } else {
            throw new \Exception("View file '$viewPath' not found");
        }
    }
}