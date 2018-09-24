<template>
    <div>
        <v-data-table
                :headers="cabecalho"
                :items="dadosTabelaTecnico.items"
                :pagination.sync="pagination"
                hide-actions

        >
            <template slot="items" slot-scope="props">
                <td>{{ props.index+1 }}</td>
                <td class="text-xs-right">
                    <v-flex xs12 sm4 text-xs-center>
                        <div>
                            <v-btn :href="'/projeto/#/incentivo/'+ props.item.idPronac">{{ props.item.Pronac }}</v-btn>
                        </div>
                    </v-flex>
                </td>
                <td class="text-xs-right">{{ props.item.NomeProjeto }}</td>
                <td class="text-xs-right">{{ props.item.Situacao }}</td>
                <td class="text-xs-right">{{ props.item.Area }}/{{ props.item.Segmento }}</td>
                <td class="text-xs-right">{{ props.item.UfProjeto }}</td>
                <td class="text-xs-right">{{ props.item.DtSituacao }}</td>
                <td class="text-xs-right">
                    <v-btn flat icon color="green" :to="{ name: 'tipoAvaliacao', params:{ id:props.item.idPronac }}">
                        <v-icon class="material-icons">compare_arrows</v-icon>
                    </v-btn>
                </td>
                <td class="text-xs-right">
                    <v-btn flat icon color="indigo" :href="'/proposta/diligenciar/listardiligenciaanalista?idPronac='+ props.item.idPronac +'&situacao=E17&tpDiligencia=174'">
                        <v-icon>warning</v-icon>
                    </v-btn>
                </td>
                <td class="text-xs-right">
                    <Historico
                            :id-pronac="props.item.idPronac"
                            :pronac="props.item.Pronac"
                            :nome-projeto="props.item.NomeProjeto"
                    ></Historico>

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
                        :total-visible="3"
                        color="green lighten-2"
                ></v-pagination>
            </div>
        </div>
    </div>

</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import ModalTemplate from '@/components/modal';
    import Historico from './Historico';

    export default {
        name: 'Painel',
        created() {
            this.obterDadosTabelaTecnico();
        },
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
                    { text: 'Nome Do Projeto', value: 'NomeProjeto' },
                    { text: 'Situacao', value: 'Situacao' },
                    { text: 'Area/Segmento', value: 'Area' },
                    { text: 'Estado', value: 'UfProjeto' },
                    { text: 'Dt.recebimento', value: 'DtSituacao' },
                    {
                        text: 'Analisar',
                        value: 'analisar',
                        sortable: false,
                    },
                    {
                        text: 'Diligencia',
                        value: 'diligencia',
                        sortable: false,

                    },
                    {
                        text: 'Historico',
                        value: 'historico',
                        sortable: false,
                    },
                ],
            };
        },
        components: {
            ModalTemplate,
            Historico,
        },
        methods: {
            ...mapActions({
                obterDadosTabelaTecnico: 'avaliacaoResultados/obterDadosTabelaTecnico',
            }),
        },
        watch: {
            dadosTabelaTecnico() {
                this.pagination.totalItems = this.dadosTabelaTecnico.items.length;
            },
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
    };
</script>
