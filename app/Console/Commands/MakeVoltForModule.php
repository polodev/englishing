<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeVoltForModule extends Command
{
    protected $signature = 'make:volt-for-module {name} {--module=}';
    protected $description = 'Create a Livewire Volt Blade component inside a module with a prefixed filename';

    public function handle()
    {
        $componentName = Str::kebab($this->argument('name')); // kebab-case for filename
        $module = $this->option('module');

        if (!$module) {
            $this->error('❌ You must provide a module name using --module');
            return;
        }

        $filename = "{$module}--{$componentName}.blade.php";
        $viewPath = base_path("app-modules/{$module}/resources/views/livewire/{$filename}");

        File::ensureDirectoryExists(dirname($viewPath));

        if (File::exists($viewPath)) {
            $this->warn("⚠️ Volt view already exists: {$viewPath}");
            return;
        }

        File::put($viewPath, $this->voltStubContent($module, $componentName));
        $this->info("✅ Volt component created: {$viewPath}");
    }

    protected function voltStubContent(string $module, string $component): string
    {
        return <<<BLADE
<?php

use Livewire\\Volt\\Component;

new class extends Component {
    //
};
?>

<div>
    <!-- {$module}--{$component} Volt component -->
</div>
BLADE;
    }
}
