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
                    Devolver Produto: {{ produto.nomeProduto }} - {{ produto.nomeProjeto }}
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-container>
                    <v-card>
                        <v-card-text>
                            <v-layout
                                row
                                wrap
                                class="mb-3"
                            >
                                <v-flex
                                    xs12
                                    sm2
                                    md2
                                >
                                    <b>Pronac</b><br>
                                    {{ produto.pronac }}
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm6
                                    md6
                                >
                                    <b>Nome do Projeto</b><br>
                                    <span v-html="produto.nomeProjeto" />
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm4
                                    md4
                                >
                                    <b>Produto</b><br>
                                    <span v-html="produto.nomeProduto" />
                                </v-flex>
                            </v-layout>
                            <v-layout
                                row
                                wrap
                                class="mb-3"
                            >
                                <v-flex
                                    xs12
                                    sm2
                                    md2
                                >
                                    <b>Área</b><br>
                                    {{ produto.area }}
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm6
                                    md6
                                >
                                    <b>Segmento</b><br>
                                    <span v-html="produto.segmento" />
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm4
                                    md4
                                >
                                    <b>Valor do Produto</b><br>
                                    R$ {{ produto.valor | formatarParaReal }}
                                </v-flex>
                            </v-layout>

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

export default {
    name: 'GerenciarReanalisarProdutoDialog',
    components: {
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
            return 'Confirma o envio para devolução à Secult?';
        },
        labelTextoRico() {
            return 'Observação para devolução';
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
            salvarAction: 'parecer/salvarDevolucaoParaSecult',
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
            this.salvarAction(this.distribuicao)
                .then(() => {
                    this.dialog = false;
                }).finally(() => {
                    this.loading = false;
                });
        },
    },

};
</script>
