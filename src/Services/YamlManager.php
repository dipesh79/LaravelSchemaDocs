<?php

namespace Dipesh79\LaravelSchemaDocs\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class YamlManager
{
    protected string $yamlPath;

    public function __construct()
    {
        $this->yamlPath = config('laravel-schema-docs.yaml_file');
    }

    public function load(): array
    {
        return file_exists($this->yamlPath)
            ? Yaml::parseFile($this->yamlPath)
            : ['tables' => []];
    }

    public function save(array $data): void
    {
        file_put_contents($this->yamlPath, Yaml::dump($data, 4));
    }

    public function clean(): void
    {
        if (File::exists($this->yamlPath)) {
            File::delete($this->yamlPath);
        }
    }

}
