import * as API from '@/helpers/api/AvaliacaoResultados';
import * as actions from './actions';
import axios from 'axios';

jest.mock('axios');

describe('Testes Actions - Avaliação de Resultados', () => {
    let commit;
    let dispatch;
    let mockReponse;
    let params;
    let tecnico;

    describe('dadosMenu', () => {
        beforeEach(() => {
            mockReponse = {
                data: [
                    {
                        analise: {
                            id: 'analise',
                            label: 'Análise',
                            title: 'Ir para Análise',
                        },
                    },
                ],
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'dadosMenu');
            actions.dadosMenu({ commit });
        });

        test('it is commit to projetosParaDistribuir', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_REGISTROS_TABELA', mockReponse.data.data);
        });

        test('it calls API.projetosRevisao', () => {
            expect(API.dadosMenu).toHaveBeenCalled();
        });
    });

    describe('projetosParaDistribuir - Aba "Encaminhar" ', () => {
        beforeEach(() => {
            mockReponse = {
                data: [
                    {
                        Pronac: '1410398',
                        PRONAC: '1410398',
                        NomeProjeto: 'Porto Verão Alegre 2015',
                        cdSituacao: 'E68',
                        Situacao: 'E68',
                        UfProjeto: 'RS',
                        IdPRONAC: '185373',
                        Prioridade: '0',
                        idPronac: '185373',
                    },
                ],
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'obterProjetosParaDistribuir');
            actions.projetosParaDistribuir({ commit });
        });

        test('it is commit to projetosParaDistribuir', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_PROJETOS_PARA_DISTRIBUIR', mockReponse.data);
        });

        test('it calls API.projetosRevisao', () => {
            expect(API.obterProjetosParaDistribuir).toHaveBeenCalled();
        });
    });

    describe('obterDadosTabelaTecnico - Aba "Em Análise" ', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: [
                        {
                            NomeProjeto: 'Manutenção das atividades da Orquestra Sinfônica Jovem de Nova Mutum',
                            Situacao: 'E27',
                            UfProjeto: 'MT',
                            PRONAC: '456789',
                            idPronac: '168213',
                            usu_nome: 'Rômulo Menhô Barbosa',
                        },
                    ],
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            params = {
                estadoid: 5,
                idAgente: 123,
            };

            commit = jest.fn();

            jest.spyOn(API, 'obterDadosTabelaTecnico');
            actions.obterDadosTabelaTecnico({ commit }, params);
        });

        test('it is commit to obterDadosTabelaTecnico', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('PROJETOS_AVALIACAO_TECNICA', mockReponse.data.data);
        });

        test('it calls API.obterDadosTabelaTecnico', () => {
            expect(API.obterDadosTabelaTecnico).toHaveBeenCalled();
        });
    });

    describe('projetosAssinatura - Aba "Assinar" ', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: [
                        {
                            NomeProjeto: 'Manutenção das atividades da Orquestra Sinfônica Jovem de Nova Mutum',
                            Situacao: 'E27',
                            UfProjeto: 'MT',
                            PRONAC: '456789',
                            idPronac: '168213',
                            usu_nome: 'Rômulo Menhô Barbosa',
                        },
                    ],
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            params = {
                estadoid: 6,
                idAgente: 123,
            };

            commit = jest.fn();

            jest.spyOn(API, 'projetosRevisao');
            actions.projetosFinalizados({ commit }, params);
        });

        test('it is commit to projetosFinalizados', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_PROJETOS_FINALIZADOS', mockReponse.data.data);
        });

        test('it calls API.projetosRevisao', () => {
            expect(API.obterDadosTabelaTecnico).toHaveBeenCalled();
        });
    });

    describe('projetosAssinatura - Aba "Histórico" ', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: [
                        {
                            NomeProjeto: 'Manutenção das atividades da Orquestra Sinfônica Jovem de Nova Mutum',
                            Situacao: 'E27',
                            UfProjeto: 'MT',
                            PRONAC: '456789',
                            idPronac: '168213',
                            usu_nome: 'Rômulo Menhô Barbosa',
                        },
                    ],
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            params = { estado: 'historico' };

            commit = jest.fn();

            jest.spyOn(API, 'obterProjetosAssinatura');
            actions.projetosAssinatura({ commit }, params);
        });

        test('it is commit to projetosFinalizados', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_PROJETOS_HISTORICO', mockReponse.data.data);
        });

        test('it calls API.projetosRevisao', () => {
            expect(API.obterProjetosAssinatura).toHaveBeenCalled();
            expect(API.obterProjetosAssinatura).toHaveBeenCalledWith(params);
        });
    });

    describe('obter lista de Técnicos - Aba "Encaminhar" ', () => {
        beforeEach(() => {
            commit = jest.fn();

            jest.spyOn(API, 'obterDestinatarios');
            actions.obterDestinatarios({ commit });

            tecnico = [{
                usu_codigo: 6087,
                usu_nome: 'Adilson S da Silva',
                idperfil: 124,
                idAgente: 6087,
            }];
        });

        test('it is commit to projetosFinalizados', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('DESTINATARIOS_ENCAMINHAMENTO', tecnico.items);
        });

        test('it calls API.obterDestinatarios', () => {
            expect(API.obterDestinatarios).toHaveBeenCalled();
        });
    });

    describe('Obter Histórico de Encaminhamentos', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: [
                        {
                            PRONAC: '138419',
                            NomeProjeto: 'Manutenção das atividades da Orquestra Sinfônica Jovem de Nova Mutum',
                            dtInicioEncaminhamento: '10/15/2018',
                            dsJustificativa: 'Justificando',
                            NomeOrigem: 'Rômulo Menhô Barbosa',
                            NomeDestino: 'Rômulo Menhô Barbosa',
                        },
                    ],
                },
            };

            axios.get.mockResolvedValue(mockReponse);


            commit = jest.fn();

            params = {
                idPronac: 168213,
            };

            jest.spyOn(API, 'obterHistoricoEncaminhamento');
            actions.obterHistoricoEncaminhamento({ commit }, params);
        });

        test('it is commit to projetosFinalizados', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('HISTORICO_ENCAMINHAMENTO', mockReponse.items);
        });

        test('it calls API.obterDestinatarios', () => {
            expect(API.obterDestinatarios).toHaveBeenCalled();
        });
    });

    describe('Dados da Planilha', () => {
        beforeEach(() => {
            mockReponse = require('../mocks/planilhaAnaliseFiltros.json');

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            params = {
                idPronac: 159934,
            };

            jest.spyOn(API, 'planilha');
            actions.planilha({ commit }, params);
        });

        test('it is commit to planilha', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('GET_PLANILHA', mockReponse.data);
        });

        test('it calls API.planilha', () => {
            expect(API.planilha).toHaveBeenCalled();
        });
    });

    describe('Analisar Projeto', () => {
        beforeEach(() => {
            mockReponse = {
                items: {
                    nomeProjeto: 'Manutenção das atividades da Orquestra Sinfônica Jovem de Nova Mutum',
                    vlTotalComprovar: 308722.05,
                    vlAprovado: 563996,
                    vlComprovado: 255273.95,
                    pronac: '138419',
                    diligencia: false,
                    estado: null,
                    documento: [],
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            params = {
                idPronac: 168213,
            };

            jest.spyOn(API, 'projetoAnalise');
            actions.projetoAnalise({ commit }, params);
        });

        test('it is commit to projetoAnalise', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('GET_PROJETO_ANALISE', mockReponse.data);
        });

        test('it calls API.projetoAnalise', () => {
            expect(API.projetoAnalise).toHaveBeenCalled();
        });
    });

    describe('setConsolidacaoAnalise', () => {
        beforeEach(() => {
            mockReponse = require('../mocks/ConsolidacaoAnalise.json');

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            params = {
                idPronac: 168213,
            };

            jest.spyOn(API, 'consolidacaoAnalise');
            actions.consolidacaoAnalise({ commit }, params);
        });

        test('it is commit to consolidacaoAnalise', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('GET_CONSOLIDACAO_ANALISE', mockReponse.data);
        });

        test('it calls API.consolidacaoAnalise', () => {
            expect(API.consolidacaoAnalise).toHaveBeenCalled();
        });
    });

    describe('Devolver Projeto', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            idPronac: '138419',
                            atual: '6',
                            proximo: '5',
                            idTipoDoAtoAdministrativo: '622',
                            usuario: {
                                grupo_ativo: '125',
                            },
                        },
                },
            };

            axios.post.mockResolvedValue(mockReponse);

            function FormDataMock() {
                this.append = jest.fn();
            }

            global.FormData = FormDataMock;


            commit = jest.fn();
            dispatch = jest.fn();

            jest.spyOn(API, 'alterarEstado');
            actions.devolverProjeto({ commit, dispatch }, mockReponse.data.data);
        });

        test('it is commit to devolverProjeto', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_PROJETOS_FINALIZADOS', {});
            expect(commit).toHaveBeenCalledWith('SET_DEVOLVER_PROJETO', mockReponse.data);
        });

        test('it calls API.alterarEstado', () => {
            expect(API.alterarEstado).toHaveBeenCalled();
        });
    });

    describe('getLaudoFinal', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            idPronac: '138419',
                        },
                },
            };
            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'obterLaudoFinal');
            actions.getLaudoFinal({ commit }, mockReponse.data.data);
        });

        test('it is commit to getLaudoFinal', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('GET_PARECER_LAUDO_FINAL', mockReponse.data.data);
        });

        test('it calls API.obterLaudoFinal', () => {
            expect(API.obterLaudoFinal).toHaveBeenCalled();
        });
    });

    describe('salvarLaudoFinal', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            idPronac: '138419',
                            siManifestacao: 'A',
                            dsLaudoFinal: undefined,
                        },
                },
            };

            axios.post.mockResolvedValue(mockReponse);

            function FormDataMock() {
                this.append = jest.fn();
            }

            global.FormData = FormDataMock;

            commit = jest.fn();

            jest.spyOn(API, 'criarParecerLaudoFinal');
            actions.salvarLaudoFinal({ commit }, mockReponse.data.data);
        });

        test('it is commit to finalizarLaudoFinal', (done) => {
            const config = { ativo: true, color: 'success', text: 'Salvo com sucesso!' };
            const root = { root: true };

            done();
            expect(commit).toHaveBeenCalledWith('noticias/SET_DADOS', config, root);
        });

        test('it calls API.criarParecerLaudoFinal', () => {
            expect(API.criarParecerLaudoFinal).toHaveBeenCalled();
        });
    });

    describe('finalizarLaudoFinal', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            idPronac: '138419',
                            idtipodoatoadministrativo: 623,
                            atual: 10,
                            proximo: 14,
                            idLaudoFinal: 141516,
                            siManifestacao: 'A',
                            dsLaudoFinal: undefined,
                        },
                },
            };

            axios.post.mockResolvedValue(mockReponse);

            function FormDataMock() {
                this.append = jest.fn();
            }

            global.FormData = FormDataMock;

            commit = jest.fn();

            jest.spyOn(API, 'alterarEstado');
            actions.finalizarLaudoFinal({ commit }, mockReponse.data.data);
        });

        test('it is commit to finalizarLaudoFinal', (done) => {
            const config = { ativo: true, color: 'success', text: 'Finalizado com sucesso!' };
            const root = { root: true };

            done();
            expect(commit).toHaveBeenCalledWith('noticias/SET_DADOS', config, root);
        });

        test('it calls API.alterarEstado', () => {
            expect(API.alterarEstado).toHaveBeenCalled();
        });
    });

    describe('enviarDiligencia', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            idPronac: '138419',
                            tpDiligencia: 'Diligência',
                            solicitacao: 'Diligência',
                        },
                },
            };

            axios.post.mockResolvedValue(mockReponse);

            function FormDataMock() {
                this.append = jest.fn();
            }

            global.FormData = FormDataMock;

            jest.spyOn(API, 'criarDiligencia');
            actions.enviarDiligencia({}, mockReponse.data.data);
        });

        test('it calls API.criarDiligencia', () => {
            expect(API.criarDiligencia).toHaveBeenCalled();
            expect(API.criarDiligencia).toHaveBeenCalledWith(mockReponse.data.data);
        });
    });

    describe('obterProjetosLaudoFinal', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            estadoId: 10,
                        },
                },
            };
            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'obterProjetosLaudoFinal');
            actions.obterProjetosLaudoFinal({ commit }, mockReponse.data.data);
        });

        test('it is commit to obterProjetosLaudoFinal', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_PROJETOS_LAUDO_FINAL', mockReponse.data.data);
        });

        test('it calls API.obterProjetosLaudoFinal', () => {
            expect(API.obterProjetosLaudoFinal).toHaveBeenCalled();
        });
    });

    describe('obterProjetosLaudoAssinar', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            estadoId: 14,
                        },
                },
            };
            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'obterProjetosLaudoFinal');
            actions.obterProjetosLaudoAssinar({ commit }, mockReponse.data.data);
        });

        test('it is commit to obterProjetosLaudoAssinar', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_PROJETOS_LAUDO_ASSINAR', mockReponse.data.data);
        });

        test('it calls API.obterProjetosLaudoFinal', () => {
            expect(API.obterProjetosLaudoFinal).toHaveBeenCalled();
        });
    });

    describe('obterProjetosLaudoEmAssinatura', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            estadoId: 11,
                        },
                },
            };
            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'obterProjetosLaudoFinal');
            actions.obterProjetosLaudoEmAssinatura({ commit }, mockReponse.data.data);
        });

        test('it is commit to obterProjetosLaudoEmAssinatura', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_PROJETOS_LAUDO_EM_ASSINATURA', mockReponse.data.data);
        });

        test('it calls API.obterProjetosLaudoFinal', () => {
            expect(API.obterProjetosLaudoFinal).toHaveBeenCalled();
        });
    });

    describe('obterProjetosLaudoFinalizados', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            estadoId: 12,
                        },
                },
            };
            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'obterProjetosLaudoFinal');
            actions.obterProjetosLaudoFinalizados({ commit }, mockReponse.data.data);
        });

        test('it is commit to obterProjetosLaudoFinalizados', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_PROJETOS_LAUDO_FINALIZADOS', mockReponse.data.data);
        });

        test('it calls API.obterProjetosLaudoFinal', () => {
            expect(API.obterProjetosLaudoFinal).toHaveBeenCalled();
        });
    });

    describe('buscarComprovantes', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            uf: 'DF',
                            idPronac: '168192',
                            codigoCidade: '13',
                            codigoProduto: '18181818',
                            stItemAvaliado: '4',
                            codigoEtapa: '122',
                            idPlanilhaItens: '20',
                        },
                },
            };
            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'buscarComprovantes');
            actions.buscarComprovantes({ commit }, mockReponse.data.data);
        });

        test('it is commit to buscarComprovantes', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SET_COMPROVANTES', mockReponse.data.data);
        });

        test('it calls API.buscarComprovantes', () => {
            expect(API.buscarComprovantes).toHaveBeenCalled();
        });
    });

    describe('projetosAssinarCoordenador', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            idPronac: '168192',
                            estadoId: '9',
                        },
                },
            };
            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'projetosPorEstado');
            actions.projetosAssinarCoordenador({ commit });
        });

        test('it is commit to projetosAssinarCoordenador', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SYNC_PROJETOS_ASSINAR_COORDENADOR', mockReponse.data.data);
        });

        test('it calls API.projetosPorEstado', () => {
            expect(API.projetosPorEstado).toHaveBeenCalledWith({ estadoid: 9 });
            expect(API.projetosPorEstado).toHaveBeenCalled();
        });
    });

    describe('projetosAssinarCoordenadorGeral', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data:
                        {
                            idPronac: '168192',
                            estadoId: '15',
                        },
                },
            };
            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();

            jest.spyOn(API, 'projetosPorEstado');
            actions.projetosAssinarCoordenadorGeral({ commit });
        });

        test('it is commit to projetosAssinarCoordenadorGeral', (done) => {
            done();
            expect(commit).toHaveBeenCalledWith('SYNC_PROJETOS_ASSINAR_COORDENADOR_GERAL', mockReponse.data.data);
        });

        test('it calls API.projetosPorEstado', () => {
            expect(API.projetosPorEstado).toHaveBeenCalledWith({ estadoid: 15 });
            expect(API.projetosPorEstado).toHaveBeenCalled();
        });
    });
});
