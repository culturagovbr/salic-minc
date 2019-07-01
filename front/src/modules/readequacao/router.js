// import SaldoAplicacao from './views/SaldoAplicacaoView';
import PainelReadequacoes from './views/PainelReadequacoesView';
import AnalisarReadequacao from './views/AnalisarReadequacao';

export default [
    {
        path: '/readequacao/painel/:idPronac',
        name: 'PainelReadequacoes',
        component: PainelReadequacoes,
        meta: {
            title: 'Painel de Readequações',
        },
    },
    {
        path: '/readequacao/analisar/:idPronac/:idReadequacao',
        name: 'AnalisarReadequacao',
        component: AnalisarReadequacao,
        meta: {
            title: 'Analisar Readequação',
        },
    },
];
