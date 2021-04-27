<template>
    <relacao-pagamento
        v-if="dadosProjeto.idPronac"
        :id-pronac="dadosProjeto.idPronac"
    />
</template>
<script>

import { mapGetters } from 'vuex';
import RelacaoPagamento from '@/modules/projeto/visualizar/components/prestacaoContas/RelacaoPagamento';

export default {
    name: 'RelacaoPagamentoView',
    components: { RelacaoPagamento },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
        }),
    },
};
</script>
