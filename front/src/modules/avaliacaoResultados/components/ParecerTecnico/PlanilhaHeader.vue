<template>
    <v-container
        grid-list-md
        text-xs-center
    >
        <v-layout
            row
            wrap
        >
            <v-flex
                v-for="(item, index) in trending"
                :key="'trending' + index"
                lg4
                sm12
            >
                <linear-statistic
                    :title="item.subheading"
                    :sub-title="item.caption"
                    :icon="item.icon.label"
                    :color="item.icon.color"
                    :value="item.linear.value"
                    :headline="item.headline"
                />
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';

import MxPlanilha from '@/mixins/planilhas';
import LinearStatistic from '@/components/widgets/statistic/LinearStatistic';

export default {
    name: 'ParecerTecnicoPlanilhaHeader',
    components: { LinearStatistic },
    mixins: [MxPlanilha],
    props: {
        dados: {
            type: Object,
            default: () => {
            },
        },
    },

    data() {
        return {
            comprovacao: {
                vlTotalComprovar: 0,
                vlAprovado: 0,
                vlComprovado: 0,
            },
            estatisticas: {
                qtTotalComprovante: 0,
                qtComprovantesValidadosProjeto: 0,
                qtComprovantesRecusadosProjeto: 0,
                qtComprovantesNaoAvaliados: 0,
                vlComprovadoProjeto: 0,
                vlComprovadoValidado: 0,
                vlComprovadoRecusado: 0,
                vlNaoComprovado: 0,
            },
        };
    },


    computed: {
        ...mapGetters({
            estatisticasGetter: 'avaliacaoResultados/getEstatisticasAvaliacao',
        }),
        trending() {
            return [
                {
                    subheading: 'Comprovação',
                    headline: `<b class="error--text text-darken-4">R$ ${this.formatarParaReal(this.comprovacao.vlTotalComprovar)}</b>`
                        + ` não comprovado de <b>R$ ${this.formatarParaReal(this.comprovacao.vlAprovado)}</b>`,
                    caption: `R$ ${this.formatarParaReal(this.comprovacao.vlComprovado)}`,
                    percent: this.calcularPercentual(this.comprovacao.vlComprovado, this.comprovacao.vlAprovado),
                    icon: {
                        label: 'trending_up',
                        color: 'success',
                    },
                    linear: {
                        value: this.calcularPercentual(this.comprovacao.vlComprovado, this.comprovacao.vlAprovado),
                        color: 'success',
                    },
                },
                {
                    subheading: 'Comprovantes validados',
                    headline: `${this.estatisticas.qtComprovantesValidadosProjeto}`
                        + ` comprovante(s) validado(s) de ${this.estatisticas.qtTotalComprovante}`,
                    caption: `R$ ${this.formatarParaReal(this.estatisticas.vlComprovadoValidado)}`,
                    percent: this.calcularPercentual(this.estatisticas.qtComprovantesValidadosProjeto, this.estatisticas.qtTotalComprovante),
                    icon: {
                        label: 'arrow_upward',
                        color: 'info',
                    },
                    linear: {
                        value: this.calcularPercentual(this.estatisticas.qtComprovantesValidadosProjeto, this.estatisticas.qtTotalComprovante),
                        color: 'info',
                    },
                },
                {
                    subheading: 'Comprovantes recusados',
                    headline: `${this.estatisticas.qtComprovantesRecusadosProjeto}`
                        + ` comprovante(s) recusado(s) de ${this.estatisticas.qtTotalComprovante}`,
                    caption: `R$ ${this.formatarParaReal(this.estatisticas.vlComprovadoRecusado)}`,
                    percent: this.calcularPercentual(this.estatisticas.qtComprovantesRecusadosProjeto, this.estatisticas.qtTotalComprovante),
                    icon: {
                        label: 'trending_down',
                        color: 'error',
                    },
                    linear: {
                        percent: this.calcularPercentual(this.estatisticas.qtComprovantesRecusadosProjeto, this.estatisticas.qtTotalComprovante),
                        color: 'error',
                    },
                },
            ];
        },
    },

    watch: {
        estatisticasGetter(val) {
            if (val) {
                this.estatisticas = val;
            }

            this.comprovacao = {
                vlTotalComprovar: this.dados.items.vlTotalComprovar,
                vlAprovado: this.dados.items.vlAprovado,
                vlComprovado: this.dados.items.vlComprovado,
            };
        },
    },

    mounted() {
        this.buscarEstatisticasAvaliacao();
    },

    methods: {
        ...mapActions({
            buscarEstatisticasAvaliacaoAction: 'avaliacaoResultados/buscarEstatisticasAvaliacao',
        }),
        calcularPercentual(valor, total) {
            if (!valor) {
                return 0;
            }
            return parseFloat(((valor / total) * 100).toFixed(0));
        },
        buscarEstatisticasAvaliacao() {
            this.buscarEstatisticasAvaliacaoAction(this.$route.params.id);
        },
    },
};
</script>
