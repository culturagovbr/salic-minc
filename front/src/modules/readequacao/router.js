import AnalisarDadosGerais from './components/analise/AnalisarDadosGerais';
import AnalisarAlteracoes from './components/analise/AnalisarAlteracoes';

const PainelReadequacoesView = () => import(/* webpackChunkName: "painel-readequacoes-view" */ './views/PainelReadequacoesView');
const AnalisarReadequacaoView = () => import(/* webpackChunkName: "analisar-readequacao-view" */ './views/AnalisarReadequacaoView');
const SaldoAplicacaoView = () => import(/* webpackChunkName: "saldo-aplicacao-view" */ './views/SaldoAplicacaoView.vue');

export default [
    {
        path: '/readequacao/painel',
        name: 'PainelReadequacoesAnalise',
        component: PainelReadequacoesView,
        meta: {
            title: 'Readequações - painel de análise',
        },
    },
    {
        path: '/readequacao/painel/:idPronac',
        name: 'PainelReadequacoes',
        component: PainelReadequacoesView,
        meta: {
            title: 'Painel de Readequações',
        },
    },
    {
        path: '/readequacao/gerenciar-assinaturas',
        redirect: '/readequacao/painel',
    },
    {
        path: '/readequacao/analisar/:idPronac/:idReadequacao',
        component: AnalisarReadequacaoView,
        meta: {
            title: 'Analisar Readequação',
        },
        children: [
            {
                path: '',
                name: 'analisar-dados-gerais',
                component: AnalisarDadosGerais,
            },
            {
                path: 'alteracoes',
                name: 'analisar-alteracoes',
                component: AnalisarAlteracoes,
            },
        ],
    },
    {
        path: '/readequacao/saldo-aplicacao/:idPronac',
        name: 'SaldoAplicacaoView',
        component: SaldoAplicacaoView,
        meta: {
            title: 'Saldo de Aplicação',
        },
    },
];
