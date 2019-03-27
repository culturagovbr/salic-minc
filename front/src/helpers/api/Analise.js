import * as api from './base';

export const buscarAprovacao = (idPronac) => {
    const modulo = '/analise';
    const controller = '/aprovacao-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarRecurso = (idPronac) => {
    const modulo = '/analise';
    const controller = '/recurso-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};
