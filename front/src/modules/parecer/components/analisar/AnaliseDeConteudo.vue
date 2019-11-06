<template>
    <v-card @keyup.alt.67="dialog = !dialog">
        <s-carregando
            v-if="loading"
            text="Carregando conteúdo"
        />
        <v-snackbar
            :value="!dialog && !loading"
            :timeout="0"
            color="cyan darken-2"
        >
            <v-btn
                dark
                flat
                large
                @click="dialog = !dialog"
            >
                <v-icon
                    class="mr-2"
                    left
                >
                    edit
                </v-icon>
                Editar parecer
            </v-btn>
        </v-snackbar>

        <keep-alive>
            <s-proposta
                v-show="!loading"
                v-if="Object.keys(produto).length > 0"
                :idpreprojeto="produto.idProjeto"
            />
        </keep-alive>
        <v-dialog
            v-model="dialog"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            scrollable
            @keydown.esc="dialog = false"
        >
            <v-card tile>
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
                        Análise de conteúdo - Produto:
                        {{ produto.nomeProduto }}
                    </v-toolbar-title>
                    <v-spacer />
                    <v-toolbar-items>
                        <v-btn
                            :loading="loadingButton"
                            dark
                            flat
                            @click="submit"
                        >
                            <v-icon left>
                                save
                            </v-icon>
                            Salvar
                        </v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <v-form
                        ref="form"
                        v-model="valid"
                        lazy-validation
                    >
                        <v-layout wrap>
                            <div
                                v-if="editorParecerRules.show"
                                class="text-xs-left"
                            >
                                <p
                                    :class="editorParecerRules.color"
                                >
                                    {{ editorParecerRules.msg }}
                                </p>
                            </div>
                            <v-flex
                                xs12
                                sm12
                                md12
                            >
                                <v-switch
                                    v-model="analiseConteudoEmEdicao.ParecerFavoravel"
                                    :label="`Parecer Favorável?: ${labelSimOuNao(analiseConteudoEmEdicao.ParecerFavoravel)}`"
                                    color="green"
                                />
                            </v-flex>
                            <v-flex
                                xs12
                                sm12
                                md12
                            >
                                <p><b>Parecer de Conteúdo do Produto</b></p>
                                <s-editor-texto
                                    v-model="analiseConteudoEmEdicao.ParecerDeConteudo"
                                    :placeholder="'Parecer técnico sobre o conteúdo do produto'"
                                    :min-char="minChar"
                                    @editor-texto-counter="validateText($event)"
                                />
                            </v-flex>
                        </v-layout>
                        <v-subheader class="pa-0">
                            Todos os campos s&atilde;o obrigat&oacute;rios
                        </v-subheader>

                        <v-layout
                            row
                            justify-center
                        >
                            <v-btn
                                :loading="loadingButton"
                                :disabled="!textIsValid"
                                color="primary"
                                @click="submit"
                            >
                                <v-icon left>
                                    save
                                </v-icon>
                                Salvar
                            </v-btn>
                            <v-btn
                                @click="dialog = false"
                            >
                                Fechar
                                <v-icon right>
                                    close
                                </v-icon>
                            </v-btn>
                        </v-layout>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import SProposta from '@/modules/proposta/visualizar/Proposta';
import SEditorTexto from '@/components/SalicEditorTexto';
import SCarregando from '@/components/CarregandoVuetify';


export default {
    name: 'AnaliseDeConteudo',
    components: { SProposta, SEditorTexto, SCarregando },
    props: {
        active: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            loadingButton: false,
            loading: true,
            valid: false,
            dialog: false,
            overlay: true,
            direction: 'top',
            fab: false,
            fling: false,
            hover: false,
            tabs: null,
            minChar: 10,
            textIsValid: false,
            editorParecerRules: {
                show: false,
                color: '',
                backgroundColor: '',
                msg: '',
                enable: false,
            },
            analiseConteudoEmEdicao: {},
            rules: {
                parecer: v => (!!v || this.$refs.stItemAvaliado.value !== '3') || 'Parecer é obrigatório',
            },
        };
    },
    computed: {
        ...mapGetters({
            analiseConteudo: 'parecer/getAnaliseConteudo',
            produto: 'parecer/getProduto',
        }),
    },
    watch: {
        analiseConteudo(val) {
            this.analiseConteudoEmEdicao = Object.assign({}, val);
            if (Object.keys(val).length > 0) {
                this.loading = false;
            }
        },
    },
    created() {
        if (Object.keys(this.analiseConteudo).length > 0) {
            this.analiseConteudoEmEdicao = Object.assign({}, this.analiseConteudo);
            this.loading = false;
        }
    },
    methods: {
        ...mapActions({
            salvarAnaliseConteudo: 'parecer/salvarAnaLiseConteudo',
        }),
        submit() {
            // if (!this.$refs.form.validate()) {
            //     return false;
            // }

            const analise = Object.assign(
                {},
                this.analiseConteudoEmEdicao,
                {
                    stPrincipal: this.produto.stPrincipal,
                    idDistribuirParecer: this.produto.idDistribuirParecer,
                },
            );

            this.loadingButton = true;
            this.salvarAnaliseConteudo(analise).finally(() => {
                this.loadingButton = false;
            });

            return true;
        },
        validateText(e) {
            this.textIsValid = e > this.minChar;
        },
        labelSimOuNao(val) {
            return val ? 'Sim' : 'Não';
        },
    },
};
</script>

<style>
    #btn-editar-parecer {
        position: fixed;
        top: 230px;
        right: 5%;
        z-index: 201;
    }

     #parecer-conteudo {
         height: 400px;
     }
</style>
