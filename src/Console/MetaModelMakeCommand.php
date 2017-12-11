<?php

namespace CludyMe\MetaData\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MetaModelMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:meta-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent meta model class';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->input->setOption('migration', true);
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        $name = Str::lower(Str::singular($this->getNameInput()));
        $className = Str::studly($name) . 'Meta';
        $path = $this->getPath($className);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ($this->files->exists($path)) {
            $this->error("Meta model {$className} already exists!");

            return;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $stub = $this->files->get($this->getStub());
        $stub = str_replace(
            ['DummyNamespace', 'DummyClass', 'DummyBelongsToFunction', 'DummyBelongsToClass'],
            [$this->getNamespace($this->rootNamespace()), $className, $name, Str::studly($name)],
            $stub);

        $this->files->put($path, $stub);

        $this->info("Meta model {$className} created successfully.");
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $this->call('make:meta-migration', [
            'name' => $this->getNameInput()
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/meta-model.stub';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a migration and factory'],

            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the meta model already exists.'],

            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the meta model.'],
        ];
    }
}
