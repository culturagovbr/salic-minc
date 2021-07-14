const TIPO_EXEMPLAR = 'e';
const TIPO_INGRESSO = 'i';
const NAO = 'n';
const SIM = 's';
const TIPO_LOCAL_ABERTO = 'a';
const TIPO_LOCAL_FECHADO = 'f';
const TIPO_ESPACO_PUBLICO = 's';
const TIPO_ESPACO_PRIVADO = 'n';

const DISTRIBUICAO_GRATUITA_PERCENTUAL_PADRAO = 0.3;
const PRECO_POPULAR_PERCENTUAL_PADRAO = 0.2;
const PROPONENTE_PERCENTUAL_PADRAO = 0.5;

Vue.component('plano-distribuicao-detalhamentos-formulario', {
    template: `
    <div class="detalhamento-distribuicao-dos-produtos">
        <div :id="_uid + '_form_detalhamento'" :formIdMunicipio="local.idMunicipio + idplanodistribuicao" class="row center-align" ref="containerForm">
            <a 
                class="btn waves-effect waves-light white-text margin20" 
                @click="mostrarFormulario(local.idMunicipio, idplanodistribuicao)"
                ref="mostrarForm">
                Adicionar detalhamento
                <i class="material-icons right">{{icon}}</i>
            </a>
        </div>
        <div :id="local.idMunicipio + this.idplanodistribuicao + '_modal'" class="modal full bottom-sheet">
            <div class="modal-content">
                <form >
                    <h4>Cadastro detalhamento</h4>
                    <div class="row">
                        <div class="col s12 m6 l6">
                            <span>
                                <b>Tipo de venda</b><br>
                                <input
                                    name="tipoVenda"
                                    type="radio"
                                    :id="_uid + 'tipoVendaIngresso'"
                                    value="i"
                                    v-model="distribuicao.tpVenda"
                                />
                                <label :for=" _uid + 'tipoVendaIngresso'">Ingresso</label>
                                <input
                                    name="tipoVenda"
                                    type="radio"
                                    :id="_uid + 'tipoVendaExemplar'"
                                    value="e"
                                    v-model="distribuicao.tpVenda"
                                />
                                <label :for=" _uid + 'tipoVendaExemplar'">Exemplar</label>
                            </span>
                        </div>
                        <div class="col s12 m6 l6">
                            <span>
                                <b>Distribui&ccedil;&atilde;o ser&aacute; totalmente gratuita?</b><br>
                                <input
                                    name="group1"
                                    type="radio"
                                    :id="_uid + '1'"
                                    value="s"
                                    v-model="distribuicaoGratuita"
                                />
                                <label :for="_uid + '1'">Sim</label>
                                <input
                                    name="group1"
                                    type="radio"
                                    :id="_uid + '2'"
                                    value="n"
                                    v-model="distribuicaoGratuita"
                                />
                                <label :for="_uid + '2'">N&atilde;o</label>
                            </span>
                        </div>
                    </div>
            
                    <div class="row" v-if="distribuicao.tpVenda == 'i'">
                        <div class="col col s12 m6 l6">
                            <span>
                                <b>Tipo do local de apresenta&ccedil;&atilde;o</b><br>
                                <input
                                    name="tipoLocalRealizacao"
                                    type="radio"
                                    :id="_uid + 'tipoAberto'"
                                    value="a"
                                    v-model="distribuicao.tpLocal"
                                />
                                <label :for=" _uid + 'tipoAberto'">Aberto</label>
                                <input
                                    name="tipoLocalRealizacao"
                                    type="radio" :id=" _uid + 'tipoFechado'"
                                    value="f"
                                    v-model="distribuicao.tpLocal"
                                />
                                <label :for=" _uid + 'tipoFechado'">Fechado</label>
                            </span>
                        </div>
                        <div class="col col s12 m6 l6">
                            <span>
                                <b>Espa&ccedil;o p&uacute;blico</b><br>
                                <input
                                    type="radio"
                                    :id="_uid + 'espacoPublicoSim'"
                                    value="s"
                                    v-model="distribuicao.tpEspaco"
                                />
                                <label :for="_uid + 'espacoPublicoSim'">Sim</label>
                                <input
                                    type="radio"
                                    id="espacoPublicoNao"
                                    :id=" _uid + 'espacoPublicoNao'"
                                    value="n"
                                    v-model="distribuicao.tpEspaco"/>
                                <label :for="_uid + 'espacoPublicoNao'">N&atilde;o</label>
                            </span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="input-field col s12 m6 l6">
                            <input
                                :id="_uid + 'dsProduto'"
                                type="text"
                                class="validate"
                                ref="dsProduto"
                                placeholder="Ex: Arquibancada"
                                v-model="distribuicao.dsProduto"
                            />
                            <label class="active" :for="_uid + 'dsProduto'">Categoria</label>
                        </div>
                        <div class="input-field col s12 m6 l6">
                            <input
                                :id="_uid + 'qtExemplares'"
                                type="number"
                                class="validate"
                                ref="qtExemplares"
                                placeholder="0"
                                v-model.number.lazy="distribuicao.qtExemplares"
                            />
                            <label class="active" :for="_uid + 'qtExemplares'">Quantidade</label>
                        </div>
                    </div>
            
                    <fieldset class="proponente-s" v-show="distribuicaoGratuita =='s' ? false : true">
                        <legend>
                            <strong>Proponente </strong>(at&eacute; {{ percentualProponentePadrao * 100 }}%)
                            <select-percent
                                v-bind:disabled="distribuicaoGratuita =='s' ? true: false"
                                v-bind:maximoCombo="(percentualProponentePadrao *  100)"
                                v-bind:selected="(percentualProponente *  100)"
                                v-on:evento="percentualProponente = $event/100">
                            </select-percent>
                        </legend>
                        <div class="row">
                            <div class="input-field col s12 m6 l2">
                                <input-money
                                    v-bind:disabled="distribuicaoGratuita =='s'? true: false"
                                    v-bind:value="inputUnitarioProponenteIntegral"
                                    v-on:ev="inputUnitarioProponenteIntegral = $event">
                                </input-money>
                                <label
                                    class="active"
                                    :for="_uid + 'vlUnitarioProponenteIntegral'"
                                >Pre&ccedil;o Unit&aacute;rio R$</label>
                            </div>
                            <div class="input-field col s12 m6 l2">
                                <input
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                    v-model.number="distribuicao.qtProponenteIntegral"
                                    ref="qtProponenteIntegral"
                                />
                                <label
                                    class="active"
                                    :for="_uid + 'qtProponenteIntegral'"
                                >Quantidade {{labelInteira}}</label>
                            </div>
                            <div class="input-field col s12 m6 l2" v-if="distribuicao.tpVenda == 'i'">
                                <input
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                    v-model.number="distribuicao.qtProponenteParcial"
                                />
                                <label
                                    class="active"
                                    :for="_uid + 'qtProponenteParcial'"
                                >Quantidade Meia</label>
                            </div>
                            <div class="input-field col s12 m6 l3">
                                <input
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                    v-model="this.vlReceitaProponenteIntegral"
                                />
                                <label
                                    class="active"
                                    :for="_uid + 'vlReceitaProponenteIntegral'"
                                >Valor {{labelInteira}} R$</label>
                            </div>
                            <div class="input-field col s12 m6 l3" v-if="distribuicao.tpVenda == 'i'">
                                <input
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                    v-model.number="this.vlReceitaProponenteParcial"
                                />
                                <label
                                    class="active"
                                    :for="_uid + 'vlReceitaProponenteParcial'"
                                >Valor meia R$</label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="preco-popular" v-show="distribuicaoGratuita =='s' ? false : true">
                        <legend>
                            <strong>Pre&ccedil;o Popular</strong> (Padr&atilde;o: {{ percentualPrecoPopularPadrao * 100 }}%)
                            <select-percent
                                v-bind:disabled="distribuicaoGratuita =='s'? true: false"
                                v-bind:maximoCombo="(percentualMaximoPrecoPopular *  100)"
                                v-bind:selected="(percentualPrecoPopular *  100)"
                                v-on:evento="percentualPrecoPopular = $event/100">
                            </select-percent>
                        </legend>
                        <div class="row">
                            <div class="input-field col s12 m6 l2">
                                <input-money
                                    v-bind:disabled="distribuicaoGratuita=='s' ? true: false"
                                    v-bind:value="inputUnitarioPopularIntegral"
                                    v-on:ev="inputUnitarioPopularIntegral = $event">
                                </input-money>
                                <label
                                    class="active"
                                    v-bind:disabled="distribuicaoGratuita=='s' ? true: false"
                                    :for="_uid + 'vlUnitarioPopularIntegral'"
                                >Pre&ccedil;o Unit&aacute;rio R$
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l2">
                                <input
                                    type="text"
                                    class="right-align disabled"
                                    disabled
                                    v-model.number="distribuicao.qtPopularIntegral"
                                    ref="qtPopularIntegral"
                                />
                                <label
                                    class="active"
                                    :for="_uid + 'qtPopularIntegral'">
                                    Quantidade {{labelInteira}}
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l2" v-if="distribuicao.tpVenda == 'i'">
                                <input
                                    type="text"
                                    class="right-align disabled"
                                    disabled
                                    v-model.number="distribuicao.qtPopularParcial"
                                    ref="distribuicao.qtPopularParcial"
                                />
                                <label class="active" :for="_uid + 'qtPopularParcial'">
                                    Quantidade Meia
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l3">
                                <input
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                    v-model.number="this.vlReceitaPopularIntegral"
                                />
                                <label class="active" :for="_uid + 'vlReceitaPopularIntegral'">
                                    Valor {{labelInteira}} R$
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l3" v-if="distribuicao.tpVenda == 'i'">
                                <input
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                    v-model.number="this.vlReceitaPopularParcial"
                                />
                                <label class="active" :for="_uid + 'vlReceitaPopularParcial'">Valor meia R$</label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="distribuicao-gratuita">
                        <legend>
                            <strong>Distribui&ccedil;&atilde;o Gratuita</strong> (m&iacute;nimo {{percentualGratuitoPadrao * 100}}%)
                            <span v-if="percentualGratuitoPadrao !== this.percentualGratuito"> 
                                        <b>Atual {{ parseInt(this.percentualGratuito *  100) }}%</b>
                                    </span>
                        </legend>
                        <div class="row">
                            <div class="input-field col s12 m6 l3">
                                <input
                                    type="number"
                                    class="validate right-align"
                                    v-model.number="distribuicao.qtGratuitaDivulgacao"
                                    ref="divulgacao"
                                />
                                <label class="active" :for="_uid + 'qtGratuitaDivulgacao'">
                                    Divulga&ccedil;&atilde;o (At&eacute; {{ parseInt(distribuicao.qtExemplares * 0.1) }})
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l3">
                                <input
                                    type="number"
                                    class="validate right-align"
                                    v-model.number="distribuicao.qtGratuitaPatrocinador"
                                    ref="patrocinador"
                                />
                                <label class="active" :for="_uid + 'qtGratuitaPatrocinador'">
                                    Patrocinador (At&eacute; {{ parseInt(distribuicao.qtExemplares * 0.1) }})
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l3">
                                <input
                                    type="text"
                                    class="right-align disabled"
                                    disabled
                                    v-model.number="distribuicao.qtGratuitaPopulacao"
                                    ref="populacao"
                                />
                                <label class="active" :for="_uid + 'qtGratuitaPopulacao'">
                                    Popula&ccedil;&atilde;o
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row receita-prevista center-align">
                        <div class="col s12 l4 offset-l8">
                            <p><strong>Receita Prevista: </strong> R$ {{vlReceitaPrevista}}</p>
                        </div>
                    </div>
                    <div class="row salvar center-align">
                        <br>
                        <a href="#!" class="modal-close waves-effect waves-green btn btn-info">Fechar</a>
                        <a 
                            href="javascript:void(0)"
                            class="btn waves-effect waves-light white-text" 
                            ref="add" 
                            v-on:click.prevent="salvar">
                            Salvar <i class="material-icons right">send</i>
                        </a>
                    </div>
                </form>
             </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
    `,
    data: function () {
        return {
            distribuicao: {
                idDetalhaPlanoDistribuicao: null,
                idPlanoDistribuicao: this.idplanodistribuicao,
                idUF: this.local.idUF,
                idMunicipio: this.local.idMunicipio,
                dsProduto: '',
                qtExemplares: 0,
                qtGratuitaDivulgacao: 0,
                qtGratuitaPatrocinador: 0,
                qtGratuitaPopulacao: 0,
                qtPopularIntegral: 0,
                qtPopularParcial: 0,
                vlUnitarioPopularIntegral: 0,
                vlReceitaPopularIntegral: 0,
                vlReceitaPopularParcial: 0,
                qtProponenteIntegral: 0,
                qtProponenteParcial: 0,
                vlUnitarioProponenteIntegral: 0,
                vlReceitaProponenteIntegral: 0,
                vlReceitaProponenteParcial: 0,
                vlReceitaPrevista: 0,
                tpVenda: TIPO_INGRESSO,
                tpLocal: TIPO_LOCAL_ABERTO,
                tpEspaco: TIPO_ESPACO_PRIVADO
            },
            active: false,
            visualizarFormulario: false,
            icon: 'add',
            "distribuicaoGratuita": NAO,
            "percentualGratuitoPadrao": DISTRIBUICAO_GRATUITA_PERCENTUAL_PADRAO,
            "percentualPrecoPopularPadrao": PRECO_POPULAR_PERCENTUAL_PADRAO,
            "percentualPrecoPopular": PRECO_POPULAR_PERCENTUAL_PADRAO,
            "percentualProponentePadrao": PROPONENTE_PERCENTUAL_PADRAO,
            "percentualProponente": PROPONENTE_PERCENTUAL_PADRAO,
            "labelInteira": 'Inteira',
            "inputUnitarioPopularIntegral": 0,
            "inputUnitarioProponenteIntegral": 0,
        }
    },
    mixins: [utils],
    props: [
        'idplanodistribuicao',
        'local',
        'disabled',
        'editarDetalhamento'
    ],
    created: function () {
        let vue = this;
        detalhamentoEventBus.$on('callBackSalvarDetalhamento', function (response) {
            if (response == true) {
                vue.limparFormulario();
            }
        });
    },
    mounted: function () {
        this.$refs.add.disabled = !this.disabled;
        $3('.modal').modal();
    },
    watch: {
        "distribuicao.qtExemplares": function (val) {
            if (val < 0) {
                this.mensagemAlerta("A quantidade n\xE3o pode passar ser menor que zero");
                this.distribuicao.qtExemplares = 0;
            }
        },
        "distribuicao.qtGratuitaDivulgacao": function (val) {
            let limiteQuantidadeDivulgacao = parseInt(this.distribuicao.qtExemplares * 0.1);

            if (val > limiteQuantidadeDivulgacao) {
                this.mensagemAlerta("A quantidade n\xE3o pode passar de " + limiteQuantidadeDivulgacao);
                this.distribuicao.qtGratuitaDivulgacao = limiteQuantidadeDivulgacao;
            }

            if (val < 0) {
                this.mensagemAlerta("A quantidade n\xE3o pode ser menor que zero");
                this.distribuicao.qtGratuitaDivulgacao = 0;
            }

            this.distribuicao.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
        },
        "distribuicao.qtGratuitaPatrocinador": function (val) {
            let limitePatrocinador = parseInt(this.distribuicao.qtExemplares * 0.1);

            if (val > limitePatrocinador) {
                this.mensagemAlerta("A quantidade n\xE3o pode passar de " + limitePatrocinador);
                this.distribuicao.qtGratuitaPatrocinador = limitePatrocinador;
            }

            if (val < 0) {
                this.mensagemAlerta("A quantidade n\xE3o pode ser menor que zero");
                this.distribuicao.qtGratuitaPatrocinador = 0;
            }

            this.distribuicao.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
        },
        "distribuicao.vlUnitarioPopularIntegral": function () {
            if (this.distribuicao.vlUnitarioPopularIntegral > 75.00) {
                this.mensagemAlerta('O pre\xE7o unit\xE1rio do pre\xE7o popular n\xE3o pode ser maior que R$ 75,00');
                this.inputUnitarioPopularIntegral = this.formatarValor(75.00);
            }
        },
        percentualProponente: function () {
            this.percentualPrecoPopular = this.percentualMaximoPrecoPopular;
        },
        atualizarCalculosDistribuicao: function () {

            this.labelInteira = 'Inteira';
            this.distribuicao.qtProponenteIntegral = 0;
            this.distribuicao.qtProponenteParcial = 0;
            this.distribuicao.qtPopularIntegral = 0;
            this.distribuicao.qtPopularParcial = 0;
            this.distribuicao.vlReceitaProponenteIntegral = 0;
            this.distribuicao.vlReceitaProponenteParcial = 0;
            this.distribuicao.vlReceitaPopularIntegral = 0;
            this.distribuicao.vlReceitaPopularParcial = 0;

            if (this.distribuicaoGratuita == NAO) {
                this.distribuicao.qtProponenteIntegral = this.obterQuantidadePorPercentual(this.percentualProponente);
                this.distribuicao.qtProponenteParcial = this.obterQuantidadePorPercentual(this.percentualProponente);
                this.distribuicao.qtPopularIntegral = this.obterQuantidadePorPercentual(this.percentualPrecoPopular);
                this.distribuicao.qtPopularParcial = this.obterQuantidadePorPercentual(this.percentualPrecoPopular);
                this.distribuicao.vlReceitaProponenteIntegral = this.converterParaMoedaAmericana(this.vlReceitaProponenteIntegral);
                this.distribuicao.vlReceitaProponenteParcial = this.converterParaMoedaAmericana(this.vlReceitaProponenteParcial);
                this.distribuicao.vlReceitaPopularIntegral = this.converterParaMoedaAmericana(this.vlReceitaPopularIntegral);
                this.distribuicao.vlReceitaPopularParcial = this.converterParaMoedaAmericana(this.vlReceitaPopularParcial);
                this.distribuicao.vlUnitarioProponenteIntegral = this.converterParaMoedaAmericana(this.inputUnitarioProponenteIntegral);
                this.distribuicao.vlUnitarioPopularIntegral = this.converterParaMoedaAmericana(this.inputUnitarioPopularIntegral);

                if (this.distribuicao.vlUnitarioProponenteIntegral < 0) {
                    this.mensagemAlerta("Valor Proponente n\xE3o pode passar ser menor que zero");
                    this.inputUnitarioProponenteIntegral = 0;
                }

                if (this.distribuicao.vlUnitarioPopularIntegral < 0) {
                    this.mensagemAlerta("Valor Popular n\xE3o pode passar ser menor que zero");
                    this.inputUnitarioPopularIntegral = 0;
                }

            } else {
                this.distribuicao.vlUnitarioProponenteIntegral = 0;
                this.distribuicao.vlUnitarioPopularIntegral = 0;
            }

            if (this.distribuicao.tpVenda == TIPO_EXEMPLAR) {
                this.distribuicao.qtPopularParcial = 0;
                this.distribuicao.qtProponenteParcial = 0;
                this.distribuicao.vlReceitaProponenteParcial = 0;
                this.distribuicao.vlReceitaPopularParcial = 0;
                this.labelInteira = '';
            }

            this.distribuicao.vlReceitaPrevista = this.converterParaMoedaAmericana(this.vlReceitaPrevista);
            this.distribuicao.qtGratuitaDivulgacao = parseInt(this.distribuicao.qtExemplares * 0.1);
            this.distribuicao.qtGratuitaPatrocinador = parseInt(this.distribuicao.qtExemplares * 0.1);
            this.distribuicao.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
        },
        editarDetalhamento: function (object) {
            let vue = this;
            if (object.idDetalhaPlanoDistribuicao != null) {
                vue.limparFormulario();
                vue.visualizarFormulario = true;

                // definir o percentual do proponente
                let percentualProponente = (parseInt(object.qtProponenteIntegral) + parseInt(object.qtProponenteParcial)) / parseInt(object.qtExemplares);
                vue.percentualProponente = Number((percentualProponente).toFixed(2));

                // definir o percentual do preco popular, é atualizado no proximo ciclo
                vue.$nextTick(() => {
                    let percentualPrecoPopular = (parseInt(object.qtPopularIntegral) + parseInt(object.qtPopularParcial)) / parseInt(object.qtExemplares);
                    vue.percentualPrecoPopular = Number((percentualPrecoPopular).toFixed(2));
                });

                Object.assign(vue.distribuicao, object);

                vue.inputUnitarioPopularIntegral = vue.formatarValor(object.vlUnitarioPopularIntegral);
                vue.inputUnitarioProponenteIntegral = vue.formatarValor(object.vlUnitarioProponenteIntegral);

                if (object.vlUnitarioPopularIntegral == 0 && object.vlUnitarioProponenteIntegral == 0) {
                    vue.distribuicaoGratuita = SIM;
                }
            }
        }
    },
    computed: {
        atualizarCalculosDistribuicao: function () {
            return [
                this.distribuicao.qtExemplares,
                this.distribuicaoGratuita,
                this.distribuicao.tpVenda,
                this.inputUnitarioProponenteIntegral,
                this.inputUnitarioPopularIntegral,
                this.percentualPrecoPopular,
                this.distribuicao
            ].join()
        },
        qtGratuitaPopulacaoMinimo: function () {
            let soma = numeral();

            soma.add(this.distribuicao.qtProponenteIntegral);
            soma.add(this.distribuicao.qtProponenteParcial);
            soma.add(this.distribuicao.qtPopularIntegral);
            soma.add(this.distribuicao.qtPopularParcial);
            soma.add(this.distribuicao.qtGratuitaDivulgacao);
            soma.add(this.distribuicao.qtGratuitaPatrocinador);

            return parseInt(parseInt(this.distribuicao.qtExemplares) - numeral(soma).value());
        },
        percentualGratuito: function () {
            if (this.distribuicaoGratuita == SIM) {
                return 1;
            }
            return DISTRIBUICAO_GRATUITA_PERCENTUAL_PADRAO +
                (this.percentualMaximoPrecoPopular - this.percentualPrecoPopular)
                ;
        },
        percentualMaximoPrecoPopular: function () {
            return PRECO_POPULAR_PERCENTUAL_PADRAO + (PROPONENTE_PERCENTUAL_PADRAO - this.percentualProponente);
        },
        qtPrecoPopularValorIntegralLimite: function () {
            var percentualPopularIntegral = 0.5;

            if (this.distribuicao.tpVenda == TIPO_EXEMPLAR) {
                percentualPopularIntegral = 1;
            }
            return parseInt((this.distribuicao.qtExemplares * this.percentualPrecoPopular) * percentualPopularIntegral);
        },
        qtPrecoPopularValorParcialLimite: function () {
            let percentualPopularParcial = 0.5;

            if (this.distribuicao.tpVenda == TIPO_EXEMPLAR) {
                percentualPopularParcial = 0
            }
            return parseInt((this.distribuicao.qtExemplares * this.percentualPrecoPopular) * percentualPopularParcial);
        },
        vlReceitaPopularIntegral: function () {
            return numeral(
                parseInt(this.distribuicao.qtPopularIntegral) *
                this.converterParaMoedaAmericana(this.inputUnitarioPopularIntegral)
            ).format();
        },
        vlReceitaPopularParcial: function () {
            return numeral(
                this.distribuicao.qtPopularParcial * this.converterParaMoedaAmericana(this.inputUnitarioPopularIntegral) * 0.5
            ).format();
        },
        vlReceitaProponenteIntegral: function () {
            return numeral(
                this.converterParaMoedaAmericana(this.inputUnitarioProponenteIntegral) * parseInt(this.distribuicao.qtProponenteIntegral)
            ).format();
        },
        vlReceitaProponenteParcial: function () {
            return numeral(
                (this.converterParaMoedaAmericana(this.inputUnitarioProponenteIntegral) * 0.5 ) * this.distribuicao.qtProponenteParcial
            ).format();
        },
        vlReceitaPrevista: function () {
            let calc = numeral();

            calc.add(this.distribuicao.vlReceitaPopularIntegral);
            calc.add(this.distribuicao.vlReceitaPopularParcial);
            calc.add(this.distribuicao.vlReceitaProponenteIntegral);
            calc.add(this.distribuicao.vlReceitaProponenteParcial);

            return numeral(calc).format();
        }
    },
    methods: {
        obterQuantidadePorPercentual: function (percentualDistribuicao) {
            let divisao = 0.5;

            if (this.distribuicao.tpVenda == TIPO_EXEMPLAR) {
                divisao = 1;
            }

            return parseInt((this.distribuicao.qtExemplares * percentualDistribuicao) * divisao);
        },
        mostrarFormulario: function (idMunicipio, idPlanoDistribuicao) {
            this.limparFormulario();
            $3('.modal').modal('close');
            $3("#" + idMunicipio + idPlanoDistribuicao + "_modal").modal('open');
        },
        limparFormulario: function () {
            Object.assign(this.$data, this.$options.data.apply(this))
        },
        salvar: function (event) {

            if (this.distribuicao.dsProduto == '' && this.distribuicao.tpVenda == 'i') {
                this.mensagemAlerta("\xC9 obrigat\xF3rio informar a categoria");
                this.$refs.dsProduto.focus();
                return;
            }

            if (this.distribuicao.qtExemplares == 0) {
                this.mensagemAlerta("Quantidade \xE9 obrigat\xF3rio!");
                this.$refs.qtExemplares.focus();
                return;
            }

            if (this.distribuicaoGratuita == NAO) {
                if (this.distribuicao.vlUnitarioProponenteIntegral == 0 && parseFloat(this.percentualProponente) > 0) {
                    this.mensagemAlerta("Pre\xE7o unit\xE1rio no Proponente \xE9 obrigat\xF3rio!");
                    return;
                }

                if (this.distribuicao.vlUnitarioPopularIntegral == 0 && parseFloat(this.percentualPrecoPopular) > 0) {
                    this.mensagemAlerta("Pre\xE7o unit\xE1rio no Pre\xE7o Popular \xE9 obrigat\xF3rio!");
                    return;
                }

                if (parseFloat(this.distribuicao.vlUnitarioPopularIntegral) > parseFloat(this.distribuicao.vlUnitarioProponenteIntegral)) {
                    this.mensagemAlerta("Pre\xE7o Unit\u00E1rio Popular n\u00E3o pode ser maior que o Pre\u00E7o Unit\u00E1rio Proponente!");
                    return;
                }
            }

            if (this.distribuicao.qtGratuitaPopulacao < this.qtGratuitaPopulacaoMinimo) {
                this.mensagemAlerta("Quantidade para popula\xE7\xE3o n\xE3o pode ser menor que " + this.qtGratuitaPopulacaoMinimo);
                this.distribuicao.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
                this.$refs.populacao.focus();
                return;
            }

            this.$emit('eventoSalvarDetalhamento', this.distribuicao);
        }
    }
});