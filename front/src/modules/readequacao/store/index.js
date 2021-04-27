import * as actions from './actions';
import * as getters from './getters';
import { mutations, state } from './mutations';

export default {
    namespaced: true,
    actions,
    getters,
    mutations,
    state,
};
