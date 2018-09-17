import { mutations } from './mutations';

describe('Proposta Mutations', () => {
    let state;
    let defaultState;
    let localRealizacaoDeslocamento;
    let fontesDeRecursos;
    let documentos;
    let proposta;

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
});
