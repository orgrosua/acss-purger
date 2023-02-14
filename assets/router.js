import { createRouter, createWebHistory } from 'vue-router';

import NotFound from './pages/NotFound.vue';

import SettingsIndex from './pages/settings/Index.vue';

const router = createRouter({
    history: createWebHistory(`${acssPurger.web_history}#/`),
    scrollBehavior(_, _2, savedPosition) {
        return savedPosition || { left: 0, top: 0 };
    },
    routes: [
        { path: '/', name: 'home', redirect: { name: 'settings' } },
        {
            path: '/settings',
            name: 'settings',
            component: SettingsIndex,
        },
        { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },
    ]
});

export default router;