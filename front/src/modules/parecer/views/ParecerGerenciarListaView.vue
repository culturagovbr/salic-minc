<template>
    <v-container
        fluid
    >
        <v-layout
            row
            wrap
        >
            <v-flex
                xs12
                md12
            >
                <v-card
                    class="mb-2"
                >
                    <v-card-title
                        primary
                        class="title"
                    >
                        Produtos {{ filtroAtual.label.toLowerCase() }}
                        <v-spacer />
                    </v-card-title>
                    <v-divider />
                    <v-card-text>
                        <v-layout
                            justify-space-between
                            class="mb-2"
                        >
                            <v-flex
                                xs12
                                sm4
                            >
                                <v-flex
                                    xs12
                                    sm6
                                    d-flex
                                >
                                    <v-select
                                        v-model="filtro"
                                        :items="filtros"
                                        label="Filtrar por situação"
                                        item-text="label"
                                        item-value="id"
                                    />
                                </v-flex>
                            </v-flex>
                            <v-flex
                                xs12
                                sm4
                            >
                                <v-text-field
                                    v-model="search"
                                    append-icon="search"
                                    label="Buscar"
                                    single-line
                                    hide-details
                                />
                            </v-flex>
                        </v-layout>
                        <v-data-table
                            v-if="!loading"
                            :headers="headers"
                            :items="produtos"
                            :rows-per-page-items="[15, 35, 50, {'text': 'Todos', value: -1}]"
                            :search="search"
                            item-key="idDistribuirParecer"
                            class="elevation-1"
                            disable-initial-sort
                        >
                            <v-progress-linear
                                v-slot:progress
                                color="blue"
                                indeterminate
                            />
                            <template
                                slot="items"
                                slot-scope="props"
                            >
                                <component
                                    :is="filtroAtual.component"
                                    :item="props.item"
                                    @visualizar-produto="visualizarProduto($event)"
                                    @visualizar-historico="visualizarHistorico($event)"
                                    @visualizar-diligencia="visualizarDiligencia($event)"
                                    @distribuir-produto="distribuirProduto($event)"
                                    @distribuir-projeto="distribuirProjeto($event)"
                                />
                            </template>

                            <template slot="no-data">
                                <div class="text-xs-center">
                                    {{ `Sem produtos ${filtroAtual.label.toLowerCase()} para análise` }}
                                </div>
                            </template>
                        </v-data-table>
                        <s-carregando
                            v-else
                            :text="`Carregando lista de produtos ${filtroAtual.label.toLowerCase()}`"
                        />
                        <s-dialog-diligencias
                            v-model="dialogDiligencias"
                            :id-pronac="diligenciaVisualizacao.idPronac"
                            :id-produto="diligenciaVisualizacao.idProduto"
                            :tp-diligencia="TP_DILIGENCIA_ANALISE_TECNICA"
                        />
                        <s-analise-declarar-impedimento-dialog
                            v-model="dialogImpedimento"
                            :produto="produtoImpedimento"
                        />
                        <s-analise-outros-produtos-dialog-detalhamento
                            v-model="dialogVisualizarProduto"
                            :produto="produtoSelecionado"
                        />
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import MxUtils from '@/mixins/utils';
import MxDiligencia from '@/modules/diligencia/mixins/diligencia';
import MxConstantes from '@/modules/parecer/mixins/const';
import SCarregando from '@/components/CarregandoVuetify';
import SDialogDiligencias from '@/modules/diligencia/components/SDialogDiligencias';
import SAnaliseDeclararImpedimentoDialog from '@/modules/parecer/components/AnaliseDeclararImpedimentoDialog';
import GerenciarListaAguardandoAnalise from '@/modules/parecer/components/GerenciarListaAguardandoAnalise';
import SAnaliseOutrosProdutosDialogDetalhamento from '@/modules/parecer/components/AnaliseOutrosProdutosDialogDetalhamento';

