<template>
    <v-card
        class="mb-2">
        <v-card-title
            primary
            class="title">Telefones</v-card-title>
        <v-data-table
            :headers="headers"
            :items="telefones"
            class="elevation-1"
        >
            <template
                slot="items"
                slot-scope="props">
                <td>{{ props.item.dstelefone }}</td>
                <td>{{ props.item.ufsigla }}</td>
                <td>{{ props.item.ddd }}</td>
                <td>{{ props.item.numero }}</td>
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
        telefones: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            headers: [
                { text: 'Tipo', value: 'dstelefone' },
                { text: 'UF', value: 'ufsigla' },
                { text: 'DDD', value: 'ddd' },
                { text: 'Número', value: 'numero' },
                { text: 'Pode Divulgar?', value: 'divulgar' },
            ],
        };
    },
};
</script>
