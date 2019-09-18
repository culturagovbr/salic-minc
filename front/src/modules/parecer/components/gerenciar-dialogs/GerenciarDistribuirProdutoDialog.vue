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
                                        <v-radio-group v-model="tipoAcao">
                                            <template v-slot:label>
                                                <div>Qual ação você pretende realizar?</div>
                                            </template>
                                            <v-radio value="distribuir">
                                                <template v-slot:label>
                                                    <strong class="primary--text">Distribuir para um novo
                                                        parecerista</strong>
                                                </template>
                                            </v-radio>
                                            <v-radio
                                                v-if="produto.tipoAnalise !== 1"
                                                value="encaminhar"
                                            >
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
                                            v-if="tipoAcao === 'distribuir'"
                                            v-model="distribuicao.idAgenteParecerista"
                                            :items="pareceristas"
                                            :item-text="formatarSelectParecerista"
                                            item-value="idParecerista"
                                            :label="loadingPareceristas ? 'Carregando...' : 'Selecione o parecerista'"
                                            :loading="loadingPareceristas"
                                            :rules="[obrigatorio]"
                                        />
                                        <v-select
                                            v-if="tipoAcao === 'encaminhar'"
                                            v-model="distribuicao.idOrgao"
                                            :items="vinculadas"
                                            item-text="Sigla"
                                            item-value="Codigo"
                                            :label="loadingVinculadas ? 'Carregando...' : 'Selecione o orgão destino'"
                                            :loading="loadingVinculadas"
                                            :rules="[obrigatorio]"
                                        />
                                    </v-flex>
                                    <v-flex
                                        v-if="exibirOpcaoDistribuirOutrosProdutos"
                                        xs12
                                        sm6
                                        md6
                                        class="mt-3"
                                    >
                                        <v-switch
                                            v-model="distribuirProjeto"
                                            label="Deseja aplicar esta ação para todos os produtos do projeto nesta unidade?"
                                            color="primary"
                                            value="true"
                                            hide-details
                                            append-icon="help"
                                            @click:append="$root.$dialogAjuda(textoAjuda)"
                                        />
                                    </v-flex>
                                </v-layout>

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

import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

import * as TbDistribuirParecer from '@/modules/parecer/constantes/TbDistribuirParecer';
import * as TbTipoEncaminhamento from '@/modules/shared/constantes/TbTipoEncaminhamento';

import SEditorTexto from '@/components/SalicEditorTexto';
import SDialogHeader from '@/modules/parecer/components/gerenciar-dialogs/DialogHeader';

export default {
    name: 'GerenciarDistribuirProdutoDialog',
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
                idAgenteParecerista: '',
                Observacao: '',
                stPrincipal: null,
                TipoAnalise: TbDistribuirParecer.TIPO_ANALISE_PRODUTO_COMPLETO,
                siAnalise: TbDistribuirParecer.SI_ANALISE_AGUARDANDO_ANALISE,
                siEncaminhamento: TbTipoEncaminhamento.SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA,
            },
            textoAjuda: 'Todos os produtos que estão nesta unidade serão distribuidos/encaminhados.',
            tipoAcao: 'distribuir',
            distribuirProjeto: false,
            obrigatorio: v => !!v || 'Este campo é obrigatório',
        };
    },

    computed: {
        ...mapGetters({
            pareceristas: 'parecer/getPareceristas',
            vinculadas: 'parecer/getVinculadas',
        }),
        textoMensagemConfirmacao() {
            return this.tipoAcao === 'distribuir'
                ? 'Confirma a distribuição para o parecerista?'
                : 'Confirma o envio para a unidade vinculada?';
        },
        labelTextoRico() {
            return this.tipoAcao === 'distribuir'
                ? 'Observação para o parecerista'
                : 'Observação para a unidade vinculada';
        },
        exibirOpcaoDistribuirOutrosProdutos() {
            return this.produto.tipoAnalise !== 1 && this.produto.stPrincipal === 1;
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

                this.definirDadosParaEdicao();
            }
            this.$emit('input', val);
        },
        tipoAcao(tipo) {
            this.distribuicao.siEncaminhamento = TbTipoEncaminhamento.SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA;

            if (tipo === 'encaminhar') {
                this.distribuicao.siEncaminhamento = TbTipoEncaminhamento.SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE;
                this.buscarVinculadas({ idOrgao: this.produto.idOrgao });
            }

            this.distribuicao.idAgenteParecerista = '';
            this.distribuicao.idOrgao = '';
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
        definirDadosParaEdicao() {
            this.distribuicao.idProduto = this.produto.idProduto;
            this.distribuicao.idPronac = this.produto.idPronac;
            this.distribuicao.idDistribuirParecer = this.produto.idDistribuirParecer;
            this.distribuicao.stPrincipal = this.produto.stPrincipal;
            this.distribuicao.TipoAnalise = this.produto.tipoAnalise;
            this.distribuicao.idOrgao = '';
            this.distribuicao.Observacao = '';

            this.distribuicao.idAgenteParecerista = '';
            if (this.produto.idAgenteParecerista) {
                this.distribuicao.idAgenteParecerista = this.produto.idAgenteParecerista;
            }
        },
        async salvarDistribuicao() {
            if (!this.$refs.form.validate()) {
                return false;
            }

            if (await this.$root.$confirm(this.textoMensagemConfirmacao) === false) {
                return false;
            }

            this.loading = true;
            const salvar = this.distribuirProjeto
                ? 'salvarDistribuicaoProjeto'
                : 'salvarDistribuicaoProduto';

            return this[salvar](this.distribuicao).then(() => {
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
