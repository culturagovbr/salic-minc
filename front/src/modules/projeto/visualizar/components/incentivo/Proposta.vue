<template>
    <slProposta :idpreprojeto="dadosProjeto.idPreProjeto"></slProposta>
</template>
<script>
import slProposta from '@/modules/proposta/visualizar/Proposta';
import { mapGetters } from 'vuex';

export default {
    components: {
        slProposta,
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
        }),
    },
};
</script>