@extends('layouts.app')
@section('title', app()->getLocale()=='km' ? 'ទំព័រដើម' : 'Home')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   HERO — Split Style (Apple-like)
   LEFT: Dark bg + Text
   RIGHT: Product Image
══════════════════════════════════════════ */
.hero-wrap {
    width: 100vw;
    position: relative;
    left: 50%;
    margin-left: -50vw;
    background: #0a0a0a;
    overflow: hidden;
}

.hero-track {
    display: flex;
    transition: transform .7s cubic-bezier(.77,0,.18,1);
    will-change: transform;
}

.hero-slide {
    min-width: 100%;
    display: flex;
    align-items: stretch;
    min-height: 520px;
    position: relative;
}
@media(max-width:768px) {
    .hero-slide {
        flex-direction: column;
        min-height: auto;
    }
}

/* ── LEFT: Text panel ── */
.hero-left {
    width: 45%;
    display: flex;
    align-items: center;
    padding: 60px 5% 60px 7%;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}
@media(max-width:768px) {
    .hero-left {
        width: 100%;
        padding: 36px 6% 24px;
        order: 2;
    }
}

.hero-badge {
    display: inline-block;
    background: #f59e0b;
    color: #000;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    padding: 4px 12px;
    border-radius: 20px;
    margin-bottom: 18px;
}

.hero-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(28px, 3.8vw, 56px);
    font-weight: 800;
    color: #fff;
    line-height: 1.1;
    letter-spacing: -.03em;
    margin-bottom: 16px;
}

.hero-sub {
    font-size: clamp(14px, 1.3vw, 17px);
    color: rgba(255,255,255,.65);
    line-height: 1.75;
    margin-bottom: 28px;
    max-width: 380px;
}

.hero-divider {
    width: 48px; height: 3px;
    background: #f59e0b;
    border-radius: 2px;
    margin-bottom: 20px;
}

.hero-slide-num {
    position: absolute;
    bottom: 28px; left: 7%;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 13px; font-weight: 700;
    color: rgba(255,255,255,.3);
    display: flex; align-items: center; gap: 8px;
}
.hero-slide-num span { color: #f59e0b; }

/* ── RIGHT: Image panel ── */
.hero-right {
    flex: 1;
    position: relative;
    overflow: hidden;
    min-height: 480px;
}
@media(max-width:768px) {
    .hero-right {
        width: 100%;
        min-height: 260px;
        order: 1;
    }
}

.hero-right img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 8s ease;
    display: block !important;
}
.hero-slide.active .hero-right img {
    transform: scale(1.04);
}
.hero-right::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 120px;
    background: linear-gradient(to right, #0a0a0a, transparent);
    z-index: 2;
    pointer-events: none;
}

/* No image fallback */
.hero-no-img {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 64px; color: rgba(255,255,255,.08);
}

/* ── DOTS ── */
.carousel-dots {
    position: absolute;
    bottom: 28px; right: 6%;
    display: flex; gap: 7px;
    z-index: 30;
}
@media(max-width:768px) {
    .carousel-dots { bottom: 16px; right: 50%; transform: translateX(50%); }
}
.carousel-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: rgba(255,255,255,.25);
    cursor: pointer; border: none; padding: 0;
    transition: all .3s;
}
.carousel-dot.active {
    background: #f59e0b;
    width: 24px; border-radius: 4px;
}

/* ── ARROWS ── */
.carousel-arrow {
    position: absolute;
    top: 50%; transform: translateY(-50%);
    z-index: 30;
    background: rgba(255,255,255,.10);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 50%;
    width: 44px; height: 44px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: #fff;
    font-size: 22px; line-height: 1;
    transition: background .2s;
    backdrop-filter: blur(6px);
}
.carousel-arrow:hover { background: rgba(255,255,255,.22); }
.carousel-arrow.prev-arr { left: 16px; }
.carousel-arrow.next-arr { right: 16px; }
@media(max-width:480px) { .carousel-arrow { display: none; } }

