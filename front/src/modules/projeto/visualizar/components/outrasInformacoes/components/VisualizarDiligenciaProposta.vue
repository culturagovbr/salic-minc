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
                <td class="text-xs-center">{{ props.item.idPreprojeto }}</td>
                <td class="text-xs-center pl-5">{{ props.item.dataSolicitacao | formatarData }}</td>
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
            width="90%">
            <v-card>
                <v-card-text v-if="Object.keys(dadosDiligencia).length > 0">
                    <v-container fluid>
                        <v-layout
                            justify-space-around
                            row
                            wrap>
                            <v-flex
                                s12
                                m6
                                lg2
                                offset-lg1
                                dark>
                                <b>DATA DA SOLICITA&Ccedil;&Atilde;O</b>
                                <p>{{ dadosDiligencia.dataSolicitacao | formatarData }}</p>
                            </v-flex>
                            <v-flex
                                s12
                                m6
                                lg3>
                                <b>DATA DA RESPOSTA</b>
                                <p>{{ dadosDiligencia.dataResposta | formatarData }}</p>
                            </v-flex>
                        </v-layout>

                        <div v-if="dadosDiligencia.Solicitacao">
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
                                    <p v-html="dadosDiligencia.Solicitacao"/>
                                </v-flex>
                            </v-layout>
                        </div>

                        <div v-if="dadosDiligencia.Resposta">
                            <v-layout
                                justify-space-around
                                row
                                wrap>
                                <v-flex
                                    lg12
                                    dark>
                                    <b>RESPOSTA</b>
                                </v-flex>
                                <v-flex>
                                    <p v-html="dadosDiligencia.Resposta"/>
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
    name: 'VisualizarDiligenciaProposta',
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
                sortBy: 'dataSolicitacao',
                descending: true,
            },
            headers: [
                {
                    text: 'NR PROPOSTA',
                    align: 'center',
                    value: 'idPreprojeto',
                },
                {
                    text: 'DATA DA SOLICITAÇÃO',
                    align: 'center',
                    value: 'dataSolicitacao',
                },
                {
                    text: 'VISUALIZAR',
                    align: 'center',
                    sortable: false,
                    value: 'idPreprojeto',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosDiligencia: 'outrasInformacoes/diligenciaProposta',
        }),
    },
    methods: {
        showItem(item) {
            const { idPreprojeto } = item;
            const valor = item.idAvaliacaoProposta;

            this.buscarDiligenciaProposta({ idPreprojeto, valor });
            this.dialog = true;
        },
        ...mapActions({
            buscarDiligenciaProposta: 'outrasInformacoes/buscarDiligenciaProposta',
        }),
    },
};
</script>
