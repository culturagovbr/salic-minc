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
                idEtapa: ''
            },
        };

        state = Object.assign({}, defaultState);

        projeto = {
            IdPRONAC: '132451',
            Item: 'Hospedagem sem Alimentação',
            NomeProjeto: 'Criança Para Vida - 15 anos',
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
            idEtapa: '8'
        };

        transferenciaRecursos = {
            idPronacTransferidor: 1,
            PronacTransferidor: 111111,
            NomeProjetoTranferidor: 'Criança Para Vida - 15 anos',
            idPronacRecebedor: 2,
            PronacRecebedor: 222222,
            NomeProjetoRecedor: 'Criança Para Vida - 15 anos',
            dtRecebimento: new Date(),
            vlRecebido: parseFloat('1000000'),
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
});
