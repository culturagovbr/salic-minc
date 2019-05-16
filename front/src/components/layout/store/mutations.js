import * as types from './types';

export const state = {
    perfisDisponiveis: [],
    solicitacoes: {},
    quantidadeSolicitacoes: 0,
    modoNoturno: false,
    statusSidebarDireita: false,
    statusSidebarEsquerda: false,
    versao: {},
    menuPrincipal: [],
};

export const mutations = {
    [types.SET_PERFIS_DISPONIVEIS](state, dados) {
        state.perfisDisponiveis = dados;
    },
    [types.SET_SOLICITACOES](state, dados) {
        state.solicitacoes = dados;
    },
    [types.SET_QUANTIDADE_SOLICITACOES](state, dados) {
        state.quantidadeSolicitacoes = dados;
    },
    [types.SET_VERSAO](state, dados) {
        state.versao = dados;
    },
    [types.SET_MODO_NOTURNO](state, status) {
        state.modoNoturno = status;
    },
    [types.SET_STATUS_SIDEBAR_DIREITA](state, status) {
        state.statusSidebarDireita = status;
    },
    [types.SET_STATUS_SIDEBAR_ESQUERDA](state, status) {
        state.statusSidebarEsquerda = status;
    },
    [types.SET_MENU_PRINCIPAL](state, dados) {
        state.menuPrincipal = dados;
    },
};
