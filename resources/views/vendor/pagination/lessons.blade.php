@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" style="display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; font-family:'Tajawal',sans-serif;">
        <p style="margin:0; font-size:12.5px; color:rgba(1,60,88,0.5); font-weight:600;">
            عرض {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} من {{ $paginator->total() }}
        </p>

        <div style="display:flex; align-items:center; gap:5px;">
            @if ($paginator->onFirstPage())
                <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:9px; color:rgba(1,60,88,0.25); cursor:not-allowed;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:9px; color:#00537A; background:rgba(0,83,122,0.06); text-decoration:none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; color:rgba(1,60,88,0.35); font-size:13px; font-weight:600;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" style="display:flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 6px; border-radius:9px; background:#013C58; color:#fff; font-family:'Poppins',sans-serif; font-size:12.5px; font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="display:flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 6px; border-radius:9px; color:#00537A; background:rgba(0,83,122,0.06); text-decoration:none; font-family:'Poppins',sans-serif; font-size:12.5px; font-weight:700; transition:background 0.15s ease;" onmouseover="this.style.background='rgba(168,232,249,0.35)';" onmouseout="this.style.background='rgba(0,83,122,0.06)';">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:9px; color:#00537A; background:rgba(0,83,122,0.06); text-decoration:none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
                </a>
            @else
                <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:9px; color:rgba(1,60,88,0.25); cursor:not-allowed;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
