import * as api from './base';

export const buscarComunicados = () => {
    const module = '/navegacao';
    const controller = '/comunicados-rest';
    const action = '/index';

    return api.getRequest(`${module}${controller}${action}`);
};
