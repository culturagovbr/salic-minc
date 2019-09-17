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
                @visualizarDiligencias="visualizarDiligencias($event)"
                @visualizarHistorico="visualizarHistorico($event)"
                @visualizarOutrosProdutos="visualizarOutrosProdutos($event)"
            />
            <s-mensagem
                v-if="isDisponivelParaAssinatura"
                :url-retorno="`${urlAssinatura}?idDocumentoAssinatura=${produto.idDocumentoAssinatura}&${retornoAssinatura.toString()}`"
                texto="Análise validada!
                Para finalizar você também deverá assinar o parecer técnico"
                msg-url-retorno="Ir para o documento"
                type="success"
            />
            <v-card v-else>
                <v-card-text>
                    <visualizacao-analise-produto
                        :produto="produto"
                    />
                </v-card-text>
            </v-card>
            <s-dialog-analise-outros-produtos
                v-model="dialogOutrosProdutos"
            />
            <s-dialog-diligencias
                v-if="isDisponivelParaAnalise"
                v-model="dialogDiligencias"
                :id-pronac="produto.idPronac"
                :id-produto="produto.idProduto"
                :tp-diligencia="TP_DILIGENCIA_ANALISE_TECNICA"
            />
            <s-analise-historico-produto-dialog
                v-model="dialogHistorico"
                :produto="produto"
            />
        </template>
        <s-mensagem
            v-else
            :rota-retorno="{ name: 'parecer-gerenciar-listar-view'}"
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
import VisualizacaoAnaliseProduto from '@/modules/parecer/components/VisualizacaoAnaliseProduto';

export default {
    name: 'ParecerAnalisarView',
    components: {
        VisualizacaoAnaliseProduto,
        AnaliseCabecalho,
        SMensagem,
        SDialogDiligencias,
        SDialogAnaliseOutrosProdutos,
        SCarregando,
        SAnaliseHistoricoProdutoDialog,
    },
    mixins: [MxDiligencia, MxConstantes],
    data: () => ({
        dialogOutrosProdutos: false,
        dialogDiligencias: false,
        loadingProduto: true,
        dialogHistorico: false,
        produtoHistorico: {},
        fab: false,
        hidden: false,
        tabs: null,
        prevRoute: null,
        urlAssinatura: '/assinatura/index/visualizar-projeto',
    }),
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            /* eslint-disable no-param-reassign */
            vm.prevRoute = from.path;
        });
    },
    computed: {
        ...mapGetters({
            produto: 'parecer/getProduto',
            analiseConteudo: 'parecer/getAnaliseConteudo',
        }),
        isDisponivelParaAnalise() {
            return !!this.produto
                && this.produto.siEncaminhamento === 5;
        },
        isDisponivelParaAssinatura() {
            return this.produto
                && this.produto.siEncaminhamento === 5
                && this.produto.siAnalise === 5
                && this.produto.idDocumentoAssinatura;
        },
        retornoAssinatura() {
            let retorno = `#${this.prevRoute}`;

            if (this.prevRoute === '/') {
                retorno = '#/parecer/gerenciar';
            }
            return `origin=${encodeURIComponent(retorno)}`;
        },
    },
    watch: {
        produto(val) {
            this.loadingProduto = true;
            if (!!val && Object.keys(val).length > 0) {
                this.loadingProduto = false;
            }

            if (typeof val === 'undefined' || !val) {
                this.loadingProduto = false;
            }
        },
        $route(prev, old) {
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
        visualizarDiligencias() {
            this.dialogDiligencias = true;
        },
        visualizarHistorico() {
            this.dialogHistorico = true;
        },
        visualizarOutrosProdutos() {
            this.dialogOutrosProdutos = true;
        },
    },
};
</script>
