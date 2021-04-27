// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
// Vue.config.productionTip = false
// import 'es6-promise/auto';
import 'vuetify/dist/vuetify.min.css';
import pt from 'vuetify/lib/locale/pt';
import Vuetify from 'vuetify';
import Vue from 'vue';
import App from './App';

import {
    store,
    router,
} from './config';

Vue.use(Vuetify, {
    theme: {
        primary: '#0A420E',
        secondary: '#00838F',
        accent: '#9c27b0',
        error: '#ff5252',
        warning: '#fb8c00',
        info: '#2196f3',
        success: '#4caf50',
    },
    lang: {
        locales: { pt },
        current: 'pt',
    },
});

Vue.config.productionTip = false;

// window.onload = () => {
/* eslint-disable-next-line */
new Vue({
    el: '#app',
    router,
    store,
    render: h => h(App),
}).$mount('#app');
// };
