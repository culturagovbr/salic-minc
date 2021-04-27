import * as types from './types';

export const state = {
    comunicados: [],
};

export const mutations = {
    [types.SET_COMUNICADOS](state, comunicados) {
        state.comunicados = comunicados;
    },
};
