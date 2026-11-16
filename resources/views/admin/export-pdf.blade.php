<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'Report' }}</title>
<style>
    /* ---- Global Resets & Typography ---- */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 12px;
        color: #111;
        background: #f9f9f9;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }

    /* ---- Screen-only Print Bar ---- */
    .print-bar {
        position: fixed; top: 0; left: 0; right: 0; z-index: 999;
        background: #111; color: #fff;
        padding: 12px 24px; display: flex; justify-content: space-between; align-items: center;
    }
    .btn-print {
        background: #fff; color: #111; border: none;
        padding: 8px 16px; font-size: 12px; font-weight: 600; 
        cursor: pointer; border-radius: 4px; transition: background 0.2s;
    }
    .btn-print:hover { background: #e5e5e5; }

    /* ---- Page Layout ---- */
    .page {
        padding: 80px 40px 40px;
        max-width: 1100px;
        margin: 0 auto;
        background: #fff;
        min-height: 100vh;
        box-shadow: 0 0 10px rgba(0,0,0,0.05); /* Subtle shadow for screen view */
    }

    /* ---- Header ---- */
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-bottom: 2px solid #111;
        padding-bottom: 16px;
        margin-bottom: 24px;
    }
    .report-title {
        font-size: 28px;
        font-weight: 700;
        letter-spacing: -0.5px;
        text-transform: uppercase;
    }
    .report-meta {
        text-align: right;
        font-size: 11px;
        color: #555;
        line-height: 1.4;
    }
    .report-meta strong {
        color: #111;
        font-size: 13px;
    }

    /* ---- Table ---- */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
    }
    thead th {
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #666;
        padding: 12px 8px;
        border-bottom: 1px solid #111;
    }
    
    /* Specific Column Alignments */
    thead th:first-child,
    tbody td:first-child { padding-left: 0; }
    
    thead th:last-child,
    tbody td:last-child { padding-right: 0; text-align: right; }
    
    /* Align numeric columns to the right */
    thead th.text-right,
    tbody td.text-right { text-align: right; }

    tbody td {
        padding: 12px 8px;
        border-bottom: 1px solid #e5e5e5;
        vertical-align: top;
        font-size: 12px;
    }
    tbody tr:hover {
        background-color: #fafafa; /* Slight highlight on hover for screen */
    }

    /* Typography inside table cells */
    .cell-bold { font-weight: 600; color: #000; }
    .cell-sub  { font-size: 11px; color: #666; display: block; margin-top: 2px; }
    .cell-mono { font-family: 'Courier New', Courier, monospace; font-size: 11px; }

    /* ---- Footer ---- */
    .doc-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #e5e5e5;
        padding-top: 16px;
        font-size: 10px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .footer-page { font-weight: 700; color: #111; }

    /* ---- Print Adjustments ---- */
    @media print {
        body { background: #fff; }
        .print-bar { display: none !important; }
        .page { 
            padding: 0; 
            max-width: 100%; 
            box-shadow: none; 
            min-height: auto;
        }
        @page { size: A4 landscape; margin: 1cm; }
        tbody tr { page-break-inside: avoid; }
        tbody tr:hover { background-color: transparent; } /* Remove hover effect */
    }
</style>
</head>
<body>

{{-- Screen-only print controls --}}
<div class="print-bar">
    <span style="font-weight: 600; font-size: 14px;">{{ $title ?? 'Report' }}</span>
    <button class="btn-print" onclick="window.print()">Print / Save as PDF</button>
</div>

<div class="page">

    {{-- Header --}}
    <div class="report-header">
        <div>
            <h1 class="report-title">{{ $title ?? 'Report' }}</h1>
            @if(!empty($activeFilters))
            <div style="margin-top: 8px; font-size: 11px; color: #555;">
                <strong>Filters:</strong> {{ implode(', ', $activeFilters) }}
            </div>
            @endif
        </div>
        <div class="report-meta">
            <div><strong>{{ config('app.name') }}</strong></div>
            <div>{{ now()->format('d M Y, H:i') }}</div>
            <div>Total Records: {{ $rows->count() }}</div>
        </div>
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                @foreach($columns as $index => $col)
                {{-- Automatically right-align columns that look like numbers --}}
                <th class="{{ in_array(strtolower($col['label']), ['price', 'total', 'amount', 'qty', 'units', 'stock']) ? 'text-right' : '' }}">
                    {{ $col['label'] }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                @foreach($columns as $col)
                <td class="{{ in_array(strtolower($col['label']), ['price', 'total', 'amount', 'qty', 'units', 'stock']) ? 'text-right' : '' }}">
                    {!! $col['render']($row) !!}
                </td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($columns) }}" style="text-align:center; padding: 40px; color: #666;">
                    No records found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="doc-footer">
        <div>{{ config('app.name') }} &mdash; </div>
        <div class="footer-page">Page 1</div>
    </div>

</div>
</body>
</html>