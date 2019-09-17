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
                                    v-model="distribuicao.Observacao"
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
                                @click.native="salvarDistribuicao()"
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
                    </v-card>
                </v-container>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>

import { mapActions } from 'vuex';
import { utils } from '@/mixins/utils';

import * as TbDistribuirParecer from '@/modules/parecer/constantes/TbDistribuirParecer';
import * as TbTipoEncaminhamento from '@/modules/shared/constantes/TbTipoEncaminhamento';

import SEditorTexto from '@/components/SalicEditorTexto';

import SDialogHeader from '@/modules/parecer/components/gerenciar-dialogs/DialogHeader';

export default {
    name: 'GerenciarReanalisarProdutoDialog',
    components: {
        SDialogHeader,
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
                idAgenteParecerista: '',
                Observacao: '',
                TipoAnalise: TbDistribuirParecer.TIPO_ANALISE_PRODUTO_COMPLETO,
                siAnalise: TbDistribuirParecer.SI_ANALISE_EM_ANALISE,
                siEncaminhamento: TbTipoEncaminhamento.SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA,
            },
            obrigatorio: v => !!v || 'Este campo é obrigatório',
        };
    },

    computed: {
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
                this.definirDadosParaEdicao();
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
            this.textIsValid = e > this.minChar;
        },
        definirDadosParaEdicao() {
            this.distribuicao.idProduto = this.produto.idProduto;
            this.distribuicao.idPronac = this.produto.idPronac;
            this.distribuicao.idOrgao = this.produto.idOrgao;
            this.distribuicao.idDistribuirParecer = this.produto.idDistribuirParecer;
            this.distribuicao.idAgenteParecerista = this.produto.idAgenteParecerista;
            this.distribuicao.TipoAnalise = this.produto.tipoAnalise;
            this.distribuicao.Observacao = '';
        },
        async salvarDistribuicao() {
            if (!this.$refs.form.validate()) {
                return false;
            }

            const mensagem = 'Confirma o envio para reanálise do parecerista?';
            if (await this.$root.$confirm(mensagem) === false) {
                return false;
            }

            this.loading = true;
            return this.salvarSolicitacaoReanaliseAction(this.distribuicao)
                .then(() => {
                    this.dialog = false;
                }).finally(() => {
                    this.loading = false;
                });
        },
    },

};
</script>
