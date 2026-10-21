@switch($icon)
    @case('company') @case('vendor')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M2 21h20M8 7h2M8 11h2M13 7h2M13 11h2M18 21v-8h2v8"/></svg>
        @break
    @case('users') @case('account') @case('contact')
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="10" r="2"/><path d="M3 20a6 6 0 0 1 12 0M15 16a4 4 0 0 1 6 4"/></svg>
        @break
    @case('lead') @case('opportunity') @case('activity') @case('leaderboard')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16M8 15l3-4 3 2 5-7"/><path d="M16 6h3v3"/></svg>
        @break
    @case('project') @case('board') @case('epic')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5h16v10H4M8 19h12M8 9h8M8 12h5"/></svg>
        @break
    @case('ticket') @case('note') @case('comment')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-5 4V6a2 2 0 0 1 1-1.7"/><path d="M8 9h8M8 13h5"/></svg>
        @break
    @case('timeline')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/><circle cx="8" cy="6" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="10" cy="18" r="2"/></svg>
        @break
    @case('notification')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
        @break
    @case('document') @case('document-type') @case('bundle')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h8l4 4v14H6zM14 3v5h5M9 13h6M9 17h6"/></svg>
        @break
    @case('upload')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4M8 8l4-4 4 4M5 14v5h14v-5"/></svg>
        @break
    @case('verification')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="m8 12 2.5 2.5L16 9"/></svg>
        @break
    @case('settings')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/><path d="m4.9 4.9 2 1.4M17.1 17.7l2 1.4M4 12H2M22 12h-2M4.9 19.1l2-1.4M17.1 6.3l2-1.4M12 4V2M12 22v-2"/></svg>
        @break
    @case('toggle')
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="18" height="10" rx="5"/><circle cx="16" cy="12" r="3"/></svg>
        @break
    @case('grid')
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg>
        @break
    @default
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/></svg>
@endswitch
