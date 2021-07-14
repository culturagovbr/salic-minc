import Vue from 'vue';
import Router from 'vue-router';
import Login from './login/login';

Vue.use(Router);

const routes = [
    {
        path: '/',
        name: 'Login',
        component: Login,
        meta: {
            title: 'Login',
        },
    },
];

export default new Router({ routes });
