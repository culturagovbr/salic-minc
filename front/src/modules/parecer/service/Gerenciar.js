import * as api from '@/helpers/api/base';

const MODULE = 'parecer';

export const obterProdutosParaGerenciar = (params) => {
    const controller = 'gerenciar-distribuir-produto-rest';
    const action = '/index';
    const filtro = `filtro=${params.filtro}`;
    const queryParams = `?${filtro}`;

    return api.getRequest(`/${MODULE}/${controller}${action}${queryParams}`);
};

export const salvarDistribuicaoProduto = (params) => {
    const controller = 'gerenciar-distribuir-produto-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};

export const salvarDistribuicaoProjeto = (params) => {
    const controller = 'gerenciar-distribuir-projeto-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};

export const salvarSolicitacaoReanalise = (params) => {
    const controller = 'gerenciar-reanalisar-produto-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};

export const salvarDevolucaoParaSecult = (params) => {
    const controller = 'gerenciar-devolver-produto-secult-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};

export const salvarValidacaoParecer = (params) => {
    const controller = 'gerenciar-avaliacao-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};

export const salvarSolicitacaoAnaliseComplementar = (params) => {
    const controller = 'gerenciar-analise-complementar-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};
