// Laravel Auth Starter - Main Entry Point
import './bootstrap';

// AdminLTE Dependencies
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';
import 'admin-lte/dist/css/adminlte.min.css';

// Vue and related libraries
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import { createI18n } from 'vue-i18n';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia } from 'pinia';

// Package imports
import routes from './routes';
import { useAuthStore } from './stores/auth';

// Toastr for notifications
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

// Import all components
import LoginForm from '../components/auth/LoginForm.vue';
import RegisterForm from '../components/auth/RegisterForm.vue';
import ForgotPasswordForm from '../components/auth/ForgotPasswordForm.vue';
import UserNav from '../components/admin/UserNav.vue';
import Dashboard from '../components/admin/Dashboard.vue';

// Import locale files
import en from '../locales/en.json';
import es from '../locales/es.json';

/**
 * Initialize Laravel Auth Starter
 * @param {Object} config - Configuration options
 * @param {string} config.locale - Default locale (default: 'en')
 * @param {Object} config.routes - Additional routes to merge
 * @param {Object} config.messages - Additional i18n messages
 * @returns {Object} Vue app instance and plugins
 */
export function createAuthStarter(config = {}) {
    const {
        locale = 'en',
        routes: additionalRoutes = [],
        messages: additionalMessages = {}
    } = config;

    // Setup i18n
    const i18n = createI18n({
        locale,
        fallbackLocale: 'en',
        legacy: false,
        messages: {
            en: { ...en, ...additionalMessages.en },
            es: { ...es, ...additionalMessages.es }
        },
    });

    // Setup Pinia
    const pinia = createPinia();

    // Setup Router
    const router = createRouter({
        routes: [...routes, ...additionalRoutes],
        history: createWebHistory(),
    });

    // Create Vue app
    const app = createApp({
        async created() {
            // Initialize auth state
            const authStore = useAuthStore();
            if (authStore.token) {
                await authStore.fetchUser();
            }
        }
    });

    // Register global components
    app.component('LoginForm', LoginForm);
    app.component('RegisterForm', RegisterForm);
    app.component('ForgotPasswordForm', ForgotPasswordForm);
    app.component('UserNav', UserNav);
    app.component('Dashboard', Dashboard);

    // Install plugins
    app.use(i18n);
    app.use(pinia);
    app.use(router);

    // Make toastr globally available
    app.config.globalProperties.$toastr = toastr;

    return {
        app,
        router,
        pinia,
        i18n,
        // Export components for individual use
        components: {
            LoginForm,
            RegisterForm,
            ForgotPasswordForm,
            UserNav,
            Dashboard
        },
        // Export stores
        stores: {
            useAuthStore
        }
    };
}

// Default export for simple usage
export default createAuthStarter;
