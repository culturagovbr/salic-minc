import * as types from './types';
import * as diligenciaAPI from '@/helpers/api/Diligencia';

export const obterDiligencias = ({ commit }, params) => {
    // commit(types.SET_DILIGENCIAS, []);
    diligenciaAPI.obterDiligencias(params)
        .then((response) => {
            const { data } = response;
            commit(types.SET_DILIGENCIAS, data.items);
        });
};

export const obterDiligenciasProduto = ({ commit }, params) => {
    // commit(types.SET_DILIGENCIAS, []);
    diligenciaAPI.obterDiligenciasProduto(params)
        .then((response) => {
            const { data } = response;
            commit(types.SET_DILIGENCIAS, data.items);
        });
};

export const obterDiligencia = ({ commit }, params) => {
    commit(types.SET_DILIGENCIA, {});
    diligenciaAPI.obterDiligencia(params)
        .then((response) => {
            const { data } = response;
            commit(types.SET_DILIGENCIA, data);
        });
};

export const salvarDiligencia = async ({ commit }, params) => diligenciaAPI
    .salvarDiligencia(params)
    .then((response) => {
        commit('noticias/SET_DADOS',
            {
                ativo: true,
                color: 'success',
                text: 'Salvo com sucesso!',
            },
            { root: true });
        return response.data;
    }).catch((e) => {
        commit('noticias/SET_DADOS',
            {
                ativo: true,
                color: 'error',
                text: e.response.data.message,
            },
            { root: true });
        throw new TypeError(e.response.data.message, 'salvarDiligencia', 10);
    });
