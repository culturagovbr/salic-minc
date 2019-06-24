import * as api from './base';

const buildData = (params) => {
    const bodyFormData = new FormData();

    Object.keys(params).forEach((key) => {
        bodyFormData.append(key, params[key]);
    });

    return bodyFormData;
};

export const obterProdutosParaAnalise = () => {
    const module = '/parecer';
    const controller = '/analise-inicial-rest';
    const action = '';

    return api.getRequest(`${module}${controller}${action}`);
};

export const obterProdutoParaAnalise = (params) => {
    const module = '/parecer';
    const controller = '/analise-inicial-rest';
    const id = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;

    const queryParams = `/${id}/${idPronac}`;

    return api.getRequest(`${module}${controller}${queryParams}`);
};

export const obterAnaliseConteudo = (params) => {
    const module = '/parecer';
    const controller = '/analise-inicial-conteudo-rest';
    const id = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;

    const queryParams = `/${id}/${idPronac}`;

    return api.getRequest(`${module}${controller}${queryParams}`);
};
export const salvarAnaliseConteudo = params => api.postRequest('/parecer/analise-inicial-conteudo-rest/', buildData(params));

export const obterPlanilhaParaAnalise = (params) => {
    const module = '/parecer';
    const controller = '/analise-inicial-custo-rest';
    const id = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;
    const stPrincipal = `stPrincipal/${params.stPrincipal}`;

    const queryParams = `/${id}/${idPronac}/${stPrincipal}`;

    return api.getRequest(`${module}${controller}${queryParams}`);
};

export const obterProdutosSecundarios = (params) => {
    const module = '/parecer';
    const controller = '/analise-inicial-outros-produtos-rest';
    const idProduto = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;

    const queryParams = `/${idProduto}/${idPronac}`;

    return api.getRequest(`${module}${controller}${queryParams}`);
};

export const salvarAvaliacaoItem = params => api.postRequest('/parecer/analise-inicial-custo-rest/', buildData(params));

export const obterAnaliseConsolidacao = (params) => {
    const module = '/parecer';
    const controller = '/analise-inicial-consolidacao-rest';
    const id = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;

    const queryParams = `/${id}/${idPronac}`;

    return api.getRequest(`${module}${controller}${queryParams}`);
};

export const salvarAnaliseConsolidacao = params => api.postRequest('/parecer/analise-inicial-consolidacao-rest/', buildData(params));

export const restaurarPlanilhaProduto = params => api.postRequest('/parecer/planilha-produto-rest', buildData(params));

export const finalizarAnalise = params => api.postRequest('/parecer/analise-inicial-finalizacao-rest/', buildData(params));

export const obterHistoricoProduto = (params) => {
    const module = '/parecer';
    const controller = '/analise-inicial-historico-rest';
    const idProduto = `idProduto/${params.idProduto}`;
    const idPronac = `idPronac/${params.idPronac}`;
    const stPrincipal = `stPrincipal/${params.stPrincipal}`;

    const queryParams = `/${idProduto}/${idPronac}/${stPrincipal}`;

    return api.getRequest(`${module}${controller}${queryParams}`);
};

export const salvarDeclaracaoImpedimento = params => api.postRequest('/parecer/analise-inicial-impedimento-rest', buildData(params));

export const buscarProdutosParaGerenciar = (params) => {
    const module = '/parecer';
    const controller = '/gerenciar-parecer-rest';
    const action = '/index';
    const filtro = `filtro=${params.filtro}`;
    const queryParams = `?${filtro}`;

    return api.getRequest(`${module}${controller}${action}${queryParams}`);
};

export const obterDadosParaDistribuicao = (params) => {
    const module = '/parecer';
    const controller = '/gerenciar-distribuir-produto-rest';
    const idProduto = `idProduto/${params.idProduto}`;
    const idPronac = `idPronac/${params.idPronac}`;
    const filtro = `filtro/${params.filtro}`;
    const queryParams = `/${idProduto}/${idPronac}/${filtro}`;

    return api.getRequest(`${module}${controller}${queryParams}`);
};

export const salvarDistribuicaoProduto = params => api.postRequest('/parecer/gerenciar-distribuir-produto-rest', buildData(params));

export const salvarDistribuicaoProjeto = params => api.postRequest('/parecer/gerenciar-distribuir-projeto-rest', buildData(params));
