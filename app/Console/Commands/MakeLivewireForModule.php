<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeLivewireForModule extends Command
{
    protected $signature = 'make:livewire-for-module {name} {--module=}';
    protected $description = 'Create a new Livewire component in a specific module (dot notation and nested support)';

    public function handle()
    {
        $name = trim($this->argument('name'));
        $module = trim($this->option('module'));

        if (! $module) {
            $this->error('The --module option is required.');
            return;
        }

        $moduleStudly = Str::studly($module); // For namespace
        $moduleKebab = Str::kebab($module);   // For folder (as-is)
        $modulePath = "app-modules/{$moduleKebab}";

        // Class path: frontend.dev-location → Frontend/DevLocation
        $classParts = collect(explode('.', $name))->map(fn ($part) => Str::studly($part));
        $classPathPart = $classParts->implode('/');
        $classNamespacePart = $classParts->implode('\\');

        // Blade view path
        $viewParts = explode('.', $name);
        $viewDir = implode('/', array_map(fn ($p) => Str::kebab($p), array_slice($viewParts, 0, -1)));
        $viewFile = Str::kebab(end($viewParts));
        $viewBladePath = $viewDir ? "{$viewDir}/{$viewFile}.blade.php" : "{$viewFile}.blade.php";

        // Final paths
        $namespace = "Modules\\{$moduleStudly}\\Livewire";
        $classPath = base_path("{$modulePath}/src/Livewire/{$classPathPart}.php");
        $viewPath = base_path("{$modulePath}/resources/views/livewire/{$viewBladePath}");
        $viewReference = "{$moduleKebab}::livewire." . str_replace('/', '.', $viewDir ? "{$viewDir}/{$viewFile}" : $viewFile);

        if (file_exists($classPath)) {
            $this->error("Component class already exists at {$classPath}");
            return;
        }

        @mkdir(dirname($classPath), 0755, true);
        @mkdir(dirname($viewPath), 0755, true);

        // Create Livewire component class
        file_put_contents($classPath, <<<PHP
<?php

namespace {$namespace}\\{$this->getNamespacePath($classNamespacePart)};

use Livewire\Component;

class {$this->getClassName($classNamespacePart)} extends Component
{
    public function render()
    {
        return view('{$viewReference}');
    }
}
PHP);

        // Create Blade view
        file_put_contents($viewPath, "<div>\n    <!-- {$classNamespacePart} component -->\n</div>");

        $this->info("✅ Livewire component created:");
        $this->line("  📁 Class: {$classPath}");
        $this->line("  🧩 View:  {$viewPath}");
    }

    protected function getClassName(string $fullyQualified): string
    {
        return class_basename(str_replace('/', '\\', $fullyQualified));
    }

    protected function getNamespacePath(string $classNamespacePart): string
    {
        $parts = explode('\\', $classNamespacePart);
        array_pop($parts); // Remove class name
        return implode('\\', $parts);
    }
}
