<template>
    <div
        v-if="dados"
        class="proposta"
    >
        <Carregando
            v-if="loading"
            :text="'Carregando proposta'"
        />
        <v-container
            v-if="!loading"
            fluid
            class="pa-0"
        >
            <v-flex
                xs12
                sm12
                class="py-2 text-xs-right"
            >
                <v-tooltip
                    bottom
                >
                    <v-btn
                        slot="activator"
                        color="grey lighten-3"
                        icon
                        small
                        @click="ordernarLista()"
                    >
                        <v-icon
                            color="blue-grey darken-5"
                        >
                            sort_by_alpha
                        </v-icon>
                    </v-btn>
                    <span> Ordenar lista de itens </span>
                </v-tooltip>
            </v-flex>
        </v-container>
        <v-expansion-panel
            v-show="!loading"
            expand
            focusable
        >
            <v-expansion-panel-content>
                <v-layout
                    slot="header"
                    class="primary--text"
                >
                    <v-icon class="mr-2 primary--text">
                        assignment
                    </v-icon>
                    <span v-if="dados.PRONAC">
                        Projeto - {{ dados.PRONAC }} - {{ dados.NomeProjeto }}
                    </span>
                    <span v-else>
                        Proposta - {{ idpreprojeto }} - {{ dados.NomeProjeto }}
                    </span>
                </v-layout>
                <proposta-identificacao
                    :idpreprojeto="idpreprojeto"
                    :proposta="dados"
                />
            </v-expansion-panel-content>
            <v-expansion-panel-content
                v-for="(item, index) of items"
                :key="index"
            >
                <v-layout
                    slot="header"
                    class="primary--text"
                >
                    <v-icon class="mr-2 primary--text">
                        {{ item.icon }}
                    </v-icon>
                    <span v-html="item.label " />
                </v-layout>
                <div class="pa-3">
                    <component
                        :is="item.component"
                        :value="item.value"
                        :idpreprojeto="dados.idPreProjeto"
                        :proposta="dados"
                    />
                </div>
            </v-expansion-panel-content>
        </v-expansion-panel>
    </div>
    <div
        v-else
        class="center-align"
    >
        <div class="padding20 card-panel">
            Opa! Proposta n&atilde;o encontrada...
        </div>
    </div>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';

import Carregando from '@/components/CarregandoVuetify';
import SalicTextoSimples from '@/components/SalicTextoSimples';
import PropostaIdentificacao from './components/PropostaIdentificacao';
import PropostaTexto from './components/PropostaTexto';
import PropostaHistoricoAvaliacoes from './components/PropostaHistoricoAvaliacoes';
import PropostaHistoricoSugestoesEnquadramento from './components/PropostaHistoricoSugestoesEnquadramento';
import PropostaHistoricoSolicitacoes from './components/PropostaHistoricoSolicitacoes';
import PropostaDocumentos from './components/PropostaDocumentos';
import PropostaPlanoDistribuicao from './components/PropostaPlanoDistribuicao';
import PropostaFontesDeRecursos from './components/PropostaFontesDeRecursos';
import PropostaLocalRealizacaoDeslocamento from './components/PropostaLocalRealizacaoDeslocamento';
import PropostaCustosVinculados from './components/PropostaCustosVinculados';
import PropostaProponente from './components/PropostaProponente';
import PropostaPlanilha from './components/PropostaPlanilha';

