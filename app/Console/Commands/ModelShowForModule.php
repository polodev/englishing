<?php

namespace App\Console\Commands;

use Str;
use Illuminate\Console\Command;
use Illuminate\Database\Console\ShowModelCommand;

class ModelShowForModule extends ShowModelCommand
{

    /**
     * The name and signature of the console command.
     * php artisan model:show-name hello --name=Modules/English/Models/WordSetListTc
     * php artisan model:show-name WordSet --module=english
     * @var string
     */
    protected $signature = 'model:show-for-module {model : The model to show}
                {--database= : The database connection to use}
                {--name= : The name}
                {--module= : The module}
                {--json : Output the model as JSON}';
                
    public function getAliases(): array
    {
        return ['model:showname', 'model:show-name', 'model:sn', 'ms'];
    }
        
    protected function qualifyModel(string $model)
    {
        if (str_contains($model, '\\') && class_exists($model)) {
            return $model;
        }

        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->laravel->getNamespace();

        if ($name = $this->option('name')) {
            $this->info($name);
            return str_replace('/', '\\', $name);
        }
        if ($module = $this->option('module')) {
            $this->info($module);
            $moduleStudlyCase =  Str::studly($module);
            $module_full_namespace = "Modules/$moduleStudlyCase/Models/$model";
            $this->info($module_full_namespace);
            return str_replace('/', '\\', $module_full_namespace);
        }

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        return is_dir(app_path('Models'))
            ? $rootNamespace.'Models\\'.$model
            : $rootNamespace.$model;
    }


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        parent::handle();
    }
}
