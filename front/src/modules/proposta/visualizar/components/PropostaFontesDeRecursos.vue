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
    name: 'PropostaFontesDeRecursos',
    components: {
        slTabelaSimples,
    },
    props: {
        idpreprojeto: {
            type: null,
            default: null,
        },
    },
    computed: {
        ...mapGetters({
            dado: 'proposta/fontesDeRecursos',
        }),
    },
    watch: {
        idpreprojeto(value) {
            this.buscaFontesDeRecursos(value);
        },
    },
    mounted() {
        if (typeof this.idpreprojeto !== 'undefined') {
            this.buscaFontesDeRecursos(this.idpreprojeto);
        }
    },
    methods: {
        ...mapActions({
            buscaFontesDeRecursos: 'proposta/buscaFontesDeRecursos',
        }),
    },
};
</script>
