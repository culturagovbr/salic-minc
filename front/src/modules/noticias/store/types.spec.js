import * as types from './types';

describe('Foo types', () => {
    test('SET_REGISTROS_TABELA', () => {
        expect(types.SET_REGISTROS_TABELA).toEqual('SET_REGISTROS_TABELA');
    });

    test('SET_REGISTRO_TABELA', () => {
        expect(types.SET_REGISTRO_TABELA).toEqual('SET_REGISTRO_TABELA');
    });

    test('SET_REGISTRO_ATIVO', () => {
        expect(types.SET_REGISTRO_ATIVO).toEqual('SET_REGISTRO_ATIVO');
    });

    test('ATUALIZAR_REGISTRO_TABELA', () => {
        expect(types.ATUALIZAR_REGISTRO_TABELA).toEqual('ATUALIZAR_REGISTRO_TABELA');
    });

    test('REMOVER_REGISTRO', () => {
        expect(types.REMOVER_REGISTRO).toEqual('REMOVER_REGISTRO');
    });
});
