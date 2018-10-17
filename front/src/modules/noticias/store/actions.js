import * as types from './types';

export const setDados = ({ commit, state }, dados) => {
    dados = { ...state.snackbar, ...dados };
    commit(types.SET_DADOS, dados);
};
