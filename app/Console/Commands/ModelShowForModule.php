<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelInspector;
use Illuminate\Database\Console\ShowModelCommand;

class ModelShowForModule extends ShowModelCommand
{
    protected $signature = 'model:show-for-module {model : The model to show}
                {--database= : The database connection to use}
                {--name= : The full model class path}
                {--module= : The module name}
                {--json : Output the model as JSON}';

    protected $description = 'Show model details, supporting module-based models (InterNACHI Modular)';

    public function getAliases(): array
    {
        return ['model:showname', 'model:show-name', 'model:sn', 'ms'];
    }

    public function handle(ModelInspector $modelInspector)
    {
        // Manually override the 'model' argument with the correct class path
        $this->input->setArgument('model', $this->resolveModelClass());

        return parent::handle($modelInspector);
    }

    protected function resolveModelClass(): string
    {
        $model = $this->argument('model');

        // --name takes full namespace
        if ($name = $this->option('name')) {
            return str_replace('/', '\\', $name);
        }

        // If module is specified, use Modules\StudlyModule\Models\StudlyModel
        if ($module = $this->option('module')) {
            $moduleStudly = Str::studly($module);
            $modelStudly = Str::studly($model);
            return "Modules\\$moduleStudly\\Models\\$modelStudly";
        }

        // Fallback to App\Models\Model
        $rootNamespace = $this->laravel->getNamespace();
        return is_dir(app_path('Models'))
            ? $rootNamespace . 'Models\\' . Str::studly($model)
            : $rootNamespace . Str::studly($model);
    }
}
