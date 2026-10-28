@switch($module)
    @case('company')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M2 21h20M8 7h2M8 11h2M8 15h2M13 7h2M13 11h2M13 15h2M18 21v-8h2v8"/></svg>
        @break
    @case('crm')
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="10" r="2"/><path d="M3 20a6 6 0 0 1 12 0M15 16a4 4 0 0 1 6 4"/></svg>
        @break
    @case('project')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 5h16v10H4M8 19h12M8 9h8M8 12h5"/></svg>
        @break
    @case('hris')
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2"/><path d="M3 20a6 6 0 0 1 12 0M15 17a4 4 0 0 1 6 3"/></svg>
        @break
    @case('settings')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/><path d="m4.9 4.9 2 1.4M17.1 17.7l2 1.4M4 12H2M22 12h-2M4.9 19.1l2-1.4M17.1 6.3l2-1.4M12 4V2M12 22v-2"/></svg>
        @break
    @default
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/><path d="M3 9h18"/></svg>
@endswitch
