@if (!empty($breadcrumbs))
    <nav class="dashboard-breadcrumbs" aria-label="Breadcrumb">
        @foreach ($breadcrumbs as $crumb)
            @if (!empty($crumb['url']))
                <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
            @else
                <span>{{ $crumb['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endif
