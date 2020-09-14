<?php

namespace Mehradsadeghi\CrudGenerator;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishCrudStubCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all CRUD stubs';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! is_dir($stubsPath = $this->laravel->basePath('stubs/crud'))) {
            (new Filesystem)->makeDirectory($stubsPath, 0755, true);
        }

        $files = [
            __DIR__.'/stubs/controller.model.stub' => $stubsPath.'/controller.model.stub',
            __DIR__.'/stubs/controller.model.validation.stub' => $stubsPath.'/controller.model.validation.stub',
            __DIR__.'/stubs/controller.plain.stub' => $stubsPath.'/controller.plain.stub',
        ];

        foreach ($files as $from => $to) {
            if (! file_exists($to)) {
                file_put_contents($to, file_get_contents($from));
            }
        }

        $this->info('Stubs published successfully.');
    }
}
