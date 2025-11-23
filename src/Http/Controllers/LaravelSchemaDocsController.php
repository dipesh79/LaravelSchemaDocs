<?php

namespace Dipesh79\LaravelSchemaDocs\Http\Controllers;

use Dipesh79\LaravelSchemaDocs\Exceptions\SchemaConfigNotFoundException;
use Dipesh79\LaravelSchemaDocs\Exceptions\SchemaFileNotFoundException;
use Dipesh79\LaravelSchemaDocs\Services\DocBuilder;
use Dipesh79\LaravelSchemaDocs\Services\YamlManager;
use Dipesh79\LaravelSchemaDocs\Services\YamlToErdParser;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class LaravelSchemaDocsController extends Controller
{
    /**
     * @throws SchemaFileNotFoundException
     * @throws SchemaConfigNotFoundException
     */
    public function __construct()
    {
        $config = config('laravel-schema-docs');
        if (!$config) {
            throw new SchemaConfigNotFoundException();
        }
        $file = config('laravel-schema-docs.yaml_file');
        if (!file_exists($file)) {
            throw new SchemaFileNotFoundException();
        }

    }

    public function index(): View
    {
        $docData = $this->loadDocData();
        return view('laravelschemadocs::index', ['schema' => $docData]);
    }

    protected function loadDocData(): array
    {
        $yamlManager = new YamlManager();
        $schema = $yamlManager->load();
        $docBuilder = new DocBuilder();
        return $docBuilder->build($schema);
    }

    public function show(string $name): View
    {
        $docData = $this->loadDocData();

        if (!isset($docData['tables'][$name]) ||
            !is_array($docData['tables'][$name])) {
            abort(404);
        }

        $table = $docData['tables'][$name];

        return view('laravelschemadocs::show', [
            'name' => $name,
            'table' => $table,
        ]);
    }

    public function erd(YamlToErdParser $parser): View
    {
        return view('laravelschemadocs::erd', [
            'mermaid' => $parser->generate()
        ]);
    }
}
