<?php

namespace MKD\StateManagement\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use MKD\StateManagement\Contracts\StoreContract;

class CreateStoreCommand extends Command
{
    // The name and signature of the console command
    protected $signature = 'store:make {name}';

    // The console command description
    protected $description = 'Create a new State Store class';

    // Filesystem instance to interact with the file system
    protected Filesystem $files;

    // Constructor
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    // Execute the console command
    public function handle()
    {
        $name = $this->argument('name');  // Get the class name from command argument
        $path = app_path("Stores/{$name}.php");  // Set the file path

        if ($this->files->exists($path)) {
            $this->error("Store class {$name} already exists!");
            return;
        }

        // Create the OTP class file
        $stub = $this->getStub();
        $stub = str_replace('{{ class }}', $name, $stub);

        $this->files->ensureDirectoryExists(app_path('Stores'));
        $this->files->put($path, $stub);

        $this->info("Store class {$name} created successfully at {$path}");
    }

    // Get the stub for the Store class
    protected function getStub()
    {
        return <<<EOT
<?php

namespace App\Stores;

use MKD\StateManagement\Contracts\StoreContract;

class {{ class }} extends StoreContract
{
    protected \$attributes = [];

    protected \$casts = [];

    protected \$enums = [];


    /**
     * Override Default State if rehydrate failed to return data
     * @return array
     */
    public function default(): array
    {
        return [];
    }

    public function customMethod()
    {
        // Custom Method can be called using store instance
    }
}
EOT;
    }
}
