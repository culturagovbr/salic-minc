<template>
    <div  class="detalhamento-plano-distribuicao">
        <ul class="collapsible" data-collapsible="expandable">
            <li v-for="( detalhamento, index ) in detalhamentos" :key="index">
                <div class="collapsible-header">
                    <i class="material-icons">place</i>
                    Detalhamento - {{detalhamento[0].DescricaoUf}} - {{detalhamento[0].DescricaoMunicipio}}
                </div>
                <div class="collapsible-body no-padding margin20 scroll-x">
                    <table>
                        <thead v-if="detalhamento.length > 0">
                            <tr>
                                <th rowspan="2">Categoria</th>
                                <th rowspan="2">Qtd.</th>
                                <th class="center-align gratuito" rowspan="2">
                                    Dist. <br>Gratuita
                                </th>
                                <th class="center-align popular" colspan="3">
                                    Pre&ccedil;o Popular
                                </th>
                                <th class="center-align proponente" colspan="3">
                                    Proponente
                                </th>
                                <th rowspan="2" class="center-align">Receita <br> Prevista</th>
                            </tr>
                            <tr>
                                <th class="right-align popular">Qtd. Inteira</th>
                                <th class="right-align popular">Qtd. Meia</th>
                                <th class="right-align popular">Pre&ccedil;o <br> Unit&aacute;rio</th>
                                <th class="right-align proponente">Qtd. Inteira</th>
                                <th class="right-align proponente">Qtd. Meia</th>
                                <th class="right-align proponente">Pre&ccedil;o <br> Unit&aacute;rio</th>
                            </tr>
                        </thead>
                        <tbody v-if="detalhamento.length > 0">
                            <tr v-for="( item, index ) in detalhamento" :key="index">
                                <td>{{item.dsProduto}}</td>
                                <td class="right-align">{{ item.qtExemplares }}</td>

                                <td class="right-align">{{ parseInt(item.qtGratuitaDivulgacao) +
                                    parseInt(item.qtGratuitaPatrocinador) + parseInt(item.qtGratuitaPopulacao) }}
                                </td>

                                <td class="right-align">{{ item.qtPopularIntegral }}</td>
                                <td class="right-align">{{ item.qtPopularParcial }}</td>
                                <td class="right-align">{{ item.vlUnitarioPopularIntegral | filtroFormatarParaReal }}</td>

                                <td class="right-align">{{ item.qtProponenteIntegral }}</td>
                                <td class="right-align">{{ item.qtProponenteParcial }}</td>
                                <td class="right-align">{{ item.vlUnitarioProponenteIntegral | filtroFormatarParaReal }}</td>

                                <td class="right-align">{{ item.vlReceitaPrevista | filtroFormatarParaReal }}</td>

                            </tr>
                        </tbody>
                        <PropostaDetalhamentoConsolidacao
                                :items="detalhamento"></PropostaDetalhamentoConsolidacao>
                    </table>
                </div>
            </li>
        </ul>
    </div>
</template>
<script>
import planilhas from '@/mixins/planilhas';
import PropostaDetalhamentoConsolidacao from './PropostaDetalhamentoConsolidacao';

export default {
    name: 'PropostaDetalhamentoPlanoDistribuicao',
    data() {
        return {
            detalhamentos: [],
        };
    },
    mixins: [planilhas],
    props: [
        'arrayDetalhamentos',
    ],
    components: {
        PropostaDetalhamentoConsolidacao,
    },
    watch: {
        arrayDetalhamentos(value) {
            this.detalhamentos = this.montarVisualizacao(value);
        },
    },
    mounted() {
        if (typeof this.arrayDetalhamentos !== 'undefined') {
            this.iniciarCollapsible();
            this.detalhamentos = this.montarVisualizacao(this.arrayDetalhamentos);
        }
    },
    methods: {
        iniciarCollapsible() {
            // eslint-disable-next-line
            $3('.detalhamento-plano-distribuicao .collapsible').each(function () {
                // eslint-disable-next-line
                $3(this).collapsible();
            });
        },
        montarVisualizacao(detalhamentos) {
            const novoDetalhamento = {};
            let i = 0;
            let idMunicipio = '';

            detalhamentos.forEach((element) => {
                if (element.idMunicipio !== idMunicipio) {
                    novoDetalhamento[element.idMunicipio] = [];
                    i = 0;
                    idMunicipio = element.idMunicipio;
                }

                novoDetalhamento[element.idMunicipio][i] = element;

                i += 1;
            });

            return novoDetalhamento;
        },
    },
};
</script>
