<template>
    <div>
        <extratos-bancarios
            v-if="dadosProjeto.idPronac"
            :id-pronac="dadosProjeto.idPronac" />
        />
    </div>
</template>
<script>

import { mapGetters } from 'vuex';
import ExtratosBancarios from '@/modules/projeto/visualizar/components/dadosBancarios/ExtratosBancarios';

export default {
    name: 'ExtratosBancariosView',
    components: { ExtratosBancarios },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
        }),
    },
};
</script>
