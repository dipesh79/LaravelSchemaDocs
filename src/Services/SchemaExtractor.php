<?php

namespace Dipesh79\LaravelSchemaDocs\Services;

use Illuminate\Support\Facades\DB;

class SchemaExtractor
{
    public function getSchema(): array
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        return match ($driver) {
            'pgsql' => $this->getPostgresSchema($connection),
            'sqlite' => $this->getSQLiteSchema($connection),
            default => $this->getMySQLSchema($connection),
        };
    }

    private function getMySQLSchema($connection): array
    {
        $database = $connection->getDatabaseName();

        $tables = $connection->select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = ?
            ORDER BY table_name ASC
        ", [$database]);

        $result = [];

        foreach ($tables as $t) {
            $table = $t->TABLE_NAME;

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

    private function getPostgresSchema($connection): array
    {
        $schema = 'public';

        $tables = $connection->select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = ? AND table_type = 'BASE TABLE'
            ORDER BY table_name ASC
        ", [$schema]);

        $result = [];

        foreach ($tables as $t) {
            $table = $t->table_name;

            $pkColumns = $connection->select("
                SELECT kcu.column_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                    ON tc.constraint_catalog = kcu.constraint_catalog
                    AND tc.constraint_schema = kcu.constraint_schema
                    AND tc.constraint_name = kcu.constraint_name
                WHERE tc.constraint_type = 'PRIMARY KEY'
                    AND tc.table_schema = ?
                    AND tc.table_name = ?
            ", [$schema, $table]);

            $pkSet = [];
            foreach ($pkColumns as $pk) {
                $pkSet[$pk->column_name] = true;
            }

            $uniqueColumns = $connection->select("
                SELECT kcu.column_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                    ON tc.constraint_catalog = kcu.constraint_catalog
                    AND tc.constraint_schema = kcu.constraint_schema
                    AND tc.constraint_name = kcu.constraint_name
                WHERE tc.constraint_type = 'UNIQUE'
                    AND tc.table_schema = ?
                    AND tc.table_name = ?
            ", [$schema, $table]);

            $uniqueSet = [];
            foreach ($uniqueColumns as $u) {
                $uniqueSet[$u->column_name] = true;
            }

            $columns = $connection->select("
                SELECT
                    c.column_name,
                    c.data_type,
                    c.is_nullable,
                    c.column_default,
                    pgd.description AS column_comment
                FROM information_schema.columns c
                LEFT JOIN pg_catalog.pg_namespace pn
                    ON c.table_schema = pn.nspname
                LEFT JOIN pg_catalog.pg_class pc
                    ON c.table_name = pc.relname AND pc.relnamespace = pn.oid
                LEFT JOIN pg_catalog.pg_attribute pa
                    ON pc.oid = pa.attrelid AND pa.attname = c.column_name AND pa.attnum > 0
                LEFT JOIN pg_catalog.pg_description pgd
                    ON pc.oid = pgd.objoid AND pgd.objsubid = pa.attnum
                WHERE c.table_schema = ? AND c.table_name = ?
                ORDER BY c.ordinal_position
            ", [$schema, $table]);

            $columnsData = [];
            foreach ($columns as $col) {
                $columnsData[$col->column_name] = [
                    'type' => $col->data_type,
                    'nullable' => $col->is_nullable === 'YES',
                    'default' => $col->column_default,
                    'primary' => isset($pkSet[$col->column_name]),
                    'unique' => isset($uniqueSet[$col->column_name]),
                    'comment' => $col->column_comment ?? '',
                ];
            }

            $relations = $connection->select("
                SELECT
                    kcu.column_name AS local_column,
                    kcu2.table_name AS referenced_table,
                    kcu2.column_name AS referenced_column
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                    ON tc.constraint_catalog = kcu.constraint_catalog
                    AND tc.constraint_schema = kcu.constraint_schema
                    AND tc.constraint_name = kcu.constraint_name
                JOIN information_schema.referential_constraints rc
                    ON tc.constraint_catalog = rc.constraint_catalog
                    AND tc.constraint_schema = rc.constraint_schema
                    AND tc.constraint_name = rc.constraint_name
                JOIN information_schema.key_column_usage kcu2
                    ON rc.unique_constraint_catalog = kcu2.constraint_catalog
                    AND rc.unique_constraint_schema = kcu2.constraint_schema
                    AND rc.unique_constraint_name = kcu2.constraint_name
                WHERE tc.constraint_type = 'FOREIGN KEY'
                    AND tc.table_schema = ?
                    AND tc.table_name = ?
            ", [$schema, $table]);

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

    private function getSQLiteSchema($connection): array
    {
        $tables = $connection->select("
            SELECT name AS table_name
            FROM sqlite_master
            WHERE type = 'table' AND name NOT LIKE 'sqlite_%'
            ORDER BY name ASC
        ");

        $result = [];

        foreach ($tables as $t) {
            $table = $t->table_name;

            $columns = $connection->select("PRAGMA table_info(\"{$table}\")");

            $columnsData = [];
            foreach ($columns as $col) {
                $columnsData[$col->name] = [
                    'type' => $col->type,
                    'nullable' => !$col->notnull,
                    'default' => $col->dflt_value,
                    'primary' => (bool) $col->pk,
                    'unique' => false,
                    'comment' => '',
                ];
            }

            $relations = $connection->select("PRAGMA foreign_key_list(\"{$table}\")");

            $relationsData = [];
            foreach ($relations as $r) {
                $relationsData[] = [
                    'column' => $r->from,
                    'references' => $r->table,
                    'on' => $r->to,
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