/* ── PROGRESS ── */
.carousel-progress {
    position: absolute; bottom: 0; left: 0;
    height: 3px; background: #f59e0b; z-index: 30;
    transition: width linear;
}

/* ══ SECTIONS ══ */
.section-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 22px; font-weight: 800;
    color: #111; letter-spacing: -.02em;
}
.sec-header {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 20px;
}
.btn-view-all {
    font-size: 13px; font-weight: 700;
    color: #555; text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
    transition: color .15s;
}
.btn-view-all:hover { color: #111; }

.cat-chip {
    display: flex; align-items: center; gap: 10px;
    background: #fff; border: 1px solid #ebebeb;
    border-radius: 10px; padding: 12px 14px;
    text-decoration: none; transition: all .18s;
}
.cat-chip:hover {
    border-color: #f59e0b;
    box-shadow: 0 4px 16px rgba(0,0,0,.07);
    transform: translateY(-2px);
}
.cat-chip-initial {
    width: 36px; height: 36px;
    background: #fff8e1; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 15px; font-weight: 800; color: #f59e0b; flex-shrink: 0;
}
.cat-chip-name { font-size: 13px; font-weight: 700; color: #111; }
.cat-chip-count { font-size: 11px; color: #9ca3af; margin-top: 1px; }
</style>
@endpush

@section('content')

{{-- ══ INFO BANNER ══ --}}
<section style="padding:0;margin:0;">
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0f172a 100%);padding:64px 0;position:relative;overflow:hidden;">

        {{-- Background decoration --}}
        <div style="position:absolute;top:-80px;right:-80px;width:400px;height:400px;border-radius:50%;background:rgba(245,158,11,.06);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-60px;left:-60px;width:300px;height:300px;border-radius:50%;background:rgba(245,158,11,.04);pointer-events:none;"></div>
        <div style="position:absolute;top:0;left:0;right:0;bottom:0;background-image:radial-gradient(rgba(255,255,255,.03) 1px,transparent 1px);background-size:32px 32px;pointer-events:none;"></div>

        <div class="container" style="position:relative;z-index:1;">
            <div class="row align-items-center g-5">

                {{-- Left: Big text --}}
                <div class="col-lg-6">
                    
                    <h2 style="font-family:'Space Grotesk',sans-serif;font-size:clamp(28px,4vw,46px);font-weight:800;color:#fff;line-height:1.1;letter-spacing:-.02em;margin-bottom:16px;">
                        {{ app()->getLocale()=='km'
                            ? 'RoboTech Academy — មជ្ឈមណ្ឌល IoT & Robotics'
                            : 'RoboTech Academy — Your IoT & Robotics Hub' }}
                    </h2>
                    <div style="width:52px;height:3px;background:#f59e0b;border-radius:2px;margin-bottom:20px;"></div>
                    <p style="font-size:16px;color:rgba(255,255,255,.7);line-height:1.75;margin-bottom:28px;">
                        {{ app()->getLocale()=='km'
                            ? 'យើងផ្តល់ឧបករណ៍ IoT, Arduino, Raspberry Pi និង Robotics គ្រប់ប្រភេទ សម្រាប់ការអប់រំ និងការស្រាវជ្រាវ នៅក្នុងប្រទេសកម្ពុជា។'
                            : 'We provide IoT, Arduino, Raspberry Pi and Robotics components for education and research across Cambodia — quality products at the best price.' }}
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('products') }}" style="display:inline-flex;align-items:center;gap:8px;background:#f59e0b;color:#000;font-weight:700;font-size:14px;padding:12px 24px;border-radius:9px;text-decoration:none;transition:all .2s;">
                            {{ app()->getLocale()=='km' ? 'មើលផលិតផល' : 'Browse Products' }}
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:8px;background:transparent;color:#fff;font-weight:600;font-size:14px;padding:12px 24px;border-radius:9px;text-decoration:none;border:1.5px solid rgba(255,255,255,.25);transition:all .2s;">
                            {{ app()->getLocale()=='km' ? 'ចូលប្រព័ន្ធ' : 'Get Started' }}
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<br>

