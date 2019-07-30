<template>
    <v-layout>
        <v-tooltip
            bottom
        >
            <v-icon
                slot="activator"
                color="#212121"
                @click.stop="dialog = true"
            >
                gavel
            </v-icon>
            <span>Analisar Readequação</span>
        </v-tooltip>
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
    data() {
        return {
            dialog: false,
        };
    },
    watch: {
        dialog() {
            if (this.dialog === true) {
                this.redirect();
            }
        },
    },
    methods: {
        redirect() {
            this.$router.push({ path: `/readequacao/analisar/${this.dadosReadequacao.idPronac}/${this.dadosReadequacao.idReadequacao}` });
        },
    },
};
</script>
