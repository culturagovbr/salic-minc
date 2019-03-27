import * as types from './types';

export const state = {
    usuario: {},
    login: {},
};

export const mutations = {
    [types.SET_USUARIO_LOGADO](state, dados) {
        state.usuario = dados;
    },
    [types.SET_LOGIN](state, dados) {
        state.login = dados;
        localStorage.setItem('user', JSON.stringify(dados));
    },
};
