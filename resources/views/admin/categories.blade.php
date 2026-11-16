@extends('layouts.admin')
@section('title', app()->getLocale()=='km' ? 'ប្រភេទ' : 'Categories')

@section('content')

{{-- Filter / action bar --}}
<div class="panel mb-4">
    <form method="GET" action="{{ route('admin.categories') }}">
        <div class="p-3 d-flex flex-wrap gap-2 align-items-center">
            <input type="text" name="search" class="form-ctrl" style="width:220px;"
                   placeholder="{{ app()->getLocale()=='km' ? 'ស្វែងរកប្រភេទ...' : 'Search categories...' }}"
                   value="{{ request('search') }}"/>
            <button type="submit" class="qa-btn qa-primary">{{ app()->getLocale()=='km' ? 'ស្វែងរក' : 'Search' }}</button>
            @if(request('search'))
                <a href="{{ route('admin.categories') }}" class="qa-btn">{{ app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear' }}</a>
            @endif

            {{-- Export buttons --}}
            <a href="{{ route('admin.categories.export.excel', request()->query()) }}"
               class="qa-btn" style="border-color:#16a34a;color:#16a34a;">
                ⬇ Excel
            </a>
            <a href="{{ route('admin.categories.export.pdf', request()->query()) }}"
               target="_blank"
               class="qa-btn" style="border-color:#dc2626;color:#dc2626;">
                ⬇ PDF
            </a>

            <a href="{{ route('admin.category.create') }}" class="qa-btn qa-primary ms-auto">
                + {{ app()->getLocale()=='km' ? 'បន្ថែមប្រភេទ' : 'Add Category' }}
            </a>
        </div>
    </form>
</div>

{{-- Bulk action bar --}}
<div id="bulk-bar" style="display:none;" class="mb-3">
    <div class="d-flex align-items-center gap-3 px-3 py-2 rounded"
         style="background:#fef9c3;border:1px solid #fde68a;">
        <span style="font-size:13px;font-weight:700;color:#92400e;" id="bulk-count">0 selected</span>
        <form id="bulk-delete-form" method="POST" action="{{ route('admin.categories.bulk-delete') }}"
              onsubmit="return confirmBulkDelete()">
            @csrf
            @method('DELETE')
            <div id="bulk-ids"></div>
            <button type="submit" class="qa-btn" style="border-color:#fecaca;color:#dc2626;background:#fff;">
                {{ app()->getLocale()=='km' ? 'លប់ដែលបានជ្រើស' : 'Delete Selected' }}
            </button>
        </form>
        <button type="button" class="qa-btn" onclick="clearSelection()">
            {{ app()->getLocale()=='km' ? 'លប់ចោល' : 'Clear' }}
        </button>
    </div>
</div>

{{-- Table --}}
<div class="panel">
    <div class="panel-head">
        <h6>{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Categories' }} ({{ $categories->count() }})</h6>
        <span style="font-size:12px;color:#9ca3af;">{{ $categories->count() }} {{ app()->getLocale()=='km' ? 'ប្រភេទសរុប' : 'total' }}</span>
    </div>
    <div class="table-responsive">
        <table class="ra-table">
            <thead>
                <tr>
                    <th style="width:40px;padding-left:16px;">
                        <input type="checkbox" id="select-all"
                               style="width:16px;height:16px;cursor:pointer;accent-color:#111;"
                               title="{{ app()->getLocale()=='km' ? 'ជ្រើសទាំងអស់' : 'Select all' }}">
                    </th>
                    <th style="padding-left:8px;">No</th>
                    <th>{{ app()->getLocale()=='km' ? 'ឈ្មោះ' : 'Name' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ផលិតផល' : 'Products' }}</th>
                    <th>{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Status' }}</th>
                    <th style="padding-right:20px;text-align:right;">{{ app()->getLocale()=='km' ? 'សកម្មភាព' : 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    {{-- Checkbox --}}
                    <td style="padding-left:16px;">
                        <input type="checkbox" class="row-check" value="{{ $cat->id }}"
                               style="width:16px;height:16px;cursor:pointer;accent-color:#111;">
                    </td>

                    <td style="padding-left:8px;font-size:12px;color:#9ca3af;">{{ $cat->id }}</td>
                    <td style="font-weight:600;color:#111;font-size:13px;">{{ $cat->name }}</td>
                    <td style="font-size:13px;color:#374151;">
                        {{ $cat->products_count }}
                        <span style="color:#9ca3af;font-size:12px;"> {{ app()->getLocale()=='km' ? 'មុខ' : 'products' }}</span>
                    </td>
                    <td>
                        <span class="pill {{ $cat->is_active ? 'pill-delivered' : 'pill-cancelled' }}">
                            {{ $cat->is_active ? (app()->getLocale()=='km' ? 'សកម្ម' : 'Active') : (app()->getLocale()=='km' ? 'លាក់' : 'Hidden') }}
                        </span>
                    </td>
                    <td style="padding-right:20px;text-align:right;">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.category.edit', $cat->id) }}" class="view-btn">
                                {{ app()->getLocale()=='km' ? 'កែ' : 'Edit' }}
                            </a>
                            <form action="{{ route('admin.category.delete', $cat->id) }}" method="POST"
                                  onsubmit="return confirm('{{ app()->getLocale()=='km' ? 'លុបចោលប្រភេទ?' : 'Delete this category?' }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="view-btn" style="border-color:#fecaca;color:#dc2626;background:#fff;">
                                    {{ app()->getLocale()=='km' ? 'លុប' : 'Delete' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                        {{ app()->getLocale()=='km' ? 'មិនទាន់មានប្រភេទ' : 'No categories yet.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
// Select all
document.getElementById('select-all').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    updateBulkBar();
});

// Individual row checkbox
document.querySelectorAll('.row-check').forEach(cb => {
    cb.addEventListener('change', function () {
        const total   = document.querySelectorAll('.row-check').length;
        const checked = document.querySelectorAll('.row-check:checked').length;
        const selectAll = document.getElementById('select-all');
        selectAll.indeterminate = checked > 0 && checked < total;
        selectAll.checked = checked === total;
        updateBulkBar();
    });
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    document.getElementById('bulk-count').textContent = checked.length + ' selected';
    document.getElementById('bulk-bar').style.display = checked.length > 0 ? 'block' : 'none';
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById('select-all');
    selectAll.checked = false;
    selectAll.indeterminate = false;
    document.getElementById('bulk-bar').style.display = 'none';
}

function confirmBulkDelete() {
    const checked = document.querySelectorAll('.row-check:checked');
    if (checked.length === 0) return false;
    if (!confirm('Delete ' + checked.length + ' selected categor' + (checked.length === 1 ? 'y' : 'ies') + '? This cannot be undone.')) return false;
    const container = document.getElementById('bulk-ids');
    container.innerHTML = '';
    checked.forEach(cb => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ids[]';
        inp.value = cb.value;
        container.appendChild(inp);
    });
    return true;
}
</script>
@endpush

@endsection