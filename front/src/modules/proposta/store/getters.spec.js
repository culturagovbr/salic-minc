import * as getters from './getters';

describe('Proposta getters', () => {
    let state;

    beforeEach(() => {
        state = {
            localRealizacaoDeslocamento: {},
            fontesDeRecursos: {},
            documentos: {},
            proposta: {},
            historicoSolicitacoes: {},
            historicoEnquadramento: {},
        };
    });

    test('localRealizacaoDeslocamento', () => {
        const result = getters.localRealizacaoDeslocamento(state);
        expect(result).toEqual(state.localRealizacaoDeslocamento);
    });

    test('fontesDeRecursos', () => {
        const result = getters.fontesDeRecursos(state);
        expect(result).toEqual(state.fontesDeRecursos);
    });

    test('documentos', () => {
        const result = getters.documentos(state);
        expect(result).toEqual(state.documentos);
    });

    test('proposta', () => {
        const result = getters.proposta(state);
        expect(result).toEqual(state.proposta);
    });

    test('historicoSolicitacoes', () => {
        const result = getters.historicoSolicitacoes(state);
        expect(result).toEqual(state.historicoSolicitacoes);
    });

    test('historicoEnquadramento', () => {
        const result = getters.historicoEnquadramento(state);
        expect(result).toEqual(state.historicoEnquadramento);
    });
});
