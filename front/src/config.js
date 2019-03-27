import { sync } from 'vuex-router-sync';
import store from './store';
import router from './router/index';

sync(store, router);

export {
    store,
    router,
};