export default {
    name: 'Proposta',
    components: {
        PropostaIdentificacao,
        PropostaHistoricoAvaliacoes,
        PropostaHistoricoSugestoesEnquadramento,
        PropostaHistoricoSolicitacoes,
        PropostaProponente,
        PropostaTexto,
        SalicTextoSimples,
        Carregando,
        PropostaDocumentos,
        PropostaPlanoDistribuicao,
        PropostaFontesDeRecursos,
        PropostaLocalRealizacaoDeslocamento,
        PropostaCustosVinculados,
        PropostaPlanilha,
    },
    props: {
        idpreprojeto: {
            type: [String, Number],
            default: '',
        },
        proposta: {
            type: Object,
            default: () => {
            },
        },
    },
    data() {
        return {
            dados: {
                type: Object,
                default() {
                    return {};
                },
            },
            loading: true,
            order: 'desc',
            opcoesDeVisualizacao: [0],
            items: [
                {
                    label: 'Hist&oacute;rico de avalia&ccedil;&otilde;es',
                    icon: 'history',
                    component: 'proposta-historico-avaliacoes',
                    class: '',
                    value: this.idpreprojeto,
                },
                {
                    label: 'Hist&oacute;rico de sugest&otilde;es de enquadramento',
                    icon: 'history',
                    component: 'proposta-historico-sugestoes-enquadramento',
                    class: '',
                    value: this.idpreprojeto,
                },
                {
                    label: 'Proponente',
                    icon: 'person',
                    component: 'proposta-proponente',
                    class: '',
                    value: this.idpreprojeto,
                },
                {
                    label: 'Ficha t&eacute;cnica',
                    icon: 'assignment',
                    component: 'proposta-texto',
                    class: '',
                    value: 'FichaTecnica',
                },
                {
                    label: 'Resumo',
                    icon: 'short_text',
                    component: 'proposta-texto',
                    class: '',
                    value: 'ResumoDoProjeto',
                },
                {
                    label: 'Objetivos',
                    icon: 'playlist_add_check',
                    component: 'proposta-texto',
                    class: '',
                    value: 'Objetivos',
                },
                {
                    label: 'Etapa de Trabalho',
                    icon: 'date_range',
                    component: 'proposta-texto',
                    class: '',
                    value: 'EtapaDeTrabalho',
                },
                {
                    label: 'Acessibilidade',
                    icon: 'accessible',
                    component: 'proposta-texto',
                    class: '',
                    value: 'Acessibilidade',
                },
                {
                    label: 'Especifica&ccedil;&otilde;es t&eacute;cnicas do produto',
                    icon: 'assignment',
                    component: 'proposta-texto',
                    class: '',
                    value: 'EspecificacaoTecnica',
                },
                {
                    label: 'Sinopse de Obra',
                    icon: 'burst_mode',
                    component: 'proposta-texto',
                    class: '',
                    value: 'Sinopse',
                },
                {
                    label: 'Democratiza&ccedil;&atilde;o de Acesso',
                    icon: 'accessibility',
                    component: 'proposta-texto',
                    class: '',
                    value: 'DemocratizacaoDeAcesso',
                },
                {
                    label: 'Justificativa',
                    icon: 'subject',
                    component: 'proposta-texto',
                    class: '',
                    value: 'Justificativa',
                },
                {
                    label: 'Descri&ccedil;&atilde;o de Atividades',
                    icon: 'timeline',
                    component: 'proposta-texto',
                    class: '',
                    value: 'DescricaoAtividade',
                },
                {
                    label: 'Documentos anexados',
                    icon: 'attachment',
                    component: 'proposta-documentos',
                    class: '',
                    value: this.idpreprojeto,
                },
                {
                    label: 'Plano Distribui&ccedil;&atilde;o',
                    icon: 'perm_media',
                    component: 'proposta-plano-distribuicao',
                    class: '',
                    value: this.idpreprojeto,
                },
                {
                    label: 'Fonte de Recurso',
                    icon: 'attach_money',
                    component: 'proposta-fontes-de-recursos',
                    class: '',
                    value: this.idpreprojeto,
                },
                {
                    label: 'Local de realiza&ccedil;&atilde;o/Deslocamento',
                    icon: 'place',
                    component: 'proposta-local-realizacao-deslocamento',
                    class: '',
                    value: this.idpreprojeto,
                },
                {
                    label: 'Custos Vinculados',
                    icon: 'attach_money',
                    component: 'proposta-custos-vinculados',
                    class: '',
                    value: this.idpreprojeto,
                },
                {
                    label: 'Planilha or&ccedil;ament&aacute;ria',
                    icon: 'attach_money',
                    component: 'proposta-planilha',
                    class: '',
                    value: this.idpreprojeto,
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProposta: 'proposta/proposta',
        }),
        ordernar() {
            return this.obterOpcaoAtiva(1);
        },
    },
    watch: {
        dadosProposta(value) {
            this.dados = value;
            this.loading = false;
        },
        idpreprojeto(value) {
            if (value !== '') {
                this.buscarDadosProposta(value);
            }
        },
    },
    mounted() {
        if (this.idpreprojeto !== ''
                && typeof this.proposta === 'undefined') {
            this.buscarDadosProposta(this.idpreprojeto);
            this.dados = this.dadosProposta;
        }

        if (typeof this.proposta !== 'undefined') {
            this.dados = this.proposta;
            this.loading = false;
        }
    },
    methods: {
        ...mapActions({
            buscarDadosProposta: 'proposta/buscarDadosProposta',
        }),
        ordernarLista() {
            // this.items. (this.compare;
            this.order = this.order === 'desc' ? 'asc' : 'desc';
            this.items = _.orderBy(this.items, ['label'], [this.order]);
        },
        obterOpcaoAtiva(index) {
            return this.opcoesDeVisualizacao.includes(index);
        },
    },
};
</script>
