import axios from 'axios';
import * as ProjetoHelperAPI from '@/helpers/api/Projeto';
import * as actions from './actions';

jest.mock('axios');

describe('Projeto actions', () => {
    let commit;
    let mockReponse;

    describe('buscaProjeto', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        projeto: {
                            IdPRONAC: '132451',
                            Item: 'Hospedagem sem Alimenta��o',
                            NomeProjeto: 'Crian�a Para Vida - 15 anos',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(ProjetoHelperAPI, 'buscaProjeto');
            actions.buscaProjeto({ commit });
        });

        test('it calls ProjetoHelperAPI.buscaProjeto', () => {
            expect(ProjetoHelperAPI.buscaProjeto).toHaveBeenCalled();
        });

        test('it is commit to buscaProjeto', (done) => {
            const projeto = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PROJETO', projeto.data);
        });
    });

    describe('buscarTransferenciaRecursos', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        transferenciaRecursos: {
                            idPronacTransferidor: 1,
                            PronacTransferidor: 111111,
                            NomeProjetoTranferidor: 'Criança Para Vida - 15 anos',
                            idPronacRecebedor: 2,
                            PronacRecebedor: 222222,
                            NomeProjetoRecedor: 'Criança Para Vida - 15 anos',
                            dtRecebimento: new Date(),
                            vlRecebido: parseFloat('1000000'),
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(ProjetoHelperAPI, 'buscarTransferenciaRecursos');
            const acao = 'transferidor';
            actions.buscarTransferenciaRecursos({ commit }, acao);
        });

        test('it calls ProjetoHelperAPI.buscarTransferenciaRecursos', () => {
            expect(ProjetoHelperAPI.buscarTransferenciaRecursos).toHaveBeenCalled();
        });

        test('it is commit to buscarTransferenciaRecursos', (done) => {
            const transferenciaRecursos = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_TRANSFERENCIA_RECURSOS', transferenciaRecursos.data);
        });
    });
});
