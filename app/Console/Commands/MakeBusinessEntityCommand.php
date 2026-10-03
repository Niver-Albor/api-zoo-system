<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeBusinessEntityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:entity {name : El nombre de la entidad}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una nueva entidad de negocio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        $className = str_ends_with($name, 'Entity') ? $name : $name . 'Entity';

        $path = app_path("Business/Entities/{$className}.php");

        if (File::exists($path))
            {
                $this->error("El servicio {$className} ya existe!");
                return self::FAILURE;
            }
        
        File::ensureDirectoryExists(dirname($path));

        $stubPath = app_path('Config\business-entity.stub');

        if (!File::exists($stubPath))
            {
                $this->error("No se encontró el archivo stub en: {$stubPath}");
                return self::FAILURE;
            }
        
        $stubContent = File::get($stubPath);

        $content = str_replace('{{class}}', $className, $stubContent);

        File::put($path, $content);

        $this->info("Entidad creada exitosamente: App/Business/Entities/{$className}.php");

        return self::SUCCESS;
    }
}
