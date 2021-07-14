import * as api from '@/helpers/api/base';

const MODULE = 'parecer';

export const buscarPareceristas = (params) => {
    const controller = 'parecerista-rest';
    return api.getRequest(`/${MODULE}/${controller}`, api.parseQueryParams(params));
};
