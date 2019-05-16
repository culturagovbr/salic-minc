// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
// Vue.config.productionTip = false

import 'vuetify/dist/vuetify.min.css';
import pt from 'vuetify/lib/locale/pt';
import Vuetify from 'vuetify';

import Vue from 'vue';
import Proposta from './visualizar/Proposta';
import PropostaDiff from './visualizar/PropostaDiff';
import PropostaProponente from './visualizar/PropostaProponente';
import PropostaPlanoDistribuicaoDetalhamentos from './views/PlanoDistribuicaoDetalhamentos';
import { store } from './config';


Vue.config.productionTip = false;

Vue.use(Vuetify, {
    theme: {
        primary: '#0A420E',
        secondary: '#00838F',
        accent: '#9c27b0',
        error: '#f44336',
        warning: '#ffeb3b',
        info: '#2196f3',
        success: '#4caf50',
    },
    lang: {
        locales: { pt },
        current: 'pt',
    },
});

window.onload = () => {
    /* eslint-disable-next-line */
    const main = new Vue({
        el: '#container-vue',
        store,
        components: {
            Proposta,
            PropostaDiff,
            PropostaProponente,
            PropostaPlanoDistribuicaoDetalhamentos,
        },
    });
};
