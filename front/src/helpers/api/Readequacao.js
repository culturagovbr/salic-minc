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

export const solicitarUsoSaldo = (params) => {
    const path = `/readequacao/solicitar-saldo/${params.idPronac}`;
    return api.getRequest(path);
};

export const obterPlanilha = (params) => {
    const path = '/readequacao/obter-planilha';
    return api.getRequest(path + parseQueryParams(params));
};

export const obterUnidadesPlanilha = (params) => {
    const path = '/readequacao/obter-unidades-planilha';
    return api.getRequest(path + parseQueryParams(params));
};

export const atualizarItemPlanilha = (params) => {
    const path = '/readequacao/item-planilha';
    return api.postRequest(path, buildData(params), params.idReadequacao);
};

export const calcularResumoPlanilha = (params) => {
    const path = '/readequacao/calcular-resumo-planilha';
    return api.getRequest(path + parseQueryParams(params));
};

export const reverterAlteracaoItem = (params) => {
    const path = '/readequacao/reverter-alteracao-item';
    return api.postRequest(path, buildData(params));
};
