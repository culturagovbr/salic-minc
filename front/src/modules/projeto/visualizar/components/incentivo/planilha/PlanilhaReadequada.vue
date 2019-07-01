<template>
    <div id="planilha-readequada">
        <Carregando
            v-if="loading"
            :text="'Procurando planilha'"/>

        <Planilha
            v-if="Object.keys(planilha).length > 0"
            :array-planilha="planilha">
            <template slot-scope="slotProps">
                <PlanilhaItensReadequados :table="slotProps.itens"/>
            </template>
        </Planilha>
        <div
            v-if="semResposta"
            class="card-panel padding 20 center-align">{{ mensagem }}</div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/Carregando';
import Planilha from '@/components/Planilha/Planilha';
import PlanilhaItensReadequados from '@/components/Planilha/PlanilhaItensReadequados';

export default {
    name: 'PlanilhaPropostaReadequada',
    components: {
        Carregando,
        Planilha,
        PlanilhaItensReadequados,
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
            planilha: 'projeto/planilhaReadequada',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.buscaPlanilhaReadequada(value.idPronac);
        },
        planilha() {
            this.loading = false;
        },
    },
    mounted() {
        this.buscaPlanilhaReadequada(this.dadosProjeto.idPronac);
    },
    methods: {
        ...mapActions({
            buscaPlanilhaReadequada: 'projeto/buscaPlanilhaReadequada',
        }),
    },
};
</script>
