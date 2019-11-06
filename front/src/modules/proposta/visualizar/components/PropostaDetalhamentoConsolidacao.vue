<template>
    <tr style="opacity: 0.5">
        <td><b>Totais</b></td>
        <td class="text-xs-right">{{ qtExemplaresTotal }}</td>

        <td class="text-xs-right">
            {{
                parseInt(qtGratuitaDivulgacaoTotal) +
                    parseInt(qtGratuitaPatrocinadorTotal) +
                    parseInt(qtGratuitaPopulacaoTotal)
            }}
        </td>

        <td class="text-xs-right">{{ qtPopularIntegralTotal }}</td>
        <td class="text-xs-right">{{ qtPopularParcialTotal }}</td>
        <td class="text-xs-right"> --- </td>

        <td class="text-xs-right">{{ qtProponenteIntegralTotal }}</td>
        <td class="text-xs-right">{{ qtProponenteParcialTotal }}</td>
        <td class="text-xs-right"> --- </td>
        <td class="text-xs-right">{{ receitaPrevistaTotal | formatarParaReal }}</td>
    </tr>
</template>
<script>

import { utils } from '@/mixins/utils';

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
        receitaPrevistaTotal() {
            let total = 0;
            for (let i = 0; i < this.items.length; i += 1) {
                const vl = this.items[i].vlReceitaPrevista;
                total += parseFloat(vl);
            }
            return total;
        },
    },
};
</script>
