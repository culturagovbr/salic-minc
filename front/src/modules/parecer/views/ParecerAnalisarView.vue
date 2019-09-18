<template>
    <v-container
        fluid
    >
        <s-carregando
            v-if="loadingProduto"
            text="Carregando produto"
        />
        <template v-else-if="produto && Object.keys(produto).length > 0">
            <analise-cabecalho
                :produto="produto"
                nome-rota-retorno="parecer-listar-view"
                @visualizarDiligencias="visualizarDiligencias($event)"
                @visualizarHistorico="visualizarHistorico($event)"
                @visualizarOutrosProdutos="visualizarOutrosProdutos($event)"
            />
            <v-stepper
                v-if="isDisponivelParaAnalise"
                v-model="currentStep"
                non-linear
            >
                <v-stepper-header>
                    <template v-for="(step, index) in arraySteps">
                        <v-stepper-step
                            :key="`${step.name}-step`"
                            :step="index + 1"
                            :editable="step.editable"
                            :complete="step.complete"
                            :rules="step.rules"
                        >
                            {{ step.label }}
                            <small v-if="step.message">
                                {{ step.message }}
                            </small>
                        </v-stepper-step>
                        <v-divider
                            v-if="index + 1 !== Object.keys(arraySteps).length"
                            :key="index + 1"
                        />
                    </template>
                </v-stepper-header>
                <v-stepper-items>
                    <v-stepper-content
                        v-for="(step, index) in arraySteps"
                        :key="`${step.name}-content`"
                        :step="index + 1"
                    >
                        <v-card
                            class="mb-5"
                            elevation="0"
                        >
                            <keep-alive>
                                <router-view
                                    v-if="(index + 1) === currentStep"
                                    :is-active="true"
                                    class="view"
                                />
                            </keep-alive>
                        </v-card>
                        <div class="text-xs-right">
                            <v-btn
                                color="primary"
                                @click="nextStep(index + 1)"
                            >
                                Próximo
                            </v-btn>
                        </div>
                    </v-stepper-content>
                </v-stepper-items>
            </v-stepper>
            <s-mensagem
                v-else-if="isDisponivelParaAssinatura"
                :url-retorno="`${urlAssinatura}?idDocumentoAssinatura=${produto.idDocumentoAssinatura}&${retornoAssinatura.toString()}`"
                texto="Você concluiu a análise do produto.
                Para finalizar a análise do projeto você deverá assinar o documento com o parecer!"
                msg-url-retorno="Ir para o documento"
                type="success"
            />
            <s-mensagem
                v-else
                :rota-retorno="{ name: 'parecer-listar-view'}"
                texto="Parecer finalizado com sucesso!"
                msg-url-retorno="Ir para lista de produtos"
                type="success"
            />
            <s-dialog-analise-outros-produtos
                v-model="dialogOutrosProdutos"
            />
            <s-dialog-diligencias
                v-if="isDisponivelParaAnalise"
                v-model="dialogDiligencias"
                :id-pronac="produto.idPronac"
                :tp-diligencia="TP_DILIGENCIA_ANALISE_TECNICA"
            />
            <s-analise-historico-produto-dialog
                v-model="dialogHistorico"
                :produto="produtoHistorico"
            />
        </template>
        <s-mensagem
            v-else
            :rota-retorno="{ name: 'parecer-listar-view'}"
            texto="Desculpe, houve um erro ao carregar o produto."
            msg-url-retorno="Voltar para lista de produtos"
            type="error"
        />
    </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import SCarregando from '@/components/CarregandoVuetify';
import MxDiligencia from '@/modules/diligencia/mixins/diligencia';
import MxConstantes from '@/modules/parecer/mixins/Constantes';
import SDialogAnaliseOutrosProdutos from '../components/outros-produtos/OutrosProdutosDialog';
import SDialogDiligencias from '@/modules/diligencia/components/SDialogDiligencias';
import SAnaliseHistoricoProdutoDialog from '@/modules/parecer/components/HistoricoProdutoDialog';
import SMensagem from '@/components/SalicMensagem';
import AnaliseCabecalho from '@/modules/parecer/components/analisar/AnaliseCabecalho';

