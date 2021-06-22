<template>
    <v-card>
        <v-card-title
            primary
            class="title">Deslocamentos</v-card-title>
        <v-card-text>
            <v-data-table
                v-if="Object.keys(deslocamentos).length > 1"
                :headers="headers"
                :items="deslocamentos"
                class="elevation-1"
            >
                <template
                    slot="items"
                    slot-scope="props">
                    <td>{{ props.item.paisorigem }}</td>
                    <td>{{ props.item.uforigem }}</td>
                    <td>{{ props.item.municipioorigem }}</td>
                    <td>{{ props.item.paisodestino }}</td>
                    <td>{{ props.item.ufdestino }}</td>
                    <td>{{ props.item.municipiodestino }}</td>
                    <td class="text-xs-right">{{ props.item.Qtde }}</td>
                </template>
            </v-data-table>
            <div v-else>Nenhum deslocamento cadastrado</div>
        </v-card-text>
    </v-card>
</template>
<script>
export default {
    name: 'PropostaDeslocamento',
    props: {
        deslocamentos: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            headers: [
                { text: 'Pais de Origem', value: 'paisorigem' },
                { text: 'UF de Origem', value: 'uforigem' },
                { text: 'Cidade de Origem', value: 'municipioorigem' },
                { text: 'Pais de Destino', value: 'paisodestino' },
                { text: 'UF de Destino', value: 'ufdestino' },
                { text: 'Cidade de Destino', value: 'municipiodestino' },
                { text: 'Quantidade', align: 'center', value: 'Qtde' },
            ],
        };
    },
};
</script>
