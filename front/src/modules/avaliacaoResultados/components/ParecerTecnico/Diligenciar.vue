<template>
    <v-container>
        <v-layout
            row
            wrap>
            <v-flex>
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
                                icon
                                dark
                                @click="$router.go(-1)">
                                <v-icon>close</v-icon>
                            </v-btn>
                            <v-toolbar-title>Avaliação Financeira - Diligenciar</v-toolbar-title>
                            <v-spacer/>
                        </v-toolbar>
                        <v-card-text>
                            <v-container>
                                <v-card>
                                    <v-card-title primary-title>
                                        <div class="headline">
                                            <b>Projeto:</b>
                                            {{ projeto.AnoProjeto }}{{ projeto.Sequencial }} - {{ projeto.NomeProjeto }}
                                        </div>
                                    </v-card-title>
                                    <v-card-text>
                                        <v-form
                                            ref="form"
                                            v-model="valid">
                                            <label for="diligencia">Tipo de Diligencia *</label>
                                            <v-radio-group
                                                id="diligencia"
                                                v-model="tpDiligencia"
                                                :rules="diligenciaRules"
                                            >
                                                <v-radio
                                                    color="success"
                                                    label="Somente itens recusados"
                                                    value="174"/>
                                                <v-radio
                                                    color="success"
                                                    label="Todos os itens orçamentários"
                                                    value="645"/>
                                            </v-radio-group>
                                            <div
                                                v-show="solicitacaoRules.show"
                                                class="text-xs-left">
                                                <h4 :class="solicitacaoRules.color">{{ solicitacaoRules.msg }}*</h4>
                                            </div>
                                            <EditorTexto
                                                :style="solicitacaoRules.backgroundColor"
                                                :value="solicitacao"
                                                required="required"
                                                @editor-texto-input="inputSolicitacao($event)"
                                                @editor-texto-counter="validarSolicitacao($event)"
                                            />
                                        </v-form>
                                    </v-card-text>
                                    <v-card-actions class="justify-center">
                                        <v-btn
                                            :disabled="!valid || !solicitacaoRules.enable"
                                            :to="{ name: 'AnalisePlanilha', params:{ id: idPronac }}"
                                            color="primary"
                                            @click.native="enviarDiligencia()"
                                        >
                                            Enviar
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-container>
                        </v-card-text>
                    </v-card>
                </v-dialog>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import EditorTexto from '../components/EditorTexto';

export default {
    components: {
        EditorTexto,
    },
    data() {
        return {
            tpDiligencia: '',
            solicitacao: '',
            idPronac: this.$route.params.id,
            valid: false,
            dialog: true,
            solicitacaoRules: {
                show: false,
                color: '',
                backgroundColor: '',
                msg: '',
                enable: false,
            },
            diligenciaRules: [
                v => !!v || 'Tipo de diligencia é obrigatório!',
            ],
        };
    },
    computed:
        {
            ...mapGetters({
                projeto: 'avaliacaoResultados/projeto',
            }),
        },
    created() {
        this.getConsolidacao(this.idPronac);
    },
    methods:
        {
            ...mapActions({
                requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
                salvar: 'avaliacaoResultados/enviarDiligencia',
            }),
            getConsolidacao(id) {
                this.requestEmissaoParecer(id);
            },
            enviarDiligencia() {
                const data = {
                    idPronac: this.idPronac,
                    tpDiligencia: this.tpDiligencia,
                    solicitacao: this.solicitacao,
                };

                this.salvar(data);
            },
            inputSolicitacao(e) {
                this.solicitacao = e;
                // this.validarSolicitacao(e);
            },
            validarSolicitacao(e) {
                if (e < 1) {
                    this.solicitacaoRules = {
                        show: true,
                        color: 'red--text',
                        backgroundColor: { 'background-color': '#FFCDD2' },
                        msg: 'A solicitação é obrigatória!',
                        enable: false,
                    };
                }
                if (e > 0) {
                    this.solicitacaoRules = {
                        show: false,
                        color: '',
                        backgroundColor: '',
                        msg: '',
                        enable: true,
                    };
                }
            },
        },
};
</script>
