import { createWebHistory, createRouter } from 'vue-router';
import { useAuthStore } from '@/store/auth';

/* Guest Component */
import Login from '../components/auth/login.vue';

/* Layout */
import Layout from '../components/layouts/layout.vue';

/* Authenticated Components */
import Home from '../components/home/home.vue';
import Users from '../components/administration/users.vue';
import Permissions from '../components/administration/permissions.vue';
import Roles from '../components/administration/roles.vue';
import BookList from '../components/books/book-list.vue';
import LoanList from '../components/loans/loan-list.vue';
import ReminderManagement from '../components/loans/reminder-management.vue';
import StatsDashboard from '../components/statistics/stats-dashboard.vue';

export const routes = [
    {
        name: 'login',
        path: '/login',
        component: Login,
        meta: {
            middleware: 'guest',
            title: 'Connexion',
        },
    },
    {
        path: '/',
        component: Layout,
        meta: {
            middleware: 'auth',
        },
        children: [
            {
                name: 'home',
                path: '/home',
                component: Home,
                meta: {
                    title: 'Accueil',
                    permissions: 'can_see_home_page',
                },
            },
            {
                name: 'books',
                path: '/books',
                component: BookList,
                meta: {
                    title: 'Ouvrages',
                    permissions: 'can_see_books_page',
                },
            },
            {
                name: 'loans',
                path: '/loans',
                component: LoanList,
                meta: {
                    title: 'Emprunts',
                    permissions: 'can_see_loans_page',
                },
            },
            {
                name: 'reminders',
                path: '/reminders',
                component: ReminderManagement,
                meta: {
                    title: 'Rappels',
                    permissions: 'can_see_reminders_page',
                },
            },
            {
                name: 'stats',
                path: '/stats',
                component: StatsDashboard,
                meta: {
                    title: 'Statistiques',
                    permissions: 'can_see_stats_page',
                },
            },
            {
                name: 'administration',
                path: '/admin',
                children: [
                    {
                        name: 'users',
                        path: 'users',
                        component: Users,
                        meta: {
                            title: 'Utilisateurs',
                            permissions: 'can_use_admin_users_page',
                        },
                    },
                    {
                        name: 'permissions',
                        path: 'permissions',
                        component: Permissions,
                        meta: {
                            title: 'Permissions',
                            permissions: 'can_use_admin_permissions_page',
                        },
                    },
                    {
                        name: 'roles',
                        path: 'roles',
                        component: Roles,
                        meta: {
                            title: 'Roles',
                            permissions: 'can_use_admin_roles_page',
                        },
                    },
                ],
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(window.__ASSET_URL__),
    routes: routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    document.title = `Bibliothèque Municipale - ${to.meta.title}`;

    if (to.meta.middleware === 'guest') {
        if (authStore.authenticated) {
            return next({ name: 'home' });
        }
        next();
    } else {
        if (authStore.authenticated) {
            await authStore.authcheck();
            if (authStore.permissions.includes(to.meta.permissions)) {
                next();
            } else {
                next({ name: authStore.homepage });
            }
        } else {
            next({ name: 'login' });
        }
    }
});

export default router;
