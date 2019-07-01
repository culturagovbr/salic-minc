<template>
    <tfoot style="opacity: 0.8">
        <tr>
            <td><b>Totais</b></td>
            <td class="right-align">{{ qtExemplaresTotal }}</td>

            <td class="right-align">
                {{
                    parseInt(qtGratuitaDivulgacaoTotal) +
                        parseInt(qtGratuitaPatrocinadorTotal) +
                        parseInt(qtGratuitaPopulacaoTotal)
                }}
            </td>

            <td class="right-align">{{ qtPopularIntegralTotal }}</td>
            <td class="right-align">{{ qtPopularParcialTotal }}</td>
            <td class="right-align"> --- </td>

            <td class="right-align">{{ qtProponenteIntegralTotal }}</td>
            <td class="right-align">{{ qtProponenteParcialTotal }}</td>
            <td class="right-align"> --- </td>
            <td class="right-align">{{ receitaPrevistaTotal }}</td>
        </tr>
    </tfoot>
</template>
<script>

import numeral from 'numeral';
import 'numeral/locales';
import { utils } from '@/mixins/utils';

numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');


export default {
    name: 'PropostaDetalhamentoConsolidacao',
    mixins: [utils],
    props: {
        items: {
            type: Array,
            default: () => [],
        },
    },
    computed: {
        qtExemplaresTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                total += parseInt(this.items[i].qtExemplares, 10);
            }

            return total;
        },
        qtGratuitaDivulgacaoTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                total += parseInt(this.items[i].qtGratuitaDivulgacao, 10);
            }

            return total;
        },
        qtGratuitaPatrocinadorTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                total += parseInt(this.items[i].qtGratuitaPatrocinador, 10);
            }

            return total;
        },
        qtGratuitaPopulacaoTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                total += parseInt(this.items[i].qtGratuitaPopulacao, 10);
            }

            return total;
        },
        qtPopularIntegralTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                total += parseInt(this.items[i].qtPopularIntegral, 10);
            }

            return total;
        },
        qtPopularParcialTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                total += parseInt(this.items[i].qtPopularParcial, 10);
            }

            return total;
        },
        vlReceitaPopularIntegralTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                const vl = (this.items[i].vlReceitaPopularIntegral);
                total += numeral(vl).value();
            }

            return numeral(total).format('0,0.00');
        },
        vlReceitaPopularParcialTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                const vl = (this.items[i].vlReceitaPopularParcial);
                total += numeral(vl).value();
            }

            return numeral(total).format('0,0.00');
        },

        qtProponenteIntegralTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                total += parseInt(this.items[i].qtProponenteIntegral, 10);
            }

            return total;
        },
        qtProponenteParcialTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                total += parseInt(this.items[i].qtProponenteParcial, 10);
            }

            return total;
        },
        vlReceitaProponenteIntegralTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                const vl = (this.items[i].vlReceitaProponenteIntegral);
                total += this.converterParaMoedaAmericana(vl);
            }

            return numeral(total).format('0,0.00');
        },
        vlReceitaProponenteParcialTotal() {
            let total = 0;

            for (let i = 0; i < this.items.length; i += 1) {
                const vl = (this.items[i].vlReceitaProponenteParcial);
                total += this.converterParaMoedaAmericana(vl);
            }

            return numeral(total).format('0,0.00');
        },
        receitaPrevistaTotal() {
            const total = numeral();

            for (let i = 0; i < this.items.length; i += 1) {
                const vl = this.items[i].vlReceitaPrevista;
                total.add(parseFloat(vl));
            }

            return total.format('0,0.00');
        },
    },
};
</script>
