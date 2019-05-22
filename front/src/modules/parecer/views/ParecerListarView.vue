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
                                            :href="`/projeto/#/${props.item.IdPRONAC}`"
                                            target="_blank"
                                            class="mr-2"
                                        >
                                            {{ props.item.PRONAC }}
                                        </a>
                                        <span>Consultar projeto {{ props.item.NomeProjeto }}</span>
                                    </v-tooltip>
                                </td>
                                <td>{{ props.item.NomeProjeto }}</td>
                                <td>
                                    <v-tooltip bottom>
                                        <router-link
                                            slot="activator"
                                            :to="{
                                                name: 'analise-conteudo',
                                                params: {
                                                    id: props.item.idProduto,
                                                    idPronac: props.item.IdPRONAC,
                                                    produtoPrincipal: props.item.stPrincipal,
                                                }
                                            }"
                                            class="subheading font-weight-medium"
                                            color="primary"
                                        >
                                            {{ props.item.dsProduto }}
                                        </router-link>
                                        <span>Clique para análisar o produto {{ props.item.dsProduto }}</span>
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
                                <td class="justify-center layout px-0">
                                    <v-tooltip
                                        bottom
                                    >
                                        <v-btn
                                            slot="activator"
                                            color="blue-grey darken-2"
                                            flat
                                            icon
                                            class="mr-2"
                                            @click="visualizarHistorico(props.item)"
                                        >
                                            <v-icon>
                                                history
                                            </v-icon>
                                        </v-btn>
                                        <span>Visualizar histórico de distribuição deste produto</span>
                                    </v-tooltip>
                                    <v-tooltip
                                        bottom
                                    >
                                        <v-btn
                                            slot="activator"
                                            @click="declararImpedimento(props.item)"
                                            color="blue-grey darken-2"
                                            flat
                                            icon
                                            class="mr-2"
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
                            :id-pronac="diligenciaVisualizacao.IdPRONAC"
                            :id-produto="diligenciaVisualizacao.idProduto"
                            :tp-diligencia="tipoDiligencia"
                        />
                        <s-analise-historico-produto-dialog
                            v-model="dialogHistorico"
                            :produto="produtoHistorico"
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
import { utils } from '@/mixins/utils';
import MxDiligencia from '@/modules/diligencia/mixins/diligencia';
import MxParecer from '../mixins/utilsParecer';
import SCarregando from '@/components/CarregandoVuetify';
import SDialogDiligencias from '@/modules/diligencia/components/SDialogDiligencias';
import SAnaliseHistoricoProdutoDialog from '@/modules/parecer/components/AnaliseHistoricoProdutoDialog';
import SAnaliseDeclararImpedimentoDialog from '@/modules/parecer/components/AnaliseDeclararImpedimentoDialog';

const TP_DILIGENCIA = 124;

export default {
    name: 'ParecerListarView',
    components: {
        SAnaliseDeclararImpedimentoDialog, SAnaliseHistoricoProdutoDialog, SCarregando, SDialogDiligencias,
    },
    mixins: [utils, MxDiligencia, MxParecer],
    data: () => ({
        headers: [
            {
                text: 'Pronac',
                value: 'PRONAC',
                width: '1',
            },
            {
                text: 'Nome do Projeto',
                align: 'left',
                value: 'NomeProjeto',
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
            { text: 'Dt. de Recebimento', value: 'DtDistribuicao', width: '2' },
            { text: 'Diligência', width: '2', value: 'stDiligencia' },
            {
                text: 'Ações', align: 'center', value: 'idProduto', sortable: false,
            },
        ],
        search: '',
        dialogDiligencias: false,
        dialogHistorico: false,
        dialogImpedimento: false,
        diligenciaVisualizacao: {
            IdPRONAC: 0,
            idProduto: 0,
        },
        produtoHistorico: {},
        produtoImpedimento: {},
        tipoDiligencia: TP_DILIGENCIA,
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
                IdPRONAC: item.IdPRONAC,
                idProduto: item.idProduto,
            };
        },
        visualizarHistorico(item) {
            this.dialogHistorico = true;
            this.produtoHistorico = item;
        },
        declararImpedimento(item) {
            this.dialogImpedimento = true;
            this.produtoImpedimento = item;
        },
    },

};
</script>
