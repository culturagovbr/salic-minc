<template>
    <div class="itens">
        <v-data-table
            :headers="headers"
            :items="table"
            :rows-per-page-items="[-1]"
            :loading="loading"
            item-key="idPlanilhaAprovacao"
            class="elevation-1"
            hide-actions
        >
            <v-progress-linear
                slot="progress"
                color="blue"
                indeterminate/>
            <template
                slot="items"
                slot-scope="props"
            >
                <tr
                    :class="getClassItem(props.item)"
                    style="cursor: pointer"
                    @click="props.expanded = !props.expanded"
                >
                    <td class="text-xs-left">
                        <a
                            v-if="isItemDisponivelEdicao(props.item) && !readonly"
                            href="javascript:void(0);"
                        >
                            {{ props.item.Item }}
                        </a>
                        <span v-else>
                            {{ props.item.Item }}
                        </span>
                    </td>
                    <td class="text-xs-center">{{ props.item.Unidade }}</td>
                    <td class="text-xs-center">{{ props.item.QtdeDias }}</td>
                    <td class="text-xs-center">{{ props.item.Quantidade }}</td>
                    <td class="text-xs-center">{{ props.item.Ocorrencia }}</td>
                    <td class="text-xs-right">{{ props.item.vlUnitario | filtroFormatarParaReal }}</td>
                    <td class="text-xs-right">{{ props.item.vlAprovado | filtroFormatarParaReal }}</td>
                    <td class="text-xs-right">{{ props.item.vlComprovado | filtroFormatarParaReal }}</td>
                    <td class="text-xs-right">{{ decodeHtml(props.item.dsJustificativa) }}</td>
                </tr>
            </template>
            <template
                slot="expand"
                slot-scope="props">
                <v-layout
                    wrap
                    column
                    class="blue-grey lighten-5 pa-2">
                    <v-card>
                        <v-card-title class="py-1">
                            <h3>Visualizando item: {{ props.item.Item }} </h3>
                        </v-card-title>
                        <v-divider/>
                        <v-card-text>
                            <div
                                v-if="isItemDisponivelEdicao(props.item) && !readonly"
                            >
                                <v-card>
                                    <visualizar-item-planilha
                                        :item="props.item"
                                    >
                                        <v-layout
                                            slot="header"
                                            row
                                        >
                                            <v-flex
                                                xs12
                                            >
                                                <h3
                                                    class="subheading"
                                                >
                                                    Dados originais
                                                </h3>
                                            </v-flex>
                                        </v-layout>
                                    </visualizar-item-planilha>
                                </v-card>
                                <editar-item-planilha
                                    :item="props.item"
                                    @fechar-item="props.expanded = false"
                                />
                            </div>
                            <div v-else>
                                <visualizar-item-planilha
                                    :item="props.item"
                                />
                            </div>
                        </v-card-text>
                    </v-card>
                </v-layout>
            </template>
            <template slot="footer">
                <tr
                    v-if="table && Object.keys(table).length > 0"
                    style="opacity: 0.5">
                    <td colspan="7"><b>Totais</b></td>
                    <td class="text-xs-right"><b>{{ obterValorAprovadoTotal(table) | filtroFormatarParaReal }}</b></td>
                    <td class="text-xs-right"><b>{{ obterValorComprovadoTotal(table) | filtroFormatarParaReal }}</b></td>
                </tr>
            </template>
        </v-data-table>
    </div>
</template>

<script>
import EditarItemPlanilha from './EditarItemPlanilha';
import VisualizarItemPlanilha from './VisualizarItemPlanilha';
import MxPlanilhaReadequacao from '../mixins/PlanilhaReadequacao';
import { utils } from '@/mixins/utils';

export default {
    name: 'PlanilhaItensSaldo',
    components: {
        EditarItemPlanilha,
        VisualizarItemPlanilha,
    },
    mixins: [
        MxPlanilhaReadequacao,
        utils,
    ],
    props: {
        table: {
            type: Array,
            required: true,
        },
        readonly: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            valid: false,
            expand: false,
            loading: false,
            headers: [
                { text: 'Item', align: 'left', value: 'Item' },
                { text: 'Unidade', align: 'left', value: 'Unidade' },
                { text: 'Dias', align: 'center', value: 'QtdeDias' },
                { text: 'Qtde', align: 'center', value: 'Quantidade' },
                { text: 'Ocor.', align: 'center', value: 'Ocorrencia' },
                { text: 'Vl. Unitário', align: 'right', value: 'vlUnitario' },
                { text: 'Vl. Total', align: 'right', value: 'vlAprovado' },
                { text: 'Vl. Comprovado', align: 'right', value: 'vlComprovado' },
                { text: 'Justificativa', align: 'right', value: 'dsJustificativa' },
            ],
            itemEmEdicao: {},
            select: {},
        };
    },
    methods: {
        getClassItem(row) {
            if (!this.readonly) {
                return this.obterClasseItem(row);
            }
            return '';
        },
    },
};
</script>
