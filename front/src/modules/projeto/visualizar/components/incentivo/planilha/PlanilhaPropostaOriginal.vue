<template>
    <div id="planilha-proposta-original">
        <Carregando v-if="loading" :text="'Procurando planilha'"></Carregando>

        <Planilha v-if="Object.keys(planilha).length > 0"
                  :arrayPlanilha="planilha"></Planilha>
        <div v-if="semResposta" class="card-panel padding 20 center-align">{{ mensagem }}</div>
    </div>
</template>

<script>
    import Carregando from '@/components/Carregando';
    import Planilha from '@/components/Planilha/Planilha';
    import { mapActions, mapGetters } from 'vuex';

    export default {
        name: 'PlanilhaPropostaOriginal',
        data() {
            return {
                loading: true,
                semResposta: false,
                mensagem: '',
            };
        },
        components: {
            Carregando,
            Planilha,
        },
        mounted() {
            this.buscaPlanilhaOriginal(this.dadosProjeto.idPreProjeto);
        },
        watch: {
            dadosProjeto(value) {
                this.buscaPlanilhaOriginal(value.idPreProjeto);
            },
            planilha() {
                this.loading = false;
            },
        },
        computed: {
            ...mapGetters({
                dadosProjeto: 'projeto/projeto',
                planilha: 'projeto/planilhaOriginal',
            }),
        },
        methods: {
            ...mapActions({
                buscaPlanilhaOriginal: 'projeto/buscaPlanilhaOriginal',
            }),
        },
    };
</script>
