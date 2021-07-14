<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Carregando Local de Realiza&ccedil;&atilde;o e Deslocamento'"/>
        </div>
        <div v-else-if="Object.keys(dados).length > 0">
            <v-card>
                <v-card-title>
                    <h6>Local de Realiza&ccedil;&atilde;o</h6>
                </v-card-title>
                <v-data-table
                    :headers="headersLocalRealizacao"
                    :items="dados.localRealizacoes"
                    class="elevation-1 container-fluid"
                >
                    <template
                        slot="items"
                        slot-scope="props">
                        <td>{{ props.item.Descricao }}</td>
                        <td>{{ props.item.UF }}</td>
                        <td>{{ props.item.Cidade }}</td>
                    </template>
                    <template slot="no-data">
                        <v-alert
                            :value="true"
                            color="info"
                            icon="warning">
                            Nenhum dado encontrado
                        </v-alert>
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
            <v-card>
                <v-card-title>
                    <h6>Deslocamento</h6>
                </v-card-title>
                <v-data-table
                    :headers="headersDeslocamento"
                    :items="dados.Deslocamento"
                    class="elevation-1 container-fluid"
                    rows-per-page-text="Items por Página"
                    no-data-text="Nenhum dado encontrado"
                >
                    <template
                        slot="items"
                        slot-scope="props">
                        <td>{{ props.item.PaisOrigem }}</td>
                        <td>{{ props.item.UFOrigem }}</td>
                        <td>{{ props.item.MunicipioOrigem }}</td>
                        <td>{{ props.item.PaisDestino }}</td>
                        <td>{{ props.item.UFDestino }}</td>
                        <td>{{ props.item.MunicipioDestino }}</td>
                        <td>{{ props.item.Qtde }}</td>
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

export default {
    name: 'LocalRealizacaoDeslocamento',
    components: {
        Carregando,
    },
    data() {
        return {
            search: '',
            pagination: {
                sortBy: 'fat',
            },
            selected: [],
            loading: true,
            headersLocalRealizacao: [
                {
                    text: 'PAÍS',
                    align: 'left',
                    value: 'Descricao',
                },
                {
                    text: 'UF',
                    value: 'UF',
                },
                {
                    text: 'CIDADE',
                    value: 'Cidade',
                },
            ],
            headersDeslocamento: [
                {
                    text: 'PAÍS DE ORIGEM',
                    align: 'left',
                    value: 'PaisOrigem',
                },
                {
                    text: 'UF DE ORIGEM',
                    value: 'UFOrigem',
                },
                {
                    text: 'CIDADE DE ORIGEM',
                    value: 'MunicipioOrigem',
                },
                {
                    text: 'PAÍS DE DESTINO',
                    value: 'PaisDestino',
                },
                {
                    text: 'UF DE DESTINO',
                    value: 'UFDestino',
                },
                {
                    text: 'CIDADE DE DESTINO',
                    value: 'MunicipioDestino',
                },
                {
                    text: 'QUANTIDADE',
                    value: 'Qtde',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'outrasInformacoes/localRealizacaoDeslocamento',
        }),
    },
    watch: {
        dados() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarLocalRealizacaoDeslocamento(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarLocalRealizacaoDeslocamento: 'outrasInformacoes/buscarLocalRealizacaoDeslocamento',
        }),
    },
};
</script>
