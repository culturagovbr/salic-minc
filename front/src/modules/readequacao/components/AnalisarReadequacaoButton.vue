<template>
    <v-layout>
        <v-btn
            dark
            icon
            flat
            small
            color="#212121"
            @click.stop="redirect()"
        >
            <v-tooltip bottom>
                <v-icon slot="activator">
                    gavel
                </v-icon>
                <span>Analisar Readequação</span>
            </v-tooltip>
        </v-btn>
    </v-layout>
</template>
<script>
export default {
    name: 'AnalisarReadequacaoButton',
    props: {
        dadosReadequacao: {
            type: Object,
            default: () => {},
        },
    },
    methods: {
        redirect() {
            this.$router.push({ path: `/readequacao/analisar/${this.dadosReadequacao.idReadequacao}` });
        },
    },
};
</script>
