import * as getters from './getters';

describe('modal getters', () => {
    let state;

    beforeEach(() => {
        state = {
            isVisible: {},
        };
    });

    test('default', () => {
        const result = getters.default(state);
        expect(result).toEqual(state.isVisible);
    });
});
