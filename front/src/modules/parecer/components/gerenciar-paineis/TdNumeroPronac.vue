<template>
    <td>
        <v-tooltip
            bottom
        >
            <a
                slot="activator"
                :href="`/projeto/#/${produto.idPronac}`"
                target="_blank"
                class="mr-2"
            >
                {{ produto.pronac }}
            </a>
            <span>Consultar projeto {{ produto.nomeProjeto }}</span>
        </v-tooltip>
    </td>
</template>

<script>

export default {
    name: 'TdNumeroPronac',

    props: {
        produto: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
