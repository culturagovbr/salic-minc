<template>
    <carregando
        v-if="Object.keys(dadosProjeto).length == 0"
        text="Carregando ..."
    />
    <v-container
        v-else
        fluid
    >
        <v-toolbar>
            <v-btn
                icon
                class="hidden-xs-only"
                @click="goBack()"
            >
                <v-icon>arrow_back</v-icon>
            </v-btn>
            <v-toolbar-title>
                Planilha: {{ dadosProjeto.items.pronac }} &#45; {{ dadosProjeto.items.nomeProjeto }}
            </v-toolbar-title>
            <v-spacer />

            <v-chip v-if="isProjetoDiligenciado">
                Projeto diligenciado
            </v-chip>
            <v-chip v-else-if="estado.estadoId === CONST.ESTADO_ANALISE_PARECER">
                Projeto em analise
            </v-chip>
            <historico-diligencias
                :id-pronac="idPronac"
                :obj="dadosProjeto.items.diligencia"
            />
        </v-toolbar>
        <v-card>
            <v-card-text>
                <v-alert
                    v-if="documento != 0"
                    :value="true"
                    color="orange darken-3"
                >
                    Existe documento disponível para assinatura nesse projeto.
                    <v-btn
                        :href="'/assinatura/index/visualizar-projeto?idDocumentoAssinatura=' + documento.idDocumentoAssinatura"
                        small
                    >
                        Ver documento
                    </v-btn>
                </v-alert>
                <parecer-tecnico-planilha-header :dados="dadosProjeto" />
            </v-card-text>
            <v-card-actions>
                <v-btn
                    :href="'/consultardadosprojeto/index?idPronac=' + idPronac"
                    target="_blank"
                    class="mr-2"
                >
                    VER PROJETO
                </v-btn>

                <consolidacao-analise
                    :id-pronac="idPronac"
                    :nome-projeto="dadosProjeto.items.nomeProjeto"
                    class="mr-2"
                />

                <relacao-pagamento-dialog
                    :id-pronac="idPronac"
                    class="mr-2"
                />

                <conciliacao-bancaria-dialog
                    :id-pronac="idPronac"
                    class="mr-2"
                />
            </v-card-actions>
        </v-card>

        <template v-if="!Object.keys(planilha).length">
            <Carregando text="Carregando planilha ..." />
        </template>
        <template v-else>
            <v-container
                fluid
                class="pa-0"
            >
                <v-flex
                    xs12
                    sm6
                    class="py-2"
                >
                    <v-btn-toggle
                        v-model="opcoesDeVisualizacao"
                        multiple
                    >
                        <v-tooltip bottom>
                            <v-btn
                                slot="activator"
                                flat
                            >
                                <v-icon>list</v-icon>
                            </v-btn>
                            <span>
                                Apenas itens
                            </span>
                        </v-tooltip>
                        <v-tooltip bottom>
                            <v-btn
                                slot="activator"
                                flat
                            >
                                <v-icon>money_off</v-icon>
                            </v-btn>
                            <span>
                                Ocultar itens sem comprovação
                            </span>
                        </v-tooltip>
                    </v-btn-toggle>
                </v-flex>
            </v-container>
            <s-planilha
                :array-planilha="planilha"
                :list-items="mostrarListagem"
                :agrupamentos="agrupamentos"
                :totais="totaisPlanilha"
            >
                <template slot-scope="slotProps">
                    <v-data-table
                        :headers="headers"
                        :items="Object.values(slotProps.itens)"
                        hide-actions
                        disable-initial-sort
                    >
                        <template
                            slot="items"
                            slot-scope="props"
                        >
                            <td>
                                <v-tooltip
                                    bottom
                                >
                                    <v-progress-circular
                                        v-if="props.item.qtComprovado > 0"
                                        slot="activator"
                                        style="margin: 1px 0px"
                                        height="5"
                                        :value="obterPercentualProgresso(props.item)"
                                        :color="obterCorProgresso(props.item)"
                                    >
                                        {{ props.item.qtComprovado }}
                                    </v-progress-circular>
                                    <span> {{ obterTextoProgresso(props.item) }} </span>
                                </v-tooltip>
                            </td>
                            <td>
                                {{ props.item.item }}
                            </td>
                            <td class="text-xs-right">
                                {{ props.item.varlorAprovado | moedaMasc }}
                            </td>
                            <td class="text-xs-right">
                                {{ props.item.varlorComprovado | moedaMasc }}
                            </td>
                            <td class="text-xs-right">
                                {{ (props.item.varlorAprovado - props.item.varlorComprovado) | moedaMasc }}
                            </td>
                            <td>
                                <v-btn
                                    v-if="podeEditar(props.item.varlorComprovado)"
                                    color="primary"
                                    dark
                                    icon
                                    title="Avaliar comprovantes do item"
                                    @click="avaliarItem(props.item)"
                                >
                                    <v-icon>
                                        gavel
                                    </v-icon>
                                </v-btn>
                            </td>
                        </template>
                    </v-data-table>
                </template>
            </s-planilha>
            <analisar-item
                v-if="isModalVisible === 'avaliacao-item'"
                :id-pronac="idPronac"
                :item="itemEmAvaliacao.item"
                :descricao-produto="itemEmAvaliacao.produto"
                :descricao-etapa="itemEmAvaliacao.etapa"
                :uf="itemEmAvaliacao.Uf"
                :produto="itemEmAvaliacao.cdProduto"
                :idmunicipio="itemEmAvaliacao.cdCidade"
                :etapa="itemEmAvaliacao.cdEtapa"
                :cd-produto="itemEmAvaliacao.cdProduto"
                :cd-uf="itemEmAvaliacao.cdUF"
                :dt-inicio-execucao="dadosProjeto.items.dtInicioExecucao"
                :dt-fim-execucao="dadosProjeto.items.dtFimExecucao"
            />
        </template>
        <v-speed-dial
            v-if="(!isProjetoDiligenciado)"
            v-model="fab"
            bottom
            right
            direction="top"
            open-on-hover
            transition="slide-y-reverse-transition"
            fixed
        >
            <v-btn
                slot="activator"
                v-model="fab"
                color="red darken-2"
                dark
                fab
            >
                <v-icon>menu</v-icon>
                <v-icon>close</v-icon>
            </v-btn>
            <v-tooltip
                v-if="(documento != 0)"
                left
            >
                <v-btn
                    slot="activator"
                    :href="'/assinatura/index/visualizar-projeto?idDocumentoAssinatura=' + documento.idDocumentoAssinatura"
                    fab
                    dark
                    small
                    color="green"
                >
                    <v-icon>edit</v-icon>
                </v-btn>
                <span>Assinar</span>
            </v-tooltip>
            <v-tooltip
                v-if="(documento == 0 && !isProjetoDiligenciado)"
                left
            >
                <v-btn
                    slot="activator"
                    :to="'/emitir-parecer/' + idPronac"
                    fab
                    dark
                    small
                    color="teal"
                    @click.native="getConsolidacao(idPronac)"
                >
                    <v-icon>gavel</v-icon>
                </v-btn>
                <span>Emitir Parecer</span>
            </v-tooltip>
            <v-tooltip
                v-if="(documento == 0) && !isProjetoDiligenciado"
                left
            >
                <v-btn
                    slot="activator"
                    :to="'/diligenciar/' + idPronac"
                    fab
                    dark
                    small
                    color="red ligthen-4"
                >
                    <v-icon>warning</v-icon>
                </v-btn>
                <span>Diligenciar</span>
            </v-tooltip>
        </v-speed-dial>
    </v-container>
