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
                    Distribuir Produto: {{ produto.pronac }} - {{ produto.nomeProjeto }}
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
                                                    <strong class="primary--text">Distribuir para um parecerista</strong>
                                                </template>
                                            </v-radio>
                                            <v-radio value="encaminhar">
                                                <template v-slot:label>
                                                    <strong class="primary--text">Encaminhar para uma unidade vinculada</strong>
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
                                            v-model="distribuicao.idParecerista"
                                            :items="pareceristas"
                                            :item-text="formatarSelectParecerista"
                                            item-value="idParecerista"
                                            label="selecione o parecerista"
                                        />
                                        <v-select
                                            v-if="tipoAcao === 'encaminhar'"
                                            v-model="distribuicao.idOrgao"
                                            :items="vinculadas"
                                            item-text="Sigla"
                                            item-value="Codigo"
                                            label="selecione o orgão destino"
                                        />
                                    </v-flex>
                                </v-layout>

                                <s-editor-texto
                                    v-model="distribuicao.Observacao"
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

import { mapGetters, mapActions } from 'vuex';
import { utils } from '@/mixins/utils';
import SEditorTexto from '@/components/SalicEditorTexto';
import SConfirmacaoDialog from '@/components/SalicConfirmacaoDialog';

export default {
    name: 'GerenciarDistribuirProdutoDialog',
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
            tipoAcao: 'distribuir',
            distribuicao: {
                idDistribuirParecer: '',
                idProduto: '',
                idPronac: '',
                idOrgao: '',
                idParecerista: '',
                Observacao: '',
            },
            dialogConfirmarEnvio: false,
        };
    },
    computed: {
        ...mapGetters({
            pareceristas: 'parecer/getPareceristas',
            vinculadas: 'parecer/getVinculadas',
        }),
    },
    watch: {
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            this.buscarDadosDistribuicao({
                idProduto: this.produto.idProduto,
                idPronac: this.produto.idPronac,
            });
            this.$emit('input', val);
        },
        tipoAcao() {
            this.distribuicao.idParecerista = '';
            this.distribuicao.idOrgao = '';
        },
    },
    methods: {
        ...mapActions({
            salvar: 'parecer/salvarDistribuicao',
            buscarDadosDistribuicao: 'parecer/obterDadosParaDistribuicao',
        }),
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
        enviarDeclaracao() {
            this.loading = true;
            this.salvar(this.distribuicao).then(() => {
                this.dialog = false;
            }).finally(() => {
                this.loading = false;
            });
        },
        formatarSelectParecerista(item) {
            return `${item.Nome} (${item.emAvaliacao})`;
        },
    },
};
</script>
