import * as parecerHelperAPI from '@/helpers/api/Parecer';
import * as types from './types';

export const parecerMensagemSucesso = ({ commit }, msg) => {
    commit('noticias/SET_DADOS',
        {
            ativo: true,
            color: 'success',
            text: msg,
        },
        { root: true });
};

export const parecerMensagemErro = ({ commit }, msg) => {
    commit('noticias/SET_DADOS',
        {
            ativo: true,
            color: 'error',
            text: msg,
        },
        { root: true });
};

export const obterProdutosParaAnalise = ({ commit }) => {
    parecerHelperAPI.obterProdutosParaAnalise()
        .then((response) => {
            commit(types.SET_PRODUTOS, response.data.items);
            return response.data;
        });
};

export const removerProdutoParaAnalise = ({ commit }, params) => {
    commit(types.REMOVE_PRODUTO_DA_LISTA, params);
};

export const obterProdutoParaAnalise = ({ commit, dispatch }, params) => {
    commit(types.SET_PRODUTO, {});
    parecerHelperAPI.obterProdutoParaAnalise(params)
        .then((response) => {
            commit(types.SET_PRODUTO, response.data.data);
        }).catch((e) => {
            dispatch('parecerMensagemErro', e.response.data.error.message);
            throw new TypeError(e.response.data.error.message, 'obterProdutoParaAnalise', 10);
        });
};

export const obterAnaLiseConteudo = ({ commit }, params) => {
    commit(types.SET_ANALISE_CONTEUDO, {});
    parecerHelperAPI.obterAnaliseConteudo(params)
        .then((response) => {
            commit(types.SET_ANALISE_CONTEUDO, response.data.data);
        });
};

export const salvarAnaLiseConteudo = async ({ commit, dispatch }, avaliacao) => parecerHelperAPI.salvarAnaliseConteudo(avaliacao)
    .then((response) => {
        commit(types.SET_ANALISE_CONTEUDO, avaliacao);
        dispatch('parecerMensagemSucesso', 'Salvo com sucesso!');
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.message);
        throw new TypeError(e.response.data.message, 'salvarAnaliseConteudo', 10);
    });

export const obterPlanilhaParaAnalise = ({ commit, dispatch }, params) => {
    commit(types.SET_PLANILHA_PARECER, []);
    parecerHelperAPI.obterPlanilhaParaAnalise(params)
        .then((response) => {
            commit(types.SET_PLANILHA_PARECER, response.data.items);
        }).catch((e) => {
            dispatch('parecerMensagemErro', e.response.data.error.message);
            throw new TypeError(e.response.data.error.message, 'obterPlanilhaParaAnalise', 10);
        });
};

export const obterProdutosSecundarios = ({ commit }, params) => {
    commit(types.SET_PRODUTOS_SECUNDARIOS, []);
    parecerHelperAPI.obterProdutosSecundarios(params)
        .then((response) => {
            commit(types.SET_PRODUTOS_SECUNDARIOS, response.data.data);
        });
};

export const salvarAvaliacaoItem = async ({ dispatch }, avaliacao) => parecerHelperAPI.salvarAvaliacaoItem(avaliacao)
    .then((response) => {
        dispatch('atualizarVariosItensPlanilha', response.data.data.items);
        dispatch('parecerMensagemSucesso', response.data.message);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.message);
        throw new TypeError(e.response.data.message, 'salvarAnaliseItem', 10);
    });

export const atualizarVariosItensPlanilha = ({ commit }, data) => {
    data.forEach((item) => {
        commit(types.UPDATE_ITEM_PLANILHA, item);
    });
};

export const obterAnaliseConteudoSecundario = ({ commit }, params) => {
    commit(types.SET_ANALISE_CONTEUDO_SECUNDARIO, {});
    parecerHelperAPI.obterAnaliseConteudo(params)
        .then((response) => {
            commit(types.SET_ANALISE_CONTEUDO_SECUNDARIO, response.data.data);
        });
};

export const obterPlanilhaProdutoSecundario = ({ commit }, params) => {
    commit(types.SET_PLANILHA_PRODUTO_SECUNDARIO, []);
    parecerHelperAPI.obterPlanilhaParaAnalise(params)
        .then((response) => {
            commit(types.SET_PLANILHA_PRODUTO_SECUNDARIO, response.data.items);
        });
};

export const obterConsolidacao = ({ commit }, params) => {
    parecerHelperAPI.obterAnaliseConsolidacao(params)
        .then((response) => {
            commit(types.SET_CONSOLIDACAO, response.data.data);
        });
};

export const salvarAnaliseConsolidacao = async ({ commit, dispatch }, avaliacao) => parecerHelperAPI.salvarAnaliseConsolidacao(avaliacao)
    .then((response) => {
        commit(types.SET_CONSOLIDACAO, avaliacao);
        dispatch('parecerMensagemSucesso', 'Salvo com sucesso');
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.message);
        throw new TypeError(e.response.data.message, 'salvarAnaliseConsolidacao', 10);
    });

export const restaurarPlanilhaProduto = async ({ dispatch }, params) => parecerHelperAPI.restaurarPlanilhaProduto(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.message);
        throw new TypeError(e.response.data.message, 'restaurarPlanilha', 10);
    });

export const finalizarAnalise = async ({ dispatch }, data) => parecerHelperAPI.finalizarAnalise(data)
    .then((response) => {
        dispatch('parecerMensagemSucesso', 'Salvo com sucesso!');
        dispatch('obterProdutoParaAnalise', {
            id: data.idProduto,
            idPronac: data.idPronac,
        });
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.message);
        throw new TypeError(e.response.data.message, 'finalizarAnalise', 10);
    });

export const salvarItensSelecionados = ({ commit }, data) => {
    data.forEach((item) => {
        const novoItem = Object.assign({}, item, { selecionado: true });
        commit(types.UPDATE_ITEM_PLANILHA, novoItem);
    });
};

export const removerItensSelecionados = ({ commit }, data) => {
    data.forEach((item) => {
        const novoItem = Object.assign({}, item, { selecionado: false });
        commit(types.UPDATE_ITEM_PLANILHA, novoItem);
    });
};

export const obterHistoricoProduto = ({ commit }, params) => {
    commit(types.SET_HISTORICO_PRODUTO, []);
    parecerHelperAPI.obterHistoricoProduto(params)
        .then((response) => {
            commit(types.SET_HISTORICO_PRODUTO, response.data.items);
        });
};

export const salvarDeclaracaoImpedimento = async ({ dispatch }, params) => parecerHelperAPI.salvarDeclaracaoImpedimento(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        dispatch('removerProdutoParaAnalise', params);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.error.message);
        throw new TypeError(e.response.data.error.message, 'declaracaoImpedimento', 10);
    });
