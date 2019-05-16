import * as api from './base';

const buildData = (params) => {
    const bodyFormData = new FormData();

    Object.keys(params).forEach((key) => {
        bodyFormData.append(key, params[key]);
    });

    return bodyFormData;
};

const parseQueryParams = (params) => {
    let queryParams = '';
    Object.keys(params).forEach((key) => {
        queryParams += (queryParams === '') ? '?' : '&';
        queryParams += `${key}=${params[key]}`;
    });
    return queryParams;
};

export const getReadequacoes = (params) => {
    const path = '/readequacao';
    return api.getRequest(path + parseQueryParams(params));
};

export const dadosReadequacao = (params) => {
    const { idReadequacao } = params;
    const path = `/readequacao/dados-readequacao/${idReadequacao}`;
    return api.getRequest(path);
};

export const inserirReadequacao = (params) => {
    const path = '/readequacao/dados-readequacao';
    return api.postRequest(path, buildData(params));
};

export const buscaReadequacaoPronacTipo = (params) => {
    const path = '/readequacao';
    return api.getRequest(path + parseQueryParams(params));
};

export const updateReadequacao = (params) => {
    const path = '/readequacao/dados-readequacao';
    return api.postRequest(path, buildData(params), params.idReadequacao);
};

export const excluirReadequacao = (params) => {
    const path = '/readequacao/dados-readequacao';
    return api.deleteRequest(path, params.idReadequacao);
};

export const updateReadequacaoSaldoAplicacao = (params) => {
    const path = '/readequacao/saldo-aplicacao/salvar-readequacao';
    return api.putRequest(path, buildData(params), params.idReadequacao);
};

export const excluirReadequacaoSaldoAplicacao = (params) => {
    const path = '/readequacao/saldo-aplicacao/excluir-readequacao';
    return api.postRequest(path, buildData(params));
};

export const obterDisponivelEdicaoItemSaldoAplicacao = (idPronac) => {
    const path = `/readequacao/saldo-disponivel-edicao-item/${idPronac}`;
    return api.getRequest(path);
};

export const obterDocumentoReadequacao = (params) => {
    const path = '/readequacao/documento';
    return api.getRequest(path + parseQueryParams(params));
};

export const adicionarDocumento = (params) => {
    const path = '/readequacao/dados-readequacao';
    return api.postRequest(path + parseQueryParams(params));
};

export const excluirDocumento = (params) => {
    const path = `/readequacao/${params.idReadequacao}/documento/${params.idDocumento}`;
    return api.deleteRequest(path);
};

export const obterTiposReadequacao = (params) => {
    const path = '/readequacao/tipos-disponiveis';
    return api.getRequest(path + parseQueryParams(params));
};

export const obterCampoAtual = (params) => {
    const path = '/readequacao/campo-atual';
    return api.getRequest(path + parseQueryParams(params));
};

export const obterTiposDisponiveis = (params) => {
    const path = '/readequacao/tipos-disponiveis';
    return api.getRequest(path + parseQueryParams(params));
};

export const finalizarReadequacao = (params) => {
    const path = '/readequacao/finalizar';
    return api.postRequest(path, buildData(params));
};
