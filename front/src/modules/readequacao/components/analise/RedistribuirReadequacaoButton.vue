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
            <span>Redistribuir Readequação</span>
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
                            Redistribuir readequação -
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
                                        v-if="!vinculada"
                                        v-model="dadosEncaminhamento.vinculada"
                                        :items="orgaosDestino"
                                        label="Orgão a encaminhar"
                                        item-text="nome"
                                        item-value="id"
                                        @change="obterDestinatarios()"
                                    />
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
                                            v-if="getDestinatariosDistribuicao.length > 0"
                                        >
                                            <v-select
                                                v-if="selecionarDestinatario"
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
                                    </template>
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
                                    Redistribuir
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
            loadingDestinatarios: false,
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
                this.checkDisponivelRedistribuir();
            },
            deep: true,
        },
        dsOrientacao() {
            this.checkDisponivelRedistribuir();
        },
        getDestinatariosDistribuicao(value) {
            if (value.length > 0) {
                this.loadingDestinatarios = false;
            }
            this.selecionarDestinatario = true;
        },
        dialog: {
            handler() {
                if (this.dialog === true && typeof this.dadosReadequacao.idPronac !== 'undefined') {
                    if (typeof this.vinculada === 'object') {
                        this.obterDestinatarios();
                    }
                }
            },
            deep: true,
        },
    },
    mounted() {
        this.loading = false;
        this.checkDisponivelRedistribuir();
    },
    methods: {
        ...mapActions({
            buscarReadequacoesPainelEmAnalise: 'readequacao/buscarReadequacoesPainelAguardandoDistribuicao',
            obterDestinatariosDistribuicao: 'readequacao/obterDestinatariosDistribuicao',
            redistribuirReadequacao: 'readequacao/redistribuirReadequacao',
            setSnackbar: 'noticias/setDados',
        }),
        checkDisponivelRedistribuir() {
            if (this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP
                || this.dadosEncaminhamento.vinculada === Const.ORGAO_GEAAP_SUAPI_DIAAPI
            ) {
                if (this.dsOrientacao !== '' && this.dsOrientacao.length > this.minChar) {
                    if (this.getDestinatariosDistribuicao.length > 0) {
                        this.selecionarDestinatario = true;
                    }
                    this.encaminharDisponivel = this.dadosEncaminhamento.destinatario !== '';
                }
            } else if (typeof this.vinculada.id !== 'undefined') {
                this.dadosEncaminhamento.vinculada = this.vinculada.id;
                this.encaminharDisponivel = this.dadosEncaminhamento.destinatario > 0 && this.dsOrientacao.length > this.minChar;
            } else {
                this.selecionarDestinatario = false;
                this.encaminharDisponivel = this.dadosEncaminhamento.vinculada > 0 && this.dsOrientacao.length > this.minChar;
            }
        },
        encaminharAnalise() {
            this.redistribuirReadequacao({
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
                this.loadingDestinatarios = true;
                this.obterDestinatariosDistribuicao({
                    idPronac: this.dadosReadequacao.idPronac,
                    vinculada: this.dadosEncaminhamento.vinculada,
                });
            } else if (typeof this.vinculada.id !== 'undefined') {
                this.loadingDestinatarios = true;
                this.obterDestinatariosDistribuicao({
                    idPronac: this.dadosReadequacao.idPronac,
                    vinculada: this.vinculada.id,
                });
            }
        },
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
    },
};
</script>
