import * as dadosBancariosHelperAPI from '@/helpers/api/DadosBancarios';
import * as types from './types';

export const buscarContasBancarias = ({ commit }, idPronac) => {
    dadosBancariosHelperAPI.buscarContasBancarias(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_CONTAS_BANCARIAS, data);
        });
};

export const buscarConciliacaoBancaria = async ({ commit }, params) => dadosBancariosHelperAPI
    .buscarConciliacaoBancaria(params)
    .then((response) => {
        const data = response.data.data.items;
        commit(types.SET_CONCILIACAO_BANCARIA, data);
    });

export const buscarInconsistenciaBancaria = ({ commit }, params) => {
    dadosBancariosHelperAPI.buscarInconsistenciaBancaria(params)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_INCONSISTENCIA_BANCARIA, data);
        });
};

export const buscarLiberacao = ({ commit }, idPronac) => {
    dadosBancariosHelperAPI.buscarLiberacao(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_LIBERACAO, data);
        });
};

export const buscarSaldoContas = ({ commit }, idPronac) => {
    dadosBancariosHelperAPI.buscarSaldoContas(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_SALDO_CONTAS, data);
        });
};

export const buscarExtratosBancarios = ({ commit }, params) => {
    dadosBancariosHelperAPI.buscarExtratosBancarios(params)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_EXTRATOS_BANCARIOS, data);
        });
};

export const buscarExtratosBancariosConsolidado = ({ commit }, idPronac) => {
    dadosBancariosHelperAPI.buscarExtratosBancariosConsolidado(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_EXTRATOS_BANCARIOS_CONSOLIDADO, data);
        });
};

export const buscarCaptacao = ({ commit }, params) => {
    dadosBancariosHelperAPI.buscarCaptacao(params)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_CAPTACAO, data);
        });
};

export const buscarDevolucoesIncentivador = ({ commit }, params) => {
    dadosBancariosHelperAPI.buscarDevolucoesIncentivador(params)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DEVOLUCOES_INCENTIVADOR, data);
        });
};
