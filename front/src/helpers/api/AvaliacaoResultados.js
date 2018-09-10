import * as api from './base';

const buildData = (params) => {
    const bodyFormData = new FormData();

    Object.keys(params).forEach((key) => {
        bodyFormData.append(key, params[key]);
    });

    return bodyFormData;
};

const path = '/navegacao/menu-principal';

export const dadosMenu = () => api.getRequest(path);

export const criarRegistro = params => api.postRequest(path, buildData(params));

export const atualizarRegistro = params => api.putRequest(path, buildData(params), params.Codigo);

export const removerRegistro = params => api.deleteRequest(path, params.Codigo);

export const parecerConsolidacao = params => api.getRequest("/avaliacao-resultados/emissao-parecer-rest/idPronac/" + params);

export const criarParecer = params => {

    const parametro = params['idPronac'];
    delete params['idPronac'];
    const data = params;
    console.info(data);

  return api.postRequest( "/avaliacao-resultados/emissao-parecer-rest/idPronac/"+parametro, data);
};

export const getTeste = params => api.postRequest("/realizarprestacaodecontas/carregar-destinatarios/", buildData(params));
