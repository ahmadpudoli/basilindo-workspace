const defaultNotificationUrl = '/dashboard';

self.addEventListener('install', (event) => {
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Network-only: authenticated pages and business data must always pass through Laravel authorization.
self.addEventListener('fetch', (event) => {
    const requestUrl = new URL(event.request.url);
    if (event.request.method !== 'GET' || requestUrl.origin !== self.location.origin) return;
    event.respondWith(fetch(event.request));
});

self.addEventListener('push', (event) => {
    let payload = {};
    try {
        payload = event.data ? event.data.json() : {};
    } catch (_) {
        payload = { body: event.data?.text() || '' };
    }

    const options = {
        body: payload.body || '',
        icon: '/images/pwa-icon-192.png',
        badge: '/images/pwa-icon-192.png',
        data: { url: payload.url || payload.data?.url || defaultNotificationUrl },
    };
    if (payload.tag) options.tag = payload.tag;
    event.waitUntil(self.registration.showNotification(payload.title || 'Basilindo Workspace', options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const target = new URL(event.notification.data?.url || defaultNotificationUrl, self.location.origin);
    if (target.origin !== self.location.origin) return;

    event.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windows) => {
        const existing = windows.find((client) => new URL(client.url).origin === self.location.origin);
        if (existing) {
            return existing.navigate(target.href).then((client) => client?.focus());
        }
        return self.clients.openWindow(target.href);
    }));
});
