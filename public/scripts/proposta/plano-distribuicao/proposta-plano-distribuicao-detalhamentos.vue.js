(function (global, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['../numeral'], factory);
    } else if (typeof module === 'object' && module.exports) {
        factory(require('../numeral'));
    } else {
        factory(global.numeral);
    }
}(this, function (numeral) {
    numeral.register('locale', 'pt-br', {
        delimiters: {
            thousands: '.',
            decimal: ','
        },
        abbreviations: {
            thousand: 'mil',
            million: 'milhões',
            billion: 'b',
            trillion: 't'
        },
        ordinal: function (number) {
            return 'º';
        },
        currency: {
            symbol: 'R$'
        }
    });
}));

// switch between locales
numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');

const funcoes = {
    methods: {
        converterParaMoedaAmericana: function (valor) {
            if (!valor) {
                return 0;
            }

            valor = String(valor);
            valor = valor.replace(/\./g, '');
            valor = valor.replace(/\,/g, '.');
            valor = parseFloat(valor);
            valor = valor.toFixed(2);

            if (isNaN(valor)) {
                valor = 0;
            }

            return valor;
        },
        mensagemSucesso: function (msg) {
            Materialize.toast(msg, 8000, 'green white-text');
        },
        mensagemErro: function (msg) {
            Materialize.toast(msg, 8000, 'red darken-1 white-text');
        },
        mensagemAlerta: function (msg) {
            Materialize.toast(msg, 8000, 'mensagem1 orange darken-3 white-text');
        },
        formatarValor: function (valor) {
            valor = parseFloat(valor);
            return numeral(valor).format();
        }
    }
}

Vue.component('select-percent', {
    template: `
        <select 
            style="width: 75px; display: inline-block;" 
            @change="valorSelecionado(selecionado)" 
            v-model="selecionado"
            ref="combo" 
            tabindex="-1" 
            class="browser-default">
                <option v-for="item in items" v-bind:value="item">{{ item }}%</option>
        </select>
    `,
    props: {
        disabled: {
            type: Boolean,
            default: false
        },
        maximoCombo: {},
        selected: {
            default: 0
        }
    },
    data: function () {
        return {
            retorno: 1,
            selecionado: this.maximoCombo
        }
    },
    computed: {
        items: function () {
            var total = [];
            for (var i = this.maximoCombo; i >= 0; i--) {
                total.push(parseInt(i));
            }
            return total;
        }
    },
    watch: {
        selected: function(val) {
            this.selecionado = parseInt(val);
        },
        disabled: function () {
            this.$refs.combo.disabled = this.disabled;
            if (this.disabled) {
                this.value = 0;
            }
        }
    },
    methods: {
        valorSelecionado: function (value) {
            this.retorno = value;
            this.$emit('evento', parseInt(this.retorno))
        }
    },
    mounted: function () {
        this.$refs.combo.disabled = this.disabled;
    }
});

