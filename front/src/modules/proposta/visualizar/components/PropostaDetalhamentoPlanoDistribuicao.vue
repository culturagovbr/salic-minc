<template>
    <v-container
        fluid
    >
        <v-layout
            row
            wrap>
            <v-expansion-panel
                :value="[1]"
                expand>
                <v-expansion-panel-content
                    v-for="( detalhamento, index ) in detalhamentos"
                    :key="index">
                    <v-layout slot="header">
                        <i class="material-icons">place</i>
                        <span class="ml-2 mt-1">Detalhamento - {{ detalhamento[0].DescricaoUf }}
                            - {{ detalhamento[0].DescricaoMunicipio }}</span>
                    </v-layout>
                    <v-data-table
                        :items="detalhamento"
                        :headers="headers"
                        class="elevation-1"
                        hide-actions
                    >
                        <template
                            slot="headers"
                            slot-scope="props">
                            <tr>
                                <th colspan="3"/>
                                <th
                                    align="center"
                                    style="border: 1px solid #ddd"
                                    colspan="3">
                                    Pre&ccedil;o Popular
                                </th>
                                <th
                                    align="center"
                                    style="border: 1px solid #ddd"
                                    colspan="3">
                                    Proponente
                                </th>
                                <th/>
                            </tr>
                            <tr>
                                <th
                                    v-for="header in props.headers"
                                    :key="header.text"
                                    :class="[
                                        'column sortable',
                                        pagination.descending ? 'desc' : 'asc',
                                        header.value === pagination.sortBy ? 'active' : ''
                                    ]"
                                    @click="changeSort(header.value)"
                                >
                                    <v-icon small>arrow_upward</v-icon>
                                    {{ header.text }}
                                </th>
                            </tr>
                        </template>
                        <template
                            slot="items"
                            slot-scope="props">
                            <td>{{ props.item.dsProduto }}</td>
                            <td class="text-xs-right">{{ props.item.qtExemplares }}</td>

                            <td class="text-xs-right">
                                {{ parseInt(props.item.qtGratuitaDivulgacao) +
                                    parseInt(props.item.qtGratuitaPatrocinador) +
                                parseInt(props.item.qtGratuitaPopulacao) }}
                            </td>

                            <td class="text-xs-right">{{ props.item.qtPopularIntegral }}</td>
                            <td class="text-xs-right">{{ props.item.qtPopularParcial }}</td>
                            <td class="text-xs-right">
                                {{ props.item.vlUnitarioPopularIntegral | filtroFormatarParaReal }}
                            </td>
                            <td class="text-xs-right">{{ props.item.qtProponenteIntegral }}</td>
                            <td class="text-xs-right">{{ props.item.qtProponenteParcial }}</td>
                            <td class="text-xs-right">
                                {{ props.item.vlUnitarioProponenteIntegral | filtroFormatarParaReal }}
                            </td>
                            <td class="text-xs-right">
                                {{ props.item.vlReceitaPrevista | filtroFormatarParaReal }}</td>
                        </template>
                        <template slot="footer">
                            <PropostaDetalhamentoConsolidacao
                                :items="detalhamento"/>
                        </template>
                    </v-data-table>
                </v-expansion-panel-content>
            </v-expansion-panel>
        </v-layout>
    </v-container>
</template>
<script>
import planilhas from '@/mixins/planilhas';
import PropostaDetalhamentoConsolidacao from './PropostaDetalhamentoConsolidacao';

export default {
    name: 'PropostaDetalhamentoPlanoDistribuicao',
    components: {
        PropostaDetalhamentoConsolidacao,
    },
    mixins: [planilhas],
    props: {
        arrayDetalhamentos: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            detalhamentos: [],
            pagination: {
                sortBy: 'dsProduto',
            },
            headers: [
                { text: 'Categoria', value: 'dsProduto' },
                { text: 'Qtd.', value: 'qtExemplares' },
                { text: 'Dist. Gratuita', align: 'center', value: 'qtGratuitaDivulgacao' },
                { text: 'Qtd. Inteira (P)', align: 'center', value: 'qtPopularIntegral' },
                { text: 'Qtd. Meia (P)', align: 'center', value: 'qtPopularParcial' },
                { text: 'Preço Unitário(P)', align: 'center', value: 'vlUnitarioPopularIntegral' },
                { text: 'Qtd. Inteira (PR)', align: 'center', value: 'qtProponenteIntegral' },
                { text: 'Qtd. Meia (PR)', align: 'center', value: 'qtProponenteParcial' },
                { text: 'Preço Unitário (PR)', align: 'center', value: 'vlUnitarioProponenteIntegral' },
                { text: 'Receita Prevista', align: 'center', value: 'vlReceitaPrevista' },
            ],
        };
    },
    watch: {
        arrayDetalhamentos(value) {
            this.detalhamentos = this.montarVisualizacao(value);
        },
    },
    mounted() {
        if (typeof this.arrayDetalhamentos !== 'undefined') {
            this.detalhamentos = this.montarVisualizacao(this.arrayDetalhamentos);
        }
    },
    methods: {
        montarVisualizacao(detalhamentos) {
            const novoDetalhamento = {};
            let i = 0;
            let idMunicipio = '';

            detalhamentos.forEach((element) => {
                if (element.idMunicipio !== idMunicipio) {
                    novoDetalhamento[element.idMunicipio] = [];
                    i = 0;
                    // TODO ajustar lint
                    // eslint-disable-next-line
                    idMunicipio = element.idMunicipio;
                }

                novoDetalhamento[element.idMunicipio][i] = element;

                i += 1;
            });

            return novoDetalhamento;
        },
        changeSort(column) {
            if (this.pagination.sortBy === column) {
                this.pagination.descending = !this.pagination.descending;
            } else {
                this.pagination.sortBy = column;
                this.pagination.descending = false;
            }
        },
    },
};
</script>
