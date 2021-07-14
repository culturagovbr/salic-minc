import * as types from './types';

export const modalOpen = ({ commit }, modal) => {
    commit(types.MODAL_OPEN, modal);
};

export const modalClose = ({ commit }) => {
    commit(types.MODAL_CLOSE);
};