export default {
    name: 'ParecerAnalisarListaView',
    components: {
        SAnaliseOutrosProdutosDialogDetalhamento,
        GerenciarListaAguardandoAnalise,
        SAnaliseDeclararImpedimentoDialog,
        SCarregando,
        SDialogDiligencias,
    },
    mixins: [MxUtils, MxDiligencia, MxConstantes],
    data: () => ({
        headers: [
            {
                text: 'Pronac',
                value: 'pronac',
                width: '1',
            },
            {
                text: 'Nome do Projeto',
                align: 'left',
                value: 'nomeProjeto',
            },
            {
                text: 'Produto para análise',
                align: 'left',
                value: 'dsProduto',
            },
            {
                text: 'Tipo',
                value: 'stPrincipal',
                width: '2',
            },
            { text: 'Dt. de Recebimento', value: 'dtDistribuicao', width: '2' },
            { text: 'Diligência', width: '2', value: 'stDiligencia' },
            {
                text: 'Ações', align: 'left', value: 'siAnalise',
            },
        ],
        filtros: [
            {
                id: 'aguardando_distribuicao',
                label: 'Aguardando distribuição',
                component: 'gerenciar-lista-aguardando-analise',

            },
            {
                id: 'em_analise',
                label: 'Em análise',
                component: 'gerenciar-lista-aguardando-analise',

            },
            {
                id: 'em_validacao',
                label: 'Em validação',
                component: 'gerenciar-lista-aguardando-analise',
            },
            {
                id: 'validados',
                label: 'Validados',
                component: 'gerenciar-lista-aguardando-analise',
            },
            {
                id: 'devolvida',
                label: 'Devolvidos',
                component: 'gerenciar-lista-aguardando-analise',
            },
            {
                id: 'impedimento_parecerista',
                label: 'Com impedimento do parecerista',
                component: 'gerenciar-lista-aguardando-analise',
            },
        ],
        filtro: 'aguardando_distribuicao',
        search: '',
        dialogDiligencias: false,
        dialogHistorico: false,
        dialogVisualizarProduto: false,
        produtoHistorico: {},
        dialogImpedimento: false,
        diligenciaVisualizacao: {
            idPronac: 0,
            idProduto: 0,
        },
        produtoImpedimento: {},
        produtos: [],
        produtoSelecionado: {},
        expand: false,
        loading: true,
    }),
    computed: {
        ...mapGetters({
            obterProdutos: 'parecer/getProdutos',
        }),
        filtroAtual() {
            return this.filtros.find(item => item.id === this.filtro);
        },
    },
    watch: {
        obterProdutos(val) {
            this.produtos = val;
            this.loading = false;
        },
        filtro(v) {
            this.loading = true;
            this.obterProdutosParaGerenciar({ filtro: v });
            if (this.$route.params.filtro !== v) {
                this.$router.push({ name: 'parecer-gerenciar-listar-view', params: { filtro: v } });
            }
        },
    },
    created() {
        if (this.$route.params.filtro) {
            this.filtro = this.$route.params.filtro;
        }
        this.obterProdutosParaGerenciar({ filtro: this.filtro });
    },
    methods: {
        ...mapActions({
            obterProdutosParaGerenciar: 'parecer/obterProdutosParaGerenciar',
        }),
        visualizarDiligencia(produto) {
            this.dialogDiligencias = true;
            this.diligenciaVisualizacao = {
                idPronac: produto.idPronac,
                idProduto: produto.idProduto,
            };
        },
        visualizarProduto(produto) {
            this.dialogVisualizarProduto = true;
            this.produtoSelecionado = produto;
        },
        visualizarHistorico(produto) {
            this.dialogHistorico = true;
            this.produtoSelecionado = produto;
        },
        distribuirProduto(produto) {
            this.dialogVisualizarProduto = true;
            this.produtoSelecionado = produto;
        },
        distribuirProjeto(produto) {
            this.dialogVisualizarProduto = true;
            this.produtoSelecionado = produto;
        },
    },
};
</script>
