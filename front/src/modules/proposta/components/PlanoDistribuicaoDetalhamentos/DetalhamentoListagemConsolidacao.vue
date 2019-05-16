<template>
    <tfoot
        v-if="detalhamentos && detalhamentos.length > 0"
        style="opacity: 0.5"
    >
        <tr>
            <td><b>Totais</b></td>
            <td class="center-align">
                <b>{{ qtExemplaresTotal }}</b>
            </td>
            <!--Fim: Preço Popular -->
            <td class="center-align">
                <b>{{ qtProponenteIntegralTotal }}</b>
            </td>
            <td class="center-align">
                <b>{{ qtProponenteParcialTotal }}</b>
            </td>
            <td class="right-align">
                -
            </td>
            <!--Preço Popular -->
            <td class="center-align">
                <b>{{ qtPopularIntegralTotal }}</b>
            </td>
            <td class="center-align">
                <b>{{ qtPopularParcialTotal }}</b>
            </td>
            <td class="right-align">
                -
            </td>
            <td class="center-align">
                <b>{{ qtDistribuicaoGratuitaTotal }}</b>
            </td>
            <td class="right-align">
                <b>{{ receitaPrevistaTotal }}</b>
            </td>
            <td v-if="!disabled"/>
        </tr>
    </tfoot>
</template>

<script>
import { utils } from '@/mixins/utils';
import numeral from 'numeral';

numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');

export default {
    name: 'DetalhamentoListagemConsolidacao',
    mixins: [utils],
    props: {
        detalhamentos: {
            type: Array,
            default: () => [],
        },
        disabled: {
            type: [String, Number],
            default: 1,
        },
    },
    computed: {
        qtExemplaresTotal() {
            return this.detalhamentos.reduce((total, value) => total + parseInt(value.qtExemplares, 10), 0);
        },
        qtDistribuicaoGratuitaTotal() {
            return this.detalhamentos.reduce((total, value) => total + (
                parseInt(value.qtGratuitaDivulgacao, 10)
                    + parseInt(value.qtGratuitaPatrocinador, 10)
                    + parseInt(value.qtGratuitaPopulacao, 10)), 0);
        },
        qtPopularIntegralTotal() {
            return this.detalhamentos.reduce((total, value) => total + parseInt(value.qtPopularIntegral, 10), 0);
        },
        qtPopularParcialTotal() {
            return this.detalhamentos.reduce((total, value) => total + parseInt(value.qtPopularParcial, 10), 0);
        },
        qtProponenteIntegralTotal() {
            return this.detalhamentos.reduce((total, value) => total + parseInt(value.qtProponenteIntegral, 10), 0);
        },
        qtProponenteParcialTotal() {
            return this.detalhamentos.reduce((total, value) => total + parseInt(value.qtProponenteParcial, 10), 0);
        },
        receitaPrevistaTotal() {
            const soma = numeral();

            soma.add(this.detalhamentos.reduce((total, value) => total + parseFloat(value.vlReceitaPrevista), 0));

            return soma.format();
        },
    },
};
</script>
