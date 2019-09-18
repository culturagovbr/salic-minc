import * as propostaHelperAPI from '@/helpers/api/Proposta';

import * as types from './types';

export const buscaLocalRealizacaoDeslocamento = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscaLocalRealizacaoDeslocamento(idPreProjeto)
        .then((response) => {
            const { data } = response;
            const localRealizacaoDeslocamento = data.data;
            commit(types.SET_LOCAL_REALIZACAO_DESLOCAMENTO, localRealizacaoDeslocamento);
        });
};

export const buscarLocaisRealizacao = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscarLocaisRealizacao(idPreProjeto)
        .then((response) => {
            const { data } = response;
            commit(types.SET_LOCAIS_REALIZACAO, data.data);
        });
};

export const buscarPlanoDistribuicaoDetalhamentos = ({ commit }, params) => {
    propostaHelperAPI.buscarPlanoDistribuicaoDetalhamentos(params)
        .then((response) => {
            const { data } = response;
            commit(types.SET_PLANO_DISTRIBUICAO_DETALHAMENTOS, data.data);
        });
};

export const salvarPlanoDistribuicaoDetalhamento = async ({ commit }, params) => propostaHelperAPI
    .salvarPlanoDistribuicaoDetalhamento(params)
    .then((response) => {
        const { data } = response;
        commit(types.UPDATE_PLANO_DISTRIBUICAO_DETALHAMENTO, data.data);
        return response.data;
    }).catch((e) => {
        throw new TypeError(e.response.data, 'salvarPlanoDetalhamento', 10);
    });

export const excluirPlanoDistribuicaoDetalhamento = async ({ commit }, params) => propostaHelperAPI
    .excluirPlanoDistribuicaoDetalhamento(params)
    .then((response) => {
        commit(types.EXCLUIR_PLANO_DISTRIBUICAO_DETALHAMENTO, params);
        return response.data;
    }).catch((e) => {
        throw new TypeError(e.response.data, 'salvarPlanoDetalhamento', 10);
    });

export const buscaFontesDeRecursos = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscaFontesDeRecursos(idPreProjeto)
        .then((response) => {
            const { data } = response;
            const fontesDeRecursos = data.data;
            commit(types.SET_FONTES_DE_RECURSOS, fontesDeRecursos);
        });
};

export const buscaDocumentos = ({ commit }, dados) => {
    propostaHelperAPI.buscaDocumentos(dados)
        .then((response) => {
            const { data } = response;
            const documentos = data.data;
            commit(types.SET_DOCUMENTOS, documentos);
        });
};

export const buscarDadosProposta = ({ commit }, idPreProjeto) => propostaHelperAPI
    .buscarDadosProposta(idPreProjeto)
    .then((response) => {
        const { data } = response;
        const proposta = data.data;
        commit(types.SET_DADOS_PROPOSTA, proposta);
    });

export const buscarHistoricoSolicitacoes = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscarHistoricoSolicitacoes(idPreProjeto)
        .then((response) => {
            const { data } = response.data;
            const historicoSolicitacoes = data.items;
            commit(types.SET_HISTORICO_SOLICITACOES, historicoSolicitacoes);
        });
};

export const buscarHistoricoEnquadramento = ({ commit }, idPreProjeto) => {
    propostaHelperAPI.buscarHistoricoEnquadramento(idPreProjeto)
        .then((response) => {
            const { data } = response.data;
            const historicoEnquadramento = data.items;
            commit(types.SET_HISTORICO_ENQUADRAMENTO, historicoEnquadramento);
        });
};
