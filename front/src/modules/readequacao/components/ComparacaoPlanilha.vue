<template>
    <v-layout>
        <v-flex
            v-if="Object.keys(getPlanilha).length > 0"
            flat
        >
            <s-planilha-tipos-visualizacao-buttons v-model="opcoesDeVisualizacao" />
            <resize-panel
                v-if="Object.keys(getPlanilha).length > 0"
                :allow-resize="true"
                :size="sizePanel"
                units="percents"
                split-to="columns"
            >
                <div
                    v-if="compararPlanilha === true"
                    slot="firstPane"
                >
                    <v-chip
                        color="blue lighten-4"
                    >
                        <v-icon>assignment</v-icon>
                        Planilha ativa
                    </v-chip>
                    <s-planilha
                        :array-planilha="getPlanilhaAtiva"
                        :expand-all="expandirTudo"
                        :list-items="mostrarListagem"
                        :agrupamentos="agrupamentos"
                        :totais="totaisPlanilha"
                    >
                        <template
                            slot="badge"
                            slot-scope="slotProps"
                        >
                            <v-chip
                                v-if="slotProps.planilha.vlAprovado"
                                outline="outline"
                                label="label"
                                color="#565555"
                            >
                                R$ {{ formatarParaReal(slotProps.planilha.vlAprovado) }}
                            </v-chip>
                        </template>
                    </s-planilha>
                </div>
                <div
                    slot="secondPane"
                >
                    <v-chip
                        color="orange accent-1"
                    >
                        <v-icon>edit</v-icon>
                        Planilha readequada
                    </v-chip>
                    <s-planilha
                        :array-planilha="getPlanilha"
                        :expand-all="expandirTudo"
                        :list-items="mostrarListagem"
                        :agrupamentos="agrupamentos"
                        :totais="totaisPlanilha"
                    >
                        <template
                            slot="badge"
                            slot-scope="slotProps"
                        >
                            <v-chip
                                v-if="slotProps.planilha.vlAprovado"
                                outline="outline"
                                label="label"
                                color="#565555"
                            >
                                R$ {{ formatarParaReal(slotProps.planilha.vlAprovado) }}
                            </v-chip>
                        </template>
                    </s-planilha>
                </div>
            </resize-panel>
        </v-flex>
    </v-layout>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import SPlanilha from '@/components/Planilha/PlanilhaV2';
import ResizePanel from '@/components/resize-panel/ResizeSplitPane';
import SPlanilhaTiposVisualizacaoButtons from '@/components/Planilha/PlanilhaTiposVisualizacaoButtons';
import MxPlanilha from '@/mixins/planilhas';

export default {
    name: 'ComparacaoTextual',
    components: {
        ResizePanel,
        SPlanilha,
        SPlanilhaTiposVisualizacaoButtons,
    },
    mixins: [
        MxPlanilha,
    ],
    props: {
        dadosReadequacao: {
            type: [Array, Object],
            default: () => {},
        },
    },
    data() {
        return {
            totaisPlanilha: [
                {
                    label: 'Valor Aprovado',
                    column: 'vlAprovado',
                },
            ],
            sizePanel: 49.8,
            opcoesDeVisualizacao: [0],
            agrupamentos: [
                'FonteRecurso',
                'Produto',
                'Etapa',
                'UF',
                'Municipio',
            ],
        };
    },
    computed: {
        ...mapGetters({
            getPlanilha: 'readequacao/getPlanilha',
            getPlanilhaAtiva: 'readequacao/getPlanilhaAtiva',
        }),
        expandirTudo() {
            return this.isOptionActive(0);
        },
        compararPlanilha() {
            return this.isOptionActive(1);
        },
        mostrarListagem() {
            return this.isOptionActive(2);
        },        
    },
    mounted() {
        this.obterPlanilha({
            idPronac: this.dadosReadequacao.idPronac,
            idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
        });
        this.obterPlanilhaAtiva({
            idPronac: this.dadosReadequacao.idPronac,
        });
    },
    methods: {
        ...mapActions({
            obterPlanilha: 'readequacao/obterPlanilha',
            obterPlanilhaAtiva: 'readequacao/obterPlanilhaAtiva',
        }),
        isOptionActive(index) {
            return this.opcoesDeVisualizacao.includes(index);
        },
    },
};
</script>
