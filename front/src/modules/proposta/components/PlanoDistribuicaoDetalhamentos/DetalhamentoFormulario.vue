<template>
    <div class="detalhamento-distribuicao-dos-produtos">
        <div
            ref="containerForm"
            :id="`${_uid}-form-detalhamento`"
            :formIdMunicipio="idMunicipioIbge"
            class="row center-align"
        >
            <button
                ref="mostrarForm"
                class="btn waves-effect waves-light"
                @click="mostrarFormulario(`${_uid}-form-detalhamento`)"
            >
                Novo detalhamento
                <i class="material-icons right">{{ icon }}</i>
            </button>
        </div>
        <transition
            name="custom-classes-transition"
            enter-active-class="animated slideInUp"
        >
            <div
                v-show="visualizarFormulario"
                class="card"
            >
                <form class="card-content">
                    <span class="card-title">Cadastrar novo detalhamento</span>
                    <div class="row">
                        <div class="col s12 m6 l6">
                            <span>
                                <b>Tipo de venda</b><br>
                                <input
                                    :id="`${_uid}-tipo-venda-ingresso`"
                                    v-model="distribuicao.tpVenda"
                                    name="tipoVenda"
                                    type="radio"
                                    value="i"
                                >
                                <label :for="`${_uid}-tipo-venda-ingresso`">Ingresso</label>
                                <input
                                    :id="`${_uid}-tipo-venda-exemplar`"
                                    v-model="distribuicao.tpVenda"
                                    name="tipoVenda"
                                    type="radio"
                                    value="e"
                                >
                                <label :for="`${_uid}-tipo-venda-exemplar`">Exemplar</label>
                            </span>
                        </div>
                        <div class="col s12 m6 l6">
                            <span>
                                <b>Distribui&ccedil;&atilde;o ser&aacute; totalmente gratuita?</b><br>
                                <input
                                    :id="`${_uid}-distribuicao-sim`"
                                    v-model="distribuicaoGratuita"
                                    name="group1"
                                    type="radio"
                                    value="s"
                                >
                                <label :for="`${_uid}-distribuicao-sim`">Sim</label>
                                <input
                                    :id="`${_uid}-distribuicao-nao`"
                                    v-model="distribuicaoGratuita"
                                    name="group1"
                                    type="radio"
                                    value="n"
                                >
                                <label :for="`${_uid}-distribuicao-nao`">N&atilde;o</label>
                            </span>
                        </div>
                    </div>

                    <div
                        v-if="distribuicao.tpVenda === 'i'"
                        class="row"
                    >
                        <div class="col col s12 m6 l6">
                            <span>
                                <b>Tipo do local de apresenta&ccedil;&atilde;o</b><br>
                                <input
                                    :id="`${_uid}-tipo-aberto`"
                                    v-model="distribuicao.tpLocal"
                                    name="tipoLocalRealizacao"
                                    type="radio"
                                    value="a"
                                >
                                <label :for="`${_uid}-tipo-aberto`">Aberto</label>
                                <input
                                    :id="`${_uid}-tipo-fechado`"
                                    v-model="distribuicao.tpLocal"
                                    name="tipoLocalRealizacao"
                                    type="radio"
                                    value="f"
                                >
                                <label :for="`${_uid}-tipo-fechado`">Fechado</label>
                            </span>
                        </div>
                        <div class="col col s12 m6 l6">
                            <span>
                                <b>Espa&ccedil;o p&uacute;blico</b><br>
                                <input
                                    :id="`${_uid}-espaco-publico-sim`"
                                    v-model="distribuicao.tpEspaco"
                                    type="radio"
                                    value="s"
                                >
                                <label :for="`${_uid}-espaco-publico-sim`">Sim</label>
                                <input
                                    :id="`${_uid}-espaco-publico-nao`"
                                    v-model="distribuicao.tpEspaco"
                                    type="radio"
                                    value="n"
                                >
                                <label :for="`${_uid}-espaco-publico-nao`">N&atilde;o</label>
                            </span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="input-field col s12 m6 l6">
                            <input
                                ref="dsProduto"
                                :id="`${_uid}-ds-produto`"
                                v-model="distribuicao.dsProduto"
                                type="text"
                                class="validate"
                                placeholder="Ex: Arquibancada"
                            >
                            <label
                                :for="`${_uid}-ds-produto`"
                                class="active"
                            >Categoria</label>
                        </div>
                        <div class="input-field col s12 m6 l6">
                            <input
                                ref="qtExemplares"
                                :id="`${_uid}-qt-exemplares`"
                                v-model.number.lazy="distribuicao.qtExemplares"
                                type="number"
                                class="validate"
                                placeholder="0"
                            >
                            <label
                                :for="`${_uid}-qt-exemplares`"
                                class="active"
                            >Quantidade</label>
                        </div>
                    </div>

                    <fieldset
                        v-show="distribuicaoGratuita =='s' ? false : true"
                        class="proponente-s"
                    >
                        <legend>
                            <strong>Proponente </strong>(at&eacute; {{ valores.percentualProponentePadrao * 100 }}%)
                            <s-select-percent
                                :disabled="distribuicaoGratuita ==='s'"
                                :maximo-combo="(valores.percentualProponentePadrao * 100)"
                                :selected="(valores.percentualProponente * 100)"
                                @evento="valores.percentualProponente = $event/100"
                            />
                        </legend>
                        <div class="row">
                            <div class="input-field col s12 m6 l2">
                                <s-input-money
                                    :id="`${_uid}-vl-unitario-proponente-integral`"
                                    :disabled="distribuicaoGratuita ==='s'"
                                    :value="inputUnitarioProponenteIntegral"
                                    @ev="inputUnitarioProponenteIntegral = $event"
                                />
                                <label
                                    :for="`${_uid}-vl-unitario-proponente-integral`"
                                    class="active"
                                >Pre&ccedil;o Unit&aacute;rio R$</label>
                            </div>
                            <div class="input-field col s12 m6 l2">
                                <input
                                    ref="qtProponenteIntegral"
                                    :id="`${_uid}-qt-proponente-integral`"
                                    v-model.number="distribuicao.qtProponenteIntegral"
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                >
                                <label
                                    :for="`${_uid}-qt-proponente-integral`"
                                    class="active"
                                >Quantidade {{ labelInteira }}</label>
                            </div>
                            <div
                                v-if="distribuicao.tpVenda === 'i'"
                                class="input-field col s12 m6 l2"
                            >
                                <input
                                    v-model.number="distribuicao.qtProponenteParcial"
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                >
                                <label
                                    :for="_uid + 'qtProponenteParcial'"
                                    class="active"
                                >Quantidade Meia</label>
                            </div>
                            <div class="input-field col s12 m6 l3">
                                <input
                                    v-model="vlReceitaProponenteIntegral"
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                >
                                <label
                                    :for="_uid + 'vlReceitaProponenteIntegral'"
                                    class="active"
                                >Valor {{ labelInteira }} R$</label>
                            </div>
                            <div
                                v-if="distribuicao.tpVenda === 'i'"
                                class="input-field col s12 m6 l3"
                            >
                                <input
                                    v-model.number="vlReceitaProponenteParcial"
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                >
                                <label
                                    :for="_uid + 'vlReceitaProponenteParcial'"
                                    class="active"
                                >Valor meia R$</label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset
                        v-show="distribuicaoGratuita === 's' ? false : true"
                        class="preco-popular"
                    >
                        <legend>
                            <strong>Pre&ccedil;o Popular</strong> (Padr&atilde;o: {{ valores.percentualPrecoPopularPadrao * 100 }}%)
                            <s-select-percent
                                :disabled="distribuicaoGratuita === 's'"
                                :maximo-combo="(percentualMaximoPrecoPopular * 100)"
                                :selected="(valores.percentualPrecoPopular * 100)"
                                @evento="valores.percentualPrecoPopular = $event/100"
                            />
                        </legend>
                        <div class="row">
                            <div class="input-field col s12 m6 l2">
                                <s-input-money
                                    :disabled="distribuicaoGratuita === 's'"
                                    :value="inputUnitarioPopularIntegral"
                                    @ev="inputUnitarioPopularIntegral = $event"
                                />
                                <label
                                    :disabled="distribuicaoGratuita === 's'"
                                    :for="_uid + 'vlUnitarioPopularIntegral'"
                                    class="active"
                                >Pre&ccedil;o Unit&aacute;rio R$
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l2">
                                <input
                                    ref="qtPopularIntegral"
                                    v-model.number="distribuicao.qtPopularIntegral"
                                    type="text"
                                    class="right-align disabled"
                                    disabled
                                >
                                <label
                                    :for="_uid + 'qtPopularIntegral'"
                                    class="active"
                                >
                                    Quantidade {{ labelInteira }}
                                </label>
                            </div>
                            <div
                                v-if="distribuicao.tpVenda === 'i'"
                                class="input-field col s12 m6 l2"
                            >
                                <input
                                    ref="distribuicao.qtPopularParcial"
                                    v-model.number="distribuicao.qtPopularParcial"
                                    type="text"
                                    class="right-align disabled"
                                    disabled
                                >
                                <label
                                    :for="_uid + 'qtPopularParcial'"
                                    class="active"
                                >
                                    Quantidade Meia
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l3">
                                <input
                                    v-model.number="vlReceitaPopularIntegral"
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                >
                                <label
                                    :for="_uid + 'vlReceitaPopularIntegral'"
                                    class="active"
                                >
                                    Valor {{ labelInteira }} R$
                                </label>
                            </div>
                            <div
                                v-if="distribuicao.tpVenda === 'i'"
                                class="input-field col s12 m6 l3"
                            >
                                <input
                                    v-model.number="vlReceitaPopularParcial"
                                    type="text"
                                    class="disabled right-align"
                                    disabled
                                >
                                <label
                                    :for="_uid + 'vlReceitaPopularParcial'"
                                    class="active"
                                >Valor meia R$</label>
                            </div>
                            <div class="col s12 m12 l12">
                                <div class="small">
                                    <i class="material-icons tiny left">info_outline</i>
                                    Valor de refer&ecirc;ncia do Vale Cultura: R$ {{ formatarValor(valores.valorMaximoPrecoPopular) }}
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="distribuicao-gratuita">
                        <legend>
                            <strong>Distribui&ccedil;&atilde;o Gratuita</strong> (m&iacute;nimo {{ valores.percentualGratuitoPadrao * 100
                            }}%)
                            <span v-if="valores.percentualGratuitoPadrao !== percentualGratuito">
                                <b>Atual {{ parseInt(percentualGratuito * 100) }}%</b>
                            </span>
                        </legend>
                        <div class="row">
                            <div class="input-field col s12 m6 l3">
                                <input
                                    ref="divulgacao"
                                    v-model.number="distribuicao.qtGratuitaDivulgacao"
                                    type="number"
                                    class="validate right-align"
                                >
                                <label
                                    :for="_uid + 'qtGratuitaDivulgacao'"
                                    class="active"
                                >
                                    Divulga&ccedil;&atilde;o (At&eacute;
                                    {{ parseInt(distribuicao.qtExemplares * valores.percentualGratuitoDivulgacao) }})
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l3">
                                <input
                                    ref="patrocinador"
                                    v-model.number="distribuicao.qtGratuitaPatrocinador"
                                    type="number"
                                    class="validate right-align"
                                >
                                <label
                                    :for="_uid + 'qtGratuitaPatrocinador'"
                                    class="active"
                                >
                                    Patrocinador (At&eacute; {{ parseInt(distribuicao.qtExemplares * valores.percentualGratuitoPatrocinador) }})
                                </label>
                            </div>
                            <div class="input-field col s12 m6 l3">
                                <input
                                    ref="populacao"
                                    v-model.number="distribuicao.qtGratuitaPopulacao"
                                    type="text"
                                    class="right-align disabled"
                                    disabled
                                >
                                <label
                                    :for="_uid + 'qtGratuitaPopulacao'"
                                    class="active"
                                >
                                    Popula&ccedil;&atilde;o
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row receita-prevista center-align">
                        <div class="col s12 l4 offset-l8">
                            <p><strong>Receita Prevista: </strong> R$ {{ vlReceitaPrevista }}</p>
                        </div>
                    </div>
                    <div
                        v-if="!disabled"
                        class="row salvar center-align">
                        <br>
                        <button
                            ref="btnSalvar"
                            class="btn waves-effect waves-light"
                            @click.prevent="salvar"
                        >
                            Salvar
                            <i class="material-icons right">send</i>
                        </button>
                    </div>
                </form>
            </div>
        </transition>
    </div>
