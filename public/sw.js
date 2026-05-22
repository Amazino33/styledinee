const CACHE_NAME = 'styledinee-v1';

const PRECACHE = [
    '/offline.html',
    '/icon-192.png',
    '/icon-512.png',
];

// Install: pre-cache the offline page and icons
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting())
    );
});

// Activate: remove old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// Fetch strategy:
// - Static assets (JS/CSS/fonts/images): cache-first
// - Livewire / POST requests: network-only
// - Navigation (HTML pages): network-first, fall back to offline page
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Only handle same-origin requests
    if (url.origin !== location.origin) return;

    // Never intercept POST / Livewire / CSRF-sensitive requests
    if (request.method !== 'GET') return;
    if (url.pathname.includes('/livewire/')) return;
    if (url.pathname.includes('/sanctum/')) return;

    // Static assets — cache-first
    const isStatic = /\.(js|css|woff2?|ttf|otf|png|jpg|jpeg|webp|svg|ico|gif)(\?.*)?$/.test(url.pathname);
    if (isStatic) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // HTML navigation — network-first, offline fallback
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then(response => response)
                .catch(() => caches.match('/offline.html'))
        );
        return;
    }
});
