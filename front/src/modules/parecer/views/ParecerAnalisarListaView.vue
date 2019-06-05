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
                        Produtos para an&aacute;lise inicial
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
                                <td>
                                    <v-tooltip
                                        bottom
                                    >
                                        <a
                                            slot="activator"
                                            :href="`/projeto/#/${props.item.idPronac}`"
                                            target="_blank"
                                            class="mr-2"
                                        >
                                            {{ props.item.pronac }}
                                        </a>
                                        <span>Consultar projeto {{ props.item.nomeProjeto }}</span>
                                    </v-tooltip>
                                </td>
                                <td>{{ props.item.nomeProjeto }}</td>
                                <td>
                                    <v-tooltip bottom>
                                        <router-link
                                            slot="activator"
                                            :to="{
                                                name: 'analise-conteudo',
                                                params: {
                                                    id: props.item.idProduto,
                                                    idPronac: props.item.idPronac,
                                                    produtoPrincipal: props.item.stPrincipal,
                                                }
                                            }"
                                            class="subheading font-weight-medium"
                                            color="primary"
                                        >
                                            {{ props.item.dsProduto }}
                                        </router-link>
                                        <span>Clique para analisar o produto {{ props.item.dsProduto }}</span>
                                    </v-tooltip>
                                </td>
                                <td class="text-xs-center">
                                    <v-tooltip
                                        v-if="props.item.stPrincipal === 1"
                                        bottom
                                    >
                                        <v-icon
                                            slot="activator"
                                            round
                                        >
                                            looks_one
                                        </v-icon>
                                        <span>Produto principal</span>
                                    </v-tooltip>
                                    <v-tooltip
                                        v-else
                                        bottom
                                    >
                                        <v-icon
                                            slot="activator"
                                            color="grey"
                                        >
                                            looks_two
                                        </v-icon>
                                        <span>Produto secundário</span>
                                    </v-tooltip>
                                </td>
                                <td class="text-xs-right">
                                    {{ props.item.DtDistribuicao | formatarData }}
                                </td>
                                <td class="text-xs-center">
                                    <v-tooltip
                                        bottom
                                    >
                                        <v-btn
                                            slot="activator"
                                            :color="obterConfigDiligencia(props.item).cor"
                                            target="_blank"
                                            icon
                                            small
                                            @click="visualizarDiligencia(props.item)"
                                        >
                                            <v-badge
                                                :value="props.item.diasEmDiligencia > 0"
                                                color="grey lighten-1"
                                                overlap
                                                left
                                            >
                                                <span slot="badge">{{ props.item.diasEmDiligencia }}</span>
                                                <v-icon
                                                    :color="obterConfigDiligencia(props.item).corIcone"
                                                >
                                                    notification_important
                                                </v-icon>
                                            </v-badge>
                                        </v-btn>
                                        <span> {{ obterConfigDiligencia(props.item).texto }} </span>
                                    </v-tooltip>
                                </td>
                                <td class="layout px-0">
                                    <v-tooltip
                                        bottom
                                    >
                                        <v-btn
                                            slot="activator"
                                            :to="{
                                                name: 'analise-conteudo',
                                                params: {
                                                    id: props.item.idProduto,
                                                    idPronac: props.item.idPronac,
                                                    produtoPrincipal: props.item.stPrincipal,
                                                }
                                            }"
                                            color="primary"
                                            flat
                                            icon
                                            class="mr-2"
                                        >
                                            <v-icon>
                                                {{ obterConfigsBotaoPrincipal(props.item).icone }}
                                            </v-icon>
                                        </v-btn>
                                        <span>{{ obterConfigsBotaoPrincipal(props.item).texto }}</span>
                                    </v-tooltip>
                                    <v-tooltip
                                        bottom
                                    >
                                        <v-btn
                                            v-if="props.item.siAnalise === SI_ANALISE_AGUARDANDO_ANALISE
                                                || !props.item.siAnalise"
                                            slot="activator"
                                            color="blue-grey darken-2"
                                            flat
                                            icon
                                            class="mr-2"
                                            @click="declararImpedimento(props.item)"
                                        >
                                            <v-icon>
                                                voice_over_off
                                            </v-icon>
                                        </v-btn>
                                        <span>Declarar impedimento para análise deste produto</span>
                                    </v-tooltip>
                                </td>
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

export default {
    name: 'ParecerListarView',
    components: {
        SAnaliseDeclararImpedimentoDialog, SCarregando, SDialogDiligencias,
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
        produtoHistorico: {},
        dialogImpedimento: false,
        diligenciaVisualizacao: {
            idPronac: 0,
            idProduto: 0,
        },
        produtoImpedimento: {},
        produtos: [],
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
        this.obterProdutosParaAnalise();
    },
    methods: {
        ...mapActions({
            obterProdutosParaAnalise: 'parecer/obterProdutosParaAnalise',
        }),
        visualizarDiligencia(item) {
            this.dialogDiligencias = true;
            this.diligenciaVisualizacao = {
                idPronac: item.idPronac,
                idProduto: item.idProduto,
            };
        },
        declararImpedimento(item) {
            this.dialogImpedimento = true;
            this.produtoImpedimento = item;
        },
        obterConfigsBotaoPrincipal(produto) {
            switch (produto.siAnalise) {
            case 1:
                return {
                    icone: 'border_color',
                    texto: 'Continuar analisando',
                };
            case 2:
                return {
                    icone: 'assignment',
                    texto: 'Assinar parecer e finalizar',
                };
            default:
                return {
                    icone: 'edit',
                    texto: 'Analisar produto',
                };
            }
        },
    },
};
</script>
