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
                        Produtos para gerenciamento
                        <v-spacer />
                        <v-text-field
                            v-model="search"
                            append-icon="search"
                            label="Buscar"
                            single-line
                            hide-details
                        />
                    </v-card-title>
                    <v-divider />
                    <v-card-text>
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
                            <template
                                slot="items"
                                slot-scope="props"
                            >
                                <gerenciar-lista-aguardando-analise
                                    :item="props.item"
                                    @visualizar-produto="visualizarProduto($event)"
                                />
                            </template>

                            <template slot="no-data">
                                <div class="text-xs-center">
                                    Sem produtos para análise
                                </div>
                            </template>
                        </v-data-table>
                        <s-carregando
                            v-else
                            text="Carregando lista de produtos"
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
        items: ['Foo', 'Bar', 'Fizz', 'Buzz'],
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
    },
    watch: {
        obterProdutos(val) {
            this.produtos = val;
            this.loading = false;
        },
    },
    created() {
        this.obterProdutosParaGerenciar(
            {
                filtro: 'aguardando-analise',
            },
        );
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
        declararImpedimento(item) {
            this.dialogImpedimento = true;
            this.produtoImpedimento = item;
        },

    },
};
</script>
