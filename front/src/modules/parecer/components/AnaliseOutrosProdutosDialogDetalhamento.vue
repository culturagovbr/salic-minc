<template>
    <v-dialog
        v-model="dialogDetalhamento"
        row
        justify-center
        @keydown.esc="dialogDetalhamento = false"
    >
        <v-card
            tile
        >
            <v-toolbar
                card
                dark
                color="primary lighten-1"
            >
                <v-btn
                    icon
                    dark
                    @click="dialogDetalhamento = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>
                    Visualizar análise do Produto
                    <b>"{{ produto.Produto }}"</b>
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
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
                        <s-planilha
                            v-if="Object.keys(planilha).length > 0"
                            :array-planilha="planilha"
                            :agrupamentos="agrupamentos"
                            :totais="totaisPlanilha"
                        >
                            <template
                                slot="badge"
                                slot-scope="slotProps"
                            >
                                <v-chip
                                    outline="outline"
                                    label="label"
                                    color="#565555"
                                >
                                    R$ {{ slotProps.planilha.VlSugeridoParecerista | formatarParaReal }}
                                </v-chip>
                            </template>
                            <template slot-scope="slotProps">
                                <s-analise-outros-produtos-planilha-itens-visualizar :table="slotProps.itens" />
                            </template>
                        </s-planilha>
                        <s-carregando
                            v-else
                            text="Carregando planilha"
                        />
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';
import SPlanilha from '@/components/Planilha/Planilha';
import SAnaliseOutrosProdutosPlanilhaItensVisualizar from './AnaliseOutrosProdutosPlanilhaItensVisualizar';
import SCarregando from '@/components/CarregandoVuetify';

export default {
    name: 'AnaliseOutrosProdutosDialog',
    components: { SCarregando, SAnaliseOutrosProdutosPlanilhaItensVisualizar, SPlanilha },
    mixins: [utils],
    props: {
        value: {
            type: Boolean,
            default: false,
        },
        produto: {
            type: Object,
            default: () => {},
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
    methods: {
        ...mapActions({
            obterAnaliseConteudoSecundario: 'parecer/obterAnaliseConteudoSecundario',
            obterPlanilha: 'parecer/obterPlanilhaProdutoSecundario',
        }),
        visualizarDetalhesProduto(produto) {
            this.obterAnaliseConteudoSecundario({
                id: produto.idProduto,
                idPronac: produto.IdPRONAC,
            });

            this.obterPlanilha({
                id: produto.idProduto,
                idPronac: produto.IdPRONAC,
                stPrincipal: produto.stPrincipal,
            });
        },
    },
};
</script>
