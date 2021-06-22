import * as types from './types';

export const setDados = ({ commit, state }, dados) => {
    const data = { ...state.snackbar, ...dados };
    commit(types.SET_DADOS, data);
};

export const mensagemSucesso = ({ dispatch }, msg) => {
    dispatch(
        'setDados',
        {
            ativo: true,
            color: 'success',
            text: msg,
        },
    );
};

export const mensagemErro = ({ dispatch }, msg) => {
    dispatch(
        'setDados',
        {
            ativo: true,
            color: 'error',
            text: msg,
        },
    );
};
