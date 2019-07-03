import AnalisarJustificativa from './components/AnalisarJustificativa';
import AnalisarAlteracoes from './components/AnalisarAlteracoes';

const PainelReadequacoesView = () => import(/* webpackChunkName: "painel-readequacoes-view" */ './views/PainelReadequacoesView');
const SaldoAplicacaoView = () => import(/* webpackChunkName: "saldo-aplicacao-view" */ './views/SaldoAplicacaoView');
const AnalisarReadequacaoView = () => import(/* webpackChunkName: "analisar-readequacao-view" */ './views/AnalisarReadequacaoView');

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
        path: '/readequacao/analisar/:idPronac/:idReadequacao',
        name: 'AnalisarReadequacao',
        component: AnalisarReadequacaoView,
        meta: {
            title: 'Analisar Readequação',
        },
        children: [
            {
                path: 'justificativa',
                name: 'analisar-justificativa',
                component: AnalisarJustificativa,
            },
            {
                path: 'alteracoes',
                name: 'analisar-alteracoes',
                component: AnalisarAlteracoes,
            },
        ],
    },
];
