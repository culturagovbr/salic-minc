<template>
    <v-expansion-panel
        :value="[true, true]"
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
        <v-expansion-panel-content>
            <v-layout
                slot="header"
                row
                justify-space-between
            >
                <v-icon class="material-icons">
                    attach_money
                </v-icon>
                <span class="ml-2 mt-1">
                    Análise de custo
                </span>
                <v-spacer />
            </v-layout>
            <visualizacao-analise-produto-planilha :produto="produto" />
            <!--<s-planilha-->
            <!--v-if="Object.keys(planilha).length > 0"-->
            <!--:array-planilha="planilha"-->
            <!--:agrupamentos="agrupamentos"-->
            <!--:totais="totaisPlanilha"-->
            <!--&gt;-->
            <!--<template-->
            <!--slot="badge"-->
            <!--slot-scope="slotProps"-->
            <!--&gt;-->
            <!--<v-chip-->
            <!--outline="outline"-->
            <!--label="label"-->
            <!--color="#565555"-->
            <!--&gt;-->
            <!--R$ {{ slotProps.planilha.vlSugeridoParecerista | formatarParaReal }}-->
            <!--</v-chip>-->
            <!--</template>-->
            <!--<template slot-scope="slotProps">-->
            <!--<s-analise-outros-produtos-planilha-itens-visualizar :table="slotProps.itens" />-->
            <!--</template>-->
            <!--</s-planilha>-->
            <!--<s-carregando-->
                <!--v-else-->
                <!--text="Carregando planilha"-->
            <!--/>-->
        </v-expansion-panel-content>
    </v-expansion-panel>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import SPlanilha from '@/components/Planilha/Planilha';
import SAnaliseOutrosProdutosPlanilhaItensVisualizar from './AnaliseOutrosProdutosPlanilhaItensVisualizar';
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
        },
    },
};
</script>
