Vue.component('plano-distribuicao-detalhamentos-consolidacao', {
    template: `
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
                <td class="center-align"><b>{{ qtDistribuicaoGratuitaTotal }}</b></td>
                <td class="right-align"><b>{{ receitaPrevistaTotal }}</b></td>
                <td v-if="!disabled" colspan="2"></td>
            </tr>
        </tfoot>
    `,
    props: {
        detalhamentos: {},
        disabled: false
    },
    mixins: [utils],
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
        }
    }
});