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
                    Gerenciar Produto: {{ produto.nomeProduto }} - {{ produto.nomeProjeto }}
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
                                <v-layout
                                    row
                                    wrap
                                    class="mb-3"
                                >
                                    <v-flex
                                        xs12
                                        sm6
                                        md6
                                    >
                                        <v-radio-group v-model="distribuicao.tipoAcao">
                                            <template v-slot:label>
                                                <div>Qual ação você pretende realizar?</div>
                                            </template>
                                            <v-radio value="distribuir">
                                                <template v-slot:label>
                                                    <strong class="primary--text">Distribuir para um novo
                                                        parecerista</strong>
                                                </template>
                                            </v-radio>
                                            <v-radio value="encaminhar">
                                                <template v-slot:label>
                                                    <strong class="primary--text">Encaminhar para uma unidade
                                                        vinculada</strong>
                                                </template>
                                            </v-radio>
                                        </v-radio-group>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm6
                                        md6
                                        class="mt-3"
                                    >
                                        <v-select
                                            v-if="distribuicao.tipoAcao === 'distribuir'"
                                            v-model="distribuicao.idAgenteParecerista"
                                            :items="pareceristas"
                                            :item-text="formatarSelectParecerista"
                                            item-value="idParecerista"
                                            :label="loadingPareceristas ? 'Carregando...' : 'Selecione o parecerista'"
                                            :loading="loadingPareceristas"
                                            :rules="[obrigatorio]"
                                        />
                                        <v-select
                                            v-if="distribuicao.tipoAcao === 'encaminhar'"
                                            v-model="distribuicao.idOrgaoDestino"
                                            :items="vinculadas"
                                            item-text="Sigla"
                                            item-value="Codigo"
                                            :label="loadingVinculadas ? 'Carregando...' : 'Selecione o orgão destino'"
                                            :loading="loadingVinculadas"
                                            :rules="[obrigatorio]"
                                        />
                                    </v-flex>
                                    <v-flex
                                        v-if="produto.stPrincipal === 1"
                                        xs12
                                        sm6
                                        md6
                                        class="mt-3"
                                    >
                                        <v-switch
                                            v-model="distribuicao.distribuirProjeto"
                                            label="Deseja aplicar esta ação para os outros produtos do projeto?"
                                            color="primary"
                                            value="true"
                                            hide-details
                                        />
                                    </v-flex>
                                </v-layout>

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

import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import SEditorTexto from '@/components/SalicEditorTexto';
import SConfirmacaoDialog from '@/components/SalicConfirmacaoDialog';
import SDialogHeader from '@/modules/parecer/components/gerenciar-dialogs/DialogHeader';

export default {
    name: 'GerenciarDistribuirProdutoDialog',
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
            loadingPareceristas: true,
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
        ...mapGetters({
            pareceristas: 'parecer/getPareceristas',
            vinculadas: 'parecer/getVinculadas',
        }),
        textoMensagemConfirmacao() {
            return this.distribuicao.tipoAcao === 'distribuir'
                ? 'Confirma a distribuição para o parecerista?'
                : 'Confirma o envio para a unidade vinculada?';
        },
        labelTextoRico() {
            return this.distribuicao.tipoAcao === 'distribuir'
                ? 'Observação para o parecerista'
                : 'Observação para a unidade vinculada';
        },
    },

    watch: {
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            this.loadingPareceristas = true;
            this.loadingVinculadas = true;
            if (val) {
                this.buscarPareceristas({
                    idOrgao: this.produto.idOrgao,
                    idArea: this.produto.idArea,
                    idSegmento: this.produto.idSegmento,
                    valor: this.produto.valor,
                });

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
        /* eslint-disable func-names */
        'distribuicao.tipoAcao': function (val) {
            if (val === 'encaminhar') {
                this.buscarVinculadas({
                    idOrgao: this.produto.idOrgao,
                });
            }
            this.distribuicao.idAgenteParecerista = '';
            this.distribuicao.idOrgaoDestino = '';
        },
        pareceristas() {
            this.loadingPareceristas = false;
        },
        vinculadas() {
            this.loadingVinculadas = false;
        },
    },

    mounted() {
        this.dialog = this.value;
    },

    methods: {
        ...mapActions({
            buscarPareceristas: 'parecer/buscarPareceristas',
            buscarVinculadas: 'parecer/buscarVinculadas',
            salvarDistribuicaoProduto: 'parecer/salvarDistribuicaoProduto',
            salvarDistribuicaoProjeto: 'parecer/salvarDistribuicaoProjeto',
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
            const salvar = this.distribuicao.distribuirProjeto
                ? 'salvarDistribuicaoProjeto'
                : 'salvarDistribuicaoProduto';

            this[salvar](this.distribuicao).then(() => {
                this.dialog = false;
            }).finally(() => {
                this.loading = false;
            });
        },
        formatarSelectParecerista(item) {
            return `${item.Nome} (${item.qtProdutos})`;
        },
    },

};
</script>
