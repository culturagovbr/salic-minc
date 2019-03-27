<template>
    <div>
        <div v-if="loading">
            <carregando :text="'Carregando Marcas Anexadas'"/>
        </div>
        <div v-else-if="dados">
            <v-card>
                <v-card-title>
                    <h6>Marcas Anexadas</h6>
                </v-card-title>
                <v-data-table
                    :pagination.sync="pagination"
                    :headers="headers"
                    :items="dados"
                    :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                    class="elevation-1 container-fluid mb-2"
                >
                    <template
                        slot="items"
                        slot-scope="props">
                        <td class="text-xs-left">{{ props.item.dsDocumento }}</td>
                        <td class="text-xs-center pl-5">{{ props.item.dtEnvio | formatarData }}</td>
                        <td class="text-xs-left">{{ props.item.stAtivoDocumentoProjeto }}</td>
                        <td class="text-xs-center">
                            <v-tooltip left>
                                <v-btn
                                    slot="activator"
                                    :loading="parseInt(props.item.idDocumento) === loadingButton"
                                    :href="`/upload/abrir?id=${props.item.idArquivo}`"
                                    style="text-decoration: none"
                                    color="blue"
                                    dark
                                    @click="loadingButton = parseInt(props.item.idDocumento)"
                                >
                                    <v-icon dark>cloud_download</v-icon>
                                </v-btn>
                                <span>{{ props.item.nmArquivo }}</span>
                            </v-tooltip>
                        </td>
                    </template>
                    <template
                        slot="pageText"
                        slot-scope="props">
                        Items {{ props.pageStart }} -
                        {{ props.pageStop }} de
                        {{ props.itemsLength }}
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

export default {
    name: 'MarcasAnexadas',
    components: {
        Carregando,
    },
    mixins: [utils],
    props: {
        idPronac: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            search: '',
            pagination: {
                rowsPerPage: 10,
                sortBy: 'dtEnvio',
                descending: true,
            },
            loading: true,
            loadingButton: -1,
            selected: [],
            headers: [
                {
                    text: 'Observações',
                    align: 'left',
                    value: 'dsDocumento',
                },
                {
                    text: 'Dt. Envio',
                    align: 'center',
                    value: 'dtEnvio',
                },
                {
                    text: 'Estado',
                    align: 'left',
                    value: 'stAtivoDocumentoProjeto',
                },
                {
                    text: 'Arquivo',
                    align: 'center',
                    value: 'nmArquivo',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            dados: 'execucao/marcasAnexadas',
        }),
    },
    watch: {
        loadingButton() {
            setTimeout(() => { this.loadingButton = -1; }, 2000);
        },
        dados() {
            this.loading = false;
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarMarcasAnexadas(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarMarcasAnexadas: 'execucao/buscarMarcasAnexadas',
        }),
    },
};
</script>
