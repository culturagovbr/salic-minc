<template>
    <v-container
        fluid
    >
        <s-carregando
            v-if="loadingProduto"
            text="Carregando produto"
        />
        <template v-else-if="produto && Object.keys(produto).length > 0">
            <v-toolbar
                height="90"
                color="blue-grey darken-2"
                class="white--text"
                dark
            >
                <v-btn
                    icon
                    class="hidden-xs-only"
                    @click="back()"
                >
                    <v-icon>arrow_back</v-icon>
                </v-btn>
                <v-toolbar-title class="ml-2">
                    <h5 class="headline font-weight-regular">
                        Análise inicial: {{ produto.dsProduto }}
                    </h5>
                    <v-divider />
                    <div class="subheading mt-1">
                        Projeto: {{ produto.PRONAC }} - {{ produto.NomeProjeto }}
                    </div>
                </v-toolbar-title>
                <v-spacer />
                <v-tooltip
                    v-if="produto.quantidadeProdutos > 1"
                    bottom
                >
                    <v-btn
                        slot="activator"
                        color="grey lighten-3"
                        icon
                        small
                        @click="dialogOutrosProdutos = !dialogOutrosProdutos"
                    >
                        <v-badge
                            color="grey lighten-1"
                            overlap
                            left
                        >
                            <span slot="badge">
                                {{ produto.quantidadeProdutos - 1 }}
                            </span>
                            <v-icon
                                color="blue-grey darken-2"
                            >
                                layers
                            </v-icon>
                        </v-badge>
                    </v-btn>
                    <span> Visualizar outros produtos do projeto </span>
                </v-tooltip>
                <v-tooltip
                    v-if="isDisponivelParaAnalise"
                    bottom
                >
                    <v-btn
                        slot="activator"
                        :color="obterConfigDiligencia(produto).cor"
                        target="_blank"
                        icon
                        small
                        @click="mostrarDiligencias()"
                    >
                        <v-badge
                            :value="produto.diasEmDiligencia > 0"
                            color="grey lighten-1"
                            overlap
                            left
                        >
                            <span slot="badge">
                                {{ produto.diasEmDiligencia }}
                            </span>
                            <v-icon
                                :color="obterConfigDiligencia(produto).corIcone"
                            >
                                notification_important
                            </v-icon>
                        </v-badge>
                    </v-btn>
                    <span> {{ obterConfigDiligencia(produto).texto }} </span>
                </v-tooltip>
                <v-tooltip
                    bottom
                >
                    <v-btn
                        slot="activator"
                        color="grey lighten-3"
                        icon
                        small
                        @click="visualizarHistorico(produto)"
                    >
                        <v-icon color="blue-grey darken-2">
                            history
                        </v-icon>
                    </v-btn>
                    <span>Visualizar histórico de distribuição deste produto</span>
                </v-tooltip>
                <v-chip
                    v-if="produto.stPrincipal === 1"
                    light
                    color="teal lighten-5"
                >
                    Produto Principal
                </v-chip>
                <v-chip
                    v-else
                    light
                    color="blue-grey lighten-5"
                >
                    Produto Secundário
                </v-chip>
            </v-toolbar>
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
            <s-dialog-analise-outros-produtos v-model="dialogOutrosProdutos" />
            <s-dialog-diligencias
                v-if="isDisponivelParaAnalise"
                v-model="dialogDiligencias"
                :id-pronac="produto.IdPRONAC"
                :id-produto="produto.idProduto"
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
import MxConstantes from '@/modules/parecer/mixins/const';
import SDialogAnaliseOutrosProdutos from '../components/AnaliseOutrosProdutosDialog';
import SDialogDiligencias from '@/modules/diligencia/components/SDialogDiligencias';
import SAnaliseHistoricoProdutoDialog from '@/modules/parecer/components/AnaliseHistoricoProdutoDialog';
import SMensagem from '@/components/SalicMensagem';

export default {
    name: 'ParecerAnalisarView',
    components: {
        SMensagem, SDialogDiligencias, SDialogAnaliseOutrosProdutos, SCarregando, SAnaliseHistoricoProdutoDialog,
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
        analiseConteudo() {
            this.validarSteps();
        },
        $route(prev, old) {
            this.atualizarStepByRoute();
            if (prev.params.id !== old.params.id
                || prev.params.idPronac !== old.params.idPronac) {
                this.obterProdutoParaAnalise({
                    id: this.$route.params.id,
                    idPronac: this.$route.params.idPronac,
                });
            }
        },
    },
    created() {
        this.obterProdutoParaAnalise({
            id: this.$route.params.id,
            idPronac: this.$route.params.idPronac,
        });
        this.obterAnaLiseConteudo({
            id: this.$route.params.id,
            idPronac: this.$route.params.idPronac,
        });
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
        getStepByName(name) {
            return this.arraySteps.find(element => element.name === name);
        },
        getIndexStepByName(name) {
            return this.arraySteps.findIndex(element => element.name === name);
        },
        deleteStepByName(name) {
            this.$delete(this.arraySteps, this.getIndexStepByName(name));
        },
        setStepEditableStatus(name, val = false) {
            this.$set(this.getStepByName(name), 'editable', val);
        },
        setStepMessage(name, msg) {
            this.$set(this.getStepByName(name), 'message', msg);
        },
        setStepRules(name, rules) {
            this.$set(this.getStepByName(name), 'rules', rules);
        },
        removerSteps() {
            if (Object.keys(this.produto).length > 0) {
                if (this.produto.stPrincipal !== 1) {
                    this.deleteStepByName('analise-consolidacao');
                }
            }
        },
        validarSteps() {
            if (Object.keys(this.analiseConteudo).length > 0) {
                this.setStepEditableStatus('analise-de-custos', true);
                this.setStepMessage('analise-de-custos', '');
                this.setStepRules('analise-de-custos', [() => true]);
                if (this.analiseConteudo.ParecerFavoravel !== true
                    && this.analiseConteudo.ParecerFavoravel !== 1) {
                    this.setStepEditableStatus('analise-de-custos', false);
                    this.setStepMessage('analise-de-custos', 'Não disponível! Parecer não favorável');
                    this.setStepRules('analise-de-custos', [() => false]);
                }
            }
        },
        atualizarStepByRoute() {
            this.currentStep = this.getIndexStepByName(this.$route.name) + 1;
        },
        mostrarDiligencias() {
            this.dialogDiligencias = true;
        },
        visualizarHistorico(item) {
            this.dialogHistorico = true;
            this.produtoHistorico = item;
        },
    },
};
</script>
