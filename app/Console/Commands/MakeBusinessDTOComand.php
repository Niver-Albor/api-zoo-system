<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeBusinessDTOComand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dto {name : El nombre del DTO}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una nueva clase de Data Transfer Object';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        
        $className = str_ends_with($name, 'Data') ? $name : $name . 'Data';

        $path = app_path("Business/DTOs/{$className}.php");

        if (File::exists($path))
            {
                $this->error("El DTO {$className} ya existe!");
                return self::FAILURE;
            }
        
        File::ensureDirectoryExists(dirname($path));

        $stubPath = app_path('Config\dto-template.stub');

        if (!File::exists($stubPath))
            {
                $this->error("No se encontró el archivo stub en: {$stubPath}");
                return self::FAILURE;
            }
        
        $stubContent = File::get($stubPath);
        
        $content = str_replace(
            ['{{className}}'],
            [$className],
            $stubContent
        );
        
        File::put($path, $content);

        $this->info("DTO creado exitosamente: App/Business/DTOs/{$className}.php");

        return self::SUCCESS;
    }
}
