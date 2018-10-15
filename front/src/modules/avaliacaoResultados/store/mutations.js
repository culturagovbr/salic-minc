import * as types from './types';
import Parecer from '../mocks/Parecer.json';
import TipoAvaliacao from '../mocks/TipoAvaliacao.json';

export const state = {
    consolidacaoComprovantes: {},
    dadosTabela: [],
    dadosTabelaTecnico: [],
    dadosHistoricoEncaminhamento: [],
    dadosDestinatarios: [],
    mocks:
    {
        parecer: Parecer,
        tipoAvaliacao: TipoAvaliacao,
    },
    parecer: {},
    projeto: {},
    proponente: {},
    registroAtivo: {},
    tipoAvaliacao: {},
    redirectLink: {},
    planilha: {},
    projetoAnalise: {},
    consolidacaoAnalise: {},
    getParecerLaudoFinal: {},
    characterManifestacao: '',
    characterParecer: '',
    projetosFinalizados: {},
    dadosItemComprovacao: {},
    projetosParaDistribuir: {},
    getProjetosAssinatura: [],
    getProjetosLaudoFinal: [],
    getProjetosAssinar: {},
    getProjetosEmAssinatura: {},
    getProjetosHistorico: {},
};

export const mutations = {
    [types.MOCK_AVALIACAO_RESULTADOS]() {
    },
    [types.GET_CONSOLIDACAO_PARECER](state, consolidacaoComprovantes) {
        state.consolidacaoComprovantes = consolidacaoComprovantes;
    },
    [types.GET_PARECER](state, parecer) {
        state.parecer = parecer;
    },
    [types.GET_PROJETO](state, projeto) {
        state.projeto = projeto;
    },
    [types.GET_PROPONENTE](state, proponente) {
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
    [types.DESTINATARIOS_ENCAMINHAMENTO](state, destinatarios) {
        state.dadosDestinatarios = destinatarios;
    },
    [types.PROJETOS_AVALIACAO_TECNICA](state, dados) {
        state.dadosTabelaTecnico = dados;
    },
    [types.HISTORICO_ENCAMINHAMENTO](state, dados) {
        state.dadosHistoricoEncaminhamento = [];
        Object.values(dados).forEach((historico) => {
            state.dadosHistoricoEncaminhamento.push(historico);
        });
    },
    [types.GET_TIPO_AVALIACAO](state, tipoAvaliacao) {
        state.tipoAvaliacao = tipoAvaliacao[0];
    },
    [types.LINK_REDIRECIONAMENTO_TIPO_AVALIACAO_RESULTADO](state, redirectLink) {
        state.redirectLink = redirectLink;
    },
    [types.GET_PLANILHA](state, planilha) {
        state.planilha = planilha;
    },
    [types.GET_PROJETO_ANALISE](state, projetoAnalise) {
        state.projetoAnalise = projetoAnalise;
    },
    [types.GET_CONSOLIDACAO_ANALISE](state, consolidacaoAnalise) {
        state.consolidacaoAnalise = consolidacaoAnalise;
    },
    [types.GET_PARECER_LAUDO_FINAL](state, data) {
        state.getParecerLaudoFinal = data;
    },
    [types.SET_MANIFESTACAO_PROVISORIA](state, characterManifestacao) {
        state.characterManifestacao = characterManifestacao;
    },
    [types.SET_PARECER_PROVISORIO](state, characterParecer) {
        state.characterParecer = characterParecer;
    },
    [types.GET_PROJETO_ANALISE](state, projetoAnalise) {
        state.projetoAnalise = projetoAnalise;
    },
    [types.SET_PARECER](state, parecer) {
        state.parecer = parecer;
    },
    [types.SET_DADOS_PROJETOS_FINALIZADOS](state, dados) {
        state.projetosFinalizados = dados;
    },
    [types.GET_DADOS_ITEM_COMPROVACAO](state, dados) {
        state.dadosItemComprovacao = dados;
    },
    [types.SET_DADOS_PROJETOS_PARA_DISTRIBUIR](state, dados){
        state.projetosParaDistribuir = dados;
    },

    [types.SET_DADOS_PROJETOS_ASSINAR](state, dados){
        state.getProjetosAssinar = dados;
    },
    [types.SET_DADOS_PROJETOS_EM_ASSINATURA](state, dados){
        state.getProjetosEmAssinatura = dados;
    },
    [types.SET_DADOS_PROJETOS_LAUDO_FINAL](state, dados){
        state.getProjetosLaudoFinal = dados;
    },
    [types.SET_DADOS_PROJETOS_HISTORICO](state, dados){
        state.getProjetosHistorico = dados;
    },
};
