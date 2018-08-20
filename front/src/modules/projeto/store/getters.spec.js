import * as getters from './getters';

describe('Projeto getters', () => {
    let state;

    beforeEach(() => {
        state = {
            projeto: {},
            transferenciaRecursos: {},
        };
    });

    test('projeto', () => {
        const result = getters.projeto(state);
        expect(result).toEqual(state.projeto);
    });

    test('transferenciaRecursos', () => {
        const result = getters.transferenciaRecursos(state);
        expect(result).toEqual(state.transferenciaRecursos);
    });
});
