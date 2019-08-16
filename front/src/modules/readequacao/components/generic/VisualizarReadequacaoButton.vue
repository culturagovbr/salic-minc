<template>
    <v-layout>
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                color="#212121"
                @click.stop="dialog = true"
            >
                visibility
            </v-icon>
            <span>Visualizar Readequação</span>
        </v-tooltip>
        <v-dialog
            v-model="dialog"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            @keydown.esc="dialog = false"
        >
            <v-card
                v-if="loading"
            >
                <carregando
                    :text="'Montando visualização de readequação...'"
                    class="mt-5"
                />
            </v-card>
            <v-card
                v-else
            >
                <v-toolbar
                    dark
                    color="primary"
                    fixed
                >
                    <v-btn
                        icon
                        dark
                        @click="dialog = false"
                    >
                        <v-icon>
                            close
                        </v-icon>
                    </v-btn>
                    <v-toolbar-title>Readequação - {{ dadosReadequacao.dsTipoReadequacao }}</v-toolbar-title>
                    <v-spacer/>
                    <v-toolbar-title>{{ projeto.Pronac }} - {{ projeto.NomeProjeto }}</v-toolbar-title>
                </v-toolbar>
                <v-divider/>
                <v-layout
                    row
                    wrap
                    class="mt-5 pt-3"
                >
                    <v-flex
                        xs12
                    >
                        <v-list
                            two-line
                            subheader
                        >
                            <v-subheader
                                inset
                                class="title"
                            >Dados da Solicitação - {{ dadosReadequacao.idReadequacao }}</v-subheader>
                            <v-list-tile avatar>
                                <v-list-tile-avatar>
                                    <v-icon class="green lighten-1 white--text">
                                        person
                                    </v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>Nome do Solicitante</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ dadosReadequacao.dsNomeSolicitante }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                                <v-list-tile-avatar>
                                    <v-icon class="green lighten-1 white--text">
                                        date_range
                                    </v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content class="ml-3">
                                    <v-list-tile-title>Data da Solicitação</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ dadosReadequacao.dtSolicitacao | formatarData }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                        <v-list
                            two-line
                            subheader
                        >
                            <v-list-tile avatar>
                                <v-list-tile-avatar>
                                    <v-icon class="green lighten-1 white--text">
                                        list
                                    </v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>Status da análise</v-list-tile-title>
                                    <v-list-tile-sub-title v-html="getStatusAnalise(dadosReadequacao.siEncaminhamento)"/>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                        <v-list
                            two-line
                        >
                            <v-list-tile
                                avatar
                                @click="visualizarJustificativa = !visualizarJustificativa"
                            >
                                <visualizar-campo-detalhado
                                    v-if="visualizarJustificativa"
                                    :dialog="true"
                                    :dados="{ titulo: 'Justificativa', descricao: dadosReadequacao.dsJustificativa }"
                                    @fechar-visualizacao="visualizarJustificativa = false"
                                />
                                <v-list-tile-avatar>
                                    <v-icon class="green lighten-1 white--text">
                                        assignment
                                    </v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>Justificativa</v-list-tile-title>
                                </v-list-tile-content>
                                <v-list-tile-action>
                                    <v-btn
                                        icon
                                        ripple
                                    >
                                        <v-icon color="grey lighten-1">
                                            info
                                        </v-icon>
                                    </v-btn>
                                </v-list-tile-action>
                            </v-list-tile>
                        </v-list>
                        <v-list
                            v-if="dadosReadequacao.idDocumento"
                            two-line
                        >
                            <v-list-tile
                                avatar
                                @click="abrirArquivo(dadosReadequacao.idDocumento)"
                            >
                                <v-list-tile-avatar>
                                    <v-icon class="green lighten-1 white--text">
                                        insert_drive_file
                                    </v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>Documento anexo</v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </v-flex>
                    <v-flex
                        v-if="existeAvaliacao"
                        xs12
                    >
                        <v-list
                            two-line
                            subheader
                            class="mt-3"
                        >
                            <v-subheader
                                color="black--text"
                                class="grey lighten-3"
                            >Dados da Avaliação</v-subheader>
                            <v-list-tile avatar>
                                <v-list-tile-avatar>
                                    <v-icon class="green lighten-1 white--text">
                                        person
                                    </v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content
                                    v-if="dadosReadequacao.idAvaliador"
                                >
                                    <v-list-tile-title>Nome do Avaliador</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ dadosReadequacao.dsNomeAvaliador }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                                <v-list-tile-content
                                    v-else
                                >
                                    <v-list-tile-title>Sem avaliação até o momento</v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                            <v-list-tile avatar>
                                <v-list-tile-avatar>
                                    <v-icon class="green lighten-1 white--text">
                                        date_range
                                    </v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>Data da Avaliação</v-list-tile-title>
                                    <v-list-tile-sub-title>{{ dadosReadequacao.dtAvaliador | formatarData }}</v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </v-list>
                    </v-flex>
                    <v-flex
                        v-if="existeAvaliacao"
                        xs12
                    >
                        <v-list
                            two-line
                        >
                            <v-list-tile
                                avatar
                                @click="visualizarAvaliacao = !visualizarAvaliacao"
                            >
                                <visualizar-campo-detalhado
                                    v-if="visualizarAvaliacao"
                                    :dialog="true"
                                    :dados="{ titulo: 'Avaliação', descricao: dadosReadequacao.dsAvaliacao }"
                                    @fechar-visualizacao="visualizarAvaliacao = false"
                                />
                                <v-list-tile-avatar>
                                    <v-icon class="green lighten-1 white--text">
                                        assignment
                                    </v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>Avaliação</v-list-tile-title>
                                </v-list-tile-content>
                                <v-list-tile-action>
                                    <v-btn
                                        icon
                                        ripple
                                    >
                                        <v-icon color="grey lighten-1">
                                            info
                                        </v-icon>
                                    </v-btn>
                                </v-list-tile-action>
                            </v-list-tile>
                        </v-list>
                    </v-flex>
                    <v-flex
                        v-if="dadosReadequacao.dsOrientacao && perfilAceito(['analise'])"
                        xs12
                    >
                        <v-list
                            two-line
                        >
                            <v-list-tile
                                avatar
                                @click="visualizarOrientacao = !visualizarOrientacao"
                            >
                                <visualizar-campo-detalhado
                                    v-if="visualizarOrientacao"
                                    :dialog="true"
                                    :dados="{ titulo: 'Observação', descricao: dadosReadequacao.dsOrientacao }"
                                    @fechar-visualizacao="visualizarOrientacao = false"
                                />
                                <v-list-tile-avatar>
                                    <v-icon class="green lighten-1 white--text">
                                        assignment
                                    </v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-content>
                                    <v-list-tile-title>Observação</v-list-tile-title>
                                </v-list-tile-content>
                                <v-list-tile-action>
                                    <v-btn
                                        icon
                                        ripple
                                    >
                                        <v-icon color="grey lighten-1">
                                            report
                                        </v-icon>
                                    </v-btn>
                                </v-list-tile-action>
                            </v-list-tile>
                        </v-list>
                    </v-flex>
                    <v-flex
                        xs12
                    >
                        <v-card>
                            <v-card-title
                                class="subheading"
                            >
                                <v-btn
                                    fab
                                    depressed
                                    small
                                    class="green lighten-1"
                                >
                                    <v-icon color="white">
                                        mode_comment
                                    </v-icon>
                                </v-btn>
                                Solicitação
                            </v-card-title>
                            <v-card-text>
                                <template
                                    v-if="getTemplateParaTipo && dialog === true"
                                >
                                    <div
                                        v-if="typeof dadosReadequacao.dsSolicitacao !=='undefined' && typeof getDadosCampo.valor !== 'undefined'"
                                    >
                                        <component
                                            :is="getTemplateParaTipo"
                                            :original="getDadosCampo"
                                            :changed="dadosReadequacao"
                                            :dados-readequacao="dadosReadequacao"
                                            :readonly="true"
                                        />
                                    </div>
                                    <div v-else>
                                        <carregando
                                            :text="'Montando comparação...'"
                                            class="mt-5"
                                        />
                                    </div>
                                </template>
                                <div
                                    v-else
                                >
                                    <div class="mb-3">
                                        Este tipo de readequação ainda não possui uma visualização específica!
                                    </div>
                                    <v-btn
                                        color="secondary"
                                        @click.stop="redirect()"
                                    >
                                        <v-icon>
                                            gavel
                                        </v-icon>
                                        Visualizar modo antigo
                                    </v-btn>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
