<?php

namespace Dipesh79\LaravelSchemaDocs\Services;

class DocBuilder
{
    public function build(array $schema): array
    {
        $tables = [];
        foreach ($schema['tables'] ?? [] as $name => $data) {
            $tables[$name] = [
                'columns' => $data['columns'] ?? [],
                'relations' => $data['relations'] ?? [],
            ];
        }
        return ['tables' => $tables];
    }
}
