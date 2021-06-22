import * as execucaoHelperAPI from '@/helpers/api/Execucao';
import * as types from './types';

export const buscarMarcasAnexadas = ({ commit }, idPronac) => {
    execucaoHelperAPI.buscarMarcasAnexadas(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_MARCAS_ANEXADAS, data);
        });
};

export const buscarDadosReadequacoes = ({ commit }, idPronac) => {
    execucaoHelperAPI.buscarDadosReadequacoes(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DADOS_READEQUACOES, data);
        });
};

export const buscarPedidoProrrogacao = ({ commit }, idPronac) => {
    execucaoHelperAPI.buscarPedidoProrrogacao(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_PEDIDO_PRORROGACAO, data);
        });
};

export const buscarDadosFiscalizacaoLista = ({ commit }, idPronac) => {
    execucaoHelperAPI.buscarDadosFiscalizacaoLista(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DADOS_FISCALIZACAO_LISTA, data);
        });
};

export const buscarDadosFiscalizacaoVisualiza = ({ commit }, value) => {
    const { idPronac, idFiscalizacao } = value;
    execucaoHelperAPI.buscarDadosFiscalizacaoVisualiza(idPronac, idFiscalizacao)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DADOS_FISCALIZACAO_VISUALIZA, data);
        });
};
