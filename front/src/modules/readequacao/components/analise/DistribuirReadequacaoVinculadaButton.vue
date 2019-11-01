<template>
    <v-layout>
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                color="blue darken-3"
                class="material-icons"
                @click.stop="dialog = true"
            >
                send
            </v-icon>
            <span>Distribuir Readequação</span>
        </v-tooltip>
        <v-dialog
            v-model="dialog"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            scrollable
            @keydown.esc="dialog = false"
        >
            <v-card
                v-if="loading"
            >
                <carregando
                    :text="'Montando distribuir readequação...'"
                    class="mt-5"
                />
            </v-card>
            <v-card
                v-else
            >
                <v-card tile>
                    <v-toolbar
                        card
                        dark
                        color="primary"
                    >
                        <V-btn
                            icon
                            dark
                            @click="dialog = false"
                        >
                            <v-icon>close</v-icon>
                        </v-btn>
                        <v-toolbar-title>
                            <span v-if="unidadeAtual">{{ unidadeAtual.Sigla }} /</span> Distribuir readequação -
                            {{ dadosReadequacao.idReadequacao }} - {{ dadosReadequacao.NomeProjeto }} - {{ dadosReadequacao.tpReadequacao }}
                        </v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form
                            ref="form"
                            v-model="valid"
                            lazy-validation
                        >
                            <v-layout
                                wrap
                                align-center>
                                <v-flex
                                    xs12
                                    sm12
                                    md6
                                >
                                    <v-radio-group
                                        v-model="acaoTomada"
                                    >
                                        <v-radio
                                            label="Devolver ao coordenador de acompanhamento"
                                            value="DEVOLVER_AO_COORDENADOR"
                                            color="green"
                                        />
                                        <v-radio
                                            label="Distribuir para parecerista"
                                            value="ENVIAR_PARECERISTA"
                                            color="green"
                                        />
                                        <v-radio
                                            label="Encaminhar para unidade vinculada"
                                            value="ENCAMINHAR_VINCULADA"
                                            color="green"
                                        />
                                    </v-radio-group>
                                </v-flex>
                                <v-flex
                                    v-if="acaoTomada === 'ENVIAR_PARECERISTA'"
                                    xs6
                                    sm12
                                    md6
                                >
                                    <template
                                        v-if="loadingDestinatarios"
                                    >
                                        <carregando
                                            :defined-class="`body-1`"
                                            :size="`small`"
                                            :text="'Carregando destinatários/as...'"
                                        />
                                    </template>
                                    <template v-else>

                                        <template
                                            v-if="getDestinatariosDistribuicao.length > 0 && selecionarDestinatario"
                                        >
                                            <v-select
                                                v-model="dadosEncaminhamento.destinatario"
                                                :items="getDestinatariosDistribuicao"
                                                label="Destinatário/a"
                                                item-text="nome"
                                                item-value="id"
                                            />
                                        </template>
                                        <template v-if="getDestinatariosDistribuicao.length === 0 && orgaoAtual">
                                            <h3 class="red--text text--darken-2">
                                                Não há destinatários/as disponíveis, impossível encaminhar a readequação no momento!
                                            </h3>
                                        </template>
                                    </template>
                                </v-flex>
                                <v-flex
                                    v-if="acaoTomada === 'ENCAMINHAR_VINCULADA'"
                                    xs12
                                    sm12
                                    md6
                                >
                                    <v-select
                                        v-model="dadosEncaminhamento.vinculada"
                                        :items="vinculadasCombo"
                                        label="Orgão a encaminhar"
                                        item-text="Sigla"
                                        item-value="Codigo"
                                    />
                                </v-flex>
                            </v-layout>

                            <v-layout
                                wrap>
                                <v-flex
                                    xs12
                                    sm12
                                    md12>
                                    <p><b>Parecer sobre a solicitação de readequação</b></p>
                                    <s-editor-texto
                                        v-model="dsOrientacao"
                                        :min-char="minChar"
                                        placeholder="Parecer sobre a solicitação de readequação"
                                        @editor-texto-counter="validateText($event)"
                                    />
                                </v-flex>

                            </v-layout>
                            <v-subheader class="pa-0 pt-5">
                                Todos os campos s&atilde;o obrigat&oacute;rios
                            </v-subheader>
                            <v-layout
                                row
                                justify-center
                            >
                                <v-btn
                                    :disabled="!encaminharDisponivel"
                                    color="primary"
                                    @click="encaminharAnalise()"
                                >
                                    <v-icon left>
                                        send
                                    </v-icon>
                                    Encaminhar
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
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import _ from 'lodash';
import SEditorTexto from '@/components/SalicEditorTexto';
import Carregando from '@/components/CarregandoVuetify';
import MxVinculadas from '@/modules/readequacao/mixins/MxVinculadas';

