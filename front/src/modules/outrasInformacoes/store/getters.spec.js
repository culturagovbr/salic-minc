import * as getters from './getters';

describe('Projeto getters', () => {
    let state;

    beforeEach(() => {
        state = {
            projeto: {},
            proponente: {},
            planilhaHomologada: {},
            planilhaOriginal: {},
            planilhaReadequada: {},
            planilhaAutorizada: {},
            planilhaAdequada: {},
            transferenciaRecursos: {},
            certidoesNegativas: {},
            documentosAssinados: {},
            dadosComplementares: {},
            documentosAnexados: [],
            localRealizacaoDeslocamento: {},
            providenciaTomada: {},
            planoDistribuicaoIn2013: [],
            historicoEncaminhamento: {},
            tramitacaoDocumento: {},
            tramitacaoProjeto: {},
            ultimaTramitacao: {},
            planoDistribuicaoIn2017: [],
            diligenciaProposta: {},
            diligenciaAdequacao: {},
            diligenciaProjeto: {},
            diligencia: {},
        };
    });

    test('projeto', () => {
        const result = getters.projeto(state);
        expect(result).toEqual(state.projeto);
    });

    test('proponente', () => {
        const result = getters.proponente(state);
        expect(result).toEqual(state.proponente);
    });

    test('planilhaHomologada', () => {
        const result = getters.planilhaHomologada(state);
        expect(result).toEqual(state.planilhaHomologada);
    });

    test('planilhaOriginal', () => {
        const result = getters.planilhaOriginal(state);
        expect(result).toEqual(state.planilhaOriginal);
    });

    test('planilhaReadequada', () => {
        const result = getters.planilhaReadequada(state);
        expect(result).toEqual(state.planilhaReadequada);
    });

    test('planilhaAutorizada', () => {
        const result = getters.planilhaAutorizada(state);
        expect(result).toEqual(state.planilhaAutorizada);
    });

    test('planilhaAdequada', () => {
        const result = getters.planilhaAdequada(state);
        expect(result).toEqual(state.planilhaAdequada);
    });

    test('transferenciaRecursos', () => {
        const result = getters.transferenciaRecursos(state);
        expect(result).toEqual(state.transferenciaRecursos);
    });

    test('certidoesNegativas', () => {
        const result = getters.certidoesNegativas(state);
        expect(result).toEqual(state.certidoesNegativas);
    });

    test('documentosAssinados', () => {
        const result = getters.documentosAssinados(state);
        expect(result).toEqual(state.documentosAssinados);
    });

    test('dadosComplementares', () => {
        const result = getters.dadosComplementares(state);
        expect(result).toEqual(state.dadosComplementares);
    });

    test('documentosAnexados', () => {
        const result = getters.documentosAnexados(state);
        expect(result).toEqual(state.documentosAnexados);
    });

    test('localRealizacaoDeslocamento', () => {
        const result = getters.localRealizacaoDeslocamento(state);
        expect(result).toEqual(state.localRealizacaoDeslocamento);
    });

    test('providenciaTomada', () => {
        const result = getters.providenciaTomada(state);
        expect(result).toEqual(state.providenciaTomada);
    });

    test('planoDistribuicaoIn2013', () => {
        const result = getters.planoDistribuicaoIn2013(state);
        expect(result).toEqual(state.planoDistribuicaoIn2013);
    });

    test('historicoEncaminhamento', () => {
        const result = getters.historicoEncaminhamento(state);
        expect(result).toEqual(state.historicoEncaminhamento);
    });

    test('tramitacaoDocumento', () => {
        const result = getters.tramitacaoDocumento(state);
        expect(result).toEqual(state.tramitacaoDocumento);
    });

    test('tramitacaoProjeto', () => {
        const result = getters.tramitacaoProjeto(state);
        expect(result).toEqual(state.tramitacaoProjeto);
    });

    test('ultimaTramitacao', () => {
        const result = getters.ultimaTramitacao(state);
        expect(result).toEqual(state.ultimaTramitacao);
    });

    test('planoDistribuicaoIn2017', () => {
        const result = getters.planoDistribuicaoIn2017(state);
        expect(result).toEqual(state.planoDistribuicaoIn2017);
    });

    test('diligenciaProposta', () => {
        const result = getters.diligenciaProposta(state);
        expect(result).toEqual(state.diligenciaProposta);
    });

    test('diligenciaAdequacao', () => {
        const result = getters.diligenciaAdequacao(state);
        expect(result).toEqual(state.diligenciaAdequacao);
    });

    test('diligenciaProjeto', () => {
        const result = getters.diligenciaProjeto(state);
        expect(result).toEqual(state.diligenciaProjeto);
    });

    test('diligencia', () => {
        const result = getters.diligencia(state);
        expect(result).toEqual(state.diligencia);
    });
});
