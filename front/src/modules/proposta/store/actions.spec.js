import * as actions from './actions';
import * as propostaHelperAPI from '@/helpers/api/Proposta';
import axios from 'axios';

jest.mock('axios');

describe('Proposta actions', () => {
    let commit;
    let mockReponse;

    describe('buscaLocalRealizacaoDeslocamento', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        localRealizacaoDeslocamento: {
                            idProjeto: '273246',
                            idPais: '31',
                            idDeslocamento: '306706',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(propostaHelperAPI, 'buscaLocalRealizacaoDeslocamento');
            actions.buscaLocalRealizacaoDeslocamento({ commit });
        });

        test('it calls propostaHelperAPI.buscaLocalRealizacaoDeslocamento', () => {
            expect(propostaHelperAPI.buscaLocalRealizacaoDeslocamento).toHaveBeenCalled();
        });

        test('it is commit to buscaLocalRealizacaoDeslocamento', (done) => {
            const localRealizacaoDeslocamento = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_LOCAL_REALIZACAO_DESLOCAMENTO', localRealizacaoDeslocamento.data);
        });
    });

    describe('buscaFontesDeRecursos', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        fontesDeRecursos: {
                            Descricao: 'Incentivo Fiscal Federal',
                            Valor: '354.726,00',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(propostaHelperAPI, 'buscaFontesDeRecursos');
            actions.buscaFontesDeRecursos({ commit });
        });

        test('it calls propostaHelperAPI.buscaFontesDeRecursos', () => {
            expect(propostaHelperAPI.buscaFontesDeRecursos).toHaveBeenCalled();
        });

        test('it is commit to buscaFontesDeRecursos', (done) => {
            const fontesDeRecursos = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_FONTES_DE_RECURSOS', fontesDeRecursos.data);
        });
    });

    describe('buscaDocumentos', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        documentos: {
                            CodigoDocumento: '27',
                            Codigo: '59213',
                            idDocumentosAgentes: '228068',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(propostaHelperAPI, 'buscaDocumentos');
            const dados = {
                idPreProjeto: 273246,
                idAgente: 59213,
            };
            actions.buscaDocumentos({ commit }, dados);
        });

        test('it calls propostaHelperAPI.buscaDocumentos', () => {
            expect(propostaHelperAPI.buscaDocumentos).toHaveBeenCalled();
        });

        test('it is commit to buscaDocumentos', (done) => {
            const documentos = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DOCUMENTOS', documentos.data);
        });
    });

    describe('buscarDadosProposta', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        dadosProposta: {
                            idPreProjeto: '273246',
                            idAgente: '59213',
                            idUsuario: '59731',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(propostaHelperAPI, 'buscarDadosProposta');
            const idPreProjeto = 273246;
            actions.buscarDadosProposta({ commit }, idPreProjeto);
        });

        test('it calls propostaHelperAPI.buscarDadosProposta', () => {
            expect(propostaHelperAPI.buscarDadosProposta).toHaveBeenCalled();
        });

        test('it is commit to buscarDadosProposta', (done) => {
            const dadosProposta = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_PROPOSTA', dadosProposta.data);
        });
    });

    describe('buscarHistoricoSolicitacoes', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items:{
                            dadosSolicitacao: {
                                idProjeto: '282177',
                                idSolicitacao: '3267',
                                idSolicitante: '285582',
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(propostaHelperAPI, 'buscarHistoricoSolicitacoes');
            const idProjeto = 273246;
            actions.buscarHistoricoSolicitacoes({ commit }, idProjeto);
        });

        test('it calls propostaHelperAPI.buscarHistoricoSolicitacoes', () => {
            expect(propostaHelperAPI.buscarHistoricoSolicitacoes).toHaveBeenCalled();
        });

        test('it is commit to buscarHistoricoSolicitacoes', (done) => {
            const dadosSolicitacao = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_HISTORICO_SOLICITACOES', dadosSolicitacao.data.items);
        });
    });

    describe('buscarHistoricoEnquadramento', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items:{
                            dadosEnquadramento: {
                                lines:{
                                    org_sigla: 'CNIC',
                                    usu_nome: 'Maricene A Gregorut',
                                },
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(propostaHelperAPI, 'buscarHistoricoEnquadramento');
            const idPreProjeto = 282177;
            actions.buscarHistoricoEnquadramento({ commit }, idPreProjeto);
        });

        test('it calls propostaHelperAPI.buscarHistoricoEnquadramento', () => {
            expect(propostaHelperAPI.buscarHistoricoEnquadramento).toHaveBeenCalled();
        });

        test('it is commit to buscarHistoricoEnquadramento', (done) => {
            const dadosEnquadramento = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_HISTORICO_ENQUADRAMENTO', dadosEnquadramento.data.items);
        });
    });

});
