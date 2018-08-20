import axios from 'axios';
import * as actions from './actions';

jest.mock('axios');

describe('Projeto actions', () => {
    let commit;
    let modal;

    describe('modalOpen', () => {
        beforeEach(() => {
            modal = 'generic-modal';
            commit = jest.fn();
            actions.modalOpen({ commit }, modal);
        });

        test('it is commit to modalOpen', () => {
            expect(commit).toHaveBeenCalledWith('MODAL_OPEN', modal);
        });
    });

    describe('modalClose', () => {
        beforeEach(() => {
            modal = '';
            commit = jest.fn();
            actions.modalClose({ commit });
        });

        test('it is commit to modalClose', () => {
            expect(commit).toHaveBeenCalledWith('MODAL_CLOSE');
        });
    });
});
