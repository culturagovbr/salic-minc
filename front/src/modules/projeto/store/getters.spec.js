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
});
