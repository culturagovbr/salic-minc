<template>
    <div class="row center-align">
        <table class="bordered">
            <thead v-if="detalhamentos && detalhamentos.length > 0">
                <tr>
                    <th
                        rowspan="2"
                        class="left-align">
                        Categoria
                    </th>
                    <th
                        rowspan="2"
                        class="center-align">
                        Quantidade
                    </th>
                    <th
                        class="proponente center-align"
                        colspan="3"
                    >
                        Proponente
                    </th>
                    <th
                        class="popular center-align"
                        colspan="3"
                    >
                        Pre&ccedil;o Popular
                    </th>
                    <th
                        class="gratuito center-align"
                        rowspan="2"
                    >
                        Distribui&ccedil;&atilde;o <br>Gratuita
                    </th>
                    <th
                        rowspan="2"
                        class="center-align"
                    >
                        Receita <br>Prevista
                    </th>
                    <th
                        v-if="!disabled"
                        rowspan="2"
                        width="120"
                        class="center-align"
                    >
                        A&ccedil;&otilde;es
                    </th>
                </tr>
                <tr style="background-color: #f2f2f2">
                    <th class="proponente center-align">
                        Qtd. Inteira
                    </th>
                    <th class="proponente center-align">
                        Qtd. Meia
                    </th>
                    <th class="proponente center-align">
                        Pre&ccedil;o <br> Unitario
                    </th>
                    <th class="popular center-align">
                        Qtd. Inteira
                    </th>
                    <th class="popular center-align">
                        Qtd. Meia
                    </th>
                    <th class="popular">
                        Pre&ccedil;o <br> Unitario
                    </th>
                </tr>
            </thead>
            <tbody
                v-if="detalhamentos && detalhamentos.length > 0">
                <tr
                    v-for="( detalhamento, index ) in detalhamentos"
                    :key="index">
                    <td>{{ detalhamento.dsProduto }}</td>
                    <td class="center-align">
                        {{ detalhamento.qtExemplares }}
                    </td>
                    <!--Preço Proponente -->
                    <td class="center-align">
                        {{ detalhamento.qtProponenteIntegral }}
                    </td>
                    <td class="center-align">
                        {{ detalhamento.qtProponenteParcial }}
                    </td>
                    <td class="right-align">
                        {{ detalhamento.vlUnitarioProponenteIntegral | filtroFormatarParaReal }}
                    </td>
                    <!--Preço Popular -->
                    <td class="center-align">
                        {{ detalhamento.qtPopularIntegral }}
                    </td>
                    <td class="center-align">
                        {{ detalhamento.qtPopularParcial }}
                    </td>
                    <td class="right-align">
                        {{ detalhamento.vlUnitarioPopularIntegral | filtroFormatarParaReal }}
                    </td>
                    <!-- Distribuicao Gratuita-->
                    <td class="center-align">
                        {{ parseInt(detalhamento.qtGratuitaDivulgacao, 10) +
                        parseInt(detalhamento.qtGratuitaPatrocinador, 10) + parseInt(detalhamento.qtGratuitaPopulacao, 10) }}
                    </td>
                    <td class="right-align">
                        {{ detalhamento.vlReceitaPrevista | filtroFormatarParaReal }}
                    </td>
                    <td v-if="!disabled">
                        <a
                            href="javascript:void(0)"
                            class="btn small waves-effect waves-light tooltipped btn-primary btn-editar"
                            data-tooltip="Editar detalhamento"
                            @click="editar(detalhamento)"
                        >
                            <i class="material-icons">edit</i>
                        </a>
                        <a
                            href="javascript:void(0)"
                            class="btn small waves-effect waves-light tooltipped btn-danger btn-excluir-item"
                            data-tooltip="Excluir detalhamento"
                            @click.prevent="excluir(detalhamento)"
                        >
                            <i class="material-icons">delete</i>
                        </a>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td
                        :colspan="disabled ? 10 : 11"
                        class="center-align"
                    >
                        Nenhum detalhamento cadastrado
                    </td>
                </tr>
            </tbody>
            <detalhamento-listagem-consolidacao
                :detalhamentos="detalhamentos"
                :disabled="disabled"
            />
        </table>

        <div
            v-if="detalhamentos && detalhamentos.length > 0"
            style="margin-top: 20px"
            class="row"
        >
            <div class="col s2 left-align">
                <b>Valor m&eacute;dio</b><br>
                <span :class="{ 'red-text text-darken-3' : isUltrapassouValorMedio}">R$ {{ valorMedioProponenteFormatado }}</span>
            </div>
            <div
                v-if="isUltrapassouValorMedio"
                class="col s10 red darken-3 white-text left-align">
                <p>O preço médio do ingresso ou produto é limitado a R$ <b>{{ valorMedioMaximo | filtroFormatarParaReal }}</b></p>
            </div>
        </div>
    </div>
</template>

<script>
import { utils } from '@/mixins/utils';
import numeral from 'numeral';
import DetalhamentoListagemConsolidacao from './DetalhamentoListagemConsolidacao';

numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');

const VALOR_MEDIO_MAXIMO = 225;

export default {
    name: 'DetalhamentoListagem',
    components: { DetalhamentoListagemConsolidacao },
    mixins: [utils],
    props: {
        disabled: {
            type: [String, Number],
            default: '',
        },
        canalAberto: {
            type: [String, Number],
            default: 0,
        },
        detalhamentos: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            valorMedioMaximo: VALOR_MEDIO_MAXIMO,
        };
    },
    computed: {
        valorMedioProponente() {
            const vlReceitaProponenteIntegral = numeral();
            const vlReceitaProponenteParcial = numeral();
            const qtProponenteIntegral = numeral();
            const qtProponenteParcial = numeral();

            for (let i = 0; i < this.detalhamentos.length; i += 1) {
                vlReceitaProponenteIntegral.add(this.detalhamentos[i].vlReceitaProponenteIntegral);
                vlReceitaProponenteParcial.add(parseFloat(this.detalhamentos[i].vlReceitaProponenteParcial));
                qtProponenteIntegral.add(parseFloat(this.detalhamentos[i].qtProponenteIntegral));
                qtProponenteParcial.add(parseFloat(this.detalhamentos[i].qtProponenteParcial));
            }

            return numeral(parseFloat(vlReceitaProponenteIntegral.value()
                + vlReceitaProponenteParcial.value())
                / (qtProponenteIntegral.value() + qtProponenteParcial.value()));
        },
        isUltrapassouValorMedio() {
            return (numeral(this.valorMedioProponente).value() > this.valorMedioMaximo
                && (parseInt(this.canalAberto, 10) === 0));
        },
        valorMedioProponenteFormatado() {
            return this.valorMedioProponente.format();
        },
    },
    methods: {
        excluir(detalhamento) {
            this.$emit('eventoRemoverDetalhamento', detalhamento);
        },
        editar(detalhamento) {
            // eslint-disable-next-line
            const elm = $3(`div[formIdMunicipio='${detalhamento.idMunicipio}']`);
            // eslint-disable-next-line
            $3('html, body').animate({
                // eslint-disable-next-line
                scrollTop: $3(elm).offset().top + 30,
            }, 600);

            this.$emit('eventoEditarDetalhamento', detalhamento);
        },
    },
};
</script>
