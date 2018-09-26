import Vue from 'vue';
import Router from 'vue-router';
import Index from './Index';

Vue.use(Router);


const routes = [
    {
        path: '/:idPreProjeto',
        name: 'index',
        component: Index,
        meta: {
            title: 'Inicio',
        },
    },
];

export default new Router({ routes });
