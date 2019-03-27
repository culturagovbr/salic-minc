<template>
    <v-card>
        <v-expansion-panel popout>
            <v-expansion-panel-content
                v-for="(detalhamento, index) of detalhamentos"
                :key="index"
                class="elevation-1"
            >
                <v-layout
                    slot="header"
                    class="black--text">
                    <v-icon class="mr-3 black--text">place</v-icon>
                    <span>
                        Detalhamento -
                        {{ detalhamento[0].DescricaoUf }} -
                        {{ detalhamento[0].DescricaoMunicipio }}
                    </span>
                </v-layout>
                <v-card-title>
                    <v-layout
                        justify-space-around
                        row
                        wrap>
                        <v-flex
                            lg4
                            offset-lg3
                            class="text-xs-center">
                            <h6>Preco Popular</h6>
                        </v-flex>
                        <v-flex
                            lg2
                            class="text-xs-left">
                            <h6>Proponente</h6>
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-data-table
                    :headers="headers"
                    :items="detalhamento"
                    class="elevation-1 container-fluid"
                    hide-actions
                >
                    <template
                        slot="items"
                        slot-scope="props">
                        <td class="text-xs-left">{{ props.item.dsProduto }}</td>
                        <td class="text-xs-right">{{ props.item.qtExemplares }}</td>
                        <td class="text-xs-right">
                            {{
                                parseInt(props.item.qtGratuitaDivulgacao) +
                                    parseInt(props.item.qtGratuitaPatrocinador) +
                                    parseInt(props.item.qtGratuitaPopulacao)
                            }}
                        </td>
                        <td class="text-xs-right">{{ props.item.qtPopularIntegral }}</td>
                        <td class="text-xs-right">{{ props.item.qtPopularParcial }}</td>
                        <td class="text-xs-right">{{ props.item.vlUnitarioPopularIntegral }}</td>

                        <td class="text-xs-right">{{ props.item.qtProponenteIntegral }}</td>
                        <td class="text-xs-right">{{ props.item.qtProponenteParcial }}</td>
                        <td class="text-xs-right">{{ props.item.vlUnitarioProponenteIntegral }}</td>

                        <td class="text-xs-right">{{ props.item.vlReceitaPrevista }}</td>
                    </template>
                </v-data-table>
            </v-expansion-panel-content>
        </v-expansion-panel>
    </v-card>
</template>
<script>
export default {
    name: 'DetalhamentoPlanoDistribuicao',
    props: {
        arrayDetalhamentos: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            detalhamentos: [],
            headers: [
                {
                    text: 'CATEGORIA',
                    align: 'left',
                    value: 'dsProduto',
                },
                {
                    text: 'QTD.',
                    align: 'center',
                    value: 'qtExemplares',
                },
                {
                    text: 'DIST. GRATUITA',
                    align: 'center',
                    value: 'qtGratuitaDivulgacao + qtGratuitaPatrocinador + qtGratuitaPopulacao',
                },
                {
                    text: 'QTD. INTEIRA',
                    align: 'center',
                    value: 'qtPopularIntegral',
                },
                {
                    text: 'QTD. MEIA',
                    align: 'center',
                    value: 'qtPopularParcial',
                },
                {
                    text: 'PREÇO UNITÁRIO',
                    align: 'center',
                    value: 'vlUnitarioPopularIntegral',
                },
                {
                    text: 'QTD. INTEIRA',
                    align: 'center',
                    value: 'qtProponenteIntegral',
                },
                {
                    text: 'QTD. MEIA',
                    align: 'center',
                    value: 'qtProponenteParcial',
                },
                {
                    text: 'PREÇO UNITÁRIO',
                    align: 'center',
                    value: 'vlUnitarioProponenteIntegral',
                },
                {
                    text: 'RECEITA PREVISTA',
                    align: 'center',
                    value: 'vlReceitaPrevista',
                },
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
            const idMunicipio = '';

            detalhamentos.forEach((element) => {
                if (element.idMunicipio !== idMunicipio) {
                    novoDetalhamento[element.idMunicipio] = [];
                    i = 0;
                }

                novoDetalhamento[element.idMunicipio][i] = element;

                i += 1;
            });

            return novoDetalhamento;
        },
    },
};
</script>
