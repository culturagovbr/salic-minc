<template>
    <carregando
        v-if="Object.keys(dadosProjeto).length === 0"
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
                <v-spacer />
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

                <execucao-receita-despesa-dialog
                    :id-pronac="idPronac"
                    class="mr-2"
                />

                <extratos-bancarios-dialog
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
                <v-layout row>
                    <v-flex
                        xs4
                        sm4
                        md2
                    >
                        <v-switch
                            v-model="mostrarListagem"
                            color="primary"
                            label="Exibir planilha em lista"
                        />
                    </v-flex>
                    <v-flex
                        xs4
                        sm4
                        md2
                    >
                        <v-switch
                            v-model="ocultarItensNaoComprovados"
                            color="primary"
                            label="Ocultar itens não comprovados"
                        />
                    </v-flex>
                </v-layout>
            </v-container>
            <s-planilha
                :array-planilha="planilha"
                :list-items="mostrarListagem"
                :agrupamentos="agrupamentos"
                :totais="totaisPlanilha"
            >
                <template slot-scope="slotProps">
                    <slot :itens="slotProps.itens">
                        <s-planilha-itens-padrao :table="slotProps.itens" />
                    </slot>
                </template>
            </s-planilha>
        </template>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import CONST from '../../const';

import ConsolidacaoAnalise from '../components/ConsolidacaoAnalise';
import HistoricoDiligencias from '@/modules/avaliacaoResultados/components/components/HistoricoDiligencias';
import ParecerTecnicoPlanilhaHeader from './PlanilhaHeader';
import SPlanilha from '@/components/Planilha/PlanilhaV2';
import Carregando from '@/components/CarregandoVuetify';
import RelacaoPagamentoDialog from '@/modules/projeto/visualizar/components/prestacaoContas/RelacaoPagamentoDialog';
import ConciliacaoBancariaDialog
    from '@/modules/projeto/visualizar/components/dadosBancarios/ConciliacaoBancariaDialog';
import ExecucaoReceitaDespesaDialog
    from '@/modules/projeto/visualizar/components/prestacaoContas/ExecucaoReceitaDespesaDialog';
import ExtratosBancariosDialog from '@/modules/projeto/visualizar/components/dadosBancarios/ExtratosBancariosDialog';


export default {
    name: 'PlanilhaAvaliacaoFinanceira',
    components: {
        ExtratosBancariosDialog,
        ExecucaoReceitaDespesaDialog,
        ConciliacaoBancariaDialog,
        RelacaoPagamentoDialog,
        ParecerTecnicoPlanilhaHeader,
        HistoricoDiligencias,
        ConsolidacaoAnalise,
        Carregando,
        SPlanilha,
    },
    data() {
        return {
            CONST,
            idPronac: this.$route.params.id,
            agrupamentos: [
                'Produto',
                'descEtapa',
                'uf',
                'cidade',
            ],
            totaisPlanilha: [],
            mostrarListagem: false,
            ocultarItensNaoComprovados: false,
        };
    },
    computed: {
        ...mapGetters({
            getPlanilha: 'avaliacaoResultados/planilha',
            getProjetoAnalise: 'avaliacaoResultados/projetoAnalise',
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
        planilha() {
            let planilha = this.getPlanilha;
            if (planilha === null || Object.keys(planilha).length === 0) {
                return [];
            }

            if (this.ocultarItensNaoComprovados) {
                planilha = this.getPlanilha.filter(item => item.vlComprovado > 0);
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
            buscarPlanilhaAction: 'avaliacaoResultados/syncPlanilhaAction',
            buscarProjetoAnaliseAction: 'avaliacaoResultados/syncProjetoAction',
        }),
        goBack() {
            if (window.history.length > 1) {
                this.$router.go(-1);
            } else {
                this.$router.push('/');
            }
        },
    },
};
</script>
