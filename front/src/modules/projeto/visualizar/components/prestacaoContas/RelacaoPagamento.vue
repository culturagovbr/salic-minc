<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Relação de Pagamentos'" />
        </div>
        <div v-else-if="dados">
            <v-card>
                <v-container fluid>
                    <v-layout
                        justify-start
                        row
                        wrap
                    >
                        <Filtro
                            :items="montaArray()"
                            :label="'Pesquise Item'"
                            class="pr-5"
                            @eventoSearch="search = $event"
                        />
                    </v-layout>
                </v-container>
                <v-data-table
                    :pagination.sync="pagination"
                    :headers="headers"
                    :items="dados"
                    :search="search"
                    :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                    item-key="id"
                    class="elevation-1 container-fluid"
                >
                    <template
                        slot="items"
                        slot-scope="props"
                    >
                        <td class="text-xs-left">
                            <b>{{ props.item.Item }}</b>
                        </td>
                        <td class="text-xs-left">
                            {{ props.item.Fornecedor }}
                        </td>
                        <td class="text-xs-left">
                            {{ props.item.nrComprovante }}
                        </td>
                        <td class="text-xs-center pl-5">
                            {{ props.item.DtPagamento | formatarData }}
                        </td>
                        <td class="text-xs-center">
                            {{ props.item.nrDocumentoDePagamento }}
                        </td>
                        <td class="text-xs-right">
                            {{ props.item.vlPagamento | filtroFormatarParaReal }}
                        </td>
                        <td class="text-xs-center pr-2">
                            <v-tooltip bottom>
                                <v-btn
                                    slot="activator"
                                    flat
                                    icon
                                    @click="showItem(props.item)"
                                >
                                    <v-icon>visibility</v-icon>
                                </v-btn>
                                <span>Visualizar Pagamento</span>
                            </v-tooltip>
                        </td>
                    </template>
                </v-data-table>
            </v-card>
            <v-card>
                <v-container fluid>
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex xs6>
                            <h6 class="mr-3">
                                VALOR TOTAL
                            </h6>
                        </v-flex>
                        <v-flex
                            xs5
                            offset-xs1
                            class="text-xs-right"
                        >
                            <h6>
                                <v-chip
                                    v-if="search"
                                    outline
                                    color="black"
                                >
                                    R$ {{ valorTotalPagamentos() | filtroFormatarParaReal }}
                                </v-chip>
                                <v-chip
                                    v-else
                                    outline
                                    color="black"
                                >
                                    R$ {{ valorTotal | filtroFormatarParaReal }}
                                </v-chip>
                            </h6>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card>
            <v-dialog v-model="dialog">
                <v-card>
                    <v-card-text v-if="dadosPagamento">
                        <v-container
                            grid-list-md
                            text-xs-left
                        >
                            <div>
                                <v-layout
                                    justify-space-around
                                    row
                                    wrap
                                >
                                    <v-flex
                                        lg12
                                        xs12
                                        sm12
                                        md12
                                        dark
                                        class="text-xs-left"
                                    >
                                        <h4>DADOS DO PAGAMENTO</h4>
                                        <h3 class="text-xs-center">
                                            <b>{{ dadosPagamento.Item }}</b>
                                        </h3>
                                        <v-divider class="pb-2" />
                                    </v-flex>
                                </v-layout>
                                <v-layout
                                    row
                                >
                                    <v-flex
                                        xs12
                                        sm12
                                        md12
                                    >
                                        <b>Arquivo</b><br>
                                        <v-btn
                                            v-if="dadosPagamento.idArquivo"
                                            :href="`/upload/abrir?id=${dadosPagamento.idArquivo}`"
                                            style="text-decoration: none"
                                            round
                                            small
                                        >
                                            <span v-html="dadosPagamento.nmArquivo" />
                                            <v-icon right>
                                                cloud_download
                                            </v-icon>
                                        </v-btn>
                                        <span v-else>
                                            -
                                        </span>
                                    </v-flex>
                                </v-layout>
                                <v-layout
                                    row
                                >
                                    <v-flex
                                        xs12
                                        sm12
                                        md3
                                    >
                                        <b>Data Pagamento</b>
                                        <p
                                            v-if="dadosPagamento.DtPagamento"
                                        >
                                            {{ dadosPagamento.DtPagamento | formatarData }}
                                        </p>
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm12
                                        md3
                                    >
                                        <b>Data Emissão</b>
                                        <p
                                            v-if="dadosPagamento.DtPagamento"
                                        >
                                            {{ dadosPagamento.DtEmissao | formatarData }}
                                        </p>
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm12
                                        md3
                                    >
                                        <b>Valor Pagamento</b>
                                        <p
                                            v-if="dadosPagamento.vlPagamento"
                                        >
                                            R$ {{ dadosPagamento.vlPagamento | filtroFormatarParaReal }}
                                        </p>
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                </v-layout>
                                <v-layout
                                    row
                                >
                                    <v-flex
                                        xs12
                                        sm12
                                        md3
                                    >
                                        <b>Fornecedor</b>
                                        <p
                                            v-if="dadosPagamento.Fornecedor"
                                            v-html="dadosPagamento.Fornecedor"
                                        />
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm12
                                        md3
                                    >
                                        <b class="pr-2">CNPJ/CPF</b>
                                        <p
                                            v-if="dadosPagamento.CNPJCPF"
                                            class="pr-2"
                                        >
                                            {{ dadosPagamento.CNPJCPF | cnpjFilter }}
                                        </p>
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                </v-layout>
                                <v-layout
                                    row
                                >
                                    <v-flex
                                        xs12
                                        sm12
                                        md3
                                    >
                                        <b>Documento</b>
                                        <p
                                            v-if="dadosPagamento.tbDocumento"
                                            v-html="dadosPagamento.tbDocumento"
                                        />
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm12
                                        md3
                                    >
                                        <b>Nº Documento</b>
                                        <p
                                            v-if="dadosPagamento.nrComprovante"
                                        >
                                            {{ dadosPagamento.nrComprovante }}
                                        </p>
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm12
                                        md3
                                    >
                                        <b>Forma de Pagamento</b>
                                        <p
                                            v-if="dadosPagamento.tpFormaDePagamento"
                                            v-html="dadosPagamento.tpFormaDePagamento"
                                        />
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        sm12
                                        md3
                                    >
                                        <b>Nº Documento Pagamento</b>
                                        <p
                                            v-if="dadosPagamento.nrDocumentoDePagamento"
                                        >
                                            {{ dadosPagamento.nrDocumentoDePagamento }}
                                        </p>
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                </v-layout>
                                <v-layout
                                    row
                                    justify-space-between
                                >
                                    <v-flex>
                                        <b>Justificativa</b>
                                        <p
                                            v-if="dadosPagamento.dsJustificativa !== ' '"
                                            v-html="dadosPagamento.dsJustificativa"
                                        />
                                        <p v-else>
                                            -
                                        </p>
                                    </v-flex>
                                </v-layout>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-divider />
                    <v-card-actions>
                        <v-spacer />
                        <v-btn
                            color="red"
                            flat
                            @click="dialog = false"
                        >
                            Fechar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import cnpjFilter from '@/filters/cnpj';
