import * as AnaliseInicialService from '../service/AnaliseInicial';
import * as GerenciarService from '../service/Gerenciar';
import * as PareceristaService from '../service/Parecerista';
import * as VinculadaService from '../service/Vinculada';
import * as PlanilhaService from '../service/Planilha';
import * as HistoricoProdutoService from '../service/HistoricoProduto';
import * as ProdutoService from '../service/Produto';

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

/** Análise inicial */

export const obterProdutosParaAnalise = ({ commit }) => {
    AnaliseInicialService.obterProdutosParaAnalise()
        .then((response) => {
            commit(types.SET_PRODUTOS, response.data.items);
            return response.data;
        });
};

export const removerProdutoDaLista = ({ commit }, params) => {
    commit(types.REMOVE_PRODUTO_DA_LISTA, params);
};

export const obterProdutoParaAnalise = ({ commit, dispatch }, params) => {
    commit(types.SET_PRODUTO, {});
    AnaliseInicialService.obterProdutoParaAnalise(params)
        .then((response) => {
            commit(types.SET_PRODUTO, response.data.data);
        }).catch((e) => {
            dispatch('parecerMensagemErro', e.response.data.error.message);
            throw new TypeError(e.response.data.error.message, 'obterProdutoParaAnalise', 10);
        });
};

export const obterAnaLiseConteudo = ({ commit }, params) => {
    commit(types.SET_ANALISE_CONTEUDO, {});
    AnaliseInicialService.obterAnaliseConteudo(params)
        .then((response) => {
            commit(types.SET_ANALISE_CONTEUDO, response.data.data);
        });
};

export const salvarAnaLiseConteudo = async ({ commit, dispatch }, avaliacao) => AnaliseInicialService
    .salvarAnaliseConteudo(avaliacao)
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
    AnaliseInicialService.obterPlanilhaParaAnalise(params)
        .then((response) => {
            commit(types.SET_PLANILHA_PARECER, response.data.items);
        }).catch((e) => {
            dispatch('parecerMensagemErro', e.response.data.error.message);
            throw new TypeError(e.response.data.error.message, 'obterPlanilhaParaAnalise', 10);
        });
};

export const obterProdutosSecundarios = ({ commit }, params) => {
    commit(types.SET_PRODUTOS_SECUNDARIOS, []);
    ProdutoService.obterProdutosSecundarios(params)
        .then((response) => {
            commit(types.SET_PRODUTOS_SECUNDARIOS, response.data.items);
        });
};

export const salvarAvaliacaoItem = async ({ dispatch }, avaliacao) => AnaliseInicialService
    .salvarAvaliacaoItem(avaliacao)
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
    AnaliseInicialService.obterAnaliseConteudo(params)
        .then((response) => {
            commit(types.SET_ANALISE_CONTEUDO_SECUNDARIO, response.data.data);
        });
};

export const obterPlanilhaProdutoSecundario = ({ commit }, params) => {
    commit(types.SET_PLANILHA_PRODUTO_SECUNDARIO, []);
    AnaliseInicialService.obterPlanilhaParaAnalise(params)
        .then((response) => {
            commit(types.SET_PLANILHA_PRODUTO_SECUNDARIO, response.data.items);
        });
};

export const obterConsolidacao = ({ commit }, params) => {
    AnaliseInicialService.obterAnaliseConsolidacao(params)
        .then((response) => {
            commit(types.SET_CONSOLIDACAO, response.data.data);
        });
};

export const salvarAnaliseConsolidacao = async ({ commit, dispatch }, avaliacao) => AnaliseInicialService
    .salvarAnaliseConsolidacao(avaliacao)
    .then((response) => {
        commit(types.SET_CONSOLIDACAO, avaliacao);
        dispatch('parecerMensagemSucesso', 'Salvo com sucesso');
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.message);
        throw new TypeError(e.response.data.message, 'salvarAnaliseConsolidacao', 10);
    });

export const restaurarPlanilhaProduto = async ({ dispatch }, params) => PlanilhaService
    .restaurarPlanilhaProduto(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.message);
        throw new TypeError(e.response.data.message, 'restaurarPlanilha', 10);
    });

export const finalizarAnalise = async ({ dispatch }, data) => AnaliseInicialService
    .finalizarAnalise(data)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
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
    HistoricoProdutoService.obterHistoricoProduto(params)
        .then((response) => {
            commit(types.SET_HISTORICO_PRODUTO, response.data.items);
        });
};

export const salvarDeclaracaoImpedimento = async ({ dispatch }, params) => AnaliseInicialService
    .salvarDeclaracaoImpedimento(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        dispatch('removerProdutoDaLista', params);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.error.message);
        throw new TypeError(e.response.data.error.message, 'declaracaoImpedimento', 10);
    });

/** Gerenciar Parecer */

export const obterProdutosParaGerenciar = async ({ commit }, params) => GerenciarService
    .obterProdutosParaGerenciar(params)
    .then((response) => {
        commit(types.SET_PRODUTOS, response.data.items);
        return response.data;
    });

export const buscarPareceristas = ({ commit }, params) => {
    PareceristaService.buscarPareceristas(params)
        .then((response) => {
            const { items } = response.data;
            commit(types.SET_PARECERISTAS, items);
            return items;
        });
};

export const buscarVinculadas = ({ commit }, params) => {
    VinculadaService.buscarVinculadas(params)
        .then((response) => {
            const { items } = response.data;
            commit(types.SET_VINCULADAS, items);
            return items;
        });
};

export const salvarDistribuicaoProduto = async ({ dispatch }, params) => GerenciarService
    .salvarDistribuicaoProduto(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        dispatch('removerProdutoDaLista', params);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.error.message);
        throw new TypeError(e.response.data.error.message, 'salvarDistribuicaoProduto', 10);
    });

export const salvarSolicitacaoReanalise = async ({ dispatch }, params) => GerenciarService
    .salvarSolicitacaoReanalise(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        dispatch('removerProdutoDaLista', params);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.error.message);
        throw new TypeError(e.response.data.error.message, 'salvarSolicitacaoReanalise', 10);
    });

export const salvarDevolucaoParaSecult = async ({ dispatch }, params) => GerenciarService
    .salvarDevolucaoParaSecult(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        dispatch('removerProdutoDaLista', params);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.error.message);
        throw new TypeError(e.response.data.error.message, 'salvarDevoluacaoSecult', 10);
    });


export const salvarDistribuicaoProjeto = async ({ dispatch }, params) => GerenciarService
    .salvarDistribuicaoProjeto(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        response.data.items.forEach((item) => {
            dispatch('removerProdutoDaLista', item);
        });
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.error.message);
        throw new TypeError(e.response.data.error.message, 'salvarDistribuicaoProjeto', 10);
    });

export const salvarValidacaoParecer = async ({ dispatch }, params) => GerenciarService
    .salvarValidacaoParecer(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        dispatch('removerProdutoDaLista', params);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.error.message);
        throw new TypeError(e.response.data.error.message, 'salvarValidacaoProduto', 10);
    });

export const salvarSolicitacaoAnaliseComplementar = async ({ dispatch }, params) => GerenciarService
    .salvarSolicitacaoAnaliseComplementar(params)
    .then((response) => {
        dispatch('parecerMensagemSucesso', response.data.message);
        dispatch('removerProdutoDaLista', params);
        return response.data;
    }).catch((e) => {
        dispatch('parecerMensagemErro', e.response.data.error.message);
        throw new TypeError(e.response.data.error.message, 'salvarValidacaoProduto', 10);
    });
