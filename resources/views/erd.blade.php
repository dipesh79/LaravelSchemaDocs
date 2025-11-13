@extends('laravelschemadocs::layouts.app')

@section('title', 'Laravel Schema Docs - ER Diagram')

@section('content')
    <style>
        .erd-container {
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: relative;
            overflow-x: auto;
        }

        .erd-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .erd-toolbar button {
            background: #0b5fff;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .erd-toolbar button:hover {
            background: #084ccc;
        }

        pre.mermaid {
            background: white;
            padding: 10px;
            border-radius: 10px;
            overflow: visible;
        }
    </style>

    <div class="erd-toolbar">
        <h1 class="page-title">Entity Relationship Diagram</h1>
        <div>
            <button id="export-png">Export PNG</button>
            <button id="export-jpg">Export JPG</button>
        </div>
    </div>

    <div class="erd-container" id="erd-container">
        <pre class="mermaid" id="erd-diagram">
            {{ generateMermaid($schema) }}
        </pre>
    </div>

    {{-- Mermaid + Export scripts --}}
    <script type="module">
        import mermaid from "https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs";
        import domtoimage from "https://cdn.jsdelivr.net/npm/dom-to-image-more@3.2.0/dist/dom-to-image-more.min.js";

        mermaid.initialize({ startOnLoad: true, theme: 'neutral' });

        const container = document.getElementById('erd-container');
        const exportBtnPng = document.getElementById('export-png');
        const exportBtnJpg = document.getElementById('export-jpg');

        exportBtnPng.addEventListener('click', () => exportDiagram('png'));
        exportBtnJpg.addEventListener('click', () => exportDiagram('jpg'));

        function exportDiagram(type = 'png') {
            const node = container;
            const method = type === 'jpg' ? domtoimage.toJpeg : domtoimage.toPng;
            method(node)
                .then((dataUrl) => {
                    const link = document.createElement('a');
                    link.download = `erd-diagram.${type}`;
                    link.href = dataUrl;
                    link.click();
                })
                .catch((error) => console.error('Export failed', error));
        }
    </script>
@endsection
