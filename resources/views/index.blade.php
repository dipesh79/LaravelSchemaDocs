@extends('laravelschemadocs::layouts.app')

@section('title', 'Laravel Schema Docs - Tables')

@section('content')
    <style>
        /* Page container styling */
        body, html {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        main.container {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        footer {
            position: sticky;
            bottom: 0;
        }

        .page-title {
            font-size: 1.8rem;
            margin-bottom: 6px;
        }

        .count {
            color: #555;
            margin-bottom: 16px;
            font-size: 0.95rem;
        }

        .search {
            margin-bottom: 18px;
        }

        .search input {
            padding: 10px 12px;
            width: 100%;
            max-width: 400px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .search input:focus {
            border-color: #0b5fff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(11, 95, 255, 0.15);
        }

        /* Table cards grid */
        .tables {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .tables li {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .tables li:hover {
            border-color: #0b5fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(11, 95, 255, 0.1);
        }

        .table-link {
            text-decoration: none;
            color: #111;
            display: block;
            padding: 14px 16px;
        }

        .table-link strong {
            display: block;
            font-size: 1.05rem;
            margin-bottom: 4px;
        }

        .table-link div {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        @media (max-width: 600px) {
            .search input {
                width: 100%;
            }
        }
    </style>

    <h1 class="page-title">Database Tables</h1>

    <div class="count">
        @php $count = !empty($schema['tables']) ? count($schema['tables']) : 0; @endphp
        {{ $count }} {{ Str::plural('table', $count) }}
    </div>

    <div class="search">
        <input id="table-search" type="search" placeholder="🔍 Filter tables...">
    </div>

    @if(!empty($schema['tables']))
        <ul class="tables" id="tables-list">
            @foreach($schema['tables'] as $tableName => $table)
                <li data-name="{{ $tableName }}">
                    <a class="table-link" href="{{ route('laravelschemadocs.show', ['name' => $tableName]) }}">
                        <strong>{{ $tableName }}</strong>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No tables found.</p>
    @endif

    <script>
        (function () {
            const input = document.getElementById('table-search');
            const list = document.getElementById('tables-list');
            if (!input || !list) return;
            input.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                Array.from(list.querySelectorAll('li')).forEach(function (li) {
                    const name = li.getAttribute('data-name') || '';
                    li.style.display = name.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        })();
    </script>
    <script>
        (function () {
            const input = document.getElementById('table-search');
            const list = document.getElementById('tables-list');
            const countDiv = document.querySelector('.count');

            if (!input || !list || !countDiv) return;

            const total = list.querySelectorAll('li').length;

            input.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                let visibleCount = 0;

                Array.from(list.querySelectorAll('li')).forEach(function (li) {
                    const name = li.getAttribute('data-name') || '';
                    const match = name.toLowerCase().includes(q);
                    li.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

                // Update the count dynamically
                const plural = visibleCount === 1 ? 'table' : 'tables';
                countDiv.textContent = `${visibleCount} ${plural}`;
            });

            // Initialize count on load
            const plural = total === 1 ? 'table' : 'tables';
            countDiv.textContent = `${total} ${plural}`;
        })();
    </script>

@endsection
