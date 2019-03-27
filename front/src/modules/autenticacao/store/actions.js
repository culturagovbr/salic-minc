import * as helperAPI from '@/helpers/api/Autenticacao';
import * as types from './types';

export const usuarioLogado = ({ commit }) => {
    helperAPI.usuarioLogado()
        .then((response) => {
            const { data } = response;
            commit(types.SET_USUARIO_LOGADO, data.data.items);
        });
};

export const loginAction = ({ commit }, params) => {
    helperAPI.login(params)
        .then((response) => {
            const { data } = response;
            commit(types.SET_LOGIN, data);

            if (data.status) {
                commit('noticias/SET_DADOS', {
                    ativo: true,
                    color: 'success',
                    text: 'Login efetuado com sucesso!',
                },
                { root: true });
            } else {
                commit('noticias/SET_DADOS', {
                    ativo: true,
                    color: 'Error',
                    text: 'Erro ao tentar efetur login!',
                },
                { root: true });
            }
        });
};

export const recoverAction = ({ commit }) => {
    const user = localStorage.getItem('user');
    commit(types.SET_LOGIN, JSON.parse(user));
};
