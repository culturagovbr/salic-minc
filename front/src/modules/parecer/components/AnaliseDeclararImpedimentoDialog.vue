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
                    Declarar impedimento: {{ produto.pronac }} - {{ produto.nomeProjeto }}
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

                            <v-form
                                ref="form"
                                v-model="valid"
                            >
                                <s-editor-texto
                                    v-model="declaracao.Observacao"
                                    :min-char="minChar"
                                    placeholder="Justifique o motivo do impedimento"
                                    @editor-texto-counter="validateText($event)"
                                />
                            </v-form>
                        </v-card-text>
                        <v-card-actions class="justify-center">
                            <v-btn
                                :loading="loading"
                                :disabled="!valid || !textIsValid || loading"
                                color="primary"
                                @click.native="dialogConfirmarEnvio = true"
                            >
                                <v-icon left>
                                    send
                                </v-icon>
                                Salvar e devolver produto
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
                            text="O produto será devolvido para o Coordenador de Parecer"
                            @dialog-response="$event && enviarDeclaracao()"
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
    name: 'AnaliseDeclararImpedimentoDialog',
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
            default: () => {},
        },
    },

    data() {
        return {
            dialog: false,
            loading: false,
            minChar: 10,
            valid: false,
            textIsValid: false,
            declaracao: {
                idDistribuirParecer: '',
                idProduto: '',
                idPronac: '',
                Observacao: '',
            },
            dialogConfirmarEnvio: false,
        };
    },

    mounted() {
        this.dialog = this.value;
    },

    watch: {
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            this.declaracao = {
                idDistribuirParecer: this.produto.idDistribuirParecer,
                idProduto: this.produto.idProduto,
                idPronac: this.produto.idPronac,
                Observacao: '',
            };
            this.$emit('input', val);
        },
    },

    methods: {
        ...mapActions({
            salvar: 'parecer/salvarDeclaracaoImpedimento',
        }),
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
        enviarDeclaracao() {
            this.loading = true;
            this.salvar(this.declaracao).then(() => {
                this.dialog = false;
            }).finally(() => {
                this.loading = false;
            });
        },
    },
};
</script>
