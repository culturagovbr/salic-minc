<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>Tramita&ccedil;&atilde;o Documento</h6>
            </v-card-title>
            <v-data-table
                :pagination.sync="pagination"
                :headers="headers"
                :items="dados"
                class="elevation-1 container-fluid mb-2"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td class="text-xs-left">{{ props.item.dsTipoDocumento }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.dtDocumento | formatarData }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.dtAnexacao | formatarData }}</td>
                    <td class="text-xs-left">
                        <a
                            :href="
                                `/consultardadosprojeto`+
                                    `/abrir-documento-tramitacao`+
                                    `?id=${ props.item.idDocumento}`+
                            `&idPronac=${idPronac}`"
                        >
                            {{ props.item.noArquivo }}
                        </a>
                    </td>
                    <td class="text-xs-left">{{ props.item.Usuario }}</td>
                    <td class="text-xs-right">{{ props.item.idLote }}</td>
                    <td class="text-xs-left">{{ props.item.Situacao }}</td>
                </template>
                <template
                    slot="pageText"
                    slot-scope="props">
                    Items {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import { utils } from '@/mixins/utils';

export default {
    name: 'TramitacaoDocumento',
    mixins: [utils],
    props: {
        idPronac: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            search: '',
            pagination: {
                sortBy: 'dtDocumento',
                descending: true,
            },
            selected: [],
            headers: [
                {
                    text: 'TIPO',
                    align: 'left',
                    value: 'dsTipoDocumento',
                },
                {
                    text: 'DATA',
                    align: 'center',
                    value: 'dtDocumento',
                },
                {
                    text: 'DT. ANEXAÇÃO',
                    align: 'center',
                    value: 'dtAnexacao',
                },
                {
                    text: 'DOCUMENTO',
                    align: 'left',
                    value: 'noArquivo',
                },
                {
                    text: 'ANEXADO POR',
                    align: 'left',
                    value: 'Usuario',
                },
                {
                    text: 'LOTE',
                    align: 'center',
                    value: 'idLote',
                },
                {
                    text: 'ESTADO',
                    align: 'left',
                    value: 'Situacao',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'outrasInformacoes/tramitacaoDocumento',
        }),
    },
    mounted() {
        if (typeof this.idPronac !== 'undefined') {
            this.buscarTramitacaoDocumento(this.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarTramitacaoDocumento: 'outrasInformacoes/buscarTramitacaoDocumento',
        }),
    },
};
</script>