</template>

<script>
import numeral from 'numeral';
import { utils } from '@/mixins/utils';
import SSelectPercent from '@/components/SalicSelectPercent';
import SInputMoney from '@/components/SalicInputMoney';

numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');

const TIPO_EXEMPLAR = 'e';
const TIPO_INGRESSO = 'i';
const NAO = 'n';
const SIM = 's';
const TIPO_LOCAL_ABERTO = 'a';
const TIPO_ESPACO_PRIVADO = 'n';

const DISTRIBUICAO_GRATUITA_PERCENTUAL_PATROCINADOR = 0.1;
const DISTRIBUICAO_GRATUITA_PERCENTUAL_DIVULGACAO = 0.1;

const PROPONENTE_PERCENTUAL_PADRAO = 0.5;
const PRECO_POPULAR_PERCENTUAL_PADRAO = 0.1;
const DISTRIBUICAO_GRATUITA_PERCENTUAL_PADRAO = 0.4;
const VALOR_MAXIMO_PRECO_POPULAR = 50.00;

const valoresPadroes = {
    percentualGratuitoPadrao: DISTRIBUICAO_GRATUITA_PERCENTUAL_PADRAO,
    percentualGratuitoDivulgacao: DISTRIBUICAO_GRATUITA_PERCENTUAL_DIVULGACAO,
    percentualGratuitoPatrocinador: DISTRIBUICAO_GRATUITA_PERCENTUAL_PATROCINADOR,
    percentualPrecoPopularPadrao: PRECO_POPULAR_PERCENTUAL_PADRAO,
    percentualPrecoPopular: PRECO_POPULAR_PERCENTUAL_PADRAO,
    percentualProponentePadrao: PROPONENTE_PERCENTUAL_PADRAO,
    percentualProponente: PROPONENTE_PERCENTUAL_PADRAO,
    valorMaximoPrecoPopular: VALOR_MAXIMO_PRECO_POPULAR,
};

