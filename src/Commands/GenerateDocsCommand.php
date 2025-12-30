<?php

namespace Dipesh79\LaravelSchemaDocs\Commands;

use Illuminate\Console\Command;
use Dipesh79\LaravelSchemaDocs\Facades\LaravelSchemaDocs;

class GenerateDocsCommand extends Command
{
    protected $signature = 'laravelschemadocs:generate {--fresh : Delete existing docs before generating}';

    protected $description = 'Generate or update database YAML and HTML documentation.';

    public function handle(): void
    {
        if ($this->option('fresh')) {
            $this->warn('🧹 Deleting existing schema documentation...');
            LaravelSchemaDocs::clean();
        }

        $this->info('🔍 Extracting schema...');
        $schema = LaravelSchemaDocs::extractSchema();

        $this->info('📝 Updating YAML file...');
        LaravelSchemaDocs::updateYaml($schema);

        $this->info('✅ Schema documentation generated successfully.');
    }
}
