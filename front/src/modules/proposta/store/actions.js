import * as propostaHelperAPI from '@/helpers/api/Proposta';

import * as types from './types';

export const buscaLocalRealizacaoDeslocamento = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscaLocalRealizacaoDeslocamento(idPreProjeto)
        .then((response) => {
            const data = response.data;
            const localRealizacaoDeslocamento = data.data;
            commit(types.SET_LOCAL_REALIZACAO_DESLOCAMENTO, localRealizacaoDeslocamento);
        });
};

export const buscaFontesDeRecursos = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscaFontesDeRecursos(idPreProjeto)
        .then((response) => {
            const data = response.data;
            const fontesDeRecursos = data.data;
            commit(types.SET_FONTES_DE_RECURSOS, fontesDeRecursos);
        });
};

export const buscaDocumentos = ({ commit }, dados) => {
    propostaHelperAPI.buscaDocumentos(dados)
        .then((response) => {
            const data = response.data;
            const documentos = data.data;
            commit(types.SET_DOCUMENTOS, documentos);
        });
};

export const buscarDadosProposta = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscarDadosProposta(idPreProjeto)
        .then((response) => {
            const data = response.data;
            const proposta = data.data;
            commit(types.SET_DADOS_PROPOSTA, proposta);
        });
};

export const buscarHistoricoSolicitacoes = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscarHistoricoSolicitacoes(idPreProjeto)
        .then((response) => {
            const data = response.data.data;
            const historicoSolicitacoes = data.items;
            commit(types.SET_HISTORICO_SOLICITACOES, historicoSolicitacoes);
        });
};

export const buscarHistoricoEnquadramento = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscarHistoricoEnquadramento(idPreProjeto)
        .then((response) => {
            const data = response.data.data;
            const historicoEnquadramento = data.items;
            commit(types.SET_HISTORICO_ENQUADRAMENTO, historicoEnquadramento);
        });
};
