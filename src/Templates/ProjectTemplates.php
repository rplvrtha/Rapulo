<?php

namespace Rapulo\CLI\Templates;

class ProjectTemplates
{
    public function getProjectFiles(string $projectName): array
    {
        return [
            'composer.json' => $this->getComposerJson($projectName),
            'public/index.php' => $this->getIndexPhp(),
            'app/Core/Router.php' => $this->getRouterPhp(),
            'app/Core/ORM.php' => $this->getOrmPhp(),
            'app/Core/Cache.php' => $this->getCachePhp(),
            'app/Core/Queue.php' => $this->getQueuePhp(),
            'app/Core/Component.php' => $this->getComponentPhp(),
            'app/Config/app.php' => $this->getAppConfig(),
            'app/Config/database.php' => $this->getDatabaseConfig($projectName),
            'routes/web.php' => $this->getWebRoutes(),
            'routes/api.php' => $this->getApiRoutes(),
            'app/Features/Auth/LoginComponent.php' => $this->getLoginComponent(),
            'app/Features/Auth/Login.view.php' => $this->getLoginView(),
            'app/Features/Auth/AuthController.php' => $this->getAuthController(),
            'app/Features/Auth/UserModel.php' => $this->getUserModel(),
            'app/Middleware/AuthMiddleware.php' => $this->getAuthMiddleware(),
            'app/Migrations/Migration_2025_01_01_create_users_table.php' => $this->getUsersMigration(),
            'app/Seeds/UserSeed.php' => $this->getUserSeed(),
            'rapulo' => $this->getRapuloCli(),
        ];
    }

