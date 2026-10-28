<div class="workspace-org-tree workspace-org-level-{{ $level }}">
    <div class="workspace-org-node">
        <strong>{{ $node['employee']->full_name }}</strong>
        <small>{{ $node['employee']->job_title ?? '-' }} <span class="workspace-org-node-number">{{ $node['employee']->employee_number }}</span></small>
    </div>

    @if($node['children'] !== [])
        <div class="workspace-org-children">
            @foreach($node['children'] as $child)
                @include('modules.partials.hris-employee-tree-node', ['node' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @elseif($level === 0)
        <div class="text-secondary small mt-2">Belum ada bawahan langsung.</div>
    @endif
</div>
