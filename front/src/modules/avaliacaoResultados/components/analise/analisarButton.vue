<template>
    <v-btn
        :to="{ name: 'AnalisePlanilha', params:{ id: idPronac }}"
        flat
        icon
        color="primary"
    >
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                class="material-icons"
            >
                gavel
            </v-icon>
            <span>Analisar Projeto</span>
        </v-tooltip>
    </v-btn>
</template>
<script>

export default {
    name: 'AnalisarButton',
    props: {
        idPronac: {
            type: String,
            default: '',
        },
    },
};
</script>
