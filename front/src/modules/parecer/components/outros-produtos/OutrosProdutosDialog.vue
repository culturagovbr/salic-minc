<template>
    <v-dialog
        v-model="dialog"
        fullscreen
        hide-overlay
        transition="dialog-bottom-transition"
        scrollable
    >
        <v-card
            tile
        >
            <v-toolbar
                card
                dark
                color="primary"
            >
                <v-btn
                    icon
                    dark
                    @click="dialog = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>
                    Outros produtos do projeto: {{ produto.pronac }} - {{ produto.nomeProjeto }}
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <s-carregando
                    v-if="loading"
                    text="Carregando outros produtos do projeto"
                />
                <div v-else>
                    <v-data-table
                        :headers="headers"
                        :items="produtosSecundarios"
                        disable-initial-sort
                        class="elevation-0"
                        item-key="idDistribuirParecer"
                    >
                        <template
                            slot="items"
                            slot-scope="props"
                        >
                            <td>
                                <v-tooltip
                                    v-if="props.item.idAgenteParecerista === produto.idAgenteParecerista"
                                    bottom
                                >
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
                                        color="primary"
                                    >
                                        {{ props.item.nomeProduto }}
                                    </router-link>
                                    <span>Clique para análisar o produto {{ props.item.nomeProduto }}</span>
                                </v-tooltip>
                                <span
                                    v-else
                                    v-text="props.item.nomeProduto"
                                />
                            </td>
                            <td>
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
                            <td>{{ props.item.dtDistribuicao | formatarData }}</td>
                            <td v-html="obterSituacao(props.item)" />
                            <td class="justify-center layout px-0">
                                <v-tooltip
                                    bottom
                                >
                                    <v-btn
                                        slot="activator"
                                        flat
                                        icon
                                        class="mr-2"
                                        @click="abrirModal(props.item)"
                                    >
                                        <v-icon>
                                            visibility
                                        </v-icon>
                                    </v-btn>
                                    <span>Visualizar dados deste produto</span>
                                </v-tooltip>
                            </td>
                        </template>
                    </v-data-table>
                    <s-analise-outros-produtos-dialog-detalhamento
                        v-model="dialogDetalhamento"
                        :produto="produtoVisualizacao"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import SCarregando from '@/components/CarregandoVuetify';
import SAnaliseOutrosProdutosDialogDetalhamento from './OutrosProdutosDialogDetalhamento';
import MxUtils from '@/mixins/utils';
import MxUtilsParecer from '../../mixins/UtilsParecer';

export default {
    name: 'OutrosProdutosDialog',
    components: {
        SAnaliseOutrosProdutosDialogDetalhamento, SCarregando,
    },
    mixins: [MxUtils, MxUtilsParecer],

    props: {
        value: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            dialog: false,
            dialogDetalhamento: false,
            loading: true,
            headers: [
                { text: 'Produto', value: 'nomeProduto' },
                { text: 'Tipo', value: 'stPrincipal' },
                { text: 'Dt. Distribuição', value: 'dtDistribuicao' },
                { text: 'Situação', value: 'siAnalise' },
                {
                    text: 'Ações', value: 'idDistribuirParecer', align: 'center', sortable: false,
                },
            ],
            produtoVisualizacao: {
                type: Object,
                default: () => {},
            },
        };
    },

    mounted() {
        this.dialog = this.value;
    },

    computed: {
        ...mapGetters({
            produtosSecundarios: 'parecer/getProdutosSecundarios',
            produto: 'parecer/getProduto',
        }),
    },

    watch: {
        value(val) {
            this.dialog = val;
        },
        dialog(val) {
            if (val) {
                this.obterProdutosSecundarios({
                    id: this.$route.params.id,
                    idPronac: this.$route.params.idPronac,
                });
            }
            this.$emit('input', val);
        },
        produtosSecundarios(val) {
            this.loading = val.length === 0;
        },
    },

    methods: {
        ...mapActions({
            obterProdutosSecundarios: 'parecer/obterProdutosSecundarios',
        }),
        abrirModal(produto) {
            this.produtoVisualizacao = produto;
            this.dialogDetalhamento = true;
        },
    },
};
</script>
