import { createWebHistory, createRouter } from "vue-router";
import store from '@/store';

import routes from '@/router/routes.js';

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.auth)) {
        if (!store.state.user.id || !localStorage.getItem('sanctumToken')) {
            next({
                name: 'Login'
            });
        }
    }

    if(to.matched.some(record => record.meta.guest)) {
        if (store.state.user.id && localStorage.getItem('sanctumToken')) {
            next({
                name: 'Home'
            });
        }
    }

    if(to.matched.some(record => record.meta.role == 'admin')) {
        if (store.state.user.role != 'admin') {
            next({
                name: 'Home'
            });
        }
    }

    next();
});

export default router;
