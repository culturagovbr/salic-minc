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

export const buscarProjetoCompleto = ({ commit }, idPronac) => {
    projetoHelperAPI.buscarProjetoCompleto(idPronac)
        .then((response) => {
            const data = response.data;
            const projeto = data.data;
            commit(types.SET_PROJETO, projeto);
        });
};

export const buscaProponente = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaProponente(idPronac)
        .then((response) => {
            const data = response.data;
            const proponente = data.data;
            commit(types.SET_PROPONENTE, proponente);
        });
};

export const buscaPlanilhaHomologada = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaPlanilhaHomologada(idPronac)
        .then((response) => {
            const data = response.data;
            const planilhaHomologada = data.data;
            commit(types.SET_PLANILHA_HOMOLOGADA, planilhaHomologada);
        });
};

export const buscaPlanilhaOriginal = ({ commit }, idPreProjeto) => {
    projetoHelperAPI.buscaPlanilhaOriginal(idPreProjeto)
        .then((response) => {
            const data = response.data;
            const planilhaOriginal = data.data;
            commit(types.SET_PLANILHA_ORIGINAL, planilhaOriginal);
        });
};

export const buscaPlanilhaReadequada = ({ commit }, idPronac) => {
    projetoHelperAPI.buscaPlanilhaReadequada(idPronac)
        .then((response) => {
            const data = response.data;
            const planilhaReadequada = data.data;
            commit(types.SET_PLANILHA_READEQUADA, planilhaReadequada);
        });
};

export const buscaPlanilhaAutorizada = ({ commit }, idPreProjeto) => {
    projetoHelperAPI.buscaPlanilhaAutorizada(idPreProjeto)
        .then((response) => {
            const data = response.data;
            const planilhaAutorizada = data.data;
            commit(types.SET_PLANILHA_AUTORIZADA, planilhaAutorizada);
        });
};

export const buscaPlanilhaAdequada = ({ commit }, idPreProjeto) => {
    projetoHelperAPI.buscaPlanilhaAdequada(idPreProjeto)
        .then((response) => {
            const data = response.data;
            const planilhaAdequada = data.data;
            commit(types.SET_PLANILHA_ADEQUADA, planilhaAdequada);
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
