@extends('layouts.app')
@section('title', $product->name)

@push('styles')
<style>
    .main-img-wrap { background:#f8f8f8; border-radius:20px; aspect-ratio:1; display:flex; align-items:center; justify-content:center; overflow:hidden; position:sticky; top:80px; cursor:zoom-in; }
    .main-img-wrap img { width:100%; height:100%; object-fit:contain; padding:32px; transition:transform .3s ease; }
    .main-img-wrap img:hover { transform:scale(1.04); }
    .thumb-strip { display:flex; gap:10px; margin-top:14px; flex-wrap:wrap; }
    .thumb { width:64px; height:64px; border-radius:10px; background:#f8f8f8; border:2px solid transparent; cursor:pointer; overflow:hidden; transition:border-color .15s; padding:6px; object-fit:contain; }
    .thumb.active, .thumb:hover { border-color:#111; }
    .info-wrap { padding:8px 0 0 20px; }
    .badge-tag { display:inline-block; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; margin-right:6px; margin-bottom:12px; }
    .product-title { font-size:28px; font-weight:800; color:#111; line-height:1.25; margin-bottom:16px; }
    .price-main { font-size:38px; font-weight:900; color:#111; }
    .price-old  { font-size:18px; color:#bbb; text-decoration:line-through; margin-left:8px; }
    .save-badge { display:inline-block; background:#111; color:#fff; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; margin-left:8px; vertical-align:middle; }
    .divider { border:none; border-top:1px solid #f0f0f0; margin:20px 0; }
    .stock-ok  { color:#16a34a; font-size:13px; font-weight:600; }
    .stock-low { color:#d97706; font-size:13px; font-weight:600; }
    .stock-out { color:#dc2626; font-size:13px; font-weight:600; }
    .qty-wrap { display:flex; align-items:center; gap:0; border:1.5px solid #e5e7eb; border-radius:10px; width:fit-content; overflow:hidden; }
    .qty-wrap button { width:40px; height:42px; background:#fff; border:none; font-size:18px; font-weight:600; cursor:pointer; color:#111; transition:background .15s; }
    .qty-wrap button:hover { background:#f5f5f5; }
    .qty-wrap input { width:52px; height:42px; border:none; border-left:1.5px solid #e5e7eb; border-right:1.5px solid #e5e7eb; text-align:center; font-size:15px; font-weight:700; color:#111; outline:none; }
    .btn-cart { flex:1; background:#111; color:#fff; border:none; border-radius:12px; padding:14px 28px; font-size:14px; font-weight:700; cursor:pointer; transition:background .2s; font-family:inherit; }
    
    .btn-wish {
        width:48px; height:48px;
        border-radius:12px; background:#fff; cursor:pointer;
        transition:all .2s; font-family:inherit;
        border:1.5px solid #e5e7eb; color:#9ca3af;
        display:flex; align-items:center; justify-content:center;
        font-size:20px;
    }
    .btn-wish:hover { border-color:#fecaca; color:#ef4444; background:#fef2f2; }
    .btn-wish.active { border-color:#fecaca; color:#ef4444; background:#fef2f2; }
    .spec-row { display:flex; padding:11px 0; border-bottom:1px solid #f5f5f5; font-size:14px; }
    .spec-key  { width:130px; color:#999; font-weight:600; flex-shrink:0; }
    .spec-val  { color:#111; font-weight:500; }
    .tab-nav { display:flex; gap:0; border-bottom:2px solid #f0f0f0; margin-bottom:28px; }
    .tab-btn { padding:13px 24px; font-size:14px; font-weight:700; color:#999; background:none; border:none; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px; transition:all .15s; }
    .tab-btn.active { color:#111; border-bottom-color:#111; }
    .rating-summary { display:flex; align-items:center; gap:20px; background:#f8f8f8; border-radius:16px; padding:24px 28px; margin-bottom:24px; }
    .rating-big { font-size:56px; font-weight:900; color:#111; line-height:1; }
    .stars { color:#f59e0b; font-size:18px; letter-spacing:2px; }
    .review-card { border:1.5px solid #f0f0f0; border-radius:14px; padding:18px 20px; margin-bottom:12px; }
    .rev-av { width:36px; height:36px; border-radius:50%; background:#111; color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0; }
    .review-form-wrap { border:1.5px solid #f0f0f0; border-radius:16px; padding:24px; margin-top:24px; }
    .star-pick label { font-size:28px; cursor:pointer; color:#e5e7eb; transition:color .1s; }
    .form-input { width:100%; border:1.5px solid #e5e7eb; border-radius:10px; padding:10px 14px; font-size:14px; outline:none; transition:border-color .15s; font-family:inherit; }
    .form-input:focus { border-color:#111; }
    .related-title { font-size:20px; font-weight:800; color:#111; margin-bottom:20px; }

    /* Image Modal */
    #imgModal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.92); z-index:9999; align-items:center; justify-content:center; cursor:zoom-out; }
    #imgModal img { max-width:90vw; max-height:88vh; border-radius:10px; object-fit:contain; box-shadow:0 24px 80px rgba(0,0,0,.6); }
    #imgModal .close-btn { position:fixed; top:20px; right:20px; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.2); color:#fff; font-size:22px; width:44px; height:44px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(8px); }
</style>
@endpush

@section('content')

{{-- Image Fullscreen Modal --}}
<div id="imgModal" onclick="closeModal()">
    <img id="modalImg" src="" alt=""/>
    <button class="close-btn" onclick="closeModal()">✕</button>
</div>

<div class="container py-5">

    {{-- Breadcrumb --}}
    <nav style="font-size:12px;color:#bbb;" class="mb-4">
        <a href="{{ route('home') }}" style="color:#bbb;text-decoration:none;">{{ __('app.home') }}</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products') }}" style="color:#bbb;text-decoration:none;">{{ app()->getLocale()=='km' ? 'ហាង' : 'Shop' }}</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products') }}?category_id={{ $product->category_id }}" style="color:#bbb;text-decoration:none;">{{ $product->category->name }}</a>
        <span class="mx-2">/</span>
        <span style="color:#111;font-weight:600;">{{ Str::limit($product->name, 30) }}</span>
    </nav>

    <div class="row g-5">

        {{-- Images --}}
        <div class="col-lg-6">
            <div class="main-img-wrap" onclick="openModal('{{ $product->image_url }}')">
                <img id="mainImg"
                     src="{{ $product->image_url ?? '' }}"
                     onerror="this.style.padding='0'"
                     alt="{{ $product->name }}"/>
            </div>
            @if($product->images->isNotEmpty())
            <div class="thumb-strip">
                <img src="{{ $product->image_url }}" class="thumb active" onclick="setImg(this, '{{ $product->image_url }}')"/>
                @foreach($product->images as $img)
                    <img src="{{ $img->image_url }}" class="thumb" onclick="setImg(this, '{{ $img->image_url }}')"/>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="col-lg-6">
            <div class="info-wrap">

                {{-- Tags --}}
                <div>
                    @if($product->brand)
                        <span class="badge-tag" style="background:#f0f0f0;color:#555;">{{ $product->brand->name }}</span>
                    @endif
                    <span class="badge-tag" style="background:#fff8e1;color:#92400e;">{{ $product->category->name }}</span>
                    @if($product->is_on_sale)
                        <span class="badge-tag" style="background:#fee2e2;color:#991b1b;">{{ app()->getLocale()=='km' ? 'បញ្ចុះតម្លៃ' : 'Sale' }}</span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="product-title">{{ $product->name }}</h1>

                <hr class="divider"/>

                {{-- Price --}}
                <div class="mb-4">
                    <span class="price-main">${{ number_format($product->effective_price, 2) }}</span>
                    @if($product->is_on_sale)
                        <span class="price-old">${{ number_format($product->price, 2) }}</span>
                        <span class="save-badge">{{ app()->getLocale()=='km' ? 'សន្សំ' : 'Save' }} {{ $product->discount_percent }}%</span>
                    @endif
                </div>


            

                @auth
                @php
                    $inWishlist = \App\Models\Wishlist::where('user_id', Auth::id())
                        ->where('product_id', $product->id)->exists();
                @endphp
                    @if($product->stock > 0)
                    {{-- Add to Cart --}}
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}"/>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <span style="font-size:14px;font-weight:600;color:#555;">{{ __('app.qty') }}</span>
                            <div class="qty-wrap">
                                <button type="button" onclick="changeQty(-1)">−</button>
                                <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="{{ $product->stock }}"/>
                                <button type="button" onclick="changeQty(1)">+</button>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-cart" style="flex:1;">
                                {{ __('app.add_to_cart') }}
                            </button>
                            {{-- Wishlist --}}
                            <button type="button"
                                    onclick="document.getElementById('wl-form').submit()"
                                    class="btn-wish {{ $inWishlist ? 'active' : '' }}"
                                    title="{{ $inWishlist ? (app()->getLocale()=='km' ? 'លប់ចេញ' : 'Remove from Wishlist') : (app()->getLocale()=='km' ? 'រក្សាទុក' : 'Add to Wishlist') }}">
                                {{ $inWishlist ? '-' : '+' }}
                            </button>
                        </div>
                    </form>
                    <form id="wl-form" action="{{ route('wishlist.toggle', $product->id) }}" method="POST" style="display:none;">
                        @csrf
                    </form>

                    @else
                        <div style="background:#fef2f2;color:#dc2626;border-radius:12px;padding:14px 18px;font-size:14px;font-weight:600;">
                            {{ app()->getLocale()=='km' ? 'ផលិតផលនេះអស់ស្តុក' : 'Currently out of stock' }}
                        </div>
                        {{-- Still allow wishlist for out of stock --}}
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" style="margin-top:10px;">
                            @csrf
                            <button type="submit"
                                    class="btn-wish {{ $inWishlist ? 'active' : '' }}"
                                    title="{{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                                {{ $inWishlist ? '❤️' : '🤍' }}
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" style="display:block;background:#111;color:#fff;text-align:center;padding:15px;border-radius:12px;font-weight:700;font-size:15px;text-decoration:none;margin-bottom:10px;">
                        {{ app()->getLocale()=='km' ? 'ចូលដើម្បីទិញ' : 'Login to Add to Cart' }}
                    </a>
                    <a href="{{ route('login') }}" style="display:flex;align-items:center;justify-content:center;gap:6px;border:1.5px solid #e5e7eb;border-radius:12px;padding:12px;color:#6b7280;text-decoration:none;font-weight:600;font-size:13px;">
                        🤍 {{ app()->getLocale()=='km' ? 'ចូលដើម្បីរក្សាទុក' : 'Login to Save' }}
                    </a>
                @endauth

                <hr class="divider" style="margin-top:20px;">

                {{-- Specs --}}
                <div>
                    <div class="spec-row"><span class="spec-key">{{ app()->getLocale()=='km' ? 'ម៉ាក' : 'Brand' }}</span><span class="spec-val">{{ $product->brand->name ?? '—' }}</span></div>
                    <div class="spec-row"><span class="spec-key">{{ app()->getLocale()=='km' ? 'ប្រភេទ' : 'Category' }}</span><span class="spec-val">{{ $product->category->name }}</span></div>
                    <div class="spec-row"><span class="spec-key">{{ app()->getLocale()=='km' ? 'ស្ថានភាព' : 'Availability' }}</span><span class="spec-val">{{ $product->stock_status }}</span></div>
                    <div class="spec-row"><span class="spec-key">{{ app()->getLocale()=='km' ? 'ស្តុក' : 'Stock' }}</span><span class="spec-val">{{ $product->stock }} {{ app()->getLocale()=='km' ? 'មុខ' : 'units' }}</span></div>
                </div>

            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mt-5 pt-4" style="border-top:2px solid #f0f0f0;">
        <div class="tab-nav">
            <button class="tab-btn active" onclick="switchTab(this,'tab-desc')">{{ __('app.description') }}</button>
            <button class="tab-btn" onclick="switchTab(this,'tab-reviews')">{{ __('app.reviews') }} ({{ $product->review_count }})</button>
        </div>

        <div id="tab-desc">
            <div style="max-width:680px;">
                <p style="font-size:15px;line-height:1.9;color:#555;">
                    {{ $product->description ?? (app()->getLocale()=='km' ? 'មិនមានការពិពណ៌នា' : 'No description available.') }}
                </p>
            </div>
        </div>

        <div id="tab-reviews" style="display:none;">
            <div style="max-width:680px;">
                <div class="rating-summary">
                    <div class="rating-big">{{ $product->average_rating }}</div>
                    <div>
                        <div class="stars">{{ str_repeat('★', (int)$product->average_rating) }}{{ str_repeat('☆', 5-(int)$product->average_rating) }}</div>
                        <div style="font-size:13px;color:#999;margin-top:4px;">
                            {{ app()->getLocale()=='km' ? 'ផ្អែកលើ '.$product->review_count.' មតិ' : 'Based on '.$product->review_count.' reviews' }}
                        </div>
                    </div>
                </div>

                @forelse($product->reviews as $review)
                <div class="review-card">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rev-av">{{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}</div>
                        <div>
                            <div style="font-weight:700;font-size:14px;color:#111;">{{ $review->user->name }}</div>
                            <div>
                                <span class="stars" style="font-size:13px;">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5-$review->rating) }}</span>
                                <span style="font-size:12px;color:#bbb;margin-left:6px;">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @if($review->comment)
                        <p style="font-size:14px;color:#555;margin:0;line-height:1.7;">{{ $review->comment }}</p>
                    @endif
                </div>
                @empty
                    <p style="color:#bbb;font-size:14px;padding:20px 0;">{{ __('app.no_reviews') }}</p>
                @endforelse

                @auth
                <div class="review-form-wrap">
                    <h6 style="font-size:15px;font-weight:700;color:#111;margin-bottom:18px;">
                        {{ $userReview ? (app()->getLocale()=='km' ? 'ធ្វើបច្ចុប្បន្នភាពមតិ' : 'Update Your Review') : __('app.write_review') }}
                    </h6>
                    <form action="{{ route('product.review', $product->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:8px;">{{ __('app.your_rating') }}</label>
                            <div class="star-pick d-flex gap-1">
                                @for($i=1;$i<=5;$i++)
                                <label data-rating="{{ $i }}">
                                    <input type="radio" name="rating" value="{{ $i }}" style="display:none;" {{ ($userReview && $userReview->rating==$i) ? 'checked' : '' }}/>★
                                </label>
                                @endfor
                            </div>
                        </div>
                        <div class="mb-4">
                            <textarea name="comment" class="form-input" rows="4"
                                      placeholder="{{ app()->getLocale()=='km' ? 'ចែករំលែកបទពិសោធន៍...' : 'Share your experience...' }}">{{ $userReview?->comment }}</textarea>
                        </div>
                        <button type="submit" class="btn-cart" style="max-width:180px;">{{ __('app.submit_review') }}</button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if($related->isNotEmpty())
    <div class="mt-5 pt-4" style="border-top:2px solid #f0f0f0;">
        <p class="related-title">{{ __('app.related_products') }}</p>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($related as $p)
                <div class="col">@include('shop._product-card', ['product' => $p])</div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function setImg(el, src) {
    document.getElementById('mainImg').src = src;
    document.getElementById('modalImg').src = src;
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}
function changeQty(delta) {
    const inp = document.getElementById('qtyInput');
    inp.value = Math.max(1, Math.min(parseInt(inp.max)||99, parseInt(inp.value||1)+delta));
}
function switchTab(btn, id) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    ['tab-desc','tab-reviews'].forEach(t => document.getElementById(t).style.display = 'none');
    document.getElementById(id).style.display = 'block';
}
function openModal(src) {
    document.getElementById('modalImg').src = src || document.getElementById('mainImg').src;
    document.getElementById('imgModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('imgModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeModal(); });

const starLabels = document.querySelectorAll('.star-pick label');
starLabels.forEach((label, idx) => {
    label.addEventListener('mouseenter', () => starLabels.forEach((s,i) => s.style.color = i<=idx ? '#f59e0b' : '#e5e7eb'));
    label.addEventListener('click',      () => starLabels.forEach((s,i) => s.style.color = i<=idx ? '#f59e0b' : '#e5e7eb'));
});
document.querySelector('.star-pick')?.addEventListener('mouseleave', () => {
    @if($userReview)
        starLabels.forEach((s,i) => s.style.color = i < {{ $userReview->rating }} ? '#f59e0b' : '#e5e7eb');
    @else
        starLabels.forEach(s => s.style.color = '#e5e7eb');
    @endif
});
@if($userReview)
starLabels.forEach((s,i) => s.style.color = i < {{ $userReview->rating }} ? '#f59e0b' : '#e5e7cf');
@endif
</script>
@endpush
@endsection