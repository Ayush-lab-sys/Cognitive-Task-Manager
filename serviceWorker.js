// serviceWorker.js
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open('task-manager-v1').then((cache) => {
      return cache.addAll([
        './',
        './index.html',
        './style.css',
        './app.js',
        './manifest.json',
        './dashboard.html',
        './img/image1.png',
        './img/image2.png',
        './img/image3.png',
        './img/logo.png'
      ]);
    })
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});

self.addEventListener('push', (event) => {
  const options = {
    body: event.data ? event.data.text() : "Task Reminder",
    icon: '/img/icon-192x192.png', // ✅ Fixed icon path
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    }
  };

  event.waitUntil(
    self.registration.showNotification('Task Reminder', options)
  );
});
