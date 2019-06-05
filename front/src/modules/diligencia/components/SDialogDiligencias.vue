<template>
    <v-dialog
        v-model="dialog"
        fullscreen
        hide-overlay
        transition="dialog-bottom-transition"
        scrollable
    >
        <v-card
            tile
        >
            <v-toolbar
                card
                dark
                color="primary"
            >
                <v-btn
                    icon
                    dark
                    @click="dialog = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>
                    Diligências
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <s-carregando
                    v-if="loading"
                    text="Carregando diligências"
                />
                <div v-else>
                    <v-data-table
                        :headers="headers"
                        :items="diligencias"
                        disable-initial-sort
                        class="elevation-0"
                        item-key="idDiligencia"
                    >
                        <template
                            slot="items"
                            slot-scope="props"
                        >
                            <td>
                                <span
                                    v-text="props.item.pronac"
                                />
                            </td>
                            <td>
                                <span
                                    v-text="props.item.nomeProjeto"
                                />
                            </td>
                            <td>
                                <span
                                    v-text="props.item.produto"
                                />
                            </td>
                            <td>
                                <span
                                    v-text="props.item.tipoDiligencia"
                                />
                            </td>
                            <td>
                                {{ props.item.dataSolicitacao | formatarData }}
                            </td>
                            <td>{{ props.item.dataResposta | formatarData }}</td>
                            <td class="justify-center layout px-0">
                                <v-tooltip
                                    bottom
                                >
                                    <v-btn
                                        slot="activator"
                                        flat
                                        icon
                                        class="mr-2"
                                        @click="abrirModal(props.item)"
                                    >
                                        <v-icon>
                                            visibility
                                        </v-icon>
                                    </v-btn>
                                    <span>Visualizar diligência</span>
                                </v-tooltip>
                            </td>
                        </template>
                    </v-data-table>
                    <s-dialog-diligencia-detalhamento
                        v-model="dialogDetalhamento"
                        :item="diligenciaVisualizacao"
                    />

                    <div
                        class="text-xs-center mt-3"
                    >
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                :disabled="isDiligenciaAberta"
                                color="primary"
                                @click="dialogCriarDiligencia = !dialogCriarDiligencia"
                            >
                                <v-icon left>
                                    notification_important
                                </v-icon>
                                Nova diligência
                            </v-btn>
                            <span v-if="!isDiligenciaAberta">Criar nova diligência</span>
                            <span v-else>Existe uma diligência em aberto</span>
                        </v-tooltip>
                        <v-btn
                            @click="dialog = false"
                        >
                            <v-icon left>
                                clear
                            </v-icon>
                            Fechar
                        </v-btn>
                    </div>
                    <s-dialog-criar-diligencia
                        v-if="!isDiligenciaAberta"
                        v-model="dialogCriarDiligencia"
                        :id-pronac="idPronac"
                        :id-produto="idProduto"
                        :tp-diligencia="tpDiligencia"
                        :situacao="situacao"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import SCarregando from '@/components/CarregandoVuetify';
import SDialogDiligenciaDetalhamento from './SDialogDiligenciaDetalhamento';
import { utils } from '@/mixins/utils';
import SDialogCriarDiligencia from './SDialogCriarDiligencia';

export default {
    name: 'SDialogDiligencias',
    components: {
        SDialogCriarDiligencia,
        SDialogDiligenciaDetalhamento,
        SCarregando,
    },
    mixins: [utils],
    props: {
        value: {
            type: Boolean,
            default: false,
        },
        idPronac: {
            type: Number,
            required: true,
        },
        tpDiligencia: {
            type: Number,
            default: null,
        },
        idProduto: {
            type: Number,
            default: null,
        },
        situacao: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            dialog: false,
            dialogDetalhamento: false,
            dialogCriarDiligencia: false,
            loading: true,
            headers: [
                { text: 'Pronac', value: 'pronac' },
                { text: 'Nome Projeto', value: 'nomeProjeto' },
                { text: 'Produto', value: 'produto' },
                { text: 'Tipo', value: 'tipoDiligencia' },
                { text: 'Dt. Solicitação', value: 'dataSolicitacao' },
                { text: 'Dt. Resposta', value: 'dataResposta' },
                {
                    text: 'Ações', value: 'idDiligencia', align: 'center', sortable: false,
                },
            ],
            diligenciaVisualizacao: {
                type: Object,
                default: () => {},
            },
        };
    },
    computed: {
        ...mapGetters({
            diligencias: 'diligencia/getDiligencias',
        }),
        isDiligenciaAberta() {
            return this.diligencias.length > 0 && typeof this.diligencias.find(item => !item.dataResposta) === 'object';
        },
    },
    watch: {
        value(val) {
            this.dialog = val;
            if (val) {
                this.obterDiligencias({
                    idPronac: this.idPronac,
                    idProduto: this.idProduto,
                    situacao: this.situacao,
                    tpDiligencia: this.tpDiligencia,
                });
                this.loading = true;
            }
        },
        dialog(val) {
            this.$emit('input', val);
        },
        diligencias() {
            this.loading = false;
        },
    },
    methods: {
        ...mapActions({
            obterDiligencias: 'diligencia/obterDiligenciasProduto',
        }),
        abrirModal(produto) {
            this.diligenciaVisualizacao = produto;
            this.dialogDetalhamento = true;
        },
    },
};
</script>