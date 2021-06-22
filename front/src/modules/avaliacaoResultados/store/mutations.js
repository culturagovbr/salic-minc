import * as types from './types';
import Parecer from '../mocks/Parecer.json';
import TipoAvaliacao from '../mocks/TipoAvaliacao.json';

export const state = {
    consolidacaoComprovantes: {},
    dadosTabela: [],
    dadosTabelaTecnico: [],
    dadosHistoricoEncaminhamento: [],
    dadosDestinatarios: [],
    diligenciasHistorico: [],
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
    projetosFinalizados: {},
    dadosItemComprovacao: {},
    projetosParaDistribuir: {},
    getProjetosAssinatura: [],
    getProjetosLaudoFinal: {},
    getProjetosLaudoAssinar: {},
    getProjetosLaudoEmAssinatura: {},
    getProjetosLaudoFinalizados: {},
    getProjetosAssinar: {},
    getProjetosEmAssinatura: {},
    getProjetosHistorico: {},
    versao: {},
    projetosRevisao: {},
    devolverProjeto: {},
    objetoParecer: {},
    itensBuscaComprovantes: {},
    comprovantes: [],
    projetosAssinarCoordenador: {},
    projetosAssinarCoordenadorGeral: {},
    dashboard: {},
    estatisticasAvaliacao: {},
    projetosSimilares: [],
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
        const { dadosTabela } = state;

        dadosTabela.forEach((value, index) => {
            if (registro.Codigo === value.Codigo) {
                state.dadosTabela.splice(index, 1, registro);
            }
        });
    },
    [types.REMOVER_REGISTRO](state, registro) {
        const { dadosTabela } = state.dadosTabela;

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
    [types.HISTORICO_DILIGENCIAS](state, diligencias) {
        state.diligenciasHistorico = diligencias;
    },
    [types.HISTORICO_ENCAMINHAMENTO](state, dados) {
        state.dadosHistoricoEncaminhamento = [];
        Object.values(dados).forEach((historico) => {
            state.dadosHistoricoEncaminhamento.push(historico);
        });
    },
    [types.GET_TIPO_AVALIACAO](state, tipoAvaliacao) {
        const valor = tipoAvaliacao[0];
        state.tipoAvaliacao = valor;
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
    [types.SET_PARECER](state, parecer) {
        state.parecer = parecer;
    },
    [types.SET_DADOS_PROJETOS_FINALIZADOS](state, dados) {
        state.projetosFinalizados = dados;
    },
    [types.GET_DADOS_ITEM_COMPROVACAO](state, dados) {
        state.dadosItemComprovacao = dados;
    },
    [types.ALTERAR_DADOS_ITEM_COMPROVACAO](state, params) {
        const { index } = params;
        const valor = params;
        delete valor.index;

        Object.keys(valor).forEach((key) => {
            state.dadosItemComprovacao.comprovantes[index][key] = valor[key];
        });
    },
    [types.SET_DADOS_PROJETOS_PARA_DISTRIBUIR](state, dados) {
        state.projetosParaDistribuir = dados;
    },

    [types.SET_DADOS_PROJETOS_ASSINAR](state, dados) {
        state.getProjetosAssinar = dados;
    },
    [types.SET_DADOS_PROJETOS_EM_ASSINATURA](state, dados) {
        state.getProjetosEmAssinatura = dados;
    },
    [types.SET_DADOS_PROJETOS_LAUDO_FINAL](state, dados) {
        state.getProjetosLaudoFinal = dados;
    },
    [types.SET_DADOS_PROJETOS_LAUDO_ASSINAR](state, dados) {
        state.getProjetosLaudoAssinar = dados;
    },
    [types.SET_DADOS_PROJETOS_LAUDO_EM_ASSINATURA](state, dados) {
        state.getProjetosLaudoEmAssinatura = dados;
    },
    [types.SET_DADOS_PROJETOS_LAUDO_FINALIZADOS](state, dados) {
        state.getProjetosLaudoFinalizados = dados;
    },
    [types.SET_DADOS_PROJETOS_HISTORICO](state, dados) {
        state.getProjetosHistorico = dados;
    },
    [types.SET_VERSAO](state, dados) {
        state.versao = dados;
    },
    [types.SYNC_PROJETOS_REVISAO](state, dados) {
        state.projetosRevisao = dados;
    },
    [types.SET_DEVOLVER_PROJETO](state, devolverProjeto) {
        state.devolverProjeto = devolverProjeto;
    },
    [types.GET_OBJETO_PARECER](state, dados) {
        state.objetoParecer = dados;
    },
    [types.SET_ITENS_BUSCA_COMPROVANTES](state, dados) {
        state.itensBuscaComprovantes = dados;
    },
    [types.SET_COMPROVANTES](state, dados) {
        state.comprovantes = dados;
    },
    [types.EDIT_COMPROVANTE](state, comprovante) {
        const index = state.comprovantes.findIndex(
            item => item.idComprovantePagamento === comprovante.idComprovantePagamento,
        );
        Object.assign(state.comprovantes[index], comprovante);
    },
    [types.SYNC_PROJETOS_ASSINAR_COORDENADOR](state, dados) {
        state.projetosAssinarCoordenador = dados;
    },
    [types.SYNC_PROJETOS_ASSINAR_COORDENADOR_GERAL](state, dados) {
        state.projetosAssinarCoordenadorGeral = dados;
    },
    [types.ALTERAR_PLANILHA](state, params) {
        const tiposXQuantidade = {
            1: 0, // avaliado
            3: 0, // impugnado
            4: 0, // aguardando analise
        };

        let totalComprovantes = 0;
        state.comprovantes.forEach((comprovante) => {
            tiposXQuantidade[comprovante.stItemAvaliado] += 1;
            totalComprovantes += 1;
        });

        const index = state.planilha.findIndex(
            item => item.cdProduto === params.cdProduto
            && item.cdEtapa === params.cdEtapa
            && item.cdUF === params.cdUF
            && item.cdCidade === params.cdCidade
            && item.idPlanilhaItens === params.idPlanilhaItens,
        );

        if (index >= 0) {
            state.planilha[index] = Object.assign(state.planilha[index], {
                qtComprovado: totalComprovantes,
                qtComprovadoRecusada: tiposXQuantidade[3],
                qtComprovadoValidado: tiposXQuantidade[1],
            });
        }
    },
    [types.SET_ESTATISTICAS_AVALIACAO](state, dados) {
        state.estatisticasAvaliacao = dados;
    },
};
