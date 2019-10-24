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
                        <v-btn
                            icon
                            dark
                            @click="dialog = false"
                        >
                            <v-icon>close</v-icon>
                        </v-btn>
                        <v-toolbar-title>
                            Distribuir readequação -
                            {{ dadosReadequacao.idReadequacao }} - {{ dadosReadequacao.NomeProjeto }} - {{ dadosReadequacao.tpReadequacao }}
                        </v-toolbar-title>
                        <v-spacer />
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
                                    <v-radio-group
                                        v-model="readequacaoEditada.stAtendimento"
                                    >
                                        <v-radio
                                            :label="`Receber`"
                                            :value="`D`"
                                            color="green"
                                        />
                                        <v-radio
                                            :label="`Devolver ao proponente`"
                                            :value="`E`"
                                            color="green"
                                        />
                                    </v-radio-group>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm12
                                    md12
                                >
                                    <p><b>Parecer sobre a solicitação de readequação</b></p>
                                    <s-editor-texto
                                        v-model="readequacaoEditada.dsAvaliacao"
                                        :placeholder="'Parecer sobre a solicitação de readequação'"
                                        :min-char="minChar"
                                        @editor-texto-counter="validateText($event)"
                                    />
                                </v-flex>
                                <v-flex
                                    v-if="opcoesEncaminhamento"
                                    xs6
                                    sm12
                                    md6
                                >
                                    <v-select
                                        v-model="dadosEncaminhamento.vinculada"
                                        :items="orgaosDestino"
                                        label="Orgão a encaminhar"
                                        item-text="Sigla"
                                        item-value="Codigo"
                                        @change="obterDestinatarios()"
                                    />
                                    <carregando
                                        v-if="loadingDestinatarios"
                                        defined-class="body-1"
                                        size="small"
                                        text="Carregando destinatários/as..."
                                    />
                                    <div v-else>
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
                                        <template v-if="getDestinatariosDistribuicao.length === 0 && dadosEncaminhamento.vinculada > 0">
                                            <h3 class="red--text text--darken-2">
                                                Não há destinatários/as disponíveis, impossível encaminhar a readequação no momento!
                                            </h3>
                                        </template>
                                    </div>
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
                                    v-if="encaminharDisponivel"
                                    dark
                                    color="blue accent-4"
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
import SEditorTexto from '@/components/SalicEditorTexto';
import Carregando from '@/components/CarregandoVuetify';
import Const from '../../const';
import MxVinculadas from '@/modules/readequacao/mixins/MxVinculadas';

export default {
    name: 'DistribuirReadequacaoButton',
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
            opcoesEncaminhamento: false,
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
            readequacaoEditada: {
                idPronac: 0,
                idReadequacao: 0,
                idTipoReadequacao: '',
                dsAvaliacao: '',
                stAtendimento: '',
            },
        };
    },
    computed: {
        ...mapGetters({
            dadosUsuario: 'autenticacao/getUsuario',
            getDestinatariosDistribuicao: 'readequacao/getDestinatariosDistribuicao',
        }),
        orgaosDestino() {
            let destinos = this.mxVinculadas;

            if ([Const.ORGAO_SUPERIOR_SAV, Const.ORGAO_SAV_CAP].includes(parseInt(this.dadosUsuario.orgao_ativo, 10))) {
                destinos[5] = {
                    Codigo: 166,
                    Sigla: 'SAV/CAP',
                };
                destinos.splice(5, 0, {
                    Codigo: 160,
                    Sigla: 'SAV',
                });
            }

            // if (parseInt(this.dadosUsuario.orgao_ativo, 10) === Const.ORGAO_IPHAN_PRONAC) {
            //     destinos = Object.assign({}, destinos, this.mxVinculadasIphan);
            // }

            return destinos;
        },
    },
    watch: {
        dadosEncaminhamento: {
            handler() {
                this.checkDisponivelEncaminhar();
            },
            deep: true,
        },
        readequacaoEditada: {
            handler(value) {
                if ((value.stAtendimento === 'E')
                    && (value.dsAvaliacao !== '' && value.dsAvaliacao.length > this.minChar)
                ) {
                    this.encaminharDisponivel = true;
                    this.opcoesEncaminhamento = false;
                } else if (typeof value.stAtendimento !== 'undefined') {
                    this.opcoesEncaminhamento = true;
                    this.encaminharDisponivel = false;
                    this.checkDisponivelEncaminhar();
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
        dialog() {
            if (this.dialog === false) {
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
            } else {
                this.inicializarReadequacaoEditada();
            }
        },
    },
    methods: {
        ...mapActions({
            buscarReadequacoesPainelAguardandoDistribuicao: 'readequacao/buscarReadequacoesPainelAguardandoDistribuicao',
            buscarReadequacoesPainelEmAnalise: 'readequacao/buscarReadequacoesPainelEmAnalise',
            obterDestinatariosDistribuicao: 'readequacao/obterDestinatariosDistribuicao',
            distribuirReadequacao: 'readequacao/distribuirReadequacao',
            setSnackbar: 'noticias/setDados',
        }),
        checkDisponivelEncaminhar() {
            if (this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP
                || this.dadosEncaminhamento.vinculada === Const.ORGAO_GEAAP_SUAPI_DIAAPI
            ) {
                if (this.getDestinatariosDistribuicao.length > 0) {
                    this.selecionarDestinatario = true;
                }
                if (this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP) {
                    this.encaminharDisponivel = true;
                } else {
                    this.encaminharDisponivel = this.dadosEncaminhamento.destinatario > 0;
                }
            } else {
                this.selecionarDestinatario = false;
                this.encaminharDisponivel = (this.dadosEncaminhamento.vinculada > 0 && this.readequacaoEditada.dsAvaliacao.length > this.minChar);
            }
        },
        inicializarReadequacaoEditada() {
            this.readequacaoEditada = {
                idPronac: this.dadosReadequacao.idPronac,
                idReadequacao: this.dadosReadequacao.idReadequacao,
                idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
                dsAvaliacao: this.dadosReadequacao.dsAvaliacao || '',
                stAtendimento: this.dadosReadequacao.stAtendimento,
            };
            this.loading = false;
            this.checkDisponivelEncaminhar();
        },
        encaminharAnalise() {
            this.distribuirReadequacao({
                idPronac: this.readequacaoEditada.idPronac,
                idReadequacao: this.readequacaoEditada.idReadequacao,
                stAtendimento: this.readequacaoEditada.stAtendimento,
                dsAvaliacao: this.readequacaoEditada.dsAvaliacao,
                destinatario: this.dadosEncaminhamento.destinatario,
                vinculada: this.dadosEncaminhamento.vinculada,
            }).then(() => {
                this.setSnackbar({
                    ativo: true,
                    color: 'success',
                    text: 'Readequação distribuída!',
                });
                this.dialog = false;
            });
        },
        obterDestinatarios() {
            this.dadosEncaminhamento.destinatario = 0;
            if (this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP
                || this.dadosEncaminhamento.vinculada === Const.ORGAO_GEAAP_SUAPI_DIAAPI) {
                this.loadingDestinatarios = true;
                this.obterDestinatariosDistribuicao({
                    vinculada: this.dadosEncaminhamento.vinculada,
                });
            } else {
                this.dadosEncaminhamento.opcoesEncaminhamento = false;
            }
        },
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
    },
};
</script>
