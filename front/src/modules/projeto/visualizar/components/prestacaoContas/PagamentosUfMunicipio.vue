<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Pagamentos por UF/Município'"/>
        </div>
        <div v-else-if="dados">
            <v-card>
                <v-container fluid>
                    <v-layout
                        justify-start
                        row
                        wrap>
                        <Filtro
                            :items="montaArray('Item', dados)"
                            :label="'Pesquise Item'"
                            class="pr-5"
                            @eventoSearch="search = $event"
                        />
                        <Filtro
                            :items="montaArray('UFFornecedor', dados)"
                            :label="'Pesquise UF'"
                            class="pr-5"
                            @eventoSearch="search = $event"
                        />
                    </v-layout>
                </v-container>
                <v-data-table
                    :pagination.sync="pagination"
                    :headers="headers"
                    :items="indexItems"
                    :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                    :search="search"
                    item-key="id"
                    class="elevation-1 container-fluid"
                >
                    <template
                        slot="items"
                        slot-scope="props">
                        <td class="text-xs-center pl-5">{{ props.item.id + 1 }}</td>
                        <td class="text-xs-left">{{ props.item.UFFornecedor }}</td>
                        <td class="text-xs-left">{{ props.item.MunicipioFornecedor }}</td>
                        <td class="text-xs-left"><b>{{ props.item.Item }}</b></td>
                        <td
                            class="text-xs-left"
                            style="white-space: nowrap">
                            {{ props.item.CNPJCPF | cnpjFilter }}
                        </td>
                        <td class="text-xs-left">{{ props.item.Fornecedor }}</td>
                        <td class="text-xs-center pl-5">{{ props.item.DtComprovacao | formatarData }}</td>
                        <td class="text-xs-right">{{ props.item.vlPagamento | filtroFormatarParaReal }}</td>
                        <td class="text-xs-left">
                            <a
                                :href="`/upload`+
                                    `/abrir`+
                                `?id=${props.item.idArquivo}`"
                            >
                                <u>
                                    <p v-html="props.item.nmArquivo"/>
                                </u>
                            </a>
                        </td>
                    </template>
                </v-data-table>
                <v-container fluid>
                    <v-layout
                        row
                        wrap>
                        <v-flex xs6>
                            <h6 class="mr-3 black--text">VALOR TOTAL</h6>
                        </v-flex>
                        <v-flex
                            xs5
                            offset-xs1
                            class=" text-xs-right">
                            <h6>
                                <v-chip
                                    outline
                                    color="black">R$ {{ valorTotal | filtroFormatarParaReal }}
                                </v-chip>
                            </h6>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card>
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
    name: 'PagamentosUfMunicipio',
    components: {
        Carregando,
        Filtro,
    },
    filters: {
        cnpjFilter,
    },
    mixins: [utils],
    data() {
        return {
            search: '',
            loadingButton: -1,
            items: [
                'UF',
                'Item',
            ],
            pagination: {
                sortBy: 'id',
                ascending: true,
            },
            selected: [],
            loading: true,
            headers: [
                {
                    text: 'N°',
                    align: 'center',
                    value: 'id',
                },
                {
                    text: 'UF Fornecedor',
                    align: 'left',
                    value: 'UFFornecedor',
                },
                {
                    text: 'Município Fornecedor',
                    align: 'left',
                    value: 'MunicipioFornecedor',
                },
                {
                    text: 'Item',
                    align: 'left',
                    value: 'Item',
                },
                {
                    text: 'CNPJ/CPF',
                    align: 'left',
                    value: 'CNPJCPF',
                },
                {
                    text: 'Fornecedor',
                    align: 'left',
                    value: 'Fornecedor',
                },
                {
                    text: 'Dt. Comprovação',
                    align: 'center',
                    value: 'DtComprovacao',
                },
                {
                    text: 'Vl. Pagamento',
                    align: 'right',
                    value: 'vlPagamento',
                },
                {
                    text: 'Arquivo',
                    align: 'left',
                    value: 'nmArquivo',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'prestacaoContas/pagamentosUfMunicipio',
        }),
        indexItems() {
            const currentItems = this.dados;
            return currentItems.map((item, index) => ({
                id: index,
                ...item,
            }));
        },
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
        dadosProjeto(value) {
            this.loading = true;
            this.buscarPagamentosUfMunicipio(value.idPronac);
        },
        dados() {
            this.loading = false;
        },
        loadingButton() {
            setTimeout(() => { this.loadingButton = -1; }, 2000);
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarPagamentosUfMunicipio(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarPagamentosUfMunicipio: 'prestacaoContas/buscarPagamentosUfMunicipio',
        }),
        montaArray(value, dados = []) {
            const dadosListagem = [];
            let arrayFiltro = dados;
            arrayFiltro = arrayFiltro.filter((element, i, array) => array.map(x => x[value]).indexOf(element[value]) === i);

            arrayFiltro.forEach((element) => {
                dadosListagem.push(element[value]);
            });

            return dadosListagem;
        },
    },
};
</script>
