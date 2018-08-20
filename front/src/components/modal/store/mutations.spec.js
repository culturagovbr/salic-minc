import { mutations } from './mutations';

describe('Projeto Mutations', () => {
    let state;
    let defaultState;
    let modal;

    beforeEach(() => {
        defaultState = {
            isVisible: false,
        };

        state = Object.assign({}, defaultState);

        modal = 'generic-modal';
    });

    test('MODAL_OPEN', () => {
        mutations.MODAL_OPEN(state, modal);
        expect(state.isVisible).toEqual(modal);
    });

    test('MODAL_CLOSE', () => {
        mutations.MODAL_CLOSE(state);
        expect(state.isVisible).toEqual('');
    });
});