export default {
    name: 'ParecerAnalisarView',
    components: {
        AnaliseCabecalho,
        SMensagem,
        SDialogDiligencias,
        SDialogAnaliseOutrosProdutos,
        SCarregando,
        SAnaliseHistoricoProdutoDialog,
    },
    mixins: [MxDiligencia, MxConstantes],

    data: () => ({
        currentStep: '1',
        dialogOutrosProdutos: false,
        dialogDiligencias: false,
        loadingProduto: true,
        dialogHistorico: false,
        produtoHistorico: {},
        urlAssinatura: '/assinatura/index/visualizar-projeto',
        retornoAssinatura: `origin=${encodeURIComponent('#/parecer/analise-inicial')}`,
        arraySteps: [
            {
                id: 1,
                label: 'Análise de conteúdo',
                message: '',
                name: 'analise-conteudo',
                complete: false,
                editable: true,
                rules: [() => true],
            },
            {
                id: 2,
                label: 'Análise de custos',
                message: '',
                name: 'analise-de-custos',
                complete: false,
                editable: true,
                rules: [() => true],
            },
            {
                id: 3,
                label: 'Consolidação',
                message: '',
                name: 'analise-consolidacao',
                complete: false,
                editable: true,
                rules: [() => true],
            },
            {
                id: 4,
                label: 'Finalizar análise',
                message: '',
                name: 'analise-finalizacao',
                complete: false,
                editable: true,
                rules: [() => true],
            },
        ],
        fab: false,
        hidden: false,
        tabs: null,
    }),

    computed: {
        ...mapGetters({
            produto: 'parecer/getProduto',
            analiseConteudo: 'parecer/getAnaliseConteudo',
        }),
        isDisponivelParaAnalise() {
            return !!this.produto
                && this.produto.siEncaminhamento === 4
                && this.produto.siAnalise !== 2;
        },
        isDisponivelParaAssinatura() {
            return this.produto
                && this.produto.siEncaminhamento === 4
                && this.produto.siAnalise === 2
                && this.produto.idDocumentoAssinatura;
        },
    },

    watch: {
        currentStep(step) {
            this.$router.push({ name: this.arraySteps[step - 1].name });
        },
        produto(val) {
            this.loadingProduto = true;
            if (!!val && Object.keys(val).length > 0) {
                this.removerSteps();
                this.atualizarStepByRoute();
                this.loadingProduto = false;
            }

            if (typeof val === 'undefined' || !val) {
                this.loadingProduto = false;
            }
        },
        $route(prev, old) {
            this.atualizarStepByRoute();
            if (prev.params.id !== old.params.id
                || prev.params.idPronac !== old.params.idPronac) {
                this.obterDadosParaAnalise();
            }
        },
    },

    created() {
        this.obterDadosParaAnalise();
    },

    methods: {
        ...mapActions({
            obterProdutoParaAnalise: 'parecer/obterProdutoParaAnalise',
            obterAnaLiseConteudo: 'parecer/obterAnaLiseConteudo',
        }),
        nextStep(n) {
            this.currentStep = (n === Object.keys(this.arraySteps).length) ? 1 : n + 1;
        },
        back() {
            this.$router.push({ name: 'parecer-listar-view' });
        },
        getIndexStepByName(name) {
            return this.arraySteps.findIndex(element => element.name === name);
        },
        deleteStepByName(name) {
            this.$delete(this.arraySteps, this.getIndexStepByName(name));
        },
        removerSteps() {
            if (Object.keys(this.produto).length > 0) {
                if (this.produto.stPrincipal !== 1) {
                    this.deleteStepByName('analise-consolidacao');
                }

                if (this.produto.tipoAnalise === 1) {
                    this.removerStepsAnaliseComplementar();
                }
            }
        },
        removerStepsAnaliseComplementar() {
            this.deleteStepByName('analise-conteudo');
            this.deleteStepByName('analise-consolidacao');
        },
        atualizarStepByRoute() {
            this.currentStep = this.getIndexStepByName(this.$route.name) + 1;
        },
        visualizarDiligencias() {
            this.dialogDiligencias = true;
        },
        visualizarHistorico(item) {
            this.dialogHistorico = true;
            this.produtoHistorico = item;
        },
        visualizarOutrosProdutos() {
            this.dialogOutrosProdutos = true;
        },
        obterDadosParaAnalise() {
            Object.assign(this.$data, this.$options.data.apply(this));
            this.obterProdutoParaAnalise({
                id: this.$route.params.id,
                idPronac: this.$route.params.idPronac,
            });
            this.obterAnaLiseConteudo({
                id: this.$route.params.id,
                idPronac: this.$route.params.idPronac,
            });
        },
    },

};
</script>
