<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeBusinessServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:service {name : El nombre del servicio} 
        {--M|model= : El nombre del modelo a importar}
        {--E|entity : Recibe la entidad de dominio en lugar de datos crudos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una nueva clase de servicio de negocio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $modelName = $this->option('model');
        $hasEntity = $this->option('entity');

        if (is_null($modelName))
            {
                $modelName = $this->selectModelInteractively();
            }

        $className = str_ends_with($name, 'Service') ? $name : $name . 'Service';

        $path = app_path("Business/Services/{$className}.php");

        if (File::exists($path))
            {
                $this->error("El servicio {$className} ya existe!");
                return self::FAILURE;
            }
        
        File::ensureDirectoryExists(dirname($path));

        $stubPath = app_path('Config\business-service.stub');

        if (!File::exists($stubPath)) 
            {
                $this->error("No se encontró el archivo stub en: {$stubPath}");
                return self::FAILURE;
            }

        $stubContent = File::get($stubPath);

        $useModel = '';
        $useEntity = '';
        $parameterType = 'array';

        if ($modelName && $modelName !== 'Ninguno') {
            $modelName = class_basename($modelName);
            $useModel = "use App\Models\\{$modelName};";

            if ($hasEntity) {
                $entityName = "{$modelName}Entity";
                $useEntity = "use App\Business\Entities\\{$entityName};";
                $parameterType = $entityName;
            } else {
                $parameterType = '?Array';
            }
        }

        $content = str_replace(
            ['{{class}}', '{{useModel}}', '{{useEntity}}', '{{parameterType}}'],
            [$className, $useModel, $useEntity, $parameterType],
            $stubContent
        );

        if (!$modelName) {
            $content = preg_replace("/\n\s*\n\s*\n/", "\n\n", $content);
        }

        File::put($path, $content);

        $this->info("Servicio creado exitosamente: App/Business/Services/{$className}.php");

        return self::SUCCESS;
    }

    protected function selectModelInteractively(): ?string
    {
        $modelsPath = app_path('Models');

        if (!File::isDirectory($modelsPath))
            {
                return null;
            }
        
        $files = File::files($modelsPath);

        $models = collect($files)
            ->map(fn($file) => $file->getFilenameWithoutExtension())
            ->values()
            ->toArray();

        if (empty($models))
            {
                return null;
            }

        array_unshift($models, 'Ninguno');

        $selected = $this->choice(
            '¿Qué modelo deseas vincular a este servicio?',
            $models,
            0
        );

        return $selected === 'Ninguno' ? null : $selected;
    }
}
