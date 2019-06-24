import * as readequacaoHelperAPI from '@/helpers/api/Readequacao';
import * as types from './types';

export const obterListaDeReadequacoes = async ({ commit }, params) => {
    const resultado = await readequacaoHelperAPI.getReadequacoes(params)
        .then((response) => {
            const { data } = response.data;
            switch (params.stStatusAtual) {
            case 'proponente':
                commit(types.GET_READEQUACOES_PROPONENTE, data);
                break;
            case 'analise':
                commit(types.GET_READEQUACOES_ANALISE, data);
                break;
            case 'finalizadas':
                commit(types.GET_READEQUACOES_FINALIZADAS, data);
                break;
            default:
                break;
            }
            return data;
        });
    return resultado;
};

export const buscaReadequacaoPronacTipo = ({ commit }, params) => {
    readequacaoHelperAPI.buscaReadequacaoPronacTipo(params)
        .then((response) => {
            const { data } = response.data;
            let readequacao = {};
            if (data.items.length > 1) {
                readequacao = { items: data.data.items };
            } else {
                readequacao = { items: data.data.items[0] };
            }
            commit(types.SET_READEQUACAO, readequacao);
        });
};

export const obterDocumento = async ({ commit }, params) => {
    const resultado = await readequacaoHelperAPI.obterDocumentoReadequacao(params)
        .then((response) => {
            if (response.data) {
                const documento = response.data.data.items;
                commit(types.GET_DOCUMENTO, {
                    documento: documento.content,
                    idDocumento: documento.idDocumento,
                });
                commit(types.GET_READEQUACAO, {
                    documento: documento.content,
                    idDocumento: documento.idDocumento,
                });
            } else {
                commit(types.GET_DOCUMENTO, {});
            }
            return response.data;
        });
    return resultado;
};

export const adicionarDocumento = ({ commit }, params) => {
    readequacaoHelperAPI.adicionarDocumento(params)
        .then((response) => {
            const { documento } = response.data.documento;
            commit(types.ADICIONAR_DOCUMENTO, documento);
        });
};

export const excluirDocumento = ({ commit }, params) => {
    readequacaoHelperAPI.excluirDocumento(params)
        .then(() => {
            commit(types.EXCLUIR_DOCUMENTO);
        });
};

export const excluirReadequacao = ({ commit, dispatch }, params) => {
    readequacaoHelperAPI.excluirReadequacao(params)
        .then(() => {
            commit(types.EXCLUIR_READEQUACAO);
            dispatch('obterListaDeReadequacoes', {
                idPronac: params.idPronac,
                stStatusAtual: 'proponente',
            });
            dispatch('obterTiposDisponiveis', {
                idPronac: params.idPronac,
            });
        });
};

export const obterReadequacao = ({ commit }, data) => {
    if (typeof data.idReadequacao !== 'undefined') {
        commit(types.GET_READEQUACAO, data);
    }
};

export const updateReadequacao = async ({ commit }, params) => {
    const resultado = await readequacaoHelperAPI.updateReadequacao(params)
        .then((response) => {
            commit(types.UPDATE_READEQUACAO, response.data.data.items);
            commit(types.GET_READEQUACAO, response.data.data.items);
        });
    return resultado;
};

export const updateReadequacaoDsSolicitacao = ({ commit }, params) => {
    commit(types.UPDATE_READEQUACAO_DS_SOLICITACAO, params);
};

export const updateReadequacaoSaldoAplicacao = ({ commit }, params) => {
    readequacaoHelperAPI.updateReadequacaoSaldoAplicacao(params)
        .then((response) => {
            const { readequacao } = response.data;
            commit(types.UPDATE_READEQUACAO_SALDO_APLICACAO, readequacao);
        });
};

export const updateReadequacaoSaldoAplicacaoDsSolicitacao = ({ commit }, dsSolicitacao) => {
    commit(types.UPDATE_READEQUACAO_SALDO_APLICACAO_DS_SOLICITACAO, dsSolicitacao);
};

export const obterDisponivelEdicaoItemSaldoAplicacao = ({ commit }, params) => {
    readequacaoHelperAPI.obterDisponivelEdicaoItemSaldoAplicacao(params)
        .then((response) => {
            const { data } = response.data.disponivelParaEdicao;
            commit(types.OBTER_DISPONIVEL_EDICAO_ITEM_SALDO_APLICACAO, data);
        });
};

export const obterCampoAtual = async ({ commit }, params) => {
    const resultado = await readequacaoHelperAPI.obterCampoAtual(params)
        .then((response) => {
            const { data } = response.data;
            commit(types.SET_CAMPO_ATUAL, data.items[0]);
        });
    return resultado;
};

export const obterTiposDisponiveis = ({ commit }, params) => {
    readequacaoHelperAPI.obterTiposDisponiveis(params)
        .then((response) => {
            commit(types.SET_TIPOS_DISPONIVEIS, response.data.data.items);
        });
};

export const inserirReadequacao = async ({ commit, dispatch }, params) => {
    const resultado = await readequacaoHelperAPI.inserirReadequacao(params)
        .then((response) => {
            const { data } = response.data;
            commit(types.SET_READEQUACAO, data);
            commit(types.SET_READEQUACOES_PROPONENTE, data);
            dispatch('obterListaDeReadequacoes', {
                idPronac: params.idPronac,
                stStatusAtual: 'proponente',
            });
            dispatch('obterTiposDisponiveis', {
                idPronac: params.idPronac,
            });
            dispatch('obterCampoAtual', {
                idPronac: params.idPronac,
                idTipoReadequacao: params.idTipoReadequacao,
            });
            return data;
        });
    return resultado;
};

export const finalizarReadequacao = async ({ dispatch }, params) => {
    const resultado = await readequacaoHelperAPI.finalizarReadequacao(params)
        .then(() => {
            dispatch('obterListaDeReadequacoes', {
                idPronac: params.idPronac,
                stStatusAtual: 'proponente',
            });
            dispatch('obterListaDeReadequacoes', {
                idPronac: params.idPronac,
                stStatusAtual: 'analise',
            });
        });
    return resultado;
};
