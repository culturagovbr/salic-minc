import * as api from './base';

const buildData = (params) => {
    const bodyFormData = new FormData();

    Object.keys(params).forEach((key) => {
        bodyFormData.append(key, params[key]);
    });

    return bodyFormData;
};

const path = '/navegacao/menu-principal';

export const dadosMenu = () => api.getRequest(path);

export const criarRegistro = params => api.postRequest(path, buildData(params));

export const atualizarRegistro = params => api.putRequest(path, buildData(params), params.Codigo);

export const removerRegistro = params => api.deleteRequest(path, params.Codigo);

export const getTeste = params => api.postRequest('/realizarprestacaodecontas/carregar-destinatarios/', buildData(params));

export const getTipoAvaliacao = params => api.getRequest(`/avaliacao-resultados/tipo-avaliacao-rest/idPronac/${params}`);

/** FLUXO PROJETO */

export const obterDadosTabelaTecnico = (params) => {
    let data = '?';

    if (params.estadoid) {
        data += `estadoid=${params.estadoid}`;
    }

    if (params.idAgente) {
        data += `&idAgente=${params.idAgente}`;
    }

    if (params.idSecretaria) {
        data += `&idSecretaria=${params.idSecretaria}`;
    }

    return api.getRequest(`/avaliacao-resultados/fluxo-projeto${data}`);
};

export const obterProjetoTabelaTecnico = id => api.getRequest(`/avaliacao-resultados/fluxo-projeto/id/${id}`);

export const projetosRevisao = (params) => {
    let data = '?';

    if (params.estadoid) {
        data += `estadoid=${params.estadoid}`;
    }

    if (params.idAgente) {
        data += `&idAgente=${params.idAgente}`;
    }

    if (params.idSecretaria) {
        data += `&idSecretaria=${params.idSecretaria}`;
    }

    return api.getRequest(`/avaliacao-resultados/fluxo-projeto${data}`);
};

export const projetosPorEstado = (params) => {
    let data = '?';

    if (params.estadoid) {
        data += `estadoid=${params.estadoid}`;
    }

    if (params.idAgente) {
        data += `&idAgente=${params.idAgente}`;
    }

    if (params.idSecretaria) {
        data += `&idSecretaria=${params.idSecretaria}`;
    }

    return api.getRequest(`/avaliacao-resultados/fluxo-projeto${data}`);
};


export const obterHistoricoEncaminhamento = params => api.getRequest(`/avaliacao-resultados/historico/idPronac/${params}`);

export const planilha = params => api.getRequest(`/avaliacao-resultados/planilha-aprovada/idPronac/${params}`);

export const projetoAnalise = params => api.getRequest(`/avaliacao-resultados/projeto/idPronac/${params}`);

export const consolidacaoAnalise = params => api.getRequest(`/prestacao-contas/visualizar-projeto/dados-projeto?idPronac=${params}`);

export const obterDestinatarios = params => api.getRequest(`/avaliacao-resultados/tecnicos/${params}`);

export const alterarEstado = params => api.postRequest('/avaliacao-resultados/estado/', buildData(params));

export const obterDadosItemComprovacao = params => api.getRequest(`/avaliacao-resultados/avaliacao-comprovante/${params}`);

export const criarParecerLaudoFinal = params => api.postRequest('/avaliacao-resultados/laudo', buildData(params));


export const obterProjetosParaDistribuir = () => api.getRequest('/avaliacao-resultados/projeto-inicio');

/** DILIGENCIA */

export const criarDiligencia = params => api.postRequest('/diligencia/diligencia', buildData(params));

export const listarDiligencias = params => api.getRequest(`/avaliacao-resultados/diligencia?idPronac=${params}&situacao=E17&tpDiligencia=147`);

/** FIM DILIGENCIA */

/**  PARECER TECNICO */

export const parecerConsolidacao = params => api.getRequest(`/avaliacao-resultados/emissao-parecer-rest/idPronac/${params}`);

export const criarParecer = (params) => {
    const parametro = params.idPronac;
    const data = params;
    delete data.idPronac;

    return api.postRequest(`/avaliacao-resultados/emissao-parecer-rest/idPronac/${parametro}`, buildData(data));
};

/** FIM DO PARECER TECNICO */

export const obterLaudoFinal = idPronac => api.getRequest(`/avaliacao-resultados/laudo/get?idPronac=${idPronac}`);

export const obterProjetosLaudoFinal = param => api.getRequest(`/avaliacao-resultados/laudo/index?estadoId=${param.estadoId}`);

export const alterarPerfil = (grupoAtivo, orgaoAtivo) => api.getRequest(`perfil/perfil-rest/index?codGrupo=${grupoAtivo}&codOrgao=${orgaoAtivo}`);

export const obterProjetosAssinatura = params => api.getRequest(`/avaliacao-resultados/projeto-assinatura/estado/${params.estado}`);

export const buscarDetalhamentoItens = idPronac => api.getRequest(`/avaliacao-resultados/detalhamento-itens-rest?idPronac=${idPronac}`);

export const buscarComprovantes = (params) => {
    const modulo = '/prestacao-contas';
    const controller = '/comprovante-pagamento';

    const uf = `uf=${params.uf}`;
    const idPronac = `idPronac=${params.idPronac}`;
    const idPlanilhaItem = `idPlanilhaItem=${params.idPlanilhaItens}`;
    const produto = `produto=${params.codigoProduto}`;
    const idMunicipio = `idmunicipio=${params.codigoCidade}`;
    const etapa = `etapa=${params.codigoEtapa}`;
    const stItemAvaliado = `stItemAvaliado=${params.stItemAvaliado}`;

    const url = `${modulo}${controller}`;
    const queryParams = `?${idPronac}&${idPlanilhaItem}&${produto}&${uf}&${idMunicipio}&${stItemAvaliado}&${etapa}`;

    return api.getRequest(url + queryParams);
};


export const salvarAvaliacaoComprovante = params => api.postRequest('/avaliacao-resultados/avaliacao-comprovante/', buildData(params));

export const dashboardQuantidade = () => api.getRequest('/avaliacao-resultados/dashboard');

export const buscarEstatisticasAvaliacao = idPronac => api.getRequest(`/avaliacao-resultados/estatisticas-avaliacao/idPronac/${idPronac}`);
