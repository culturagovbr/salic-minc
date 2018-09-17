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
    name: 'PropostaFontesDeRecursos',
    props: ['idpreprojeto'],
    components: {
        slTabelaSimples,
    },
    mounted() {
        if (typeof this.idpreprojeto !== 'undefined') {
            this.buscaFontesDeRecursos(this.idpreprojeto);
        }
    },
    watch: {
        idpreprojeto(value) {
            this.buscaFontesDeRecursos(value);
        },
    },
    computed: {
        ...mapGetters({
            dado: 'proposta/fontesDeRecursos',
        }),
    },
    methods: {
        ...mapActions({
            buscaFontesDeRecursos: 'proposta/buscaFontesDeRecursos',
        }),
    },
};
</script>
