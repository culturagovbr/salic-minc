<template>
    <div id="planilha-readequada">
        <Carregando v-if="loading" :text="'Procurando planilha'"></Carregando>

        <Planilha v-if="Object.keys(planilha).length > 0" :arrayPlanilha="planilha">
            <template slot-scope="slotProps">
                <PlanilhaItensReadequados :table="slotProps.itens"></PlanilhaItensReadequados>
            </template>
        </Planilha>
        <div v-if="semResposta" class="card-panel padding 20 center-align">{{ mensagem }}</div>
    </div>
</template>

<script>
    import Carregando from '@/components/Carregando';
    import Planilha from '@/components/Planilha/Planilha';
    import PlanilhaItensReadequados from '@/components/Planilha/PlanilhaItensReadequados';
    import { mapActions, mapGetters } from 'vuex';

    export default {
        /* eslint-disable */
        name: 'PlanilhaPropostaReadequada',
        data: function () {
            return {
                loading: true,
                semResposta: false,
                mensagem: ''
            };
        },
        components: {
            Carregando,
            Planilha,
            PlanilhaItensReadequados
        },
        mounted: function () {
            this.buscaPlanilhaReadequada(this.dadosProjeto.idPronac);
        },
        watch: {
            dadosProjeto(value) {
                this.buscaPlanilhaReadequada(value.idPronac);
            },
            planilha() {
                this.loading = false;
            },
        },
        computed: {
            ...mapGetters({
                dadosProjeto: 'projeto/projeto',
                planilha: 'projeto/planilhaReadequada',
            }),
        },
        methods: {
            ...mapActions({
                buscaPlanilhaReadequada: 'projeto/buscaPlanilhaReadequada'
            }),
        },
    };
</script>
