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
                            {{ vinculada.nome }} / Distribuir readequação -
                            {{ dadosReadequacao.idReadequacao }} - {{ dadosReadequacao.NomeProjeto }} - {{ dadosReadequacao.tpReadequacao }}
                        </v-toolbar-title>
                        <v-spacer />
                        <v-toolbar-items>
                            <v-btn
                                dark
                                flat
                                @click="encaminharAnalise"
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
                                    <v-radio-group
                                        v-model="acaoTomada"
                                    >
                                        <v-radio
                                            :label="`Devolver ao coordenador de acompanhamento`"
                                            :value="`DEVOLVER_AO_COORDENADOR`"
                                            color="green"
                                        />
                                        <v-radio
                                            :label="`Distribuir para parecerista`"
                                            :value="`ENVIAR_PARECERISTA`"
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
                                        v-model="dsOrientacao"
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
                                    <template
                                        v-if="loadingDestinatarios"
                                    >
                                        <carregando
                                            :defined-class="`body-1`"
                                            :size="`small`"
                                            :text="'Carregando destinatários/as...'"
                                        />
                                    </template>
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

export default {
    name: 'DistribuirReadequacaoVinculadaButton',
    components: {
        Carregando,
        SEditorTexto,
    },
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
            dsOrientacao: '',
            readequacaoEditada: {
                idPronac: 0,
                idReadequacao: 0,
                idTipoReadequacao: '',
                stAtendimento: '',
            },
            acaoTomada: '',
            orgaosDestino: [
                {
                    id: 93,
                    nome: 'FBN',
                },
                {
                    id: 94,
                    nome: 'FCP',
                },
                {
                    id: 95,
                    nome: 'FCRB',
                },
                {
                    id: 92,
                    nome: 'FUNARTE',
                },
                {
                    id: 335,
                    nome: 'IBRAM',
                },
                {
                    id: 91,
                    nome: 'IPHAN',
                },
                {
                    id: 160,
                    nome: 'SAV',
                },
                {
                    id: 166,
                    nome: 'SAV',
                },
                {
                    id: 262,
                    nome: 'SEFIC',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            getUsuario: 'autenticacao/getUsuario',
            getDestinatariosDistribuicao: 'readequacao/getDestinatariosDistribuicao',
        }),
        orgao() {
            return this.getUsuario.orgao_ativo;
        },
        vinculada() {
            const orgaos = JSON.parse(JSON.stringify(this.orgaosDestino));
            const vinculada = orgaos.find(orgao => orgao.id === parseInt(this.orgao, 10));
            if (typeof vinculada !== 'undefined') {
                return vinculada;
            }
            return false;
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
                    if (typeof this.vinculada === 'object') {
                        this.obterDestinatarios();
                    }
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
                if (this.acaoTomada === 'DEVOLVER_AO_COORDENADOR') {
                    this.opcoesEncaminhamento = false;
                } else if (this.acaoTomada === 'ENVIAR_PARECERISTA') {
                    this.opcoesEncaminhamento = true;
                }
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
                } else if (typeof value.stAtendimento !== 'undefined') {
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
    },
    methods: {
        ...mapActions({
            buscarReadequacoesPainelAguardandoDistribuicao: 'readequacao/buscarReadequacoesPainelAguardandoDistribuicao',
            buscarReadequacoesPainelEmAnalise: 'readequacao/buscarReadequacoesPainelEmAnalise',
        }),
        checkDisponivelEncaminhar() {
            if (this.acaoTomada === 'DEVOLVER_AO_COORDENADOR') {
                this.encaminharDisponivel = this.dsOrientacao.length > this.minChar;
            } else if (this.acaoTomada === 'ENVIAR_PARECERISTA') {
                this.dadosEncaminhamento.vinculada = this.vinculada.id;
                this.encaminharDisponivel = this.dadosEncaminhamento.destinatario > 0 && this.dsOrientacao.length > this.minChar;
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
            if (this.acaoTomada === 'ENVIAR_PARECERISTA') {
                this.distribuirReadequacao({
                    idPronac: this.readequacaoEditada.idPronac,
                    idReadequacao: this.readequacaoEditada.idReadequacao,
                    stAtendimento: this.readequacaoEditada.stAtendimento,
                    dsOrientacao: this.dsOrientacao,
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
            } else if (this.acaoTomada === 'DEVOLVER_AO_COORDENADOR') {
                this.devolverAoCoordenador({
                    idPronac: this.readequacaoEditada.idPronac,
                    idReadequacao: this.readequacaoEditada.idReadequacao,
                    dsOrientacao: this.dsOrientacao,
                }).then(() => {
                    this.setSnackbar({
                        ativo: true,
                        color: 'success',
                        text: 'Readequação devolvida ao coordenador de acompanhamento!',
                    });
                    this.dialog = false;
                });
            }
        },
        obterDestinatarios() {
            this.dadosEncaminhamento.vinculada = this.vinculada.id;
            this.loadingDestinatarios = true;
            this.obterDestinatariosDistribuicao({
                area: this.dadosReadequacao.Area,
                segmento: this.dadosReadequacao.Segmento,
                vinculada: this.vinculada.id,
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
