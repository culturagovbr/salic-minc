import * as types from './types';

export const state = {
    localRealizacaoDeslocamento: {},
    fontesDeRecursos: {},
    documentos: {},
    proposta: {},
    historicoSolicitacoes: {},
    historicoEnquadramento: {},
};

export const mutations = {
    [types.SET_LOCAL_REALIZACAO_DESLOCAMENTO](state, localRealizacaoDeslocamento) {
        state.localRealizacaoDeslocamento = localRealizacaoDeslocamento;
    },
    [types.SET_FONTES_DE_RECURSOS](state, fontesDeRecursos) {
        state.fontesDeRecursos = fontesDeRecursos;
    },
    [types.SET_DOCUMENTOS](state, documentos) {
        state.documentos = documentos;
    },
    [types.SET_DADOS_PROPOSTA](state, proposta) {
        state.proposta = proposta;
    },
    [types.SET_HISTORICO_SOLICITACOES](state, historicoSolicitacoes) {
        state.historicoSolicitacoes = historicoSolicitacoes;
    },
    [types.SET_HISTORICO_ENQUADRAMENTO](state, historicoEnquadramento) {
        state.historicoEnquadramento = historicoEnquadramento;
    },
};
