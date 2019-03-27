import * as outrasInformacoesHelperAPI from '@/helpers/api/OutrasInformacoes';
import * as types from './types';

export const buscarCertidoesNegativas = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarCertidoesNegativas(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_CERTIDOES_NEGATIVAS, data);
        });
};

export const buscarDocumentosAssinados = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarDocumentosAssinados(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DOCUMENTOS_ASSINADOS, data);
        });
};

export const buscarDadosComplementares = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarDadosComplementares(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DADOS_COMPLEMENTARES, data);
        });
};

export const buscarDocumentosAnexados = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarDocumentosAnexados(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DOCUMENTOS_ANEXADOS, data);
        });
};
export const buscarLocalRealizacaoDeslocamento = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarLocalRealizacaoDeslocamento(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_LOCAL_REALIZACAO_DESLOCAMENTO, data);
        });
};

export const buscarProvidenciaTomada = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarProvidenciaTomada(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_PROVIDENCIA_TOMADA, data);
        });
};

export const buscarPlanoDistribuicaoIn2013 = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarPlanoDistribuicaoIn2013(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_PLANO_DISTRIBUICAO_IN2013, data);
        });
};

export const buscarHistoricoEncaminhamento = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarHistoricoEncaminhamento(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_HISTORICO_ENCAMINHAMENTO, data);
        });
};

export const buscarTramitacaoDocumento = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarTramitacaoDocumento(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_TRAMITACAO_DOCUMENTO, data);
        });
};

export const buscarTramitacaoProjeto = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarTramitacaoProjeto(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_TRAMITACAO_PROJETO, data);
        });
};

export const buscarUltimaTramitacao = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarUltimaTramitacao(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_ULTIMA_TRAMITACAO, data);
        });
};

export const buscarPlanoDistribuicaoIn2017 = ({ commit }, idPreProjeto) => {
    outrasInformacoesHelperAPI.buscarPlanoDistribuicaoIn2017(idPreProjeto)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_PLANO_DISTRIBUICAO_IN2017, data);
        });
};

export const buscarDiligenciaProposta = ({ commit }, value) => {
    const { idPreprojeto, valor } = value;
    outrasInformacoesHelperAPI.buscarDiligenciaProposta(idPreprojeto, valor)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DILIGENCIA_PROPOSTA, data);
        });
};

export const buscarDiligenciaAdequacao = ({ commit }, value) => {
    const { idPronac, valor } = value;
    outrasInformacoesHelperAPI.buscarDiligenciaAdequacao(idPronac, valor)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DILIGENCIA_ADEQUACAO, data);
        });
};

export const buscarDiligenciaProjeto = ({ commit }, value) => {
    const { idPronac, valor } = value;
    outrasInformacoesHelperAPI.buscarDiligenciaProjeto(idPronac, valor)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DILIGENCIA_PROJETO, data);
        });
};

export const buscarDiligencia = ({ commit }, idPronac) => {
    outrasInformacoesHelperAPI.buscarDiligencia(idPronac)
        .then((response) => {
            const data = response.data.data.items;
            commit(types.SET_DILIGENCIA, data);
        });
};
