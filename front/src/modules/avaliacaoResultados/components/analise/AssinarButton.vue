<template>
    <v-btn
        dark
        icon
        flat
        small
        color="green"
        :to="{ name: 'AnalisePlanilha', params:{ id: idPronac }}"
    >
        <v-icon>edit</v-icon>
    </v-btn>
</template>

<script>
    export default {
        name: 'AssinarButton',
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