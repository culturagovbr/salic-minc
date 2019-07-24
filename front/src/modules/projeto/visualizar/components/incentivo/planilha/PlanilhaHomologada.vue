<template>
    <div id="planilha-homologada">
        <Carregando
            v-if="loading"
            :text="'Procurando planilha'"/>
        <Planilha
            v-if="Object.keys(planilha).length > 0"
            :array-planilha="planilha"
            :agrupamentos="agrupamentos"
            :totais="totaisPlanilha"
        >
            <template
                slot="badge"
                slot-scope="slotProps"
            >
                <VChip
                    v-if="slotProps.planilha.vlAprovado"
                    outline="outline"
                    label="label"
                    color="#565555"
                >
                    R$ {{ formatarParaReal(slotProps.planilha.vlAprovado) }}
                </VChip>
            </template>
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
import MxPlanilha from '@/mixins/planilhas';

export default {
    name: 'PlanilhaPropostaHomologada',
    components: {
        Carregando,
        Planilha,
        PlanilhaItensHomologados,
    },
    mixins: [MxPlanilha],
    data() {
        return {
            loading: true,
            semResposta: false,
            mensagem: '',
            totaisPlanilha: [
                {
                    label: 'Valor Aprovado',
                    column: 'vlAprovado',
                },
            ],
            agrupamentos: ['FonteRecurso', 'Produto', 'Etapa', 'UF', 'Municipio'],
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
