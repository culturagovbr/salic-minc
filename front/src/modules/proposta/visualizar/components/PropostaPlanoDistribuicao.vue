<template>
    <div
        v-if="produtos"
        class="plano-distribuicao"
    >
        <v-card>
            <v-card-title
                primary
                class="title"
            >
                Produtos, distribuição e detalhamentos
            </v-card-title>
            <v-card-text>
                <div
                    v-if="produtos.length <= 0"
                    class="padding10"
                >
                    <b>Aguarde! Carregando....</b>
                </div>
                <v-expansion-panel
                    :value="[1]"
                    expand
                >
                    <v-expansion-panel-content
                        v-for="(produto, index) of produtos"
                        :key="index"
                    >
                        <v-layout
                            slot="header"
                            style="color: green;"
                        >
                            <i class="material-icons">perm_media</i>
                            <span class="ml-2 mt-1">{{ produto.Produto }}</span>
                        </v-layout>

                        <v-container
                            fluid
                            grid-list-lg
                        >
                            <v-layout
                                row
                                wrap
                            >
                                <v-flex
                                    xs12
                                >
                                    <h3>Produto</h3>
                                    <v-divider />
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Área</b>
                                    <div>{{ produto.DescricaoArea }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Segmento</b>
                                    <div>{{ produto.DescricaoSegmento }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Principal</b>
                                    <div>{{ produto.stPrincipal | formatarLabelSimOuNao }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Canal aberto?</b>
                                    <div>{{ produto.canalAberto | formatarLabelSimOuNao }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                >
                                    <h3>Quantidade Distribuição Gratuita</h3>
                                    <v-divider />
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Divulgação</b>
                                    <div>{{ produto.QtdeProponente }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Patrocinador</b>
                                    <div>{{ produto.QtdePatrocinador }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>População</b>
                                    <div>{{ produto.QtdeOutros }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                >
                                    <h3>Pre&ccedil;o Popular</h3>
                                    <v-divider />
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Quantidade Inteira</b>
                                    <div>{{ produto.QtdeVendaPopularNormal }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Quantidade Meia</b>
                                    <div>{{ produto.QtdeVendaPopularPromocional }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Valor médio</b>
                                    <div>{{ produto.ReceitaPopularNormal }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                >
                                    <h3>Proponente</h3>
                                    <v-divider />
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Quantidade Inteira</b>
                                    <div>{{ produto.QtdeVendaNormal }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Quantidade Meia</b>
                                    <div>{{ produto.QtdeVendaPromocional }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Valor médio</b>
                                    <div>{{ produto.PrecoUnitarioNormal }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                >
                                    <h3>Consolidação</h3>
                                    <v-divider />
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Quantidade Total</b>
                                    <div>{{ produto.QtdeProduzida }}</div>
                                </v-flex>
                                <v-flex
                                    xs12
                                    sm3
                                    md3
                                >
                                    <b>Receita Prevista Total</b>
                                    <div>{{ produto.Receita }}</div>
                                </v-flex>
                            </v-layout>
                        </v-container>
                        <PropostaDetalhamentoPlanoDistribuicao
                            :array-detalhamentos="detalhamentosByID(
                                detalhamentos,
                                produto.idPlanoDistribuicao
                            )"
                        />
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
import PropostaDetalhamentoPlanoDistribuicao from './PropostaDetalhamentoPlanoDistribuicao';
import { utils } from '@/mixins/utils';

export default {
    name: 'PropostaPlanoDistribuicao',
    components: {
        PropostaDetalhamentoPlanoDistribuicao,
    },
    mixins: [utils],
    props: {
        arrayProdutos: {
            type: Array,
            default: () => [],
        },
        arrayDetalhamentos: {
            type: Array,
            default: () => [],
        },
        proposta: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            produtos: [],
            detalhamentos: [],
            active: false,
            icon: 'add',
            radio: 'n',
        };
    },
    watch: {
        proposta(v) {
            this.produtos = v.planodistribuicaoproduto;
            this.detalhamentos = v.tbdetalhaplanodistribuicao;
        },
        arrayProdutos(value) {
            this.produtos = value;
        },
        arrayDetalhamentos(value) {
            this.detalhamentos = value;
        },
    },
    mounted() {
        if (typeof this.arrayProdutos !== 'undefined') {
            this.produtos = this.arrayProdutos;
        }

        if (typeof this.arrayDetalhamentos !== 'undefined') {
            this.detalhamentos = this.arrayDetalhamentos;
        }
    },
    methods: {
        detalhamentosByID(lista, id) {
            if (typeof lista !== 'undefined') {
                /* eslint-disable */
                    let novaLista = [];

                    Object.keys(lista).map((key) => {
                        if (lista[key].idPlanoDistribuicao === id) {
                            novaLista.push(lista[key]);
                        }
                        return novaLista;
                    });
                    return novaLista;
                }
                return lista;
            },
        },
    };
</script>
