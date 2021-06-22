<template>
    <td class="text-xs-center">
        <v-tooltip
            v-if="produto.stPrincipal === 1"
            bottom
        >
            <v-icon
                slot="activator"
                round
            >
                looks_one
            </v-icon>
            <span>Produto principal</span>
        </v-tooltip>
        <v-tooltip
            v-else
            bottom
        >
            <v-icon
                slot="activator"
                color="grey"
            >
                looks_two
            </v-icon>
            <span>Produto secundário</span>
        </v-tooltip>
    </td>
</template>

<script>


export default {
    name: 'TdTipoProduto',

    props: {
        produto: {
            type: Object,
            default: () => {},
        },
    },

};
</script>
