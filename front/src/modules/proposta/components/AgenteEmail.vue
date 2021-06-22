<template>
    <v-card
        class="mb-2">
        <v-card-title
            primary
            class="title">E-mail</v-card-title>
        <v-data-table
            :headers="headers"
            :items="emails"
            class="elevation-1"
        >
            <template
                slot="items"
                slot-scope="props">
                <td>{{ props.item.tipo }}</td>
                <td>{{ props.item.descricao }}</td>
                <td>{{ props.item.divulgar | formatarLabelSimOuNao }}</td>
            </template>
        </v-data-table>
    </v-card>
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'AgenteTelefone',
    mixins: [utils],
    props: {
        emails: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            headers: [
                { text: 'Tipo', value: 'tipo' },
                { text: 'E-mail', value: 'descricao' },
                { text: 'Pode Divulgar?', value: 'divulgar' },
            ],
        };
    },
};
</script>
