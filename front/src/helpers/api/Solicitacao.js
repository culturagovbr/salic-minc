import * as api from './base';

export const obterSolicitacoes = () => {
    const path = '/solicitacao/index-rest/';
    return api.getRequest(path);
};
