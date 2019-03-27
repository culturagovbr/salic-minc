<template>
    <div>
        <v-data-table
            :pagination.sync="pagination"
            :headers="headers"
            :items="diligencias"
            class="elevation-1"
        >
            <template
                slot="items"
                slot-scope="props">
                <td class="text-xs-center pl-5">{{ props.item.dtAvaliacao | formatarData }}</td>
                <td
                    class="text-xs-left"
                    v-html="props.item.tipoDiligencia"/>
                <td class="text-xs-center">
                    <v-tooltip bottom>
                        <v-btn
                            slot="activator"
                            flat
                            icon
                            @click="showItem(props.item)"
                        >
                            <v-icon>visibility</v-icon>
                        </v-btn>
                        <span>Visualizar Projeto</span>
                    </v-tooltip>
                </td>
            </template>
            <template
                slot="pageText"
                slot-scope="props">
                Items {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
            </template>
        </v-data-table>

        <v-dialog
            v-model="dialog"
            width="80%">
            <v-card>
                <v-card-text v-if="Object.keys(dadosDiligencia).length > 0">
                    <v-container fluid>
                        <div v-if="dadosDiligencia.dsAvaliacao">
                            <v-layout
                                justify-space-around
                                row
                                wrap>
                                <v-flex
                                    lg12
                                    dark>
                                    <b>SOLICITAÇÃO</b>
                                </v-flex>
                                <v-flex>
                                    <p v-html="dadosDiligencia.dsAvaliacao"/>
                                </v-flex>
                            </v-layout>
                        </div>

                    </v-container>
                </v-card-text>
                <v-card-text v-else>
                    <Carregando :text="'Carregando ...'"/>
                </v-card-text>
                <v-divider/>
                <v-card-actions>
                    <v-spacer/>
                    <v-btn
                        color="red"
                        flat
                        @click="dialog = false">
                        Fechar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';

export default {
    name: 'VisualizarDiligenciaAdequacao',
    components: {
        Carregando,
    },
    mixins: [utils],
    props: {
        diligencias: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            dialog: false,
            pagination: {
                rowsPerPage: 10,
                sortBy: 'dtAvaliacao',
                descending: true,
            },
            headers: [
                {
                    text: 'DATA DA AVALIAÇÃO',
                    align: 'center',
                    value: 'dtAvaliacao',
                },
                {
                    text: 'TIPO DE DILIGÊNCIA',
                    align: 'left',
                    value: 'tipoDiligencia',
                },
                {
                    text: 'VISUALIZAR',
                    align: 'center',
                    sortable: false,
                    value: '',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosDiligencia: 'outrasInformacoes/diligenciaAdequacao',
        }),
    },
    methods: {
        showItem(item) {
            const { idPronac } = this.dadosProjeto;
            const valor = item.idAvaliarAdequacaoProjeto;

            this.buscarDiligenciaAdequacao({ idPronac, valor });
            this.dialog = true;
        },
        ...mapActions({
            buscarDiligenciaAdequacao: 'outrasInformacoes/buscarDiligenciaAdequacao',
        }),
    },
};
</script>
