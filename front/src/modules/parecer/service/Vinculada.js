import * as api from '@/helpers/api/base';

const MODULE = 'parecer';

export const buscarVinculadas = (params) => {
    const controller = 'vinculada-rest';
    const queryParams = api.parseQueryParams(params);

    return api.getRequest(`/${MODULE}/${controller}${queryParams}`);
};
