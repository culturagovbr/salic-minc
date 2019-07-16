import Vue from 'vue';
import * as types from './types';

export const state = {
    avaliacaoReadequacao: {},
    campoAtual: {},
    documentoAssinaturaReadequacoes: {},
    linkAssinatura: {},
    readequacoesProponente: {},
    readequacoesAnalise: {},
    readequacoesFinalizadas: {},
    readequacoesPainelTecnico: {},
    readequacoesPainelAguardandoDistribuicao: {},
    readequacoesPainelEmAnalise: {},
    readequacoesPainelAnalisados: {},
    readequacoesPainelAguardandoPublicacao: {},
    readequacao: {},
    saldoAplicacao: {},
    saldoAplicacaoDisponivelEdicaoItem: {},
    tiposDisponiveis: [],
    planilha: {},
    planilhaAtiva: {},
    unidadesPlanilha: {},
    itemPlanilhaEdicao: {},
    resumoPlanilha: {},
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
    [types.SET_READEQUACOES_PAINEL_TECNICO](state, data) {
        state.readequacoesPainelTecnico = data;
    },
    [types.GET_READEQUACOES_PAINEL_TECNICO](state, data) {
        state.readequacoesPainelTecnico = data;
    },
    [types.SET_READEQUACOES_PAINEL_AGUARDANDO_DISTRIBUICAO](state, data) {
        state.readequacoesPainelAguardandoDistribuicao = data;
    },
    [types.GET_READEQUACOES_PAINEL_AGUARDANDO_DISTRIBUICAO](state, data) {
        state.readequacoesPainelAguardandoDistribuicao = data;
    },
    [types.SET_READEQUACOES_PAINEL_EM_ANALISE](state, data) {
        state.readequacoesPainelEmAnalise = data;
    },
    [types.GET_READEQUACOES_PAINEL_EM_ANALISE](state, data) {
        state.readequacoesPainelEmAnalise = data;
    },
    [types.SET_READEQUACOES_PAINEL_ANALISADOS](state, data) {
        state.readequacoesPainelAnalisados = data;
    },
    [types.GET_READEQUACOES_PAINEL_ANALISADOS](state, data) {
        state.readequacoesPainelAnalisados = data;
    },
    [types.SET_READEQUACOES_PAINEL_AGUARDANDO_PUBLICACAO](state, data) {
        state.readequacoesPainelAguardandoPublicacao = data;
    },
    [types.GET_READEQUACOES_PAINEL_AGUARDANDO_PUBLICACAO](state, data) {
        state.readequacoesPainelAguardandoPublicacao = data;
    },
    [types.SET_READEQUACAO](state, readequacao) {
        state.readequacao = readequacao;
    },
    [types.GET_READEQUACAO](state, readequacao) {
        state.readequacao = readequacao;
    },
    [types.SET_PLANILHA](state, planilha) {
        state.planilha = planilha;
    },
    [types.GET_PLANILHA](state, planilha) {
        state.planilha = planilha;
    },
    [types.SET_PLANILHA_ATIVA](state, data) {
        state.planilhaAtiva = data;
    },
    [types.GET_PLANILHA_ATIVA](state, data) {
        state.planilhaAtiva = data;
    },
    [types.GET_UNIDADES_PLANILHA](state, data) {
        state.unidadesPlanilha = data;
    },
    [types.SET_UNIDADES_PLANILHA](state, data) {
        state.unidadesPlanilha = data;
    },
    [types.SET_ITEM_PLANILHA_EDICAO](state, data) {
        state.itemPlanilhaEdicao = data;
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
    [types.SET_TIPOS_DISPONIVEIS](state, tiposDisponiveis) {
        state.tiposDisponiveis = tiposDisponiveis;
    },
    [types.GET_TIPOS_DISPONIVEIS](state, tiposDisponiveis) {
        state.tiposDisponiveis = tiposDisponiveis;
    },
    [types.SET_READEQUACOES_PROPONENTE](state, novaReadequacao) {
        state.readequacoesProponente.items.unshift(novaReadequacao);
    },
    [types.GET_AVALIACAO_READEQUACAO](state, data) {
        state.avaliacaoReadequacao = data;
    },
    [types.SET_AVALIACAO_READEQUACAO](state, data) {
        state.avaliacaoReadequacao = data;
    },
    [types.SET_DOCUMENTO_ASSINATURA](state, data) {
        state.documentoAssinatura = data;
    },
    [types.GET_DOCUMENTO_ASSINATURA](state, data) {
        state.documentoAssinatura = data;
    },
    [types.SET_DOCUMENTO_ASSINATURA_READEQUACOES](state, data) {
        state.documentoAssinatura = data;
    },
    [types.GET_DOCUMENTO_ASSINATURA_READEQUACOES](state, data) {
        state.documentoAssinatura = data;
    },
    [types.SET_RESUMO_PLANILHA](state, data) {
        state.resumoPlanilha = data;
    },
    [types.GET_RESUMO_PLANILHA](state, data) {
        state.resumoPlanilha = data;
    },
};
