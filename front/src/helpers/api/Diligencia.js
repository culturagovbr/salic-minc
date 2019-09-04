import * as api from './base';

const buildData = (params) => {
    const bodyFormData = new FormData();

    Object.keys(params).forEach((key) => {
        if (params[key] !== null) {
            bodyFormData.append(key, params[key]);
        }
    });

    return bodyFormData;
};

const parseQueryParams = (params) => {
    let queryParams = '';
    Object.keys(params).forEach((key) => {
        if (params[key] !== null) {
            queryParams += (queryParams === '') ? '?' : '&';
            queryParams += `${key}=${params[key]}`;
        }
    });
    return queryParams;
};

export const obterDiligencias = (params) => {
    const path = '/diligencia/diligencia-rest';
    return api.getRequest(path + parseQueryParams(params));
};

export const obterDiligenciasProduto = (params) => {
    const path = '/diligencia/diligencia-rest';
    return api.getRequest(path + parseQueryParams(params));
};

export const obterDiligencia = (params) => {
    const path = '/diligencia/diligencia-rest';
    return api.getRequest(path + parseQueryParams(params));
};

export const salvarDiligencia = params => api.postRequest('/diligencia/diligencia-rest', buildData(params));
