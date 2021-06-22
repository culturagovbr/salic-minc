import * as getters from './getters';

describe('Foo getters', () => {
    let state;

    beforeEach(() => {
        state = {
            dadosTabela: [],
            registroAtivo: {},
        };
    });

    test('dadosTabela', () => {
        const result = getters.dadosTabela(state);
        expect(result).toEqual(state.dadosTabela);
    });

    test('registro', () => {
        const result = getters.registro(state);
        expect(result).toEqual(state.registroAtivo);
    });
});
