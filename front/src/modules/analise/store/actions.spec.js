import axios from 'axios';
import * as projetoHelperAPI from '@/helpers/api/Projeto';
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
                            Item: 'Hospedagem sem Alimenta\xE7\xE3o',
                            NomeProjeto: 'Crian\xE7a Para Vida - 15 anos',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscaProjeto');
            actions.buscaProjeto({ commit });
        });

        test('it calls projetoHelperAPI.buscaProjeto', () => {
            expect(projetoHelperAPI.buscaProjeto).toHaveBeenCalled();
        });

        test('it is commit to buscaProjeto', (done) => {
            const projeto = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PROJETO', projeto.data);
        });
    });

    describe('buscaProponente', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        proponente: {
                            Proponente: 'Associa\xE7\xC3o Beneficiente Cultural Religiosa Centro Judaico do Brooklin',
                            idAgente: '24806',
                            TipoPessoa: 'Jur\xCDdica',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscaProponente');
            actions.buscaProponente({ commit });
        });

        test('it calls projetoHelperAPI.buscaProponente', () => {
            expect(projetoHelperAPI.buscaProponente).toHaveBeenCalled();
        });

        test('it is commit to buscaProponente', (done) => {
            const proponente = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PROPONENTE', proponente.data);
        });
    });

    describe('buscaPlanilhaHomologada', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        planilhaHomologada: {
                            tpPlanilha: 'CO',
                            IdPronac: '189786',
                            PRONAC: '150151',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscaPlanilhaHomologada');
            const idPronac = 123456;
            actions.buscaPlanilhaHomologada({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscaPlanilhaHomologada', () => {
            expect(projetoHelperAPI.buscaPlanilhaHomologada).toHaveBeenCalled();
        });

        test('it is commit to buscaPlanilhaHomologada', (done) => {
            const planilhaHomologada = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PLANILHA_HOMOLOGADA', planilhaHomologada.data);
        });
    });

    describe('buscaPlanilhaOriginal', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        planilhaOriginal: {
                            idPlanilhaProposta: '3675289',
                            FonteRecurso: 'Incentivo Fiscal Federal',
                            idEtapa: '2',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscaPlanilhaOriginal');
            const idPreProjeto = 273246;
            actions.buscaPlanilhaOriginal({ commit }, idPreProjeto);
        });

        test('it calls projetoHelperAPI.buscaPlanilhaOriginal', () => {
            expect(projetoHelperAPI.buscaPlanilhaOriginal).toHaveBeenCalled();
        });

        test('it is commit to buscaPlanilhaOriginal', (done) => {
            const planilhaOriginal = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PLANILHA_ORIGINAL', planilhaOriginal.data);
        });
    });

    describe('buscaPlanilhaReadequada', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        planilhaReadequada: {
                            tpPlanilha: 'RP',
                            IdPronac: '189786',
                            PRONAC: '150151',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscaPlanilhaReadequada');
            const idPronac = 123456;
            actions.buscaPlanilhaReadequada({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscaPlanilhaReadequada', () => {
            expect(projetoHelperAPI.buscaPlanilhaReadequada).toHaveBeenCalled();
        });

        test('it is commit to buscaPlanilhaReadequada', (done) => {
            const planilhaReadequada = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PLANILHA_READEQUADA', planilhaReadequada.data);
        });
    });

    describe('buscaPlanilhaAutorizada', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        planilhaAutorizada: {
                            tpPlanilha: 'CO',
                            idPronac: '200728',
                            PRONAC: '1510482',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscaPlanilhaAutorizada');
            const idPreProjeto = 273246;
            actions.buscaPlanilhaAutorizada({ commit }, idPreProjeto);
        });

        test('it calls projetoHelperAPI.buscaPlanilhaAutorizada', () => {
            expect(projetoHelperAPI.buscaPlanilhaAutorizada).toHaveBeenCalled();
        });

        test('it is commit to buscaPlanilhaAutorizada', (done) => {
            const planilhaAutorizada = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PLANILHA_AUTORIZADA', planilhaAutorizada.data);
        });
    });

    describe('buscaPlanilhaAdequada', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        planilhaAdequada: {
                            Seq: '28',
                            idPlanilhaProposta: '4913779',
                            idEtapa: '8',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscaPlanilhaAdequada');
            const idPreProjeto = 273246;
            actions.buscaPlanilhaAdequada({ commit }, idPreProjeto);
        });

        test('it calls projetoHelperAPI.buscaPlanilhaAdequada', () => {
            expect(projetoHelperAPI.buscaPlanilhaAdequada).toHaveBeenCalled();
        });

        test('it is commit to buscaPlanilhaAdequada', (done) => {
            const planilhaAdequada = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PLANILHA_ADEQUADA', planilhaAdequada.data);
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
                            NomeProjetoTranferidor: 'CrianÃ§a Para Vida - 15 anos',
                            idPronacRecebedor: 2,
                            PronacRecebedor: 222222,
                            NomeProjetoRecedor: 'CrianÃ§a Para Vida - 15 anos',
                            dtRecebimento: new Date(),
                            vlRecebido: parseFloat('1000000'),
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarTransferenciaRecursos');
            const acao = 'transferidor';
            actions.buscarTransferenciaRecursos({ commit }, acao);
        });

        test('it calls projetoHelperAPI.buscarTransferenciaRecursos', () => {
            expect(projetoHelperAPI.buscarTransferenciaRecursos).toHaveBeenCalled();
        });

        test('it is commit to buscarTransferenciaRecursos', (done) => {
            const transferenciaRecursos = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_TRANSFERENCIA_RECURSOS', transferenciaRecursos.data);
        });
    });

    describe('buscarCertidoesNegativas', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            certidoes: {
                                dsCertidao: 'Quita&ccedil;&atilde;o de Tributos Federais',
                                CodigoCertidao: 49,
                                Pronac: 160059,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarCertidoesNegativas');
            const idPronac = 216941;
            actions.buscarCertidoesNegativas({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarCertidoesNegativas', () => {
            expect(projetoHelperAPI.buscarCertidoesNegativas).toHaveBeenCalled();
        });

        test('it is commit to buscarCertidoesNegativas', (done) => {
            const certidoesNegativas = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_CERTIDOES_NEGATIVAS', certidoesNegativas.data.items);
        });
    });

    describe('buscarDocumentosAssinados', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            dsAtoAdministrativo: 'Parecer de Aprova&ccedil;&atilde;o Preliminar',
                            idDocumentoAssinatura: 3564,
                            pronac: 178894,
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarDocumentosAssinados');
            const idPronac = 216941;
            actions.buscarDocumentosAssinados({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarDocumentosAssinados', () => {
            expect(projetoHelperAPI.buscarDocumentosAssinados).toHaveBeenCalled();
        });

        test('it is commit to buscarDocumentosAssinados', (done) => {
            const documentosAssinados = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DOCUMENTOS_ASSINADOS', documentosAssinados.data.items);
        });
    });

    describe('buscarDadosComplementares', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            CustosVinculados: {
                                Descricao: 'Custos de Administra&ccedil;&atilde;o',
                                Percentual: 15,
                            },
                            Proposta: {
                                Objetivos: 'Objetivo espec&iacute;fico do projeto &eacute; a realiza&ccedil;&atilde;o de tr&ecirc;s atra&ccedil;&otilde;es',
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarDadosComplementares');
            const idPronac = 216941;
            actions.buscarDadosComplementares({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarDadosComplementares', () => {
            expect(projetoHelperAPI.buscarDadosComplementares).toHaveBeenCalled();
        });

        test('it is commit to buscarDadosComplementares', (done) => {
            const dadosComplementares = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DADOS_COMPLEMENTARES', dadosComplementares.data.items);
        });
    });

    describe('buscarDocumentosAnexados', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            documentos: {
                                Anexado: 'Documento do Proponente',
                                idArquivo: 180609,
                                AgenteDoc: 1,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarDocumentosAnexados');
            const idPronac = 216941;
            actions.buscarDocumentosAnexados({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarDocumentosAnexados', () => {
            expect(projetoHelperAPI.buscarDocumentosAnexados).toHaveBeenCalled();
        });

        test('it is commit to buscarDocumentosAnexados', (done) => {
            const documentosAnexados = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DOCUMENTOS_ANEXADOS', documentosAnexados.data.items);
        });
    });

    describe('buscarLocalRealizacaoDeslocamento', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            localRealizacoes: {
                                Descricao: 'Brasil',
                                UF: 'Santa Catarina',
                                Cidade: 'Conc&oacute;rdia',
                            },
                            Deslocamento: {
                                Qtde: 28,
                                PaisOrigem: 'Brasil',
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarLocalRealizacaoDeslocamento');
            const idPronac = 216941;
            actions.buscarLocalRealizacaoDeslocamento({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarLocalRealizacaoDeslocamento', () => {
            expect(projetoHelperAPI.buscarLocalRealizacaoDeslocamento).toHaveBeenCalled();
        });

        test('it is commit to buscarLocalRealizacaoDeslocamento', (done) => {
            const localRealizacaoDeslocamento = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_LOCAL_REALIZACAO_DESLOCAMENTO', localRealizacaoDeslocamento.data.items);
        });
    });

    describe('buscarProvidenciaTomada', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            providenciaTomada: {
                                Situacao: 'B01',
                                cnpjcpf: '08887383740',
                                ProvidenciaTomada: 'Proposta transformada em projeto cultural',
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarProvidenciaTomada');
            const idPronac = 216941;
            actions.buscarProvidenciaTomada({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarProvidenciaTomada', () => {
            expect(projetoHelperAPI.buscarProvidenciaTomada).toHaveBeenCalled();
        });

        test('it is commit to buscarProvidenciaTomada', (done) => {
            const providenciaTomada = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PROVIDENCIA_TOMADA', providenciaTomada.data.items);
        });
    });

    describe('buscarPlanoDistribuicaoIn2013', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            idPlanoDistribuicao: 171982,
                            idProjeto: 207951,
                            idProduto: 3,
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarPlanoDistribuicaoIn2013');
            const idPronac = 194617;
            actions.buscarPlanoDistribuicaoIn2013({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarPlanoDistribuicaoIn2013', () => {
            expect(projetoHelperAPI.buscarPlanoDistribuicaoIn2013).toHaveBeenCalled();
        });

        test('it is commit to buscarPlanoDistribuicaoIn2013', (done) => {
            const planoDistribuicaoIn2013 = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PLANO_DISTRIBUICAO_IN2013', planoDistribuicaoIn2013.data.items);
        });
    });

    describe('buscarHistoricoEncaminhamento', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            Encaminhamentos: {
                                Unidade: 'FUNARTE',
                                DtEnvio: '03/04/2018 00:00:00',
                                qtDias: 44,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarHistoricoEncaminhamento');
            const idPronac = 216941;
            actions.buscarHistoricoEncaminhamento({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarHistoricoEncaminhamento', () => {
            expect(projetoHelperAPI.buscarHistoricoEncaminhamento).toHaveBeenCalled();
        });

        test('it is commit to buscarHistoricoEncaminhamento', (done) => {
            const historicoEncaminhamento = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_HISTORICO_ENCAMINHAMENTO', historicoEncaminhamento.data.items);
        });
    });

    describe('buscarTramitacaoDocumento', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            dsTipoDocumento: 'Comunicado de Mecenato',
                            idDocumento: 453659,
                            idLote: 295184,
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarTramitacaoDocumento');
            const idPronac = 216941;
            actions.buscarTramitacaoDocumento({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarTramitacaoDocumento', () => {
            expect(projetoHelperAPI.buscarTramitacaoDocumento).toHaveBeenCalled();
        });

        test('it is commit to buscarTramitacaoDocumento', (done) => {
            const tramitacaoDocumento = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_TRAMITACAO_DOCUMENTO', tramitacaoDocumento.data.items);
        });
    });

    describe('buscarTramitacaoProjeto', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            Situacao: 'Cadastrado',
                            Origem: 'SEFIC/GEAAP/SUAPI/DIAAPI',
                            Destino: 'SEFIC/GEAR/SACAV',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarTramitacaoProjeto');
            const idPronac = 216941;
            actions.buscarTramitacaoProjeto({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarTramitacaoProjeto', () => {
            expect(projetoHelperAPI.buscarTramitacaoProjeto).toHaveBeenCalled();
        });

        test('it is commit to buscarTramitacaoProjeto', (done) => {
            const tramitacaoProjeto = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_TRAMITACAO_PROJETO', tramitacaoProjeto.data.items);
        });
    });

    describe('buscarUltimaTramitacao', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            Emissor: 'M&ordf; do Socorro Silva',
                            Receptor: 'Renata L.Oliveira',
                            Estado: 'Recebido',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarUltimaTramitacao');
            const idPronac = 216941;
            actions.buscarUltimaTramitacao({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarUltimaTramitacao', () => {
            expect(projetoHelperAPI.buscarUltimaTramitacao).toHaveBeenCalled();
        });

        test('it is commit to buscarUltimaTramitacao', (done) => {
            const ultimaTramitacao = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_ULTIMA_TRAMITACAO', ultimaTramitacao.data.items);
        });
    });

    describe('buscarPlanoDistribuicaoIn2017', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            planodistribuicaoproduto: {
                                idPlanoDistribuicao: 229891,
                                idProjeto: 273246,
                                idProduto: 19,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarPlanoDistribuicaoIn2017');
            const idPronac = 216941;
            actions.buscarPlanoDistribuicaoIn2017({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarPlanoDistribuicaoIn2017', () => {
            expect(projetoHelperAPI.buscarPlanoDistribuicaoIn2017).toHaveBeenCalled();
        });

        test('it is commit to buscarPlanoDistribuicaoIn2017', (done) => {
            const planoDistribuicaoIn2017 = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_PLANO_DISTRIBUICAO_IN2017', planoDistribuicaoIn2017.data.items);
        });
    });

    describe('buscarDiligenciaProposta', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            nomeProjeto: 'FOTOATIVIDADES',
                            dataSolicitacao: '04/04/2017',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarDiligenciaProposta');
            const idPreProjeto = 245047;
            const idAvaliacaoProposta = 407842;
            const value = { idPreProjeto, idAvaliacaoProposta };

            actions.buscarDiligenciaProposta({ commit }, value);
        });

        test('it calls projetoHelperAPI.buscarDiligenciaProposta', () => {
            expect(projetoHelperAPI.buscarDiligenciaProposta).toHaveBeenCalled();
        });

        test('it is commit to buscarDiligenciaProposta', (done) => {
            const diligenciaProposta = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DILIGENCIA_PROPOSTA', diligenciaProposta.data.items);
        });
    });

    describe('buscarDiligenciaAdequacao', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            idAvaliarAdequacaoProjeto: 1452,
                            dtAvaliacao: '06/06/2018',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarDiligenciaAdequacao');
            const idPronac = 209561;
            const idAvaliarAdequacaoProjeto = 1452;
            const value = { idPronac, idAvaliarAdequacaoProjeto };
            actions.buscarDiligenciaAdequacao({ commit }, value);
        });

        test('it calls projetoHelperAPI.buscarDiligenciaAdequacao', () => {
            expect(projetoHelperAPI.buscarDiligenciaAdequacao).toHaveBeenCalled();
        });

        test('it is commit to buscarDiligenciaAdequacao', (done) => {
            const diligenciaAdequacao = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DILIGENCIA_ADEQUACAO', diligenciaAdequacao.data.items);
        });
    });

    describe('buscarDiligenciaProjeto', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            arquivos: {
                                idArquivo: 1272611,
                            },
                            nomeProjeto: 'FOTOATIVIDADES',
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarDiligenciaProjeto');
            const idPronac = 209561;
            const idDiligencia = 72427;
            const value = { idPronac, idDiligencia };
            actions.buscarDiligenciaProjeto({ commit }, value);
        });

        test('it calls projetoHelperAPI.buscarDiligenciaProjeto', () => {
            expect(projetoHelperAPI.buscarDiligenciaProjeto).toHaveBeenCalled();
        });

        test('it is commit to buscarDiligenciaProjeto', (done) => {
            const diligenciaProjeto = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DILIGENCIA_PROJETO', diligenciaProjeto.data.items);
        });
    });

    describe('buscarDiligencia', () => {
        beforeEach(() => {
            mockReponse = {
                data: {
                    data: {
                        items: {
                            diligenciaAdequacao: {
                                tipoDiligencia: 'Diligência na Análise da adequação à realidade do projeto.',
                                idAvaliarAdequacaoProjeto: 1452,
                            },
                            diligenciaProjeto: {
                                tipoDiligencia: 'Diligência de Checklist - Análise',
                                idDiligencia: 72427,
                            },
                            diligenciaProposta: {
                                idAvaliacaoProposta: 401888,
                                idPreprojeto: 245047,
                            },
                        },
                    },
                },
            };

            axios.get.mockResolvedValue(mockReponse);

            commit = jest.fn();
            jest.spyOn(projetoHelperAPI, 'buscarDiligencia');
            const idPronac = 216941;
            actions.buscarDiligencia({ commit }, idPronac);
        });

        test('it calls projetoHelperAPI.buscarDiligencia', () => {
            expect(projetoHelperAPI.buscarDiligencia).toHaveBeenCalled();
        });

        test('it is commit to buscarDiligencia', (done) => {
            const diligencia = mockReponse.data;
            done();
            expect(commit).toHaveBeenCalledWith('SET_DILIGENCIA', diligencia.data.items);
        });
    });
});
