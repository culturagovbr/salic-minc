import * as api from './base';

export const buscarDadosLayout = () => {
    const module = '/navegacao';
    const controller = '/dados-rest';
    const action = '/index';

    return api.getRequest(`${module}${controller}${action}`);
};

export const buscarPerfisDisponiveis = () => api.getRequest('/navegacao/perfil-rest/index');

export const buscarDadosMenu = () => api.getRequest('/navegacao/menu-principal');
