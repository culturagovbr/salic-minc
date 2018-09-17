import Vue from 'vue';
import Router from 'vue-router';

import Listar from './listar/Index';
import Visualizar from './visualizar/Visualizar';
import DadosProjeto from './visualizar/components/DadosProjeto';
import PlanilhaPropostaOriginal from './visualizar/components/incentivo/planilha/PlanilhaPropostaOriginal';
import PlanilhaPropostaAutorizada from './visualizar/components/incentivo/planilha/PlanilhaPropostaAutorizada';
import PlanilhaPropostaAdequada from './visualizar/components/incentivo/planilha/PlanilhaPropostaAdequada';
import PlanilhaHomologada from './visualizar/components/incentivo/planilha/PlanilhaHomologada';
import PlanilhaReadequada from './visualizar/components/incentivo/planilha/PlanilhaReadequada';
import RelacaoDePagamentos from './visualizar/components/incentivo/RelacaoDePagamentos';
import Proponente from './visualizar/components/incentivo/Proponente';
import Convenente from './visualizar/components/convenio/Convenente';
import Proposta from './visualizar/components/incentivo/Proposta';

Vue.use(Router);

const templateAjax = {
    template: '<div id="conteudo"></div>',
};

const routes = [
    {
        path: '/',
        name: 'index',
        component: Listar,
        meta: {
            title: 'Lista de projetos',
        },
    },
    {
        path: '/:idPronac',
        component: Visualizar,
        children: [
            {
                path: '',
                name: 'dadosprojeto',
                component: DadosProjeto,
                meta: {
                    title: 'Dados do Projeto',
                },
            },
            {
                path: 'proponente',
                name: 'proponente',
                component: Proponente,
                meta: {
                    title: 'Proponente',
                },
            },
            {
                path: 'convenente',
                name: 'convenente',
                component: Convenente,
                meta: {
                    title: 'Convenente',
                },
            },
            {
                path: 'planilha-proposta',
                name: 'planilhaproposta',
                component: PlanilhaPropostaOriginal,
                meta: {
                    title: 'Planilha de Solicita&ccedil;&atilde;o da Proposta Original',
                },
            },
            {
                path: 'planilha-autorizada',
                name: 'planilhaautorizada',
                component: PlanilhaPropostaAutorizada,
                meta: {
                    title: 'Planilha Autorizada para Captar',
                },
            },
            {
                path: 'planilha-adequada',
                name: 'planilhaadequada',
                component: PlanilhaPropostaAdequada,
                meta: {
                    title: 'Planilha Adequada &agrave; realidade de execu&ccedil;&atilde;o pelo proponente',
                },
            },
            {
                path: 'planilha-homologada',
                name: 'planilhahomologada',
                component: PlanilhaHomologada,
                meta: {
                    title: 'Planilha Homologada para execu&ccedil;&atilde;o',
                },
            },
            {
                path: 'planilha-aprovada',
                name: 'planilhaaprovada',
                component: PlanilhaHomologada,
                meta: {
                    title: 'Planilha Autorizada para Captar',
                },
            },
            {
                path: 'planilha-readequada',
                name: 'planilhareadequada',
                component: PlanilhaReadequada,
                meta: {
                    title: 'Planilha Readequada na execu&ccedil;&atilde;o',
                },
            },
            {
                path: 'relacao-de-pagamentos',
                name: 'relacaodepagamentos',
                component: RelacaoDePagamentos,
                meta: {
                    title: 'Rela&ccedil;&atilde;o de Pagamentos',
                },
            },
            {
                path: 'proposta',
                name: 'proposta',
                component: Proposta,
                meta: {
                    title: 'Proposta',
                },
            },
            {
                path: 'conteudo-dinamico',
                name: 'containerAjax',
                component: templateAjax,
            },
        ],
    },
];

export default new Router({ routes });
