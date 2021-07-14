<template>
    <div>
        <v-expansion-panel
            v-model="panel"
            popout
            focusable
            expand
        >
            <v-expansion-panel-content class="elevation-1">
                <v-layout
                    slot="header"
                    class="red--text"
                >
                    <v-icon class="mr-3 red--text">
                        trending_down
                    </v-icon>
                    DESPESA
                </v-layout>
                <v-card />
                <v-card>
                    <v-data-table
                        :pagination.sync="pagination"
                        :headers="headers"
                        :items="indexItems"
                        class="elevation-1 container-fluid mb-2"
                    >
                        <template
                            slot="items"
                            slot-scope="props"
                        >
                            <td class="text-xs-center pl-5">
                                {{ props.item.id + 1 }}
                            </td>
                            <td class="text-xs-left">
                                {{ props.item.Etapa }}
                            </td>
                            <td class="text-xs-left">
                                {{ props.item.Item }}
                            </td>
                            <td class="text-xs-right">
                                R$ {{ props.item.vlPagamento | filtroFormatarParaReal }}
                            </td>
                        </template>
                    </v-data-table>
                    <v-container fluid>
                        <v-layout
                            row
                            wrap
                        >
                            <v-flex xs6>
                                <h3 class="subheading mr-3 red--text">
                                    TOTAL DESPESA
                                </h3>
                            </v-flex>
                            <v-flex
                                xs5
                                offset-xs1
                                class=" text-xs-right"
                            >
                                <h3 class="subheading">
                                    <v-chip
                                        outline
                                        color="red"
                                    >
                                        R$ {{ valorDespesaTotal | filtroFormatarParaReal }}
                                    </v-chip>
                                </h3>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card>
            </v-expansion-panel-content>
        </v-expansion-panel>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'ExecucaoDespesa',
    mixins: [utils],
    props: {
        valorDespesaTotal: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            panel: [true],
            pagination: {
                sortBy: '',
                descending: true,
            },
            headers: [
                {
                    text: 'N°',
                    align: 'center',
                    value: 'id',
                },
                {
                    text: 'ETAPA',
                    align: 'left',
                    value: 'Etapa',
                },
                {
                    text: 'ITEM ORÇAMENTÁRIO',
                    align: 'left',
                    value: 'Item',
                },
                {
                    text: 'VALOR',
                    align: 'right',
                    value: 'vlPagamento',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'prestacaoContas/execucaoReceitaDespesa',
        }),
        indexItems() {
            const currentItems = this.dados.relatorioExecucaoDespesa;
            return currentItems.map((item, index) => ({
                id: index,
                ...item,
            }));
        },
    },
};
</script>
