<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Pagamentos Consolidados'"/>
        </div>
        <div v-else-if="dados">
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="indexItems"
                :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                item-key="id"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-center pl-5">{{ props.item.id + 1 }}</td>
                    <td class="text-xs-left">{{ props.item.UFFornecedor }}</td>
                    <td class="text-xs-left">{{ props.item.MunicipioFornecedor }}</td>
                    <td class="text-xs-right">{{ props.item.vlPagamento | filtroFormatarParaReal }}</td>
                </template>
            </v-data-table>
            <v-card>
                <v-container fluid>
                    <v-layout
                        row
                        wrap>
                        <v-flex xs6>
                            <h6 class="mr-3">TOTAL DOS PAGAMENTOS</h6>
                        </v-flex>
                        <v-flex
                            xs5
                            offset-xs1
                            class="text-xs-right">
                            <h6>
                                <v-chip
                                    outline
                                    color="black"
                                >R$ {{ valorPagamentoTotal | filtroFormatarParaReal }}
                                </v-chip>
                            </h6>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card>
        </div>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';

export default {
    name: 'PagamentosConsolidados',
    components: {
        Carregando,
    },
    mixins: [utils],
    data() {
        return {
            search: '',
            pagination: {
                sortBy: 'id',
                ascending: true,
            },
            selected: [],
            loading: true,
            headers: [
                {
                    text: 'N°',
                    align: 'center',
                    value: 'id',
                },
                {
                    text: 'UF Fornecedor',
                    align: 'left',
                    value: 'UFFornecedor',
                },
                {
                    text: 'Município Fornecedor',
                    align: 'left',
                    value: 'MunicipioFornecedor',
                },
                {
                    text: 'Vl. Pagamento',
                    align: 'right',
                    value: 'vlPagamento',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'prestacaoContas/pagamentosConsolidados',
        }),
        indexItems() {
            const currentItems = this.dados;
            return currentItems.map((item, index) => ({
                id: index,
                ...item,
            }));
        },
        valorPagamentoTotal() {
            if (Object.keys(this.dados).length === 0) {
                return 0;
            }
            const table = this.dados;
            let valor = 0;

            Object.entries(table).forEach(([, row]) => {
                valor += parseFloat(row.vlPagamento);
            });

            return valor;
        },
    },
    watch: {
        dadosProjeto(value) {
            this.loading = true;
            this.buscarPagamentosConsolidados(value.idPronac);
        },
        dados() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarPagamentosConsolidados(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarPagamentosConsolidados: 'prestacaoContas/buscarPagamentosConsolidados',
        }),
    },
};
</script>
