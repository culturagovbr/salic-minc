<template>
    <carregando-vuetify
        v-if="loadingParecer"
        text="Carregando parecer"/>
    <v-container
        v-else
        row
        justify-center>
        <v-toolbar>
            <v-btn
                icon
                class="hidden-xs-only"
                @click="voltar()"
            >
                <v-icon>arrow_back</v-icon>
            </v-btn>
            <v-toolbar-title>
                Avaliação de Resultados - Emitir Laudo Final
            </v-toolbar-title>
            <v-spacer />
        </v-toolbar>
        <v-form
            ref="form"
            v-model="valid"
            lazy-validation>
            <v-card>
                <v-card-title>
                    <v-container
                        pa-0
                        ma-0>
                        <div>
                            <div class="headline"><b>Projeto:</b>
                                {{ projeto.AnoProjeto }}{{ projeto.Sequencial }} - {{ projeto.NomeProjeto }}
                            </div>
                            <span class="black--text"><b>Proponente:</b>
                                {{ proponente.CgcCpf | cnpjFilter }} - {{ proponente.Nome }}
                            </span>
                        </div>
                    </v-container>
                </v-card-title>
                <v-card-text>
                    <v-container
                        grid-list-xs
                        text-xs-center
                        ma-0
                        pa-0>
                        <v-layout
                            row
                            wrap>
                            <v-flex xs7>
                                <h4 class="text-xs-left">Manifestação *</h4>
                                <v-radio-group
                                    :value="parecer.siManifestacao"
                                    :rules="manifestacaoRules"
                                    row
                                    class="text-xs-left"
                                    @change="updateManifestacao">
                                    <v-radio
                                        color="success"
                                        label="Aprovado"
                                        value="A"/>
                                    <v-radio
                                        color="success"
                                        label="Aprovado com ressalvas"
                                        value="P"/>
                                    <v-radio
                                        color="error"
                                        label="Reprovado"
                                        value="R"/>
                                </v-radio-group>
                            </v-flex>

                            <v-flex
                                md12
                                xs12
                                mb-4>
                                <v-card>
                                    <v-responsive>
                                        <div
                                            v-show="laudoRules.show"
                                            class="text-xs-left"><h4
                                                :class="laudoRules.color">{{ laudoRules.msg }}*</h4></div>
                                        <EditorTexto
                                            :style="laudoRules.backgroundColor"
                                            :value="parecer.dsLaudoFinal"
                                            required="required"
                                            @editor-texto-input="inputLaudo($event)"
                                            @editor-texto-counter="validarLaudo($event)"
                                        />
                                    </v-responsive>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-container
                        grid-list-xs
                        text-xs-center
                        ma-0
                        pa-0>
                        <v-btn
                            :disabled="!valid || !laudoRules.enable || loadingSalvar || loadingFinalizar"
                            :loading="loadingSalvar"
                            color="primary"
                            @click.native="salvarLaudoFinal()">Salvar
                        </v-btn>
                        <v-btn
                            :disabled="!valid || !laudoRules.enable || loadingSalvar || loadingFinalizar"
                            :loading="loadingFinalizar"
                            color="primary"
                            @click.native="finalizarLaudoFinal()">Finalizar
                        </v-btn>
                    </v-container>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import cnpjFilter from '@/filters/cnpj';
import EditorTexto from '../components/EditorTexto';
import CarregandoVuetify from '@/components/CarregandoVuetify';

const dadosParecer = {
    siManifestacao: '',
    dsLaudoFinal: '',
};

