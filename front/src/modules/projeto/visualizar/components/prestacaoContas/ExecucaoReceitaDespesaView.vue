<template>
    <execucao-receita-despesa
        v-if="dadosProjeto.idPronac"
        :id-pronac="dadosProjeto.idPronac"
    />
</template>
<script>

import { mapGetters } from 'vuex';
import ExecucaoReceitaDespesa from '@/modules/projeto/visualizar/components/prestacaoContas/ExecucaoReceitaDespesa';

export default {
    name: 'ExecucaoReceitaDespesaView',
    components: {
        ExecucaoReceitaDespesa,
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
        }),
    },
};
</script>
