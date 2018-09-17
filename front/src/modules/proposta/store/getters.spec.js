import * as getters from './getters';

describe('Proposta getters', () => {
    let state;

    beforeEach(() => {
        state = {
            localRealizacaoDeslocamento: {},
            fontesDeRecursos: {},
            documentos: {},
            proposta: {},
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
});
