<template>
    <div class="tabelas">
        <div class="row">
            <slTabelaSimples v-bind:dados="dados"></slTabelaSimples>
        </div>
    </div>
</template>
<script>
import slTabelaSimples from '@/components/slTabelaSimples';

export default {
    name: 'PropostaCustosVinculados',
    data() {
        return {
            dados: [],
        };
    },
    props: ['arrayCustos'],
    components: {
        slTabelaSimples,
    },
    mounted() {
        if (typeof this.arrayCustos !== 'undefined') {
            this.dados = this.arrayCustos;
        }
    },
    watch: {
        arrayCustos(value) {
            this.dados = value;
        },
    },
};
</script>