{{-- ══ HERO SPLIT CAROUSEL ══ --}}
@if($banners->isNotEmpty())
<div class="hero-wrap" id="heroWrap">
    <div class="hero-track" id="heroTrack">

        @foreach($banners as $i => $banner)
        <div class="hero-slide {{ $i===0 ? 'active' : '' }}" data-index="{{ $i }}">

            {{-- LEFT: Text --}}
            <div class="hero-left">
                <div>
                    @if($banner->subtitle)
                        <div class="hero-badge">{{ app()->getLocale()=='km' ? 'ថ្មី' : 'New' }}</div>
                    @endif
                    <div class="hero-divider"></div>
                    <h1 class="hero-title">{{ $banner->title }}</h1>
                    @if($banner->subtitle)
                        <p class="hero-sub">{{ Str::limit($banner->subtitle, 120) }}</p>
                    @endif
                    <a href="{{ route('products') }}" style="display:inline-flex;align-items:center;gap:8px;background:#f59e0b;color:#000;font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:14px;padding:11px 24px;border-radius:8px;text-decoration:none;transition:all .2s;">
                        {{ app()->getLocale()=='km' ? 'មើលផលិតផល' : 'Shop Now' }}
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                    <div class="hero-slide-num">
                        <span>{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span> / {{ str_pad($banners->count(), 2, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            </div>

            {{-- RIGHT: Image (click to fullscreen) --}}
            <div class="hero-right" onclick="openImg('{{ $banner->image_url }}','{{ $banner->title }}')" style="cursor:zoom-in;" title="{{ app()->getLocale()=='km' ? 'ចុចដើម្បីមើលរូបភាពពេញ' : 'Click to view full image' }}">
                @if($banner->image_url)
                    <img src="{{ $banner->image_url }}"
                         alt="{{ $banner->title }}"
                         onerror="this.style.display='none'"/>
                    <div style="position:absolute;top:12px;right:12px;z-index:10;background:rgba(0,0,0,.5);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:6px 10px;display:flex;align-items:center;gap:5px;color:#fff;font-size:12px;font-weight:600;pointer-events:none;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                        {{ app()->getLocale()=='km' ? 'ពេញ' : 'Fullscreen' }}
                    </div>
                @else
                    <div class="hero-no-img">🤖</div>
                @endif
            </div>

        </div>
        @endforeach

    </div>

    {{-- Arrows --}}
    @if($banners->count() > 1)
    <button class="carousel-arrow prev-arr" onclick="changeSlide(-1)" aria-label="Prev">&#8249;</button>
    <button class="carousel-arrow next-arr" onclick="changeSlide(1)"  aria-label="Next">&#8250;</button>

    {{-- Dots --}}
    <div class="carousel-dots">
        @foreach($banners as $i => $b)
        <button class="carousel-dot {{ $i===0 ? 'active' : '' }}"
                onclick="goToSlide({{ $i }})"
                aria-label="Slide {{ $i+1 }}"></button>
        @endforeach
    </div>
    @endif

    {{-- Progress bar --}}
    <div class="carousel-progress" id="carouselProgress" style="width:0%"></div>
</div>
@endif
<br>
{{-- ══ CATEGORIES ══ --}}
<section style="padding:56px 0 0;">
    <div class="container">
        <div class="sec-header">
            <h2 class="section-title mb-0">
                {{ app()->getLocale()=='km' ? 'ប្រភេទទំនិញ' : 'Shop by Category' }}
            </h2>
        </div>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            @foreach($categories as $cat)
            <div class="col">
                <a href="{{ route('products') }}?category_id={{ $cat->id }}" class="cat-chip">
                    <div class="cat-chip-initial">{{ strtoupper(substr($cat->name,0,1)) }}</div>
                    <div>
                        <div class="cat-chip-name">{{ $cat->name }}</div>
                        <div class="cat-chip-count">{{ $cat->products_count ?? 0 }} {{ app()->getLocale()=='km' ? 'មុខ' : 'items' }}</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══ FEATURED PRODUCTS ══ --}}
@if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
<section style="padding:48px 0 72px;">
    <div class="container">
        <div class="sec-header">
            <h2 class="section-title mb-0">
                {{ app()->getLocale()=='km' ? 'ផលិតផលពិសេស' : 'Featured Products' }}
            </h2>
            <a href="{{ route('products') }}?featured=1" class="btn-view-all">
                {{ app()->getLocale()=='km' ? 'មើលទាំងអស់' : 'View All' }}
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            @foreach($featuredProducts as $product)
            <div class="col">@include('shop._product-card', ['product' => $product])</div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Fullscreen Image Modal --}}
