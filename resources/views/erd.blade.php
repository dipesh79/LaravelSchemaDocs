@extends('laravelschemadocs::layouts.app')

@section('content')
    <h2>Database ERD</h2>

    <div id="erd-wrapper" style="position:relative; border:1px solid #ddd; border-radius:8px; background:#f9f9f9;">
        <!-- Loading message -->
        <div id="erd-loading" style="padding:2rem; text-align:center; font-weight:600;">
            Loading ERD diagram...
        </div>

        <!-- Mermaid ERD (hidden initially) -->
        <div id="erd-container" style="display:none; overflow-x:auto; padding:1rem;">
        <pre class="mermaid">
{!! $mermaid !!}
        </pre>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        import mermaid from "https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs";

        // Initialize Mermaid
        mermaid.initialize({
            startOnLoad: false, // disable automatic parsing
            theme: "default",
            flowchart: { curve: "basis" }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const mermaidDiv = document.querySelector('#erd-container pre.mermaid');

            // Render Mermaid diagram manually
            mermaid.render('erdDiagram', mermaidDiv.textContent)
                .then(({svg}) => {
                    // Hide loading, show rendered ERD
                    document.getElementById('erd-loading').style.display = 'none';
                    const container = document.getElementById('erd-container');
                    container.innerHTML = svg;
                    container.style.display = 'block';
                })
                .catch(err => {
                    document.getElementById('erd-loading').textContent = 'Failed to load ERD';
                    console.error(err);
                });
        });
    </script>
@endpush
