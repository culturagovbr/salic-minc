<template>
    <v-layout>
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                color="green darken-3"
                class="material-icons"
                @click.stop="dialog = true"
            >
                arrow_back
            </v-icon>
            <span>Devolver Readequação</span>
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
                    :text="'Montando encaminhar readequação...'"
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
                            Devolver readequação -
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
                                        v-model="encaminharOutroTecnico"
                                    >
                                        <v-radio
                                            :label="`Devolver para o mesmo técnico/parecerista`"
                                            :value="false"
                                            color="green"
                                        />
                                        <v-radio
                                            :label="`Enviar para outro técnico/parecerista`"
                                            :value="true"
                                            color="green"
                                        />
                                    </v-radio-group>
                                </v-flex>
                                <v-flex
                                    v-if="encaminharOutroTecnico"
                                    xs3
                                    sm12
                                    md4
                                >
                                    <v-select
                                        v-model="dadosEncaminhamento.vinculada"
                                        :items="orgaosDestino"
                                        label="Orgão a encaminhar"
                                        item-text="nome"
                                        item-value="id"
                                        @change="obterDestinatarios()"
                                    />
                                    <carregando
                                        v-if="loadingDestinatarios"
                                        :defined-class="`body-1`"
                                        :text="'Carregando destinatários/as...'"
                                    />
                                    <v-select
                                        v-if="selecionarDestinatario"
                                        v-model="dadosEncaminhamento.destinatario"
                                        :items="getDestinatariosDistribuicao"
                                        label="Destinatário/a"
                                        item-text="nome"
                                        item-value="id"
                                    />
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm12
                                    md12
                                >
                                    <p><b>Observações do encaminhamento</b></p>
                                    <s-editor-texto
                                        v-model="dsOrientacao"
                                        :placeholder="'Observaçõs do encaminhamento da solicitação de readequação'"
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
                                    v-if="devolverDisponivel"
                                    dark
                                    color="blue accent-4"
                                    @click="devolverAnalise()"
                                >
                                    <v-icon left>
                                        send
                                    </v-icon>
                                    Devolver
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

export default {
    name: 'DevolverReadequacaoButton',
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
            devolverDisponivel: false,
            selecionarDestinatario: false,
            valid: false,
            loading: true,
            dialog: false,
            loadingDestinatarios: false,
            minChar: 10,
            dsOrientacao: '',
            encaminharOutroTecnico: false,
            dadosEncaminhamento: {
                vinculada: 0,
                destinatario: '',
            },
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
            return this.dadosReadequacao.idOrgao;
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
                this.checkDisponivelDevolver();
            },
            deep: true,
        },
        dsOrientacao() {
            this.checkDisponivelDevolver();
        },
        encaminharOutroTecnico() {
            this.checkDisponivelDevolver();
        },
        getDestinatariosDistribuicao(value) {
            if (value.length > 0) {
                this.loadingDestinatarios = false;
            }
            this.selecionarDestinatario = true;
        },
    },
    mounted() {
        this.loading = false;
        this.checkDisponivelDevolver();
    },
    methods: {
        ...mapActions({
            buscarReadequacoesPainelEmAnalise: 'readequacao/buscarReadequacoesPainelAguardandoDistribuicao',
            obterDestinatariosDistribuicao: 'readequacao/obterDestinatariosDistribuicao',
            devolverReadequacao: 'readequacao/devolverReadequacao',
            setSnackbar: 'noticias/setDados',
        }),
        checkDisponivelDevolver() {
            if (this.dsOrientacao !== '' && this.dsOrientacao.length > this.minChar) {
                if (this.encaminharOutroTecnico) {
                    if (this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP
                        || this.dadosEncaminhamento.vinculada === Const.ORGAO_GEAAP_SUAPI_DIAAPI
                    ) {
                        if (this.getDestinatariosDistribuicao.length > 0) {
                            this.selecionarDestinatario = true;
                        }
                        this.devolverDisponivel = this.dadosEncaminhamento.destinatario !== '';
                    } else {
                        this.selecionarDestinatario = false;
                        this.devolverDisponivel = this.dadosEncaminhamento.vinculada > 0;
                    }
                } else {
                    this.devolverDisponivel = true;
                }
            } else {
                this.devolverDisponivel = false;
            }
        },
        devolverAnalise() {
            if (typeof this.vinculada.id !== 'undefined') {
                this.dadosEncaminhamento.vinculada = this.vinculada.id;
            } else {
                this.dadosEncaminhamento.vinculada = this.orgao;
            }
            if (this.dadosEncaminhamento.destinatario === '') {
                this.dadosEncaminhamento.destinatario = (
                    typeof this.dadosReadequacao.idTecnico !== 'undefined'
                ) ? this.dadosReadequacao.idTecnico : this.dadosReadequacao.idTecnicoParecerista;
            }
            this.devolverReadequacao({
                idPronac: this.dadosReadequacao.idPronac,
                idReadequacao: this.dadosReadequacao.idReadequacao,
                destinatario: this.dadosEncaminhamento.destinatario,
                vinculada: this.dadosEncaminhamento.vinculada,
                dsOrientacao: this.dsOrientacao,
                stAtendimento: 'N',
            }).then(() => {
                this.setSnackbar({
                    ativo: true,
                    color: 'success',
                    text: 'Readequação devolvida!',
                });
                this.dialog = false;
            });
        },
        obterDestinatarios() {
            this.loadingDestinatarios = true;
            this.dadosEncaminhamento.destinatario = '';
            if (typeof this.vinculada.id !== 'undefined') {
                this.dadosEncaminhamento.vinculada = this.vinculada.id;
                this.loadingDestinatarios = true;
                this.obterDestinatariosDistribuicao({
                    area: this.dadosReadequacao.Area,
                    segmento: this.dadosReadequacao.Segmento,
                    vinculada: this.vinculada.id,
                });
            } else if (this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP
                || this.dadosEncaminhamento.vinculada === Const.ORGAO_GEAAP_SUAPI_DIAAPI) {
                this.obterDestinatariosDistribuicao({
                    idPronac: this.dadosReadequacao.idPronac,
                    vinculada: this.dadosEncaminhamento.vinculada,
                });
            }
        },
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
    },
};
</script>
