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
                        Produtos {{ filtroSelecionado.label.toLowerCase() }}
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
                            :headers="filtroSelecionado.headers"
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
                                    :is="filtroSelecionado.component"
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
                                    {{ `Sem produtos ${filtroSelecionado.label.toLowerCase()} para análise` }}
                                </div>
                            </template>
                        </v-data-table>
                        <s-carregando
                            v-else
                            :text="`Carregando lista de produtos ${filtroSelecionado.label.toLowerCase()}`"
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
                        <s-gerenciar-distribuir-produto-dialog
                            v-model="dialogDistribuirProduto"
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
import SAnaliseOutrosProdutosDialogDetalhamento from '@/modules/parecer/components/AnaliseOutrosProdutosDialogDetalhamento';
import SGerenciarDistribuirProdutoDialog from '@/modules/parecer/components/GerenciarDistribuirProdutoDialog';
import GerenciarListaItensAguardandoAnalise from '@/modules/parecer/components/GerenciarListaItensAguardandoAnalise';

export default {
    name: 'ParecerAnalisarListaView',
    components: {
        SGerenciarDistribuirProdutoDialog,
        SAnaliseOutrosProdutosDialogDetalhamento,
        GerenciarListaItensAguardandoAnalise,
        SAnaliseDeclararImpedimentoDialog,
        SCarregando,
        SDialogDiligencias,
    },
    mixins: [MxUtils, MxDiligencia, MxConstantes],
    data: () => ({
        filtros: [
            {
                id: 'aguardando_distribuicao',
                label: 'Aguardando distribuição',
                component: 'gerenciar-lista-itens-aguardando-analise',
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
                        value: 'nomeProduto',
                    },
                    {
                        text: 'Tipo',
                        value: 'stPrincipal',
                        width: '2',
                    },
                    {
                        text: 'Segmento',
                        align: 'left',
                        value: 'segmento',
                    },
                    { text: 'Dt. de Recebimento', value: 'dtEnvioMincVinculada', width: '2' },
                    {
                        text: 'Ações', align: 'center', value: 'siAnalise',
                    },
                ],
            },
            {
                id: 'em_analise',
                label: 'Em análise',
                component: 'gerenciar-lista-itens-aguardando-analise',
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
                        value: 'nomeProduto',
                    },
                    {
                        text: 'Tipo',
                        value: 'stPrincipal',
                        width: '2',
                    },
                    { text: 'Dt. de Recebimento', value: 'dtDistribuicao', width: '2' },
                    { text: 'Ações', width: '2', value: 'stPrincipal' },
                ],

            },
            {
                id: 'em_validacao',
                label: 'Em validação',
                component: 'gerenciar-lista-itens-aguardando-analise',
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
                        value: 'nomeProduto',
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
            },
            {
                id: 'validados',
                label: 'Validados',
                component: 'gerenciar-lista-itens-aguardando-analise',
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
                        value: 'nomeProduto',
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
            },
            {
                id: 'devolvida',
                label: 'Devolvidos',
                component: 'gerenciar-lista-itens-aguardando-analise',
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
                        value: 'nomeProduto',
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
            },
            {
                id: 'impedimento_parecerista',
                label: 'Com impedimento do parecerista',
                component: 'gerenciar-lista-itens-aguardando-analise',
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
                        value: 'nomeProduto',
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
            },
        ],
        filtro: 'aguardando_distribuicao',
        search: '',
        dialogDiligencias: false,
        dialogHistorico: false,
        dialogVisualizarProduto: false,
        dialogDistribuirProduto: false,
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
        filtroSelecionado() {
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
        $route(prev, old) {
            if (!!prev.params.filtro && !!old.params.filtro && prev.params.filtro !== old.params.filtro) {
                this.filtro = prev.params.filtro;
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
            this.dialogDistribuirProduto = true;
            this.produtoSelecionado = produto;
        },
        distribuirProjeto(produto) {
            this.dialogVisualizarProduto = true;
            this.produtoSelecionado = produto;
        },
    },
};
</script>
