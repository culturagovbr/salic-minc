<template>
    <v-card>
        <v-card-title
            primary
            class="title">Local de realização</v-card-title>
        <v-card-text>
            <v-data-table
                v-if="localizacoes"
                :headers="headers"
                :items="localizacoes"
                class="elevation-1"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td>{{ props.item.pais }}</td>
                    <td>{{ props.item.uf }}</td>
                    <td>{{ props.item.cidade }}</td>
                </template>
            </v-data-table>
            <div v-else>Nenhuma localização</div>
        </v-card-text>
    </v-card>
</template>
<script>
export default {
    name: 'PropostaLocalRealizacao',
    props: {
        localizacoes: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            headers: [
                { text: 'País', value: 'pais' },
                { text: 'UF', value: 'uf' },
                { text: 'Cidade', value: 'cidade' },
            ],
        };
    },
};
</script>
