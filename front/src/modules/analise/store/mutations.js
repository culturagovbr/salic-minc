import * as types from './types';

export const state = {
    aprovacao: {},
    recurso: [],
};

export const mutations = {
    [types.SET_APROVACAO](state, dados) {
        state.aprovacao = dados;
    },
    [types.SET_RECURSO](state, dados) {
        state.recurso = dados;
    },
};
