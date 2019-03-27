<template>
    <div class="tabelas">
        <div class="row">
            <slTabelaSimples :dados="dados"/>
        </div>
    </div>
</template>
<script>
import slTabelaSimples from '@/components/slTabelaSimples';

export default {
    name: 'PropostaCustosVinculados',
    components: {
        slTabelaSimples,
    },
    props: {
        arrayCustos: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            dados: [],
        };
    },
    watch: {
        arrayCustos(value) {
            this.dados = value;
        },
    },
    mounted() {
        if (typeof this.arrayCustos !== 'undefined') {
            this.dados = this.arrayCustos;
        }
    },
};
</script>
