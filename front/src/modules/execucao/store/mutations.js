import * as types from './types';

export const state = {
    marcasAnexadas: [],
    dadosReadequacoes: [],
    pedidoProrrogacao: [],
    dadosFiscalizacaoLista: {},
    dadosFiscalizacaoVisualiza: {},
};

export const mutations = {
    [types.SET_MARCAS_ANEXADAS](state, dados) {
        state.marcasAnexadas = dados;
    },
    [types.SET_DADOS_READEQUACOES](state, dados) {
        state.dadosReadequacoes = dados;
    },
    [types.SET_PEDIDO_PRORROGACAO](state, dados) {
        state.pedidoProrrogacao = dados;
    },
    [types.SET_DADOS_FISCALIZACAO_LISTA](state, dados) {
        state.dadosFiscalizacaoLista = dados;
    },
    [types.SET_DADOS_FISCALIZACAO_VISUALIZA](state, dados) {
        state.dadosFiscalizacaoVisualiza = dados;
    },
};
