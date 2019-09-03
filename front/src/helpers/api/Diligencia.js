import * as api from './base';

const buildData = (params) => {
    const bodyFormData = new FormData();

    Object.keys(params).forEach((key) => {
        bodyFormData.append(key, params[key]);
    });

    return bodyFormData;
};

export const obterDiligencias = (params) => {
    const module = '/diligencia';
    const controller = '/diligencia-rest';
    const action = 'index';
    const idPronac = `idPronac=${params.idPronac}`;
    const situacao = `situacao=${params.situacao}`;
    const tpDiligencia = `tpDiligencia=${params.tpDiligencia}`;
    const queryParams = `?${idPronac}&${situacao}&${tpDiligencia}`;
    return api.getRequest(`${module}${controller}${action}${queryParams}`);
};

export const obterDiligenciasProduto = (params) => {
    const module = '/diligencia';
    const controller = '/diligencia-rest';
    const action = '/index';
    const idPronac = `idPronac=${params.idPronac}`;
    const idProduto = `idProduto=${params.idProduto}`;
    const situacao = `situacao=${params.situacao}`;
    const tpDiligencia = `tpDiligencia=${params.tpDiligencia}`;
    const queryParams = `?${idPronac}&${idProduto}&${situacao}&${tpDiligencia}`;
    return api.getRequest(`${module}${controller}${action}${queryParams}`);
};

export const obterDiligencia = (params) => {
    const module = '/diligencia';
    const controller = '/diligencia-rest';
    const idPronac = `idPronac/${params.idPronac}`;
    const idDiligencia = `idDiligencia/${params.idDiligencia}`;
    const queryParams = `/${idPronac}/${idDiligencia}`;
    return api.getRequest(`${module}${controller}${queryParams}`);
};

export const salvarDiligencia = params => api.postRequest('/diligencia/diligencia-rest', buildData(params));
