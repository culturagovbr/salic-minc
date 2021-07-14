import { mutations } from './mutations';

describe('Testes das Mutations - Avaliação de Resultados', () => {
    // Testa se as informações passadas ao chamar uma MUTATION
    // estão sendo armazenadas corretamente nos STATES respectivos

    let state;
    let defaultState;
    let dados;

    beforeEach(() => {
        defaultState = {
            dados: [],
        };

        state = Object.assign({}, defaultState);

        dados = [
            {
                uf: 'DF',
                idPronac: '168192',
            },
        ];
    });


    test('GET_CONSOLIDACAO_PARECER', () => {
        mutations.GET_CONSOLIDACAO_PARECER(state, dados);
        expect(state.consolidacaoComprovantes).toEqual(dados);
    });

    test('GET_PARECER', () => {
        mutations.GET_PARECER(state, dados);
        expect(state.parecer).toEqual(dados);
    });

    test('GET_PROJETO', () => {
        mutations.GET_PROJETO(state, dados);
        expect(state.projeto).toEqual(dados);
    });

    test('GET_PROPONENTE', () => {
        mutations.GET_PROPONENTE(state, dados);
        expect(state.proponente).toEqual(dados);
    });


    test('SET_REGISTRO_ATIVO', () => {
        mutations.SET_REGISTRO_ATIVO(state, dados);
        expect(state.registroAtivo).toEqual(dados);
    });

    describe('SET_REGISTROS_TABELA - Criação e Atualização do state.dadosTabela', () => {
        beforeEach(() => {
            mutations.SET_REGISTROS_TABELA(state, dados);
        });

        test('SET_REGISTROS_TABELA', () => {
            expect(state.dadosTabela).toEqual(dados);
        });

        test('SET_REGISTRO_TABELA', () => {
            mutations.SET_REGISTRO_TABELA(state, dados);
            expect(state.dadosTabela).toContain(dados);
        });

        test('ATUALIZAR_REGISTRO_TABELA', () => {
            mutations.ATUALIZAR_REGISTRO_TABELA(state, dados);
            expect(state.dadosTabela).toEqual(dados);
        });

        test('REMOVER_REGISTRO', () => {
            expect(state.dadosTabela).toEqual(dados);
            mutations.REMOVER_REGISTRO(state, dados);
            expect(state.dadosTabela).toEqual([]);
        });
    });

    test('DESTINATARIOS_ENCAMINHAMENTO', () => {
        mutations.DESTINATARIOS_ENCAMINHAMENTO(state, dados);
        expect(state.dadosDestinatarios).toEqual(dados);
    });

    test('PROJETOS_AVALIACAO_TECNICA', () => {
        mutations.PROJETOS_AVALIACAO_TECNICA(state, dados);
        expect(state.dadosTabelaTecnico).toEqual(dados);
    });

    test('HISTORICO_ENCAMINHAMENTO', () => {
        mutations.HISTORICO_ENCAMINHAMENTO(state, dados);
        expect(state.dadosHistoricoEncaminhamento).toEqual(dados);
    });

    test('GET_TIPO_AVALIACAO', () => {
        mutations.GET_TIPO_AVALIACAO(state, dados);
        expect(state.tipoAvaliacao).toEqual(dados[0]);
    });

    test('LINK_REDIRECIONAMENTO_TIPO_AVALIACAO_RESULTADO', () => {
        const link = '/#';
        mutations.LINK_REDIRECIONAMENTO_TIPO_AVALIACAO_RESULTADO(state, link);
        expect(state.redirectLink).toEqual(link);
    });

    test('GET_PLANILHA', () => {
        mutations.GET_PLANILHA(state, dados);
        expect(state.planilha).toEqual(dados);
    });

    test('GET_PROJETO_ANALISE', () => {
        mutations.GET_PROJETO_ANALISE(state, dados);
        expect(state.projetoAnalise).toEqual(dados);
    });

    test('GET_CONSOLIDACAO_ANALISE', () => {
        mutations.GET_CONSOLIDACAO_ANALISE(state, dados);
        expect(state.consolidacaoAnalise).toEqual(dados);
    });

    test('GET_PARECER_LAUDO_FINAL', () => {
        mutations.GET_PARECER_LAUDO_FINAL(state, dados);
        expect(state.getParecerLaudoFinal).toEqual(dados);
    });

    test('SET_PARECER', () => {
        mutations.SET_PARECER(state, dados);
        expect(state.parecer).toEqual(dados);
    });

    test('SET_DADOS_PROJETOS_FINALIZADOS', () => {
        mutations.SET_DADOS_PROJETOS_FINALIZADOS(state, dados);
        expect(state.projetosFinalizados).toEqual(dados);
    });


    describe('Criação e Atualização do state.dadosItemComprovacao', () => {
        beforeEach(() => {
            mutations.GET_DADOS_ITEM_COMPROVACAO(state, dados);
        });

        test('GET_DADOS_ITEM_COMPROVACAO', () => {
            expect(state.dadosItemComprovacao).toEqual(dados);
        });
    });

    test('SET_DADOS_PROJETOS_PARA_DISTRIBUIR', () => {
        mutations.SET_DADOS_PROJETOS_PARA_DISTRIBUIR(state, dados);
        expect(state.projetosParaDistribuir).toEqual(dados);
    });

    test('SET_DADOS_PROJETOS_ASSINAR', () => {
        mutations.SET_DADOS_PROJETOS_ASSINAR(state, dados);
        expect(state.getProjetosAssinar).toEqual(dados);
    });

    test('SET_DADOS_PROJETOS_EM_ASSINATURA', () => {
        mutations.SET_DADOS_PROJETOS_EM_ASSINATURA(state, dados);
        expect(state.getProjetosEmAssinatura).toEqual(dados);
    });

    test('SET_DADOS_PROJETOS_LAUDO_FINAL', () => {
        mutations.SET_DADOS_PROJETOS_LAUDO_FINAL(state, dados);
        expect(state.getProjetosLaudoFinal).toEqual(dados);
    });

    test('SET_DADOS_PROJETOS_LAUDO_ASSINAR', () => {
        mutations.SET_DADOS_PROJETOS_LAUDO_ASSINAR(state, dados);
        expect(state.getProjetosLaudoAssinar).toEqual(dados);
    });

    test('SET_DADOS_PROJETOS_LAUDO_EM_ASSINATURA', () => {
        mutations.SET_DADOS_PROJETOS_LAUDO_EM_ASSINATURA(state, dados);
        expect(state.getProjetosLaudoEmAssinatura).toEqual(dados);
    });

    test('SET_DADOS_PROJETOS_LAUDO_FINALIZADOS', () => {
        mutations.SET_DADOS_PROJETOS_LAUDO_FINALIZADOS(state, dados);
        expect(state.getProjetosLaudoFinalizados).toEqual(dados);
    });

    test('SET_DADOS_PROJETOS_HISTORICO', () => {
        mutations.SET_DADOS_PROJETOS_HISTORICO(state, dados);
        expect(state.getProjetosHistorico).toEqual(dados);
    });

    test('SET_VERSAO', () => {
        mutations.SET_VERSAO(state, dados);
        expect(state.versao).toEqual(dados);
    });

    test('SYNC_PROJETOS_REVISAO', () => {
        mutations.SYNC_PROJETOS_REVISAO(state, dados);
        expect(state.projetosRevisao).toEqual(dados);
    });

    test('SET_DEVOLVER_PROJETO', () => {
        mutations.SET_DEVOLVER_PROJETO(state, dados);
        expect(state.devolverProjeto).toEqual(dados);
    });

    test('GET_OBJETO_PARECER', () => {
        mutations.GET_OBJETO_PARECER(state, dados);
        expect(state.objetoParecer).toEqual(dados);
    });

    test('SET_ITENS_BUSCA_COMPROVANTES', () => {
        mutations.SET_ITENS_BUSCA_COMPROVANTES(state, dados);
        expect(state.itensBuscaComprovantes).toEqual(dados);
    });

    test('SET_COMPROVANTES', () => {
        mutations.SET_COMPROVANTES(state, dados);
        expect(state.comprovantes).toEqual(dados);
    });

    test('SYNC_PROJETOS_ASSINAR_COORDENADOR', () => {
        mutations.SYNC_PROJETOS_ASSINAR_COORDENADOR(state, dados);
        expect(state.projetosAssinarCoordenador).toEqual(dados);
    });

    test('SYNC_PROJETOS_ASSINAR_COORDENADOR_GERAL', () => {
        mutations.SYNC_PROJETOS_ASSINAR_COORDENADOR_GERAL(state, dados);
        expect(state.projetosAssinarCoordenadorGeral).toEqual(dados);
    });
});

