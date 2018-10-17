import { mutations } from './mutations';

describe('Foo Mutations', () => {
    let state;
    let defaultState;
    let dadosTabela;
    let registroAtivo;

    beforeEach(() => {
        defaultState = {
            dadosTabela: [
                {
                    Codigo: 1,
                    DadoNr: 'Random String 1',
                },
            ],
        };

        state = Object.assign({}, defaultState);

        dadosTabela = {
            Codigo: 1,
            DadoNr: 'Random String 1 updated',
        };
    });

    describe('when call mutation SET_REGISTROS_TABELA', () => {
        test('SET_REGISTROS_TABELA', () => {
            mutations.SET_REGISTROS_TABELA(state, dadosTabela);
            expect(state.dadosTabela).toEqual(dadosTabela);
        });
    });

    describe('when call mutation SET_REGISTRO_ATIVO', () => {
        test('SET_REGISTRO_ATIVO', () => {
            mutations.SET_REGISTRO_ATIVO(state, registroAtivo);
            expect(state.registroAtivo).toEqual(registroAtivo);
        });
    });

    describe('when call mutation SET_REGISTRO_TABELA', () => {
        beforeEach(() => {
            dadosTabela = {
                Codigo: 2,
                DadoNr: 'Random String 2',
            };
        });

        const resultado = [
            {
                Codigo: 1,
                DadoNr: 'Random String 1',
            },
            {
                Codigo: 2,
                DadoNr: 'Random String 2',
            },
        ];

        test('SET_REGISTRO_TABELA', () => {
            mutations.SET_REGISTRO_TABELA(state, dadosTabela);
            expect(state.dadosTabela).toEqual(resultado);
        });
    });

    describe('when call mutation ATUALIZAR_REGISTRO_TABELA', () => {
        test('ATUALIZAR_REGISTRO_TABELA', () => {
            mutations.ATUALIZAR_REGISTRO_TABELA(state, dadosTabela);
            expect(state.dadosTabela).toEqual([dadosTabela]);
        });
    });

    describe('when call mutation REMOVER_REGISTRO', () => {
        test('REMOVER_REGISTRO', () => {
            mutations.REMOVER_REGISTRO(state, dadosTabela);
            expect(state.dadosTabela).toEqual([]);
        });
    });
});