Vue.component('input-money', {
    template: `<input
        type="text"
        class="validate right-align"
        v-bind:disabled="false"
        v-bind:value="value"
        ref="input"
        v-on:input="updateMoney($event.target.value)"
        v-on:blur="formatValue"
    />`,
    props: {
        value: {
            default: 0
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    data: function () {
        return {
            val: 1
        }
    },
    mounted: function () {
        this.formatValue();
        this.$refs.input.disabled = this.disabled;
    },
    methods: {
        formatValue: function () {
            this.$refs.input.value = numeral(this.$refs.input.value).format();
        },
        updateMoney: function (value) {
            this.val = value;
            this.$emit('ev', this.val)
        }
    },
    watch: {
        disabled: function () {
            this.$refs.input.disabled = this.disabled;
            if (this.disabled) {
                this.val = 0;
            }
        }
    }
});

Vue.component('proposta-plano-distribuicao-detalhamentos', {
    template: `
        <div class="plano-distribuicao-detalhanentos">
            <proposta-plano-distribuicao-lista-detalhamentos
                :disabled="disabled"
                :idplanodistribuicao="idplanodistribuicao"
                :idpreprojeto="idpreprojeto"
                :iduf="iduf"
                :idmunicipioibge="idmunicipioibge"
                :detalhamentos="detalhamentos"
                :canalaberto="canalaberto"
                 v-on:eventoRemoverDetalhamento="removerDetalhamento"
                 v-on:eventoEditarDetalhamento="editarDetalhamento"
                >
            </proposta-plano-distribuicao-lista-detalhamentos>
            <proposta-plano-distribuicao-formulario-detalhamento
                :disabled="disabled"
                :idplanodistribuicao="idplanodistribuicao"
                :idpreprojeto="idpreprojeto"
                :iduf="iduf"
                :idmunicipioibge="idmunicipioibge"
                :editarDetalhamento="detalhamento"
                v-on:eventoSalvarDetalhamento="salvarDetalhamento"
                >
            </proposta-plano-distribuicao-formulario-detalhamento>
        </div>
    `,
    data: function () {
        return {
            detalhamentos: [],
            detalhamento: {}
        }
    },
    mixins: [funcoes],
    props: [
        'idpreprojeto',
        'idplanodistribuicao',
        'idmunicipioibge',
        'iduf',
        'disabled',
        'canalaberto',
    ],
    mounted: function () {
        this.obterDetalhamentos();
    },
    methods: {
        removerDetalhamento(detalhamento, index) {
            var vue = this;
            if (confirm("Tem certeza que deseja deletar o item?")) {
                $3.ajax({
                    type: "POST",
                    url: "/proposta/plano-distribuicao/detalhar-excluir/idPreProjeto/" + this.idpreprojeto,
                    data: {
                        idDetalhaPlanoDistribuicao: detalhamento.idDetalhaPlanoDistribuicao,
                        idPlanoDistribuicao: this.idplanodistribuicao
                    }
                }).done(function (response) {
                    if (response.success == 'true') {
                        Vue.delete(vue.detalhamentos, index);
                        vue.mensagemSucesso(response.msg);
                    }
                }).fail(function (response) {
                    vue.mensagemErro(response.responseJSON.msg);
                });
            }
        },
        editarDetalhamento(detalhamento, index) {
            this.detalhamento = detalhamento;
        },
        salvarDetalhamento(detalhamento) {

            let vue = this;
            $3.ajax({
                type: "POST",
                url: "/proposta/plano-distribuicao/detalhar-salvar/idPreProjeto/" + this.idpreprojeto,
                data: detalhamento
            }).done(function (response) {
                if (response.success == 'true') {
                    let index = vue.$data.detalhamentos.map(item => item.idDetalhaPlanoDistribuicao).indexOf(response.data.idDetalhaPlanoDistribuicao);
                    Vue.delete(vue.detalhamentos, index);
                    vue.$data.detalhamentos.push(response.data);
                    vue.mensagemSucesso(response.msg);
                    detalhamentoEventBus.$emit('callBackSalvarDetalhamento', true);
                }
            }).fail(function (response) {
                vue.mensagemErro(response.responseJSON.msg);
            });
        },
        obterDetalhamentos: function () {
            var vue = this;

            url = "/proposta/plano-distribuicao/obter-detalhamentos/idPreProjeto/" + this.idpreprojeto + "?idPlanoDistribuicao=" + this.idplanodistribuicao + "&idMunicipio=" + this.idmunicipioibge + "&idUF=" + this.iduf
            $3.ajax({
                type: "GET",
                url: url
            }).done(function (data) {
                vue.$data.detalhamentos = data.data;
            }).fail(function () {
                vue.mensagemErro('Erro ao buscar detalhamento');
            });
        }
    }
});

Vue.component('proposta-plano-distribuicao-lista-detalhamentos', {
    template: `
        <div class="row center-align">
            <table class="bordered">
                <thead v-if="detalhamentos && detalhamentos.length > 0">
                    <tr>
                        <th rowspan="2">Categoria</th>
                        <th rowspan="2">Quantidade</th>
                        <th class="proponente" colspan="3">
                            Proponente
                        </th>
                        <th class="popular" colspan="3">
                            Pre&ccedil;o Popular
                        </th>
                        <th class="gratuito" rowspan="2">
                            Distribui&ccedil;&atilde;o <br>Gratuita
                        </th>
                        <th rowspan="2" class="center-align">Receita <br> Prevista</th>
                        <th rowspan="2" colspan="2" width="10%" class="center-align">A&ccedil;&otildees</th>
                    </tr>
                    <tr>
                        <th class="proponente">Qtd. Inteira</th>
                        <th class="proponente">Qtd. Meia</th>
                        <th class="proponente">Pre&ccedil;o <br> Unitario</th>
                        <th class="popular">Qtd. Inteira</th>
                        <th class="popular">Qtd. Meia</th>
                        <th class="popular">Pre&ccedil;o <br> Unitario</th>
                    </tr>
                </thead>
                <tbody v-if="detalhamentos && detalhamentos.length > 0">
                    <tr v-for="( detalhamento, index ) in detalhamentos">
                        <td>{{ detalhamento.dsProduto }}</td>
                        <td class="center-align">{{ detalhamento.qtExemplares }}</td>
                        <!--Preço Proponente -->
                        <td class="center-align">{{ detalhamento.qtProponenteIntegral }}</td>
                        <td class="center-align">{{ detalhamento.qtProponenteParcial }}</td>
                        <td class="right-align">{{ formatarValor(detalhamento.vlUnitarioProponenteIntegral) }}</td>
                        <!--Preço Popular -->
                        <td class="center-align">{{ detalhamento.qtPopularIntegral }}</td>
                        <td class="center-align">{{ detalhamento.qtPopularParcial }}</td>
                        <td class="right-align">{{ formatarValor(detalhamento.vlUnitarioPopularIntegral) }}</td>
                        <!-- Distribuicao Gratuita-->
                        <td class="center-align">{{ parseInt(detalhamento.qtGratuitaDivulgacao) +
                            parseInt(detalhamento.qtGratuitaPatrocinador) + parseInt(detalhamento.qtGratuitaPopulacao) }}
                        </td>
                        <td class="right-align">{{ formatarValor(detalhamento.vlReceitaPrevista) }}</td>
                        <td>
                             <a 
                                href="javascript:void(0)"
                                class="btn small waves-effect waves-light tooltipped btn-primary btn-editar"
                                :class="_uid + '_teste'"
                                data-tooltip="Editar detalhamento"
                                v-bind:disabled="!disabled"
                                    @click.prevent="editar(detalhamento, index)">
                                <i class="material-icons">edit</i>
                            </a>
                        </td>
                        <td>
                            <a
                                href="javascript:void(0)"
                                class="btn small waves-effect waves-light tooltipped btn-danger btn-excluir-item"
                                data-tooltip="Excluir detalhamento"
                                v-bind:disabled="!disabled"
                                    @click.prevent="excluir(detalhamento, index)">
                                <i class="material-icons">delete</i>
                            </a>
                            
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="12" class="center-align">Nenhum detalhamento cadastrado</td>
                    </tr>
                </tbody>
                <tfoot v-if="detalhamentos && detalhamentos.length > 0" style="opacity: 0.5">
                    <tr>
                        <td><b>Totais</b></td>
                        <td class="center-align"><b>{{ qtExemplaresTotal }}</b></td>
                        <!--Fim: Preço Popular -->
                        <td class="center-align"><b>{{ qtProponenteIntegralTotal }}</b></td>
                        <td class="center-align"><b>{{ qtProponenteParcialTotal }}</b></td>
                        <td class="right-align"> -</td>
                        <!--Preço Popular -->
                        <td class="center-align"><b>{{ qtPopularIntegralTotal }}</b></td>
                        <td class="center-align"><b>{{ qtPopularParcialTotal }}</b></td>
                        <td class="right-align"> -</td>
                        <td class="center-align"><b>{{ qtDistribuicaoGratuitaTotal }}</b>
                        </td>
                        <td class="right-align"><b>{{ receitaPrevistaTotal }}</b></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <table style="max-width: 300px" v-if="detalhamentos && detalhamentos.length > 0">
                <tr>
                    <th>
                        <b>Valor m&eacute;dio </b>
                    </th>
                    <td class="center-align red" v-if="((valorMedioProponente.value() > 225) && (this.canalaberto == 0))"> 
                        {{valorMedioProponenteFormatado}}
                    </td>
                    <td class="center-align " v-else>{{valorMedioProponenteFormatado}}</td>
                </tr>
            </table>
        </div>
    `,
    data: function () {
        return {}
    },
    mixins: [funcoes],
    watch: {
        detalhamentos: function () {
            if ((numeral(this.valorMedioProponente).value() > 225
                && (this.canalaberto == 0))) {
                this.mensagemAlerta("O valor medio:" + this.valorMedioProponenteFormatado + ", n\xE3o pode ultrapassar: 225,00");
                this.$data.detalhamentos.splice(-1, 1)
            }
        }
    },
    props: [
        'idpreprojeto',
        'idplanodistribuicao',
        'idmunicipioibge',
        'iduf',
        'disabled',
        'canalaberto',
        'detalhamentos'
    ],
    computed: {
        qtExemplaresTotal: function () {
            return this.detalhamentos.reduce(function (total, value) {
                return total + parseInt(value.qtExemplares);
            }, 0);
        },
        qtDistribuicaoGratuitaTotal: function () {
            return this.detalhamentos.reduce(function (total, value) {
                return total + (
                    parseInt(value.qtGratuitaDivulgacao) +
                    parseInt(value.qtGratuitaPatrocinador) +
                    parseInt(value.qtGratuitaPopulacao));
            }, 0);
        },
        qtPopularIntegralTotal: function () {
            return this.detalhamentos.reduce(function (total, value) {
                return total + parseInt(value.qtPopularIntegral);
            }, 0);
        },
        qtPopularParcialTotal: function () {
            return this.detalhamentos.reduce(function (total, value) {
                return total + parseInt(value.qtPopularParcial);
            }, 0);
        },
        qtProponenteIntegralTotal: function () {
            return this.detalhamentos.reduce(function (total, value) {
                return total + parseInt(value.qtProponenteIntegral);
            }, 0);
        },
        qtProponenteParcialTotal: function () {
            return this.detalhamentos.reduce(function (total, value) {
                return total + parseInt(value.qtProponenteParcial);
            }, 0);
        },
        receitaPrevistaTotal: function () {
            var soma = numeral();

            soma.add(this.detalhamentos.reduce(function (total, value) {
                return total + parseFloat(value.vlReceitaPrevista);
            }, 0));

            return soma.format();
        },
        valorMedioProponente: function () {
            var vlReceitaProponenteIntegral = numeral();
            var vlReceitaProponenteParcial = numeral();
            var qtProponenteIntegral = numeral();
            var qtProponenteParcial = numeral();

            for (var i = 0; i < this.detalhamentos.length; i++) {
                vlReceitaProponenteIntegral.add(this.detalhamentos[i]['vlReceitaProponenteIntegral']);
                vlReceitaProponenteParcial.add(parseFloat(this.detalhamentos[i]['vlReceitaProponenteParcial']));
                qtProponenteIntegral.add(parseFloat(this.detalhamentos[i]['qtProponenteIntegral']));
                qtProponenteParcial.add(parseFloat(this.detalhamentos[i]['qtProponenteParcial']));
            }

            let media = numeral(parseFloat(vlReceitaProponenteIntegral.value() + vlReceitaProponenteParcial.value()) / (qtProponenteIntegral.value() + qtProponenteParcial.value()));

            return media;
        },
        valorMedioProponenteFormatado: function () {
            return this.valorMedioProponente.format();
        }
    },
    methods: {
        excluir: function (detalhamento, index) {
            this.$emit('eventoRemoverDetalhamento', detalhamento, index);
        },
        editar: function(detalhamento, index) {
            let elm = $3("div[formIdMunicipio='"+ detalhamento.idMunicipio + "']" );
            $3("html, body").animate({
                scrollTop: $3(elm).offset().top + 30
            }, 600);

            this.$emit('eventoEditarDetalhamento', detalhamento, index);
        }
    }
});

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

Vue.component('proposta-plano-distribuicao-formulario-detalhamento', {
    template: `
        <div class="detalhamento-distribuicao-dos-produtos">
            <div :id="_uid + '_form_detalhamento'" :formIdMunicipio="idmunicipioibge" class="row center-align" ref="containerForm">
                <button 
                    class="btn waves-effect waves-light" 
                    ref="mostrarForm"
                    @click="mostrarFormulario(_uid + '_form_detalhamento')">
                    Novo detalhamento
                    <i class="material-icons right">{{icon}}</i>
                </button>
            </div>
            <transition
                name="custom-classes-transition"
                enter-active-class="animated slideInUp"
            >
                <div v-show="visualizarFormulario" class="card">
                    <form class="card-content">
                        <span class="card-title">Cadastrar novo detalhamento</span>
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
                                <strong>Distribui&ccedil;&atilde;o Gratuita</strong> (m&iacute;nimo {{percentualGratuitoPadrao * 100
                                }}%)
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
                            <button class="btn waves-effect waves-light" ref="add" v-on:click.prevent="salvar">
                                Salvar
                                <i class="material-icons right">send</i>
                            </button>
                        </div>
                    </form>
                 </div>
            </transition>
        </div>
    `,
    data: function () {
        return {
            distribuicao: {
                idDetalhaPlanoDistribuicao: null,
                idPlanoDistribuicao: this.idplanodistribuicao,
                idUF: this.iduf,
                idMunicipio: this.idmunicipioibge,
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
    mixins: [funcoes],
    props: [
        'idpreprojeto',
        'idplanodistribuicao',
        'idmunicipioibge',
        'iduf',
        'disabled',
        'editarDetalhamento'
    ],
    created: function() {
        let vue = this;
        detalhamentoEventBus.$on('callBackSalvarDetalhamento', function (response) {
            if(response == true) {
                vue.limparFormulario();
            }
        });
    },
    mounted: function () {
        this.$refs.add.disabled = !this.disabled;
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
        editarDetalhamento: function(object) {
            let vue = this;
            if(object.idDetalhaPlanoDistribuicao != null) {
                vue.limparFormulario();
                vue.visualizarFormulario = true;

                // definir o percentual do proponente
                let percentualProponente = (parseInt(object.qtProponenteIntegral) + parseInt(object.qtProponenteParcial)) / parseInt(object.qtExemplares);
                vue.percentualProponente = Number((percentualProponente).toFixed(2));

                // definir o percentual do preco popular, é atualizado no proximo clico
                vue.$nextTick(() => {
                    let percentualPrecoPopular = (parseInt(object.qtPopularIntegral) + parseInt(object.qtPopularParcial)) / parseInt(object.qtExemplares);
                    vue.percentualPrecoPopular = Number((percentualPrecoPopular).toFixed(2));
                });

                Object.assign(vue.distribuicao, object);

                vue.inputUnitarioPopularIntegral = vue.formatarValor(object.vlUnitarioPopularIntegral);
                vue.inputUnitarioProponenteIntegral = vue.formatarValor(object.vlUnitarioProponenteIntegral);

                if(object.vlUnitarioPopularIntegral == 0 && object.vlUnitarioProponenteIntegral == 0) {
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
        mostrarFormulario: function (id) {

            this.visualizarFormulario = !this.visualizarFormulario;
            this.icon =  this.visualizarFormulario ? 'visibility_off' : 'add';

            if (this.visualizarFormulario == true) {
                let elm = $3("#" + id);
                $3("html, body").animate({
                    scrollTop: $3(elm).offset().top + 30
                }, 600);
            } else {
                this.limparFormulario();
            }
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
                if (this.distribuicao.vlUnitarioProponenteIntegral == 0 && this.percentualProponente > 0) {
                    this.mensagemAlerta("Pre\xE7o unit\xE1rio no Proponente \xE9 obrigat\xF3rio!");
                    return;
                }

                if (this.distribuicao.vlUnitarioPopularIntegral == 0 && this.percentualPrecoPopular > 0) {
                    this.mensagemAlerta("Pre\xE7o unit\xE1rio no Pre\xE7o Popular \xE9 obrigat\xF3rio!");
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

var detalhamentoEventBus = new Vue();
var app6 = new Vue({
    el: '#container-vue'
});

$3(document).ready(function () {
    $3('#container-loading').fadeIn();
});

$3(document).ajaxStart(function () {
    $3('#container-loading').fadeIn();
});

$3(document).ajaxComplete(function () {
    $3('#container-loading').fadeOut();
});