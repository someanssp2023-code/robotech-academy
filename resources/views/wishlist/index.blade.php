@extends('layouts.app')
@section('title', app()->getLocale()=='km' ? 'បញ្ជីចូលចិត្ត' : 'My Wishlist')

@section('content')
<style>
.wl-wrap { max-width: 1080px; margin: 0 auto; padding: 36px 16px 80px; }
.wl-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
.wl-count { font-size: 14px; font-weight: 500; color: #94a3b8; }
.alert-s { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 16px; }
.alert-e { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 16px; }

/* Grid */
.wl-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
@media(max-width:576px) { .wl-grid { grid-template-columns: repeat(2,1fr); gap: 12px; } }

/* Card */
.wl-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    transition: box-shadow .2s, transform .2s;
    position: relative;
}
.wl-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.09); transform: translateY(-2px); }

/* Remove btn */
.btn-wl-remove {
    position: absolute; top: 10px; right: 10px; z-index: 2;
    width: 32px; height: 32px; border-radius: 50%;
    background: rgba(255,255,255,.9); border: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 14px; color: #94a3b8;
    transition: all .2s; backdrop-filter: blur(4px);
}
.btn-wl-remove:hover { background: #fee2e2; border-color: #fecaca; color: #dc2626; }

/* Sale badge */
.badge-sale {
    position: absolute; top: 10px; left: 10px; z-index: 2;
    background: #ef4444; color: #fff;
    font-size: 10px; font-weight: 700;
    padding: 3px 8px; border-radius: 6px;
}

/* Image */
.wl-img-wrap {
    background: #f8fafc; aspect-ratio: 1;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; padding: 16px;
}
.wl-img-wrap img {
    width: 100%; height: 100%; object-fit: contain;
    transition: transform .3s;
}
.wl-card:hover .wl-img-wrap img { transform: scale(1.05); }

/* Body */
.wl-body { padding: 14px; }
.wl-brand { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #f59e0b; margin-bottom: 4px; }
.wl-name { font-size: 13px; font-weight: 700; color: #0f172a; line-height: 1.4; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.wl-price { font-size: 16px; font-weight: 800; color: #f59e0b; }
.wl-price-old { font-size: 12px; color: #94a3b8; text-decoration: line-through; margin-left: 4px; }
.wl-stock-low { font-size: 11px; color: #d97706; font-weight: 600; margin-top: 4px; }
.wl-stock-out { font-size: 11px; color: #dc2626; font-weight: 600; margin-top: 4px; }

/* Actions */
.wl-actions { display: flex; gap: 8px; margin-top: 12px; }
.btn-add-cart {
    flex: 1; background: #0f172a; color: #fff;
    border: none; border-radius: 8px; padding: 9px;
    font-size: 12px; font-weight: 700; cursor: pointer;
    font-family: inherit; transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 5px;
}
.btn-add-cart:hover { background: #f59e0b; color: #000; }
.btn-add-cart:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
.btn-view {
    width: 36px; height: 36px; border-radius: 8px;
    border: 1.5px solid #e2e8f0; background: #fff;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none; color: #374151; font-size: 14px;
    transition: all .2s; flex-shrink: 0;
}
.btn-view:hover { border-color: #0f172a; color: #0f172a; }

/* Empty */
.empty-state { text-align: center; padding: 80px 20px; background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; }
</style>

<div class="wl-wrap">
    <div class="wl-title">
        ❤️ {{ app()->getLocale()=='km' ? 'បញ្ជីចូលចិត្ត' : 'My Wishlist' }}
        @if($wishlists->isNotEmpty())
        <span class="wl-count">({{ $wishlists->count() }} {{ app()->getLocale()=='km' ? 'មុខ' : 'items' }})</span>
        @endif
    </div>

    @if(session('success'))
    <div class="alert-s">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-e">❌ {{ session('error') }}</div>
    @endif

    @if($wishlists->isEmpty())
    {{-- Empty --}}
    <div class="empty-state">
        <div style="font-size:64px;margin-bottom:16px;">❤️</div>
        <div style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px;">
            {{ app()->getLocale()=='km' ? 'មិនទាន់មានផលិតផលចូលចិត្ត' : 'No favorites yet' }}
        </div>
        <div style="font-size:13px;color:#94a3b8;margin-bottom:24px;">
            {{ app()->getLocale()=='km' ? 'ចុច ❤️ លើផលិតផលដែលចូលចិត្ត!' : 'Click ❤️ on products you love!' }}
        </div>
        <a href="{{ route('products') }}" style="display:inline-flex;align-items:center;gap:8px;background:#0f172a;color:#fff;text-decoration:none;padding:12px 28px;border-radius:10px;font-size:14px;font-weight:700;transition:all .2s;" onmouseover="this.style.background='#f59e0b';this.style.color='#000'" onmouseout="this.style.background='#0f172a';this.style.color='#fff'">
            🛍️ {{ app()->getLocale()=='km' ? 'ស្វែងរកផលិតផល' : 'Browse Products' }}
        </a>
    </div>

    @else
    <div class="wl-grid">
        @foreach($wishlists as $item)
        @php $product = $item->product; @endphp
        <div class="wl-card">

            {{-- Remove button --}}
            <form action="{{ route('wishlist.remove', $item->id) }}" method="POST" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-wl-remove" title="{{ app()->getLocale()=='km' ? 'លប់ចេញ' : 'Remove' }}">✕</button>
            </form>

            {{-- Sale badge --}}
            @if($product->is_on_sale)
            <div class="badge-sale">-{{ $product->discount_percent }}%</div>
            @endif

            {{-- Image --}}
            <a href="{{ route('product.detail', $product->slug) }}">
                <div class="wl-img-wrap">
                    <img src="{{ $product->image_url ?? '' }}"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23f3f4f6%22/%3E%3C/svg%3E'"
                         alt="{{ $product->name }}"
                         loading="lazy"/>
                </div>
            </a>

            {{-- Body --}}
            <div class="wl-body">
                @if($product->brand)
                <div class="wl-brand">{{ $product->brand->name }}</div>
                @endif

                <a href="{{ route('product.detail', $product->slug) }}" style="text-decoration:none;">
                    <div class="wl-name">{{ $product->name }}</div>
                </a>

                <div class="d-flex align-items-baseline gap-1">
                    <span class="wl-price">${{ number_format($product->effective_price, 2) }}</span>
                    @if($product->is_on_sale)
                    <span class="wl-price-old">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                @if($product->stock == 0)
                <div class="wl-stock-out">❌ {{ app()->getLocale()=='km' ? 'អស់ស្តុក' : 'Out of stock' }}</div>
                @elseif($product->stock < 5)
                <div class="wl-stock-low">⚠️ {{ app()->getLocale()=='km' ? 'នៅសល់ '.$product->stock.' ប៉ុណ្ណោះ' : 'Only '.$product->stock.' left' }}</div>
                @endif

                {{-- Actions --}}
                <div class="wl-actions">
                    @if($product->stock > 0)
                    <form action="{{ route('wishlist.to-cart', $product->id) }}" method="POST" style="flex:1;">
                        @csrf
                        <button type="submit" class="btn-add-cart" style="width:100%;">
                            🛒 {{ app()->getLocale()=='km' ? 'ដាក់ទៅកន្ត្រក' : 'Add to Cart' }}
                        </button>
                    </form>
                    @else
                    <button class="btn-add-cart" disabled style="flex:1;">
                        {{ app()->getLocale()=='km' ? 'អស់ស្តុក' : 'Out of Stock' }}
                    </button>
                    @endif

                    <a href="{{ route('product.detail', $product->slug) }}" class="btn-view" title="{{ app()->getLocale()=='km' ? 'មើលលម្អិត' : 'View Details' }}">
                        👁
                    </a>
                </div>

                {{-- Added date --}}
                <div style="font-size:10px;color:#cbd5e1;margin-top:8px;text-align:right;">
                    {{ app()->getLocale()=='km' ? 'បន្ថែម' : 'Added' }} {{ $item->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Bottom actions --}}
    <div style="margin-top:24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <a href="{{ route('products') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;font-weight:600;">
            ← {{ app()->getLocale()=='km' ? 'ទៅទិញទំនិញ' : 'Continue Shopping' }}
        </a>
        <a href="{{ route('cart') }}" style="display:inline-flex;align-items:center;gap:8px;background:#0f172a;color:#fff;text-decoration:none;padding:11px 24px;border-radius:10px;font-size:13px;font-weight:700;transition:all .2s;" onmouseover="this.style.background='#f59e0b';this.style.color='#000'" onmouseout="this.style.background='#0f172a';this.style.color='#fff'">
            🛒 {{ app()->getLocale()=='km' ? 'មើលកន្ត្រក' : 'View Cart' }}
        </a>
    </div>
    @endif
</div>
@endsection