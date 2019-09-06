<template>
    <v-container>
        <template
            v-if="loading"
            xs9
            offset-xs1
        >
            <carregando
                :text="'Montando planilha orçamentária...'"
            />
        </template>
        <template
            v-else
        >
            <v-layout
                row
                justify-center
            >
                <v-flex
                    xs4
                    md4
                    sm4
                >
                    <v-card
                        class="mb-3"
                        max-width="500"
                    >
                        <v-card-title
                            class="grey lighten-5 headline justify-center"
                        >
                            {{ getResumoPlanilha.statusPlanilha }}
                        </v-card-title>
                    </v-card>
                </v-flex>
            </v-layout>
            <v-layout
                row
            >
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
                                    ATIVO
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
                                        {{ getResumoPlanilha.PlanilhaAtivaTotal | filtroFormatarParaReal }}
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
                                    READEQUADO
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
                                        {{ getResumoPlanilha.PlanilhaReadequadaTotal | filtroFormatarParaReal }}
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
                                    DIFERENÇA
                                </span>
                            </v-toolbar-title>
                            <v-spacer />
                        </v-toolbar>
                        <v-card-text
                            :class="classDiferenca"
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
                                        {{ getResumoPlanilha.diferenca | filtroFormatarParaReal }}
                                    </span>
                                </v-flex>
                            </v-layout>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
            <v-layout
                row
            >
                <v-flex
                    xs12
                    sm12
                    md12
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
                                <template slot-scope="slotProps">
                                    <s-planilha-itens-readequacao
                                        :table="slotProps.itens"
                                        :readonly="true"
                                    />
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
                            <v-btn
                                round
                                flat
                                class="light-green lighten-3 ml-5 mt-0 mb-0"
                                @click.stop="dialogLegenda = true"
                            >
                                <v-icon>
                                    format-list-bulleted
                                </v-icon>
                                legenda de cores
                            </v-btn>
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
                                <template slot-scope="slotProps">
                                    <s-planilha-itens-readequacao
                                        :table="slotProps.itens"
                                        :readonly="readonly"
                                    />
                                </template>
                            </s-planilha>
                        </div>
                    </resize-panel>
                </v-flex>
                <v-dialog
                    v-model="dialogLegenda"
                    width="350"
                >
                    <legenda-planilha/>
                </v-dialog>
            </v-layout>
        </template>
    </v-container>
</template>
<script>
import _ from 'lodash';
import { mapActions, mapGetters } from 'vuex';
import Carregando from '@/components/CarregandoVuetify';
import SPlanilha from '@/components/Planilha/PlanilhaV2';
import ResizePanel from '@/components/resize-panel/ResizeSplitPane';
import SPlanilhaTiposVisualizacaoButtons from '@/components/Planilha/PlanilhaTiposVisualizacaoButtons';
import SPlanilhaItensReadequacao from '../planilha/PlanilhaItensReadequacao';
import MxPlanilha from '@/mixins/planilhas';
import LegendaPlanilha from './LegendaPlanilha';

export default {
    name: 'ComparacaoPlanilha',
    components: {
        Carregando,
        ResizePanel,
        SPlanilha,
        SPlanilhaTiposVisualizacaoButtons,
        SPlanilhaItensReadequacao,
        LegendaPlanilha,
    },
    mixins: [
        MxPlanilha,
    ],
    props: {
        dadosReadequacao: {
            type: [Array, Object],
            default: () => {},
        },
        readonly: {
            type: Boolean,
            default: false,
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
            loaded: {
                ativa: false,
                readequada: false,
            },
            loading: true,
            dialogLegenda: false,
        };
    },
    computed: {
        ...mapGetters({
            getPlanilha: 'readequacao/getPlanilha',
            getPlanilhaAtiva: 'readequacao/getPlanilhaAtiva',
            getResumoPlanilha: 'readequacao/getResumoPlanilha',
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
        classDiferenca() {
            if (this.getResumoPlanilha.statusPlanilha === 'Redução') {
                return 'red lighten-3';
            }
            if (this.getResumoPlanilha.statusPlanilha === 'Complementação') {
                return 'green lighten-3';
            }
            return '';
        },
    },
    watch: {
        getPlanilha: {
            handler() {
                this.loaded.readequada = true;
            },
            deep: true,
        },
        getPlanilhaAtiva: {
            handler() {
                this.loaded.ativa = true;
            },
            deep: true,
        },
        loaded: {
            handler(value) {
                const fullyLoaded = _.keys(value).every(i => value[i]);
                if (fullyLoaded) {
                    this.loading = false;
                }
            },
            deep: true,
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
        this.obterUnidadesPlanilha({
            idPronac: this.dadosReadequacao.idPronac,
        });
        this.calcularResumoPlanilha({
            idPronac: this.dadosReadequacao.idPronac,
            idTipoReadequacao: this.dadosReadequacao.idTipoReadequacao,
        });
    },
    methods: {
        ...mapActions({
            obterPlanilha: 'readequacao/obterPlanilha',
            obterPlanilhaAtiva: 'readequacao/obterPlanilhaAtiva',
            obterUnidadesPlanilha: 'readequacao/obterUnidadesPlanilha',
            calcularResumoPlanilha: 'readequacao/calcularResumoPlanilha',
        }),
        isOptionActive(index) {
            return this.opcoesDeVisualizacao.includes(index);
        },
    },
};
</script>
