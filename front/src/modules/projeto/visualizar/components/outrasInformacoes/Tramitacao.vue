<template>
    <div>
        <div v-if="dadosProjeto">
            <UltimaTramitacao :id-pronac="dadosProjeto.idPronac"/>
            <TramitacaoProjeto
                :id-pronac="dadosProjeto.idPronac"/>
            <TramitacaoDocumento
                :id-pronac="dadosProjeto.idPronac"/>
        </div>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import UltimaTramitacao from './components/UltimaTramitacao';
import TramitacaoProjeto from './components/TramitacaoProjeto';
import TramitacaoDocumento from './components/TramitacaoDocumento';

export default {
    name: 'Tramitacao',
    components: {
        UltimaTramitacao,
        TramitacaoProjeto,
        TramitacaoDocumento,
    },
    props: {
        idPronac: {
            type: Number,
            default: 0,
        },
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
        }),
    },
};
</script>
