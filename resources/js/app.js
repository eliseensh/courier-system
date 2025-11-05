import './bootstrap';
import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Alpine = Alpine;
Alpine.start();

// ---------------------------
// Pusher & Echo Setup
// ---------------------------
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// ---------------------------
// Notifications DOM Logic
// ---------------------------
document.addEventListener('DOMContentLoaded', () => {
    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    const countEl = document.getElementById('notificationCount');
    const unreadDiv = document.getElementById('unreadNotifications');
    const readDiv = document.getElementById('readNotifications');

    if (!bell || !dropdown || !countEl) return;

    const updateCount = () => {
        const unreadCount = unreadDiv.querySelectorAll('.notification-item[data-read="0"]').length;
        countEl.textContent = unreadCount;
        countEl.style.display = unreadCount > 0 ? 'inline-block' : 'none';
    };

    const flashBell = () => {
        bell.classList.add('flash');
        setTimeout(() => bell.classList.remove('flash'), 600);
    };

    const renderNotification = (message, isUnread = true, id = null, createdAt = null) => {
        const div = document.createElement('div');
        div.className = 'notification-item';
        div.dataset.read = isUnread ? "0" : "1";
        if (id) div.dataset.id = id;
        div.style.opacity = isUnread ? '1' : '0.7';
        div.innerHTML = `<strong>${message}</strong><small>${createdAt ? new Date(createdAt).toLocaleString() : new Date().toLocaleString()}</small>`;
        if (isUnread) unreadDiv.prepend(div);
        else readDiv.prepend(div);
        updateCount();
    };

    // Toggle dropdown and mark as read
    bell.addEventListener('click', async () => {
        dropdown.classList.toggle('active');
        if (dropdown.classList.contains('active')) {
            const unreadItems = unreadDiv.querySelectorAll('.notification-item[data-read="0"]');
            if (unreadItems.length > 0) {
                try {
                    await axios.post('/notifications/read');
                    unreadItems.forEach(item => {
                        item.dataset.read = "1";
                        item.style.opacity = 0.7;
                        readDiv.prepend(item);
                    });
                    updateCount();
                } catch (err) {
                    console.error('Failed to mark notifications as read:', err);
                }
            }
        }
    });

    // ---------------------------
    // Real-time Notifications
    // ---------------------------
    const userId = document.head.querySelector('meta[name="user-id"]').content;
    if (userId) {
        window.Echo.private(`notifications.${userId}`)
            .listen('.LetterActivityEvent', (event) => {
                renderNotification(event.message, true, event.id, event.created_at);
                if (!dropdown.classList.contains('active')) flashBell();
            });
    }

    updateCount();

    // ---------------------------
    // Sidebar toggle
    // ---------------------------
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('closed');
        mainContent.classList.toggle('expanded');
    });
});
