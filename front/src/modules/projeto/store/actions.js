import * as projetoHelperAPI from '@/helpers/api/Projeto';
import { state } from './mutations';
import * as types from './types';

export const buscaProjeto = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaProjeto(idPronac)
        .then((response) => {
            const { data } = response;
            const projeto = data.data;
            commit(types.SET_PROJETO, projeto);
        });
};

export const buscarProjetoCompleto = ({ commit }, idPronac) => {
    projetoHelperAPI.buscarProjetoCompleto(idPronac)
        .then((response) => {
            const { data } = response;
            const projeto = data.data;
            commit(types.SET_PROJETO, projeto);
        });
};

export const buscaProponente = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaProponente(idPronac)
        .then((response) => {
            const { data } = response;
            const proponente = data.data;
            commit(types.SET_PROPONENTE, proponente);
        });
};

export const buscaPlanilhaHomologada = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaPlanilhaHomologada(idPronac)
        .then((response) => {
            const { data } = response;
            const planilhaHomologada = data.data;
            commit(types.SET_PLANILHA_HOMOLOGADA, planilhaHomologada);
        });
};

export const buscaPlanilhaOriginal = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaPlanilhaOriginal(idPronac)
        .then((response) => {
            const { data } = response;
            const planilhaOriginal = data.data;
            commit(types.SET_PLANILHA_ORIGINAL, planilhaOriginal);
        });
};

export const buscaPlanilhaReadequada = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaPlanilhaReadequada(idPronac)
        .then((response) => {
            const { data } = response;
            const planilhaReadequada = data.data;
            commit(types.SET_PLANILHA_READEQUADA, planilhaReadequada);
        });
};

export const buscaPlanilhaAutorizada = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaPlanilhaAutorizada(idPronac)
        .then((response) => {
            const { data } = response;
            const planilhaAutorizada = data.data;
            commit(types.SET_PLANILHA_AUTORIZADA, planilhaAutorizada);
        });
};

export const buscaPlanilhaAdequada = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaPlanilhaAdequada(idPronac)
        .then((response) => {
            const { data } = response;
            const planilhaAdequada = data.data;
            commit(types.SET_PLANILHA_ADEQUADA, planilhaAdequada);
        });
};

export const buscarTransferenciaRecursos = ({ commit }, acao) => {
    const { projeto } = state;
    const { idPronac } = projeto;
    projetoHelperAPI.buscarTransferenciaRecursos(idPronac, acao)
        .then((response) => {
            const { data } = response;
            const transferenciaRecursos = data.data;
            commit(types.SET_TRANSFERENCIA_RECURSOS, transferenciaRecursos);
        });
};
