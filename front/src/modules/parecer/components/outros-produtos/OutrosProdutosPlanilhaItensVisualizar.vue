<template>
    <div class="itens">
        <v-data-table
            :headers="headers"
            :items="table"
            :rows-per-page-items="[-1]"
            :loading="loading"
            item-key="idPlanilhaProjeto"
            class="elevation-1"
            hide-actions
        >
            <v-progress-linear
                slot="progress"
                color="blue"
                indeterminate
            />
            <template
                slot="items"
                slot-scope="props"
            >
                <tr
                    :class="obterClasseItem(props.item)"
                >
                    <td class="text-xs-center">
                        {{ props.item.Seq }}
                    </td>
                    <td class="text-xs-left">
                        {{ props.item.Item }}
                    </td>
                    <td class="text-xs-center">
                        {{ props.item.UnidadeProposta }}
                    </td>
                    <td class="text-xs-center">
                        {{ props.item.diasprop }}
                    </td>
                    <td class="text-xs-center">
                        {{ props.item.quantidadeprop }}
                    </td>
                    <td class="text-xs-center">
                        {{ props.item.ocorrenciaprop }}
                    </td>
                    <td class="text-xs-right">
                        {{ props.item.valorUnitarioprop | filtroFormatarParaReal }}
                    </td>
                    <td class="text-xs-right">
                        {{ props.item.VlSolicitado | filtroFormatarParaReal }}
                    </td>
                    <td class="text-xs-right">
                        {{ props.item.VlSugeridoParecerista | filtroFormatarParaReal }}
                    </td>
                    <td class="justify-center layout px-0">
                        <v-icon
                            v-if="props.item.justificitivaproponente.length > 1 || props.item.dsJustificativaParecerista.length > 1"
                            small
                            class="mr-2"
                            @click="visualizarJustificativa(props.item)"
                        >
                            visibility
                        </v-icon>
                        <span
                            v-else
                            class="py-2"
                        >
                            -
                        </span>
                    </td>
                </tr>
            </template>
            <template slot="footer">
                <tr
                    v-if="table && Object.keys(table).length > 0"
                    style="opacity: 0.5"
                >
                    <td colspan="7">
                        <b>Totais</b>
                    </td>
                    <td class="text-xs-right">
                        <b>{{ obterValorSolicitadoTotalParecer(table) | filtroFormatarParaReal }}</b>
                    </td>
                    <td class="text-xs-right">
                        <b>{{ obterValorSugeridoTotalParecer(table) | filtroFormatarParaReal }}</b>
                    </td>
                    <td />
                </tr>
            </template>
        </v-data-table>
        <SPlanilhaDialog
            v-model="dialog"
            :item="itemVisualizacao"
            title="Justificativas"
        >
            <template slot-scope="slotProps">
                <v-layout
                    v-if="Object.keys(slotProps.item).length >0"
                    wrap
                >
                    <v-flex
                        v-if="slotProps.item.justificitivaproponente.length > 1 "
                        xs12
                    >
                        <b class="body-1 font-weight-medium">
                            Justificativa Proponente:
                        </b>
                        <div v-html="slotProps.item.justificitivaproponente" />
                    </v-flex>
                    <v-flex
                        v-if="slotProps.item.dsJustificativaParecerista.length > 1 "
                        xs12
                    >
                        <b class="body-1 font-weight-medium">
                            Justificativa Parecerista:
                        </b>
                        <div v-html="slotProps.item.dsJustificativaParecerista" />
                    </v-flex>
                </v-layout>
            </template>
        </SPlanilhaDialog>
    </div>
</template>

<script>
import MxPlanilhaParecer from '@/modules/parecer/mixins/PlanilhaParecer';
import SPlanilhaDialog from '@/components/Planilha/PlanilhaItensDialog';

export default {
    name: 'OutrosProdutosPlanilhaItensVisualizar',
    components: { SPlanilhaDialog },
    mixins: [MxPlanilhaParecer],
    props: {
        table: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            valid: false,
            expand: false,
            dialog: false,
            loading: false,
            headers: [
                { text: '#', align: 'center', value: 'Seq' },
                { text: 'Item', align: 'left', value: 'Item' },
                { text: 'Unidade', align: 'left', value: 'UnidadeProposta' },
                { text: 'Dias', align: 'center', value: 'diasprop' },
                { text: 'Qtde', align: 'center', value: 'quantidadeprop' },
                { text: 'Ocor.', align: 'center', value: 'ocorrenciaprop' },
                { text: 'Vl. Unitário', align: 'right', value: 'valorUnitarioprop' },
                { text: 'Vl. Solicitado', align: 'right', value: 'VlSolicitado' },
                { text: 'Valor Sugerido', align: 'left', value: 'VlSugeridoParecerista' },
                { text: 'Justificativas', align: 'left', value: 'justificitivaproponente' },
            ],
            itemVisualizacao: {},
            select: {},
        };
    },
    methods: {
        visualizarJustificativa(item) {
            this.itemVisualizacao = Object.assign({}, item);
            this.dialog = true;
        },
    },
};
</script>
