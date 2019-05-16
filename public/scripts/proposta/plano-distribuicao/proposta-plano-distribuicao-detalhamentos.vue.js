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
                v-model="exibirFormulario"
                :disabled="disabled"
                :idplanodistribuicao="idplanodistribuicao"
                :idpreprojeto="idpreprojeto"
                :iduf="iduf"
                :idmunicipioibge="idmunicipioibge"
                :editarDetalhamento="detalhamento"
                :id-normativo="idNormativo"
                v-on:eventoSalvarDetalhamento="salvarDetalhamento"
                >
            </proposta-plano-distribuicao-formulario-detalhamento>
        </div>
    `,
    data: function () {
        return {
            detalhamentos: [],
            detalhamento: {},
            exibirFormulario: false,
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
        'idNormativo',
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
            this.exibirFormulario = true;
            this.detalhamento = Object.assign({}, detalhamento);
        },
        salvarDetalhamento(detalhamento) {

            let vue = this;
            $3.ajax({
                type: "POST",
                url: "/proposta/plano-distribuicao/detalhar-salvar/idPreProjeto/" + this.idpreprojeto,
                data: detalhamento
            }).done(function (response) {
                if (response.success == 'true') {
                    let index = vue.$data.detalhamentos.findIndex(item => item.idDetalhaPlanoDistribuicao == response.data.idDetalhaPlanoDistribuicao);

                    if (index >= 0) {
                        Object.assign(vue.$data.detalhamentos[index], detalhamento);
                    } else {
                        vue.$data.detalhamentos.push(response.data);
                    }
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

const VALOR_MEDIO_MAXIMO = 225;
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
                                    @click="editar(detalhamento, index)">
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
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
            
            <table style="margin-top: 20px; max-width: 300px" v-if="detalhamentos && detalhamentos.length > 0">
                <tr>
                    <th>
                        <b>Valor m&eacute;dio </b>
                    </th>
                    <td class="center-align red darken-3 white-text" v-if="((valorMedioProponente.value() > valorMedioMaximo) && (this.canalaberto == 0))"> 
                        <b>R$ {{valorMedioProponenteFormatado}}</b>
                    </td>
                    <td class="center-align " v-else>R$ {{valorMedioProponenteFormatado}}</td>
                </tr>
            </table>
        </div>
    `,
    data: function () {
        return {
            valorMedioMaximo: VALOR_MEDIO_MAXIMO,
        }
    },
    mixins: [funcoes],
    watch: {
        detalhamentos: function () {
            if ((numeral(this.valorMedioProponente).value() > this.valorMedioMaximo
                && (this.canalaberto == 0))) {
                this.mensagemAlerta("O valor m&eacute;dio: R$ " + this.valorMedioProponenteFormatado + ", n\xE3o pode ultrapassar: R$ " + this.formatarValor(this.valorMedioMaximo));
                // this.$data.detalhamentos.splice(-1, 1)
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
        },
    }
});


Vue.component('proposta-plano-distribuicao-formulario-detalhamento', {
    template: `
       
    `,

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
