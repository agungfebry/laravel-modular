<?php

namespace Xyz\LaravelModular\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
        protected $signature = 'make:module {path}';
        protected $description = 'Create a new Laravel module with clean architecture';

        public function handle()
        {
                $path = $this->argument('path');
                $parts = explode('/', $path);
                $modulePath = base_path('modules/' . implode('/', $parts));

                if (File::exists($modulePath)) {
                        $this->error("Module already exists!");
                        return;
                }

                // Folder structure
                $folders = ['Controllers', 'Services', 'Models', 'Requests', 'Routes', 'Views', 'Database/Migrations', 'Database/Seeders'];
                foreach ($folders as $folder) {
                        File::makeDirectory("{$modulePath}/{$folder}", 0755, true);
                }

                // Controller
                $controllerName = end($parts) . 'Controller';
                $namespace = 'Modules\\' . implode('\\', $parts) . '\\Controllers';
                $controllerContent = <<<PHP
        <?php

        namespace {$namespace};

        use App\Http\Controllers\Controller;

        class {$controllerName} extends Controller
        {
            public function index()
            {
                return view('{$parts[0]}.{$parts[1]}::index');
            }
        }
        PHP;
                File::put("{$modulePath}/Controllers/{$controllerName}.php", $controllerContent);

                // Service
                $serviceNamespace = 'Modules\\' . implode('\\', $parts) . '\\Services';
                $serviceName = end($parts) . 'Service';
                $serviceContent = <<<PHP
        <?php

        namespace {$serviceNamespace};

        class {$serviceName}
        {
            public function example()
            {
                return 'Service working!';
            }
        }
        PHP;
                File::put("{$modulePath}/Services/{$serviceName}.php", $serviceContent);

                // Routes
                File::put("{$modulePath}/Routes/web.php", "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\nuse {$namespace}\\{$controllerName};\n\nRoute::get('/', [{$controllerName}::class, 'index']);");

                // View
                File::put("{$modulePath}/Views/index.blade.php", "<h1>Module " . end($parts) . "</h1>");

                $this->info("Module created at {$modulePath}");
        }
}
