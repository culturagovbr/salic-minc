import { fnSetCookie, fnGetCookie } from '@/mixins/funcoes/cookie';
import * as layoutHelperAPI from '@/helpers/api/Layout';
import * as solicitacaoHelperAPI from '@/helpers/api/Solicitacao';
import * as desencapsularResponse from '@/helpers/actions';
import * as types from './types';

export const buscarPerfisDisponiveis = ({ commit }, params) => {
    layoutHelperAPI.buscarPerfisDisponiveis(params)
        .then((response) => {
            const items = desencapsularResponse.default(response);
            commit(types.SET_PERFIS_DISPONIVEIS, items);
        });
};

export const alterarPerfil = (_, perfil) => {
    const grupoAtivo = perfil.gru_codigo;
    const orgaoAtivo = perfil.uog_orgao;
    const url = `/autenticacao/perfil/alterarperfil?codGrupo=${grupoAtivo}&codOrgao=${orgaoAtivo}`;
    window.location.replace(url);
};

export const obterSolicitacoes = ({ commit }) => {
    solicitacaoHelperAPI.obterSolicitacoes()
        .then((response) => {
            const items = desencapsularResponse.default(response);
            commit(types.SET_SOLICITACOES, items);
        });
};

export const buscarDadosLayout = ({ commit }) => {
    layoutHelperAPI.buscarDadosLayout()
        .then((response) => {
            const data = desencapsularResponse.default(response);
            commit(types.SET_VERSAO, data.versao[0]);
            commit(types.SET_QUANTIDADE_SOLICITACOES, data.quantidadeSolicitacoes);
        });
};

export const obterModoNoturno = ({ commit }) => {
    const status = (fnGetCookie('layout-modo-noturno') === 'true');
    commit(types.SET_MODO_NOTURNO, status);
};

export const atualizarModoNoturno = ({ commit }, status) => {
    fnSetCookie('layout-modo-noturno', status, 365);
    commit(types.SET_MODO_NOTURNO, status);
};

export const atualizarStatusSidebarEsquerda = ({ commit }, status) => {
    commit(types.SET_STATUS_SIDEBAR_ESQUERDA, status);
};

export const atualizarStatusSidebarDireita = ({ commit }, status) => {
    commit(types.SET_STATUS_SIDEBAR_DIREITA, status);
};

export const buscarDadosMenu = ({ commit }) => {
    layoutHelperAPI.buscarDadosMenu()
        .then((response) => {
            const { data } = response;
            const dadosTabela = data.data;
            commit(types.SET_MENU_PRINCIPAL, dadosTabela);
        });
};
