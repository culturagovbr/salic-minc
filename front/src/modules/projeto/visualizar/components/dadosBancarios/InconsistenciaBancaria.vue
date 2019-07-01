<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Inconsistência Bancária'"/>
        </div>
        <div v-else-if="dadosInconsistencia">
            <v-card>
                <div v-if="Object.keys(dadosInconsistencia).length > 0">
                    <v-container fluid>
                        <FiltroData
                            :text="'Escolha a Data de Pagamento:'"
                            @eventoFiltrarData="filtrarData"
                        />
                    </v-container>
                </div>
                <v-data-table
                    :headers="headers"
                    :items="dadosInconsistencia"
                    :pagination.sync="pagination"
                    :rows-per-page-items="[10, 25, 50, 100, {'text': 'Todos', value: -1}]"
                    class="elevation-1 container-fluid"
                >
                    <template
                        slot="items"
                        slot-scope="props">
                        <td class="text-xs-left">
                            {{ props.item.ItemOrcamentario }}
                        </td>
                        <td
                            class="text-xs-left"
                            style="width: 250px">
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
                        <td class="text-xs-right font-weight-bold">
                            {{ props.item.vlComprovado | filtroFormatarParaReal }}
                        </td>

                        <td
                            v-if="props.item.vlDebitado"
                            class="text-xs-right">
                            {{ props.item.vlDebitado | filtroFormatarParaReal }}
                        </td>
                        <td
                            v-else
                            class="text-xs-right">
                            {{ '000' | filtroFormatarParaReal }}
                        </td>

                        <td
                            v-if="props.item.vlDiferenca && props.item.vlDiferenca !== 0"
                            class="text-xs-right red--text font-weight-bold"
                        >
                            {{ props.item.vlDiferenca | filtroFormatarParaReal }}
                        </td>
                        <td
                            v-else
                            class="text-xs-right">
                            {{ '000' | filtroFormatarParaReal }}
                        </td>
                    </template>
                    <template
                        slot="pageText"
                        slot-scope="props">
                        Items {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                    </template>
                </v-data-table>
                <v-card-actions v-if="Object.keys(dadosInconsistencia).length > 0">
                    <v-spacer/>
                    <v-btn
                        small
                        fab
                        round
                        target="_blank"
                        @click="print">
                        <v-icon dark>local_printshop</v-icon>
                    </v-btn>
                </v-card-actions>
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
    name: 'InconsistenciaBancaria',
    components: {
        Carregando,
        FiltroData,
    },
    filters: {
        cnpjFilter,
    },
    mixins: [utils],
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
                    text: 'VL PAGAMENTO ',
                    align: 'center',
                    value: 'vlPagamento',
                },
                {
                    text: 'VL. COMPROVADO',
                    align: 'center',
                    value: 'vlComprovado',
                },
                {
                    text: 'VL. DEBITADO',
                    align: 'center',
                    value: 'vlDebitado',
                },
                {
                    text: 'VL. DIFERENÇA',
                    align: 'center',
                    value: 'vlDiferenca',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosInconsistencia: 'dadosBancarios/inconsistenciaBancaria',
        }),
    },
    watch: {
        dadosInconsistencia() {
            this.loading = false;
        },
        dadosProjeto(value) {
            this.loading = true;
            const params = {
                idPronac: value.idPronac,
                dtInicio: '',
                dtFim: '',
            };
            this.buscarInconsistenciaBancaria(params);
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            const params = {
                idPronac: this.dadosProjeto.idPronac,
                dtInicio: '',
                dtFim: '',
            };
            this.buscarInconsistenciaBancaria(params);
        }
    },
    methods: {
        ...mapActions({
            buscarInconsistenciaBancaria: 'dadosBancarios/buscarInconsistenciaBancaria',
        }),
        filtrarData(response) {
            const params = {
                idPronac: this.dadosProjeto.idPronac,
                dtInicio: response.dtInicio,
                dtFim: response.dtFim,
            };
            this.buscarInconsistenciaBancaria(params);
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
    },
};
</script>
