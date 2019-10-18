import planilhas from '@/mixins/planilhas';

export default {
    mixins: [planilhas],
    methods: {
        isLinhaAlterada(row) {
            const proponente = [
                row.Unidade,
                row.VlSolicitado,
                row.ocorrenciaprop,
                row.quantidadeprop,
                row.diasprop,
                row.valorUnitarioprop,
                row.stCustoPraticado,
            ];
            const parecerista = [
                row.idUnidade,
                row.VlSugeridoParecerista,
                row.ocorrenciaparc,
                row.quantidadeparc,
                row.diasparc,
                row.valorUnitarioparc,
                row.stCustoPraticadoParc,
            ];
            return JSON.stringify(proponente) !== JSON.stringify(parecerista);
        },
        isItemZerado(row) {
            if (row.idUnidade === 1
            && row.ocorrenciaparc === 0
            && row.quantidadeparc === 0
            && row.diasparc === 0
            && row.valorUnitarioparc === 0) {
                return true;
            }
            return false;
        },
        isStCustoPraticado(row, cell = 'stCustoPraticado') {
            return (row[cell] === true
            || parseInt(row[cell], 10) === 1);
        },
        obterClasseItem(row, cell = 'stCustoPraticado') {
            let classe = {};
            switch (true) {
            case row.selecionado:
                classe = { 'purple lighten-5': true };
                break;
            case this.isItemZerado(row):
                classe = { 'grey lighten-2': true };
                break;
            case this.isStCustoPraticado(row, cell):
                classe = { 'orange lighten-4': true };
                break;
            case this.isLinhaAlterada(row):
                classe = { 'indigo lighten-4': true };
                break;
            case row.isDisponivelParaAnalise === false:
                classe = { 'grey lighten-3 grey--text text--darken-3': true };
                break;
            default:
                classe = {};
                break;
            }
            return classe;
        },
        obterEstiloItem(row) {
            return {
                cursor: row.isDisponivelParaAnalise === false ? 'not-allowed' : 'pointer',
                'text-decoration': this.isItemZerado(row) ? 'line-through' : 'none',
            };
        },
        obterValorSolicitadoTotalParecer(table) {
            let soma = 0;

            Object.entries(table).forEach(([, cell]) => {
                if (cell.VlSolicitado !== undefined) {
                    soma += parseFloat(cell.VlSolicitado);
                }
            });

            return soma;
        },
        obterValorSugeridoTotalParecer(table) {
            let soma = 0;
            Object.entries(table).forEach(([, cell]) => {
                if (cell.valorUnitarioparc !== undefined) {
                    soma += (
                        parseFloat(cell.valorUnitarioparc)
                        * parseInt(cell.ocorrenciaparc, 10)
                        * parseInt(cell.quantidadeparc, 10)
                    );
                }
            });
            return soma;
        },
    },
};
