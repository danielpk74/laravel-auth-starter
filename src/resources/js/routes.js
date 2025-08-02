import Dashboard from '../components/admin/Dashboard.vue';
import ListAppointments from '../components/pages/appointments/ListAppointments.vue';
import UserList from '../components/pages/users/UserList.vue';
import UpdateSettings from '../components/pages/settings/UpdateSettings.vue';
import UpdateProfile from '../components/pages/profile/UpdateProfile.vue';

// Auth components
import LoginForm from '../components/auth/LoginForm.vue';
import RegisterForm from '../components/auth/RegisterForm.vue';
import ForgotPasswordForm from '../components/auth/ForgotPasswordForm.vue';
import Unauthorized from '../components/pages/errors/Unauthorized.vue';

// Auth guards
import { requireAuth, requireGuest, requireAdmin } from './guards/auth';

export default [
    // Guest routes (login, register)
    {
        path: '/login',
        name: 'auth.login',
        component: LoginForm,
        beforeEnter: requireGuest,
        meta: { layout: 'guest' }
    },
    {
        path: '/register',
        name: 'auth.register',
        component: RegisterForm,
        beforeEnter: requireGuest,
        meta: { layout: 'guest' }
    },
    {
        path: '/forgot-password',
        name: 'auth.forgot-password',
        component: ForgotPasswordForm,
        beforeEnter: requireGuest,
        meta: { layout: 'guest' }
    },
    
    // Error pages
    {
        path: '/unauthorized',
        name: 'unauthorized',
        component: Unauthorized,
        meta: { layout: 'admin' }
    },

    // Admin routes (protected)
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
        beforeEnter: requireAuth,
        meta: { layout: 'admin' }
    },
    {
        path: '/admin/appointments',
        name: 'admin.appointments',
        component: ListAppointments,
        beforeEnter: requireAdmin,
        meta: { layout: 'admin' }
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: UserList,
        beforeEnter: requireAdmin,
        meta: { layout: 'admin' }
    },
    {
        path: '/admin/settings',
        name: 'admin.settings',
        component: UpdateSettings,
        beforeEnter: requireAdmin,
        meta: { layout: 'admin' }
    },
    {
        path: '/admin/profile',
        name: 'admin.profile',
        component: UpdateProfile,
        beforeEnter: requireAuth,
        meta: { layout: 'admin' }
    },

    // User dashboard route
    {
        path: '/dashboard',
        name: 'user.dashboard',
        component: Dashboard,
        beforeEnter: requireAuth,
        meta: { layout: 'admin' }
    },

    // Redirect root to appropriate dashboard
    {
        path: '/',
        redirect: to => {
            // This will be handled by the app initialization
            return '/admin/dashboard'
        }
    },

    // Catch all route
    {
        path: '/:pathMatch(.*)*',
        redirect: '/admin/dashboard'
    }
]
