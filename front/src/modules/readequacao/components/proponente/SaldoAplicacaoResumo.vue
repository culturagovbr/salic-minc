<template>
    <v-container>
        <v-layout>
            <v-flex
                xs12
                sm4
                md4
            >
                <v-card
                    class="mx-auto mb-2"
                    max-width="300"
                >
                    <v-toolbar
                        card
                        dense
                    >
                        <v-toolbar-title>
                            <span class="subheading">
                                SALDO DECLARADO
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
                                    class="display-1 font-weight-light"
                                >
                                    {{ SaldoDeclarado | filtroFormatarParaReal }}
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
                    max-width="300"
                >
                    <v-toolbar
                        card
                        dense
                    >
                        <v-toolbar-title>
                            <span class="subheading">
                                UTILIZADO
                            </span>
                        </v-toolbar-title>
                        <v-spacer />
                    </v-toolbar>
                    <v-card-text
                        :class="colorUtilizado"
                    >
                        <v-layout
                            justify-space-between
                        >
                            <v-flex text-xs-left>
                                <span class="subheading font-weight-light mr-1">
                                    R$
                                </span>
                                <span
                                    class="display-1 font-weight-light"
                                >
                                    {{ SaldoUtilizado | filtroFormatarParaReal }}
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
                    max-width="300"
                >
                    <v-toolbar
                        card
                        dense
                    >
                        <v-toolbar-title>
                            <span class="subheading">
                                DISPONÍVEL
                            </span>
                        </v-toolbar-title>
                        <v-spacer />
                    </v-toolbar>
                    <v-card-text
                        :class="colorDisponivel"
                    >
                        <v-layout
                            justify-space-between
                        >
                            <v-flex text-xs-left>
                                <span class="subheading font-weight-light mr-1">
                                    R$
                                </span>
                                <span
                                    class="display-1 font-weight-light"
                                >
                                    {{ SaldoDisponivel | filtroFormatarParaReal }}
                                </span>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
        <v-layout
            v-if="SaldoDisponivel < 0"
        >
            <v-flex
                xs12
                sm12
            >
                <v-chip
                    color="red accent-1"
                >
                    Valor ultrapassado! Reduza sua planilha em R$ {{ SaldoDisponivel }}.
                </v-chip>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import { utils } from '@/mixins/utils';

export default {
    name: 'SaldoAplicacaoResumo',
    mixins: [
        utils,
    ],
    props: {
        SaldoDeclarado: {
            type: [Number, String],
            default: 0,
        },
        SaldoDisponivel: {
            type: [Number, String],
            default: 0,
        },
        SaldoUtilizado: {
            type: [Number, String],
            default: 0,
        },
    },
    data() {
        return {
        };
    },
    computed: {
        colorDisponivel() {
            let cor = '';
            if (this.SaldoDisponivel < 0) {
                cor = 'red lighten-3';
            } else if (this.SaldoDisponivel > 0) {
                cor = 'green lighten-3';
            }
            return cor;
        },
        colorUtilizado() {
            let cor = '';
            if (this.SaldoUtilizado > this.SaldoDeclarado) {
                cor = 'red lighten-3';
            }
            return cor;
        },
    },
};
</script>
