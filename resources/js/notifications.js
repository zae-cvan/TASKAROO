function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
  const rawData = atob(base64);
  return Uint8Array.from([...rawData].map(c => c.charCodeAt(0)));
}

async function subscribeForPush() {
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
  const registration = await navigator.serviceWorker.register('/service-worker.js');
  const res = await fetch('/api/push/vapid-public');
  const { publicKey } = await res.json();

  const subscription = await registration.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey: urlBase64ToUint8Array(publicKey)
  });

  await fetch('/api/push/subscribe', {
    method:'POST',
    headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    body: JSON.stringify({ subscription })
  });
}

async function fetchNotifications() {
  const res = await fetch('/api/notifications', { headers:{'Accept':'application/json'}});
  const list = await res.json();
  const ul = document.getElementById('notifList');
  const unreadCount = list.filter(n=>!n.read_at).length;
  document.getElementById('unreadCount').innerText = unreadCount;
  ul.innerHTML = '';
  list.forEach(n=>{
    const li = document.createElement('li');
    li.className = 'p-2 border-b cursor-pointer';
    li.innerText = `${n.data.title} - due ${n.data.when}`;
    li.onclick = ()=> window.location.href = `/tasks/${n.data.task_id}`;
    ul.appendChild(li);
  });
}

document.getElementById('notifBtn').addEventListener('click', ()=>{
  document.getElementById('notifDropdown').classList.toggle('hidden');
});

subscribeForPush();
fetchNotifications();
setInterval(fetchNotifications, 60000); // refresh every 1 min

// resources/js/notifications.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Notifications.js loaded");

    if (window.Echo) {
        const userMeta = document.querySelector('meta[name="user-id"]');
        if (!userMeta) return console.error("No user ID meta tag found.");

        const userId = userMeta.content;

        const unreadCount = document.getElementById('notificationCount');
        const sidebarUnreadCount = document.getElementById('sidebarUnreadCount');
        const notifList = document.getElementById('notificationList');

        Echo.private(`user.${userId}`)
            .notification((notification) => {

                // Increase both badges
                const newCount = Number(unreadCount.textContent) + 1;

                unreadCount.textContent = newCount;
                sidebarUnreadCount.textContent = newCount;

                // Add new notification to the top of dropdown list
                notifList.innerHTML = `
                    <li class="px-4 py-2 hover:bg-orange-50 cursor-pointer">
                        <a href="${notification.url}" class="block text-gray-700">
                            <strong>${notification.title}</strong><br>
                            <span class="text-xs text-gray-500">${notification.when}</span>
                        </a>
                    </li>
                ` + notifList.innerHTML;
            });
    }
});
