import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const getMeta = (name) => document.querySelector(`meta[name="${name}"]`)?.content || '';
const userId = getMeta('pearl-user-id');
const isAdmin = getMeta('pearl-user-admin') === '1';
const csrfToken = getMeta('csrf-token');

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
if (reverbKey) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
        wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT || 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        },
    });
}

let refreshTimer = null;
const scheduleLiveRefresh = (payload = {}) => {
    if (refreshTimer) return;
    refreshTimer = window.setTimeout(() => {
        refreshTimer = null;
        if (typeof window.PearlLiveRefresh === 'function') {
            window.PearlLiveRefresh(payload);
        } else if (payload?.scope) {
            window.location.reload();
        }
    }, 800);
};

if (window.Echo) {
    if (isAdmin) {
        window.Echo.private('admin.operations')
            .listen('.booking.updated', (payload) => scheduleLiveRefresh(payload))
            .listen('.inventory.updated', (payload) => scheduleLiveRefresh(payload))
            .listen('.admin.updated', (payload) => scheduleLiveRefresh(payload));
    }

    if (userId) {
        window.Echo.private(`user.${userId}`)
            .listen('.booking.updated', (payload) => scheduleLiveRefresh(payload));
    }

    window.Echo.channel('inventory')
        .listen('.inventory.updated', (payload) => scheduleLiveRefresh(payload));
}

const initializeLivePolling = () => {
    if (window.__pearlLivePollingStarted || typeof window.PearlLiveRefresh !== 'function') {
        return;
    }

    window.__pearlLivePollingStarted = true;
    const POLL_INTERVAL = 15000;
    window.setInterval(() => {
        if (document.visibilityState === 'visible') {
            window.PearlLiveRefresh({ scope: 'poll' });
        }
    }, POLL_INTERVAL);
};

document.addEventListener('DOMContentLoaded', initializeLivePolling);
window.addEventListener('pearl:live-ready', initializeLivePolling);
