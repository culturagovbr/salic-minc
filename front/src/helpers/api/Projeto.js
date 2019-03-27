import * as api from './base';

export const buscaProjeto = (idPronac) => {
    const module = '/projeto';
    const controller = '/projeto';
    const action = '/get';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(`${module}${controller}${action}`, queryParams);
};

export const buscarProjetoCompleto = (idPronac) => {
    const module = '/projeto';
    const controller = '/dados-projeto';
    const action = '/get';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(`${module}${controller}${action}`, queryParams);
};

export const buscarTransferenciaRecursos = (idPronac, acao) => {
    const module = '/readequacao';
    const controller = '/transferencia-recursos-rest';
    const action = '/index';
    const queryParams = `?idPronac=${idPronac}&acao=${acao}`;
    return api.getRequest(`${module}${controller}${action}`, queryParams);
};

export const buscaProponente = (idPronac) => {
    const path = '/projeto/proponente-rest';
    const queryParams = `/idPronac/${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscaPlanilhaHomologada = (idPronac) => {
    const path = '/projeto/orcamento/obter-planilha-homologada-ajax';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscaPlanilhaOriginal = (idPronac) => {
    const path = '/projeto/orcamento/obter-planilha-proposta-original-ajax/';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscaPlanilhaReadequada = (idPronac) => {
    const path = '/projeto/orcamento/obter-planilha-readequada-ajax/';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscaPlanilhaAutorizada = (idPronac) => {
    const path = '/projeto/orcamento/obter-planilha-proposta-original-ajax/';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscaPlanilhaAdequada = (idPronac) => {
    const path = '/projeto/orcamento/obter-planilha-proposta-adequada-ajax/';
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};
