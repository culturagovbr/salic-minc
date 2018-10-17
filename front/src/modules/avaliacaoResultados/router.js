import Vue from 'vue';
import Router from 'vue-router';
import ComponenteEncaminhar from './components/ComponenteEncaminhar';
import EmitirParecer from './components/EmitirParecer';
import Painel from './components/Painel';
import TipoAvaliacao from './components/TipoAvaliacao';
import Planilha from './components/Planilha';
import AnaliseComprovantes from './components/AnaliseComprovantes';
import Diligenciar from './components/Diligenciar';
import Historico from './components/Historico';
import ConsolidacaoAnalise from './components/ConsolidacaoAnalise';
import EmitirLaudoFinal from './components/EmitirLaudoFinal';
import Laudo from './components/PainelLaudo';
import AnalisarItem from './components/AnalisarItem';
import RevisaoParecer from './components/RevisaoParecer';

Vue.use(Router);

const routes = [
    {
        path: '/componente-encaminhar',
        name: 'ListBar',
        component: ComponenteEncaminhar,
        meta: {
            title: 'Principal',
        },
    },
    {
        path: '/emitir-parecer/:id',
        name: 'EmitirEditar',
        component: EmitirParecer,
        meta: {
            title: 'Principal',
        },
    },
    {
        path: '/emitir-parecer',
        name: 'Emitir',
        component: EmitirParecer,
        meta: {
            title: 'Principal',
        },
    },
    {
        path: '/revisao',
        name: 'Revisao',
        component: RevisaoParecer,
        meta: {
            title: 'Revisão Parecer',
        },
    },
    {
        path: '/tipo-avaliacao/:id',
        name: 'tipoAvaliacao',
        component: TipoAvaliacao,
        meta: {
            title: 'Tipo Avaliacao',
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
        path: '/analise-comprovantes',
        name: 'Analise',
        component: AnaliseComprovantes,
        meta: {
            title: 'Analise dos comprovantes',
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
        path: '/historico',
        name: 'historico',
        component: Historico,
        meta: {
            title: 'Historico dos encaminhamentos',
        },
    },
    {
        path: '/consolidacao-analise',
        name: 'ConsolidacaoAnalise',
        component: ConsolidacaoAnalise,
        meta: {
            title: 'Consolidacao da Analise',
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
            title: 'Laudo Final de Avaliação de Resultados',
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
        path: '*',
        name: 'Painel',
        component: Painel,
        meta: {
            title: 'Painel',
        },
    },
];

export default new Router({ routes });
