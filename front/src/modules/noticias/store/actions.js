import * as types from './types';

export const setDados = ({ commit, state }, dados) => {
    const data = { ...state.snackbar, ...dados };
    commit(types.SET_DADOS, data);
};
