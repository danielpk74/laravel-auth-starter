import './bootstrap';

import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';

import { createApp } from 'vue/dist/vue.esm-bundler.js';
import { createI18n } from 'vue-i18n';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia } from 'pinia';

import Routes from './routes';
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

// Import components
import UserNav from '../components/admin/UserNav.vue';

// Import stores
import { useAuthStore } from './stores/auth';

import en from '../locales/en.json';
import es from '../locales/es.json';

const i18n = createI18n({
    locale: 'en',
    fallbackLocale: 'en',
    legacy: false, // Use Composition API mode
    messages: {
        en,
        es
    },
});

const pinia = createPinia();

const app = createApp({
    async created() {
        // Initialize auth state
        const authStore = useAuthStore();
        if (authStore.token) {
            await authStore.fetchUser();
        }
    }
});

const router = createRouter({
    routes: Routes,
    history: createWebHistory(),
});

// Global navigation guard to check authentication
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    // If user has token but no user data, try to fetch it
    if (authStore.token && !authStore.user) {
        await authStore.fetchUser();
    }
    
    next();
});

app.use(pinia);
app.use(router);
app.use(i18n);

// Register global components
app.component('UserNav', UserNav);

// Make toastr globally available
app.config.globalProperties.$toastr = toastr;

app.mount('#app');

// Listen for changes to the locale (e.g., from a language switcher component)
app.config.globalProperties.$onLocaleChange = (newLocale) => {
    i18n.global.locale.value = newLocale;
};

