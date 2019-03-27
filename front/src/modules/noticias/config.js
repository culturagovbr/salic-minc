import { sync } from 'vuex-router-sync';
import store from '../../store';
import router from './router';

sync(store, router);

export {
    store,
    router,
};
