<template>
    <v-dialog
        v-model="dialogDetalhamento"
        row
        justify-center
        @keydown.esc="dialogDetalhamento = false"
    >
        <v-card
            tile
        >
            <v-toolbar
                card
                dark
                color="primary lighten-1"
            >
                <v-btn
                    icon
                    dark
                    @click="dialogDetalhamento = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>
                    Visualizar diligência
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <s-carregando
                    v-if="Object.keys(diligencia).length === 0"
                    text="Carregando detalhamento da diligência"
                />
                <v-container
                    v-else
                    fluid
                    grid-list-lg
                >
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex
                            xs12
                        >
                            <h3>Dados da Solicitação</h3>
                            <v-divider />
                        </v-flex>
                        <v-flex
                            xs12
                            sm3
                            md3
                        >
                            <b>Tipo</b>
                            <div v-html="diligencia.tipoDiligencia" />
                        </v-flex>
                        <v-flex
                            xs12
                            sm3
                            md3
                        >
                            <b>Data</b>
                            <div>{{ diligencia.dataSolicitacao | formatarData }}</div>
                        </v-flex>
                        <v-flex
                            v-if="diligencia.produto"
                            xs12
                            sm3
                            md3
                        >
                            <b>Produto</b>
                            <div>{{ diligencia.produto }}</div>
                        </v-flex>
                        <v-flex
                            xs12
                            sm12
                            md12
                        >
                            <b>Solicitação:</b>
                            <div v-html="diligencia.Solicitacao " />
                        </v-flex>
                    </v-layout>
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex
                            xs12
                        >
                            <h3>Dados da Resposta</h3>
                            <v-divider />
                        </v-flex>
                        <template v-if="diligencia.Resposta">
                            <v-flex
                                xs12
                                sm3
                                md3
                            >
                                <b>Data Resposta</b>
                                <div>{{ diligencia.dataResposta | formatarData }}</div>
                            </v-flex>
                            <v-flex
                                xs12
                                sm12
                                md12
                            >
                                <b>Resposta</b>
                                <div v-html="diligencia.Resposta" />
                            </v-flex>
                        </template>
                        <v-flex
                            v-else
                            xs12
                        >
                            <div class="text-xs-left">
                                Esta diligência ainda não foi respondida.
                            </div>
                        </v-flex>
                    </v-layout>
                    <v-layout
                        v-if="diligencia.documentosExigidos"
                        row
                        wrap
                    >
                        <v-flex
                            xs12
                        >
                            <h3>Documentos exigidos</h3>
                            <v-divider />
                        </v-flex>
                        <v-flex
                            v-if="diligencia.documentosExigidos"
                            xs12
                            sm12
                            md12
                        >
                            <v-list>
                                <v-list-tile
                                    v-for="item in diligencia.documentosExigidos"
                                    :key="item.Codigo"
                                    avatar
                                >
                                    <v-list-tile-content>
                                        <v-list-tile-title v-text="item.Descricao" />
                                    </v-list-tile-content>
                                </v-list-tile>
                            </v-list>
                        </v-flex>
                    </v-layout>
                    <v-layout
                        v-if="diligencia.anexos"
                        row
                        wrap
                    >
                        <v-flex
                            xs12
                        >
                            <h3>Documentos anexados</h3>
                            <v-divider class="mb-2" />
                            <v-data-table
                                :headers="headerDocumentosAnexados"
                                :items="diligencia.anexos"
                                class="elevation-0"
                                item-key="idArquivo"
                                hide-actions
                            >
                                <template
                                    slot="items"
                                    slot-scope="props"
                                >
                                    <td>{{ props.item.dtEnvio | formatarData }}</td>
                                    <td>
                                        <a
                                            :href="`/default/upload/abrir?id=${props.item.idArquivo}`"
                                            target="_blank"
                                            v-html="props.item.nmArquivo"
                                        />
                                    </td>
                                </template>
                            </v-data-table>
                        </v-flex>
                    </v-layout>
                    <v-divider class="mb-2" />
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex
                            xs12
                        >
                            <b>Parecerista</b>
                            <div>{{ diligencia.nomeParecerista }}</div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import mixinUtils from '@/mixins/utils';
import SCarregando from '@/components/CarregandoVuetify';

export default {
    name: 'SDialoagDiligenciaDetalhamento',
    components: { SCarregando },
    mixins: [mixinUtils],
    props: {
        value: {
            type: Boolean,
            default: false,
        },
        item: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            dialogDetalhamento: false,
            loading: true,
            headerDocumentosAnexados: [
                { text: 'Data envio', value: 'dtEnvio' },
                { text: 'Documento', value: 'nmArquivo' },
            ],
        };
    },
    computed: {
        ...mapGetters({
            diligencia: 'diligencia/getDiligencia',
        }),
    },
    watch: {
        value(val) {
            this.dialogDetalhamento = val;
        },
        dialogDetalhamento(val) {
            this.$emit('input', val);
        },
        item(val) {
            if (Object.keys(val).length > 0 && this.value) {
                this.visualizarDetalhesDiligencia(val);
            }
        },
    },
    methods: {
        ...mapActions({
            obterDiligencia: 'diligencia/obterDiligencia',
        }),
        visualizarDetalhesDiligencia(diligencia) {
            this.obterDiligencia({
                idDiligencia: diligencia.idDiligencia,
                idPronac: diligencia.IdPRONAC,
            });
        },
    },
};
</script>