import { utils } from '@/mixins/utils';
import Filtro from './components/Filtro';

export default {
    name: 'RelacaoPagamento',
    components: {
        Carregando,
        Filtro,
    },
    filters: {
        cnpjFilter,
    },
    mixins: [utils],
    props: {
        idPronac: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            search: '',
            pagination: {
            },
            selected: [],
            dadosPagamento: {},
            loading: true,
            dialog: false,
            headers: [
                {
                    text: 'Item',
                    align: 'left',
                    value: 'Item',
                },
                {
                    text: 'Fornecedor',
                    align: 'left',
                    value: 'Fornecedor',
                },
                {
                    text: 'Nr. Comprovante',
                    align: 'left',
                    value: 'nrComprovante',
                },
                {
                    text: 'Dt. Pagamento',
                    align: 'center',
                    value: 'DtPagamento',
                },
                {
                    text: 'Nr. Pagamento',
                    align: 'left',
                    value: 'nrDocumentoDePagamento',
                },
                {
                    text: 'Vl. Pagamento',
                    align: 'right',
                    value: 'vlPagamento',
                },
                {
                    text: 'VISUALIZAR',
                    align: 'center',
                    value: 'item',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'prestacaoContas/relacaoPagamento',
        }),
        valorTotal() {
            if (Object.keys(this.dados).length === 0) {
                return 0;
            }
            const table = this.dados;
            let soma = 0;

            Object.entries(table).forEach(([, row]) => {
                soma += parseFloat(row.vlPagamento);
            });

            return soma;
        },
    },
    watch: {
        dados() {
            this.loading = false;
        },
        idPronac(val) {
            this.buscarRelacaoPagamento(val);
        },
    },
    mounted() {
        this.buscarRelacaoPagamento(this.idPronac);
    },
    methods: {
        ...mapActions({
            buscarRelacaoPagamentoAction: 'prestacaoContas/buscarRelacaoPagamento',
        }),
        showItem(item) {
            this.dadosPagamento = item;
            this.dialog = true;
        },
        montaArray() {
            const dadosListagem = [];

            const pagamentosByGroup = this.pagamentosPorItem();

            Object.keys(pagamentosByGroup).forEach((key) => {
                dadosListagem.push(key);
            });

            return dadosListagem;
        },
        valorTotalPagamentos() {
            let total = 0;
            const pagamentosPorItem = this.pagamentosPorItem();

            if (typeof this.search !== 'undefined' && this.search.length > 0) {
                pagamentosPorItem[this.search].forEach((pagamento) => {
                    total += pagamento.vlPagamento;
                });
                return total;
            }

            return total;
        },
        /* global _ */
        pagamentosPorItem() {
            return _.groupBy(this.dados, pagamento => pagamento.Item.trim());
        },
        buscarRelacaoPagamento(param) {
            this.loading = true;
            this.buscarRelacaoPagamentoAction(param).finally(() => {
                this.loading = false;
            });
        },
    },
};
</script>
