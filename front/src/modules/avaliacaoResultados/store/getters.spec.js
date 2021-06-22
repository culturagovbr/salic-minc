import * as getters from './getters';

describe('Teste getters - Avaliação de Resultados', () => {
    let state;

    beforeEach(() => {
        state = {
            dadosTabelaTecnico: [],
            projetosParaDistribuir: [],
            getProjetosAssinatura: [],
            getProjetosAssinar: [],
            getProjetosEmAssinatura: [],
            getProjetosHistorico: [],
            getProjetosLaudoFinal: [],
            getProjetosLaudoAssinar: [],
            getProjetosLaudoEmAssinatura: [],
            getProjetosLaudoFinalizados: [],
            dadosHistoricoEncaminhamento: [],
            planilha: [],
            consolidacaoAnalise: [],
            projetoAnalise: [],
            dadosMenu: [],
            registro: [],
            consolidacaoComprovantes: [],
            dadosDestinatarios: [],
            proponente: [],
            parecer: [],
            projeto: [],
            tipoAvaliacao: [],
            getParecerLaudoFinal: [],
            projetosFinalizados: [],
            dadosItemComprovacao: [],

        };
    });

    test('obterDadosTabelaTecnico', () => {
        const result = getters.dadosTabelaTecnico(state);
        expect(result).toEqual(state.dadosTabelaTecnico);
    });

    test('getProjetosParaDistribuir', () => {
        const result = getters.getProjetosParaDistribuir(state);
        expect(result).toEqual(state.projetosParaDistribuir);
    });

    test('getProjetosAssinatura', () => {
        const result = getters.getProjetosAssinatura(state);
        expect(result).toEqual(state.getProjetosAssinatura);
    });

    test('getProjetosAssinar', () => {
        const result = getters.getProjetosAssinar(state);
        expect(result).toEqual(state.getProjetosAssinar);
    });

    test('getProjetosEmAssinatura', () => {
        const result = getters.getProjetosEmAssinatura(state);
        expect(result).toEqual(state.getProjetosEmAssinatura);
    });

    test('getProjetosHistorico', () => {
        const result = getters.getProjetosHistorico(state);
        expect(result).toEqual(state.getProjetosHistorico);
    });

    test('getProjetosLaudoFinal', () => {
        const result = getters.getProjetosLaudoFinal(state);
        expect(result).toEqual(state.getProjetosLaudoFinal);
    });

    test('getProjetosLaudoAssinar', () => {
        const result = getters.getProjetosLaudoAssinar(state);
        expect(result).toEqual(state.getProjetosLaudoAssinar);
    });

    test('getProjetosLaudoEmAssinatura', () => {
        const result = getters.getProjetosLaudoEmAssinatura(state);
        expect(result).toEqual(state.getProjetosLaudoEmAssinatura);
    });

    test('dadosHistoricoEncaminhamento', () => {
        const result = getters.dadosHistoricoEncaminhamento(state);
        expect(result).toEqual(state.dadosHistoricoEncaminhamento);
    });

    test('planilha', () => {
        const result = getters.planilha(state);
        expect(result).toEqual(state.planilha);
    });

    test('consolidacaoAnalise', () => {
        const result = getters.consolidacaoAnalise(state);
        expect(result).toEqual(state.consolidacaoAnalise);
    });

    test('projetoAnalise', () => {
        const result = getters.projetoAnalise(state);
        expect(result).toEqual(state.projetoAnalise);
    });

    test('dadosMenu', () => {
        const result = getters.dadosMenu(state);
        expect(result).toEqual(state.dadosTabela);
    });

    test('registro', () => {
        const result = getters.registro(state);
        expect(result).toEqual(state.registroAtivo);
    });

    test('consolidacaoComprovantes', () => {
        const result = getters.consolidacaoComprovantes(state);
        expect(result).toEqual(state.consolidacaoComprovantes);
    });

    test('dadosDestinatarios', () => {
        const result = getters.dadosDestinatarios(state);
        expect(result).toEqual(state.dadosDestinatarios);
    });

    test('proponente', () => {
        const result = getters.proponente(state);
        expect(result).toEqual(state.proponente);
    });

    test('parecer', () => {
        const result = getters.parecer(state);
        expect(result).toEqual(state.parecer);
    });

    test('projeto', () => {
        const result = getters.projeto(state);
        expect(result).toEqual(state.projeto);
    });

    test('tipoAvaliacao', () => {
        const result = getters.tipoAvaliacao(state);
        expect(result).toEqual(state.tipoAvaliacao);
    });

    test('getParecerLaudoFinal', () => {
        const result = getters.getParecerLaudoFinal(state);
        expect(result).toEqual(state.getParecerLaudoFinal);
    });

    test('getProjetosFinalizados', () => {
        const result = getters.getProjetosFinalizados(state);
        expect(result).toEqual(state.projetosFinalizados);
    });

    test('dadosItemComprovacao', () => {
        const result = getters.dadosItemComprovacao(state);
        expect(result).toEqual(state.dadosItemComprovacao);
    });
});
