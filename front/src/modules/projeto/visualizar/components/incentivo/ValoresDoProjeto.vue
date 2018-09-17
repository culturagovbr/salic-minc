<template>
    <fieldset>
        <legend>Valores em R$</legend>
        <table class="tabela planilha-proposta" :class="verificarSePlanilhaAtiva('planilha-proposta')">
            <tr class="destacar">
                <td align="center" colspan="5">
                    <b>
                        1 - Planilha de Solicita&ccedil;&atilde;o da proposta original
                    </b>
                    {{ mensagemPlanilhaAtiva('planilha-proposta') }}
                </td>
            </tr>
            <tr class="destacar">
                <td class="right-align"><b>Solicitado (A)</b></td>
                <td class="right-align"><b>Outras Fontes (B)</b></td>
                <td class="right-align"><b>Total Proposta (C=A+B)</b></td>
            </tr>
            <tr>
                <td class="right-align destaque-texto"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlSolicitadoOriginal"/>
                </b></td>
                <td class="right-align destaque-texto-secondary"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlOutrasFontesPropostaOriginal"/>
                </b>
                </td>
                <td class="right-align destaque-texto-primary">
                    <b>
                        <router-link v-if="dadosProjeto.vlTotalPropostaOriginal > 0"
                                     :to="{ name: 'planilhaproposta', params: { idPronac: idPronac }}">
                            <SalicFormatarValor :valor="dadosProjeto.vlTotalPropostaOriginal"/>
                        </router-link>
                        <SalicFormatarValor v-else :valor="dadosProjeto.vlTotalPropostaOriginal"/>
                    </b>
                </td>
            </tr>
        </table>
        <table class="tabela planilha-autorizada" :class="verificarSePlanilhaAtiva('planilha-autorizada')">
            <tr class="destacar">
                <td align="center" colspan="3">
                    <b>
                        2 - Planilha Autorizada para Captar
                    </b>
                    {{ mensagemPlanilhaAtiva('planilha-autorizada') }}
                </td>
            </tr>
            <tr class="destacar">
                <td class="right-align"><b>Autorizado (D)</b></td>
                <td class="right-align"><b>Outras fontes (E)</b></td>
                <td class="right-align"><b>Total Autorizado (F=D+E)</b></td>
            </tr>
            <tr>
                <td class="right-align destaque-texto"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlAutorizado"/>
                </b></td>
                <td class="right-align destaque-texto-secondary"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlAutorizadoOutrasFontes"/>
                </b></td>
                <td class="right-align destaque-texto-primary">
                    <b>
                        <router-link
                            v-if="dadosProjeto.vlTotalAutorizado > 0 && parseInt(dadosProjeto.idNormativo) > 6"
                            :to="{ name: 'planilhaautorizada', params: { idPronac: idPronac }}">
                            <SalicFormatarValor :valor="dadosProjeto.vlTotalAutorizado"/>
                        </router-link>
                        <router-link
                            v-else-if="dadosProjeto.vlTotalAutorizado > 0 && parseInt(dadosProjeto.idNormativo) <= 6"
                            :to="{ name: 'planilhaaprovada', params: { idPronac: idPronac }}">
                            <SalicFormatarValor :valor="dadosProjeto.vlTotalAutorizado"/>
                        </router-link>
                        <SalicFormatarValor v-else :valor="dadosProjeto.vlTotalAutorizado"/>
                    </b>
                </td>
            </tr>
        </table>

        <table class="tabela planilha-adequada" :class="verificarSePlanilhaAtiva('planilha-adequada')">
            <tr class="destacar">
                <td align="center" colspan="3">
                    <b>
                        3 - Planilha Adequada &agrave; realidade de execu&ccedil;&atilde;o pelo proponente
                    </b>
                    {{ mensagemPlanilhaAtiva('planilha-adequada') }}
                </td>
            </tr>
            <tr class="destacar">
                <td class="right-align"><b>Adequado (G)</b></td>
                <td class="right-align"><b>Outras fontes (H)</b></td>
                <td class="right-align"><b>Total Adequado (I=G+H)</b></td>
            </tr>
            <tr>
                <td class="right-align destaque-texto"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlAdequadoIncentivo"/>
                </b></td>
                <td class="right-align destaque-texto-secondary"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlAdequadoOutrasFontes"/>
                </b></td>
                <td class="right-align destaque-texto-primary">
                    <b>
                        <router-link v-if="dadosProjeto.vlTotalAdequado > 0"
                                     :to="{ name: 'planilhaadequada', params: { idPronac: idPronac }}">
                            <SalicFormatarValor :valor="dadosProjeto.vlTotalAdequado"/>
                        </router-link>
                        <SalicFormatarValor v-else :valor="dadosProjeto.vlTotalAdequado"/>
                    </b>
                </td>
            </tr>
        </table>

        <table class="tabela planilha-homologada" :class="verificarSePlanilhaAtiva('planilha-homologada')">
            <tr class="destacar">
                <td align="center" colspan="3">
                    <b>
                        4 - Planilha Homologada para execu&ccedil;&atilde;o
                    </b>
                    {{ mensagemPlanilhaAtiva('planilha-homologada') }}
                </td>
            </tr>
            <tr class="destacar">
                <td class="right-align"><b>Homologado (J)</b></td>
                <td class="right-align"><b>Outras fontes (K)</b></td>
                <td class="right-align"><b>Total Homologado (L=J+K)</b></td>
            </tr>
            <tr>
                <td class="right-align destaque-texto"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlHomologadoIncentivo"/>
                </b></td>
                <td class="right-align destaque-texto-secondary"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlHomologadoOutrasFontes"/>
                </b></td>
                <td class="right-align destaque-texto-primary">
                    <b>
                        <router-link v-if="dadosProjeto.vlTotalHomologado > 0"
                                     :to="{ name: 'planilhahomologada', params: { idPronac: idPronac }}">
                            <SalicFormatarValor :valor="dadosProjeto.vlTotalHomologado"/>
                        </router-link>
                        <SalicFormatarValor v-else :valor="dadosProjeto.vlTotalHomologado"/>
                    </b>
                </td>
            </tr>
        </table>

        <table class="tabela planilha-readequada" :class="verificarSePlanilhaAtiva('planilha-readequada')">
            <tr class="destacar">
                <td align="center" colspan="3">
                    <b>
                        5 - Planilha Readequada na execu&ccedil;&atilde;o
                    </b>
                    {{ mensagemPlanilhaAtiva('planilha-readequada') }}
                </td>
            </tr>
            <tr class="destacar">
                <td class="right-align"><b>Readequado (M)</b></td>
                <td class="right-align"><b>Outras fontes (N)</b></td>
                <td class="right-align"><b>Total Readequado (O=M+N)</b></td>
            </tr>
            <tr>
                <td class="right-align destaque-texto">
                    <b>
                        <SalicFormatarValor :valor="dadosProjeto.vlReadequadoIncentivo"/>
                    </b>
                </td>
                <td class="right-align destaque-texto-secondary"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlReadequadoOutrasFontes"/>
                </b></td>
                <td class="right-align destaque-texto-primary">
                    <b>
                        <router-link v-if="dadosProjeto.vlTotalReadequado > 0"
                                     :to="{ name: 'planilhareadequada', params: { idPronac: idPronac }}">
                            <SalicFormatarValor :valor="dadosProjeto.vlTotalReadequado"/>
                        </router-link>
                        <SalicFormatarValor v-else :valor="dadosProjeto.vlTotalReadequado"/>
                    </b>
                </td>
            </tr>
        </table>

        <table class="tabela">
            <tr class="destacar">
                <td align="center" colspan="5">
                    <b>
                        Capta&ccedil;&atilde;o de recursos
                    </b>
                </td>
            </tr>
            <tr class="destacar">
                <td class="right-align"><b>Captado(P)</b></td>
                <td class="right-align"><b>Transferido(Q)</b></td>
                <td class="right-align"><b>Recebido(R)</b></td>
                <td class="right-align"><b>Saldo a captar(S)</b></td>
                <td class="right-align"><b>% Captado(T)</b></td>
            </tr>
            <tr>
                <td class="right-align destaque-texto-primary destacar-celula">
                    <b>
                        <a :href="'/default/consultardadosprojeto/dados-bancarios-captacao?idPronac=' + idPronac">
                            <SalicFormatarValor :valor="dadosProjeto.vlCaptado"/>
                        </a>
                    </b>
                </td>
                <td class="right-align">
                    <b v-if="dadosProjeto.vlTransferido === '0'">
                        <SalicFormatarValor :valor="dadosProjeto.vlTransferido"/>
                    </b>
                    <b v-else>
                        <TransferenciaRecursos :valor="dadosProjeto.vlTransferido" :acao="'transferidor'"></TransferenciaRecursos>
                    </b>
                </td>
                <td class="right-align">
                    <b v-if="dadosProjeto.vlRecebido === '0'">
                        <SalicFormatarValor :valor="dadosProjeto.vlRecebido"/>
                    </b>
                    <b v-else>
                        <TransferenciaRecursos :valor="dadosProjeto.vlRecebido" :acao="'recebedor'"></TransferenciaRecursos>
                    </b>
                </td>
                <td class="right-align destacar-celula"><b>
                    <SalicFormatarValor :valor="dadosProjeto.vlSaldoACaptar"/>
                </b></td>
                <td class="right-align destaque-texto-primary">
                    <b>
                        <a :href="'/default/consultardadosprojeto/dados-bancarios-captacao?idPronac=' + idPronac">
                            <SalicFormatarValor :valor="dadosProjeto.PercentualCaptado"/>
                        </a>
                    </b>
                </td>
            </tr>
        </table>
        <table class="tabela" v-if="dadosProjeto.PercentualComprovado > 0">
            <tr class="destacar">
                <td align="center" colspan="3">
                    <b>
                        Comprova&ccedil;&atilde;o
                    </b>
                </td>
            </tr>
            <tr class="destacar">
                <td class="right-align"><b>Comprovado(U)</b></td>
                <td class="right-align"><b>A Comprovar(V=S-U)</b></td>
                <td class="right-align"><b>% Comprovado(X)</b></td>
            </tr>
            <tr>
                <td class="right-align destaque-texto-primary destacar-celula">
                    <b>
                        <router-link v-if="dadosProjeto.vlComprovado > 0"
                                     :to="{ name: 'relacaodepagamentos', params: { idPronac: idPronac }}">
                            <SalicFormatarValor :valor="dadosProjeto.vlComprovado"/>
                        </router-link>
                        <SalicFormatarValor v-else :valor="dadosProjeto.vlComprovado"/>
                    </b>
                </td>
                <td class="right-align destacar-celula">
                    <b>
                        <SalicFormatarValor :valor="dadosProjeto.vlAComprovar"/>
                    </b>
                </td>
                <td class="right-align destaque-texto-primary">
                    <b>
                        <router-link :to="{ name: 'relacaodepagamentos', params: { idPronac: idPronac }}">
                            <SalicFormatarValor :valor="dadosProjeto.PercentualComprovado"/>
                        </router-link>
                    </b>
                </td>
            </tr>
        </table>
    </fieldset>
