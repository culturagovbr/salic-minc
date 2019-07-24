const PainelReadequacoesView = () => import(/* webpackChunkName: "painel-readequacoes-view" */ './views/PainelReadequacoesView.vue');
const SaldoAplicacaoView = () => import(/* webpackChunkName: "saldo-aplicacao-view" */ './views/SaldoAplicacaoView.vue');

export default [
    {
        path: '/readequacao/painel/:idPronac',
        name: 'PainelReadequacoes',
        component: PainelReadequacoesView,
        meta: {
            title: 'Painel de Readequações',
        },
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
