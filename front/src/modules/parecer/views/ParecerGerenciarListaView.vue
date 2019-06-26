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
                                sm6
                                d-flex
                            >
                                <v-select
                                    v-model="filtro"
                                    :items="filtros"
                                    label="Produto por situação"
                                    item-text="label"
                                    item-value="id"
                                />
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
                        <component
                            :is="filtroSelecionado.component"
                            v-if="!loading"
                            :produtos="produtos"
                            :search="search"
                            @visualizar-produto="visualizarProduto($event)"
                            @visualizar-historico="visualizarHistorico($event)"
                            @visualizar-diligencia="visualizarDiligencia($event)"
                            @distribuir-produto="distribuirProduto($event)"
                            @distribuir-projeto="distribuirProjeto($event)"
                            @visualizar-detalhes="visualizarDetalhes($event)"
                        />
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
                        <s-analise-outros-produtos-dialog-detalhamento
                            v-model="dialogVisualizarProduto"
                            :produto="produtoSelecionado"
                        />
                        <s-gerenciar-distribuir-produto-dialog
                            v-model="dialogDistribuirProduto"
                            :produto="produtoSelecionado"
                            :filtro="filtro"
                        />
                        <s-gerenciar-detalhes-analise-dialog
                            v-model="dialogDetalhes"
                            :produto="produtoSelecionado"
                            :filtro="filtro"
                        />
                        <s-analise-historico-produto-dialog
                            v-model="dialogHistorico"
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
import SAnaliseOutrosProdutosDialogDetalhamento from '@/modules/parecer/components/AnaliseOutrosProdutosDialogDetalhamento';
import SGerenciarDistribuirProdutoDialog from '@/modules/parecer/components/GerenciarDistribuirProdutoDialog';
import SGerenciarDetalhesAnaliseDialog from '@/modules/parecer/components/GerenciarDetalhesAnaliseDialog';
import GerenciarListaAguardandoDistribuicao from '@/modules/parecer/components/GerenciarListaAguardandoDistribuicao';
import GerenciarListaItensEmAnalise from '@/modules/parecer/components/GerenciarListaItensEmAnalise';
import GerenciarListaValidados from '@/modules/parecer/components/GerenciarListaValidados';
import GerenciarListaEmValidacao from '@/modules/parecer/components/GerenciarListaEmValidacao';
import SAnaliseHistoricoProdutoDialog from '@/modules/parecer/components/AnaliseHistoricoProdutoDialog';

export default {
    name: 'ParecerAnalisarListaView',
    components: {
        SAnaliseHistoricoProdutoDialog,
        SGerenciarDistribuirProdutoDialog,
        SAnaliseOutrosProdutosDialogDetalhamento,
        GerenciarListaAguardandoDistribuicao,
        GerenciarListaItensEmAnalise,
        GerenciarListaEmValidacao,
        GerenciarListaValidados,
        SGerenciarDetalhesAnaliseDialog,
        SCarregando,
        SDialogDiligencias,
    },
    mixins: [MxUtils, MxDiligencia, MxConstantes],
    data: () => ({
        filtros: [
            {
                id: 'aguardando_distribuicao',
                label: 'Aguardando distribuição',
                component: 'gerenciar-lista-aguardando-distribuicao',
            },
            {
                id: 'em_analise',
                label: 'Em análise',
                component: 'gerenciar-lista-itens-em-analise',

            },
            {
                id: 'em_validacao',
                label: 'Em validação',
                component: 'gerenciar-lista-em-validacao',
            },
            {
                id: 'validados',
                label: 'Validados',
                component: 'gerenciar-lista-validados',
            },
            {
                id: 'devolvida',
                label: 'Devolvidos',
                component: 'gerenciar-lista-validados',
            },
            {
                id: 'impedimento_parecerista',
                label: 'Com impedimento do parecerista',
                component: 'gerenciar-lista-itens-em-analise',
            },
        ],
        filtro: 'aguardando_distribuicao',
        search: '',
        dialogDiligencias: false,
        dialogHistorico: false,
        dialogVisualizarProduto: false,
        dialogDistribuirProduto: false,
        dialogDetalhes: false,
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
        visualizarDetalhes(produto) {
            this.dialogDetalhes = true;
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
