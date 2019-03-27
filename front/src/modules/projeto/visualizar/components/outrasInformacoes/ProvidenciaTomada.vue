<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Providência Tomada'"/>
        </div>
        <div v-else-if="dados.providenciaTomada">
            <v-data-table
                :headers="headers"
                :items="dados.providenciaTomada"
                :pagination.sync="pagination"
                :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                class="elevation-1 container-fluid"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-center pl-5">{{ props.item.DtSituacao | formatarData }}</td>
                    <td class="text-xs-right">{{ props.item.Situacao }}</td>
                    <td
                        class="text-xs-left"
                        v-html="props.item.ProvidenciaTomada"/>
                    <td
                        v-if="props.item.cnpjcpf"
                        class="text-xs-left"
                        style="width: 200px">
                        {{ props.item.cnpjcpf | cnpjFilter }}
                    </td>
                    <td
                        v-else
                        class="text-xs-left">
                        Nao se aplica.
                    </td>
                    <td
                        class="text-xs-left"
                        style="width: 200px">{{ props.item.usuario }}</td>
                </template>
                <template
                    slot="pageText"
                    slot-scope="props">
                    Items {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                </template>
            </v-data-table>
        </div>
    </div>

</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import cnpjFilter from '@/filters/cnpj';
import { utils } from '@/mixins/utils';

export default {
    components: {
        Carregando,
    },
    filters: {
        cnpjFilter,
    },
    mixins: [utils],
    data() {
        return {
            search: '',
            pagination: {
                rowsPerPage: 10,
                sortBy: 'DtSituacao',
                descending: true,
            },
            selected: [],
            loading: true,
            headers: [
                {
                    text: 'DT. SITUAÇÃO',
                    align: 'center',
                    value: 'DtSituacao',
                },
                {
                    text: 'SITUAÇÃO',
                    align: 'right',
                    value: 'Situacao',
                },
                {
                    text: 'PROVIDÊNCIA TOMADA',
                    align: 'left',
                    value: 'ProvidenciaTomada',
                },
                {
                    text: 'CPF',
                    align: 'left',
                    value: 'cnpjcpf',
                },
                {
                    text: 'NOME',
                    align: 'left',
                    value: 'usuario',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'outrasInformacoes/providenciaTomada',
        }),
    },
    watch: {
        dados() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarProvidenciaTomada(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarProvidenciaTomada: 'outrasInformacoes/buscarProvidenciaTomada',
        }),
    },
};
</script>
