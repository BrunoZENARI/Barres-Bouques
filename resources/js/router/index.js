import {createWebHistory, createRouter} from "vue-router";

import axios from 'axios';

import { useAuthStore } from '@/store/auth'

/* Guest Component */
import Login from '../components/auth/Login.vue';
/* Guest Component */

/* Layouts */
import Layout from '../components/layouts/Layout.vue';
/* Layouts */

/* *********** Authenticated Component *********** */
import Home from '../components/home/Home.vue';
import Users from '../components/administration/Users.vue';
import Permissions from '../components/administration/Permissions.vue';
import Roles from '../components/administration/Roles.vue';
/* *********** Authenticated Component *********** */


export const routes = [
    {
        name:"login",
        path:"/login",
        component:Login,
        meta:{
            middleware:"guest",
            title:`Login`
        }
    },
    {
        path:"/",
        component:Layout,
        meta:{
            middleware:"auth"
        },
        children:[
            {
                name:"home",
                path: '/home',
                component: Home,
                meta:{
                    title:`Accueil`,
                    permissions : 'can_see_home_page',
                }
            },
            {
                name:"administration",
                path: '/admin',
                children:[
                    {
                        name:"users",
                        path: 'user',
                        component: Users,
                        meta:{
                            title:`Utilisateurs`,
                            permissions : 'can_use_admin_users_page'
                        }
                    },
                    {
                        name:"permissions",
                        path: 'permission',
                        component: Permissions,
                        meta:{
                            title:`Permissions`,
                            permissions : 'can_use_admin_permissions_page'
                        }
                    },
                    {
                        name:"roles",
                        path: 'role',
                        component: Roles,
                        meta:{
                            title:`Roles`,
                            permissions : 'can_use_admin_roles_page'
                        }
                    },
                ]
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(window.__ASSET_URL__),
    routes: routes
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()
    document.title = `fedex-onboarding - ${to.meta.title}`
    if(to.meta.middleware=="guest"){
        if(authStore.authenticated){
            next({name:"home"})
        }
        next()
    }else{
        if(authStore.authenticated){
            authStore.authcheck()
            if(authStore.permissions.includes(to.meta.permissions)){
                next()
            }else{
                next({name:authStore.homepage})
            }
        }else{
            next({name:"login"})
        }
    }
})



export default router
