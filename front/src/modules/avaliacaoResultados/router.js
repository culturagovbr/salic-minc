import Vue from 'vue';
import Router from 'vue-router';
import EmitirParecer from './components/ParecerTecnico/EmitirParecer';
import HistoricoDiligencias from './components/components/HistoricoDiligencias';
import Painel from './components/ParecerTecnico/Painel';
import Planilha from './components/ParecerTecnico/Planilha';
import VisualizarPlanilha from './components/components/VisualizarPlanilha';
import Diligenciar from './components/ParecerTecnico/Diligenciar';
import EmitirLaudoFinal from './components/LaudoFinal/EmitirLaudoFinal';
import Laudo from './components/LaudoFinal/PainelLaudo';
import AnalisarItem from './components/ParecerTecnico/AnalisarItem';
import VisualizarParecer from './components/components/VisualizarParecer';
import VisualizarLaudo from './components/LaudoFinal/VisualizarLaudo';
import ProjetosSimilares from './components/ParecerTecnico/ProjetosSimilares';

Vue.use(Router);

const routes = [
    {
        path: '/emitir-parecer/:id',
        name: 'EmitirEditar',
        component: EmitirParecer,
        meta: {
            title: 'Principal',
        },
    },
    {
        path: '/planilha/:id',
        name: 'AnalisePlanilha',
        component: Planilha,
        meta: {
            title: 'Analise da planilha',
        },
    },
    {
        path: '/visualizar-planilha/:id',
        name: 'VisualizarPlanilha',
        component: VisualizarPlanilha,
        meta: {
            title: 'Visualizar Planilha',
        },
    },
    {
        path: '/diligenciar/:id',
        name: 'Diligenciar',
        component: Diligenciar,
        meta: {
            title: 'Dilengiar o proponente',
        },
    },
    {
        path: '/emitir-laudo-final/:id',
        name: 'EmitirLaudoFinal',
        component: EmitirLaudoFinal,
        meta: {
            title: 'Emitir Laudo Final',
        },
    },
    {
        path: '/laudo',
        name: 'Laudo',
        component: Laudo,
        meta: {
            title: 'Avaliação de Resultados: Laudo Final',
        },
    },
    {
        path: '/laudo/aba-em-analise',
        name: 'laudo_aba_em_analise',
        component: Laudo,
        meta: {
            title: 'Avaliação de Resultados: Laudo Final',
            tab: 'tab-0',
        },
    },
    {
        path: '/laudo/assinar',
        name: 'laudo_aba_assinar',
        component: Laudo,
        meta: {
            title: 'Avaliação de Resultados: Laudo Final',
            tab: 'tab-1',
        },
    },
    {
        path: '/laudo/finalizados',
        name: 'laudo_aba_finalizados',
        component: Laudo,
        meta: {
            title: 'Avaliação de Resultados: Laudo Final',
            tab: 'tab-3',
        },
    },
    {
        path: '/analisar-item/*',
        name: 'AnalisarItem',
        component: AnalisarItem,
        meta: {
            title: 'Análise de itens',
        },
    },
    {
        path: '/visualizar-parecer/:id',
        name: 'VisualizarParecer',
        component: VisualizarParecer,
        meta: {
            title: 'Visualizar parecer',
        },
    },
    {
        path: '/visualizar-laudo/:id',
        name: 'VisualizarLaudo',
        component: VisualizarLaudo,
        meta: {
            title: 'Visualizar laudo',
        },
    },
    {
        path: '/diligencias',
        name: 'HistoricoDiligencias',
        component: HistoricoDiligencias,
        meta: {
            title: 'Avaliação de Resultados: Diligencias do Projeto',
        },
    },
    {
        path: '/painel/aba-em-analise',
        name: 'Painel_Aba_Em_Avaliacao',
        component: Painel,
        meta: {
            title: 'Avaliação de Resultados: Parecer Técnico',
            tab: 'tab-1',
        },
    },
    {
        path: '/painel/dashboard',
        name: 'Painel_Aba_Em_Dashboard',
        component: Painel,
        meta: {
            title: 'Avaliação de Resultados: Parecer Técnico',
            tab: 'tab-6',
        },
    },
    {
        path: '/painel/assinar',
        name: 'painel_aba_assinar',
        component: Painel,
        meta: {
            title: 'Avaliação de Resultados: Parecer Técnico',
            tab: 'tab-2',
        },
    },
    {
        path: '/painel/historico',
        name: 'painel_aba_historico',
        component: Painel,
        meta: {
            title: 'Avaliação de Resultados: Parecer Técnico',
            tab: 'tab-4',
        },
    },
    {
        path: '/painel/distribuir',
        name: 'painel_aba_distribuir',
        component: Painel,
        meta: {
            title: 'Avaliação de Resultados: Parecer Técnico',
            tab: 'tab-0',
        },
    },
    {
        path: '/painel/projetos-similares/:idpronac',
        name: 'projetosSimilares',
        component: ProjetosSimilares,
        meta: {
            title: 'Avaliação de Resultados: Parecer Técnico',
            tab: 'tab-0',
        },
    },
    {
        path: '*',
        name: 'Painel',
        component: Painel,
        meta: {
            title: 'Avaliação de Resultados: Parecer Técnico',
        },
    },
];

export default new Router({ routes });
