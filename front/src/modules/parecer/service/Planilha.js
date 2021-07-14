import * as api from '@/helpers/api/base';

const MODULE = 'parecer';

export const restaurarPlanilhaProduto = (params) => {
    const controller = 'planilha-produto-rest';
    return api.postRequest(`/${MODULE}/${controller}`, api.buildData(params));
};
