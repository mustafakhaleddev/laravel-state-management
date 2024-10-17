<?php

namespace MKD\StateManagement\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use MKD\StateManagement\Casts\StateCastAttribute;
use MKD\StateManagement\Contracts\StoreContract;

class CreateStoreCastCommand extends Command
{
    // The name and signature of the console command
    protected $signature = 'store-cast:make {name}';

    // The console command description
    protected $description = 'Create a new State Castable class';

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
        $path = app_path("Stores/Casts/{$name}.php");  // Set the file path

        if ($this->files->exists($path)) {
            $this->error("Store Cast class {$name} already exists!");
            return;
        }

        // Create the OTP class file
        $stub = $this->getStub();
        $stub = str_replace('{{ class }}', $name, $stub);

        $this->files->ensureDirectoryExists(app_path('Stores/Casts/'));
        $this->files->put($path, $stub);

        $this->info("Store class {$name} created successfully at {$path}");
    }

    // Get the stub for the Casts class
    protected function getStub()
    {
        return <<<EOT
<?php

namespace App\Stores\Casts;

use MKD\StateManagement\Casts\StateCastAttribute;

class {{ class }} implements StateCastAttribute
{


    public function get(string \$key, mixed \$value)
    {
        return \$value;
    }

    public function set(string \$key, mixed \$value)
    {
        return \$value;

    }
}

EOT;
    }
}
