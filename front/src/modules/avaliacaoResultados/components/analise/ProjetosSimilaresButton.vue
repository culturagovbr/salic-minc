<template>
    <v-btn
        :to="{ name: 'projetosSimilares', params:{ idpronac: idPronac }}"
        flat
        icon
        color="primary"
    >
        <v-tooltip bottom>
            <v-icon
                slot="activator"
                class="material-icons"
                dark
                icon
                flat
                small
            >
                near_me
            </v-icon>
            <span>Projetos Similares</span>
        </v-tooltip>
    </v-btn>
</template>
<script>

export default {
    name: 'ProjetosSimilaresButton',
    props: {
        idPronac: {
            type: String,
            default: '',
        },
    },
};
</script>
