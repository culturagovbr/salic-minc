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
                                <tr :class="mxObterClasseItem(props.item)">
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
                                        <v-tooltip
                                            v-if="props.item.tipoAnalise === 1"
                                            bottom
                                        >
                                            <v-badge
                                                slot="activator"
                                                right
                                                color="orange darken-4"
                                            >
                                                <span slot="badge">
                                                    <v-icon dark>attach_money</v-icon>
                                                </span>
                                                {{ props.item.dsProduto }}
                                            </v-badge>
                                            <span>Solicitado análise financeira complementar</span>
                                        </v-tooltip>
                                        <div v-else>
                                            {{ props.item.dsProduto }}
                                        </div>
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
                                    <td class="text-xs-right">
                                        {{ props.item.DtDistribuicao | formatarData }}
                                    </td>
                                    <td class="layout px-0">
                                        <v-tooltip
                                            v-if="props.item.siAnalise === SI_ANALISE_ANALISADO
                                                && props.item.stPrincipal !== 1"
                                            bottom
                                        >
                                            <v-btn
                                                slot="activator"
                                                color="grey lighten-3"
                                                icon
                                                class="mr-2"
                                                disabled
                                            >
                                                <v-icon color="blue-grey darken-2">
                                                    watch_later
                                                </v-icon>
                                            </v-btn>
                                            <span>
                                                Análise finalizada.
                                                Este produto é secundário e será enviado após finalização do produto principal.
                                            </span>
                                        </v-tooltip>
                                        <v-tooltip
                                            v-else
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
                                                :color="obterConfigsBotaoPrincipal(props.item).cor"
                                                icon
                                                class="mr-2"
                                            >
                                                <v-icon :color="obterConfigsBotaoPrincipal(props.item).corIcone">
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
                                                color="yellow accent-4"
                                                icon
                                                class="mr-2"
                                                @click="declararImpedimento(props.item)"
                                            >
                                                <v-icon color="yellow darken-4">
                                                    voice_over_off
                                                </v-icon>
                                            </v-btn>
                                            <span>Declarar impedimento para análise deste produto</span>
                                        </v-tooltip>
                                    </td>
                                </tr>
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
                            @diligencia-criada="obterProdutosParaAnalise()"
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

/** mixins */
import MxUtils from '@/mixins/utils';
import MxUtilsParecer from '@/modules/parecer/mixins/UtilsParecer';
import MxDiligencia from '@/modules/diligencia/mixins/diligencia';
import MxConstantes from '@/modules/parecer/mixins/Constantes';

import SCarregando from '@/components/CarregandoVuetify';
import SDialogDiligencias from '@/modules/diligencia/components/SDialogDiligencias';
import SAnaliseDeclararImpedimentoDialog from '@/modules/parecer/components/analisar/AnaliseDeclararImpedimentoDialog';

export default {
    name: 'ParecerListarView',
    components: {
        SAnaliseDeclararImpedimentoDialog, SCarregando, SDialogDiligencias,
    },
    mixins: [MxUtils, MxDiligencia, MxConstantes, MxUtilsParecer],

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
            {
                text: 'Diligência',
                width: '2',
                value: 'stDiligencia',
            },
            {
                text: 'Dt. de Recebimento',
                value: 'dtDistribuicao',
                width: '2',
            },
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
                    icone: 'gavel',
                    texto: 'Continuar analisando',
                    cor: 'green lighten-3',
                    corIcone: 'green darken-4',
                };
            case 2:
                return {
                    icone: 'edit',
                    texto: 'Assinar parecer e finalizar',
                    cor: 'grey lighten-3',
                    corIcone: 'blue-grey darken-2',
                };
            default:
                return {
                    icone: 'gavel',
                    texto: 'Analisar produto',
                    cor: 'grey lighten-3',
                    corIcone: 'blue-grey darken-2',
                };
            }
        },
        obterLabelTooltipProduto(produto) {
            if (produto.tipoAnalise === 1) {
                return 'Solicitado análise financeira complementar';
            }
            return `Clique para analisar o produto ${produto.dsProduto}`;
        },
    },
};
</script>
