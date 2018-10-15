<template>
    <v-container fluid>
        <v-card>
            <v-card-title>
                <h2>Laudo Final</h2>
                <v-spacer></v-spacer>
                <v-text-field
                        v-model="search"
                        append-icon="search"
                        label="Pesquisar"
                        single-line
                        hide-details
                        height="35px"
                ></v-text-field>
            </v-card-title>
            <v-data-table
                    :headers="cabecalho"
                    :items="getProjetosLaudoFinal.items"
                    :pagination.sync="pagination"
                    hide-actions
                    :search="search"
            >
                <template slot="items" slot-scope="props">
                    <td class="text-xs-center">{{ props.index+1 }}</td>
                    <td class="text-xs-center">
                        <v-flex>
                            <div>
                                <v-btn :href="'/projeto/#/incentivo/'+ props.item.IdPronac">{{ props.item.PRONAC }}</v-btn>
                            </div>
                        </v-flex>
                    </td>
                    <td class="text-xs-center">{{ props.item.NomeProjeto }}</td>
                    <td class="text-xs-center">
                        <v-chip
                                v-if="props.item.siManifestacao == 'A'"
                                color="green darken-4"
                                text-color="white"
                        >
                            <v-avatar>
                                <v-icon dark>mood</v-icon>
                            </v-avatar>
                            Aprovado
                        </v-chip>
                        <v-chip
                                v-if="props.item.siManifestacao == 'P'"
                                color="green lighten-1"
                                text-color="white"
                        >
                            <v-avatar>
                                <v-icon>mood</v-icon>
                            </v-avatar>
                            Aprovado com ressalva
                        </v-chip>
                        <v-chip
                                v-if="props.item.siManifestacao == 'R'"
                                color="red"
                                text-color="white"
                        >
                            <v-avatar>
                                <v-icon>sentiment_very_dissatisfied</v-icon>
                            </v-avatar>
                            Reprovado
                        </v-chip>
                    </td>
                    <td class="text-xs-center">
                        <v-dialog v-model="dialog" max-width="290">
                            <v-btn slot="activator" flat icon color="green">
                                <v-icon>keyboard_return</v-icon>
                            </v-btn>
                            <v-card>
                                <v-card-title class="headline">Deseja realmente devolver o documento?</v-card-title>
                                <v-card-text>Let Google help apps determine location. This means sending anonymous location data to Google, even when no apps are running.</v-card-text>
                                <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="red" flat @click.native="dialog = false">Cancelar</v-btn>
                                <v-btn color="green" flat @click.native="dialog = false">Devolver</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </td>
                    <td class="text-xs-center">
                        <v-btn flat icon color="blue"
                               :to="{ name: 'EmitirLaudoFinal', params:{ id:props.item.IdPronac }}">
                            <v-icon>create</v-icon>
                        </v-btn>
                    </td>
                </template>
                <template slot="no-data">
                    <v-alert :value="true" color="error" icon="warning">
                        Nenhum dado encontrado ¯\_(ツ)_/¯

                    </v-alert>
                </template>
                <v-alert slot="no-results" :value="true" color="error" icon="warning">
                    Não foi possível encontrar um projeto com a palavra chave '{{search}}'.
                </v-alert>
            </v-data-table>
            <div v-if="pagination.totalItems" class="text-xs-center">
                <div class="text-xs-center pt-2">
                    <v-pagination
                            v-model="pagination.page"
                            :length="pages"
                            :total-visible="3"
                            color="green lighten-2"
                    >
                    </v-pagination>

                </div>
            </div>
        </v-card>
    </v-container>

</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import ModalTemplate from '@/components/modal';

    export default {
        name: 'Painel',
        created() {
            this.obterProjetosLaudoFinal();
        },
        data() {
            return {
                // LinkEmitirLaudo: 'localhost/avaliacao-resultado/#/emitir-laudo-final/',
                pagination: {
                    rowsPerPage: 10,
                },
                searchLength: 0,
                search: '',
                dialog: false,
                cabecalho: [
                    {
                        align: 'center',
                        text: '#',
                        sortable: false,
                    },
                    {
                        align: 'center',
                        text: 'PRONAC',
                        value: 'pronac',
                    },
                    {
                        align: 'center',
                        text: 'Nome Do Projeto',
                        value: 'nomeProjeto',
                    },
                    {
                        align: 'center',
                        text: 'Manifestação',
                        value: 'manifestacao',
                    },
                    {
                        align: 'center',
                        text: 'Devolver',
                        sortable: false,

                    },
                    {
                        align: 'center',
                        text: 'Emitir Laudo',
                        sortable: false,
                    },
                ],
            };
        },
        components: {
            ModalTemplate,
        },
        methods: {
            ...mapActions({
                obterProjetosLaudoFinal: 'avaliacaoResultados/obterProjetosLaudoFinal',
            }),
        },
        watch: {
            dadosTabela() {
            },
        },
        computed: {
            ...mapGetters({
                getProjetosLaudoFinal: 'avaliacaoResultados/getProjetosLaudoFinal',
            }),
            pages() {
                if (this.pagination.rowsPerPage == null ||
                    this.pagination.totalItems == null
                ) return 0;
                return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage);
            },
        },
        created() {
            this.ProjetosLaudoFinal();
        },
    };
</script>
