<template>
    <v-btn
        :to="{ name: 'AnalisePlanilha', params:{ id: idPronac }}"
        flat
        icon
        color="green">
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                class="material-icons"
            >
                visibility
            </v-icon>
            <span>Visualizar Comprovação Financeira</span>
        </v-tooltip>
    </v-btn>

</template>

<script>
export default {
    name: 'VisualizarButton',
    props: {
        idPronac: {
            type: String,
            default: '',
        },
    },
};
</script>
