<template>
    <v-card>
        <v-card-title
            primary
            class="title"
        >
            Documentos da Proposta
        </v-card-title>
        <v-card-text>
            <v-data-table
                v-if="documentos && documentos.length > 0"
                :headers="headers"
                :items="documentos"
                class="elevation-1"
            >
                <template
                    slot="items"
                    slot-scope="props"
                >
                    <td>{{ props.item.Descricao }}</td>
                    <td>{{ props.item.Data | formatarData }}</td>
                    <td>
                        <a
                            :href="getUrl(
                                props.item.idDocumentosPreProjetos, props.item.tpDoc)"
                            title="Abrir arquivo"
                        >{{ props.item.NoArquivo }}</a>
                    </td>
                </template>
            </v-data-table>
            <div v-else>
                Não existem documentos cadastrados.
            </div>
        </v-card-text>
    </v-card>
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'PropostaDocumentosProposta',
    mixins: [utils],
    props: {
        documentos: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            headers: [
                { text: 'Documento', value: 'Descricao' },
                { text: 'Data envio', value: 'Data' },
                { text: 'Arquivo', value: 'NoArquivo' },
            ],
        };
    },
    methods: {
        getUrl(id, tipo) {
            return `/admissibilidade/admissibilidade/abrir-documentos-anexados-admissibilidade/?id=${id}&tipo=${tipo}`;
        },
    },
};
</script>
