<template>
    <v-container
        grid-list-md
        text-xs-center
    >
        <v-layout
            row
            wrap
        >

            <v-flex lg4 sm12 v-for="(item, index) in trending" :key="'trending' + index">
                <linear-statistic
                    :title="item.subheading"
                    :sub-title="item.caption"
                    :icon="item.icon.label"
                    :color="item.icon.color"
                    :value="item.linear.value"
                    :headline="item.headline"
                >
                </linear-statistic>
            </v-flex>
            <v-flex
                xs12
                sm4
                md4
            >
                <v-card
                    class="mx-auto mb-2"
                    max-width="600"
                >
                    <v-toolbar
                        card
                        dense
                    >
                        <v-toolbar-title>
                            <span class="subheading">
                                APROVADO
                            </span>
                        </v-toolbar-title>
                        <v-spacer/>
                    </v-toolbar>

                    <v-card-text>
                        <v-layout
                            justify-space-between
                        >
                            <v-flex text-xs-left>
                                <span class="subheading font-weight-light mr-1">
                                    R$
                                </span>
                                <span
                                    class="display-2 font-weight-light"
                                >
                                    {{ dados.items.vlAprovado | filtroFormatarParaReal }}
                                </span>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex
                xs12
                sm4
                md4
            >
                <v-card
                    class="mx-auto mb-2"
                    max-width="600"
                >
                    <v-toolbar
                        card
                        dense
                    >
                        <v-toolbar-title>
                            <span class="subheading">
                                COMPROVADO
                            </span>
                        </v-toolbar-title>
                    </v-toolbar>

                    <v-card-text>
                        <v-layout
                            justify-space-between
                        >
                            <v-flex text-xs-left>
                                <span class="subheading font-weight-light mr-1 green--text text--darken-4">
                                    R$
                                </span>
                                <span
                                    class="display-2 font-weight-light green--text text--darken-4"
                                >
                                    {{ dados.items.vlComprovado | filtroFormatarParaReal }}
                                </span>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex
                xs12
                sm4
                md4
            >
                <v-card
                    class="mx-auto mb-2"
                    max-width="600"
                >
                    <v-toolbar
                        card
                        dense
                    >
                        <v-toolbar-title>
                            <span class="subheading">
                                A COMPROVAR
                            </span>
                        </v-toolbar-title>
                    </v-toolbar>

                    <v-card-text>
                        <v-layout
                            justify-space-between
                        >
                            <v-flex text-xs-left>
                                <span class="subheading font-weight-light mr-1 red--text text--darken-4">
                                    R$
                                </span>
                                <span
                                    class="display-2 font-weight-light red--text text--darken-4"
                                >
                                    {{ dados.items.vlTotalComprovar | filtroFormatarParaReal }}
                                </span>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>

    import MxPlanilha from '@/mixins/planilhas';
    import LinearStatistic from "@/components/widgets/statistic/LinearStatistic";

    export default {
        name: 'ParecerTecnicoPlanilhaHeader',
        components: {LinearStatistic},
        mixins: [MxPlanilha],
        props: {
            dados: {
                type: Object,
                default: () => {
                },
            },
        },
        computed: {
            trending() {
                return [
                    {
                        subheading: "Comprovado",
                        headline: `de R$ ${this.formatarParaReal(this.dados.items.vlAprovado)}`,
                        caption: `R$ ${this.formatarParaReal(this.dados.items.vlComprovado)}`,
                        percent: (this.dados.items.vlComprovado / this.dados.items.vlAprovado) * 100,
                        icon: {
                            label: "trending_up",
                            color: "success"
                        },
                        linear: {
                            value: (this.dados.items.vlComprovado / this.dados.items.vlAprovado) * 100,
                            color: "success"
                        }
                    },
                    {
                        subheading: "A comprovar",
                        headline: `de R$ ${this.formatarParaReal(this.dados.items.vlAprovado)}`,
                        caption: `R$ ${this.formatarParaReal(this.dados.items.vlTotalComprovar)}`,
                        percent: (this.dados.items.vlTotalComprovar / this.dados.items.vlAprovado) * 100,
                        icon: {
                            label: "trending_down",
                            color: "error"
                        },
                        linear: {
                            value: (this.dados.items.vlTotalComprovar / this.dados.items.vlAprovado) * 100,
                            color: "error"
                        }
                    },
                    {
                        subheading: "Orders",
                        headline: "5,00",
                        caption: "increase",
                        percent: 50,
                        icon: {
                            label: "arrow_upward",
                            color: "info"
                        },
                        linear: {
                            value: 50,
                            color: "info"
                        }
                    }
                ];
            }
        }
    };
</script>
