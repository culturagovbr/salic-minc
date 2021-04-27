import * as api from './base';

export const buscarMarcasAnexadas = (idPronac) => {
    const modulo = '/execucao';
    const controller = '/marcas-anexadas-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarDadosReadequacoes = (idPronac) => {
    const modulo = '/execucao';
    const controller = '/dados-readequacoes-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarPedidoProrrogacao = (idPronac) => {
    const modulo = '/execucao';
    const controller = '/pedido-prorrogacao-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarDadosFiscalizacaoLista = (idPronac) => {
    const modulo = '/execucao';
    const controller = '/fiscalizacao-rest';
    const path = `${modulo}${controller}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarDadosFiscalizacaoVisualiza = (idPronac, idFiscalizacao) => {
    const modulo = '/execucao';
    const controller = '/fiscalizacao-rest';
    const path = `${modulo}${controller}`;
    const queryParams = `?id=${idFiscalizacao}&idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};
