<template>
    <slProposta :idpreprojeto="dadosProjeto.idPreProjeto"/>
</template>
<script>
import { mapGetters } from 'vuex';
import slProposta from '@/modules/proposta/visualizar/Proposta';

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
