<template>
    <div id="planilha-congelada">
        <Carregando v-if="loading" :text="'Procurando planilha'"></Carregando>
        <Planilha v-if="Object.keys(planilha).length > 0" :arrayPlanilha="planilha">
            <template slot-scope="slotProps">
                <PlanilhaItensAutorizados :table="slotProps.itens"></PlanilhaItensAutorizados>
            </template>
        </Planilha>
        <div v-if="semResposta" class="card-panel padding 20 center-align">{{ mensagem }}</div>
    </div>
</template>

<script>
    import Carregando from '@/components/Carregando';
    import Planilha from '@/components/Planilha/Planilha';
    import PlanilhaItensAutorizados from '@/components/Planilha/PlanilhaItensAutorizados';
    import { mapActions, mapGetters } from 'vuex';

    export default {
        name: 'PlanilhaPropostaAutorizada',
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
            PlanilhaItensAutorizados,
        },
        mounted() {
            this.buscaPlanilhaAutorizada(this.dadosProjeto.idPreProjeto);
        },
        watch: {
            dadosProjeto(value) {
                if (typeof value !== 'undefined') {
                    this.buscaPlanilhaAutorizada(value.idPreProjeto);
                }
            },
            planilha() {
                this.loading = false;
            },
        },
        computed: {
            ...mapGetters({
                dadosProjeto: 'projeto/projeto',
                planilha: 'projeto/planilhaAutorizada',
            }),
        },
        methods: {
            ...mapActions({
                buscaPlanilhaAutorizada: 'projeto/buscaPlanilhaAutorizada',
            }),
        },
    };
</script>
