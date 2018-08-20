import { mutations } from './mutations';

describe('Projeto Mutations', () => {
    let state;
    let defaultState;
    let projeto;
    let transferenciaRecursos;

    beforeEach(() => {
        defaultState = {
            projeto: {
                IdPRONAC: '',
                Item: '',
                NomeProjeto: '',
            },
        };

        state = Object.assign({}, defaultState);

        projeto = {
            IdPRONAC: '132451',
            Item: 'Hospedagem sem Alimentação',
            NomeProjeto: 'Criança Para Vida - 15 anos',
        };

        transferenciaRecursos = {
            idPronacTransferidor: 1,
            PronacTransferidor: 111111,
            NomeProjetoTranferidor: 'Criança Para Vida - 15 anos',
            idPronacRecebedor: 2,
            PronacRecebedor: 222222,
            NomeProjetoRecedor: 'Criança Para Vida - 15 anos',
            dtRecebimento: new Date(),
            vlRecebido: parseFloat('1000000'),
        };
    });

    test('SET_PROJETO', () => {
        mutations.SET_PROJETO(state, projeto);
        expect(state.projeto).toEqual(projeto);
    });

    test('SET_TRANSFERENCIA_RECURSOS', () => {
        mutations.SET_TRANSFERENCIA_RECURSOS(state, transferenciaRecursos);
        expect(state.transferenciaRecursos).toEqual(transferenciaRecursos);
    });
});
