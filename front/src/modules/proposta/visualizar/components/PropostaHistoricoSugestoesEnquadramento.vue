<template>
    <div class="tabelas">
        <div class="row">
            <slTabelaSimples v-bind:dados="dado"></slTabelaSimples>
        </div>
    </div>
</template>
<script>
    import slTabelaSimples from '@/components/slTabelaSimples';
    import { mapActions, mapGetters } from 'vuex';

    export default {
        name: 'PropostaHistoricoSugestoesEnquadramento',
        props: ['idpreprojeto'],
        components: {
            slTabelaSimples,
        },
        mounted() {
            if (typeof this.idpreprojeto !== 'undefined') {
                this.buscarHistoricoEnquadramento(this.idpreprojeto);
            }
        },
        watch: {
            idpreprojeto(value) {
                this.buscarHistoricoEnquadramento(value);
            },
        },
        computed: {
            ...mapGetters({
                dado: 'proposta/historicoEnquadramento',
            }),
        },
        methods: {
            ...mapActions({
                buscarHistoricoEnquadramento: 'proposta/buscarHistoricoEnquadramento',
            }),
        },
    };
</script>
