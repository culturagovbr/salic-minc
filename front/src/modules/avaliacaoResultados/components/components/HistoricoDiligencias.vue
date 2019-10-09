<template>
    <v-dialog
        v-model="dialog"
        transition="dialog-bottom-transition"
        scrollable
        fullscreen
    >
        <diligencia-button
            slot="activator"
            :item="obj"
            @click="obterDiligencias(idPronac)"
        />

        <v-card>
            <v-toolbar
                dark
                color="#0a420e !important"
            >
                <v-btn
                    icon
                    dark
                    @click="dialog = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>Diligências Projeto: {{ info.pronac }} - {{ info.nomeProjeto }} </v-toolbar-title>
            </v-toolbar>

            <v-card-text>
                <div>
                    <v-card-text
                        v-if="loadingDiligencias"
                    >
                        <Carregando text="Carregando ..." />
                    </v-card-text>
                    <v-data-table
                        v-else
                        :headers="headers"
                        :items="sortByDate"
                        item-key="idDiligencia"
                        class="mt-2 pt-2 elevation-1"
                        rows-per-page-text="Items por Página"
                        no-data-text="Nenhum dado encontrado"
                    >
                        <template
                            slot="items"
                            slot-scope="props"
                        >
                            <tr
                                class="line"
                                @click="props.expanded = !props.expanded; arrow = !arrow"
                            >
                                <td
                                    v-if="props.item.produto"
                                    class="text-xs-left"
                                >
                                    {{ props.item.produto }}
                                </td>
                                <td
                                    v-else
                                    class="text-xs-left"
                                >
                                    -
                                </td>
                                <td class="text-xs-left">
                                    {{ props.item.tipoDiligencia }}
                                </td>
                                <td class="text-xs-center">
                                    {{ props.item.dataSolicitacao | date }}
                                </td>
                                <td
                                    v-if="props.item.dataResposta === null "
                                    class="text-xs-center"
                                >
                                    {{ props.item.dataResposta }}
                                </td>
                                <td
                                    v-if="props.item.dataResposta !== null "
                                    class="text-xs-center"
                                >
                                    {{ props.item.dataResposta | date }}
                                </td>
                                <td class="text-xs-center">
                                    {{ statusDiligencia(props.item).prazo }}
                                </td>
                                <td class="text-xs-left">
                                    Prorrogado
                                </td>
                                <td>
                                    <v-icon v-if="arrow">
                                        keyboard_arrow_down
                                    </v-icon>
                                    <v-icon v-else>
                                        keyboard_arrow_up
                                    </v-icon>
                                </td>
                            </tr>
                        </template>
                        <template
                            slot="expand"
                            slot-scope="props"
                        >
                            <v-card flat>
                                <v-card-text>
                                    <v-container fluid>
                                        <div v-if="props.item.Solicitacao">
                                            <v-layout
                                                justify-space-around
                                                row
                                                wrap
                                            >
                                                <v-flex
                                                    lg12
                                                    dark
                                                >
                                                    <b>SOLICITAÇÃO</b>
                                                </v-flex>
                                                <v-flex>
                                                    <p v-html="props.item.Solicitacao" />
                                                </v-flex>
                                            </v-layout>
                                        </div>

                                        <div v-if="props.item.Resposta">
                                            <v-layout
                                                justify-space-around
                                                row
                                                wrap
                                            >
                                                <v-flex
                                                    lg12
                                                    dark
                                                >
                                                    <b>RESPOSTA</b>
                                                </v-flex>
                                                <v-flex>
                                                    <p v-html="props.item.Resposta" />
                                                </v-flex>
                                            </v-layout>
                                        </div>

                                        <div v-if="props.item.Arquivos && Object.keys(props.item.Arquivos).length > 0">
                                            <v-flex
                                                lg12
                                                dark
                                                class="text-xs-center"
                                            >
                                                <b>ARQUIVOS ANEXADOS</b>
                                            </v-flex>
                                            <v-container grid-list-md>
                                                <v-layout
                                                    justify-space-around
                                                    row
                                                    wrap
                                                >
                                                    <v-flex xs6>
                                                        <b>Arquivo</b>
                                                    </v-flex>
                                                    <v-flex xs2>
                                                        <b>Dt.Envio</b>
                                                    </v-flex>
                                                </v-layout>
                                                <v-layout
                                                    v-for="arquivo of props.item.Arquivos"
                                                    :key="arquivo.idArquivo"
                                                    justify-space-around
                                                    align-center
                                                    row
                                                >
                                                    <v-flex xs6>
                                                        <p>
                                                            <a
                                                                :href="`/upload/abrir?id=${arquivo.idArquivo}`"
                                                                target="_blank"
                                                            >
                                                                {{ arquivo.nmArquivo }}
                                                            </a>
                                                        </p>
                                                    </v-flex>
                                                    <v-flex xs2>
                                                        <p>
                                                            {{ arquivo.dtEnvio }}
                                                        </p>
                                                    </v-flex>
                                                </v-layout>
                                            </v-container>
                                        </div>
                                    </v-container>
                                </v-card-text>
                            </v-card>
                        </template>
                    </v-data-table>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import Vue from 'vue';
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import Data from '../../../../filters/date';
import statusDiligencia from '../../mixins/statusDiligencia';

