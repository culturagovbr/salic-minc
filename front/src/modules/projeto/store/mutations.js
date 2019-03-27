import * as types from './types';

export const state = {
    projeto: {},
    proponente: {},
    planilhaHomologada: {},
    planilhaOriginal: {},
    planilhaReadequada: {},
    planilhaAutorizada: {},
    planilhaAdequada: {},
    transferenciaRecursos: [],
    certidoesNegativas: {},
    documentosAssinados: [],
    dadosComplementares: {},
    documentosAnexados: [],
    localRealizacaoDeslocamento: {},
    providenciaTomada: {},
    planoDistribuicaoIn2013: [],
    historicoEncaminhamento: {},
    tramitacaoDocumento: [],
    tramitacaoProjeto: [],
    ultimaTramitacao: [],
    planoDistribuicaoIn2017: [],
    diligenciaProposta: [],
    diligenciaAdequacao: [],
    diligenciaProjeto: [],
    diligencia: [],
    marcasAnexadas: [],
    dadosReadequacoes: [],
    pedidoProrrogacao: [],
    dadosFiscalizacaoLista: [],
    dadosFiscalizacaoVisualiza: {},
    contasBancarias: [],
    conciliacaoBancaria: [],
    inconsistenciaBancaria: [],
    liberacao: [],
    saldoContas: [],
    extratosBancarios: [],
    extratosBancariosConsolidado: [],
    captacao: [],
    devolucoesIncentivador: [],
};

export const mutations = {
    [types.SET_PROJETO](state, projeto) {
        state.projeto = projeto;
    },
    [types.SET_TRANSFERENCIA_RECURSOS](state, transferenciaRecursos) {
        state.transferenciaRecursos = transferenciaRecursos;
    },
    [types.SET_PROPONENTE](state, proponente) {
        state.proponente = proponente;
    },
    [types.SET_PLANILHA_HOMOLOGADA](state, planilhaHomologada) {
        state.planilhaHomologada = planilhaHomologada;
    },
    [types.SET_PLANILHA_ORIGINAL](state, planilhaOriginal) {
        state.planilhaOriginal = planilhaOriginal;
    },
    [types.SET_PLANILHA_READEQUADA](state, planilhaReadequada) {
        state.planilhaReadequada = planilhaReadequada;
    },
    [types.SET_PLANILHA_AUTORIZADA](state, planilhaAutorizada) {
        state.planilhaAutorizada = planilhaAutorizada;
    },
    [types.SET_PLANILHA_ADEQUADA](state, planilhaAdequada) {
        state.planilhaAdequada = planilhaAdequada;
    },
    [types.SET_CERTIDOES_NEGATIVAS](state, certidoesNegativas) {
        state.certidoesNegativas = certidoesNegativas;
    },
    [types.SET_DOCUMENTOS_ASSINADOS](state, documentosAssinados) {
        state.documentosAssinados = documentosAssinados;
    },
    [types.SET_DADOS_COMPLEMENTARES](state, dadosComplementares) {
        state.dadosComplementares = dadosComplementares;
    },
    [types.SET_DOCUMENTOS_ANEXADOS](state, dados) {
        state.documentosAnexados = dados;
    },
    [types.SET_LOCAL_REALIZACAO_DESLOCAMENTO](state, localRealizacaoDeslocamento) {
        state.localRealizacaoDeslocamento = localRealizacaoDeslocamento;
    },
    [types.SET_PROVIDENCIA_TOMADA](state, providenciaTomada) {
        state.providenciaTomada = providenciaTomada;
    },
    [types.SET_PLANO_DISTRIBUICAO_IN2013](state, dados) {
        state.planoDistribuicaoIn2013 = dados;
    },
    [types.SET_HISTORICO_ENCAMINHAMENTO](state, historicoEncaminhamento) {
        state.historicoEncaminhamento = historicoEncaminhamento;
    },
    [types.SET_TRAMITACAO_DOCUMENTO](state, tramitacaoDocumento) {
        state.tramitacaoDocumento = tramitacaoDocumento;
    },
    [types.SET_TRAMITACAO_PROJETO](state, tramitacaoProjeto) {
        state.tramitacaoProjeto = tramitacaoProjeto;
    },
    [types.SET_ULTIMA_TRAMITACAO](state, ultimaTramitacao) {
        state.ultimaTramitacao = ultimaTramitacao;
    },
    [types.SET_PLANO_DISTRIBUICAO_IN2017](state, dados) {
        state.planoDistribuicaoIn2017 = dados;
    },
    [types.SET_DILIGENCIA_PROPOSTA](state, dados) {
        state.diligenciaProposta = dados;
    },
    [types.SET_DILIGENCIA_ADEQUACAO](state, dados) {
        state.diligenciaAdequacao = dados;
    },
    [types.SET_DILIGENCIA_PROJETO](state, dados) {
        state.diligenciaProjeto = dados;
    },
    [types.SET_DILIGENCIA](state, dados) {
        state.diligencia = dados;
    },
    [types.SET_MARCAS_ANEXADAS](state, dados) {
        state.marcasAnexadas = dados;
    },
    [types.SET_DADOS_READEQUACOES](state, dados) {
        state.dadosReadequacoes = dados;
    },
    [types.SET_PEDIDO_PRORROGACAO](state, dados) {
        state.pedidoProrrogacao = dados;
    },
    [types.SET_DADOS_FISCALIZACAO_LISTA](state, dados) {
        state.dadosFiscalizacaoLista = dados;
    },
    [types.SET_DADOS_FISCALIZACAO_VISUALIZA](state, dados) {
        state.dadosFiscalizacaoVisualiza = dados;
    },
    [types.SET_CONTAS_BANCARIAS](state, dados) {
        state.contasBancarias = dados;
    },
    [types.SET_CONCILIACAO_BANCARIA](state, dados) {
        state.conciliacaoBancaria = dados;
    },
    [types.SET_INCONSISTENCIA_BANCARIA](state, dados) {
        state.inconsistenciaBancaria = dados;
    },
    [types.SET_LIBERACAO](state, dados) {
        state.liberacao = dados;
    },
    [types.SET_SALDO_CONTAS](state, dados) {
        state.saldoContas = dados;
    },
    [types.SET_EXTRATOS_BANCARIOS](state, dados) {
        state.extratosBancarios = dados;
    },
    [types.SET_EXTRATOS_BANCARIOS_CONSOLIDADO](state, dados) {
        state.extratosBancariosConsolidado = dados;
    },
    [types.SET_CAPTACAO](state, dados) {
        state.captacao = dados;
    },
    [types.SET_DEVOLUCOES_INCENTIVADOR](state, dados) {
        state.devolucoesIncentivador = dados;
    },
};
