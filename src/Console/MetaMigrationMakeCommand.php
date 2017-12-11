<?php

namespace CludyMe\MetaData\Console;

use Illuminate\Support\Str;
use Illuminate\Support\Composer;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class MetaMigrationMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:meta-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new meta migration file';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new meta migration install command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  \Illuminate\Support\Composer      $composer
     *
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct($files);

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = Str::lower(Str::singular($this->getNameInput()));
        $table = "{$name}_metas";
        $migration = "create_{$table}_table";
        $className = Str::studly($migration);

        $path = $this->getPath($migration);
        $this->makeDirectory($path);

        $stub = $this->files->get($this->getStub());

        $stub = str_replace(
            ['DummyClass', 'DummyTable', 'DummyBelongsToName'],
            [$className, $table, $name],
            $stub);

        $this->files->put($path, $stub);

        $this->composer->dumpAutoloads();

        $this->info(pathinfo($path, PATHINFO_FILENAME) . ' migration created. run "php artisan migrate" to create the table');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/meta-migration.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        return $this->getMigrationPath() . '/' . date('Y_m_d_His') . '_' . $name . '.php';
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return $this->laravel->databasePath() . '/migrations';
    }
}
