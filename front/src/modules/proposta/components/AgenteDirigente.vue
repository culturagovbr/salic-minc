<template>
    <v-card
        class="mb-2">
        <v-card-title
            primary
            class="title">Dirigentes</v-card-title>
        <v-card-text>
            <v-data-table
                v-if="dirigentes && dirigentes.length > 0"
                :headers="headers"
                :items="dirigentes"
                class="elevation-1"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td>{{ props.item.cnpjcpfdirigente | formatarCpfOuCnpj }}</td>
                    <td>{{ props.item.nomedirigente }}</td>
                </template>
            </v-data-table>
            <div v-else>Não existem dirigentes cadastrados.</div>
        </v-card-text>
    </v-card>
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'AgenteDirigente',
    mixins: [utils],
    props: {
        dirigentes: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            headers: [
                { text: 'CPF', value: 'cnpjcpfdirigente' },
                { text: 'Nome', value: 'nomedirigente' },
            ],
        };
    },
};
</script>
