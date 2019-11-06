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

                        <!-- paineis -->
                        <component
                            v-if="!loading"
                            :is="filtroSelecionado.component"
                            :produtos="produtos"
                            :search="search"
                            @visualizar-historico="abrirDialog($event, 's-analise-historico-produto-dialog')"
                            @distribuir-produto="abrirDialog($event, 's-gerenciar-distribuir-produto-dialog')"
                            @visualizar-detalhes="abrirDialog($event, 's-gerenciar-detalhes-analise-dialog')"
                            @reanalisar-produto="abrirDialog($event, 's-gerenciar-reanalisar-produto-dialog')"
                            @devolver-produto-para-secult="abrirDialog($event, 's-gerenciar-devolver-para-secult-dialog')"
                            @solicitar-analise-complementar="abrirDialog($event, 's-gerenciar-solicitar-analise-complementar-dialog')"
                        />
                        <s-carregando
                            v-else
                            :text="`Carregando lista de produtos ${filtroSelecionado.label.toLowerCase()}`"
                        />

                        <!-- dialogs -->
                        <component
                            :is="modal.component"
                            v-model="modal.show"
                            :produto="produtoSelecionado"
                            :filtro="filtro"
                            @recarregar-lista="recarregarLista()"
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
import SCarregando from '@/components/CarregandoVuetify';

import SAnaliseHistoricoProdutoDialog from '@/modules/parecer/components/HistoricoProdutoDialog';

// dialogs de gerenciar
import SGerenciarDistribuirProdutoDialog from '@/modules/parecer/components/gerenciar-dialogs/GerenciarDistribuirProdutoDialog';
import SGerenciarReanalisarProdutoDialog from '@/modules/parecer/components/gerenciar-dialogs/GerenciarReanalisarProdutoDialog';
import SGerenciarDetalhesAnaliseDialog from '@/modules/parecer/components/gerenciar-dialogs/GerenciarDetalhesAnaliseDialog';
import SGerenciarDevolverParaSecultDialog from '@/modules/parecer/components/gerenciar-dialogs/GerenciarDevolverParaSecultDialog';
import SGerenciarSolicitarAnaliseComplementarDialog from '@/modules/parecer/components/gerenciar-dialogs/GerenciarSolicitarAnaliseComplementarDialog';

// paineis
import GerenciarListaAguardandoDistribuicao from '@/modules/parecer/components/gerenciar-paineis/GerenciarListaAguardandoDistribuicao';
import GerenciarListaItensEmAnalise from '@/modules/parecer/components/gerenciar-paineis/GerenciarListaItensEmAnalise';
import GerenciarListaDevolvidasSecult from '@/modules/parecer/components/gerenciar-paineis/GerenciarListaDevolvidasSecult';
import GerenciarListaValidados from '@/modules/parecer/components/gerenciar-paineis/GerenciarListaValidados';
import GerenciarListaEmValidacao from '@/modules/parecer/components/gerenciar-paineis/GerenciarListaEmValidacao';

export default {
    name: 'ParecerAnalisarListaView',
    components: {
        SAnaliseHistoricoProdutoDialog,
        SGerenciarDistribuirProdutoDialog,
        SGerenciarReanalisarProdutoDialog,
        SGerenciarDevolverParaSecultDialog,
        SGerenciarSolicitarAnaliseComplementarDialog,
        GerenciarListaAguardandoDistribuicao,
        GerenciarListaDevolvidasSecult,
        GerenciarListaItensEmAnalise,
        GerenciarListaEmValidacao,
        GerenciarListaValidados,
        SGerenciarDetalhesAnaliseDialog,
        SCarregando,
    },
    mixins: [MxUtils],

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
                label: 'Devolvidas (SECULT)',
                component: 'gerenciar-lista-devolvidas-secult',
            },
            {
                id: 'enviada_vinculada',
                label: 'Enviadas para análise compartilhada (outra unidade)',
                component: 'gerenciar-lista-itens-em-analise',
            },
            {
                id: 'devolucao_vinculada',
                label: 'Devolvidas análise compartilhada (outra unidade)',
                component: 'gerenciar-lista-em-validacao',
            },
            {
                id: 'impedimento_parecerista',
                label: 'Com impedimento do parecerista',
                component: 'gerenciar-lista-itens-em-analise',
            },
        ],
        filtroPadrao: 'aguardando_distribuicao',
        filtro: '',
        search: '',
        modal: {
            show: false,
            component: '',
        },
        produtoImpedimento: {},
        produtoSelecionado: {},
        expand: false,
        loading: true,
        urlAssinatura: '/assinatura/index/visualizar-projeto',
    }),

    computed: {
        ...mapGetters({
            produtos: 'parecer/getProdutos',
        }),
        filtroSelecionado() {
            return this.filtros.find(item => item.id === this.filtro);
        },
    },

    watch: {
        filtro(v) {
            this.obterProdutosParaGerenciar({ filtro: v });
            this.$router.push({ name: 'parecer-gerenciar-listar-view', params: { filtro: v } });
        },
        $route(prev, old) {
            if (prev.params.filtro !== old.params.filtro) {
                this.filtro = prev.params.filtro || 'aguardando_distribuicao';
            }
        },
    },

    created() {
        if (this.$route.params.filtro) {
            this.filtro = this.$route.params.filtro;
        } else {
            this.filtro = this.filtroPadrao;
        }
    },

    methods: {
        ...mapActions({
            obterProdutosParaGerenciarAction: 'parecer/obterProdutosParaGerenciar',
        }),
        abrirDialog(produto, component) {
            this.produtoSelecionado = produto;
            this.modal.component = component;
            this.modal.show = true;
        },
        obterProdutosParaGerenciar(params) {
            this.loading = true;
            this.obterProdutosParaGerenciarAction(params)
                .finally(() => {
                    this.loading = false;
                });
        },
        recarregarLista() {
            this.obterProdutosParaGerenciar({ filtro: this.filtro });
        },
    },
};
</script>

<style>
    .table__expand table.v-table thead th:first-child,
    .table__expand table.v-table tbody tr:not(.v-datatable__expand-row) td:first-child {
        max-width: 40px;
        text-align: center !important;
        padding: 0;
    }
</style>
