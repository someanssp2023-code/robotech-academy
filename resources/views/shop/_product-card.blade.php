<div style="background:#fff;border-radius:12px;border:1px solid #ebebeb;overflow:hidden;transition:box-shadow .2s,transform .2s;height:100%;"
     onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,.08)';this.style.transform='translateY(-2px)'"
     onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)'">

    {{-- Image --}}
    <div style="position:relative;background:#f9fafb;aspect-ratio:1;overflow:hidden;">
        <a href="{{ route('product.detail', $product->slug) }}">
            <img src="{{ $product->image_url ?? '' }}"
                 onerror="this.style.background='#f3f4f6';this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22%3E%3Crect width=%22200%22 height=%22200%22 fill=%22%23f3f4f6%22/%3E%3Ctext x=%22100%22 y=%22105%22 text-anchor=%22middle%22 font-size=%2224%22 fill=%22%23ccc%22%3EP%3C/text%3E%3C/svg%3E'"
                 alt="{{ $product->name }}"
                 loading="lazy"
                 style="width:100%;height:100%;object-fit:contain;padding:16px;transition:transform .3s;"
                 onmouseover="this.style.transform='scale(1.05)'"
                 onmouseout="this.style.transform='scale(1)'"/>
        </a>
        @if($product->is_on_sale)
            <span style="position:absolute;top:10px;left:10px;background:#ef4444;color:#fff;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;">
                -{{ $product->discount_percent }}%
            </span>
        @endif
        @if($product->stock == 0)
            <span style="position:absolute;top:10px;right:10px;background:#6b7280;color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;">
                {{ __('app.out_of_stock') }}
            </span>
        @endif
    </div>

    {{-- Body --}}
    <div style="padding:14px;">
        {{-- Brand --}}
        @if($product->brand)
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#f59e0b;margin-bottom:4px;">
                {{ $product->brand->name }}
            </div>
        @endif

        {{-- Name --}}
        <a href="{{ route('product.detail', $product->slug) }}" style="text-decoration:none;">
            <div style="font-size:13px;font-weight:600;color:#111;line-height:1.4;margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                {{ $product->name }}
            </div>
        </a>

        {{-- Price --}}
        <div class="d-flex align-items-baseline gap-1 mb-2">
            <span style="font-size:16px;font-weight:800;color:#f59e0b;">${{ number_format($product->effective_price, 2) }}</span>
            @if($product->is_on_sale)
                <span style="font-size:12px;color:#9ca3af;text-decoration:line-through;">${{ number_format($product->price, 2) }}</span>
            @endif
        </div>

        {{-- Stock warning --}}
        @if($product->stock > 0 && $product->stock < 5)
            <div style="font-size:11px;color:#d97706;font-weight:600;margin-bottom:8px;">
                {{ __('app.low_stock', ['count' => $product->stock]) }}
            </div>
        @endif

        {{-- Add to Cart --}}
        @auth
            @if($product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}"/>
                    <input type="hidden" name="quantity" value="1"/>
                    <button type="submit"
                            style="width:100%;background:#111;color:#fff;border:none;border-radius:8px;padding:9px;font-size:13px;font-weight:700;cursor:pointer;transition:all .15s;font-family:inherit;"
                            onmouseover="this.style.background='#f59e0b';this.style.color='#000'"
                            onmouseout="this.style.background='#111';this.style.color='#fff'">
                        {{ __('app.add_to_cart') }}
                    </button>
                </form>
            @else
                <button disabled
                        style="width:100%;background:#f3f4f6;color:#9ca3af;border:none;border-radius:8px;padding:9px;font-size:13px;font-weight:700;cursor:not-allowed;font-family:inherit;">
                    {{ __('app.out_of_stock') }}
                </button>
            @endif
        @else
            <a href="{{ route('login') }}"
               style="display:block;width:100%;background:#111;color:#fff;border-radius:8px;padding:9px;font-size:13px;font-weight:700;text-align:center;text-decoration:none;transition:all .15s;"
               onmouseover="this.style.background='#f59e0b';this.style.color='#000'"
               onmouseout="this.style.background='#111';this.style.color='#fff'">
                {{ __('app.add_to_cart') }}
            </a>
        @endauth
    </div>
</div>