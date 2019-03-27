<template>
    <v-container
        row
        justify-center>
        <v-form v-model="valid">
            <v-dialog
                v-model="dialog"
                full-width
                scrollable
                fullscreen
                transition="dialog-bottom-transition"
            >
                <v-card>
                    <v-toolbar
                        dark
                        color="primary">
                        <v-btn
                            :to="{ name: 'Laudo' }"
                            icon
                            dark>
                            <v-icon>close</v-icon>
                        </v-btn>
                        <v-toolbar-title>Avaliação de Resultados - Emitir Laudo Final</v-toolbar-title>
                        <v-spacer/>
                    </v-toolbar>
                    <v-card-text>
                        <v-container>
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
                                                    :value="parecerLaudoFinal.items.siManifestacao"
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
                                                            :value="parecerLaudoFinal.items.dsLaudoFinal"
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
                                            :disabled="!valid || !laudoRules.enable"
                                            color="primary"
                                            @click.native="salvarLaudoFinal()">Salvar
                                        </v-btn>
                                        <v-btn
                                            :disabled="!valid || !laudoRules.enable"
                                            :to="{ name: 'Laudo'}"
                                            color="primary"
                                            @click.native="finalizarLaudoFinal()">Finalizar
                                        </v-btn>
                                    </v-container>
                                </v-card-actions>
                            </v-card>
                        </v-container>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </v-form>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import cnpjFilter from '@/filters/cnpj';
import EditorTexto from '../components/EditorTexto';

export default {
    components: {
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
                enable: false,
            },
            manifestacaoRules: [
                v => !!v || 'Tipo de manifestação é obrigatório!',
            ],
            laudoFinalData: {},
        };
    },
    computed: {
        ...mapGetters({
            modalVisible: 'modal/default',
            proponente: 'avaliacaoResultados/proponente',
            projeto: 'avaliacaoResultados/projeto',
            parecerLaudoFinal: 'avaliacaoResultados/getParecerLaudoFinal',
        }),
    },
    mounted() {
        this.validarLaudo();
    },
    methods: {
        ...mapActions({
            modalOpen: 'modal/modalOpen',
            modalClose: 'modal/modalClose',
            salvar: 'avaliacaoResultados/salvarLaudoFinal',
            finalizar: 'avaliacaoResultados/finalizarLaudoFinal',
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
        salvarLaudoFinal() {
            const data = {
                idPronac: this.idPronac,
                siManifestacao: this.parecerLaudoFinal.items.siManifestacao,
                dsLaudoFinal: this.parecerLaudoFinal.items.dsLaudoFinal,
            };

            if (this.parecerLaudoFinal.items.idLaudoFinal) {
                data.idLaudoFinal = this.parecerLaudoFinal.items.idLaudoFinal;
            }

            if (this.laudoFinalData.siManifestacao) {
                data.siManifestacao = this.laudoFinalData.siManifestacao;
            }

            if (this.laudoFinalData.dsLaudoFinal) {
                data.dsLaudoFinal = this.laudoFinalData.dsLaudoFinal;
            }
            this.salvar(data);
        },
        finalizarLaudoFinal() {
            const data = {
                idpronac: this.idPronac,
                idtipodoatoadministrativo: 623,
                siManifestacao: this.parecerLaudoFinal.items.siManifestacao,
                dsLaudoFinal: this.parecerLaudoFinal.items.dsLaudoFinal,
                atual: 10,
                proximo: 14,
            };

            if (this.parecerLaudoFinal.items.idLaudoFinal) {
                data.idLaudoFinal = this.parecerLaudoFinal.items.idLaudoFinal;
            }

            if (this.laudoFinalData.siManifestacao) {
                data.siManifestacao = this.laudoFinalData.siManifestacao;
            }

            if (this.laudoFinalData.dsLaudoFinal) {
                data.dsLaudoFinal = this.laudoFinalData.dsLaudoFinal;
            }

            this.finalizar(data);
        },
        updateManifestacao(e) {
            this.laudoFinalData.siManifestacao = e;
        },
        inputLaudo(e) {
            this.laudoFinalData.dsLaudoFinal = e;
            this.validarLaudo(e);
        },
    },
};
</script>
