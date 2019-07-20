<template>
    <v-layout>
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                color="green darken-3"
                class="material-icons"
                @click.stop="dialog = true"
            >
                send
            </v-icon>
            <span>Encaminhar Readequação</span>
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
                            Avaliar readequação -
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
import Const from '../const';

export default {
    name: 'DistribuirReadequacaoButton',
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
            encaminharDisponivel: false,
            selecionarDestinatario: false,
            valid: false,
            loading: true,
            dialog: false,
            minChar: 10,
            dsOrientacao: '',
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
            getDestinatariosDistribuicao: 'readequacao/getDestinatariosDistribuicao',
        }),
    },
    watch: {
        dadosEncaminhamento: {
            handler() {
                this.checkDisponivelEncaminhar();
            },
            deep: true,
        },
        dsOrientacao() {
            this.checkDisponivelEncaminhar();
        },
        getDestinatariosDistribuicao() {
            this.selecionarDestinatario = true;
        },
    },
    mounted() {
        this.loading = false;
        this.checkDisponivelEncaminhar();
    },
    methods: {
        ...mapActions({
            buscarReadequacoesPainelEmAnalise: 'readequacao/buscarReadequacoesPainelAguardandoDistribuicao',
            obterDestinatariosDistribuicao: 'readequacao/obterDestinatariosDistribuicao',
            encaminharParaAnalise: 'readequacao/encaminharParaAnalise',
            setSnackbar: 'noticias/setDados',
        }),
        checkDisponivelEncaminhar() {
            if (this.dsOrientacao !== '' && this.dsOrientacao.length > this.minChar) {
                if (this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP
                    || this.dadosEncaminhamento.vinculada === Const.ORGAO_GEAAP_SUAPI_DIAAPI
                ) {
                    if (this.getDestinatariosDistribuicao.length > 0) {
                        this.selecionarDestinatario = true;
                    }
                    this.encaminharDisponivel = this.dadosEncaminhamento.destinatario !== '';
                } else {
                    this.selecionarDestinatario = false;
                    this.encaminharDisponivel = this.dadosEncaminhamento.vinculada > 0;
                }
            } else {
                this.encaminharDisponivel = false;
            }
        },
        encaminharAnalise() {
            this.encaminharParaAnalise({
                idPronac: this.dadosReadequacao.idPronac,
                idReadequacao: this.dadosReadequacao.idReadequacao,
                destinatario: this.dadosEncaminhamento.destinatario,
                vinculada: this.dadosEncaminhamento.vinculada,
                dsOrientacao: this.dsOrientacao,
            }).then(() => {
                this.setSnackbar({
                    ativo: true,
                    color: 'success',
                    text: 'Readequação encaminada!',
                });
                this.dialog = false;
            });
        },
        obterDestinatarios() {
            this.dadosEncaminhamento.destinatario = '';
            if (this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP || this.dadosEncaminhamento.vinculada === Const.ORGAO_GEAAP_SUAPI_DIAAPI) {
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
