import Vue from 'vue';
import Vuex from 'vuex';

import modal from '@/components/modal/store';
import layout from '@/components/layout/store';
import projeto from './modules/projeto/store';
import foo from './modules/foo/store';
import avaliacaoResultados from './modules/avaliacaoResultados/store';
import paginaInicial from './modules/paginaInicial/store';
import proposta from './modules/proposta/store';
import autenticacao from './modules/autenticacao/store';
import noticias from './modules/noticias/store';
import dateFilter from './filters/date';
import prestacaoContas from './modules/prestacaoContas/store';
import dadosBancarios from './modules/dadosBancarios/store';
import execucao from './modules/execucao/store';
import outrasInformacoes from './modules/outrasInformacoes/store';
import analise from './modules/analise/store';

Vue.use(Vuex);
Vue.filter('date', dateFilter);

const debug = process.env.NODE_ENV !== 'production' || process.env.NODE_ENV !== 'staging';

export default new Vuex.Store({
    modules: {
        projeto,
        foo,
        modal,
        layout,
        avaliacaoResultados,
        paginaInicial,
        proposta,
        autenticacao,
        noticias,
        prestacaoContas,
        dadosBancarios,
        execucao,
        outrasInformacoes,
        analise,
    },
    getters: {
        route: state => state.route,
    },
    strict: debug,
});
