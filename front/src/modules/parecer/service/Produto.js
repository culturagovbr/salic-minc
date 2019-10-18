import * as api from '@/helpers/api/base';

const MODULE = 'parecer';

export const obterProdutosSecundarios = (params) => {
    const controller = 'analise-inicial-outros-produtos-rest';
    const idProduto = `id/${params.id}`;
    const idPronac = `idPronac/${params.idPronac}`;

    const queryParams = `/${idProduto}/${idPronac}`;

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
};