</template>
<script>
    import { utils } from '@/mixins/utils';
    import SalicFormatarValor from '@/components/SalicFormatarValor';
    import TransferenciaRecursos from './TransferenciaRecursos';

    export default {
        components: {
            SalicFormatarValor,
            TransferenciaRecursos,
        },
        mixins: [utils],
        props: {
            dadosProjeto: {},
            idPronac: 0,
        },
        methods: {
            obterPlanilhaAtiva() {
                let planilhaAtiva = '';

                switch (true) {
                case (parseFloat(this.dadosProjeto.vlTotalReadequado) > 0) :
                    planilhaAtiva = 'planilha-readequada';
                    break;
                case (parseFloat(this.dadosProjeto.vlTotalHomologado) > 0) :
                    planilhaAtiva = 'planilha-homologada';
                    break;
                case (parseFloat(this.dadosProjeto.vlTotalAdequado) > 0) :
                    planilhaAtiva = 'planilha-adequada';
                    break;
                case (parseFloat(this.dadosProjeto.vlTotalAutorizado) > 0) :
                    planilhaAtiva = 'planilha-autorizada';
                    break;
                case (parseFloat(this.dadosProjeto.vlTotalPropostaOriginal) > 0) :
                    planilhaAtiva = 'planilha-proposta';
                    break;
                default :
                    planilhaAtiva = '';
                    break;
                }

                return planilhaAtiva;
            },
            verificarSePlanilhaAtiva(nomePlanilha) {
                return {
                    active: nomePlanilha === this.obterPlanilhaAtiva(),
                };
            },
            mensagemPlanilhaAtiva(nomePlanilha) {
                return (nomePlanilha === this.obterPlanilhaAtiva()) ? '(PLANILHA ATUAL)' : '';
            },
        },
    };
</script>
