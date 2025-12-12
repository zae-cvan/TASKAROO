self.addEventListener('push', function(event) {
  let data = {};
  if(event.data) data = event.data.json();
  const options = { body: data.body, icon: data.icon || '/favicon.ico', data: data.url, tag: data.tag };
  event.waitUntil(self.registration.showNotification(data.title || 'Notification', options));
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  event.waitUntil(clients.openWindow(event.notification.data || '/'));
});
