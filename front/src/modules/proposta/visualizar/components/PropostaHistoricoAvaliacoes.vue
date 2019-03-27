<template>
    <div class="tabelas">
        <div class="row">
            <slTabelaSimples :dados="dado"/>
        </div>
    </div>
</template>
<script>
import slTabelaSimples from '@/components/slTabelaSimples';

export default {
    name: 'PropostaHistoricoAvaliacoes',
    components: {
        slTabelaSimples,
    },
    props: {
        idpreprojeto: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            dado: [],
        };
    },
    watch: {
        idpreprojeto(value) {
            this.fetch(value);
        },
    },
    mounted() {
        if (typeof this.idpreprojeto !== 'undefined') {
            this.fetch(this.idpreprojeto);
        }
    },
    methods: {
        fetch(id) {
            if (id) {
                const self = this;
                /* eslint-disable */
                $3.ajax({
                    url: '/proposta/visualizar/obter-historico-avaliacoes/idPreProjeto/' + id
                }).done(function (response) {
                    self.dado = response.data;
                });
            }
        },
    }
};
</script>