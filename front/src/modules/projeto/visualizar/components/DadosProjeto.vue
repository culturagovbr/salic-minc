<template>
    <div id="dados-projeto">
        <component
            :is="componenteProjeto"
            :id-pronac="dadosProjeto.idPronac"/>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import DadosProjetoIncentivo from './incentivo/Incentivo';
import DadosProjetoConvenio from './convenio/Convenio';

const MECANISMO_MECENATO = '1';

export default {
    components: {
        DadosProjetoIncentivo,
        DadosProjetoConvenio,
    },
    data() {
        return {
            componenteProjeto: '',
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
        }),
    },
    created() {
        this.selecionarTipoComponente();
    },
    methods: {
        selecionarTipoComponente() {
            if (this.dadosProjeto.idMecanismo === MECANISMO_MECENATO) {
                this.componenteProjeto = 'DadosProjetoIncentivo';
            } else {
                this.componenteProjeto = 'DadosProjetoConvenio';
            }
        },
    },
};
</script>
