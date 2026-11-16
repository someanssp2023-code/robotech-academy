{{-- Language Switcher Dropdown --}}
<div class="lang-dropdown" style="position:relative;">
    <button class="lang-btn" onclick="toggleLang()" style="
        display:flex; align-items:center; gap:6px;
        background:#fff; border:1.5px solid #e5e7eb;
        border-radius:8px; padding:6px 12px;
        font-size:13px; font-weight:600; color:#374151;
        cursor:pointer; transition:all .15s;
        font-family:inherit;
    ">
        @if(app()->getLocale() == 'km')
            <span style="font-size:16px;">🇰🇭</span> ខ្មែរ
        @else
            <span style="font-size:16px;">🇺🇸</span> English
        @endif
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="opacity:.5;">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    <div id="lang-menu" style="
        display:none;
        position:absolute; top:calc(100% + 6px); right:0;
        background:#fff; border:1px solid #e5e7eb;
        border-radius:10px; padding:6px;
        min-width:140px;
        box-shadow:0 8px 24px rgba(0,0,0,.1);
        z-index:999;
    ">
        <a href="{{ route('language.switch', 'en') }}"
           style="
               display:flex; align-items:center; gap:8px;
               padding:8px 10px; border-radius:7px;
               text-decoration:none; font-size:13px; font-weight:600;
               color:{{ app()->getLocale() == 'en' ? '#111' : '#6b7280' }};
               background:{{ app()->getLocale() == 'en' ? '#f3f4f6' : 'transparent' }};
               transition:background .1s;
           "
           onmouseover="this.style.background='#f9fafb'"
           onmouseout="this.style.background='{{ app()->getLocale() == 'en' ? '#f3f4f6' : 'transparent' }}'">
            <span style="font-size:16px;">🇺🇸</span> English
            @if(app()->getLocale() == 'en')
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" style="margin-left:auto;"><path d="M20 6L9 17l-5-5"/></svg>
            @endif
        </a>
        <a href="{{ route('language.switch', 'km') }}"
           style="
               display:flex; align-items:center; gap:8px;
               padding:8px 10px; border-radius:7px;
               text-decoration:none; font-size:13px; font-weight:600;
               color:{{ app()->getLocale() == 'km' ? '#111' : '#6b7280' }};
               background:{{ app()->getLocale() == 'km' ? '#f3f4f6' : 'transparent' }};
               transition:background .1s;
           "
           onmouseover="this.style.background='#f9fafb'"
           onmouseout="this.style.background='{{ app()->getLocale() == 'km' ? '#f3f4f6' : 'transparent' }}'">
            <span style="font-size:16px;">🇰🇭</span> ខ្មែរ
            @if(app()->getLocale() == 'km')
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" style="margin-left:auto;"><path d="M20 6L9 17l-5-5"/></svg>
            @endif
        </a>
    </div>
</div>

<script>
function toggleLang() {
    const menu = document.getElementById('lang-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.lang-dropdown')) {
        const menu = document.getElementById('lang-menu');
        if (menu) menu.style.display = 'none';
    }
});
</script>