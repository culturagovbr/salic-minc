import * as api from './base';

export const obterUnidadesPlanilha = () => {
    const module = '/planilha';
    const controller = '/planilha-unidade-rest';
    const action = '';

    return api.getRequest(`${module}${controller}${action}`);
};

export const obterMediana = (params) => {
    const module = '/planilha';
    const controller = '/mediana-item-orcamentario-rest';
    const idProduto = `idProduto/${params.idProduto}`;
    const idUnidade = `idUnidade/${params.idUnidade}`;
    const idPlanilhaItem = `idPlanilhaItem/${params.idPlanilhaItem}`;
    const idUfDespesa = `idUfDespesa/${params.idUfDespesa}`;
    const idMunicipioDespesa = `idMunicipioDespesa/${params.idMunicipioDespesa}`;
    const queryParams = `/${idProduto}/${idUnidade}/${idPlanilhaItem}/${idUfDespesa}/${idMunicipioDespesa}`;

    return api.getRequest(`${module}${controller}${queryParams}`);
};
