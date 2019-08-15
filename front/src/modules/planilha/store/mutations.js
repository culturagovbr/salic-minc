import * as types from './types';

export const state = {
    unidades: [],
    mediana: {},
};

export const mutations = {
    [types.SET_UNIDADES](state, dados) {
        state.unidades = dados;
    },
    [types.SET_MEDIANA](state, dados) {
        state.mediana = dados;
    },
};
