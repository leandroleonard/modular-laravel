<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('app:make-domain-command {name}')]
#[Description('Create a new DDD domain structure')]
class MakeDomainCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = base_path("app/Domains/{$name}");

        $directories = [
            'Entities',
            'ValueObjects',
            'Repositories',
            'Services',
            'Exceptions',
        ];

        foreach ($directories as $dir) {
            File::ensureDirectoryExists("{$path}/{$dir}");
        }

        File::put("{$path}/Entities/{$name}Entity.php", $this->getEntityStub($name));

        $this->info("Domain '{$name}' created successfully at app/Domains/{$name}");
        return 0;
    }

    private function getEntityStub($name)
    {
        return "<?php\n\nnamespace App\\Domains\\{$name}\\Entities;\n\nclass {$name}Entity\n{\n    // Business logic here\n}";
    }
}