import DiligenciaButton from '@/modules/diligencia/components/DiligenciaButton';

Vue.filter('date', Data);

export default {
    name: 'HistoricoDiligencias',
    components: {
        DiligenciaButton,
        Carregando,
    },
    mixins: [statusDiligencia],
    props: {
        idPronac: { type: String, default: '' },
        obj: {
            type: Object,
            default: () => {},
        },
        filtros: { type: String, default: '' },
    },
    data() {
        return {
            dialog: false,
            loadingDiligencias: false,
            show: {
                solicitacao: false,
                resposta: false,
                index: '',
            },
            info: {
                nomeProjeto: 'Nenhuma Diligência Registrada',
                pronac: '',
            },
            headers: [
                {
                    text: 'PRODUTO',
                    align: 'left',
                    value: 'produto',
                },
                {
                    text: 'TIPO DE DILIGÊNCIA',
                    align: 'left',
                    value: 'tipoDiligencia',
                },
                {
                    text: 'DATA DA SOLICITAÇÃO',
                    align: 'center',
                    value: 'dataSolicitacao',
                },
                {
                    text: 'DATA DA RESPOSTA',
                    align: 'center',
                    value: 'dataResposta',
                },
                {
                    text: 'PRAZO DA RESPOSTA',
                    align: 'center',
                    value: 'prazoResposta',
                },
                {
                    text: 'PRORROGADO',
                    value: 'prorrogado',
                    sortable: false,
                    align: 'left',
                },
                {
                    text: '',
                    sortable: false,
                },
            ],
            arrow: true,
        };
    },
    computed: {
        ...mapGetters({
            diligencias: 'avaliacaoResultados/diligenciasHistorico',
        }),

        sortByDate() {
            return _.orderBy(this.diligencias.items, 'dataSolicitacao', 'desc');
        },
    },
    watch: {
        filtros(e) {
            this.filtros = e;
        },
    },
    updated() {
        this.setInfo();
    },
    methods: {
        ...mapActions({
            obterDiligenciasAction: 'avaliacaoResultados/obetDadosDiligencias',
        }),
        mostrarSolicitacao(index) {
            this.show.solicitacao = !this.show.solicitacao;
            this.show.index = index;
        },
        mostrarResposta(index) {
            this.show.resposta = !this.show.resposta;
            this.show.index = index;
        },
        setInfo() {
            if (Object.keys(this.diligencias).length > 0) {
                this.info.nomeProjeto = this.diligencias.items[0].nomeProjeto;
                this.info.pronac = this.diligencias.items[0].pronac;
                return this.info;
            }
            return this.info;
        },
        obterDiligencias(params) {
            this.loadingDiligencias = true;
            this.obterDiligenciasAction(params).finally(() => {
                this.loadingDiligencias = false;
            });
        },
    },
};
</script>

<style scoped>
    .line {
        cursor: pointer;
    }
</style>
