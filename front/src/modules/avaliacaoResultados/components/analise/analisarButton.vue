<template>
    <v-btn flat icon color="green" :to="{ name: 'AnalisePlanilha', params:{ id: idPronac }}">
        <v-tooltip bottom>
            <v-icon slot="activator" class="material-icons">compare_arrows</v-icon>
            <span>Analisar Projeto</span>
        </v-tooltip>
    </v-btn>

</template>

<script>
    export default {
        name: 'AnalisarButton',
        props: [
            'idPronac',
        ],
        data() {
            return {
            };
        },
        methods: {
        },
        computed: {
        },
    };
</script>