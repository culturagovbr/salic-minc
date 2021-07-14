import * as types from './types';

export const state = {
    pagamentosConsolidados: [],
    pagamentosUfMunicipio: [],
    execucaoReceitaDespesa: [],
    relatorioFisico: [],
    relacaoPagamento: [],
    relatorioCumprimentoObjeto: [],
};

export const mutations = {
    [types.SET_PAGAMENTOS_CONSOLIDADOS](state, dados) {
        state.pagamentosConsolidados = dados;
    },
    [types.SET_PAGAMENTOS_UF_MUNICIPIO](state, dados) {
        state.pagamentosUfMunicipio = dados;
    },
    [types.SET_EXECUCAO_RECEITA_DESPESA](state, dados) {
        state.execucaoReceitaDespesa = dados;
    },
    [types.SET_RELATORIO_FISICO](state, dados) {
        state.relatorioFisico = dados;
    },
    [types.SET_RELACAO_PAGAMENTO](state, dados) {
        state.relacaoPagamento = dados;
    },
    [types.SET_RELATORIO_CUMPRIMENTO_OBJETO](state, dados) {
        state.relatorioCumprimentoObjeto = dados;
    },
};
