<?php

namespace Dipesh79\LaravelSchemaDocs\Services;

class LaravelSchemaDocs
{
    protected SchemaExtractor $extractor;
    protected YamlManager $yamlManager;
    protected DocBuilder $docBuilder;
    protected array $excludedTables;

    public function __construct()
    {
        $this->extractor = new SchemaExtractor();
        $this->yamlManager = new YamlManager();
        $this->docBuilder = new DocBuilder();
        $this->excludedTables = config('laravel-schema-docs.excluded_tables', []);
    }

    public function extractSchema(): array
    {
        $schema = $this->extractor->getSchema();

        // Remove excluded tables so YAML generation won't include them
        if (isset($schema['tables']) && is_array($schema['tables']) && !empty($this->excludedTables)) {
            $schema['tables'] = array_diff_key($schema['tables'], array_flip($this->excludedTables));
        }

        return $schema;
    }

    public function updateYaml(array $schema): void
    {
        $existing = $this->yamlManager->load();
        if (!is_array($existing)) {
            $existing = [];
        }

        if (!isset($schema['tables']) || !is_array($schema['tables'])) {
            $schema['tables'] = [];
        }

        foreach ($schema['tables'] as $tableName => &$tableData) {
            // Skip excluded tables
            if (in_array($tableName, $this->excludedTables, true)) {
                continue;
            }

            if (!isset($tableData['columns']) || !is_array($tableData['columns'])) {
                continue;
            }

            foreach ($tableData['columns'] as $columnName => &$columnData) {
                if (!is_array($columnData)) {
                    $columnData = [];
                }

                if (!array_key_exists('logic', $columnData)) {
                    if (
                        isset($existing['tables'][$tableName]['columns'][$columnName])
                        && is_array($existing['tables'][$tableName]['columns'][$columnName])
                        && array_key_exists('logic', $existing['tables'][$tableName]['columns'][$columnName])
                    ) {
                        $columnData['logic'] = $existing['tables'][$tableName]['columns'][$columnName]['logic'];
                    } else {
                        $columnData['logic'] = '';
                    }
                }
            }
            unset($columnData);
        }
        unset($tableData);

        $schema['tables'] = array_diff_key($schema['tables'], array_flip($this->excludedTables));

        $merged = array_replace_recursive($existing, $schema);
        $this->yamlManager->save($merged);
    }

    public function clean(): void
    {
        $this->yamlManager->clean();
    }
}
