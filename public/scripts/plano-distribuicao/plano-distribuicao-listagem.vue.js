Vue.component('plano-distribuicao-listagem', {
    template: `
        <div class="plano-distribuicao-listagem">
            <carregando  v-if="!produtos.length" :text="'Carregando produtos'"></carregando>
            <carregando  v-if="!locais.length" :text="'Carregando locais de realiza&ccedil;&atilde;o'"></carregando>
            <ul v-if="produtos" class="collapsible collapsible-produto no-padding" data-collapsible="accordion">
                <li v-for="produto of produtos">
                    <div class="collapsible-header green-text" :class="{ active: produto.stPrincipal == 1 }">
                        <i class="material-icons">perm_media</i>{{produto.Produto}}
                        <span v-if="produto.tpSolicitacao == 'A'" class="orange-text">(alterado)</span>
                        <span v-if="produto.stPrincipal == 1" class='badge'>Produto Principal</span>
                    </div>
                    <div class="collapsible-body no-padding margin10 scroll-x">
                        <component :is="componenteProdutoCabecalho" :produto="produto"></component>
                        <div style="width: 100%; margin-bottom: 20px" class="center-align">
                            <a
                                class="btn waves-effect waves-light white-text"
                                href="javascript:void(0)"
                                @click.prevent="visualizarOcultarDetalhamentos()"
                            >
                                <span v-if="!mostrarDetalhamentos && !disabled">Editar<i class="material-icons right">edit</i></span>
                                <span v-if="!mostrarDetalhamentos && disabled">Visualizar detalhamentos<i class="material-icons right">visibility</i></span>
                                <span v-if="mostrarDetalhamentos">Visualizar resumo<i class="material-icons right">visibility</i></span>
                            </a>
                        </div>
                        <transition name="custom-classes-transition" enter-active-class="animated bounceInUp">
                            <div v-show="!mostrarDetalhamentos" class="produto">
                                <component :is="componenteProdutoRodape" :produto="produto"></component>
                            </div>
                        </transition>
                        <transition name="custom-classes-transition" enter-active-class="animated bounceInUp">
                            <ul v-show="mostrarDetalhamentos" class="collapsible collapsible-locais" data-collapsible="accordion">
                                <li v-for="local of locais" v-if="local.idMunicipio">
                                    <div class="collapsible-header black-text active">
                                        <i class="material-icons">place</i> {{local.uf}} - {{local.municipio}}
                                        <span class="badge" v-if="detalhamentos">{{filtrarDetalhamentos(detalhamentos, produto.idPlanoDistribuicao, local.idMunicipio).length}}</span>
                                    </div>
                                    <div class="collapsible-body no-padding margin10 scroll-x">
                                        <component
                                            :is="componenteDetalhamento"
                                            :disabled="disabled"
                                            :produto="produto"
                                            :local="local"
                                            :id="idProjeto"
                                            :array-detalhamentos="filtrarDetalhamentos(detalhamentos, produto.idPlanoDistribuicao, local.idMunicipio)"
                                        ></component>
                                    </div>
                                </li>
                            </ul>
                        </transition>
                    </div>
                </li>
            </ul>
        </div>
    `,
    data: function () {
        return {
            produtos: [],
            detalhamentos: [],
            locais: [],
            mostrarDetalhamentos: true,
            icon: 'add',
            radio: 'n'
        }
    },
    props: {
        'idProjeto': null,
        'arrayProdutos': {},
        'arrayDetalhamentos': {},
        'arrayLocais': {},
        'componenteDetalhamento': {
            default: 'salic-proposta-detalhamento-plano-distribuicao',
            type: String
        },
        'componenteProdutoCabecalho': {
            default: 'plano-distribuicao-visualizar-produto-cabecalho',
            type: String
        },
        'componenteProdutoRodape': {
            default: 'plano-distribuicao-visualizar-produto-resumido',
            type: String
        },
        'disabled': false
    },
    mixins: [utils],
    computed: {},
    watch: {
        arrayProdutos: function (value) {
            this.produtos = value;
        },
        arrayDetalhamentos: function (value) {
            this.detalhamentos = value;
        },
        arrayLocais: function (value) {
            this.locais = value;
        }
    },
    updated: function () {
        this.iniciarCollapsible();
    },
    mounted: function () {
        if (typeof this.arrayProdutos != 'undefined') {
            this.produtos = this.arrayProdutos;
        }

        if (typeof this.arrayDetalhamentos != 'undefined') {
            this.detalhamentos = this.arrayDetalhamentos;
        }

        if (typeof this.arrayLocais != 'undefined') {
            this.locais = this.arrayLocais;
        }
    },
    methods: {
        filtrarDetalhamentos: function (detalhamentos, idPlano, idMunicipio) {
            let novaLista = [];
            if (typeof detalhamentos != 'undefined') {
                Object.keys(detalhamentos)
                    .map(function (key) {
                        if (detalhamentos[key].idPlanoDistribuicao == idPlano && detalhamentos[key].idMunicipio == idMunicipio) {
                            novaLista.push(detalhamentos[key]);
                        }
                    })
                ;
                return novaLista;
            }
            return detalhamentos;
        },
        iniciarCollapsible: function () {
            $3('.collapsible').each(function () {
                $3(this).collapsible();
            });
        },
        visualizarOcultarDetalhamentos: function () {

            if (this.mostrarDetalhamentos && !this.disabled) {
                detalhamentoEventBus.$emit('busAtualizarProdutos', true);
            }
            this.mostrarDetalhamentos = !this.mostrarDetalhamentos;
        }
    }
});
