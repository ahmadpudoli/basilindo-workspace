@php($hasChildren = $node['children'] !== [])
@php($nameParts = collect(preg_split('/\s+/', trim($node['employee']->full_name)))->filter())
@php($initials = $nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode(''))
@php($jobTitle = filled($node['employee']->job_title) ? $node['employee']->job_title : 'Jabatan belum diatur')

@if($hasChildren)
    <details class="workspace-org-branch" @if($node['level'] === 0 || $node['contains_current']) open @endif>
        <summary class="workspace-org-node {{ $node['is_current'] ? 'is-current' : '' }}">
            <span class="workspace-org-avatar" aria-hidden="true">{{ $initials }}</span>
            <span class="workspace-org-node-main">
                <strong>{{ $node['employee']->full_name }}</strong>
                <small><span>{{ $jobTitle }}</span><span class="workspace-org-node-number">NIP {{ $node['employee']->employee_number }}</span></small>
            </span>
            <span class="workspace-org-expand-label">Buka</span>
            @if($node['is_current'])
                <span class="workspace-org-current-badge">Anda</span>
            @endif
        </summary>
        <div class="workspace-org-children">
            @foreach($node['children'] as $child)
                @include('modules.partials.hris-employee-org-tree', ['node' => $child])
            @endforeach
        </div>
    </details>
@else
    <div class="workspace-org-node {{ $node['is_current'] ? 'is-current' : '' }}">
        <span class="workspace-org-avatar" aria-hidden="true">{{ $initials }}</span>
        <span class="workspace-org-node-main">
            <strong>{{ $node['employee']->full_name }}</strong>
            <small><span>{{ $jobTitle }}</span><span class="workspace-org-node-number">NIP {{ $node['employee']->employee_number }}</span></small>
        </span>
        @if($node['is_current'])
            <span class="workspace-org-current-badge">Anda</span>
        @endif
    </div>
@endif
