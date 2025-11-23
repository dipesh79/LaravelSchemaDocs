<?php

namespace Dipesh79\LaravelSchemaDocs\Services;

use Symfony\Component\Yaml\Yaml;

class YamlToErdParser
{
    protected array $schema;

    public function __construct()
    {
        $this->schema = Yaml::parseFile(config('laravel-schema-docs.yaml_file'));
    }

    /**
     * Generate Mermaid ER Diagram from YAML schema
     */
    public function generate(): string
    {
        $tables = $this->schema['tables'] ?? [];
        $output = "erDiagram\n";

        // ----------------------------
        // 1) TABLE BLOCKS
        // ----------------------------
        foreach ($tables as $tableName => $tableData) {
            $output .= "    {$tableName} {\n";

            foreach ($tableData['columns'] as $columnName => $columnData) {
                $line = $this->formatColumn($columnName, $columnData);
                $output .= "        {$line}\n";
            }

            $output .= "    }\n\n";
        }

        // ----------------------------
        // 2) RELATIONS
        // ----------------------------
        foreach ($tables as $tableName => $tableData) {
            $relations = $tableData['relations'] ?? [];

            foreach ($relations as $rel) {
                $parent = $rel['references'];  // referenced table
                $child = $tableName;           // current table

                // Basic: assume one-to-many
                $output .= "    {$parent} ||--o{ {$child} : has_many\n";
            }
        }

        return $output;
    }

    /**
     * Format a column line for Mermaid
     */
    protected function formatColumn(string $columnName, array $data): string
    {
        $type = $data['type'] ?? 'unknown';

        $suffix = '';
        if (!empty($data['primary'])) {
            $suffix .= ' PK';
        } elseif (!empty($data['unique'])) {
            $suffix .= ' UK';
        }

        return "{$type} {$columnName}{$suffix}";
    }
}
