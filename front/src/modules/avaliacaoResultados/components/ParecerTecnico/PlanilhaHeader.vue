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
                        <v-spacer />
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

export default {
    name: 'ParecerTecnicoPlanilhaHeader',
    mixins: [MxPlanilha],
    props: {
        dados: {
            type: Object,
            default: () => {},
        },
    },
};
</script>
