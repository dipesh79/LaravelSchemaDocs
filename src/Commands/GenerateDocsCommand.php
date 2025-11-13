<?php

namespace Dipesh79\LaravelSchemaDocs\Commands;

use Illuminate\Console\Command;
use Dipesh79\LaravelSchemaDocs\Facades\LaravelSchemaDocs;

class GenerateDocsCommand extends Command
{
    protected $signature = 'laravelschemadocs:generate';
    protected $description = 'Generate or update database YAML and HTML documentation.';

    public function handle(): void
    {
        $this->info('🔍 Extracting schema...');
        $schema = LaravelSchemaDocs::extractSchema();

        $this->info('📝 Updating YAML file...');
        LaravelSchemaDocs::updateYaml($schema);
    }
}
