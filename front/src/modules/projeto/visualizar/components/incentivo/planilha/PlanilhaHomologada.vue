<template>
    <div id="planilha-homologada">
        <Carregando v-if="loading" :text="'Procurando planilha'"></Carregando>
        <Planilha v-if="Object.keys(planilha).length > 0" :arrayPlanilha="planilha">
            <template slot-scope="slotProps">
                <PlanilhaItensHomologados :table="slotProps.itens"></PlanilhaItensHomologados>
            </template>
        </Planilha>
        <div v-if="semResposta" class="card-panel padding 20 center-align">{{ mensagem }}</div>
    </div>
</template>

<script>
    import Carregando from '@/components/Carregando';
    import Planilha from '@/components/Planilha/Planilha';
    import PlanilhaItensHomologados from '@/components/Planilha/PlanilhaItensHomologados';
    import { mapActions, mapGetters } from 'vuex';

    export default {
        name: 'PlanilhaPropostaHomologada',
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
            PlanilhaItensHomologados,
        },
        created() {
            this.buscaPlanilhaHomologada(this.dadosProjeto.idPronac);
        },
        watch: {
            dadosProjeto(value) {
                this.buscaPlanilhaHomologada(value.idPronac);
            },
            planilha() {
                this.loading = false;
            },
        },
        computed: {
            ...mapGetters({
                dadosProjeto: 'projeto/projeto',
                planilha: 'projeto/planilhaHomologada',
            }),
        },
        methods: {
            ...mapActions({
                buscaPlanilhaHomologada: 'projeto/buscaPlanilhaHomologada',
            }),
        },
    };
</script>
