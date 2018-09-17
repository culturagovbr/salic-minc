import * as types from './types';

describe('PrestacaoDeContas types', () => {
    test('SET_PROJETO', () => {
        expect(types.SET_PROJETO).toEqual('SET_PROJETO');
    });

    test('SET_PROPONENTE', () => {
        expect(types.SET_PROPONENTE).toEqual('SET_PROPONENTE');
    });

    test('SET_PLANILHA_HOMOLOGADA', () => {
        expect(types.SET_PLANILHA_HOMOLOGADA).toEqual('SET_PLANILHA_HOMOLOGADA');
    });

    test('SET_PLANILHA_ORIGINAL', () => {
        expect(types.SET_PLANILHA_ORIGINAL).toEqual('SET_PLANILHA_ORIGINAL');
    });

    test('SET_PLANILHA_READEQUADA', () => {
        expect(types.SET_PLANILHA_READEQUADA).toEqual('SET_PLANILHA_READEQUADA');
    });

    test('SET_PLANILHA_AUTORIZADA', () => {
        expect(types.SET_PLANILHA_AUTORIZADA).toEqual('SET_PLANILHA_AUTORIZADA');
    });

    test('SET_PLANILHA_ADEQUADA', () => {
        expect(types.SET_PLANILHA_ADEQUADA).toEqual('SET_PLANILHA_ADEQUADA');
    });


    test('SET_TRANSFERENCIA_RECURSOS', () => {
        expect(types.SET_TRANSFERENCIA_RECURSOS).toEqual('SET_TRANSFERENCIA_RECURSOS');
    });
});
