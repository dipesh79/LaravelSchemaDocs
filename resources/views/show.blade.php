@extends('laravelschemadocs::layouts.app')

@section('title', 'Table: ' . ($name ?? ''))

@section('content')
    <a href="{{ route('laravelschemadocs.index') }}"
       style="display:inline-block;margin-bottom:12px;color:#0b5fff;text-decoration:none;">&larr; Back to tables</a>

    <h1 class="page-title">{{ $name }}</h1>

    @if(!empty($table['description']))
        <div class="description" style="margin-bottom:10px;">{{ $table['description'] }}</div>
    @endif

    @if(!empty($table['columns']) && is_array($table['columns']))
        <table style="width:100%;border-collapse:collapse;margin-top:12px;">
            <thead>
            <tr>
                <th style="border:1px solid #ddd;padding:8px;text-align:left;background:#f8f8f8;">Column</th>
                <th style="border:1px solid #ddd;padding:8px;text-align:left;background:#f8f8f8;">Type</th>
                <th style="border:1px solid #ddd;padding:8px;text-align:left;background:#f8f8f8;">Nullable</th>
                <th style="border:1px solid #ddd;padding:8px;text-align:left;background:#f8f8f8;">Default</th>
                <th style="border:1px solid #ddd;padding:8px;text-align:left;background:#f8f8f8;">Logic</th>
                <th style="border:1px solid #ddd;padding:8px;text-align:left;background:#f8f8f8;">Relation</th>
            </tr>
            </thead>
            <tbody>
            @foreach($table['columns'] as $colName => $col)
                @php
                    $relationTable = null;

                    if (!empty($col['relation']) && is_array($col['relation']) && !empty($col['relation']['table'])) {
                        $relationTable = $col['relation']['table'];
                    } elseif (!empty($col['references']) && is_string($col['references'])) {
                        $parts = preg_split('/[.\s(]+/', $col['references']);
                        $relationTable = $parts[0] ?? null;
                    }

                    if (empty($relationTable) && !empty($table['relations']) && is_array($table['relations'])) {
                        foreach ($table['relations'] as $rel) {
                            if (!is_array($rel)) continue;
                            if (!empty($rel['column']) && $rel['column'] === $colName) {
                                $target = $rel['references'] ?? $rel['table'] ?? null;
                                if (is_string($target)) {
                                    $parts = preg_split('/[.\s(]+/', $target);
                                    $relationTable = $parts[0] ?? $target;
                                } else {
                                    $relationTable = $target;
                                }
                                break;
                            }
                        }
                    }
                @endphp
                <tr>
                    <td style="border:1px solid #ddd;padding:8px;">{{ $colName }}</td>
                    <td style="border:1px solid #ddd;padding:8px;">{{ $col['type'] ?? '' }}</td>
                    <td style="border:1px solid #ddd;padding:8px;">{{ !empty($col['nullable']) ? 'yes' : 'no' }}</td>
                    <td style="border:1px solid #ddd;padding:8px;">{{ $col['default'] ?? '' }}</td>
                    <td style="border:1px solid #ddd;padding:8px;font-style:italic;color:#555;">{{ $col['logic'] ?? '' }}</td>
                    <td style="border:1px solid #ddd;padding:8px;">
                        @if($relationTable)
                            <a href="{{ route('laravelschemadocs.show', ['name' => $relationTable]) }}"
                               style="color:#0b5fff;text-decoration:none;">{{ $relationTable }}</a>
                            @else
                                &mdash;
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>No columns available for this table.</p>
    @endif
@endsection
