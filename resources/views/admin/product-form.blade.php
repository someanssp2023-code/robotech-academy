@extends('layouts.admin')
@section('title', $product ? 'Edit Product' : 'Add Product')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.products') }}" class="qa-btn">← Back</a>
    <h5 class="mb-0 fw-bold" style="font-size:16px;">{{ $product ? 'Edit: '.$product->name : 'Add New Product' }}</h5>
</div>

<form action="{{ $product ? route('admin.product.update', $product->id) : route('admin.product.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if($product) @method('PUT') @endif

    <div class="row g-4">

        {{-- ── Left Column ── --}}
        <div class="col-lg-8">

            {{-- Basic Info --}}
            <div class="panel p-4 mb-4">
               

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Product Name *</label>
                    <input type="text" name="name" class="form-ctrl" required
                           value="{{ old('name', $product?->name) }}"
                           placeholder="e.g. iPhone 15 Pro Max"/>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Category *</label>
                        <select name="category_id" class="form-ctrl" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product?->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Brand</label>
                        <select name="brand_id" class="form-ctrl">
                            <option value="">No Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product?->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Short Description</label>
                    <input type="text" name="short_description" class="form-ctrl"
                           value="{{ old('short_description', $product?->short_description) }}"
                           placeholder=""/>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold small">Full Description</label>
                    <textarea name="description" class="form-ctrl" rows="5"
                              placeholder="">{{ old('description', $product?->description) }}</textarea>
                </div>
            </div>

            {{-- Pricing & Stock --}}
            <div class="panel p-4">
                <h6 style="font-size:13px;font-weight:700;color:#111;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                    Pricing & Stock
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Regular Price ($) *</label>
                        <input type="number" name="price" class="form-ctrl" step="0.01" min="0" required
                               value="{{ old('price', $product?->price) }}" placeholder="0.00"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">
                            Sale Price ($) <span style="color:#9ca3af;font-weight:400;">optional</span>
                        </label>
                        <input type="number" name="sale_price" class="form-ctrl" step="0.01" min="0"
                               value="{{ old('sale_price', $product?->sale_price) }}"
                               placeholder="0.00"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Stock Quantity *</label>
                        <input type="number" name="stock" class="form-ctrl" min="0" required
                               value="{{ old('stock', $product?->stock ?? 0) }}" placeholder="0"/>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Right Column ── --}}
        <div class="col-lg-4">

            {{-- Product Image --}}
            <div class="panel p-4 mb-4">
                <h6 style="font-size:13px;font-weight:700;color:#111;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                    Product Image
                </h6>

                @if($product?->image_url)
                    <img src="{{ $product->image_url }}"
                         onerror="this.style.background='#f3f4f6'"
                         style="width:100%;height:160px;object-fit:contain;border-radius:10px;background:#f9fafb;padding:12px;margin-bottom:12px;"/>
                @endif

                {{-- Upload area --}}
                <div id="upload-area"
                     style="border:2px dashed #e5e7eb;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:border-color .15s;"
                     onclick="document.getElementById('imageInput').click()"
                     ondragover="event.preventDefault();this.style.borderColor='#f59e0b';"
                     ondragleave="this.style.borderColor='#e5e7eb';"
                     ondrop="handleDrop(event)">
                    <div id="upload-placeholder">
                        <div style="font-size:28px;color:#d1d5db;margin-bottom:6px;">↑</div>
                        <div style="font-size:13px;font-weight:600;color:#374151;">Click or drag image here</div>
                        
                    </div>
                    <img id="imgPreview" style="display:none;width:100%;height:140px;object-fit:contain;border-radius:8px;"/>
                </div>
                <input type="file" name="image" id="imageInput" accept="image/*"
                       style="display:none;" onchange="previewImg(this)"/>
                <button type="button" id="removeImg"
                        style="display:none;margin-top:8px;width:100%;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;padding:7px;font-size:12px;font-weight:600;cursor:pointer;"
                        onclick="removeImg()">
                    Remove Image
                </button>
            </div>

            {{-- Options --}}
            <div class="panel p-4 mb-4">
                <h6 style="font-size:13px;font-weight:700;color:#111;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6;">
                    Options
                </h6>

                <label class="d-flex align-items-center gap-2 mb-3" style="cursor:pointer;">
                    <input type="checkbox" name="is_featured" value="1"
                           style="width:16px;height:16px;accent-color:#f59e0b;"
                           {{ old('is_featured', $product?->is_featured) ? 'checked' : '' }}/>
                    <span class="fw-semibold small">Featured Product</span>
                </label>

                @if($product)
                <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1"
                           style="width:16px;height:16px;accent-color:#f59e0b;"
                           {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }}/>
                    <span class="fw-semibold small">Active (visible in store)</span>
                </label>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2">
                <button type="submit" class="qa-btn qa-primary flex-grow-1" style="padding:12px;justify-content:center;">
                    {{ $product ? 'Update Product' : 'Create Product' }}
                </button>
                <a href="{{ route('admin.products') }}" class="qa-btn" style="padding:12px 16px;">Cancel</a>
            </div>

        </div>
    </div>
</form>

@push('scripts')
<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('upload-placeholder').style.display = 'none';
            const prev = document.getElementById('imgPreview');
            prev.src = e.target.result;
            prev.style.display = 'block';
            document.getElementById('removeImg').style.display = 'block';
            document.getElementById('upload-area').style.borderColor = '#f59e0b';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImg() {
    document.getElementById('imageInput').value = '';
    document.getElementById('imgPreview').style.display = 'none';
    document.getElementById('imgPreview').src = '';
    document.getElementById('upload-placeholder').style.display = 'block';
    document.getElementById('removeImg').style.display = 'none';
    document.getElementById('upload-area').style.borderColor = '#e5e7eb';
}

function handleDrop(event) {
    event.preventDefault();
    const file = event.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const input = document.getElementById('imageInput');
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        previewImg(input);
    }
}
</script>
@endpush
@endsection