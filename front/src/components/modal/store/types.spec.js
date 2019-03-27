import * as types from './types';

describe('PrestacaoDeContas types', () => {
    test('MODAL_OPEN', () => {
        expect(types.MODAL_OPEN).toEqual('MODAL_OPEN');
    });

    test('MODAL_CLOSE', () => {
        expect(types.MODAL_CLOSE).toEqual('MODAL_CLOSE');
    });
});
