import Vue from 'vue';
import * as types from './types';

export const state = {
    readequacoesProponente: {},
    readequacoesAnalise: {},
    readequacoesFinalizadas: {},
    readequacao: {},
    saldoAplicacao: {},
    saldoAplicacaoDisponivelEdicaoItem: {},
    campoAtual: {},
    tiposDisponiveis: [],
};

export const mutations = {
    [types.GET_READEQUACOES_PROPONENTE](state, readequacoes) {
        state.readequacoesProponente = readequacoes;
    },
    [types.GET_READEQUACOES_ANALISE](state, readequacoes) {
        state.readequacoesAnalise = readequacoes;
    },
    [types.GET_READEQUACOES_FINALIZADAS](state, readequacoes) {
        state.readequacoesFinalizadas = readequacoes;
    },
    [types.SET_READEQUACAO](state, readequacao) {
        state.readequacao = readequacao;
    },
    [types.GET_READEQUACAO](state, readequacao) {
        state.readequacao = readequacao;
    },
    [types.GET_CAMPO_ATUAL](state, campoAtual) {
        state.campoAtual = campoAtual;
    },
    [types.SET_CAMPO_ATUAL](state, campoAtual) {
        const chave = `key_${campoAtual.idTipoReadequacao}`;
        Vue.set(state.campoAtual, chave, campoAtual);
    },
    [types.UPDATE_READEQUACAO](state, readequacao) {
        state.readequacao = readequacao;
    },
    [types.EXCLUIR_READEQUACAO](state) {
        state.readequacao = {};
    },
    [types.GET_DOCUMENTO](state, data) {
        const readequacoesProponente = [];
        state.readequacoesProponente.items.forEach((item) => {
            const readequacao = item;
            if (item.idDocumento === data.idDocumento) {
                readequacao.documento = data.documento;
            }
            readequacoesProponente.push(readequacao);
        });
        state.readequacoesProponente.items = readequacoesProponente;
    },
    [types.ADICIONAR_DOCUMENTO](state, data) {
        state.readequacao.idDocumento = data.idDocumento;
        state.readequacao.nomeArquivo = data.nomeArquivo;
    },
    [types.EXCLUIR_DOCUMENTO](state) {
        state.readequacao.idDocumento = '';
        state.readequacao.nomeArquivo = '';
    },
    [types.UPDATE_READEQUACAO_DS_SOLICITACAO](state, dsSolicitacao) {
        state.readequacao.items.dsSolicitacao = dsSolicitacao;
    },
    [types.UPDATE_READEQUACAO_SALDO_APLICACAO_DS_SOLICITACAO](state, dsSolicitacao) {
        state.readequacao.saldoAplicacaoDsSolicitacao = dsSolicitacao;
    },
    [types.OBTER_DISPONIVEL_EDICAO_ITEM_SALDO_APLICACAO](state, disponivel) {
        state.readequacao.saldoAplicacaoDisponivelEdicaoItem = disponivel;
    },
    [types.SET_TIPOS_DISPONIVEIS](state, tiposDisponiveis) {
        state.tiposDisponiveis = tiposDisponiveis;
    },
    [types.GET_TIPOS_DISPONIVEIS](state, tiposDisponiveis) {
        state.tiposDisponiveis = tiposDisponiveis;
    },
    [types.SET_READEQUACOES_PROPONENTE](state, novaReadequacao) {
        state.readequacoesProponente.items.unshift(novaReadequacao);
    },
};