<div id="imgModal" onclick="closeImg()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.95);z-index:99999;align-items:center;justify-content:center;flex-direction:column;gap:12px;cursor:zoom-out;">
    <div style="position:relative;max-width:96vw;max-height:90vh;">
        <img id="imgModalSrc" src="" style="max-width:96vw;max-height:86vh;border-radius:8px;object-fit:contain;display:block;box-shadow:0 24px 80px rgba(0,0,0,.6);"/>
        <button onclick="closeImg()" style="position:fixed;top:20px;right:20px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;font-size:22px;width:44px;height:44px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(8px);">✕</button>
        <div style="position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.6);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.15);border-radius:20px;padding:6px 16px;">
            <span id="imgModalTitle" style="font-size:13px;font-weight:600;color:rgba(255,255,255,.8);"></span>
        </div>
    </div>
</div>

@push('scripts')
<script>
const track    = document.getElementById('heroTrack');
const slides   = document.querySelectorAll('.hero-slide');
const dots     = document.querySelectorAll('.carousel-dot');
const progress = document.getElementById('carouselProgress');
const total    = slides.length;
let current    = 0;
let timer      = null;
const DELAY    = 6000;

function goToSlide(n) {
    slides[current].classList.remove('active');
    if (dots[current]) dots[current].classList.remove('active');
    current = (n + total) % total;
    // Move track
    if (track) track.style.transform = `translateX(-${current * 100}%)`;
    slides[current].classList.add('active');
    if (dots[current]) dots[current].classList.add('active');
    resetProgress();
}

function changeSlide(dir) {
    goToSlide(current + dir);
    resetAutoplay();
}

function resetProgress() {
    if (!progress) return;
    progress.style.transition = 'none';
    progress.style.width = '0%';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        progress.style.transition = `width ${DELAY}ms linear`;
        progress.style.width = '100%';
    }));
}

function resetAutoplay() {
    clearInterval(timer);
    if (total > 1) timer = setInterval(() => goToSlide(current + 1), DELAY);
}

if (total > 1) { resetProgress(); resetAutoplay(); }

// Pause on hover
const wrap = document.getElementById('heroWrap');
if (wrap) {
    wrap.addEventListener('mouseenter', () => clearInterval(timer));
    wrap.addEventListener('mouseleave', () => resetAutoplay());
}

// Touch swipe
let tx = 0;
if (wrap) {
    wrap.addEventListener('touchstart', e => tx = e.changedTouches[0].screenX, {passive:true});
    wrap.addEventListener('touchend', e => {
        const d = tx - e.changedTouches[0].screenX;
        if (Math.abs(d) > 50) changeSlide(d > 0 ? 1 : -1);
    });
}

// Keyboard
document.addEventListener('keydown', e => {
    if (e.key==='ArrowLeft')  changeSlide(-1);
    if (e.key==='ArrowRight') changeSlide(1);
    if (e.key==='Escape')     closeImg();
});

// Fullscreen image modal
function openImg(src, title) {
    if (!src) return;
    document.getElementById('imgModalSrc').src   = src;
    document.getElementById('imgModalTitle').textContent = title || '';
    const m = document.getElementById('imgModal');
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeImg() {
    document.getElementById('imgModal').style.display = 'none';
    document.body.style.overflow = '';
}
</script>
@endpush
@endsection