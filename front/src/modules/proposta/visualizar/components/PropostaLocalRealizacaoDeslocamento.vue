<template>
    <div
        v-if="localizacoes"
        class="local-realizacao-deslocamento">
        <proposta-local-realizacao :localizacoes="localizacoes.abrangencia"/>
        <proposta-deslocamento :deslocamentos="localizacoes.deslocamento"/>
    </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import PropostaLocalRealizacao from './PropostaLocalRealizacao';
import PropostaDeslocamento from './PropostaDeslocamento';

export default {
    name: 'PropostaLocalRealizacaoDeslocamento',
    components: { PropostaDeslocamento, PropostaLocalRealizacao },
    props: {
        idpreprojeto: {
            type: [String, Number],
            default: '',
        },
        proposta: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            localizacoes: {},
        };
    },
    computed: {
        ...mapGetters({
            local: 'proposta/localRealizacaoDeslocamento',
        }),
    },
    watch: {
        idpreprojeto(value) {
            this.buscaLocalRealizacaoDeslocamento(value);
        },
        local(value) {
            this.localizacoes = value;
        },
        proposta(value) {
            this.localizacoes = value;
        },
    },
    mounted() {
        if (this.proposta && this.proposta.abrangencia) {
            this.localizacoes = this.proposta;
        }
        if (this.idpreprojeto !== '') {
            this.buscaLocalRealizacaoDeslocamento(this.idpreprojeto);
        }
    },
    methods: {
        ...mapActions({
            buscaLocalRealizacaoDeslocamento: 'proposta/buscaLocalRealizacaoDeslocamento',
        }),
    },
};
</script>
