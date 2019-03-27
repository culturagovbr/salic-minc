<template>
    <div>
        <v-card>
            <v-card-title>
                <h6>Tramita&ccedil;&atilde;o do Projeto</h6>
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
                    <td class="text-xs-left">{{ props.item.Origem }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.dtTramitacaoEnvio | formatarData }}</td>
                    <td class="text-xs-left">{{ props.item.Destino }}</td>
                    <td class="text-xs-center pl-5">{{ props.item.dtTramitacaoRecebida | formatarData }}</td>
                    <td class="text-xs-left">{{ props.item.Situacao }}</td>
                    <td class="text-xs-left">{{ props.item.meDespacho }}</td>
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
    name: 'TramitacaoProjeto',
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
                sortBy: 'dtTramitacaoEnvio',
                descending: true,
            },
            selected: [],
            headers: [
                {
                    text: 'ORIGEM',
                    align: 'left',
                    value: 'Origem',
                },
                {
                    text: 'DT.ENVIO',
                    align: 'center',
                    value: 'dtTramitacaoEnvio',
                },
                {
                    text: 'DESTINO',
                    align: 'left',
                    value: 'Destino',
                },
                {
                    text: 'DT.RECEBIMENTO',
                    align: 'center',
                    value: 'dtTramitacaoRecebida',
                },
                {
                    text: 'ESTADO',
                    align: 'left',
                    value: 'Situacao',
                },
                {
                    text: 'DESPACHO',
                    align: 'left',
                    value: 'meDespacho',
                },
            ],
        };
    },
    computed: {
        ...mapGetters({
            dados: 'outrasInformacoes/tramitacaoProjeto',
        }),
    },
    mounted() {
        if (typeof this.idPronac !== 'undefined') {
            this.buscarTramitacaoProjeto(this.idPronac);
        }
    },
    methods: {
        ...mapActions({
            buscarTramitacaoProjeto: 'outrasInformacoes/buscarTramitacaoProjeto',
        }),
    },
};
</script>
