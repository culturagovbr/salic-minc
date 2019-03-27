import * as types from './types';

export const state = {
    certidoesNegativas: {},
    documentosAssinados: [],
    dadosComplementares: {},
    documentosAnexados: [],
    localRealizacaoDeslocamento: {},
    providenciaTomada: {},
    planoDistribuicaoIn2013: [],
    historicoEncaminhamento: {},
    tramitacaoDocumento: [],
    tramitacaoProjeto: [],
    ultimaTramitacao: [],
    planoDistribuicaoIn2017: [],
    diligenciaProposta: [],
    diligenciaAdequacao: [],
    diligenciaProjeto: [],
    diligencia: [],
};

export const mutations = {
    [types.SET_CERTIDOES_NEGATIVAS](state, certidoesNegativas) {
        state.certidoesNegativas = certidoesNegativas;
    },
    [types.SET_DOCUMENTOS_ASSINADOS](state, documentosAssinados) {
        state.documentosAssinados = documentosAssinados;
    },
    [types.SET_DADOS_COMPLEMENTARES](state, dadosComplementares) {
        state.dadosComplementares = dadosComplementares;
    },
    [types.SET_DOCUMENTOS_ANEXADOS](state, dados) {
        state.documentosAnexados = dados;
    },
    [types.SET_LOCAL_REALIZACAO_DESLOCAMENTO](state, localRealizacaoDeslocamento) {
        state.localRealizacaoDeslocamento = localRealizacaoDeslocamento;
    },
    [types.SET_PROVIDENCIA_TOMADA](state, providenciaTomada) {
        state.providenciaTomada = providenciaTomada;
    },
    [types.SET_PLANO_DISTRIBUICAO_IN2013](state, dados) {
        state.planoDistribuicaoIn2013 = dados;
    },
    [types.SET_HISTORICO_ENCAMINHAMENTO](state, historicoEncaminhamento) {
        state.historicoEncaminhamento = historicoEncaminhamento;
    },
    [types.SET_TRAMITACAO_DOCUMENTO](state, tramitacaoDocumento) {
        state.tramitacaoDocumento = tramitacaoDocumento;
    },
    [types.SET_TRAMITACAO_PROJETO](state, tramitacaoProjeto) {
        state.tramitacaoProjeto = tramitacaoProjeto;
    },
    [types.SET_ULTIMA_TRAMITACAO](state, ultimaTramitacao) {
        state.ultimaTramitacao = ultimaTramitacao;
    },
    [types.SET_PLANO_DISTRIBUICAO_IN2017](state, dados) {
        state.planoDistribuicaoIn2017 = dados;
    },
    [types.SET_DILIGENCIA_PROPOSTA](state, dados) {
        state.diligenciaProposta = dados;
    },
    [types.SET_DILIGENCIA_ADEQUACAO](state, dados) {
        state.diligenciaAdequacao = dados;
    },
    [types.SET_DILIGENCIA_PROJETO](state, dados) {
        state.diligenciaProjeto = dados;
    },
    [types.SET_DILIGENCIA](state, dados) {
        state.diligencia = dados;
    },
};
