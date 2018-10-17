// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
// Vue.config.productionTip = false
import Vue from 'vue';
import Index from './Index';
import {
    router,
    store,
} from './config';

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
