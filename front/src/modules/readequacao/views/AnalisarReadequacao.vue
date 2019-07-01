<template>
    <v-container>
        <v-layout
            v-if="loading"
            row
            wrap
        >
            <v-flex
                xs12
                class="mt-2"
            >
                <v-card>
                    <carregando
                        :text="'Montando análise de readequação...'"
                        class="mt-5 pb-4"
                    />
                </v-card>
            </v-flex>
        </v-layout>
        <v-layout
            row
            wrap
            v-else
        >
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
                    Avaliar readequação
                </v-btn>
            </v-snackbar>
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
                            Avaliar readequação
                        </v-toolbar-title>
                        <v-spacer />
                        <v-toolbar-items>
                            <v-btn
                                dark
                                flat
                                @click="enviarAnalise"
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
                                <v-flex
                                    xs12
                                    sm12
                                    md12
                                >
                                    <v-switch
                                        v-model="parecerReadequacao.ParecerFavoravel"
                                        :label="`Parecer Favorável?: ${parecerFavoravelTexto}`"
                                        false-value="1"
                                        true-value="2"
                                        color="green"
                                    />
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm12
                                    md12
                                >
                                    <p><b>Parecer sobre a solicitação de readequação</b></p>
                                    <s-editor-texto
                                        v-model="parecerReadequacao.ParecerDeConteudo"
                                        :placeholder="'Parecer sobre a solicitação de readequação'"
                                        :min-char="minChar"
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
                                    color="primary"
                                    @click="enviarAnalise()"
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
            <v-flex
                xs12
                class="mt-2"
            >
                <v-toolbar
                    height="90"
                    class="blue-grey darken-2"
                    dark
                >
                    <v-btn
                        icon
                        class="hidden-xs-only"
                        @click="voltar()"
                    >
                        <v-icon color="white">arrow_back</v-icon>
                    </v-btn>
                    <v-toolbar-title class="ml-2">
                        <h5 class="headline font-weight-regular">Readequação: Saldo de Aplicação</h5>
                        <v-divider/>
                        <div class="subheading mt-1">
                            Projeto: {{ dadosProjeto.Pronac }} - {{ dadosProjeto.NomeProjeto }}
                        </div>
                    </v-toolbar-title>
                </v-toolbar>                
                <v-stepper
                    v-model="currentStep"
                    non-linear
                >
                    <v-stepper-header>
                        <v-stepper-step
                            editable
                            step="1"
                        >
                            Dados da readequação
                        </v-stepper-step>
                    </v-stepper-header>
                    <v-stepper-items>
                        <v-stepper-content
                            step="1"
                        >
                        </v-stepper-content>
                    </v-stepper-items>
                </v-stepper>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';
import SEditorTexto from '@/components/SalicEditorTexto';
import Carregando from '@/components/CarregandoVuetify';
import CampoDiff from '@/components/CampoDiff';
import MxReadequacao from '../mixins/Readequacao';

export default {
    name: 'AnalisarReadequacao',
    components: {
        SEditorTexto,
        Carregando,
        CampoDiff,
    },
    mixins: [
        MxReadequacao,
    ],
    props: {
        idReadequacao: {
            type: [Number, String],
            default: 0,
        },
    },
    data() {
        return {
            valid: false,
            minChar: 10,
            dialog: false,
            loading: true,
            loaded: {
                projeto: false,
                readequacao: false,
                usuario: false,
            },
            currentStep: 1,
            parecerReadequacao: {
                ParecerFavoravel: 1,
            },
        };
    },
    computed: {
        ...mapGetters({
            getUsuario: 'autenticacao/getUsuario',
            getReadequacao: 'readequacao/getReadequacao',
            dadosProjeto: 'projeto/projeto',
            dadosAvaliacaoReadequacao: 'readequacao/getAvaliacaoReadequacao',
        }),
        parecerFavoravelTexto() {
            const parecerFavoravelTexto = (parseInt(this.parecerReadequacao.ParecerFavoravel) === 2) ? 'Sim' : 'Não';
            return parecerFavoravelTexto;
        },
    },
    watch: {
        getReadequacao(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    this.loaded.readequacao = true;
                }
            }
        },
        getUsuario(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    this.loaded.usuario = true;
                }
            }
        },
        dadosProjeto(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    this.loaded.projeto = true;
                }
            }
        },
        loaded: {
            handler(value) {
                const fullyLoaded = _.keys(value).every(i => value[i]);
                if (fullyLoaded) {
                    this.loading = false;
                }
            },
            deep: true,
        },
    },
    created() {
        this.loaded = this.checkAlreadyLoadedData(
            this.loaded,
            this.getUsuario,
            this.dadosProjeto,
            this.getReadequacao,
        );
        if (typeof this.$route.params.idPronac !== 'undefined') {
            this.idPronac = this.$route.params.idPronac;
            this.buscarProjetoCompleto(this.idPronac);
        }
        if (typeof this.$route.params.idReadequacao !== 'undefined') {
            this.obterReadequacao({
                idReadequacao: this.$route.params.idReadequacao,
            }).then((readequacao) => {
                this.obterAvaliacaoReadequacao({ idReadequacao: readequacao.idReadequacao });
            });
        } else {
            console.log('sem idReadequacao');
        }
    },
    methods: {
        ...mapActions({
            buscarProjetoCompleto: 'projeto/buscarProjetoCompleto',
            obterReadequacao: 'readequacao/obterReadequacao',
            obterAvaliacaoReadequacao: 'readequacao/obterAvaliacaoReadequacao',
        }),
        enviarAnalise() {
        },
    },
};
</script>
