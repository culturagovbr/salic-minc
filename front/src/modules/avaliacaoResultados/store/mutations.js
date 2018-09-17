import * as types from './types';
import Parecer from '../mocks/Parecer.json';
import TipoAvaliacao from '../mocks/TipoAvaliacao.json';

export const state = {
    consolidacaoComprovantes: {},
    dadosTabela: [],
    dadosTabelaTecnico: [],
    dadosHistoricoEncaminhamento: [],
    destinatarios: {},
    mocks:
    {
        parecer:Parecer,
        tipoAvaliacao:TipoAvaliacao,
    },
    parecer: {},
    projeto: {},
    proponente: {},
    registroAtivo: {},
    tipoAvaliacao: {},
    redirectLink:{}
};

export const mutations = {
    [types.MOCK_AVALIACAO_RESULTADOS](state){
        state.mocks;
    },
    [types.GET_CONSOLIDACAO_PARECER](state, consolidacaoComprovantes){
        state.consolidacaoComprovantes = consolidacaoComprovantes;
    },
    [types.GET_PARECER](state, parecer){
        state.parecer = parecer;
    },
    [types.GET_PROJETO](state, projeto){
        state.projeto = projeto;
    },
    [types.GET_PROPONENTE](state, proponente){
        state.proponente = proponente;
    },
    [types.SET_REGISTROS_TABELA](state, dadosTabela) {
        state.dadosTabela = dadosTabela;
    },
    [types.SET_REGISTRO_ATIVO](state, registro) {
        state.registroAtivo = registro;
    },
    [types.SET_REGISTRO_TABELA](state, registro) {
        state.dadosTabela.push(registro);
    },
    [types.ATUALIZAR_REGISTRO_TABELA](state, registro) {
        const dadosTabela = state.dadosTabela;

        dadosTabela.forEach((value, index) => {
            if (registro.Codigo === value.Codigo) {
                state.dadosTabela.splice(index, 1, registro);
            }
        });
    },
    [types.REMOVER_REGISTRO](state, registro) {
        const dadosTabela = state.dadosTabela;

        dadosTabela.forEach((value, index) => {
            if (registro.Codigo === value.Codigo) {
                state.dadosTabela.splice(index, 1);
            }
        });
    },
    [types.DESTINATARIOS_ENCAMINHAMENTO](state, destinatarios){
        state.destinatarios = destinatarios;
    },
    [types.PROJETOS_AVALIACAO_TECNICA](state, dados){
        state.dadosTabelaTecnico = dados;
    },
    [types.HISTORICO_ENCAMINHAMENTO](state, dados){
        state.dadosHistoricoEncaminhamento = [];
        Object.values(dados).forEach(function(historico) {
            state.dadosHistoricoEncaminhamento.push(historico);
        });
    },
    [types.GET_TIPO_AVALIACAO](state,tipoAvaliacao){
        state.tipoAvaliacao = tipoAvaliacao[0];
    },
    [types.LINK_REDIRECIONAMENTO_TIPO_AVALIACAO_RESULTADO] (state, redirectLink){
        state.redirectLink = redirectLink;
    }
};
