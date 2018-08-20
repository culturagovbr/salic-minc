import * as projetoHelperAPI from '@/helpers/api/Projeto';
import { state } from './mutations';
import * as types from './types';

export const buscaProjeto = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaProjeto(idPronac)
        .then((response) => {
            const data = response.data;
            const projeto = data.data;
            commit(types.SET_PROJETO, projeto);
        });
};

export const buscarTransferenciaRecursos = ({ commit }, acao) => {
    const projeto = state.projeto;
    const idPronac = projeto.idPronac;
    projetoHelperAPI.buscarTransferenciaRecursos(idPronac, acao)
        .then((response) => {
            const data = response.data;
            const transferenciaRecursos = data.data;
            commit(types.SET_TRANSFERENCIA_RECURSOS, transferenciaRecursos);
        });
};
