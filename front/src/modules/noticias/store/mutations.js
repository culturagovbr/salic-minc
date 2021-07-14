import * as types from './types';

export const state = {
    snackbar: {},
};

export const mutations = {
    [types.SET_DADOS](state, dados) {
        state.snackbar = dados;
    },
};
