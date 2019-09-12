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
                    Solicitar reanálise Produto: {{ produto.nomeProduto }} - {{ produto.nomeProjeto }}
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-container>
                    <v-card>
                        <v-card-text>
                            <s-dialog-header :produto="produto" />

                            <v-form
                                ref="form"
                                v-model="valid"
                                lazy-validation
                            >
                                <s-editor-texto
                                    v-model="distribuicao.observacao"
                                    :label="labelTextoRico"
                                    :min-char="minChar"
                                    @editor-texto-counter="validarTexto($event)"
                                />
                            </v-form>
                        </v-card-text>
                        <v-card-actions class="justify-center">
                            <v-btn
                                :loading="loading"
                                :disabled="!valid || !textIsValid || loading"
                                color="primary"
                                @click.native="abrirDialogConfirmacao()"
                            >
                                <v-icon left>
                                    send
                                </v-icon>
                                Enviar
                            </v-btn>
                            <v-btn
                                @click="dialog = false"
                            >
                                <v-icon left>
                                    clear
                                </v-icon>
                                Cancelar
                            </v-btn>
                        </v-card-actions>
                        <s-confirmacao-dialog
                            v-model="dialogConfirmarEnvio"
                            :text="textoMensagemConfirmacao"
                            @dialog-response="$event && salvarDistribuicao()"
                        />
                    </v-card>
                </v-container>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>

import { mapActions } from 'vuex';
import { utils } from '@/mixins/utils';
import SEditorTexto from '@/components/SalicEditorTexto';
import SConfirmacaoDialog from '@/components/SalicConfirmacaoDialog';

import SDialogHeader from '@/modules/parecer/components/gerenciar-dialogs/DialogHeader';

export default {
    name: 'GerenciarReanalisarProdutoDialog',
    components: {
        SDialogHeader,
        SConfirmacaoDialog,
        SEditorTexto,
    },
    mixins: [utils],

    props: {
        value: {
            type: Boolean,
            default: false,
        },
        produto: {
            type: Object,
            default: () => {
            },
        },
        filtro: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            dialog: false,
            loadingVinculadas: true,
            loading: false,
            minChar: 10,
            valid: false,
            textIsValid: false,
            distribuicao: {
                idDistribuirParecer: '',
                idProduto: '',
                idPronac: '',
                idOrgao: '',
                idSegmentoProduto: '',
                idAreaProduto: '',
                idOrgaoDestino: '',
                idAgenteParecerista: '',
                filtro: '',
                observacao: '',
                tipoAcao: 'distribuir',
                distribuirProjeto: false,
            },
            dialogConfirmarEnvio: false,
            obrigatorio: v => !!v || 'Este campo é obrigatório',
        };
    },

    computed: {
        textoMensagemConfirmacao() {
            return 'Confirma o envio para reanálise do parecerista?';
        },
        labelTextoRico() {
            return 'Observação para reanálise';
        },
    },

    watch: {
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            if (val) {
                this.distribuicao.idProduto = this.produto.idProduto;
                this.distribuicao.idPronac = this.produto.idPronac;
                this.distribuicao.idOrgao = this.produto.idOrgao;
                this.distribuicao.idDistribuirParecer = this.produto.idDistribuirParecer;
                this.distribuicao.idSegmentoProduto = this.produto.idSegmento;
                this.distribuicao.idAreaProduto = this.produto.idArea;
                this.distribuicao.filtro = this.filtro;
                this.distribuicao.idOrgaoDestino = '';
                this.distribuicao.observacao = '';
                this.distribuicao.idAgenteParecerista = this.produto.idAgenteParecerista;
            }
            this.$emit('input', val);
        },
    },

    mounted() {
        this.dialog = this.value;
    },

    methods: {
        ...mapActions({
            salvarSolicitacaoReanaliseAction: 'parecer/salvarSolicitacaoReanalise',
        }),
        validarTexto(e) {
            this.textIsValid = e >= this.minChar;
        },
        abrirDialogConfirmacao() {
            if (this.$refs.form.validate()) {
                this.dialogConfirmarEnvio = true;
            }
        },
        salvarDistribuicao() {
            if (!this.$refs.form.validate()) {
                return;
            }

            this.loading = true;
            this.salvarSolicitacaoReanaliseAction(this.distribuicao)
                .then(() => {
                    this.dialog = false;
                }).finally(() => {
                    this.loading = false;
                });
        },
    },

};
</script>
