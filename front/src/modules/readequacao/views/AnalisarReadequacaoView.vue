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
            v-else
            row
            wrap
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
                        <h5 class="headline font-weight-regular">Readequação: {{ dadosReadequacao.dsTipoReadequacao }}</h5>
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
                        <template v-for="(step, index) in arraySteps">
                            <v-stepper-step
                                :key="`${step.name}-step`"
                                :step="index + 1"
                                :editable="step.editable"
                                :complete="step.complete"
                                :rules="step.rules"
                            >
                                {{ step.label }}
                            </v-stepper-step>
                        </template>
                    </v-stepper-header>
                    <v-stepper-items>
                        <v-stepper-content
                            v-for="(step, index) in arraySteps"
                            :key="`${step.name}-content`"
                            :step="index + 1"
                        >
                            <v-card
                                class="mb-5"
                                elevation="0"
                            >
                                <keep-alive>
                                    <router-view
                                        v-if="(index + 1) === currentStep"
                                        :is-active="true"
                                        class="view"
                                    />
                                </keep-alive>
                            </v-card>
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
import AnalisarJustificativa from '../components/AnalisarJustificativa';
import AnalisarAlteracoes from '../components/AnalisarAlteracoes';
import MxReadequacao from '../mixins/Readequacao';

export default {
    name: 'AnalisarReadequacao',
    components: {
        SEditorTexto,
        Carregando,
        CampoDiff,
        AnalisarJustificativa,
        AnalisarAlteracoes,
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
            arraySteps: [
                {
                    id: 1,
                    label: 'Justificativa',
                    name: 'analisar-justificativa',
                    complete: false,
                    editable: true,
                    rules: [() => true],
                },
                {
                    id: 2,
                    label: 'Alterações',
                    name: 'analisar-alteracoes',
                    complete: false,
                    editable: true,
                    rules: [() => true],
                },
            ],
            currentStep: 1,
            parecerReadequacao: {
                ParecerFavoravel: 1,
            },
        };
    },
    computed: {
        ...mapGetters({
            dadosUsuario: 'autenticacao/getUsuario',
            dadosReadequacao: 'readequacao/getReadequacao',
            dadosProjeto: 'projeto/projeto',
            dadosAvaliacaoReadequacao: 'readequacao/getAvaliacaoReadequacao',
            campoOriginal: 'readequacao/getCampoAtual',
        }),
        parecerFavoravelTexto() {
            const parecerFavoravelTexto = (parseInt(this.parecerReadequacao.ParecerFavoravel, 10) === 2) ? 'Sim' : 'Não';
            return parecerFavoravelTexto;
        },
    },
    watch: {
        currentStep(step) {
            this.$router.push({ name: this.arraySteps[step - 1].name });
        },
        dadosReadequacao(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    this.loaded.readequacao = true;
                }
            }
        },
        dadosUsuario(value) {
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
            this.dadosUsuario,
            this.dadosProjeto,
            this.dadosReadequacao,
        );
        if (typeof this.$route.params.idPronac !== 'undefined') {
            this.idPronac = this.$route.params.idPronac;
            this.buscarProjetoCompleto(this.idPronac);
        }
        if (typeof this.$route.params.idReadequacao !== 'undefined') {
            this.obterReadequacao({
                idReadequacao: this.$route.params.idReadequacao,
            }).then((readequacao) => {
                this.obterAvaliacaoReadequacao({
                    idReadequacao: readequacao.idReadequacao,
                });
                this.obterCampoAtual({
                    idPronac: this.dadosReadequacao.idPronac,
                    idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
                });
            });
        } else {
            // sem readequacao
        }
    },
    methods: {
        ...mapActions({
            buscarProjetoCompleto: 'projeto/buscarProjetoCompleto',
            obterReadequacao: 'readequacao/obterReadequacao',
            obterAvaliacaoReadequacao: 'readequacao/obterAvaliacaoReadequacao',
            obterCampoAtual: 'readequacao/obterCampoAtual',
        }),
        nextStep(n) {
            this.currentStep = (n === Object.keys(this.arraySteps).length) ? 1 : n + 1;
        },
        enviarAnalise() {
        },
    },
};
</script>
