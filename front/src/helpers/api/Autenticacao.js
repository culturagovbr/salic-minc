import * as api from './base';

const buildData = (params) => {
    const bodyFormData = new FormData();

    Object.keys(params).forEach((key) => {
        bodyFormData.append(key, params[key]);
    });

    return bodyFormData;
};

export const usuarioLogado = () => api.getRequest('/autenticacao/usuario/usuario/logado');
export const login = usuario => api.postRequest('/autenticacao/index/login2', buildData(usuario));
