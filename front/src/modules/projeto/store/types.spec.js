import * as types from './types';

describe('PrestacaoDeContas types', () => {
    test('SET_PROJETO', () => {
        expect(types.SET_PROJETO).toEqual('SET_PROJETO');
    });

    test('SET_TRANSFERENCIA_RECURSOS', () => {
        expect(types.SET_TRANSFERENCIA_RECURSOS).toEqual('SET_TRANSFERENCIA_RECURSOS');
    });
});