const PROPONENTE_PERCENTUAL_PADRAO_2017 = 0.5;
const PRECO_POPULAR_PERCENTUAL_PADRAO_2017 = 0.2;
const DISTRIBUICAO_GRATUITA_PERCENTUAL_PADRAO_2017 = 0.3;
const VALOR_MAXIMO_PRECO_POPULAR_2017 = 75.00;

const valoresNormativo2017 = {
    percentualGratuitoPadrao: DISTRIBUICAO_GRATUITA_PERCENTUAL_PADRAO_2017,
    percentualGratuitoDivulgacao: DISTRIBUICAO_GRATUITA_PERCENTUAL_DIVULGACAO,
    percentualGratuitoPatrocinador: DISTRIBUICAO_GRATUITA_PERCENTUAL_PATROCINADOR,
    percentualPrecoPopularPadrao: PRECO_POPULAR_PERCENTUAL_PADRAO_2017,
    percentualPrecoPopular: PRECO_POPULAR_PERCENTUAL_PADRAO_2017,
    percentualProponentePadrao: PROPONENTE_PERCENTUAL_PADRAO_2017,
    percentualProponente: PROPONENTE_PERCENTUAL_PADRAO_2017,
    valorMaximoPrecoPopular: VALOR_MAXIMO_PRECO_POPULAR_2017,
};

