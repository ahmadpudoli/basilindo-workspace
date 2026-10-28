@foreach($items as $item)
    @php
        $hasChildren = isset($item['items']) && $item['items'] !== [];
        $itemUrl = isset($item['url']) ? parse_url($item['url'], PHP_URL_PATH) : null;
        $itemActive = $itemUrl && $activeNavPath === $itemUrl;
        $hasActiveDescendant = function (array $children) use (&$hasActiveDescendant, $activeNavPath): bool {
            foreach ($children as $child) {
                if (isset($child['url']) && parse_url($child['url'], PHP_URL_PATH) === $activeNavPath) return true;
                if (isset($child['items']) && $hasActiveDescendant($child['items'])) return true;
            }
            return false;
        };
        $childActive = $hasChildren && $hasActiveDescendant($item['items']);
        $collapseId = 'workspace-nav-'.md5(($item['label'] ?? 'item').'-'.$level);
    @endphp
    @if($hasChildren)
        <div class="workspace-nav-group workspace-nav-level-{{ $level }} {{ $childActive ? 'is-active' : '' }}">
            <button class="workspace-nav-link workspace-nav-group-toggle {{ $childActive ? 'active' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $childActive ? 'true' : 'false' }}">
                <span class="workspace-nav-icon">@include('layouts.partials.nav-icon', ['icon' => $item['icon'] ?? ($navigationIcons[$item['label']] ?? 'dashboard')])</span><span>{{ $item['label'] }}</span><span class="workspace-nav-chevron">⌄</span>
            </button>
            <div class="collapse {{ $childActive ? 'show' : '' }}" id="{{ $collapseId }}"><div class="workspace-nav-children">@include('layouts.partials.sidebar-nav-items', ['items' => $item['items'], 'navigationIcons' => $navigationIcons, 'activeNavPath' => $activeNavPath, 'level' => $level + 1])</div></div>
        </div>
    @elseif($itemUrl)
        <a class="workspace-nav-link workspace-nav-level-{{ $level }} {{ $itemActive ? 'active' : '' }}" href="{{ $item['url'] }}"><span class="workspace-nav-icon">@include('layouts.partials.nav-icon', ['icon' => $navigationIcons[$item['label']] ?? 'dashboard'])</span><span>{{ $item['label'] }}</span></a>
    @endif
@endforeach
