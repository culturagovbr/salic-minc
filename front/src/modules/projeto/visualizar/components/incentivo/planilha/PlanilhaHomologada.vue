<template>
    <div id="planilha-homologada">
        <Carregando
            v-if="loading"
            :text="'Procurando planilha'"/>
        <Planilha
            v-if="Object.keys(planilha).length > 0"
            :array-planilha="planilha">
            <template slot-scope="slotProps">
                <PlanilhaItensHomologados :table="slotProps.itens"/>
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
import PlanilhaItensHomologados from '@/components/Planilha/PlanilhaItensHomologados';

export default {
    name: 'PlanilhaPropostaHomologada',
    components: {
        Carregando,
        Planilha,
        PlanilhaItensHomologados,
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
            planilha: 'projeto/planilhaHomologada',
        }),
    },
    watch: {
        dadosProjeto(value) {
            this.buscaPlanilhaHomologada(value.idPronac);
        },
        planilha() {
            this.loading = false;
        },
    },
    created() {
        this.buscaPlanilhaHomologada(this.dadosProjeto.idPronac);
    },
    methods: {
        ...mapActions({
            buscaPlanilhaHomologada: 'projeto/buscaPlanilhaHomologada',
        }),
    },
};
</script>
