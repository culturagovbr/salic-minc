<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Devoluções'"/>
        </div>
        <div v-else>
            <v-card>
                <div v-if="Object.keys(dadosDevolucoesIncentivador).length > 0">
                    <v-container fluid>
                        <FiltroData
                            :text="'Escolha a Dt. Devolução:'"
                            @eventoFiltrarData="filtrarData"
                        />
                    </v-container>
                </div>
                <v-data-table
                    :headers="headers"
                    :items="dadosDevolucoesIncentivador"
                    :pagination.sync="pagination"
                    :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                    :search="search"
                    class="elevation-1 container-fluid"
                >
                    <template
                        slot="items"
                        slot-scope="props">
                        <td
                            class="text-xs-left"
                            v-html="props.item.Nome"/>
                        <td class="text-xs-left">
                            {{ props.item.CNPJCPF | cnpjFilter }}
                        </td>
                        <td class="text-xs-left">
                            {{ props.item.Descricao }}
                        </td>
                        <td class="text-xs-center pl-5">
                            {{ props.item.dtLote | formatarData }}
                        </td>
                        <td class="text-xs-right">
                            {{ props.item.vlDeposito | filtroFormatarParaReal }}
                        </td>
                    </template>
                    <template
                        slot="pageText"
                        slot-scope="props">
                        Items {{ props.pageStart }}
                        - {{ props.pageStop }}
                        de {{ props.itemsLength }}
                    </template>
                </v-data-table>
            </v-card>
        </div>
    </div>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';
import FiltroData from './components/FiltroData';
import cnpjFilter from '@/filters/cnpj';

export default {
    name: 'Devolucoes',
    components: {
        Carregando,
        FiltroData,
    },
    filters: {
        cnpjFilter,
    },
    mixins: [utils],
    data() {
        return {
            search: '',
            pagination: {
                sortBy: 'fat',
            },
            selected: [],
            loading: true,
            headers: [
                {
                    text: 'INCENTIVADOR',
                    align: 'left',
                    value: 'Nome',
                },
                {
                    text: 'CNPJ',
                    align: 'left',
                    value: 'CNPJCPF',
                },
                {
                    text: 'DESCRIÇÃO',
                    align: 'left',
                    value: 'Descricao',
                },
                {
                    text: 'DT. DEVOLUÇÃO',
                    align: 'center',
                    value: 'dtLote',
                },
                {
                    text: 'VL. DEPÓSITO',
                    align: 'center',
                    value: 'vlDeposito',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dadosDevolucoesIncentivador: 'dadosBancarios/devolucoesIncentivador',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.loading = true;
            const params = {
                idPronac: value.idPronac,
                dtInicio: '',
                dtFim: '',
            };
            this.buscarDevolucoesIncentivador(params);
        },
        dadosDevolucoesIncentivador() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            const params = {
                idPronac: this.dadosProjeto.idPronac,
                dtInicio: '',
                dtFim: '',
            };
            this.buscarDevolucoesIncentivador(params);
        }
    },
    methods: {
        ...mapActions({
            buscarDevolucoesIncentivador: 'dadosBancarios/buscarDevolucoesIncentivador',
        }),
        filtrarData(response) {
            const params = {
                idPronac: this.dadosProjeto.idPronac,
                dtInicio: response.dtInicio,
                dtFim: response.dtFim,
            };
            this.buscarDevolucoesIncentivador(params);
        },
    },
};
</script>
