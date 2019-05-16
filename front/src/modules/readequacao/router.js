// import SaldoAplicacao from './views/SaldoAplicacaoView';
import PainelReadequacoes from './views/PainelReadequacoesView';

export default [
    {
        path: '/readequacao/painel/:idPronac',
        name: 'PainelReadequacoes',
        component: PainelReadequacoes,
        meta: {
            title: 'Painel de Readequações',
        },
    },
];
