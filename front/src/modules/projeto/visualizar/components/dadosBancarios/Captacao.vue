<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Captação'"/>
        </div>
        <div v-else>
            <v-card>
                <div v-if="Object.keys(dadosCaptacao.captacao).length > 0">
                    <v-container fluid>
                        <FiltroData
                            :text="'Escolha a Dt. Captação:'"
                            @eventoFiltrarData="filtrarData"
                        />
                    </v-container>
                </div>
                <v-data-table
                    :headers="headers"
                    :items="dadosCaptacao.captacao"
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
                            v-html="props.item.Nome"/>
                        <td class="text-xs-right">{{ props.item.NumeroRecibo }}</td>
                        <td
                            class="text-xs-left"
                            v-html="props.item.TipoApoio"/>
                        <td class="text-xs-center pl-5">
                            {{ props.item.DtRecibo | formatarData }}
                        </td>
                        <td class="text-xs-center pl-5">
                            {{ props.item.DtTransferenciaRecurso | formatarData }}
                        </td>
                        <td class="text-xs-right">
                            {{ props.item.CaptacaoReal | filtroFormatarParaReal }}
                        </td>
                        <td class="text-xs-right">
                            {{ parseFloat(((props.item.CaptacaoReal / (props.item.ValorCaptado))*
                            100).toFixed(2)) }}
                        </td>
                        <td
                            v-if="props.item.isBemServico == 0"
                            class="text-xs-right">Não
                        </td>
                        <td
                            v-else-if="props.item.isBemServico == 1"
                            class="text-xs-right">Sim
                        </td>
                    </template>
                    <template
                        slot="pageText"
                        slot-scope="props">
                        Items {{ props.pageStart }}
                        - {{ props.pageStop }}
                        de {{ props.itemsLength }}
                    </template>
                </v-data-table>
                <v-container
                    v-if="dadosCaptacao.vlTotal"
                    fluid>
                    <v-layout
                        row
                        wrap>
                        <v-flex xs6>
                            <h6>Total Captado</h6>
                        </v-flex>
                        <v-flex
                            xs5
                            offset-xs1
                            class=" text-xs-right">
                            <h6>R$ {{ dadosCaptacao.vlTotal | filtroFormatarParaReal }}</h6>
                        </v-flex>
                    </v-layout>
                    <div>
                        <v-layout
                            row
                            wrap>
                            <v-flex xs6>
                                <h6>Total % Captado</h6>
                            </v-flex>
                            <v-flex
                                xs5
                                offset-xs1
                                class=" text-xs-right"
                            >
                                <h6>{{ percentualCaptado }}%</h6>
                            </v-flex>
                        </v-layout>
                    </div>
                </v-container>
                <v-card-actions v-if="Object.keys(dadosCaptacao.captacao).length > 0">
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
import { utils } from '@/mixins/utils';
import FiltroData from './components/FiltroData';

export default {
    name: 'Captacao',
    components: {
        Carregando,
        FiltroData,
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
                sortBy: 'DtRecibo',
                descending: true,
            },
            selected: [],
            loading: true,
            headers: [
                {
                    text: 'INCENTIVADOR',
                    align: 'left',
                    value: 'Nome',
                },
                {
                    text: 'NR. RECIBO',
                    align: 'center',
                    value: 'NumeroRecibo',
                },
                {
                    text: 'TIPO DE APOIO',
                    align: 'left',
                    value: 'TipoApoio',
                },
                {
                    text: 'DT. CAPTAÇÃO',
                    align: 'center',
                    value: 'DtRecibo',
                },
                {
                    text: 'DT. TRANSFERÊMCIA',
                    align: 'center',
                    value: 'DtTransferenciaRecurso',
                },
                {
                    text: 'VL. CAPTADO',
                    align: 'center',
                    value: 'CaptacaoReal',
                },
                {
                    text: '% CAPTADO',
                    align: 'center',
                    value: 'PorcentagemCaptacao',
                },
                {
                    text: 'BEM/SERVIÇO',
                    align: 'center',
                    value: 'isBemServico',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosCaptacao: 'dadosBancarios/captacao',
        }),
        percentualCaptado() {
            return ((this.dadosCaptacao.vlTotal / (this.dadosProjeto.vlAutorizadoOutrasFontes + this.dadosProjeto.vlAutorizado))
                * 100).toFixed(1);
        },
    },
    watch: {
        dadosProjeto(value) {
            this.loading = true;

            const params = {
                idPronac: value.idPronac,
                dtInicio: '',
                dtFim: '',
            };
            this.buscarCaptacao(params);
        },
        dadosCaptacao() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            const params = {
                idPronac: this.dadosProjeto.idPronac,
                dtInicio: '',
                dtFim: '',
            };
            this.buscarCaptacao(params);
        }
    },
    methods: {
        ...mapActions({
            buscarCaptacao: 'dadosBancarios/buscarCaptacao',
        }),
        filtrarData(response) {
            const params = {
                idPronac: this.dadosProjeto.idPronac,
                dtInicio: response.dtInicio,
                dtFim: response.dtFim,
            };
            this.buscarCaptacao(params);
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
