import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const modal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');
const deleteButtons = document.querySelectorAll('.delete-btn');

deleteButtons.forEach(button => {
    button.addEventListener('click', () => {
        const taskId = button.dataset.taskId;
        deleteForm.action = `/tasks/${taskId}`;
        modal.classList.remove('hidden');
    });
});

function closeModal() {
    modal.classList.add('hidden');
}

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

let userId = document.head.querySelector('meta[name="user-id"]').content;

Echo.private(`App.Models.User.${userId}`)
    .listen('TaskDeadlineNotification', (e) => {
        console.log('Real-time task deadline:', e);
        // Example: Show popup notification
        alert(e.message);
    });

import './notifications';