    private function getComposerJson(string $projectName): string
    {
        return json_encode([
            'name' => "rapulo/$projectName",
            'version' => '1.0.0',
            'autoload' => [
                'psr-4' => [
                    'Rapulo\\' => 'app/',
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function getIndexPhp(): string
    {
        return <<<EOT
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Rapulo\Core\Router;
use Rapulo\Core\Cache;
use Rapulo\Core\Queue;

session_start();

Cache::init(__DIR__ . '/../storage/cache');
Queue::init(__DIR__ . '/../storage/queue');

\$router = new Router();
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';
\$router->dispatch();
EOT;
    }

    private function getRouterPhp(): string
    {
        // Konten lengkap dari Router.php asli
        // Untuk singkat, masukkan konten dari kode asli atau sesuaikan
        return <<<EOT
<?php
namespace Rapulo\Core;

class Router {
    private \$routes = [];
    private \$middleware = [];
    private \$basePath;

    public function __construct() {
        \$this->basePath = rtrim(dirname(\$_SERVER['SCRIPT_NAME']), '/');
    }

    public function get(\$path, \$handler) {
        \$this->addRoute('GET', \$path, \$handler);
    }

    public function post(\$path, \$handler) {
        \$this->addRoute('POST', \$path, \$handler);
    }

    private function addRoute(\$method, \$path, \$handler) {
        \$this->routes[] = [
            'method' => \$method,
            'path' => \$this->compilePath(\$path),
            'handler' => \$handler,
            'middleware' => []
        ];
    }

    public function middleware(\$name, \$middleware) {
        \$this->middleware[\$name] = \$middleware;
    }

    public function group(\$middleware, \$callback) {
        \$prevMiddleware = \$this->middleware;
        \$currentMiddleware = [];
        foreach ((array)\$middleware as \$m) {
            \$currentMiddleware[] = \$m;
            \$this->middleware[\$m] = \$this->middleware[\$m] ?? [];
        }
        foreach (\$this->routes as &\$route) {
            \$route['middleware'] = array_merge(\$route['middleware'], \$currentMiddleware);
        }
        call_user_func(\$callback, \$this);
        \$this->middleware = \$prevMiddleware;
    }

    private function compilePath(\$path) {
        \$path = trim(\$path, '/');
        return preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?<\1>[^/]+)', \$path);
    }

    public function dispatch() {
        \$uri = parse_url(\$_SERVER['REQUEST_URI'], PHP_URL_PATH);
        \$uri = trim(str_replace(\$this->basePath, '', \$uri), '/');
        \$method = \$_SERVER['REQUEST_METHOD'];

        if (isset(\$_GET['debug_routes']) && \$_GET['debug_routes'] == 1) {
            \$this->debugRoutes();
            exit;
        }

        foreach (\$this->routes as \$route) {
            if (\$route['method'] !== \$method) {
                continue;
            }

            if (preg_match('#^' . \$route['path'] . '$#', \$uri, \$matches)) {
                \$this->runMiddleware(\$route['middleware']);
                \$this->callHandler(\$route['handler'], \$matches);
                return;
            }
        }

        \$this->logError("404 Not Found: \$method \$uri");
        http_response_code(404);
        echo '404 Not Found';
    }

    private function runMiddleware(\$middleware) {
        foreach (\$middleware as \$m) {
            if (isset(\$this->middleware[\$m])) {
                \$instance = new \$this->middleware[\$m]();
                \$instance->handle();
            }
        }
    }

    private function callHandler(\$handler, \$params) {
        try {
            if (is_callable(\$handler)) {
                call_user_func(\$handler, \$params);
            } elseif (is_array(\$handler)) {
                [\$controller, \$method] = \$handler;
                \$instance = new \$controller();
                \$instance->\$method(\$params);
            }
        } catch (\Exception \$e) {
            \$this->logError("Handler error: " . \$e->getMessage());
            http_response_code(500);
            echo '500 Internal Server Error';
        }
    }

    private function debugRoutes() {
        echo "<h1>Registered Routes</h1><ul>";
        foreach (\$this->routes as \$route) {
            echo "<li>{\$route['method']} /{\$route['path']} (Middleware: " . implode(', ', \$route['middleware']) . ")</li>";
        }
        echo "</ul>";
    }

    private function logError(\$message) {
        \$logFile = __DIR__ . '/../../storage/logs/app.log';
        \$timestamp = date('Y-m-d H:i:s');
        file_put_contents(\$logFile, "[\$timestamp] \$message
", FILE_APPEND);
    }
}
EOT;
    }

    // Implementasikan method lain sesuai kebutuhan
    private function getOrmPhp(): string
    {
        return <<<EOT
<?php
namespace Rapulo\Core;

class ORM {
    private \$pdo;
    private \$table;
    private \$query = '';
    private \$bindings = [];

    public function __construct(\$table) {
        \$this->table = \$table;
        try {
            \$config = require __DIR__ . '/../Config/database.php';
            \$this->pdo = new \\PDO(
                "{\$config['driver']}:host={\$config['host']};dbname={\$config['database']};charset={\$config['charset']}",
                \$config['username'],
                \$config['password'],
                [\\PDO::ATTR_ERRMODE => \\PDO::ERRMODE_EXCEPTION]
            );
        } catch (\\PDOException \$e) {
            \$this->logError("Database connection failed: " . \$e->getMessage());
            throw new \\Exception("Failed to connect to database");
        }
    }

    public static function table(\$table) {
        return new static(\$table);
    }

    public function select(\$columns = '*') {
        \$this->query = "SELECT \$columns FROM {\$this->table}";
        return \$this;
    }

    public function where(\$column, \$operator, \$value) {
        \$this->query .= empty(\$this->query) ? "SELECT * FROM {\$this->table} WHERE" : " AND";
        \$this->query .= " \$column \$operator ?";
        \$this->bindings[] = \$value;
        return \$this;
    }

    public function get() {
        try {
            \$stmt = \$this->pdo->prepare(\$this->query);
            \$stmt->execute(\$this->bindings);
            return \$stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch (\\PDOException \$e) {
            \$this->logError("Query failed: " . \$e->getMessage());
            throw new \\Exception("Database query failed");
        }
    }

    public function create(\$data) {
        try {
            \$columns = implode(', ', array_keys(\$data));
            \$placeholders = implode(', ', array_fill(0, count(\$data), '?'));
            \$this->query = "INSERT INTO {\$this->table} (\$columns) VALUES (\$placeholders)";
            \$this->bindings = array_values(\$data);
            \$stmt = \$this->pdo->prepare(\$this->query);
            return \$stmt->execute(\$this->bindings);
        } catch (\\PDOException \$e) {
            \$this->logError("Insert failed: " . \$e->getMessage());
            throw new \\Exception("Database insert failed");
        }
    }

    public function getPdo() {
        return \$this->pdo;
    }

    private function logError(\$message) {
        \$logFile = __DIR__ . '/../../storage/logs/app.log';
        \$timestamp = date('Y-m-d H:i:s');
        file_put_contents(\$logFile, "[\$timestamp] \$message
", FILE_APPEND);
    }
}
EOT;
    }
    private function getCachePhp(): string
    {
        return <<<EOT
<?php
namespace Rapulo\Core;

class Cache {
    private static \$storageDir;
    private static \$cache = [];

    public static function init(\$dir) {
        if (!is_writable(\$dir)) {
            throw new \\Exception("Cache directory '\$dir' is not writable");
        }
        self::\$storageDir = \$dir;
    }

    public static function get(\$key, \$default = null) {
        if (isset(self::\$cache[\$key])) {
            return self::\$cache[\$key];
        }

        \$file = self::\$storageDir . '/' . md5(\$key) . '.cache';
        if (file_exists(\$file)) {
            \$data = @unserialize(file_get_contents(\$file));
            if (\$data === false || !isset(\$data['expires']) || \$data['expires'] < time()) {
                @unlink(\$file);
                return \$default;
            }
            self::\$cache[\$key] = \$data['value'];
            return \$data['value'];
        }

        return \$default;
    }

    public static function set(\$key, \$value, \$ttl = 3600) {
        self::\$cache[\$key] = \$value;
        \$file = self::\$storageDir . '/' . md5(\$key) . '.cache';
        \$data = ['value' => \$value, 'expires' => time() + \$ttl];
        if (!@file_put_contents(\$file, serialize(\$data))) {
            throw new \\Exception("Failed to write cache to '\$file'");
        }
    }
}
EOT;
    }
    private function getQueuePhp(): string
    {
        return <<<EOT
<?php
namespace Rapulo\Core;

class Queue {
    private static \$queueDir;

    public static function init(\$dir) {
        if (!is_writable(\$dir)) {
            throw new \Exception("Queue directory '\$dir' is not writable");
        }
        self::\$queueDir = \$dir;
    }

    public static function push(\$job, \$data) {
        \$id = uniqid();
        \$task = serialize(['job' => \$job, 'data' => \$data]);
        \$file = self::\$queueDir . '/' . \$id . '.job';
        if (!@file_put_contents(\$file, \$task)) {
            throw new \Exception("Failed to write queue task to '\$file'");
        }
    }

    public static function process() {
        \$files = glob(self::\$queueDir . '/*.job');
        foreach (\$files as \$file) {
            \$task = @unserialize(file_get_contents(\$file));
            if (\$task === false) {
                @unlink(\$file);
                continue;
            }
            try {
                \$job = new \$task['job']();
                \$job->handle(\$task['data']);
                @unlink(\$file);
            } catch (\Exception \$e) {
                \$logFile = __DIR__ . '/../../storage/logs/app.log';
                \$timestamp = date('Y-m-d H:i:s');
                file_put_contents(\$logFile, "[\$timestamp] Queue error: " . \$e->getMessage() . "
", FILE_APPEND);
            }
        }
    }
}
EOT;
    }
    private function getComponentPhp(): string
    {
        return <<<EOT
<?php
namespace Rapulo\Core;

class Component {
    protected \$props = [];
    protected \$cacheKey;

    public function __construct(\$props = []) {
        \$this->props = \$props;
        \$this->cacheKey = md5(serialize([get_class(\$this), \$props]));
    }

    public function render() {
        \$cached = Cache::get(\$this->cacheKey);
        if (\$cached !== null) {
            return \$cached;
        }

        ob_start();
        \$this->view();
        \$output = ob_get_clean();

        Cache::set(\$this->cacheKey, \$output, 3600);
        return \$output;
    }

    protected function view() {
        \$view = str_replace('Rapulo\\\\Features\\\\', '', get_class(\$this));
        \$view = str_replace('\\\\', '/', \$view);
        \$view = strtolower(preg_replace('/([A-Z])/', '-\1', \$view)) . '.view.php';
        \$viewPath = __DIR__ . '/../Features/' . \$view;
        if (file_exists(\$viewPath)) {
            extract(\$this->props);
            require \$viewPath;
        } else {
            throw new \Exception("View file '\$viewPath' not found");
        }
    }
}
EOT;
    }
    private function getAppConfig(): string
    {
        return <<<EOT
<?php
return [
    'debug' => true,
    'timezone' => 'Asia/Jakarta',
    'log_file' => __DIR__ . '/../../storage/logs/app.log',
];
EOT;
    }
    private function getDatabaseConfig(string $projectName): string
    {
        return <<<EOT
<?php
return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => '$projectName',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
EOT;
    }
    private function getWebRoutes(): string
    {
        return <<<EOT
<?php
use Rapulo\Features\Auth\AuthController;
\$router->middleware('auth', 'Rapulo\Middleware\AuthMiddleware');

\$router->get('/login', [AuthController::class, 'showLogin']);
\$router->post('/login', [AuthController::class, 'login']);
\$router->group(['auth'], function(\$router) {
    \$router->get('/dashboard', [AuthController::class, 'dashboard']);
});
EOT;
    }
    private function getApiRoutes(): string
    {
        return <<<EOT
<?php
\$router->group(['api'], function(\$router) {
    \$router->get('/api/test', function() {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'API Test']);
    });
});
EOT;
    }
    private function getLoginComponent(): string
    {
        return <<<EOT
<?php
namespace Rapulo\Features\Auth;
use Rapulo\Core\Component;

class LoginComponent extends Component {
    public function view() {
        parent::view();
    }
}
EOT;
    }
    private function getLoginView(): string
    {
        return <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <form method="POST" action="/login">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
EOT;
    }
    private function getAuthController(): string
    {
        return <<<EOT
<?php
namespace Rapulo\Features\Auth;
use Rapulo\Core\Component;
use Rapulo\Core\ORM;

class AuthController {
    public function showLogin() {
        \$component = new LoginComponent();
        echo \$component->render();
    }

    public function login() {
        \$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        \$password = \$_POST['password'] ?? '';

        \$user = ORM::table('users')
            ->where('username', '=', \$username)
            ->get();

        if (!empty(\$user) && password_verify(\$password, \$user[0]->password)) {
            \$_SESSION['user'] = \$user[0];
            header('Location: /dashboard');
            exit;
        } else {
            echo 'Invalid credentials';
        }
    }

    public function dashboard() {
        echo 'Welcome to Dashboard, ' . htmlspecialchars(\$_SESSION['user']->username);
    }
}
EOT;
    }
    private function getUserModel(): string
    {
        return <<<EOT
<?php
namespace Rapulo\Features\Auth;

use Rapulo\Core\ORM;

class UserModel extends ORM
{
    public function __construct()
    {
        parent::__construct('users');
    }
}
EOT;
    }
    private function getAuthMiddleware(): string
    {
        return <<<EOT
<?php
namespace Rapulo\Middleware;

class AuthMiddleware {
    public function handle() {
        if (!isset(\$_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}
EOT;
    }
    private function getUsersMigration(): string
    {
        return <<<EOT
<?php
use Rapulo\Core\ORM;

class Migration_create_users_table {
    public function up() {
        \$pdo = (new ORM('migrations'))->getPdo();
        \$pdo->exec("
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down() {
        \$pdo = (new ORM('migrations'))->getPdo();
        \$pdo->exec("DROP TABLE IF EXISTS users");
    }
}
EOT;
    }
    private function getUserSeed(): string
    {
        return <<<EOT
<?php
use Rapulo\Core\ORM;

class UserSeed {
    public function run() {
        ORM::table('users')->create([
            'username' => 'admin',
            'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);
    }
}
EOT;
    }
    private function getRapuloCli(): string
    {
        return <<<EOT
#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

class RapuloCLI
{
    public function run()
    {
        global \$argv;
        \$command = \$argv[1] ?? 'help';
        \$args = array_slice(\$argv, 2);

        switch (\$command) {
            case 'make:feature':
                \$this->makeFeature(\$args);
                break;
            case 'make:component':
                \$this->makeComponent(\$args);
                break;
            case 'make:controller':
                \$this->makeController(\$args);
                break;
            case 'make:model':
                \$this->makeModel(\$args);
                break;
            case 'make:middleware':
                \$this->makeMiddleware(\$args);
                break;
            case 'make:migration':
                \$this->makeMigration(\$args);
                break;
            case 'make:seed':
                \$this->makeSeed(\$args);
                break;
            case 'migrate':
                \$this->migrate(\$args);
                break;
            case 'seed':
                \$this->seed(\$args);
                break;
            case 'serve':
                \$this->serve(\$args);
                break;
            default:
                \$this->showHelp();
                break;
        }
    }

    private function makeFeature(array \$args)
    {
        if (empty(\$args[0])) {
            echo "Error: Feature name is required\n";
            exit(1);
        }
        \$featureName = ucfirst(\$args[0]);
        \$featureDir = "app/Features/\$featureName";
        if (is_dir(\$featureDir)) {
            echo "Error: Feature '\$featureName' already exists\n";
            exit(1);
        }
        if (!mkdir(\$featureDir, 0755, true)) {
            echo "Error: Failed to create feature directory '\$featureDir'\n";
            exit(1);
        }
        echo "Feature '\$featureName' created successfully at \$featureDir\n";
    }

    private function makeComponent(array \$args)
    {
        if (empty(\$args[0]) || empty(\$args[1])) {
            echo "Error: Component name and feature name are required\n";
            exit(1);
        }
        \$componentName = ucfirst(\$args[0]);
        \$featureName = ucfirst(\$args[1]);
        \$featureDir = "app/Features/\$featureName";
        \$componentFile = "\$featureDir/{\$componentName}Component.php";
        \$viewFile = "\$featureDir/{\$componentName}.view.php";
        if (!is_dir(\$featureDir)) {
            echo "Error: Feature '\$featureName' does not exist\n";
            exit(1);
        }
        if (file_exists(\$componentFile)) {
            echo "Error: Component '\$componentName' already exists\n";
            exit(1);
        }
        \$componentContent = <<<CONTENT
<?php
namespace Rapulo\\Features\\{\$featureName};
use Rapulo\\Core\\Component;

class {\$componentName}Component extends Component {
    public function view() {
        parent::view();
    }
}
CONTENT;
        \$viewContent = <<<CONTENT
<!DOCTYPE html>
<html>
<head>
    <title>{\$componentName}</title>
</head>
<body>
    <h1>{\$componentName} Component</h1>
</body>
</html>
CONTENT;
        if (!file_put_contents(\$componentFile, \$componentContent) || !file_put_contents(\$viewFile, \$viewContent)) {
            echo "Error: Failed to create component files\n";
            exit(1);
        }
        echo "Component '\$componentName' created successfully in feature '\$featureName'\n";
    }

    private function makeController(array \$args)
    {
        if (empty(\$args[0]) || empty(\$args[1])) {
            echo "Error: Controller name and feature name are required\n";
            exit(1);
        }
        \$controllerName = ucfirst(\$args[0]);
        \$featureName = ucfirst(\$args[1]);
        \$featureDir = "app/Features/\$featureName";
        \$controllerFile = "\$featureDir/{\$controllerName}Controller.php";
        if (!is_dir(\$featureDir)) {
            echo "Error: Feature '\$featureName' does not exist\n";
            exit(1);
        }
        if (file_exists(\$controllerFile)) {
            echo "Error: Controller '\$controllerName' already exists\n";
            exit(1);
        }
        \$controllerContent = <<<CONTENT
<?php
namespace Rapulo\\Features\\{\$featureName};
use Rapulo\\Core\\ORM;

class {\$controllerName}Controller {
    public function index() {
        echo '{\$controllerName} Controller';
    }
}
CONTENT;
        if (!file_put_contents(\$controllerFile, \$controllerContent)) {
            echo "Error: Failed to create controller file\n";
            exit(1);
        }
        echo "Controller '\$controllerName' created successfully in feature '\$featureName'\n";
    }

    private function makeModel(array \$args)
    {
        if (empty(\$args[0]) || empty(\$args[1])) {
            echo "Error: Model name and feature name are required\n";
            exit(1);
        }
        \$modelName = ucfirst(\$args[0]);
        \$featureName = ucfirst(\$args[1]);
        \$featureDir = "app/Features/\$featureName";
        \$modelFile = "\$featureDir/{\$modelName}Model.php";
        if (!is_dir(\$featureDir)) {
            echo "Error: Feature '\$featureName' does not exist\n";
            exit(1);
        }
        if (file_exists(\$modelFile)) {
            echo "Error: Model '\$modelName' already exists\n";
            exit(1);
        }
        \$modelContent = <<<CONTENT
<?php
namespace Rapulo\\Features\\{\$featureName};
use Rapulo\\Core\\ORM;

class {\$modelName}Model extends ORM {
    public function __construct() {
        parent::__construct(strtolower('{\$modelName}'));
    }
}
CONTENT;
        if (!file_put_contents(\$modelFile, \$modelContent)) {
            echo "Error: Failed to create model file\n";
            exit(1);
        }
        echo "Model '\$modelName' created successfully in feature '\$featureName'\n";
    }

    private function makeMiddleware(array \$args)
    {
        if (empty(\$args[0])) {
            echo "Error: Middleware name is required\n";
            exit(1);
        }
        \$middlewareName = ucfirst(\$args[0]);
        \$middlewareFile = "app/Middleware/{\$middlewareName}Middleware.php";
        if (file_exists(\$middlewareFile)) {
            echo "Error: Middleware '\$middlewareName' already exists\n";
            exit(1);
        }
        \$middlewareContent = <<<CONTENT
<?php
namespace Rapulo\\Middleware;

class {\$middlewareName}Middleware {
    public function handle() {
        // Middleware logic here
    }
}
CONTENT;
        if (!file_put_contents(\$middlewareFile, \$middlewareContent)) {
            echo "Error: Failed to create middleware file\n";
            exit(1);
        }
        echo "Middleware '\$middlewareName' created successfully\n";
    }

    private function makeMigration(array \$args)
    {
        if (empty(\$args[0])) {
            echo "Error: Migration name is required\n";
            exit(1);
        }
        \$migrationName = \$this->snakeCase(\$args[0]);
        \$timestamp = date('Y_m_d_His');
        \$migrationFile = "app/Migrations/{\$timestamp}_{\$migrationName}.php";
        \$className = 'Migration_' . \$timestamp . '_' . \$migrationName;
        \$migrationContent = <<<CONTENT
<?php
use Rapulo\\Core\\ORM;

class \$className {
    public function up() {
        \\\$pdo = (new ORM('migrations'))->getPdo();
        \\\$pdo->exec("
            // Define your migration here
        ");
    }

    public function down() {
        \\\$pdo = (new ORM('migrations'))->getPdo();
        \\\$pdo->exec("
            // Define your rollback here
        ");
    }
}
CONTENT;
        if (!file_put_contents(\$migrationFile, \$migrationContent)) {
            echo "Error: Failed to create migration file\n";
            exit(1);
        }
        echo "Migration '\$migrationName' created successfully at \$migrationFile\n";
    }

    private function makeSeed(array \$args)
    {
        if (empty(\$args[0])) {
            echo "Error: Seed name is required\n";
            exit(1);
        }
        \$seedName = ucfirst(\$args[0]);
        \$seedFile = "app/Seeds/{\$seedName}Seed.php";
        if (file_exists(\$seedFile)) {
            echo "Error: Seed '\$seedName' already exists\n";
            exit(1);
        }
        \$seedContent = <<<CONTENT
<?php
use Rapulo\\Core\\ORM;

class {\$seedName}Seed {
    public function run() {
        // Define your seed logic here
    }
}
CONTENT;
        if (!file_put_contents(\$seedFile, \$seedContent)) {
            echo "Error: Failed to create seed file\n";
            exit(1);
        }
        echo "Seed '\$seedName' created successfully at \$seedFile\n";
    }

    private function migrate(array \$args)
    {
        \$migrationDir = 'app/Migrations';
        if (!is_dir(\$migrationDir)) {
            echo "Error: Migrations directory not found\n";
            exit(1);
        }

        \$config = require 'app/Config/database.php';
        try {
            \$pdo = new \\PDO(
                "{\$config['driver']}:host={\$config['host']};charset={\$config['charset']}",
                \$config['username'],
                \$config['password'],
                [\\PDO::ATTR_ERRMODE => \\PDO::ERRMODE_EXCEPTION]
            );
            \$pdo->exec("CREATE DATABASE IF NOT EXISTS {\$config['database']}");
            echo "Database '{\$config['database']}' created or already exists\n";
        } catch (\\PDOException \$e) {
            echo "Error creating database: " . \$e->getMessage() . "\n";
            exit(1);
        }

        \$files = glob("\$migrationDir/*.php");
        foreach (\$files as \$file) {
            require_once \$file;
            \$className = 'Migration_' . basename(\$file, '.php');
            if (class_exists(\$className)) {
                try {
                    \$migration = new \$className();
                    \$migration->up();
                    echo "Applied migration: " . basename(\$file) . "\n";
                } catch (\\Exception \$e) {
                    echo "Error applying migration " . basename(\$file) . ": " . \$e->getMessage() . "\n";
                    exit(1);
                }
            }
        }

        echo "All migrations applied successfully\n";
    }

    private function seed(array \$args)
    {
        \$seedDir = 'app/Seeds';
        if (!is_dir(\$seedDir)) {
            echo "Error: Seeds directory not found\n";
            exit(1);
        }

        \$files = glob("\$seedDir/*.php");
        foreach (\$files as \$file) {
            require_once \$file;
            \$className = basename(\$file, '.php');
            if (class_exists(\$className)) {
                try {
                    \$seeder = new \$className();
                    \$seeder->run();
                    echo "Applied seed: " . basename(\$file) . "\n";
                } catch (\\Exception \$e) {
                    echo "Error applying seed " . basename(\$file) . ": " . \$e->getMessage() . "\n";
                    exit(1);
                }
            }
        }

        echo "All seeds applied successfully\n";
    }

    private function serve(array \$args)
    {
        \$host = 'localhost';
        \$port = '8000';
        if (!empty(\$args[0])) {
            if (strpos(\$args[0], ':') !== false) {
                [\$host, \$port] = explode(':', \$args[0]);
            } else {
                \$host = \$args[0];
                \$port = \$args[1] ?? '8000';
            }
        }
        \$command = "php -S \$host:\$port -t public";
        echo "Starting Rapulo development server at http://\$host:\$port\n";
        passthru(\$command);
    }

    private function showHelp()
    {
        echo "Available commands:\n";
        echo "  make:feature <name>              Create a new feature directory\n";
        echo "  make:component <name> <feature>  Create a new component in the specified feature\n";
        echo "  make:controller <name> <feature> Create a new controller in the specified feature\n";
        echo "  make:model <name> <feature>      Create a new model in the specified feature\n";
        echo "  make:middleware <name>           Create a new middleware\n";
        echo "  make:migration <name>            Create a new database migration\n";
        echo "  make:seed <name>                 Create a new database seed\n";
        echo "  migrate                         Run all database migrations\n";
        echo "  seed                            Run all database seeds\n";
        echo "  serve [host:port]                Run the development server\n";
        echo "  help                            Show this help message\n";
    }

    private function snakeCase(\$string)
    {
        return strtolower(preg_replace('/([A-Z])/', '_\\1', \$string));
    }
}

\$cli = new RapuloCLI();
\$cli->run();
?>
EOT;
    }
}
