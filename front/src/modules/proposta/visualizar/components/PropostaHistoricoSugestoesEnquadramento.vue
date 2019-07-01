<template>
    <div class="tabelas">
        <div class="row">
            <slTabelaSimples :dados="dado"/>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import slTabelaSimples from '@/components/slTabelaSimples';

export default {
    name: 'PropostaHistoricoSugestoesEnquadramento',
    components: {
        slTabelaSimples,
    },
    props: {
        idpreprojeto: {
            type: Number,
            default: 0,
        },
    },
    computed: {
        ...mapGetters({
            dado: 'proposta/historicoEnquadramento',
        }),
    },
    watch: {
        idpreprojeto(value) {
            this.buscarHistoricoEnquadramento(value);
        },
    },
    mounted() {
        if (this.idpreprojeto !== 0) {
            this.buscarHistoricoEnquadramento(this.idpreprojeto);
        }
    },
    methods: {
        ...mapActions({
            buscarHistoricoEnquadramento: 'proposta/buscarHistoricoEnquadramento',
        }),
    },
};
</script>
