import * as api from './base';

const buildData = (params) => {
    const bodyFormData = new FormData();

    Object.keys(params).forEach((key) => {
        bodyFormData.append(key, params[key]);
    });

    return bodyFormData;
};

export const buscaLocalRealizacaoDeslocamento = (idPreProjeto) => {
    const path = `/proposta/visualizar/obter-local-realizacao-deslocamento/idPreProjeto/${idPreProjeto}`;
    return api.getRequest(path);
};

export const buscarLocaisRealizacao = (idPreProjeto) => {
    const path = `/proposta/visualizar/obter-locais-realizacao/idPreProjeto/${idPreProjeto}`;
    return api.getRequest(path);
};

export const buscarPlanoDistribuicaoDetalhamentos = (dados) => {
    const url = `/proposta/plano-distribuicao/obter-detalhamentos/idPreProjeto/${dados.idPreProjeto}`;
    const params = `?idPlanoDistribuicao=${dados.idPlanoDistribuicao}`;
    return api.getRequest(url + params);
};

export const salvarPlanoDistribuicaoDetalhamento = params => api.postRequest(
    `/proposta/plano-distribuicao/detalhar-salvar/idPreProjeto/${params.idPreProjeto}`,
    buildData(params),
);

export const excluirPlanoDistribuicaoDetalhamento = params => api.postRequest(
    `/proposta/plano-distribuicao/detalhar-excluir/idPreProjeto/${params.idPreProjeto}`,
    buildData(params),
);

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
    const path = `/proposta/enquadramento-rest/index/idPreProjeto/${idPreProjeto}`;
    return api.getRequest(path);
};
