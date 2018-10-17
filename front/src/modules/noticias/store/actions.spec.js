import * as fooHelperAPI from '@/helpers/api/Foo';
import * as actions from './actions';
import axios from 'axios';

jest.mock('axios');

describe('Foo actions', () => {
    let commit;
    let mockReponse;
    let registro;

    describe('obterDadosTabela', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: [
                        {
                            Codigo: 1,
                            DadoNr: 'Random String 1',
                        },
                    ],
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(fooHelperAPI, 'obterDadosTabela');
            actions.obterDadosTabela({ commit });

            registro = [{ Codigo: 1, DadoNr: 'Random String 1' }];
        });

        test('it is commit to obterDadosTabela', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_REGISTROS_TABELA', registro);
        });

        test('it calls fooHelperAPI.obterDadosTabela', () => {
            expect(fooHelperAPI.obterDadosTabela).toHaveBeenCalled();
        });
    });

    describe('criarRegistro', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        Codigo: 1,
                        DadoNr: 'Random String 1',
                    },
                },
            };

            axios.post.mockResolvedValue(mockReponse);

            function FormDataMock() {
                this.append = jest.fn();
            }

            global.FormData = FormDataMock;

            registro = { Codigo: 1, DadoNr: 'Random String 1' };

            commit = jest.fn();
            jest.spyOn(fooHelperAPI, 'criarRegistro');
            actions.criarRegistro({ commit }, registro);
        });

        test('it is commit to criarRegistro', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_REGISTRO_TABELA', registro);
        });

        test('it calls fooHelperAPI.criarRegistro', () => {
            expect(fooHelperAPI.criarRegistro).toHaveBeenCalled();
        });
    });

    describe('atualizarRegistro', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        Codigo: 1,
                        DadoNr: 'Random String 1',
                    },
                },
            };

            axios.post.mockResolvedValue(mockReponse);

            function FormDataMock() {
                this.append = jest.fn();
            }

            global.FormData = FormDataMock;

            registro = { Codigo: 1, DadoNr: 'Random String 1' };

            commit = jest.fn();
            jest.spyOn(fooHelperAPI, 'atualizarRegistro');
            actions.atualizarRegistro({ commit }, registro);
        });

        test('it is commit to atualizarRegistro', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('ATUALIZAR_REGISTRO_TABELA', registro);
        });

        test('it calls fooHelperAPI.atualizarRegistro', () => {
            expect(fooHelperAPI.atualizarRegistro).toHaveBeenCalled();
        });
    });

    describe('removerRegistro', () => {
        beforeEach(() => {
            mockReponse = { Codigo: 1, DadoNr: 'Random String 1' };

            commit = jest.fn();

            axios.delete.mockResolvedValue(mockReponse);

            registro = {
                Codigo: 1,
                DadoNr: 'Random String 1',
            };

            jest.spyOn(fooHelperAPI, 'removerRegistro');
            actions.removerRegistro({ commit }, registro);
        });

        test('it is commit to removerRegistro', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('REMOVER_REGISTRO', registro);
        });

        test('it calls fooHelperAPI.removerRegistro', () => {
            expect(fooHelperAPI.removerRegistro).toHaveBeenCalled();
        });
    });

    describe('setRegistroAtivo', () => {
        beforeEach(() => {
            commit = jest.fn();
            registro = { Codigo: 1, DadoNr: 'Random String 1' };
            actions.setRegistroAtivo({ commit }, registro);
        });

        test('it is commit to setRegistroAtivo', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_REGISTRO_ATIVO', registro);
        });
    });
});
