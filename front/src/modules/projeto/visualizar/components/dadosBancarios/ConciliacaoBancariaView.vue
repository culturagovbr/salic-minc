<template>
    <div>
        <conciliacao-bancaria
            v-if="dadosProjeto.idPronac"
            :id-pronac="dadosProjeto.idPronac"
        />
    </div>
</template>
<script>

import { mapGetters } from 'vuex';
import ConciliacaoBancaria from '@/modules/projeto/visualizar/components/dadosBancarios/ConciliacaoBancaria';

export default {
    name: 'ConciliacaoBancariaView',
    components: { ConciliacaoBancaria },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
        }),
    },
};
</script>
