import * as api from './base';

export const buscaLocalRealizacaoDeslocamento = (idPreProjeto) => {
    const path = `/proposta/visualizar/obter-local-realizacao-deslocamento/idPreProjeto/${idPreProjeto}`;
    return api.getRequest(path);
};

export const buscaFontesDeRecursos = (idPreProjeto) => {
    const path = `/proposta/visualizar/obter-fonte-de-recurso/idPreProjeto/${idPreProjeto}`;
    return api.getRequest(path);
};

export const buscaDocumentos = (dados) => {
    const path = `/proposta/visualizar/obter-documentos-anexados/idPreProjeto/${dados.idPreProjeto}/idAgente/${dados.idAgente}`;
    return api.getRequest(path);
};

export const buscarDadosProposta = (idPreProjeto) => {
    const path = `/proposta/visualizar/obter-proposta-cultural-completa/idPreProjeto/${idPreProjeto}`;
    return api.getRequest(path);
};

export const buscarHistoricoSolicitacoes = (idPreProjeto) => {
    const path = `/solicitacao/mensagem-rest/index/idPreProjeto/${idPreProjeto}`;
    return api.getRequest(path);
};

export const buscarHistoricoEnquadramento = (idPreProjeto) => {
    const path = `/proposta/visualizar-rest/index/idPreProjeto/${idPreProjeto}`;
    return api.getRequest(path);
};
