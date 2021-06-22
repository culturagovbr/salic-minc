<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Extratos Bancários Consolidado'"/>
        </div>
        <v-card v-else>
            <div v-if="Object.keys(dadosExtratosConsolidado).length > 0">
                <v-container fluid>
                    <FiltroTipoConta
                        @eventoSearch="search = $event"
                    />
                </v-container>
            </div>
            <v-data-table
                :headers="headers"
                :items="dadosExtratosConsolidado"
                :pagination.sync="pagination"
                :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                :search="search"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td
                        class="text-xs-left"
                        v-html="props.item.TipoConta"/>
                    <td class="text-xs-right">{{ props.item.NrConta | formatarConta }}</td>
                    <td class="text-xs-right">{{ props.item.Codigo }}</td>
                    <td
                        class="text-xs-left"
                        v-html="props.item.Lancamento"/>

                    <td
                        v-if="props.item.stLancamento === 'C'"
                        class="text-xs-right blue--text font-weight-bold"
                    >
                        {{ props.item.vlLancamento | filtroFormatarParaReal }}
                    </td>
                    <td
                        v-else
                        class="text-xs-right red--text font-weight-bold">
                        {{ props.item.vlLancamento | filtroFormatarParaReal }}
                    </td>

                    <td
                        v-if="props.item.stLancamento === 'C'"
                        class="text-xs-right blue--text font-weight-bold"
                    >
                        {{ props.item.stLancamento }}
                    </td>
                    <td
                        v-else
                        class="text-xs-right red--text font-weight-bold">
                        {{ props.item.stLancamento }}
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
            <v-card-actions v-if="Object.keys(dadosExtratosConsolidado).length > 0">
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
import { utils } from '@/mixins/utils';
import Carregando from '@/components/CarregandoVuetify';
import FiltroTipoConta from './components/FiltroTipoConta';

export default {
    name: 'ExtratosBancariosConsolidado',
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
                sortBy: 'fat',
            },
            selected: [],
            loading: true,
            headers: [
                {
                    text: 'TIPO DA CONTA',
                    align: 'left',
                    value: 'TipoConta',
                },
                {
                    text: 'NR. CONTA',
                    align: 'center',
                    value: 'NrConta',
                },
                {
                    text: 'CÓDIGO',
                    align: 'center',
                    value: 'Codigo',
                },
                {
                    text: 'LANÇAMENTO',
                    align: 'left',
                    value: 'Lancamento',
                },
                {
                    text: 'VL. LANÇAMENTO',
                    align: 'center',
                    value: 'vlLancamento',
                },
                {
                    text: 'D/C',
                    align: 'center',
                    value: 'stLancamento',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosExtratosConsolidado: 'dadosBancarios/extratosBancariosConsolidado',
        }),
    },
    watch: {
        dadosExtratosConsolidado() {
            this.loading = false;
        },
        dadosProjeto(value) {
            this.loading = true;
            this.buscarExtratosBancariosConsolidado(value.idPronac);
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarExtratosBancariosConsolidado(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarExtratosBancariosConsolidado: 'dadosBancarios/buscarExtratosBancariosConsolidado',
        }),
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
