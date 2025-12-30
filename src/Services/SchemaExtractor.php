<?php

namespace Dipesh79\LaravelSchemaDocs\Services;

use Illuminate\Support\Facades\DB;

class SchemaExtractor
{
    public function getSchema(): array
    {
        $connection = DB::connection();
        $database = $connection->getDatabaseName();

        // Get all tables
        $tables = $connection->select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = ?
            ORDER BY table_name ASC
        ", [$database]);

        $result = [];

        foreach ($tables as $t) {
            $table = $t->TABLE_NAME;

            // Get columns
            $columns = $connection->select("
                SELECT column_name, data_type, is_nullable, column_default, column_key, column_comment
                FROM information_schema.columns
                WHERE table_schema = ? AND table_name = ?
                ORDER BY ordinal_position
            ", [$database, $table]);

            $columnsData = [];
            foreach ($columns as $col) {
                $columnsData[$col->COLUMN_NAME] = [
                    'type' => $col->DATA_TYPE,
                    'nullable' => $col->IS_NULLABLE === 'YES',
                    'default' => $col->COLUMN_DEFAULT,
                    'primary' => $col->COLUMN_KEY === 'PRI',
                    'unique' => $col->COLUMN_KEY === 'UNI',
                    'comment' => $col->COLUMN_COMMENT ?: '',
                ];
            }

            // Get relations (foreign keys)
            $relations = $connection->select("
                SELECT
                    kcu.column_name AS local_column,
                    kcu.referenced_table_name AS referenced_table,
                    kcu.referenced_column_name AS referenced_column
                FROM information_schema.key_column_usage AS kcu
                WHERE
                    kcu.table_schema = ?
                    AND kcu.table_name = ?
                    AND kcu.referenced_table_name IS NOT NULL
            ", [$database, $table]);

            $relationsData = [];
            foreach ($relations as $r) {
                $relationsData[] = [
                    'column' => $r->local_column,
                    'references' => $r->referenced_table,
                    'on' => $r->referenced_column,
                ];
            }

            $result[$table] = [
                'columns' => $columnsData,
                'relations' => $relationsData,
            ];
        }

        return ['tables' => $result];
    }
}
