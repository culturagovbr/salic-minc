import * as types from './types';

export const state = {
    contasBancarias: [],
    conciliacaoBancaria: [],
    inconsistenciaBancaria: [],
    liberacao: [],
    saldoContas: [],
    extratosBancarios: [],
    extratosBancariosConsolidado: [],
    captacao: [],
    devolucoesIncentivador: [],
};

export const mutations = {
    [types.SET_CONTAS_BANCARIAS](state, dados) {
        state.contasBancarias = dados;
    },
    [types.SET_CONCILIACAO_BANCARIA](state, dados) {
        state.conciliacaoBancaria = dados;
    },
    [types.SET_INCONSISTENCIA_BANCARIA](state, dados) {
        state.inconsistenciaBancaria = dados;
    },
    [types.SET_LIBERACAO](state, dados) {
        state.liberacao = dados;
    },
    [types.SET_SALDO_CONTAS](state, dados) {
        state.saldoContas = dados;
    },
    [types.SET_EXTRATOS_BANCARIOS](state, dados) {
        state.extratosBancarios = dados;
    },
    [types.SET_EXTRATOS_BANCARIOS_CONSOLIDADO](state, dados) {
        state.extratosBancariosConsolidado = dados;
    },
    [types.SET_CAPTACAO](state, dados) {
        state.captacao = dados;
    },
    [types.SET_DEVOLUCOES_INCENTIVADOR](state, dados) {
        state.devolucoesIncentivador = dados;
    },
};
