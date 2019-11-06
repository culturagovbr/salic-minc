import * as types from './types';

export const state = {
    localRealizacaoDeslocamento: {},
    locaisRealizacao: [],
    planoDistribuicaoDetalhamentos: [],
    fontesDeRecursos: {},
    documentos: {},
    proposta: {},
    historicoSolicitacoes: [],
    historicoEnquadramento: [],
};

export const mutations = {
    [types.SET_LOCAL_REALIZACAO_DESLOCAMENTO](state, localRealizacaoDeslocamento) {
        state.localRealizacaoDeslocamento = localRealizacaoDeslocamento;
    },
    [types.SET_LOCAIS_REALIZACAO](state, locais) {
        state.locaisRealizacao = locais;
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
    [types.SET_PLANO_DISTRIBUICAO_DETALHAMENTOS](state, data) {
        state.planoDistribuicaoDetalhamentos = data;
    },
    [types.UPDATE_PLANO_DISTRIBUICAO_DETALHAMENTO](state, data) {
        const index = state.planoDistribuicaoDetalhamentos.findIndex(
            item => parseInt(item.idDetalhaPlanoDistribuicao, 10) === parseInt(data.idDetalhaPlanoDistribuicao, 10),
        );

        if (index >= 0) {
            Object.assign(state.planoDistribuicaoDetalhamentos[index], data);
        } else {
            state.planoDistribuicaoDetalhamentos.push(data);
        }
    },
    [types.EXCLUIR_PLANO_DISTRIBUICAO_DETALHAMENTO](state, data) {
        const index = state.planoDistribuicaoDetalhamentos.findIndex(
            item => parseInt(item.idDetalhaPlanoDistribuicao, 10) === parseInt(data.idDetalhaPlanoDistribuicao, 10),
        );
        state.planoDistribuicaoDetalhamentos.splice(index, 1);
    },
};
