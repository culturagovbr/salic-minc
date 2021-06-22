<template>
    <div>
        <div v-if="loading">
            <Carregando :text="'Documentos Anexados'"/>
        </div>
        <div v-else-if="documentosAnexados.documentos">
            <v-data-table
                :headers="headers"
                :items="indexItems"
                :search="search"
                :pagination.sync="pagination"
                :rows-per-page-items="[10, 25, 50, {'text': 'Todos', value: -1}]"
                item-key="id"
                class="elevation-1"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-right">{{ props.item.id + 1 }}</td>
                    <td class="text-xs-left">{{ props.item.Anexado }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.Data | formatarData }}</td>
                    <td class="text-xs-left">{{ props.item.Descricao }}</td>
                    <td class="text-xs-left">
                        <v-btn
                            :loading="parseInt(props.item.id) === loadingButton"
                            :href="`/consultardadosprojeto`+
                                `/abrir-documentos-anexados`+
                                `?id=${props.item.idArquivo}`+
                                `&tipo=${props.item.AgenteDoc}`+
                            `&idPronac=${dadosProjeto.idPronac}`"
                            style="text-decoration: none"
                            round
                            small
                            @click="loadingButton = parseInt(props.item.id)"
                        >
                            {{ props.item.NoArquivo }}
                        </v-btn>
                    </td>
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
import { mapGetters, mapActions } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import { utils } from '@/mixins/utils';

export default {
    name: 'DocumentosAnexados',
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
                sortBy: 'Data',
                descending: true,
            },
            indexDocumentosAnexados: 0,
            selected: [],
            loading: true,
            loadingButton: -1,
            headers: [
                {
                    text: 'N°',
                    align: 'center',
                    value: 'id',
                },
                {
                    align: 'left',
                    text: 'CLASSIFICAÇÃO',
                    value: 'Anexado',
                },
                {
                    align: 'center',
                    text: 'DATA',
                    value: 'Data',
                },
                {
                    align: 'left',
                    text: 'TIPO DE DOCUMENTO',
                    value: 'Descricao',
                },
                {
                    align: 'left',
                    text: 'DOCUMENTO',
                    value: 'NoArquivo',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            documentosAnexados: 'outrasInformacoes/documentosAnexados',
        }),
        indexItems() {
            const currentItems = this.documentosAnexados.documentos;
            return currentItems.map((item, index) => ({
                id: index,
                ...item,
            }));
        },
    },
    watch: {
        dados(value) {
            this.informacoes = value.informacoes;
        },
        documentosAnexados() {
            this.loading = false;
        },
        loadingButton() {
            setTimeout(() => { this.loadingButton = -1; }, 2000);
        },
    },
    mounted() {
        if (typeof this.dadosProjeto.idPronac !== 'undefined') {
            this.buscarDocumentosAnexados(this.dadosProjeto.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarDocumentosAnexados: 'outrasInformacoes/buscarDocumentosAnexados',
        }),
    },
};
</script>
