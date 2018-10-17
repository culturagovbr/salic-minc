<template>
    <div>
        <v-data-table
            :headers="cabecalho"
            :items="dados.items"
            :pagination.sync="pagination"
            hide-actions
        >
            <template slot="items" slot-scope="props">
                <td>{{ props.index+1 }}</td>
                <td class="text-xs-right">
                    <v-flex xs12 sm4 text-xs-center>
                        <div>
                            <v-btn :href="'/projeto/#/incentivo/'+ props.item.idPronac">{{ props.item.PRONAC }}</v-btn>
                        </div>
                    </v-flex>
                </td>
                <td class="text-xs-left">{{ props.item.NomeProjeto }}</td>
                <td class="text-xs-center">{{ props.item.Situacao }}</td>
                <td class="text-xs-center">{{ props.item.UfProjeto }}</td>
                <!-- <td class="text-xs-right">
                    <v-btn flat icon color="green" :to="{ name: 'AnalisePlanilha', params:{ id:props.item.idPronac }}">
                        <v-icon class="material-icons">assignment_indcompare_arrows</v-icon>
                    </v-btn>
                </td>
                <td class="text-xs-right">
                    <v-btn flat icon color="indigo" :href="'/proposta/diligenciar/listardiligenciaanalista?idPronac='+ props.item.idPronac +'&situacao=E17&tpDiligencia=174'">
                        <v-icon>warning</v-icon>
                    </v-btn>
                </td> -->
                <td class="text-xs-center">
                    <template v-for="c in componentes">
                        <component 
                            :is="c" 
                            :id-pronac="props.item.IdPRONAC"
                            :pronac="props.item.PRONAC"
                            :nome-projeto="props.item.NomeProjeto"
                        ></component>
                    </template>
                </td>
            </template>
            <template slot="no-data">
                <v-alert :value="true" color="error" icon="warning">
                    Nenhum dado encontrado ¯\_(ツ)_/¯
                </v-alert>
            </template>
        </v-data-table>
        <div class="text-xs-center">
            <div class="text-xs-center pt-2">
                <v-pagination
                    v-model="pagination.page"
                    :length="pages"
                    :total-visible="4"
                    color="green lighten-2"
                ></v-pagination>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'TabelaProjetos',
    props: ['dados', 'componentes'],
    data() {
        return {
            pagination: {
                rowsPerPage: 10,
            },
            selected: [],
            cabecalho: [
                {
                    text: '#',
                    align: 'left',
                    sortable: false,
                    value: 'numero',
                },
                { text: 'PRONAC', value: 'Pronac' },
                { text: 'Nome Do Projeto', 
                    align: 'center',
                    value: 'NomeProjeto' },
                { 
                    text: 'Situacao', 
                    align: 'center',
                    value: 'Situacao' },
                { 
                    text: 'Estado', 
                    align: 'center',
                    value: 'UfProjeto' },
                {
                    text: 'Ações',
                    sortable: false,
                    align: 'center',
                },
            ],
        };
    },
    methods: {
        ...mapActions({
            obterDadosTabelaTecnico: 'avaliacaoResultados/obterDadosTabelaTecnico',
        }),
    },
    computed: {
        ...mapGetters({
            dadosTabelaTecnico: 'avaliacaoResultados/dadosTabelaTecnico',
        }),
        pages() {
            if (this.pagination.rowsPerPage == null ||
                this.pagination.totalItems == null
            ) return 0;
            return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage);
        },
    },
    watch: {
        dadosTabelaTecnico() {
            if (this.dados.items !== undefined) {
                this.pagination.totalItems = this.dados.items.length;
            }
        },
    },
};
</script>
