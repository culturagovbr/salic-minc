<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Conciliação Bancária'" />
        </div>
        <div v-else>
            <v-card>
                <div v-if="Object.keys(dadosConciliacao).length > 0">
                    <v-container fluid>
                        <FiltroData
                            :text="'Escolha a Data de Pagamento:'"
                            @eventoFiltrarData="filtrarData"
                        />
                    </v-container>
                </div>
                <v-card id="geraPdf">
                    <v-card-title>
                        <v-spacer/>
                        <v-text-field
                            v-model="search"
                            append-icon="search"
                            label="Buscar"
                            single-line
                            hide-details
                        />
                    </v-card-title>
                    <v-data-table
                        :headers="headers"
                        :items="dadosConciliacao"
                        :search="search"
                        :pagination.sync="pagination"
                        :rows-per-page-items="[10, 25, 50, 100, {'text': 'Todos', value: -1}]"
                        class="elevation-1 container-fluid"
                    >
                        <template
                            slot="items"
                            slot-scope="props"
                        >
                            <td class="text-xs-left">
                                {{ props.item.ItemOrcamentario }}
                            </td>
                            <td
                                class="text-xs-left"
                                style="width: 200px"
                            >
                                {{ props.item.CNPJCPF | cnpjFilter }}
                            </td>
                            <td class="text-xs-left">
                                {{ props.item.Fornecedor }}
                            </td>
                            <td class="text-xs-right">
                                {{ props.item.nrDocumentoDePagamento }}
                            </td>
                            <td class="text-xs-center pl-5">
                                {{ props.item.dtPagamento | formatarData }}
                            </td>
                            <td class="text-xs-right font-weight-bold">
                                {{ props.item.vlPagamento | filtroFormatarParaReal }}
                            </td>
                            <td class="text-xs-left">
                                {{ props.item.dsLancamento }}
                            </td>
                            <td
                                v-if="props.item.vlDebitado"
                                class="text-xs-right font-weight-bold"
                            >
                                {{ props.item.vlDebitado | filtroFormatarParaReal }}
                            </td>
                            <td
                                v-else
                                class="text-xs-right font-weight-bold"
                            >
                                {{ '000' | filtroFormatarParaReal }}
                            </td>

                            <td
                                v-if="props.item.vlDiferenca"
                                class="text-xs-right font-weight-bold red--text"
                            >
                                {{ props.item.vlDiferenca | filtroFormatarParaReal }}
                            </td>
                            <td
                                v-else
                                class="text-xs-right font-weight-bold"
                            >
                                {{ '000' | filtroFormatarParaReal }}
                            </td>
                        </template>
                        <template
                            slot="pageText"
                            slot-scope="props"
                        >
                            Items {{ props.pageStart }}
                            - {{ props.pageStop }}
                            de {{ props.itemsLength }}
                        </template>
                    </v-data-table>
                    <v-card-actions v-if="Object.keys(dadosConciliacao).length > 0">
                        <v-spacer />
                        <v-btn
                            small
                            fab
                            round
                            target="_blank"
                            @click="print"
                        >
                            <v-icon dark>
                                local_printshop
                            </v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-card>
        </div>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import { Printd } from 'printd';
import Carregando from '@/components/CarregandoVuetify';
import cnpjFilter from '@/filters/cnpj';
import { utils } from '@/mixins/utils';
import FiltroData from './components/FiltroData';

export default {
    name: 'ConciliacaoBancaria',
    components: {
        Carregando,
        FiltroData,
    },
    filters: {
        cnpjFilter,
    },
    mixins: [utils],
    props: {
        idPronac: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            cssText: [`
              .box {
                width: 5000px;
                text-align: left;
                padding: 1em;
              }
              body {
                  margin-top: 80px;
              }
              .v-input , button, .v-icon, .v-datatable__actions__pagination, .v-datatable__actions__select, h6, .pb-2{
                display: none !important;
              }

              th{
                width: 130px
              }

              td{
                width: 120px;
                text-align: center;
              }
              `],
            name: '',
            search: '',
            pagination: {
                sortBy: 'dtPagamento',
                descending: true,
            },
            selected: [],
            loading: true,
            headers: [
                {
                    text: 'ITEM ORÇAMENTÁRIO',
                    align: 'left',
                    value: 'ItemOrcamentario',
                },
                {
                    text: 'CNPJ / CPF',
                    align: 'left',
                    value: 'CNPJCPF',
                },
                {
                    text: 'FORNECEDOR',
                    align: 'left',
                    value: 'Fornecedor',
                },
                {
                    text: 'NÚMERO',
                    align: 'left',
                    value: 'nrDocumentoDePagamento',
                },
                {
                    text: 'DT. PAGAMENTO',
                    align: 'center',
                    value: 'dtPagamento',
                },
                {
                    text: 'VL. COMPROVADO',
                    align: 'left',
                    value: 'vlPagamento',
                },
                {
                    text: 'LANÇAMENTO',
                    align: 'left',
                    value: 'dsLancamento',
                },
                {
                    text: 'VL. DEBITADO',
                    align: 'left',
                    value: 'vlDebitado',
                },
                {
                    text: 'VL. DIFERENÇA',
                    align: 'left',
                    value: 'vlDiferenca',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosConciliacao: 'dadosBancarios/conciliacaoBancaria',
        }),
    },
    watch: {
        idPronac(value) {
            const params = {
                idPronac: value,
                dtInicio: '',
                dtFim: '',
            };
            this.buscarConciliacaoBancaria(params);
        },
    },
    mounted() {
        const params = {
            idPronac: this.idPronac,
            dtInicio: '',
            dtFim: '',
        };
        this.buscarConciliacaoBancaria(params);
    },
    methods: {
        ...mapActions({
            buscarConciliacaoBancariaAction: 'dadosBancarios/buscarConciliacaoBancaria',
        }),
        filtrarData(response) {
            const params = {
                idPronac: this.idPronac,
                dtInicio: response.dtInicio,
                dtFim: response.dtFim,
            };
            this.buscarConciliacaoBancaria(params);
        },
        print() {
            this.d = new Printd();

            const { contentWindow } = this.d.getIFrame();

            contentWindow.addEventListener(
                'beforeprint', () => {},
            );
            contentWindow.addEventListener(
                'afterprint', () => {},
            );

            this.d.print(this.$el, this.cssText);
        },
        buscarConciliacaoBancaria(params) {
            this.loading = true;
            this.buscarConciliacaoBancariaAction(params)
                .finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>
