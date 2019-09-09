import * as api from '@/helpers/api/base';

const MODULE = 'parecer';

export const obterProdutosParaAnalise = () => {
    const controller = 'analise-inicial-rest';
    return api.getRequest(`/${MODULE}/${controller}`);
};

export const obterProdutoParaAnalise = (params) => {
    const controller = 'analise-inicial-rest';
    const id = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;

    const queryParams = `/${id}/${idPronac}`;

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
};

export const obterAnaliseConteudo = (params) => {
    const controller = 'analise-inicial-conteudo-rest';
    const id = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;

    const queryParams = `/${id}/${idPronac}`;

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
};
export const salvarAnaliseConteudo = (params) => {
    const controller = 'analise-inicial-conteudo-rest';
    return api.postRequest(`/${MODULE}/${controller}/`, api.buildData(params));
};

export const obterPlanilhaParaAnalise = (params) => {
    const controller = 'analise-inicial-custo-rest';
    const id = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;
    const stPrincipal = `stPrincipal/${params.stPrincipal}`;

    const queryParams = `/${id}/${idPronac}/${stPrincipal}`;

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
};

export const obterProdutosSecundarios = (params) => {
    const controller = 'analise-inicial-outros-produtos-rest';
    const idProduto = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;

    const queryParams = `/${idProduto}/${idPronac}`;

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
};

export const salvarAvaliacaoItem = (params) => {
    const controller = 'analise-inicial-custo-rest';
    return api.postRequest(`/${MODULE}/${controller}/`, api.buildData(params));
};

export const obterAnaliseConsolidacao = (params) => {
    const controller = 'analise-inicial-consolidacao-rest';
    const id = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;

    const queryParams = `/${id}/${idPronac}`;

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
};

export const salvarAnaliseConsolidacao = (params) => {
    const controller = 'analise-inicial-consolidacao-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};

export const finalizarAnalise = (params) => {
    const controller = 'analise-inicial-finalizacao-rest';
    return api.postRequest(`/${MODULE}/${controller}/`, api.buildData(params));
};

export const obterHistoricoProduto = (params) => {
    const controller = 'analise-inicial-historico-rest';
    const idProduto = `idProduto/${params.idProduto}`;
    const idPronac = `idPronac/${params.idPronac}`;
    const stPrincipal = `stPrincipal/${params.stPrincipal}`;

    const queryParams = `/${idProduto}/${idPronac}/${stPrincipal}`;

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
};

export const salvarDeclaracaoImpedimento = (params) => {
    const controller = 'analise-inicial-impedimento-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};

export const restaurarPlanilhaProduto = (params) => {
    const controller = 'planilha-produto-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};
