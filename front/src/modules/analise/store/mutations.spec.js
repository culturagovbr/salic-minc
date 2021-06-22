import { mutations } from './mutations';

describe('Projeto Mutations', () => {
    let state;
    let defaultState;
    let projeto;
    let proponente;
    let planilhaHomologada;
    let planilhaOriginal;
    let planilhaReadequada;
    let planilhaAutorizada;
    let planilhaAdequada;
    let transferenciaRecursos;
    let certidoesNegativas;
    let documentosAssinados;
    let dadosComplementares;
    let documentosAnexados;
    let localRealizacaoDeslocamento;
    let providenciaTomada;
    let planoDistribuicaoIn2013;
    let historicoEncaminhamento;
    let tramitacaoDocumento;
    let tramitacaoProjeto;
    let ultimaTramitacao;
    let planoDistribuicaoIn2017;
    let diligenciaProposta;
    let diligenciaAdequacao;
    let diligenciaProjeto;
    let diligencia;

    beforeEach(() => {
        defaultState = {
            projeto: {
                IdPRONAC: '',
                Item: '',
                NomeProjeto: '',
            },
            proponente: {
                Proponente: '',
                idAgente: '',
                TipoPessoa: '',
            },
            planilhaHomologada: {
                tpPlanilha: '',
                IdPronac: '',
                PRONAC: '',
            },
            planilhaOriginal: {
                idPlanilhaProposta: '',
                FonteRecurso: '',
                idEtapa: '',
            },
            planilhaReadequada: {
                tpPlanilha: '',
                idPronac: '',
                PRONAC: '',
            },
            planilhaAutorizada: {
                tpPlanilha: '',
                idPronac: '',
                PRONAC: '',
            },
            planilhaAdequada: {
                Seq: '',
                idPlanilhaProposta: '',
                idEtapa: '',
            },
            transferenciaRecursos: {
                idPronacTransferidor: '',
                PronacTransferidor: '',
                NomeProjetoTranferidor: '',
                idPronacRecebedor: '',
                PronacRecebedor: '',
                NomeProjetoRecedor: '',
                dtRecebimento: '',
                vlRecebido: '',
            },
            certidoesNegativas: {
                dsCertidao: '',
                CodigoCertidao: '',
                Pronac: '',
            },
            documentosAssinados: {
                dsAtoAdministrativo: '',
                idDocumentoAssinatura: '',
                pronac: '',
            },
            dadosComplementares: {
                CustosVinculados: {
                    Descricao: '',
                    Percentual: '',
                },
                Proposta: {
                    Objetivos: '',
                },
            },
            documentosAnexados: {
                Anexado: '',
                idArquivo: '',
                AgenteDoc: '',
            },
            localRealizacaoDeslocamento: {
                localRealizacoes: {
                    Descricao: '',
                    UF: '',
                    Cidade: '',
                },
                Deslocamento: {
                    Qtde: '',
                    PaisOrigem: '',
                },
            },
            providenciaTomada: {
                Situacao: '',
                cnpjcpf: '',
                ProvidenciaTomada: '',
            },
            planoDistribuicaoIn2013: {
                idPlanoDistribuicao: '',
                idProjeto: '',
                idProduto: '',
            },
            historicoEncaminhamento: {
                Unidade: '',
                DtEnvio: '',
                qtDias: '',
            },
            tramitacaoDocumento: {
                dsTipoDocumento: '',
                idDocumento: '',
                idLote: '',
            },
            tramitacaoProjeto: {
                Situacao: '',
                Origem: '',
                Destino: '',
            },
            ultimaTramitacao: {
                Emissor: '',
                Receptor: '',
                Estado: '',
            },
            planoDistribuicaoIn2017: {
                idPlanoDistribuicao: '',
                idProjeto: '',
                idProduto: '',
            },
            diligenciaProposta: {
                nomeProjeto: '',
                dataSolicitacao: '',
            },
            diligenciaAdequacao: {
                idAvaliarAdequacaoProjeto: '',
                dtAvaliacao: '',
            },
            diligenciaProjeto: {
                arquivos: {
                    idArquivo: '',
                },
                nomeProjeto: '',
            },
            diligencia: {
                diligenciaAdequacao: {
                    tipoDiligencia: '',
                    idAvaliarAdequacaoProjeto: '',
                },
                diligenciaProjeto: {
                    tipoDiligencia: '',
                    idDiligencia: '',
                },
                diligenciaProposta: {
                    idAvaliacaoProposta: '',
                    idPreprojeto: '',
                },
            },
        };

        state = Object.assign({}, defaultState);

        projeto = {
            IdPRONAC: '132451',
            Item: 'Hospedagem sem AlimentaÃ§Ã£o',
            NomeProjeto: 'CrianÃ§a Para Vida - 15 anos',
        };

        proponente = {
            Proponente: 'Associa\xE7\xC3o Beneficiente Cultural Religiosa Centro Judaico do Brooklin',
            idAgente: '24806',
            TipoPessoa: 'Jur\xCDdica',
        };

        planilhaHomologada = {
            tpPlanilha: 'CO',
            IdPronac: '189786',
            PRONAC: '150151',
        };

        planilhaOriginal = {
            idPlanilhaProposta: '3675289',
            FonteRecurso: 'Incentivo Fiscal Federal',
            idEtapa: '2',
        };

        planilhaReadequada = {
            tpPlanilha: 'RP',
            idPronac: '189786',
            PRONAC: '150151',
        };

        planilhaAutorizada = {
            tpPlanilha: 'CO',
            idPronac: '200728',
            PRONAC: '1510482',
        };

        planilhaAdequada = {
            Seq: '28',
            idPlanilhaProposta: '4913779',
            idEtapa: '8',
        };

        transferenciaRecursos = {
            idPronacTransferidor: 1,
            PronacTransferidor: 111111,
            NomeProjetoTranferidor: 'CrianÃ§a Para Vida - 15 anos',
            idPronacRecebedor: 2,
            PronacRecebedor: 222222,
            NomeProjetoRecedor: 'CrianÃ§a Para Vida - 15 anos',
            dtRecebimento: new Date(),
            vlRecebido: parseFloat('1000000'),
        };
        certidoesNegativas = {
            dsCertidao: 'Quita&ccedil;&atilde;o de Tributos Federais',
            CodigoCertidao: 49,
            Pronac: 160059,
        };
        documentosAssinados = {
            dsAtoAdministrativo: 'Parecer de Aprova&ccedil;&atilde;o Preliminar',
            idDocumentoAssinatura: 3564,
            pronac: 178894,
        };
        dadosComplementares = {
            CustosVinculados: {
                Descricao: 'Custos de Administra&ccedil;&atilde;o',
                Percentual: 15,
            },
            Proposta: {
                Objetivos: 'Objetivo espec&iacute;fico do projeto &eacute; a realiza&ccedil;&atilde;o de tr&ecirc;s atra&ccedil;&otilde;es',
            },
        };
        documentosAnexados = {
            Anexado: 'Documento do Proponente',
            idArquivo: 180609,
            AgenteDoc: 1,
        };
        localRealizacaoDeslocamento = {
            localRealizacoes: {
                Descricao: 'Brasil',
                UF: 'Santa Catarina',
                Cidade: 'Conc&oacute;rdia',
            },
            Deslocamento: {
                Qtde: 28,
                PaisOrigem: 'Brasil',
            },
        };
        providenciaTomada = {
            Situacao: 'B01',
            cnpjcpf: '08887383740',
            ProvidenciaTomada: 'Proposta transformada em projeto cultural',
        };
        planoDistribuicaoIn2013 = {
            idPlanoDistribuicao: 171982,
            idProjeto: 207951,
            idProduto: 3,
        };
        historicoEncaminhamento = {
            Unidade: 'FUNARTE',
            DtEnvio: '03/04/2018 00:00:00',
            qtDias: 44,
        };
        tramitacaoDocumento = {
            dsTipoDocumento: 'Comunicado de Mecenato',
            idDocumento: 453659,
            idLote: 295184,
        };
        tramitacaoProjeto = {
            Situacao: 'Cadastrado',
            Origem: 'SEFIC/GEAAP/SUAPI/DIAAPI',
            Destino: 'SEFIC/GEAR/SACAV',
        };
        ultimaTramitacao = {
            Emissor: 'M&ordf; do Socorro Silva',
            Receptor: 'Renata L.Oliveira',
            Estado: 'Recebido',
        };
        planoDistribuicaoIn2017 = {
            idPlanoDistribuicao: 229891,
            idProjeto: 273246,
            idProduto: 19,
        };
        diligenciaProposta = {
            nomeProjeto: 'FOTOATIVIDADES',
            dataSolicitacao: '04/04/2017',
        };
        diligenciaAdequacao = {
            idAvaliarAdequacaoProjeto: 1452,
            dtAvaliacao: '06/06/2018',
        };
        diligenciaProjeto = {
            arquivos: {
                idArquivo: 1272611,
            },
            nomeProjeto: 'FOTOATIVIDADES',
        };
        diligencia = {
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
        };
    });

    test('SET_PROJETO', () => {
        mutations.SET_PROJETO(state, projeto);
        expect(state.projeto).toEqual(projeto);
    });

    test('SET_PROPONENTE', () => {
        mutations.SET_PROPONENTE(state, proponente);
        expect(state.proponente).toEqual(proponente);
    });

    test('SET_PLANILHA_HOMOLOGADA', () => {
        mutations.SET_PLANILHA_HOMOLOGADA(state, planilhaHomologada);
        expect(state.planilhaHomologada).toEqual(planilhaHomologada);
    });

    test('SET_PLANILHA_ORIGINAL', () => {
        mutations.SET_PLANILHA_ORIGINAL(state, planilhaOriginal);
        expect(state.planilhaOriginal).toEqual(planilhaOriginal);
    });

    test('SET_PLANILHA_READEQUADA', () => {
        mutations.SET_PLANILHA_READEQUADA(state, planilhaReadequada);
        expect(state.planilhaReadequada).toEqual(planilhaReadequada);
    });

    test('SET_PLANILHA_AUTORIZADA', () => {
        mutations.SET_PLANILHA_AUTORIZADA(state, planilhaAutorizada);
        expect(state.planilhaAutorizada).toEqual(planilhaAutorizada);
    });

    test('SET_PLANILHA_ADEQUADA', () => {
        mutations.SET_PLANILHA_ADEQUADA(state, planilhaAdequada);
        expect(state.planilhaAdequada).toEqual(planilhaAdequada);
    });

    test('SET_TRANSFERENCIA_RECURSOS', () => {
        mutations.SET_TRANSFERENCIA_RECURSOS(state, transferenciaRecursos);
        expect(state.transferenciaRecursos).toEqual(transferenciaRecursos);
    });

    test('SET_CERTIDOES_NEGATIVAS', () => {
        mutations.SET_CERTIDOES_NEGATIVAS(state, certidoesNegativas);
        expect(state.certidoesNegativas).toEqual(certidoesNegativas);
    });

    test('SET_DOCUMENTOS_ASSINADOS', () => {
        mutations.SET_DOCUMENTOS_ASSINADOS(state, documentosAssinados);
        expect(state.documentosAssinados).toEqual(documentosAssinados);
    });

    test('SET_DADOS_COMPLEMENTARES', () => {
        mutations.SET_DADOS_COMPLEMENTARES(state, dadosComplementares);
        expect(state.dadosComplementares).toEqual(dadosComplementares);
    });

    test('SET_DOCUMENTOS_ANEXADOS', () => {
        mutations.SET_DOCUMENTOS_ANEXADOS(state, documentosAnexados);
        expect(state.documentosAnexados).toEqual(documentosAnexados);
    });

    test('SET_LOCAL_REALIZACAO_DESLOCAMENTO', () => {
        mutations.SET_LOCAL_REALIZACAO_DESLOCAMENTO(state, localRealizacaoDeslocamento);
        expect(state.localRealizacaoDeslocamento).toEqual(localRealizacaoDeslocamento);
    });

    test('SET_PROVIDENCIA_TOMADA', () => {
        mutations.SET_PROVIDENCIA_TOMADA(state, providenciaTomada);
        expect(state.providenciaTomada).toEqual(providenciaTomada);
    });

    test('SET_PLANO_DISTRIBUICAO_IN2013', () => {
        mutations.SET_PLANO_DISTRIBUICAO_IN2013(state, planoDistribuicaoIn2013);
        expect(state.planoDistribuicaoIn2013).toEqual(planoDistribuicaoIn2013);
    });

    test('SET_HISTORICO_ENCAMINHAMENTO', () => {
        mutations.SET_HISTORICO_ENCAMINHAMENTO(state, historicoEncaminhamento);
        expect(state.historicoEncaminhamento).toEqual(historicoEncaminhamento);
    });

    test('SET_TRAMITACAO_DOCUMENTO', () => {
        mutations.SET_TRAMITACAO_DOCUMENTO(state, tramitacaoDocumento);
        expect(state.tramitacaoDocumento).toEqual(tramitacaoDocumento);
    });

    test('SET_TRAMITACAO_PROJETO', () => {
        mutations.SET_TRAMITACAO_PROJETO(state, tramitacaoProjeto);
        expect(state.tramitacaoProjeto).toEqual(tramitacaoProjeto);
    });

    test('SET_ULTIMA_TRAMITACAO', () => {
        mutations.SET_ULTIMA_TRAMITACAO(state, ultimaTramitacao);
        expect(state.ultimaTramitacao).toEqual(ultimaTramitacao);
    });

    test('SET_PLANO_DISTRIBUICAO_IN2017', () => {
        mutations.SET_PLANO_DISTRIBUICAO_IN2017(state, planoDistribuicaoIn2017);
        expect(state.planoDistribuicaoIn2017).toEqual(planoDistribuicaoIn2017);
    });

    test('SET_DILIGENCIA_PROPOSTA', () => {
        mutations.SET_DILIGENCIA_PROPOSTA(state, diligenciaProposta);
        expect(state.diligenciaProposta).toEqual(diligenciaProposta);
    });

    test('SET_DILIGENCIA_ADEQUACAO', () => {
        mutations.SET_DILIGENCIA_ADEQUACAO(state, diligenciaAdequacao);
        expect(state.diligenciaAdequacao).toEqual(diligenciaAdequacao);
    });

    test('SET_DILIGENCIA_PROJETO', () => {
        mutations.SET_DILIGENCIA_PROJETO(state, diligenciaProjeto);
        expect(state.diligenciaProjeto).toEqual(diligenciaProjeto);
    });

    test('SET_DILIGENCIA', () => {
        mutations.SET_DILIGENCIA(state, diligencia);
        expect(state.diligencia).toEqual(diligencia);
    });
});
