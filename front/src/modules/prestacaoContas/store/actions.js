import * as prestacaoContasHelperAPI from '@/helpers/api/PrestacaoContas';
import * as types from './types';

export const buscarPagamentosConsolidados = ({ commit }, idPronac) => {
    prestacaoContasHelperAPI.buscarPagamentosConsolidados(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_PAGAMENTOS_CONSOLIDADOS, data);
        });
};

export const buscarPagamentosUfMunicipio = ({ commit }, idPronac) => {
    prestacaoContasHelperAPI.buscarPagamentosUfMunicipio(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_PAGAMENTOS_UF_MUNICIPIO, data);
        });
};

export const buscarExecucaoReceitaDespesa = ({ commit }, idPronac) => {
    prestacaoContasHelperAPI.buscarExecucaoReceitaDespesa(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_EXECUCAO_RECEITA_DESPESA, data);
        });
};

export const buscarRelatorioFisico = ({ commit }, idPronac) => {
    prestacaoContasHelperAPI.buscarRelatorioFisico(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_RELATORIO_FISICO, data);
        });
};

export const buscarRelacaoPagamento = async ({ commit }, idPronac) => prestacaoContasHelperAPI
    .buscarRelacaoPagamento(idPronac)
    .then((response) => {
        const data = response.data.data.items;
        commit(types.SET_RELACAO_PAGAMENTO, data);
    });

export const buscarRelatorioCumprimentoObjeto = ({ commit }, idPronac) => {
    prestacaoContasHelperAPI.buscarRelatorioCumprimentoObjeto(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_RELATORIO_CUMPRIMENTO_OBJETO, data);
        });
};
