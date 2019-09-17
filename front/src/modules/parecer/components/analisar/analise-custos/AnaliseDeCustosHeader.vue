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
        <s-confirmacao-dialog
            v-model="dialog"
            text="Ao restaurar a planilha as alterações realizadas serão descartadas!"
            @dialog-response="$event && restaurarPlanilha()"
        />
        <s-progresso-dialog
            v-model="loadingRestore"
            label="Aguarde, restaurando planilha"
        />
    </v-container>
</template>
<script>

import { mapActions, mapGetters } from 'vuex';
import MxPlanilha from '@/mixins/planilhas';
import MxUtils from '@/mixins/utils';
import SProgressoDialog from '@/components/SalicProgressoDialog';
import SConfirmacaoDialog from '@/components/SalicConfirmacaoDialog';

const dataDefaults = {
    calculos: {
        totalSolicitado: 0,
        totalSugerido: 0,
        totalInconsistencias: 0,
        fontes: {},
        produtos: {},
        etapas: {},
    },
};

export default {
    name: 'AnaliseDeCustosHeader',
    components: { SConfirmacaoDialog, SProgressoDialog },
    mixins: [MxPlanilha, MxUtils],

    props: {
        planilha: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            dialog: false,
            loadingRestore: false,
            calculos: dataDefaults.calculos,
        };
    },

    computed: {
        ...mapGetters({
            produto: 'parecer/getProduto',
        }),
    },

    watch: {
        planilha: {
            handler(val) {
                if (Object.keys(val).length > 0) {
                    this.calculos = Object.assign({}, this.$options.data().calculos);
                    this.calcularTotais(val);
                }
            },
            deep: true,
        },
    },

    methods: {
        ...mapActions({
            restaurarPlanilhaProduto: 'parecer/restaurarPlanilhaProduto',
            obterPlanilhaParecer: 'parecer/obterPlanilhaParaAnalise',
        }),
        calcularTotais(planilha) {
            if (!planilha) {
                return {};
            }
            planilha.forEach((item) => {
                this.calculos.totalSugerido += item.VlSugeridoParecerista;
                this.calculos.totalSolicitado += item.VlSolicitado;
                this.calculos.totalInconsistencias += (item.stCustoPraticadoParc === 1
                    && this.stripTags(item.dsJustificativaParecerista).length === 0) ? 1 : 0;
            });
            return true;
        },
        restaurarPlanilha() {
            if (Object.keys(this.produto).length === 0) {
                return false;
            }
            this.dialog = false;
            this.loadingRestore = true;
            const params = {
                id: this.produto.idProduto,
                idPronac: this.produto.IdPRONAC,
                stPrincipal: this.produto.stPrincipal,
            };
            this.restaurarPlanilhaProduto(params).then(() => {
                this.obterPlanilhaParecer(params);
            }).finally(() => {
                this.loadingRestore = false;
            });

            return true;
        },
    },
};
</script>
