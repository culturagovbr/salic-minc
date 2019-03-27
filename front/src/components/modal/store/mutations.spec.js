import { mutations } from './mutations';

describe('Projeto Mutations', () => {
    let state;
    let defaultState;
    let modal;

    beforeEach(() => {
        defaultState = {
            modalAberta: false,
        };

        state = Object.assign({}, defaultState);

        modal = 'generic-modal';
    });

    test('MODAL_OPEN', () => {
        mutations.MODAL_OPEN(state, modal);
        expect(state.modalAberta).toEqual(modal);
    });

    test('MODAL_CLOSE', () => {
        mutations.MODAL_CLOSE(state);
        expect(state.modalAberta).toEqual('');
    });
});
