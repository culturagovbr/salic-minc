<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Pagamentos Consolidados'"/>
        </div>
        <div v-else-if="dados">
            <v-card>
                <v-container fluid>
                    <v-layout
                        justify-start
                        row
                        wrap>
                        <Filtro
                            :items="montaArray('Item')"
                            :label="'Pesquise Item'"
                            class="pr-5"
                            @eventoSearch="search = $event"
                        />
                        <Filtro
                            :items="montaArray('UFFornecedor')"
                            :label="'Pesquise UF'"
                            class="pr-5"
                            @eventoSearch="search = $event"
                        />
                        <Filtro
                            :items="montaArray('MunicipioFornecedor')"
                            :label="'Pesquise Municipio'"
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
                        <td class="text-xs-center">{{ props.item.id + 1 }}</td>
                        <td class="text-xs-left">{{ props.item.UFFornecedor }}</td>
                        <td class="text-xs-left">{{ props.item.MunicipioFornecedor }}</td>
                        <td class="text-xs-left"><b>{{ props.item.Item }}</b></td>
                        <td
                            class="text-xs-center pl-5"
                            style="width: 200px">
                            {{ props.item.CNPJCPF | cnpjFilter }}
                        </td>
                        <td class="text-xs-left">{{ props.item.Fornecedor }}</td>
                        <td class="text-xs-center pl-5">{{ props.item.DtComprovacao | formatarData }}</td>
                        <td class="text-xs-right">{{ props.item.vlPagamento | filtroFormatarParaReal }}</td>
                        <td class="text-xs-left">
                            <v-btn
                                :loading="parseInt(props.item.id) === loadingButton"
                                :href="`/upload`+
                                    `/abrir`+
                                `?id=${props.item.idArquivo}`"
                                style="text-decoration: none"
                                round
                                small
                                @click="loadingButton = parseInt(props.item.id)"
                            >
                                {{ props.item.nmArquivo }}
                            </v-btn>
                        </td>
                    </template>
                </v-data-table>
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
                    align: 'center',
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
    },
    watch: {
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
        montaArray(value) {
            const dadosListagem = [];
            let arrayFiltro = this.dados;
            arrayFiltro = arrayFiltro.filter((element, i, array) => {
                return array.map(x => x[value]).indexOf(element[value]) === i;
            });

            arrayFiltro.forEach((element) => {
                dadosListagem.push(element[value]);
            });

            return dadosListagem;
        },
    },
};
</script>