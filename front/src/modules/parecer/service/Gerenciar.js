import * as api from '@/helpers/api/base';

const MODULE = 'parecer';

export const restaurarPlanilhaProduto = (params) => {
    const controller = 'planilha-produto-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};

export const obterProdutosParaGerenciar = (params) => {
    const controller = 'gerenciar-distribuir-produto-rest';
    const action = '/index';
    const filtro = `filtro=${params.filtro}`;
    const queryParams = `?${filtro}`;

    return api.getRequest(`/${MODULE}/${controller}${action}${queryParams}`);
};

export const buscarPareceristas = (params) => {
    const controller = 'parecerista-rest';
    return api.getRequest(`/${MODULE}/${controller}`, api.parseQueryParams(params));
};

export const buscarVinculadas = (params) => {
    const controller = 'vinculada-rest';
    const queryParams = api.parseQueryParams(params);

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
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
