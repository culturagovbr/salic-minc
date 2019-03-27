import * as types from './types';

export const state = {
    modalAberta: false,
};

export const mutations = {
    [types.MODAL_OPEN](state, modal) {
        state.modalAberta = modal;
    },
    [types.MODAL_CLOSE](state) {
        state.modalAberta = '';
    },
};
