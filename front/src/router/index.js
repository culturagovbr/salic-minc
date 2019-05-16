import Vue from 'vue';
import Router from 'vue-router';
import PaginaInicial from '@/modules/paginaInicial';
import Pagina404 from '@/components/404';
import RotasFoo from '@/modules/foo/router';
import Readequacao from '@/modules/readequacao/router';
import RotasParecer from '@/modules/parecer/router';

Vue.use(Router);

const baseRoutes = [
    {
        path: '/',
        name: 'Página Inicial',
        component: PaginaInicial,
    },
    {
        path: '*',
        component: Pagina404,
    },
];

let routes = [];
routes = routes.concat(Readequacao);
routes = routes.concat(RotasFoo);
routes = routes.concat(RotasParecer);
routes = routes.concat(baseRoutes);

export default new Router({
    routes,
});
