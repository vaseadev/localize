import Home from '@/pages/Home.vue';
import Login from '@/pages/Login.vue';

import Users from '@/pages/Users/List.vue';
import ViewUser from '@/pages/Users/View.vue';

import Projects from '@/pages/Projects/List.vue';
import ViewProject from '@/pages/Projects/View.vue';
import Languages from '@/pages/Projects/Languages.vue';
import Tokens from '@/pages/Projects/Tokens.vue';
import Translates from '@/pages/Projects/Translates.vue';

import PageNotFound from '@/pages/404.vue';

const routes = [
    {
        path : '/admin',
        name : 'Home',
        component : Home,
        meta: {auth: true}
    },
    {
        path : '/admin/login',
        name : 'Login',
        component : Login,
        meta: {guest: true}
    },
    {
        path : '/admin/projects',
        name : 'Projects',
        component : Projects,
        meta: {auth: true}
    },
    {
        path : '/admin/projects/:id',
        name : 'ViewProject',
        component : ViewProject,
        meta: {auth: true},
        params: true
    },
    {
        path : '/admin/projects/:id/languages',
        name : 'Languages',
        component : Languages,
        meta: {auth: true},
        params: true
    },
    {
        path : '/admin/projects/:id/tokens',
        name : 'Tokens',
        component : Tokens,
        meta: {auth: true},
        params: true
    },
    {
        path : '/admin/projects/:id/translates',
        name : 'Translates',
        component : Translates,
        meta: {auth: true},
        params: true
    },
    {
        path : '/admin/users',
        name : 'Users',
        component : Users,
        meta: {auth: true, role: 'admin'},
    },
    {
        path : '/admin/users/:id',
        name : 'ViewUser',
        component : ViewUser,
        meta: {auth: true, role: 'admin'},
        params: true
    },
    {
        path: '/:pathMatch(.*)*',
        name : 'PageNotFOund',
        component: PageNotFound
    }
];

export default routes;
