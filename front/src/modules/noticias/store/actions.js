import * as types from './types';

export const setDados = ({ commit, state }, dados) => {
    const data = { ...state.snackbar, ...dados };
    commit(types.SET_DADOS, data);
};

export const mostrarMensagemSucesso = ({ dispatch }, msg) => {
    dispatch('setDados',
        {
            ativo: true,
            color: 'success',
            text: msg,
        },
        { root: true });
};

export const mostrarMensagemErro = ({ dispatch }, msg) => {
    dispatch('setDados',
        {
            ativo: true,
            color: 'error',
            text: msg,
        },
        { root: true });
};
