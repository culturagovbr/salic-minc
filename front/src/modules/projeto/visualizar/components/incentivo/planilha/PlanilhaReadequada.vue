<template>
    <div id="planilha-readequada">
        <Carregando
            v-if="loading"
            :text="'Procurando planilha'"
        />

        <Planilha
            v-if="Object.keys(planilha).length > 0"
            :array-planilha="planilha"
            :agrupamentos="['FonteRecurso', 'Produto', 'Etapa', 'UF', 'Municipio']"
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
                <PlanilhaItensReadequados :table="slotProps.itens" />
            </template>
        </Planilha>
        <div
            v-if="semResposta"
            class="card-panel padding 20 center-align"
        >
            {{ mensagem }}
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/Carregando';
import Planilha from '@/components/Planilha/Planilha';
import PlanilhaItensReadequados from '@/components/Planilha/PlanilhaItensReadequados';
import MxPlanilha from '@/mixins/planilhas';

export default {
    name: 'PlanilhaPropostaReadequada',
    components: {
        Carregando,
        Planilha,
        PlanilhaItensReadequados,
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
                {
                    label: 'Valor Comprovado',
                    column: 'VlComprovado',
                },
            ],
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
