<script>
document.addEventListener('DOMContentLoaded', () => {
    let activeQueueMenu = null;

    const closeQueueMenu = (restoreFocus = false) => {
        if (!activeQueueMenu) return;
        activeQueueMenu.menu.classList.remove('show');
        activeQueueMenu.menu.style.visibility = '';
        activeQueueMenu.toggle.setAttribute('aria-expanded', 'false');
        if (restoreFocus) activeQueueMenu.toggle.focus();
        activeQueueMenu = null;
    };

    const placeQueueMenu = () => {
        if (!activeQueueMenu) return;
        const { menu, toggle } = activeQueueMenu;
        const anchor = toggle.getBoundingClientRect();
        menu.style.visibility = 'hidden';
        menu.style.left = '0px';
        menu.style.top = '0px';
        menu.style.right = 'auto';
        menu.style.bottom = 'auto';
        const box = menu.getBoundingClientRect();
        const left = Math.max(8, Math.min(window.innerWidth - box.width - 8, anchor.right - box.width));
        const below = anchor.bottom + 8;
        const top = below + box.height <= window.innerHeight - 8
            ? below
            : Math.max(8, anchor.top - box.height - 8);
        menu.style.left = `${left}px`;
        menu.style.top = `${top}px`;
        menu.style.visibility = 'visible';
    };

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-queue-action-toggle]');
        if (toggle) {
            event.preventDefault();
            if (activeQueueMenu?.toggle === toggle) {
                closeQueueMenu();
                return;
            }

            closeQueueMenu();
            const menu = document.getElementById(toggle.dataset.menuId);
            if (!menu) return;
            document.body.append(menu);
            menu.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
            activeQueueMenu = { toggle, menu };
            placeQueueMenu();
            return;
        }

        if (activeQueueMenu?.menu.contains(event.target)) {
            if (!event.target.closest('form') && event.target.closest('.dropdown-item') && !event.target.closest('.dropdown-item:disabled')) {
                closeQueueMenu();
            }
            return;
        }

        closeQueueMenu();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeQueueMenu(true);
    });
    window.addEventListener('resize', placeQueueMenu);
    window.addEventListener('scroll', placeQueueMenu, true);
});
</script>
