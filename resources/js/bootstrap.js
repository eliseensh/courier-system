// Lodash (optional)
import _ from 'lodash';
window._ = _;

// Bootstrap JS
import 'bootstrap';

// Axios setup
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token
const tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
if (tokenMeta) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.content;
}

// Pusher + Echo
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});
