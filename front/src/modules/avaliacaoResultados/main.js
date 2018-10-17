// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
// Vue.config.productionTip = false
import Vue from 'vue';
import Vuetify from 'vuetify';
import Index from './Index';

import {
    router,
    store,
} from './config';

Vue.use(Vuetify, {
        theme: {
            primary: "#0A420E",
            secondary: "#00838F",
            accent: "#9c27b0",
            error: "#f44336",
            warning: "#ffeb3b",
            info: "#2196f3",
            success: "#4caf50"
        }
    }
);
Vue.config.productionTip = false;

window.onload = () => {
    /* eslint-disable-next-line */
    const main = new Vue({
        el: '#app',
        router,
        store,
        components: {
            Index,
        },
        template: '<Index/>',
    });
};