const INSTRUCAO_NORMATIVA_2019 = 10;

export default {
    name: 'PlanoDistribuicaoDetalhamentoFormulario',
    components: { SSelectPercent, SInputMoney },
    mixins: [utils],
    props: {
        value: {
            type: Boolean,
            default: false,
        },
        idPreProjeto: {
            type: [String, Number],
            required: true,
        },
        idPlanoDistribuicao: {
            type: [String, Number],
            required: true,
        },
        idMunicipioIbge: {
            type: [Number],
            required: true,
        },
        idUf: {
            type: [Number],
            required: true,
        },
        disabled: {
            type: [String, Number],
            default: '1',
        },
        canalAberto: {
            type: [String, Number],
            default: 0,
        },
        idNormativo: {
            type: [String, Number],
            default: '',
        },
        editarDetalhamento: {
            type: Object,
            default: () => {},
        },
    },
    data() {
        return {
            distribuicao: {
                idDetalhaPlanoDistribuicao: null,
                idPlanoDistribuicao: this.idPlanoDistribuicao,
                idUF: this.idUf,
                idMunicipio: this.idMunicipioIbge,
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
                tpEspaco: TIPO_ESPACO_PRIVADO,
            },
            active: false,
            visualizarFormulario: false,
            icon: 'add',
            distribuicaoGratuita: NAO,
            labelInteira: 'Inteira',
            inputUnitarioPopularIntegral: 0,
            inputUnitarioProponenteIntegral: 0,
            valores: valoresPadroes,
        };
    },
    computed: {
        atualizarCalculosDistribuicao() {
            return [
                this.distribuicao.qtExemplares,
                this.distribuicaoGratuita,
                this.distribuicao.tpVenda,
                this.inputUnitarioProponenteIntegral,
                this.inputUnitarioPopularIntegral,
                this.valores.percentualPrecoPopular,
                this.distribuicao,
            ].join();
        },
        qtGratuitaPopulacaoMinimo() {
            const soma = numeral();

            soma.add(this.distribuicao.qtProponenteIntegral);
            soma.add(this.distribuicao.qtProponenteParcial);
            soma.add(this.distribuicao.qtPopularIntegral);
            soma.add(this.distribuicao.qtPopularParcial);
            soma.add(this.distribuicao.qtGratuitaDivulgacao);
            soma.add(this.distribuicao.qtGratuitaPatrocinador);

            return parseInt(parseInt(this.distribuicao.qtExemplares, 10) - numeral(soma).value(), 10);
        },
        percentualGratuito() {
            if (this.distribuicaoGratuita === SIM) {
                return 1;
            }
            return this.valores.percentualGratuitoPadrao
                    + (this.percentualMaximoPrecoPopular - this.valores.percentualPrecoPopular);
        },
        percentualMaximoPrecoPopular() {
            return this.valores.percentualPrecoPopularPadrao + (this.valores.percentualProponentePadrao - this.valores.percentualProponente);
        },
        qtPrecoPopularValorIntegralLimite() {
            const percentualPopularIntegral = this.distribuicao.tpVenda === TIPO_EXEMPLAR ? 1 : 0.5;
            return parseInt((this.distribuicao.qtExemplares * this.valores.percentualPrecoPopular) * percentualPopularIntegral, 10);
        },
        qtPrecoPopularValorParcialLimite() {
            const percentualPopularParcial = this.distribuicao.tpVenda === TIPO_EXEMPLAR ? 0 : 0.5;
            return parseInt((this.distribuicao.qtExemplares * this.valores.percentualPrecoPopular) * percentualPopularParcial, 10);
        },
        vlReceitaPopularIntegral() {
            return numeral(
                parseInt(this.distribuicao.qtPopularIntegral, 10)
                    * this.converterParaMoedaAmericana(this.inputUnitarioPopularIntegral),
            ).format();
        },
        vlReceitaPopularParcial() {
            return numeral(
                this.distribuicao.qtPopularParcial * this.converterParaMoedaAmericana(this.inputUnitarioPopularIntegral) * 0.5,
            ).format();
        },
        vlReceitaProponenteIntegral() {
            return numeral(
                this.converterParaMoedaAmericana(this.inputUnitarioProponenteIntegral) * parseInt(this.distribuicao.qtProponenteIntegral, 10),
            ).format();
        },
        vlReceitaProponenteParcial() {
            return numeral(
                (this.converterParaMoedaAmericana(this.inputUnitarioProponenteIntegral) * 0.5) * this.distribuicao.qtProponenteParcial,
            ).format();
        },
        vlReceitaPrevista() {
            const calc = numeral();

            calc.add(this.distribuicao.vlReceitaPopularIntegral);
            calc.add(this.distribuicao.vlReceitaPopularParcial);
            calc.add(this.distribuicao.vlReceitaProponenteIntegral);
            calc.add(this.distribuicao.vlReceitaProponenteParcial);

            return numeral(calc).format();
        },
    },
    watch: {
        // eslint-disable-next-line
        'distribuicao.qtExemplares': function (val) {
            if (val < 0) {
                this.mensagemAlerta('A quantidade n\xE3o pode passar ser menor que zero');
                this.distribuicao.qtExemplares = 0;
            }
        },
        // eslint-disable-next-line
        'distribuicao.qtGratuitaDivulgacao': function (val) {
            const limiteQuantidadeDivulgacao = parseInt(this.distribuicao.qtExemplares * this.valores.percentualGratuitoDivulgacao, 10);

            if (val > limiteQuantidadeDivulgacao) {
                this.mensagemAlerta(`A quantidade n\xE3o pode passar de ${limiteQuantidadeDivulgacao}`);
                this.distribuicao.qtGratuitaDivulgacao = limiteQuantidadeDivulgacao;
            }

            if (val < 0) {
                this.mensagemAlerta('A quantidade n\xE3o pode ser menor que zero');
                this.distribuicao.qtGratuitaDivulgacao = 0;
            }

            this.distribuicao.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
        },
        // eslint-disable-next-line
        'distribuicao.qtGratuitaPatrocinador': function (val) {
            const limitePatrocinador = parseInt(this.distribuicao.qtExemplares * this.valores.percentualGratuitoPatrocinador, 10);

            if (val > limitePatrocinador) {
                this.mensagemAlerta(`A quantidade n\xE3o pode passar de ${limitePatrocinador}`);
                this.distribuicao.qtGratuitaPatrocinador = limitePatrocinador;
            }

            if (val < 0) {
                this.mensagemAlerta('A quantidade n\xE3o pode ser menor que zero');
                this.distribuicao.qtGratuitaPatrocinador = 0;
            }

            this.distribuicao.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
        },
        // eslint-disable-next-line
        'distribuicao.vlUnitarioPopularIntegral': function () {
            if (this.distribuicao.vlUnitarioPopularIntegral > this.valores.valorMaximoPrecoPopular) {
                this.mensagemAlerta(`O pre\xE7o unit\xE1rio do pre\xE7o popular n\xE3o pode
                ser maior que R$ ${this.formatarValor(this.valores.valorMaximoPrecoPopular)}`);
                this.inputUnitarioPopularIntegral = this.formatarValor(this.valores.valorMaximoPrecoPopular);
            }
        },
        // eslint-disable-next-line
        'valores.percentualProponente': function (val) {
            if (val > this.valores.percentualProponentePadrao) {
                this.mensagemAlerta(`Percentual do Proponente n\u00E3o pode ser maior que ${this.valores.percentualProponentePadrao * 100}%`);
            }

            this.valores.percentualPrecoPopular = this.percentualMaximoPrecoPopular;
        },
        atualizarCalculosDistribuicao() {
            this.labelInteira = 'Inteira';
            this.distribuicao.qtProponenteIntegral = 0;
            this.distribuicao.qtProponenteParcial = 0;
            this.distribuicao.qtPopularIntegral = 0;
            this.distribuicao.qtPopularParcial = 0;
            this.distribuicao.vlReceitaProponenteIntegral = 0;
            this.distribuicao.vlReceitaProponenteParcial = 0;
            this.distribuicao.vlReceitaPopularIntegral = 0;
            this.distribuicao.vlReceitaPopularParcial = 0;

            if (this.distribuicaoGratuita === NAO) {
                this.distribuicao.qtProponenteIntegral = this.obterQuantidadePorPercentual(this.valores.percentualProponente);
                this.distribuicao.qtProponenteParcial = this.obterQuantidadePorPercentual(this.valores.percentualProponente);
                this.distribuicao.qtPopularIntegral = this.obterQuantidadePorPercentual(this.valores.percentualPrecoPopular);
                this.distribuicao.qtPopularParcial = this.obterQuantidadePorPercentual(this.valores.percentualPrecoPopular);
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

            if (this.distribuicao.tpVenda === TIPO_EXEMPLAR) {
                this.distribuicao.qtPopularParcial = 0;
                this.distribuicao.qtProponenteParcial = 0;
                this.distribuicao.vlReceitaProponenteParcial = 0;
                this.distribuicao.vlReceitaPopularParcial = 0;
                this.labelInteira = '';
            }

            this.distribuicao.vlReceitaPrevista = this.converterParaMoedaAmericana(this.vlReceitaPrevista);
            this.distribuicao.qtGratuitaDivulgacao = parseInt(this.distribuicao.qtExemplares * this.valores.percentualGratuitoDivulgacao, 10);
            this.distribuicao.qtGratuitaPatrocinador = parseInt(this.distribuicao.qtExemplares * this.valores.percentualGratuitoPatrocinador, 10);
            this.distribuicao.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
        },
        editarDetalhamento(object) {
            const self = this;
            this.limparFormulario();
            if (object.idDetalhaPlanoDistribuicao != null) {
                self.visualizarFormulario = true;
                // definir o percentual do proponente
                const percentualProponente = (parseInt(object.qtProponenteIntegral, 10)
                    + parseInt(object.qtProponenteParcial, 10)) / parseInt(object.qtExemplares, 10);
                self.percentualProponente = Number((percentualProponente).toFixed(2));

                // definir o percentual do preco popular
                this.$nextTick(() => {
                    const percentualPrecoPopular = (parseInt(object.qtPopularIntegral, 10)
                        + parseInt(object.qtPopularParcial, 10))
                        / parseInt(object.qtExemplares, 10);
                    self.valores.percentualPrecoPopular = Number((percentualPrecoPopular).toFixed(2));
                });

                Object.assign(self.distribuicao, object);

                self.inputUnitarioPopularIntegral = self.formatarValor(object.vlUnitarioPopularIntegral);
                self.inputUnitarioProponenteIntegral = self.formatarValor(object.vlUnitarioProponenteIntegral);

                if (object.vlUnitarioPopularIntegral === 0 && object.vlUnitarioProponenteIntegral === 0) {
                    self.distribuicaoGratuita = SIM;
                }

                this.$nextTick(() => {
                    this.distribuicao.qtGratuitaDivulgacao = object.qtGratuitaDivulgacao;
                    this.distribuicao.qtGratuitaPatrocinador = object.qtGratuitaPatrocinador;
                });
            }
        },
        value(val) {
            this.visualizarFormulario = val;
        },
        visualizarFormulario(val) {
            this.$emit('input', val);
        },
    },
    mounted() {
        if (parseInt(this.idNormativo, 10) < INSTRUCAO_NORMATIVA_2019) {
            Object.assign(this.$data.valores, valoresNormativo2017);
        }
    },
    methods: {
        obterQuantidadePorPercentual(percentualDistribuicao) {
            const divisao = this.distribuicao.tpVenda === TIPO_EXEMPLAR ? 1 : 0.5;
            return parseInt((this.distribuicao.qtExemplares * percentualDistribuicao) * divisao, 10);
        },
        mostrarFormulario(id) {
            this.limparFormulario();
            this.visualizarFormulario = true;
            this.icon = this.visualizarFormulario ? 'visibility_off' : 'add';
            // eslint-disable-next-line
            const elm = $3(`#${id}`);
            // eslint-disable-next-line
            $3('html, body').animate({
                // eslint-disable-next-line
                scrollTop: $3(elm).offset().top + 30,
            }, 600);
        },
        limparFormulario() {
            Object.assign(this.$data, this.$options.data.apply(this));
        },
        salvar() {
            if (this.distribuicao.dsProduto === '' && this.distribuicao.tpVenda === 'i') {
                this.mensagemAlerta('\xC9 obrigat\xF3rio informar a categoria');
                this.$refs.dsProduto.focus();
                return;
            }

            if (this.distribuicao.qtExemplares === 0) {
                this.mensagemAlerta('Quantidade \xE9 obrigat\xF3rio!');
                this.$refs.qtExemplares.focus();
                return;
            }

            if (this.distribuicaoGratuita === NAO) {
                if (this.distribuicao.vlUnitarioProponenteIntegral === 0 && this.valores.percentualProponente > 0) {
                    this.mensagemAlerta('Pre\xE7o unit\xE1rio no Proponente \xE9 obrigat\xF3rio!');
                    return;
                }

                if (this.distribuicao.vlUnitarioPopularIntegral === 0 && this.valores.percentualPrecoPopular > 0) {
                    this.mensagemAlerta('Pre\xE7o unit\xE1rio no Pre\xE7o Popular \xE9 obrigat\xF3rio!');
                    return;
                }
            }

            if (this.valores.percentualProponente > this.valores.percentualProponentePadrao) {
                this.mensagemAlerta(`Percentual do Proponente n\u00E3o pode ser maior que ${this.valores.percentualProponentePadrao * 100}%`);
                return;
            }

            if (this.distribuicao.qtGratuitaPopulacao < this.qtGratuitaPopulacaoMinimo) {
                this.mensagemAlerta(`Quantidade para popula\xE7\xE3o n\xE3o pode ser menor que ${this.qtGratuitaPopulacaoMinimo}`);
                this.distribuicao.qtGratuitaPopulacao = this.qtGratuitaPopulacaoMinimo;
                this.$refs.populacao.focus();
                return;
            }

            this.$emit('eventoSalvarDetalhamento', this.distribuicao);
        },
    },
};
</script>
