import AnaliseDeConteudo from './components/AnaliseDeConteudo';
import AnaliseDeCustos from './components/AnaliseDeCustos';
import AnaliseConsolidacao from './components/AnaliseConsolidacao';
import AnaliseFinalizacao from './components/AnaliseFinalizacao';

const ParecerAnalisarView = () => import(/* webpackChunkName: "parecer-analisar-view" */ './views/ParecerAnalisarView');
const ParecerAnalisarListaView = () => import(/* webpackChunkName: "parecer-listar-view" */ './views/ParecerAnalisarListaView');
const ParecerGerenciarListaView = () => import(/* webpackChunkName: "parecer-listar-view" */ './views/ParecerGerenciarListaView');
const ParecerGerenciarVisualizarView = () => import(/* webpackChunkName: "parecer-listar-view" */ './views/ParecerGerenciarVisualizarView');

export default [
    {
        path: '/parecer/analise-inicial',
        name: 'parecer-listar-view',
        component: ParecerAnalisarListaView,
        meta: {
            title: 'Produtos para análise',
        },
    },
    {
        path: '/parecer/analise-inicial/analisar/:id/:idPronac/:produtoPrincipal',
        component: ParecerAnalisarView,
        children: [
            {
                path: '',
                name: 'analise-conteudo',
                component: AnaliseDeConteudo,
                meta: {
                    title: 'Análise inicial',
                },
            },
            {
                path: 'custos',
                name: 'analise-de-custos',
                component: AnaliseDeCustos,
            },
            {
                path: 'consolidacao',
                name: 'analise-consolidacao',
                component: AnaliseConsolidacao,
            },
            {
                path: 'finalizacao',
                name: 'analise-finalizacao',
                component: AnaliseFinalizacao,
            },
        ],
    },
    {
        path: '/parecer/gerenciar',
        name: 'parecer-gerenciar-listar-view',
        component: ParecerGerenciarListaView,
        meta: {
            title: 'Produtos para análise',
        },
    },
    {
        path: '/parecer/gerenciar/produto/:id/:idPronac/:produtoPrincipal',
        name: 'parecer-gerenciar-visualizar-view',
        component: ParecerGerenciarVisualizarView,
        meta: {
            title: 'Análise Produto',
        },
    },
];
