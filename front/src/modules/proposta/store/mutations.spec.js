import { mutations } from './mutations';

describe('Proposta Mutations', () => {
    let state;
    let defaultState;
    let localRealizacaoDeslocamento;
    let fontesDeRecursos;
    let documentos;
    let proposta;
    let historicoSolicitacoes;
    let historicoEnquadramento;

    beforeEach(() => {
        defaultState = {
            localRealizacaoDeslocamento: {
                idProjeto: '',
                idPais: '',
                idDeslocamento: '',
            },
            fontesDeRecursos: {
                Descricao: '',
                Valor: '',
            },
            documentos: {
                CodigoDocumento: '',
                Codigo: '',
                idDocumentosAgentes: '',
            },
            proposta: {
                idPreProjeto: '',
                idAgente: '',
                idUsuario: '',
            },
            historicoSolicitacoes: {
                items:{
                    idProjeto: '',
                    idSolicitacao: '',
                    idSolicitante: '',
                },
            },
            historicoEnquadramento: {
                items:{
                    lines:{
                        org_sigla: '',
                        usu_nome: '',
                    },
                },
            },
        };

        state = Object.assign({}, defaultState);

        localRealizacaoDeslocamento = {
            idProjeto: '273246',
            idPais: '31',
            idDeslocamento: '306706',
        };

        fontesDeRecursos = {
            Descricao: 'Incentivo Fiscal Federal',
            Valor: '354.726,00',
        };

        documentos = {
            CodigoDocumento: '27',
            Codigo: '59213',
            idDocumentosAgentes: '228068',
        };

        proposta = {
            idPreProjeto: '273246',
            idAgente: '59213',
            idUsuario: '59731',
        };

        historicoSolicitacoes = {
            items:{
                idProjeto: '282177',
                idSolicitacao: '3267',
                idSolicitante: '285582',
            },
        };

        historicoEnquadramento = {
            items:{
                lines:{
                    org_sigla: 'CNIC',
                    usu_nome: 'Maricene A Gregorut',
                },
            },
        };
    });

    test('SET_LOCAL_REALIZACAO_DESLOCAMENTO', () => {
        mutations.SET_LOCAL_REALIZACAO_DESLOCAMENTO(state, localRealizacaoDeslocamento);
        expect(state.localRealizacaoDeslocamento).toEqual(localRealizacaoDeslocamento);
    });

    test('SET_FONTES_DE_RECURSOS', () => {
        mutations.SET_FONTES_DE_RECURSOS(state, fontesDeRecursos);
        expect(state.fontesDeRecursos).toEqual(fontesDeRecursos);
    });

    test('SET_DOCUMENTOS', () => {
        mutations.SET_DOCUMENTOS(state, documentos);
        expect(state.documentos).toEqual(documentos);
    });

    test('SET_DADOS_PROPOSTA', () => {
        mutations.SET_DADOS_PROPOSTA(state, proposta);
        expect(state.proposta).toEqual(proposta);
    });

    test('SET_HISTORICO_SOLICITACOES', () => {
        mutations.SET_HISTORICO_SOLICITACOES(state, historicoSolicitacoes);
        expect(state.historicoSolicitacoes).toEqual(historicoSolicitacoes);
    });

    test('SET_HISTORICO_ENQUADRAMENTO', () => {
        mutations.SET_HISTORICO_ENQUADRAMENTO(state, historicoEnquadramento);
        expect(state.historicoEnquadramento).toEqual(historicoEnquadramento);
    });
});
