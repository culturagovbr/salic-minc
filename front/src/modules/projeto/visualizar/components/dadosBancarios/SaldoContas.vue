<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Saldo das Contas'"/>
        </div>
        <v-card v-else>
            <div v-if="Object.keys(dadosSaldo).length > 0">
                <v-container fluid>
                    <FiltroTipoConta
                        @eventoSearch="search = $event"
                    />
                </v-container>
            </div>
            <v-data-table
                :headers="headers"
                :items="dadosSaldo"
                :search="search"
                :pagination.sync="pagination"
                :rows-per-page-items="[10, 25, 50, 100, {'text': 'Todos', value: -1}]"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-left">{{ props.item.Tipo }}</td>
                    <td
                        class="text-xs-left"
                    >
                        {{ props.item.NrConta | formatarConta }}
                    </td>
                    <td class="text-xs-left">{{ props.item.TipoSaldo }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.dtSaldoBancario | formatarData }}</td>
                    <td
                        v-if="props.item.vlSaldoBancario === 0"
                        class="text-xs-right blue--text font-weight-bold"
                    >
                        {{ '0' | filtroFormatarParaReal }}
                    </td>
                    <td
                        v-else-if="props.item.stSaldoBancario !== 'C'"
                        class="text-xs-right red--text font-weight-bold">
                        {{ props.item.vlSaldoBancario | filtroFormatarParaReal }}
                    </td>
                    <td
                        v-else
                        class="text-xs-right blue--text font-weight-bold">
                        {{ props.item.vlSaldoBancario | filtroFormatarParaReal }}
                    </td>

                    <td
                        v-if="props.item.stSaldoBancario === 'C'"
                        class="text-xs-center blue--text font-weight-bold pl-5"
                    >
                        {{ props.item.stSaldoBancario }}
                    </td>
                    <td
                        v-else
                        class="text-xs-center red--text font-weight-bold pl-5">
                        {{ props.item.stSaldoBancario }}
                    </td>
                </template>
                <template
                    slot="pageText"
                    slot-scope="props">
                    Items {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                </template>
                <v-alert
                    slot="no-results"
                    :value="true"
                    color="error"
                    icon="warning">
                    Sua busca por "{{ search }}" não encontrou nenhum resultado.
                </v-alert>
            </v-data-table>
            <v-card-actions v-if="Object.keys(dadosSaldo).length > 0">
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
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import { Printd } from 'printd';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';
import FiltroTipoConta from './components/FiltroTipoConta';

export default {
    name: 'SaldoContas',
    components: {
        Carregando,
        FiltroTipoConta,
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
            items: [
                'Captação',
                'Movimentação',
            ],
            search: '',
            pagination: {
                sortBy: 'dtSaldoBancario',
                descending: true,
            },
            selected: [],
            loading: true,
            headers: [
                {
                    text: 'TIPO DA CONTA',
                    align: 'left',
                    value: 'Tipo',
                },
                {
                    text: 'NR. CONTA',
                    align: 'left',
                    value: 'NrConta',
                },
                {
                    text: 'TIPO DE SALDO',
                    align: 'left',
                    value: 'TipoSaldo',
                },
                {
                    text: 'DT. SALDO',
                    align: 'center',
                    value: 'dtSaldoBancario',
                },
                {
                    text: 'VL. SALDO',
                    align: 'center',
                    value: 'vlSaldoBancario',
                },
                {
                    text: 'D/C',
                    align: 'center',
                    value: 'stSaldoBancario',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosSaldo: 'dadosBancarios/saldoContas',
        }),
    },
    watch: {
        dadosSaldo() {
            this.loading = false;
        },
        dadosProjeto(value) {
            this.loading = true;
            this.buscarSaldoContas(value.idPronac);
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarSaldoContas(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarSaldoContas: 'dadosBancarios/buscarSaldoContas',
        }),
        print() {
            this.d = new Printd();

            const { contentWindow } = this.d.getIFrame();

            contentWindow.addEventListener(
                'beforeprint', () => {
                },
            );
            contentWindow.addEventListener(
                'afterprint', () => {
                },
            );
            this.d.print(this.$el, this.cssText);
        },
    },
};
</script>
