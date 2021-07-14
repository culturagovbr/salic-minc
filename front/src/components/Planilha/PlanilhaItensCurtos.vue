<template>
    <div class="itens">
        <table class="bordered">
            <thead>
                <tr>
                    <th class="center-align">
                        #
                    </th>
                    <th class="left-align">
                        Item
                    </th>
                    <th class="left-align">
                        Unidade
                    </th>
                    <th class="center-align">
                        Dias
                    </th>
                    <th class="center-align">
                        Qtde
                    </th>
                    <th class="center-align">
                        Ocor.
                    </th>
                    <th class="right-align">
                        Vl. Unit&aacute;rio
                    </th>
                    <th>Vl. Solicitado</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row of table"
                    v-if="isObject(row)"
                    :key="row.idPlanilhaProposta"
                    :class="{'orange lighten-2': ultrapassaValor(row)}"
                >
                    <td class="center-align">
                        {{ row.Seq }}
                    </td>
                    <td class="left-align">
                        {{ row.Item }}
                    </td>
                    <td class="center-align">
                        {{ row.Unidade }}
                    </td>
                    <td class="center-align">
                        {{ row.QtdeDias }}
                    </td>
                    <td class="center-align">
                        {{ row.Quantidade }}
                    </td>
                    <td class="center-align">
                        {{ row.Ocorrencia }}
                    </td>
                    <td class="right-align">
                        {{ row.vlUnitario | filtroFormatarParaReal }}
                    </td>
                    <td class="right-align">
                        <SalicFormatarValor :valor="row.vlSolicitado" />
                    </td>
                    <td>
                        <a
                            v-if="row.JustProponente.length > 3"
                            :data-tooltip="row.JustProponente"
                            class="tooltipped"
                            data-position="left"
                            data-delay="50"
                        ><i class="material-icons tiny">message</i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import SalicFormatarValor from '@/components/SalicFormatarValor';
import * as planilhas from '@/mixins/planilhas';

export default {
    name: 'PlanilhaListaDeItensCurta',
    components: {
        SalicFormatarValor,
    },
    props: {
        table: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            planilha: [],
        };
    },
    methods: {
        isObject(el) {
            return typeof el === 'object';
        },
        ultrapassaValor(row) {
            return planilhas.ultrapassaValor(row);
        },
    },
};
</script>
