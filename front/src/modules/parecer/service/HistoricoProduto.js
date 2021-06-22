import * as api from '@/helpers/api/base';

const MODULE = 'parecer';

export const obterHistoricoProduto = (params) => {
    const controller = 'historico-produto-rest';
    const idProduto = `idProduto/${params.idProduto}`;
    const idPronac = `idPronac/${params.idPronac}`;
    const stPrincipal = `stPrincipal/${params.stPrincipal}`;

    const queryParams = `/${idProduto}/${idPronac}/${stPrincipal}`;

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
};
