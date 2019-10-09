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
                sm6
                md6
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
                                SOLICITADO
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
                                    {{ calculos.totalSolicitado | filtroFormatarParaReal }}
                                </span>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
            <v-flex
                xs12
                sm6
                md6
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
                                SUGERIDO
                            </span>
                        </v-toolbar-title>
                        <v-tooltip
                            bottom
                        >
                            <v-badge
                                slot="activator"
                                :value="calculos.totalInconsistencias > 0"
                                class="ml-2"
                                overlap
                                left
                                color="red"
                            >
                                <template v-slot:badge>
                                    <span>{{ calculos.totalInconsistencias }}</span>
                                </template>

                                <v-icon
                                    v-if="calculos.totalInconsistencias > 0"

                                    color="grey"
                                >
                                    error
                                </v-icon>
                            </v-badge>
                            <span> Existem ({{ calculos.totalInconsistencias }}) inconsistências na planilha </span>
                        </v-tooltip>
                        <v-spacer />
                        <v-tooltip
                            bottom
                        >
                            <v-btn
                                slot="activator"
                                icon
                                @click="dialog = !dialog"
                            >
                                <v-icon>settings_backup_restore</v-icon>
                            </v-btn>
                            <span>Restaurar planilha original</span>
                        </v-tooltip>
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
                                    {{ calculos.totalSugerido | filtroFormatarParaReal }}
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
