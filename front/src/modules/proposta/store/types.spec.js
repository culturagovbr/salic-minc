import * as types from './types';

describe('Proposta types', () => {
    test('SET_LOCAL_REALIZACAO_DESLOCAMENTO', () => {
        expect(types.SET_LOCAL_REALIZACAO_DESLOCAMENTO).toEqual('SET_LOCAL_REALIZACAO_DESLOCAMENTO');
    });

    test('SET_FONTES_DE_RECURSOS', () => {
        expect(types.SET_FONTES_DE_RECURSOS).toEqual('SET_FONTES_DE_RECURSOS');
    });

    test('SET_DOCUMENTOS', () => {
        expect(types.SET_DOCUMENTOS).toEqual('SET_DOCUMENTOS');
    });

    test('SET_DADOS_PROPOSTA', () => {
        expect(types.SET_DADOS_PROPOSTA).toEqual('SET_DADOS_PROPOSTA');
    });

    test('SET_HISTORICO_SOLICITACOES', () => {
        expect(types.SET_HISTORICO_SOLICITACOES).toEqual('SET_HISTORICO_SOLICITACOES');
    });

    test('SET_HISTORICO_ENQUADRAMENTO', () => {
        expect(types.SET_HISTORICO_ENQUADRAMENTO).toEqual('SET_HISTORICO_ENQUADRAMENTO');
    });

});
