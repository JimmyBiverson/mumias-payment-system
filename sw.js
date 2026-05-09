const CACHE_NAME = 'oph-pwa-v1';
const FILES_TO_CACHE = [
  '/',
  '/index.php',
  '/login.php',
  '/manifest.php',
  '/offline.html',
  '/assets/img/app-icon-192.png',
  '/assets/img/app-icon-512.png',
  '/assets/img/app-icon-192.svg',
  '/assets/img/app-icon-512.svg'
];

self.addEventListener('install', (evt) => {
  evt.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(FILES_TO_CACHE))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (evt) => {
  evt.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.map((k)=> {
      if (k !== CACHE_NAME) return caches.delete(k);
    })))
  );
  self.clients.claim();
});

self.addEventListener('fetch', (evt) => {
  // Navigation requests -> try network, fallback to offline page
  if (evt.request.mode === 'navigate' || (evt.request.method === 'GET' && evt.request.headers.get('accept') && evt.request.headers.get('accept').includes('text/html'))){
    evt.respondWith(
      fetch(evt.request).then((res)=>{
        // Put a copy in cache
        const copy = res.clone();
        caches.open(CACHE_NAME).then(cache=> cache.put(evt.request, copy));
        return res;
      }).catch(()=> caches.match('/offline.html'))
    );
    return;
  }
  // For other requests, try network else cache
  evt.respondWith(
    fetch(evt.request).then((res)=>{
      return res;
    }).catch(()=> caches.match(evt.request))
  );
});