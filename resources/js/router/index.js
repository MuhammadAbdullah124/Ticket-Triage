import { createRouter, createWebHistory } from 'vue-router';

import Dashboard from '../pages/Dashboard.vue';
import Tickets from '../pages/Tickets.vue';
const routes = [
    { path: '/', name: 'Dashboard', component: Dashboard },
    { path: '/tickets', name: 'Ticket', component: Tickets },
    { path: '/tickets', name: 'Ticket', component: Tickets },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