</template>

<script>
import Vue from 'vue';
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import ConsolidacaoAnalise from '../components/ConsolidacaoAnalise';
import AnalisarItem from './AnalisarItem';
import Moeda from '../../../../filters/money';
import HistoricoDiligencias from '@/modules/avaliacaoResultados/components/components/HistoricoDiligencias';
import ParecerTecnicoPlanilhaHeader from '@/modules/avaliacaoResultados/components/ParecerTecnico/PlanilhaHeader';
import SPlanilha from '@/components/Planilha/PlanilhaV2';
import CONST from '../../const';
import RelacaoPagamentoDialog from '@/modules/projeto/visualizar/components/prestacaoContas/RelacaoPagamentoDialog';
import ConciliacaoBancariaDialog
    from '@/modules/projeto/visualizar/components/dadosBancarios/ConciliacaoBancariaDialog';


Vue.filter('moedaMasc', Moeda);

export default {
    name: 'Planilha',
    components: {
        ConciliacaoBancariaDialog,
        RelacaoPagamentoDialog,
        ParecerTecnicoPlanilhaHeader,
        HistoricoDiligencias,
        ConsolidacaoAnalise,
        AnalisarItem,
        Carregando,
        SPlanilha,
    },
    data() {
        return {
            CONST,
            headers: [
                {
                    text: '#', value: 'qtComprovado', sortable: true, align: 'left',
                },
                { text: 'Item', value: 'item', sortable: true },
                {
                    text: 'Vl. Aprovado (R$)', value: 'varlorAprovado', sortable: true, align: 'right',
                },
                {
                    text: 'Vl. Comprovado (R$)', value: 'varlorComprovado', sortable: true, align: 'right',
                },
                {
                    text: 'Vl. a Comprovar (R$)', value: 'vlAComprovar', sortable: true, align: 'right',
                },
                { text: '', value: 'varlorComprovado', sortable: false },
            ],
            fab: false,
            idPronac: this.$route.params.id,
            itemEmAvaliacao: {},
            opcoesDeVisualizacao: [],
            agrupamentos: [
                'Produto',
                'descEtapa',
                'uf',
                'cidade',
            ],
            totaisPlanilha: [
                {
                    label: 'Valor Aprovado',
                    column: 'vlAprovado',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            getPlanilha: 'avaliacaoResultados/planilha',
            getProjetoAnalise: 'avaliacaoResultados/projetoAnalise',
            isModalVisible: 'modal/default',
        }),
        dadosProjeto() {
            if (Object.keys(this.getProjetoAnalise).length > 0) {
                return this.getProjetoAnalise.data;
            }
            return {};
        },
        documento() {
            let { documento } = this.getProjetoAnalise.data.items;
            documento = documento !== null ? this.getProjetoAnalise.data.items.documento : 0;
            return documento;
        },
        estado() {
            let { estado } = this.getProjetoAnalise.data.items;
            estado = (estado !== null) ? this.getProjetoAnalise.data.items.estado : 0;
            return estado;
        },
        isProjetoDiligenciado() {
            const { diligencia } = this.dadosProjeto.items;
            return diligencia && diligencia.DtSolicitacao && !diligencia.DtResposta;
        },
        mostrarListagem() {
            return this.isOptionActive(0);
        },
        ocultarItensNaoComprovados() {
            return this.isOptionActive(1);
        },
        planilha() {
            let planilha = this.getPlanilha;
            if (planilha === null || Object.keys(planilha).length === 0) {
                return [];
            }

            if (this.ocultarItensNaoComprovados) {
                planilha = this.getPlanilha.filter(item => item.varlorComprovado > 0);
            }

            return planilha;
        },
    },

    mounted() {
        this.buscarPlanilhaAction(this.idPronac);
        this.buscarProjetoAnaliseAction(this.idPronac);
    },

    methods: {
        ...mapActions({
            modalOpen: 'modal/modalOpen',
            requestEmissaoParecer: 'avaliacaoResultados/getDadosEmissaoParecer',
            buscarPlanilhaAction: 'avaliacaoResultados/syncPlanilhaAction',
            buscarProjetoAnaliseAction: 'avaliacaoResultados/syncProjetoAction',
        }),
        getConsolidacao(id) {
            this.requestEmissaoParecer(id);
        },
        moeda: (moedaString) => {
            const moeda = Number(moedaString);
            return moeda.toLocaleString('pt-br', { currency: 'BRL' });
        },
        podeEditar(varlorComprovado) {
            if (varlorComprovado !== 0
                && !this.dadosProjeto.items.diligencia
                && this.documento.length === 0
            ) {
                return true;
            }

            return false;
        },
        avaliarItem(item) {
            this.itemEmAvaliacao = {
                item,
                produto: item.Produto,
                etapa: item.descEtapa,
                Uf: item.uf,
                cdProduto: item.cdProduto,
                cdCidade: item.cdCidade,
                cdEtapa: item.cdEtapa,
                cdUF: item.cdUF,
            };
            this.modalOpen('avaliacao-item');
        },
        goBack() {
            if (window.history.length > 1) {
                this.$router.go(-1);
            } else {
                this.$router.push('/');
            }
        },
        isOptionActive(index) {
            return this.opcoesDeVisualizacao.includes(index);
        },
        obterPercentualProgresso(item) {
            return ((item.qtComprovadoValidado + item.qtComprovadoRecusada) / item.qtComprovado) * 100;
        },
        obterCorProgresso(item) {
            let cor = 'green';
            if (item.qtComprovadoRecusada > 0) {
                cor = 'orange';
            }

            if (item.qtComprovadoRecusada > item.qtComprovadoValidado) {
                cor = 'red';
            }
            return cor;
        },
        obterTextoProgresso(item) {
            return `${item.qtComprovadoValidado} validado(s) e ${item.qtComprovadoRecusada} recusado(s) `
                + `de ${item.qtComprovado} comprovado(s)`;
        },
    },
};
</script>
