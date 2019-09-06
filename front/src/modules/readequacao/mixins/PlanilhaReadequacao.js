import planilhas from '@/mixins/planilhas';
import Const from '../const';

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
        isLinhaAumentada(row) {
            if (row.tpAcao === 'A') {
                if ((row.QuantidadeAtivo * row.vlUnitarioAtivo * row.OcorrenciaAtivo)
                    < (row.Quantidade * row.vlUnitario * row.Ocorrencia)) {
                    return true;
                }
            }
            return false;
        },
        isLinhaReduzida(row) {
            if (row.tpAcao === 'A') {
                if ((row.QuantidadeAtivo * row.vlUnitarioAtivo * row.OcorrenciaAtivo)
                    > (row.Quantidade * row.vlUnitario * row.Ocorrencia)) {
                    return true;
                }
            }
            return false;
        },
        isUnidadeAlterada(row) {
            if (typeof row.idUnidadeAtivo !== 'undefined') {
                if (row.idUnidadeAtivo !== row.idUnidade) {
                    return true;
                }
            }
            return false;
        },
        isItemAdicionado(row) {
            if (row.tpAcao === 'I') {
                return true;
            }
            return false;
        },
        isItemExcluido(row) {
            if (row.tpAcao === 'E') {
                return true;
            }
            return false;
        },
        isItemCustoVinculado(item) {
            const tiposCustosVinculados = [5249, 8197, 8198];
            if (tiposCustosVinculados.includes(item.idPlanilhaItem)
                && item.vlComprovado < item.vlAprovado
                && item.tpAcao !== 'E'
            ) {
                return true;
            }
            return false;
        },
        isItemOutrasFontes(item) {
            if (item.idFonte !== 109) {
                return true;
            }
            return false;
        },
        isItemDisponivelEdicao(item) {
            if (this.isItemCustoVinculado(item)) {
                return false;
            }
            if (this.isItemOutrasFontes(item)) {
                if (this.perfil === Const.PERFIL_PROPONENTE) {
                    return true;
                }
                return false;
            }
            return true;
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
            case this.isLinhaAumentada(row):
                classe = { 'blue lighten-5': true };
                break;
            case this.isLinhaReduzida(row):
                classe = { 'orange lighten-5': true };
                break;
            case this.isUnidadeAlterada(row):
                classe = { 'indigo lighten-5': true };
                break;
            case this.isItemAdicionado(row):
                classe = { 'green lighten-5': true };
                break;
            case this.isItemExcluido(row):
                classe = { 'red lighten-4': true };
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
