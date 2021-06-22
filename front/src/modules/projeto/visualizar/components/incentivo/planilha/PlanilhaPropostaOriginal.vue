<template>
    <div id="planilha-proposta-original">
        <Carregando
            v-if="loading"
            :text="'Procurando planilha'"/>

        <Planilha
            v-if="Object.keys(planilha).length > 0"
            :array-planilha="planilha"/>
        <div
            v-if="semResposta"
            class="card-panel padding 20 center-align">{{ mensagem }}</div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/Carregando';
import Planilha from '@/components/Planilha/Planilha';

export default {
    name: 'PlanilhaPropostaOriginal',
    components: {
        Carregando,
        Planilha,
    },
    data() {
        return {
            loading: true,
            semResposta: false,
            mensagem: '',
        };
    },
    computed: {
        ...mapGetters({
            dadosProjeto: 'projeto/projeto',
            planilha: 'projeto/planilhaOriginal',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.buscaPlanilhaOriginal(value.idPronac);
        },
        planilha() {
            this.loading = false;
        },
    },
    mounted() {
        this.buscaPlanilhaOriginal(this.dadosProjeto.idPronac);
    },
    methods: {
        ...mapActions({
            buscaPlanilhaOriginal: 'projeto/buscaPlanilhaOriginal',
        }),
    },
};
</script>
