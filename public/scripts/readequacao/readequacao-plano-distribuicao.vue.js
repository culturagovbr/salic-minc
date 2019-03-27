Vue.component('readequacao-plano-distribuicao', {
    template: `
        <div class="readequacao-plano-distribuicao">
            <carregando v-if="loading" :text="'carregando'"></carregando>
            <div v-if="mostrarMensagemInicial" class="card">
                <div class="card-content">
                    <p class="center-align">Aqui voc&ecirc; pode readequar os detalhamentos do seu plano de distribui&ccedil;&atilde;o.</p>
                    <p class="center-align bold">
                        <br>
                        <a class="waves-effect waves-light btn white-text btn-incluir-novo-item pulse"
                           @click.prevent="criarReadequacao"
                           ><i class="material-icons left">add</i><strong>Iniciar readequa&ccedil;&atilde;o</strong>
                        </a>
                    </p>
                </div>
            </div>
            <ul v-if="mostrarFormulario" class="collapsible no-padding" data-collapsible="accordion">
                <li>
                    <div class="collapsible-header active"><i class="material-icons">edit</i>
                        <span v-if="!disabled">Readequar Plano de Distribui&ccedil;&atilde;o</span>
                        <span v-else>Readequa&ccedil;&atilde;o do Plano de Distribui&ccedil;&atilde;o</span>
                    </div>
                    <div class="collapsible-body padding10">
                        <plano-distribuicao-listagem
                            :id-projeto="idPronac"
                            :disabled="disabled"
                            :active="active"
                            :array-produtos="produtos"
                            :array-detalhamentos="detalhamentos"
                            :componente-detalhamento="componenteDetalhamento"
                            :array-locais="locais"
                        ></plano-distribuicao-listagem>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">assignment</i>
                        <span v-if="!disabled">Justificar readequa&ccedil;&atilde;o</span>
                        <span v-else>Justificativa da readequa&ccedil;&atilde;o</span>
                    </div>
                    <div class="collapsible-body padding10">
                        <readequacao-formulario
                            v-if="mostrarFormulario"
                            :disabled="disabled"
                            ref="formulario"
                            :id-pronac="idPronac"
                            :id-tipo-readequacao="idTipoReadequacao"
                            :objReadequacao="readequacao"
                            v-on:eventoAtualizarReadequacao="atualizarReadequacao"
                            v-on:eventoSalvarReadequacao="salvarReadequacao"
                        ></readequacao-formulario>
                    </div>
                </li>
            </ul>
            <div v-if="mostrarFormulario && !disabled" class="readequacao-plano-distribuicao padding20 right-align">
                <a
                    href="javascript:void(0)"
                    class="btn waves-effect waves-light btn-danger btn-excluir"
                    title="Excluir readequa\u00E7\u00E3o"
                    @click="excluirReadequacao"
                >Excluir <i class="material-icons right">delete</i></a>
                <a
                    href="javascript:void(0)"
                    :disabled="desativarBotaoFinalizar"
                    class="waves-effect waves-light btn btn-secondary"
                    title="Finalizar readequa\u00E7\u00E3o e enviar para o MinC"
                    @click="finalizarReadequacao"
                >Finalizar<i class="material-icons right">send</i></a>
            </div>
            <div v-if="mostrarMensagemFinal" class="card">
                <div class="card-content">
                    <div class="row">
                        <div class="col s1 right-align"><i class="medium green-text material-icons">check_circle</i></div>
                        <div class="col s11">
                            <p><b>Solicita&ccedil;&atilde;o enviada com sucesso!</b></p>
                            <p>Sua solicita&ccedil;&atilde;o agora est&atilde; para an&atilde;lise t&etilde;cnica do MinC.</p>
                            <p>Para acompanhar, acesse o menu lateral "Execu&ccedil;&atilde;o -> Dados das readequa&ccedil;&otilde;es" 
                            em <a :href="'/default/consultardadosprojeto/index?idPronac=' + idPronacHash">consultar dados do projeto</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    data: function () {
        return {
            produtos: {},
            detalhamentos: {},
            locais: {},
            active: true,
            mostrarFormulario: false,
            mostrarMensagemInicial: false,
            mostrarMensagemFinal: false,
            readequacao: {},
            loading: true,
            componenteDetalhamento: "readequacao-plano-distribuicao-detalhamentos"
        }
    },
    props: {
        'idPronac': '',
        'siEncaminhamento': {
            default: 12,
            type: Number
        },
        'idTipoReadequacao': {
            default: 11,
            type: Number
        },
        'disabled': false
    },
    mixins: [utils],
    watch: {
        produtos: function (value) {
            if (value.length > 0) {
                this.obterDadosReadequacao();
                this.obterLocaisRealizacao();
                this.loading = false;
                this.mostrarFormulario = true;
                this.mostrarMensagemInicial = false;
            }

            if (value.length === 0) {
                this.loading = false;
                this.mostrarMensagemInicial = true;
            }
        }
    },
    created: function () {
        let self = this;
        detalhamentoEventBus.$on('busAtualizarProdutos', function (response) {
            self.obterPlanoDistribuicao();
        });

        this.obterPlanoDistribuicao();

        $3(document).ajaxStart(function () {
            $3('#container-loading').fadeIn('slow');
        });
        $3(document).ajaxComplete(function () {
            $3('#container-loading').fadeOut('slow');
        });
    },
    computed: {
        idPronacHash: function () {
            return $("#idPronacHash").val();
        },
        desativarBotaoFinalizar: function () {
            if(typeof this.readequacao.idReadequacao == 'undefined') {
                return true;
            }

            if(this.readequacao.justificativa.length < 3) {
                return true;
            }

            return false;
        }
    },
    methods: {
        criarReadequacao: function () {
            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/plano-distribuicao/criar-readequacao-ajax",
                data: {
                    idPronac: self.idPronac
                }
            }).done(function (response) {
                self.obterPlanoDistribuicao();
                self.mostrarMensagemInicial = false;
                self.mostrarFormulario = true;
            }).fail(function (response) {
                self.mensagemErro(response.responseJSON.msg)
            });
        },
        obterDadosReadequacao: function () {
            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/readequacoes/obter-dados-readequacao",
                data: {
                    idTipoReadequacao: self.idTipoReadequacao,
                    idPronac: self.idPronac,
                    siEncaminhamento: self.siEncaminhamento
                }
            }).done(function (response) {
                if (response.readequacao != null) {
                    self.readequacao = response.readequacao;
                }
            });
        },
        salvarReadequacao: function (readequacao) {
            let self = this;
            $3.ajax({
                type: "POST",
                url: "/readequacao/plano-distribuicao/atualizar-readequacao-ajax",
                data: readequacao
            }).done(function (response) {
                self.readequacao = readequacao;
                self.mensagemSucesso(response.msg);
            }).fail(function (response) {
                self.mensagemErro(response.responseJSON.msg)
            });
        },
        atualizarReadequacao: function (readequacao) {
            this.readequacao = readequacao;
        },
        obterPlanoDistribuicao: function () {
            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/plano-distribuicao/obter-plano-distribuicao-detalhamentos-ajax",
                data: {
                    idPronac: self.idPronac
                }
            }).done(function (response) {
                let dados = response.data;
                self.produtos = dados.planodistribuicao;
                self.detalhamentos = dados.detalhamentos;
            }).fail(function (response) {
                self.mensagemErro(response.responseJSON.msg)
            });
        },
        obterLocaisRealizacao: function () {
            let self = this;
            $3.ajax({
                type: "GET",
                url: "/readequacao/local-realizacao/obter-locais-de-realizacao-ajax",
                data: {
                    idPronac: self.idPronac
                }
            }).done(function (response) {
                self.locais = response.data;
            }).fail(function (response) {
                self.mensagemErro(response.responseJSON.msg)
            });
        },
        excluirReadequacao: function () {
            let self = this;
            if (confirm("Tem certeza que deseja excluir a redequa\u00E7\u00E3o?")) {
                $3.ajax({
                    type: "GET",
                    url: "/readequacao/plano-distribuicao/excluir-readequacao-plano-distribuicao-ajax",
                    data: {
                        idPronac: self.idPronac
                    }
                }).done(function (response) {
                    self.restaurarFormulario();
                    self.loading = false;
                    self.mostrarMensagemInicial = true;
                    self.mensagemSucesso(response.msg);
                }).fail(function (response) {
                    self.mensagemErro(response.responseJSON.msg)
                });
            }
        },
        finalizarReadequacao: function () {
            let self = this;
            if (confirm("Tem certeza que deseja finalizar a redequa\u00E7\u00E3o?")) {
                $3.ajax({
                    type: "POST",
                    url: "/readequacao/plano-distribuicao/finalizar-readequacao-plano-distribuicao-ajax",
                    data: self.readequacao
                }).done(function (response) {
                    self.mensagemSucesso(response.msg);
                    self.restaurarFormulario();
                    self.loading = false;
                    self.mostrarMensagemInicial = false;
                    self.mostrarMensagemFinal = true;
                }).fail(function (response) {
                    self.mensagemErro(response.responseJSON.msg)
                });
            }
        },
        restaurarFormulario: function () {
            Object.assign(this.$data, this.$options.data.apply(this))
        }
    }
});