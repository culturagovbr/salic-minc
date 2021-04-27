import * as types from './types';

export const state = {
    diligencias: [],
    diligencia: {},
};

export const mutations = {
    [types.SET_DILIGENCIAS](state, dados) {
        state.diligencias = dados;
    },
    [types.SET_DILIGENCIA](state, dados) {
        state.diligencia = dados;
    },
};
