<template>
    <v-expansion-panel
        :value="[true, true, true]"
        expand
    >
        <v-expansion-panel-content>
            <v-layout
                slot="header"
                row
                justify-space-between
            >
                <v-icon class="material-icons">
                    assignment
                </v-icon>
                <span class="ml-2 mt-1">
                    Análise do conteúdo
                </span>
                <v-spacer />
            </v-layout>
            <v-layout
                v-if="Object.keys(analiseConteudo).length > 0"
                wrap
                class="pa-3"
            >
                <v-flex
                    v-if="analiseConteudo.ParecerDeConteudo.length > 1"
                    xs12
                    sm12
                    md12
                >
                    <p><b>Parecer favorável: </b> {{ analiseConteudo.ParecerFavoravel | formatarLabelSimOuNao }}</p>
                </v-flex>
                <v-flex
                    v-if="analiseConteudo.ParecerDeConteudo.length > 1"
                    xs12
                    sm12
                    md12
                >
                    <p><b>Parecer de Conteúdo do Produto</b></p>
                    <div
                        v-html="analiseConteudo.ParecerDeConteudo"
                    />
                </v-flex>
                <v-flex
                    v-else
                    xs12
                    sm12
                    md12
                >
                    <b>Conteúdo ainda não avaliado</b>
                </v-flex>
            </v-layout>
            <s-carregando
                v-else
                text="Carregando análise do produto"
            />
        </v-expansion-panel-content>
        <v-expansion-panel-content v-if="produto.stPrincipal === 1">
            <v-layout
                slot="header"
                row
                justify-space-between
            >
                <v-icon class="material-icons">
                    assignment_turned_in
                </v-icon>
                <span class="ml-2 mt-1">
                    Consolidação do Parecer
                </span>
                <v-spacer />
            </v-layout>
            <v-layout
                v-if="Object.keys(consolidacaoGetter).length > 0"
                wrap
                class="pa-3"
            >
                <v-flex
                    v-if="consolidacaoGetter.ResumoParecer.length > 1"
                    xs12
                    sm12
                    md12
                >
                    <p><b>Parecer</b></p>
                    <div
                        v-html="consolidacaoGetter.ResumoParecer"
                    />
                </v-flex>
                <v-flex
                    v-else
                    xs12
                    sm12
                    md12
                >
                    <b>Parecer ainda não realizado</b>
                </v-flex>
            </v-layout>
            <s-carregando
                v-else
                text="Carregando parecer"
            />
        </v-expansion-panel-content>
        <v-expansion-panel-content>
            <v-layout
                slot="header"
                row
                justify-space-between
            >
                <v-icon class="material-icons">
                    monetization_on
                </v-icon>
                <span class="ml-2 mt-1">
                    Análise de custo
                </span>
                <v-spacer />
            </v-layout>
            <visualizacao-analise-produto-planilha :produto="produto" />
        </v-expansion-panel-content>
    </v-expansion-panel>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import SCarregando from '@/components/CarregandoVuetify';
import VisualizacaoAnaliseProdutoPlanilha from '@/modules/parecer/components/VisualizacaoAnaliseProdutoPlanilha';

export default {
    name: 'VisualizacaoAnaliseProduto',
    components: {
        VisualizacaoAnaliseProdutoPlanilha, SCarregando,
    },
    mixins: [utils],
    props: {
        value: {
            type: Boolean,
            default: false,
        },
        produto: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            dialogDetalhamento: false,
            loading: true,
            totaisPlanilha: [
                {
                    label: 'Valor Sugerido',
                    column: 'VlSugeridoParecerista',
                },
                {
                    label: 'Valor Solicitado',
                    column: 'VlSolicitado',
                },
            ],
            agrupamentos: ['FonteRecurso', 'Produto', 'Etapa', 'UF', 'Cidade'],
        };
    },
    computed: {
        ...mapGetters({
            analiseConteudo: 'parecer/getAnaliseConteudoSecundario',
            planilha: 'parecer/getPlanilhaProdutoSecundario',
            consolidacaoGetter: 'parecer/getConsolidacao',
        }),
    },
    watch: {
        value(val) {
            this.dialogDetalhamento = val;
        },
        dialogDetalhamento(val) {
            this.$emit('input', val);
        },
        produto(val) {
            if (Object.keys(val).length > 0 && this.value) {
                this.visualizarDetalhesProduto(val);
            }
        },
    },
    mounted() {
        if (Object.keys(this.produto).length > 0) {
            this.visualizarDetalhesProduto(this.produto);
        }
    },
    methods: {
        ...mapActions({
            obterConsolidacaoAction: 'parecer/obterConsolidacao',
            obterAnaliseConteudoSecundario: 'parecer/obterAnaliseConteudoSecundario',
            obterPlanilha: 'parecer/obterPlanilhaProdutoSecundario',
        }),
        visualizarDetalhesProduto(produto) {
            this.obterAnaliseConteudoSecundario({
                id: produto.idProduto,
                idPronac: produto.idPronac,
            });

            this.obterPlanilha({
                id: produto.idProduto,
                idPronac: produto.idPronac,
                stPrincipal: produto.stPrincipal,
            });

            if (produto.stPrincipal === 1) {
                this.obterConsolidacaoAction({
                    id: produto.idProduto,
                    idPronac: produto.idPronac,
                });
            }
        },
    },
};
</script>
