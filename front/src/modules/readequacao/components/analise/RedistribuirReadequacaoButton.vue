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
                                    <v-card
                                        class="mb-2 grey lighten-3"
                                    >
                                        <v-card-title
                                            class="title"
                                        >
                                            Órgão / Técnico atual:
                                        </v-card-title>
                                        <v-card-text>
                                            {{ dadosReadequacao.sgUnidade }} /
                                            <template
                                                v-if="dadosReadequacao.nmTecnicoParecerista !== null"
                                            >
                                                {{ dadosReadequacao.nmTecnicoParecerista }}
                                            </template>
                                            <template v-else>
                                                Aguardando distribuição
                                            </template>
                                        </v-card-text>
                                    </v-card>
                                    <v-select
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
                                        <template v-if="exibirDestinatariosIndisponiveis">
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
    name: 'RedistribuirReadequacaoButton',
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
            orgaosObterSefic: [
                Const.ORGAO_SAV_CAP,
                Const.ORGAO_GEAAP_SUAPI_DIAAPI,
            ],
            orgaosObterDestinatarios: [
                Const.ORGAO_SUPERIOR_SAV,
                Const.ORGAO_SAV_CAP,
                Const.ORGAO_GEAAP_SUAPI_DIAAPI,
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
        orgaoAtual() {
            return parseInt(this.getUsuario.orgao_ativo, 10);
        },
        vinculada() {
            const orgaos = JSON.parse(JSON.stringify(this.orgaosDestino));
            const vinculada = orgaos.find(orgao => orgao.id === parseInt(this.orgao, 10));
            if (typeof vinculada !== 'undefined') {
                return vinculada;
            }
            return false;
        },
        exibirDestinatariosIndisponiveis() {
            return (this.getDestinatariosDistribuicao.length === 0
                    && this.dadosEncaminhamento.vinculada > 0
                    && this.selecionarDestinatario === true);
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
            if (typeof value !== 'undefined') {
                this.loadingDestinatarios = false;
            }
            this.selecionarDestinatario = true;
        },
        dialog: {
            handler() {
                if (this.dialog === true && typeof this.dadosReadequacao.idPronac !== 'undefined') {
                    this.checkDisponivelRedistribuir();
                    if ([Const.ORGAO_SUPERIOR_SAV, Const.ORGAO_SAV_CAP].includes(parseInt(this.getUsuario.orgao_ativo, 10))) {
                        this.orgaosDestino[5] = {
                            id: 166,
                            nome: 'SAV/CAP',
                        };
                        this.orgaosDestino.splice(5, 0, {
                            id: 160,
                            nome: 'SAV',
                        });
                    }
                    if (this.orgaoAtual === Const.ORGAO_SEFIC_DEIPC_CGEFI) {
                        this.dadosEncaminhamento.vinculada = Const.ORGAO_GEAAP_SUAPI_DIAAPI;
                    } else {
                        this.dadosEncaminhamento.vinculada = this.orgaoAtual;
                    }
                    this.obterDestinatarios();
                }
            },
            deep: true,
        },
    },
    mounted() {
        this.loading = false;
    },
    methods: {
        ...mapActions({
            buscarReadequacoesPainelEmAnalise: 'readequacao/buscarReadequacoesPainelAguardandoDistribuicao',
            obterDestinatariosDistribuicao: 'readequacao/obterDestinatariosDistribuicao',
            redistribuirReadequacao: 'readequacao/redistribuirReadequacao',
            setSnackbar: 'noticias/setDados',
        }),
        checkDisponivelRedistribuir() {
            if (this.orgaoAtual === Const.ORGAO_SEFIC_DEIPC_CGEFI
                && (this.orgaosObterSefic.includes(this.dadosEncaminhamento.vinculada))) {
                if (this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP
                    && this.dsOrientacao.length > this.minChar) {
                    this.encaminharDisponivel = true;
                } else {
                    this.selecionarDestinatario = true;
                    this.encaminharDisponivel = this.dadosEncaminhamento.destinatario !== '';
                }
            } else if (this.dadosEncaminhamento.vinculada === this.orgaoAtual
                       && this.dadosEncaminhamento.vinculada === Const.ORGAO_SAV_CAP
                       && this.dsOrientacao.length > this.minChar) {
                this.encaminharDisponivel = true;
            } else if (this.dadosEncaminhamento.vinculada === this.orgaoAtual) {
                this.encaminharDisponivel = (this.dadosEncaminhamento.vinculada > 0
                                             && this.dsOrientacao.length > this.minChar
                                             && this.dadosEncaminhamento.destinatario.toString().length > 0);
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
                stAtendimento: Const.ST_ATENDIMENTO_DEFERIDA,
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
            if (this.orgaoAtual === Const.ORGAO_SEFIC_DEIPC_CGEFI
                && (this.orgaosObterSefic.includes(this.dadosEncaminhamento.vinculada))) {
                this.obterDestinatariosDistribuicao({
                    idPronac: this.dadosReadequacao.idPronac,
                    vinculada: this.dadosEncaminhamento.vinculada,
                    area: this.dadosReadequacao.Area,
                    segmento: this.dadosReadequacao.Segmento,
                });
            } else if (this.dadosEncaminhamento.vinculada === this.orgaoAtual) {
                this.loadingDestinatarios = true;
                this.obterDestinatariosDistribuicao({
                    idPronac: this.dadosReadequacao.idPronac,
                    vinculada: this.dadosEncaminhamento.vinculada,
                    area: this.dadosReadequacao.Area,
                    segmento: this.dadosReadequacao.Segmento,
                });
            }
        },
        validateText(e) {
            this.textIsValid = e >= this.minChar;
        },
    },
};
</script>
<style>
#app .v-menu__content {
    min-width: 130px !important;
}
</style>