import _ from 'lodash';
import { mapGetters, mapActions } from 'vuex';
import { utils } from '@/mixins/utils';
import Carregando from '@/components/CarregandoVuetify';
import CampoDiff from '@/components/CampoDiff';
import Const from '../../const';
import VisualizarCampoDetalhado from './VisualizarCampoDetalhado';
import MxReadequacao from '../../mixins/Readequacao';
import ComparacaoTextual from '../analise/ComparacaoTextual';
import ComparacaoPlanilha from '../analise/ComparacaoPlanilha';

export default {
    name: 'VisualizarReadequacaoButton',
    components: {
        VisualizarCampoDetalhado,
        CampoDiff,
        Carregando,
        ComparacaoTextual,
        ComparacaoPlanilha,
    },
    mixins: [
        utils,
        MxReadequacao,
    ],
    props: {
        obj: {
            type: Object,
            default: () => {},
        },
        dadosProjeto: {
            type: Object,
            default: () => {},
        },
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
        perfisAceitos: {
            type: [Array, Object],
            default: () => [],
        },
        perfil: {
            type: [Number, String],
            default: 0,
        },
    },
    data() {
        return {
            dialog: false,
            loading: true,
            loaded: {
                projeto: false,
                campo: false,
            },
            panel: [true, true],
            visualizarAvaliacao: false,
            visualizarJustificativa: false,
            visualizarOrientacao: false,
            projeto: {},
            tiposComponentes: {
                textarea: 'ComparacaoTextual',
                input: 'ComparacaoTextual',
                date: 'ComparacaoTextual',
                planilha: 'ComparacaoPlanilha',
            },
            tiposComponentesRedirect: {
                local_realizacao: '/readequacao/readequacoes/visualizar-readequacao?id=',
                planilha: '/readequacao/readequacoes/visualizar-readequacao?id=',
                saldo_aplicacao: '/readequacao/readequacoes/visualizar-readequacao?id=',
                plano_distribuicao: '/readequacao/readequacoes/visualizar-readequacao?id=',
                transferencia_recursos: '/readequacoes/visualizar-readequacao?id=',
            },
        };
    },
    computed: {
        ...mapGetters({
            campoAtual: 'readequacao/getCampoAtual',
            getDadosProjeto: 'projeto/projeto',
        }),
        existeAvaliacao() {
            if (this.dadosReadequacao
                && this.perfilAceito(['analise'])) {
                if (!_.isNull(this.dadosReadequacao.dsAvaliacao)
                    && !_.isNull(this.dadosReadequacao.dtAvaliador)) {
                    return true;
                }
            }
            return false;
        },
        getDadosCampo() {
            return this.parseDadosCampo(this.campoAtual);
        },
        getTemplateParaTipo() {
            let templateName = false;
            const chave = `key_${this.dadosReadequacao.idTipoReadequacao}`;
            if (Object.prototype.hasOwnProperty.call(this.campoAtual, chave)) {
                templateName = this.tiposComponentes[this.campoAtual[chave].tpCampo];
            }
            return templateName;
        },
        tipoComponente() {
            let tipoComponente = '';
            const chave = `key_${this.dadosReadequacao.idTipoReadequacao}`;
            if (Object.prototype.hasOwnProperty.call(this.campoAtual, chave)) {
                tipoComponente = this.campoAtual[chave].tpCampo;
            }
            return tipoComponente;
        },
    },
    watch: {
        dialog() {
            if (this.dialog === true) {
                this.resetLoading();
                this.obterCampoAtual({
                    idPronac: this.dadosReadequacao.idPronac,
                    idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
                });
                if (typeof this.dadosProjeto !== 'undefined') {
                    this.projeto = this.dadosProjeto;
                    this.loaded.projeto = true;
                } else {
                    this.buscarProjetoCompleto(this.dadosReadequacao.idPronac);
                }
            } else {
                this.resetLoading();
            }
        },
        getDadosCampo(value) {
            if (typeof value === 'object') {
                this.loaded.campo = true;
            }
        },
        getDadosProjeto(value) {
            if (typeof value === 'object') {
                if (Object.keys(value).length > 0) {
                    this.projeto = value;
                    this.loaded.projeto = true;
                }
            }
        },
        loaded: {
            handler(value) {
                const fullyLoaded = _.keys(value).every(i => value[i]);
                if (fullyLoaded) {
                    this.loading = false;
                }
            },
            deep: true,
        },
    },
    methods: {
        ...mapActions({
            buscarProjetoCompleto: 'projeto/buscarProjetoCompleto',
            obterCampoAtual: 'readequacao/obterCampoAtual',
        }),
        perfilAceito(tiposPerfil) {
            return tiposPerfil.some((perfil) => {
                if (Object.prototype.hasOwnProperty.call(this.perfisAceitos, perfil)) {
                    return this.verificarPerfil(this.perfil, this.perfisAceitos[perfil]);
                }
                return false;
            });
        },
        getStatusAnalise(siEncaminhamento) {
            if (Object.prototype.hasOwnProperty.call(Const.SI_ENCAMINHAMENTO, siEncaminhamento)) {
                return Const.SI_ENCAMINHAMENTO[siEncaminhamento];
            }
            return false;
        },
        redirect() {
            if (this.tipoComponente !== '') {
                if (Object.prototype.hasOwnProperty.call(this.tiposComponentesRedirect, this.tipoComponente)) {
                    this.abreUrl();
                }
            }
        },
        abreUrl() {
            if (this.urlRedirect !== 'undefined') {
                let routePath = this.urlRedirect;
                if (routePath.match(/#/)) {
                    routePath = routePath.replace(/#/, '');
                    this.$router.push({ path: routePath });
                } else {
                    window.open(routePath, '_blank');
                }
            }
        },
        resetLoading() {
            this.loading = true;
            this.loaded.projeto = false;
            this.loaded.campo = false;
        },
    },
};
</script>
