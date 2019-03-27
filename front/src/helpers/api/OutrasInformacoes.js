import * as api from './base';

export const buscarCertidoesNegativas = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/certidoes-negativas-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarDocumentosAssinados = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/documentos-assinados-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarDadosComplementares = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/dados-complementares-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarDocumentosAnexados = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/documentos-anexados-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarLocalRealizacaoDeslocamento = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/local-realizacao-deslocamento-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarProvidenciaTomada = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/providencia-tomada-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarPlanoDistribuicaoIn2013 = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/plano-distribuicao-in2013-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarHistoricoEncaminhamento = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/historico-encaminhamento-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarTramitacaoDocumento = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/tramitacao-documento-rest';
    const metodo = '/get';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarTramitacaoProjeto = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/tramitacao-projeto-rest';
    const metodo = '/get';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarUltimaTramitacao = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/ultima-tramitacao-rest';
    const metodo = '/get';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};

export const buscarPlanoDistribuicaoIn2017 = (idPreProjeto) => {
    const modulo = '/projeto';
    const controller = '/plano-distribuicao-rest';
    const metodo = '/index';
    const path = `${modulo}${controller}${metodo}`;
    const queryParams = `?idPreProjeto=${idPreProjeto}`;
    return api.getRequest(path, queryParams);
};

export const buscarDiligenciaProposta = (idPreprojeto, idAvaliacaoProposta) => {
    const modulo = '/projeto';
    const controller = '/diligencia-proposta-rest';
    const path = `${modulo}${controller}`;
    const queryParams = `?idPreProjeto=${idPreprojeto}&id=${idAvaliacaoProposta}`;
    return api.getRequest(path, queryParams);
};

export const buscarDiligenciaAdequacao = (idPronac, idAvaliarAdequacaoProjeto) => {
    const modulo = '/projeto';
    const controller = '/diligencia-adequacao-rest';
    const path = `${modulo}${controller}`;
    const queryParams = `?idPronac=${idPronac}&id=${idAvaliarAdequacaoProjeto}`;
    return api.getRequest(path, queryParams);
};

export const buscarDiligenciaProjeto = (idPronac, idDiligencia) => {
    const modulo = '/projeto';
    const controller = '/diligencia-projeto-rest';
    const path = `${modulo}${controller}`;
    const queryParams = `?idPronac=${idPronac}&id=${idDiligencia}`;
    return api.getRequest(path, queryParams);
};

export const buscarDiligencia = (idPronac) => {
    const modulo = '/projeto';
    const controller = '/diligencia-projeto-rest';
    const path = `${modulo}${controller}`;
    const queryParams = `?idPronac=${idPronac}`;
    return api.getRequest(path, queryParams);
};
