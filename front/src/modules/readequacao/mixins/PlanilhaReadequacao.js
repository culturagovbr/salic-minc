import planilhas from '@/mixins/planilhas';

export default {
    mixins: [planilhas],
    methods: {
        isLinhaAlterada(row) {
            const planilhaEdicao = [
                row.idUnidade,
                row.Ocorrencia,
                row.Quantidade,
                row.QtdeDias,
                row.vlUnitario,
            ];
            const planilhaAtiva = [
                row.idUnidadeAtivo,
                row.OcorrenciaAtivo,
                row.QuantidadeAtivo,
                row.QtdeDiasAtivo,
                row.vlUnitarioAtivo,
            ];
            return JSON.stringify(planilhaEdicao) !== JSON.stringify(planilhaAtiva);
        },
        isItemDisponivelEdicao(item) {
            if (item.vlComprovado < item.vlAprovado) {
                return true;
            }
            return false;
        },
        isDisponivelParaEdicao(row) {
            return row;
        },
        obterClasseItem(row = '') {
            let classe = {};
            switch (true) {
            case row.selecionado:
                classe = { 'purple lighten-5': true };
                break;
            case this.isLinhaAlterada(row):
                classe = { 'indigo lighten-4': true };
                break;
            case row.isDisponivelParaEdicao === false:
                classe = { 'grey lighten-3 grey--text text--darken-3': true };
                break;
            default:
                classe = {};
                break;
            }
            return classe;
        },
        obterValorAprovadoTotal(table) {
            let soma = 0;
            Object.entries(table).forEach(([, cell]) => {
                if (cell.vlAprovado !== undefined) {
                    soma += parseFloat(cell.vlAprovado);
                }
            });
            return soma;
        },
        obterValorComprovadoTotal(table) {
            let soma = 0;
            Object.entries(table).forEach(([, cell]) => {
                if (cell.vlComprovado !== undefined) {
                    soma += (cell.vlComprovado);
                }
            });
            return soma;
        },
    },
};
