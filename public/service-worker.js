const CACHE_NAME = 'courier-app-cache-v2';

// Static assets to cache
const STATIC_ASSETS = [
    '/',
    '/css/app.css',           // Your compiled CSS
    '/js/app.js',             // Your compiled JS
    '/images/web-app-manifest-192x192.png',
    '/images/web-app-manifest-512x512.png',
    '/favicon.ico',
    '/site.webmanifest',
    // Add more static files if needed
];

// Install event: cache static assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// Activate event: clean old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames =>
            Promise.all(
                cacheNames.map(name => {
                    if (name !== CACHE_NAME) return caches.delete(name);
                })
            )
        )
    );
    return self.clients.claim();
});

// Fetch event: cache-first strategy for static assets, network-first for dynamic requests
self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    // Handle API or dynamic routes (like Laravel letter pages)
    if (url.origin === self.location.origin && url.pathname.startsWith('/')) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Put a copy in cache
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
                    return response;
                })
                .catch(() =>
                    caches.match(request).then(cachedResponse => cachedResponse || caches.match('/'))
                )
        );
    } else {
        // Static assets: cache-first
        event.respondWith(
            caches.match(request).then(cachedResponse => cachedResponse || fetch(request))
        );
    }
});