export default {
    name: 'DistribuirReadequacaoVinculadaButton',
    components: {
        Carregando,
        SEditorTexto,
    },
    mixins: [MxVinculadas],

    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            // opcoesEncaminhamento: false,
            encaminharDisponivel: false,
            selecionarDestinatario: false,
            textIsValid: false,
            valid: false,
            loading: true,
            loadingDestinatarios: false,
            dialog: false,
            minChar: 10,
            dadosEncaminhamento: {
                vinculada: 0,
                destinatario: 0,
            },
            dsOrientacao: '',
            readequacaoEditada: {
                idPronac: 0,
                idReadequacao: 0,
                idTipoReadequacao: '',
                stAtendimento: '',
            },
            acaoTomada: '',
        };
    },

    computed: {
        ...mapGetters({
            getUsuario: 'autenticacao/getUsuario',
            getDestinatariosDistribuicao: 'readequacao/getDestinatariosDistribuicao',
        }),
        orgaoAtual() {
            return this.getUsuario.orgao_ativo;
        },
        unidadeAtual() {
            return this.vinculadas.find(orgao => parseInt(orgao.Codigo, 10) === parseInt(this.orgaoAtual, 10));
        },
        vinculadas() {
            let orgaos = this.mxVinculadas;
            if (parseInt(this.orgaoAtual, 10) === 91) {
                orgaos = _.concat(orgaos, this.mxVinculadasIphan);
            }
            return orgaos;
        },
        vinculadasCombo() {
            const orgaos = _.cloneDeep(this.vinculadas);
            orgaos.forEach((item, index) => {
                if (parseInt(item.Codigo, 10) === parseInt(this.orgaoAtual, 10)) {
                    orgaos.splice(index, 1);
                }
            });
            return orgaos;
        },
    },

    watch: {
        dadosEncaminhamento: {
            handler() {
                this.checkDisponivelEncaminhar();
            },
            deep: true,
        },
        dialog: {
            handler() {
                if (this.dialog === true && typeof this.dadosReadequacao.idPronac !== 'undefined') {
                    this.inicializarReadequacaoEditada();
                    this.obterDestinatarios();
                } else if (this.dialog === false) {
                    this.dadosEncaminhamento = {
                        vinculada: 0,
                        destinatario: 0,
                    };
                    this.dsOrientacao = '';
                    this.readequacaoEditada = {
                        idPronac: 0,
                        idReadequacao: 0,
                        idTipoReadequacao: '',
                        dsAvaliacao: '',
                        stAtendimento: '',
                    };
                }
            },
            deep: true,
        },
        acaoTomada: {
            handler() {
                this.checkDisponivelEncaminhar();
            },
        },
        dsOrientacao: {
            handler() {
                this.checkDisponivelEncaminhar();
            },
        },
        readequacaoEditada: {
            handler(value) {
                if ((value.stAtendimento === 'E')
                    && (value.dsAvaliacao !== '' && value.dsAvaliacao.length > this.minChar)
                ) {
                    this.encaminharDisponivel = true;
                } else {
                    this.encaminharDisponivel = false;
                    this.checkDisponivelEncaminhar();
                }
            },
            deep: true,
        },
        getDestinatariosDistribuicao(value) {
            if (typeof value !== 'undefined') {
                this.loadingDestinatarios = false;
            }
            this.selecionarDestinatario = true;
        },
    },

    methods: {
        ...mapActions({
            buscarReadequacoesPainelAguardandoDistribuicao: 'readequacao/buscarReadequacoesPainelAguardandoDistribuicao',
            buscarReadequacoesPainelEmAnalise: 'readequacao/buscarReadequacoesPainelEmAnalise',
            obterDestinatariosDistribuicao: 'readequacao/obterDestinatariosDistribuicao',
            distribuirReadequacao: 'readequacao/distribuirReadequacao',
            devolverAoCoordenador: 'readequacao/devolverAoCoordenador',
            mensagemSucesso: 'noticias/mensagemSucesso',
        }),
        checkDisponivelEncaminhar() {
            if (this.acaoTomada === 'DEVOLVER_AO_COORDENADOR') {
                this.encaminharDisponivel = this.textIsValid;
            } else if (this.acaoTomada === 'ENVIAR_PARECERISTA') {
                // this.dadosEncaminhamento.vinculada = this.vinculada.Codigo;
                this.encaminharDisponivel = this.dadosEncaminhamento.destinatario > 0 && this.textIsValid;
            } else if (this.acaoTomada === 'ENCAMINHAR_VINCULADA') {
                this.encaminharDisponivel = this.dadosEncaminhamento.vinculada > 0
                    && this.textIsValid;
            }
        },
        inicializarReadequacaoEditada() {
            this.readequacaoEditada = {
                idPronac: this.dadosReadequacao.idPronac,
                idReadequacao: this.dadosReadequacao.idReadequacao,
                idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
                stAtendimento: this.dadosReadequacao.stAtendimento,
            };
            this.loading = false;
            this.checkDisponivelEncaminhar();
        },
        encaminharAnalise() {
            if (!this.encaminharDisponivel) {
                return false;
            }

            if (this.acaoTomada === 'DEVOLVER_AO_COORDENADOR') {
                return this.devolverAoCoordenador({
                    idPronac: this.readequacaoEditada.idPronac,
                    idReadequacao: this.readequacaoEditada.idReadequacao,
                    dsOrientacao: this.dsOrientacao,
                }).then(() => {
                    this.mensagemSucesso('Readequação devolvida ao coordenador de acompanhamento!');
                    this.dialog = false;
                });
            }

            if (this.acaoTomada === 'ENVIAR_PARECERISTA') {
                this.dadosEncaminhamento.vinculada = 0;
            }

            if (this.acaoTomada === 'ENCAMINHAR_VINCULADA') {
                this.dadosEncaminhamento.destinatario = 0;
            }

            return this.distribuirReadequacao({
                idPronac: this.readequacaoEditada.idPronac,
                idReadequacao: this.readequacaoEditada.idReadequacao,
                stAtendimento: this.readequacaoEditada.stAtendimento,
                dsOrientacao: this.dsOrientacao,
                destinatario: this.dadosEncaminhamento.destinatario,
                vinculada: this.dadosEncaminhamento.vinculada,
            }).then(() => {
                this.mensagemSucesso('Readequação distribuída!');
                this.dialog = false;
            });
        },
        obterDestinatarios() {
            this.loadingDestinatarios = true;
            this.obterDestinatariosDistribuicao({
                area: this.dadosReadequacao.Area,
                segmento: this.dadosReadequacao.Segmento,
                vinculada: this.orgaoAtual,
            }).then(() => {
                this.loadingDestinatarios = false;
                this.selecionarDestinatario = true;
            });
        },
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
    },
};
</script>
