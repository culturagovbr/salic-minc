<template>
    <div>
        <analise-de-custos-buttons
            :custom-buttons="customButtons"
            v-model="opcoesDeVisualizacao" />
        <resize-panel
            v-if="Object.keys(planilha).length > 0"
            :allow-resize="true"
            :size="sizePanel"
            units="percents"
            split-to="columns"
        >
            <div
                v-if="compararPlanilha === true"
                slot="firstPane"
            >
                <s-planilha
                    :array-planilha="planilhaParecer"
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
                            v-if="slotProps.planilha.VlSolicitado"
                            outline="outline"
                            label="label"
                            color="#565555"
                        >
                            R$ {{ formatarParaReal(slotProps.planilha.VlSolicitado) }}
                        </v-chip>
                    </template>
                    <template slot-scope="slotProps">
                        <s-analise-de-custos-planilha-itens-solicitado :table="slotProps.itens" />
                    </template>
                </s-planilha>
            </div>
            <div
                slot="secondPane"
            >
                <s-planilha
                    :array-planilha="planilhaParecer"
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
                            outline="outline"
                            label="label"
                            color="#565555"
                        >
                            R$ {{ formatarParaReal(slotProps.planilha.VlSugeridoParecerista) }}
                        </v-chip>
                    </template>
                    <template slot-scope="slotProps">
                        <s-analise-outros-produtos-planilha-itens-visualizar
                            :visualizar-todas-justificativas="habilitarVisualizarTodasJustificativas"
                            :table="slotProps.itens" />
                    </template>
                </s-planilha>
            </div>
        </resize-panel>
        <s-carregando
            v-else
            text="Carregando Planilha"
        />
        <v-snackbar
            :value="totalItensSelecionados > 0"
            :timeout="0"
            color="cyan darken-2"
        >
            <v-btn
                dark
                flat
                class="ml-0"
                @click="filtrarItensSelecionados = !filtrarItensSelecionados"
            >
                <span v-if="!filtrarItensSelecionados">
                    <v-icon
                        class="mr-2 left"
                    >
                        visibility
                    </v-icon>
                    Visualizar itens selecionados ({{ totalItensSelecionados }})
                </span>
                <span v-else>
                    <v-icon
                        class="mr-2 left"
                    >
                        visibility_off
                    </v-icon>
                    Voltar planilha completa ({{ totalItensSelecionados }})
                </span>
            </v-btn>
        </v-snackbar>
    </div>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import SPlanilha from '@/components/Planilha/Planilha';
import SAnaliseDeCustosPlanilhaItensSolicitado from './analisar/analise-custos/AnaliseDeCustosPlanilhaItensSolicitado';
import SCarregando from '@/components/CarregandoVuetify';
import ResizePanel from '@/components/resize-panel/ResizeSplitPane';
import MxPlanilha from '@/mixins/planilhas';
import AnaliseDeCustosButtons from '@/modules/parecer/components/analisar/analise-custos/AnaliseDeCustosButtons';
import SAnaliseOutrosProdutosPlanilhaItensVisualizar from './outros-produtos/OutrosProdutosPlanilhaItensVisualizar';

export default {
    name: 'VisualizacaoAnaliseProdutoPlanilha',
    components: {
        AnaliseDeCustosButtons,
        ResizePanel,
        SAnaliseDeCustosPlanilhaItensSolicitado,
        SAnaliseOutrosProdutosPlanilhaItensVisualizar,
        SPlanilha,
        SCarregando,
    },
    mixins: [MxPlanilha],
    props: {
        produto: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            customButtons: [
                { icon: 'remove_red_eye', tooltip: 'Visualizar todas as justificativas' },
            ],
            opcoesDeVisualizacao: [0],
            sizePanel: 49.8,
            totalItensSelecionados: 0,
            filtrarItensSelecionados: false,
            totaisPlanilha: [
                {
                    label: 'Valor Sugerido',
                    column: 'VlSugeridoParecerista',
                },
                {
                    label: 'Valor Solicitado',
                    column: 'VlSolicitado',
                },
            ],
            planilhaParecer: [],
            agrupamentos: ['FonteRecurso', 'Produto', 'Etapa', 'UF', 'Cidade'],
        };
    },
    computed: {
        ...mapGetters({
            planilha: 'parecer/getPlanilhaParecer',
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
        habilitarModoSelecao() {
            return this.isOptionActive(3);
        },
        habilitarVisualizarTodasJustificativas() {
            return this.isOptionActive(4);
        },
    },
    watch: {
        produto(value) {
            if (Object.keys(value).length > 0) {
                this.buscarPlanilha();
            }
        },
        planilha: {
            handler(val) {
                this.totalItensSelecionados = this.contarItensSelecionados(val);
                this.filtrarPlanilha();
            },
            deep: true,
        },
        filtrarItensSelecionados() {
            this.filtrarPlanilha();
        },
        totalItensSelecionados(val) {
            if (val === 0) {
                this.filtrarItensSelecionados = false;
            }
        },
    },
    created() {
        if (Object.keys(this.produto).length > 0) {
            this.buscarPlanilha();
        }
    },
    methods: {
        ...mapActions({
            obterPlanilhaParecer: 'parecer/obterPlanilhaParaAnalise',
            obterUnidades: 'planilha/obterUnidadesPlanilha',
        }),
        isOptionActive(index) {
            return this.opcoesDeVisualizacao.includes(index);
        },
        contarItensSelecionados(planilha) {
            if (planilha.length === 0) {
                return 0;
            }

            return planilha.reduce((soma, item) => (item.selecionado ? 1 : 0) + soma, 0);
        },
        buscarPlanilha() {
            const params = {
                id: this.produto.idProduto,
                idPronac: this.produto.idPronac,
                stPrincipal: this.produto.stPrincipal,
            };
            this.obterPlanilhaParecer(params);
            this.obterUnidades();
        },
        filtrarPlanilha() {
            this.planilhaParecer = this.filtrarItensSelecionados ? this.planilha.filter(item => item.selecionado) : this.planilha;
        },
    },
};
</script>
