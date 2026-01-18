@extends('laravelschemadocs::layouts.app')

@push('styles')
<style>
    #erd-wrapper {
        position: relative;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        height: 80vh;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .erd-toolbar {
        position: absolute;
        top: 20px;
        right: 20px;
        display: flex;
        gap: 10px;
        z-index: 1000;
        padding: 8px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .toolbar-btn {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .toolbar-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e0;
        color: #1e293b;
        transform: translateY(-1px);
    }

    /* Fixed Black Boxes and Relationship Labels */
    #erd-container svg {
        width: 100% !important;
        height: 100% !important;
        max-width: none !important;
        background-color: white;
        cursor: grab;
    }

    #erd-container svg:active {
        cursor: grabbing;
    }

    /* Target the relationship labels that are turning black */
    #erd-container .er.relationshipLabelBox {
        fill: white !important;
        stroke: #94a3b8 !important;
    }

    #erd-container .er.relationshipLabel {
        fill: #475569 !important;
    }

    #erd-container .er.entityBox {
        fill: #f8fafc !important;
        stroke: #475569 !important;
    }

    #erd-container .er.attributeBoxOdd {
        fill: #ffffff !important;
        stroke: #e2e8f0 !important;
    }

    #erd-container .er.attributeBoxEven {
        fill: #f8fafc !important;
        stroke: #e2e8f0 !important;
    }

    #erd-container [id^="edge-"] path {
        stroke: #64748b !important;
        stroke-width: 1.5px !important;
    }
</style>
@endpush

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; font-size: 1.5rem; color: #1e293b; font-weight: 700;">Database Relationships</h2>
        <div style="font-size: 0.875rem; color: #64748b; background: #f1f5f9; padding: 4px 12px; border-radius: 20px;">
            Scroll to zoom • Drag to move
        </div>
    </div>

    <div id="erd-wrapper">
        <div class="erd-toolbar" id="erd-toolbar" style="display: none;">
            <button class="toolbar-btn" id="zoom-in"><svg style="width:16px;height:16px;margin-right:6px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Zoom In</button>
            <button class="toolbar-btn" id="zoom-out"><svg style="width:16px;height:16px;margin-right:6px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg> Zoom Out</button>
            <button class="toolbar-btn" id="reset-zoom">Reset</button>
            <button class="toolbar-btn" id="download-image" style="background: #2563eb; color: white; border: none;">
                <svg style="width:16px;height:16px;margin-right:6px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> 
                Download PNG
            </button>
        </div>

        <div id="erd-loading" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
            <div style="width: 40px; height: 40px; border: 3px solid #e2e8f0; border-top-color: #2563eb; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="margin-top: 15px; color: #64748b; font-weight: 500;">Rendering Diagram...</p>
            <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
        </div>

        <div id="erd-container" style="display:none; width: 100%; height: 100%;">
            <pre class="mermaid" style="display: none;">
{!! $mermaid !!}
            </pre>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/svg-pan-zoom@3.6.1/dist/svg-pan-zoom.min.js"></script>
    <script type="module">
        import mermaid from "https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs";

        mermaid.initialize({
            startOnLoad: false,
            theme: "default",
            themeVariables: {
                primaryColor: "#f8fafc",
                primaryTextColor: "#1e293b",
                primaryBorderColor: "#64748b",
                lineColor: "#94a3b8",
                secondaryColor: "#f1f5f9",
                tertiaryColor: "#ffffff",
                fontSize: "14px"
            },
            er: {
                useMaxWidth: false,
                entityPadding: 20
            }
        });

        let panZoomInstance;

        document.addEventListener('DOMContentLoaded', () => {
            const mermaidDiv = document.querySelector('#erd-container pre.mermaid');
            const code = mermaidDiv.textContent;

            mermaid.render('erd-svg-' + Math.random().toString(36).substr(2, 9), code)
                .then(({svg}) => {
                    document.getElementById('erd-loading').style.display = 'none';
                    const container = document.getElementById('erd-container');
                    container.innerHTML = svg;
                    container.style.display = 'block';
                    document.getElementById('erd-toolbar').style.display = 'flex';

                    const svgElement = container.querySelector('svg');
                    
                    panZoomInstance = svgPanZoom(svgElement, {
                        zoomEnabled: true,
                        controlIconsEnabled: false,
                        fit: true,
                        center: true,
                        minZoom: 0.1,
                        maxZoom: 20,
                    });

                    document.getElementById('zoom-in').onclick = () => panZoomInstance.zoomIn();
                    document.getElementById('zoom-out').onclick = () => panZoomInstance.zoomOut();
                    document.getElementById('reset-zoom').onclick = () => {
                        panZoomInstance.resetZoom();
                        panZoomInstance.center();
                    };
                    document.getElementById('download-image').onclick = exportToImage;
                });
        });

        async function exportToImage() {
            const svg = document.querySelector('#erd-container svg');
            if (!svg) return;

            // Clone SVG and prepare for export
            const clone = svg.cloneNode(true);
            const viewport = clone.querySelector('.svg-pan-zoom_viewport');
            if (viewport) viewport.removeAttribute('transform');

            // Embed Styles - CRITICAL for PNG export
            const styles = document.querySelectorAll('style');
            let css = '';
            styles.forEach(s => css += s.textContent);
            const styleElement = document.createElement('style');
            styleElement.textContent = css;
            clone.prepend(styleElement);

            const bbox = svg.getBBox();
            const width = bbox.width + 100;
            const height = bbox.height + 100;

            clone.setAttribute('width', width);
            clone.setAttribute('height', height);
            clone.setAttribute('viewBox', `${bbox.x - 50} ${bbox.y - 50} ${width} ${height}`);

            const svgString = new XMLSerializer().serializeToString(clone);
            const img = new Image();
            const svgBlob = new Blob([svgString], {type: 'image/svg+xml;charset=utf-8'});
            const url = URL.createObjectURL(svgBlob);

            img.onload = () => {
                const canvas = document.createElement('canvas');
                const scale = 2; // High DPI
                canvas.width = width * scale;
                canvas.height = height * scale;
                const ctx = canvas.getContext('2d');
                
                // White background
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                ctx.scale(scale, scale);
                ctx.drawImage(img, 0, 0);

                const now = new Date();
                const timestamp = now.getFullYear() + '-' + 
                                (now.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                                now.getDate().toString().padStart(2, '0') + '_' + 
                                now.getHours().toString().padStart(2, '0') + 
                                now.getMinutes().toString().padStart(2, '0');

                const link = document.createElement('a');
                link.download = `laravel-schema-erd-${timestamp}.png`;
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();
                URL.revokeObjectURL(url);
            };
            img.src = url;
        }
    </script>
@endpush