export default {
    components: {
        CarregandoVuetify,
        EditorTexto,
    },
    filters: {
        cnpjFilter,
    },
    data() {
        return {
            tipo: true,
            idPronac: this.$route.params.id,
            valid: false,
            dialog: true,
            laudoRules: {
                show: false,
                color: '',
                backgroundColor: '',
                msg: '',
                enable: true,
            },
            manifestacaoRules: [
                v => !!v || 'Tipo de manifestação é obrigatório!',
            ],
            laudoFinalData: {},
            loadingParecer: true,
            loadingSalvar: false,
            loadingFinalizar: false,
            parecer: dadosParecer,
        };
    },
    computed: {
        ...mapGetters({
            proponente: 'avaliacaoResultados/proponente',
            projeto: 'avaliacaoResultados/projeto',
            parecerLaudoFinal: 'avaliacaoResultados/getParecerLaudoFinal',
        }),
    },

    watch: {
        parecerLaudoFinal(value) {
            if (value && value.items) {
                this.parecer = value.items;
            }
        },
        projeto() {
            this.loadingParecer = false;
        },
    },

    mounted() {
        this.parecer = dadosParecer;
        this.requestEmissaoParecer(this.$route.params.id);
        this.getLaudoFinal(this.$route.params.id);
        this.validarLaudo();
    },

    methods: {
        ...mapActions({
            salvarAction: 'avaliacaoResultados/salvarLaudoFinal',
            finalizar: 'avaliacaoResultados/finalizarLaudoFinal',
            requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
            getLaudoFinal: 'avaliacaoResultados/getLaudoFinal',
            mensagemSucesso: 'noticias/mensagemSucesso',
            mensagemErro: 'noticias/mensagemErro',
        }),
        validarLaudo(e) {
            if (e < 10) {
                this.laudoRules = {
                    show: true,
                    color: 'red--text',
                    backgroundColor: { 'background-color': '#FFCDD2' },
                    msg: 'O Laudo deve conter mais que 10 characteres',
                    enable: false,
                };
            }
            if (e < 1) {
                this.laudoRules = {
                    show: true,
                    color: 'red--text',
                    backgroundColor: { 'background-color': '#FFCDD2' },
                    msg: 'O Laudo é obrigatório!',
                    enable: false,
                };
            }
            if (e >= 10) {
                this.laudoRules = {
                    show: false,
                    color: '',
                    backgroundColor: '',
                    msg: '',
                    enable: true,
                };
            }
        },
        isValido() {
            if (!this.$refs.form.validate()) {
                this.mensagemErro('Dados obrigatórios não informados');
                return false;
            }
            if (this.parecer.dsLaudoFinal.length < 10) {
                this.mensagemErro('Parecer deve ter no mímimo 10 caracteres');
                return false;
            }

            return true;
        },
        salvarLaudoFinal() {
            if (!this.isValido()) {
                return false;
            }

            const data = {
                idLaudoFinal: '',
                idPronac: this.idPronac,
                siManifestacao: this.parecer.siManifestacao,
                dsLaudoFinal: this.parecer.dsLaudoFinal,
            };

            if (this.parecer.idLaudoFinal) {
                data.idLaudoFinal = this.parecer.idLaudoFinal;
            }

            this.loadingSalvar = true;
            return this.salvarAction(data).then(() => {
                this.mensagemSucesso('Salvo com sucesso');
            }).finally(() => {
                this.loadingSalvar = false;
            });
        },
        finalizarLaudoFinal() {
            if (!this.isValido()) {
                return false;
            }

            const data = {
                idLaudoFinal: '',
                idPronac: this.idPronac,
                idTipoDoAtoAdministrativo: 623,
                siManifestacao: this.parecer.siManifestacao,
                dsLaudoFinal: this.parecer.dsLaudoFinal,
                atual: 10,
                proximo: 14,
                finalizar: true,
            };

            if (this.parecer.idLaudoFinal) {
                data.idLaudoFinal = this.parecer.idLaudoFinal;
            }

            this.loadingFinalizar = true;
            return this.salvarAction(data).then(() => {
                this.mensagemSucesso('Finalizado com sucesso');
                this.$router.push({ name: 'Laudo' });
            }).finally(() => {
                this.loadingFinalizar = false;
            });
        },
        updateManifestacao(e) {
            this.parecer.siManifestacao = e;
        },
        inputLaudo(e) {
            this.parecer.dsLaudoFinal = e;
            this.validarLaudo(e);
        },
        voltar() {
            if (window.history.length > 1) {
                this.$router.go(-1);
            } else {
                this.$router.push({ name: 'Laudo' });
            }
        },
    },
};
</script>
