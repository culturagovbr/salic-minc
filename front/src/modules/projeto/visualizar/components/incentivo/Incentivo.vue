<template>
    <div id="conteudo">
        <div class="row" v-if="dadosProjeto.ProponenteInabilitado">
            <div style="background-color: #EF5350; text-transform: uppercase"
                 class="darken-2 padding10 white-text center-align">
                <div><b>Proponente Inabilitado</b></div>
            </div>
        </div>
        <div v-if="Object.keys(dadosProjeto).length > 0">
            <table class="tabela">
                <tr class="destacar">
                    <td><b>PRONAC</b></td>
                    <td><b>Nome do Projeto</b></td>
                </tr>
                <tr>
                    <td>{{ dadosProjeto.Pronac }}</td>
                    <td>{{ dadosProjeto.NomeProjeto }}</td>
                </tr>
                <tr class="destacar">
                    <td><b>CNPJ/CPF</b></td>
                    <td><b>Proponente</b></td>
                </tr>
                <tr>
                    <td>
                        <span v-if="dadosProjeto.idUsuarioExterno"><SalicFormatarCpfCnpj
                            :cpf="dadosProjeto.CgcCPf"/></span>
                        <a v-else
                           :href="'/default/relatorio/resultado-projeto?cnpfcpf=' + dadosProjeto.CgcCPf">
                            <SalicFormatarCpfCnpj :cpf="dadosProjeto.CgcCPf"/>
                        </a>
                    </td>
                    <td>
                        <span v-html="dadosProjeto.Proponente"></span>
                    </td>
                </tr>
            </table>
            <table class="tabela">
                <tr class="destacar">
                    <td class="centro"><b>UF</b></td>
                    <td class="centro"><b>Mecanismo</b></td>
                    <td class="centro"><b>&Aacute;rea Cultural</b></td>
                    <td class="centro"><b>Segmento Cultural</b></td>
                    <td class="centro"><b>Enquadramento</b></td>
                </tr>
                <tr>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.UfProjeto"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.Mecanismo"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.Area"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.Segmento"/>
                    </td>
                    <td align="center" class="bold destacar-celula">
                        <SalicTextoSimples :texto="dadosProjeto.Enquadramento"/>
                    </td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <td class="centro">
                        <b>
                            N&ordm; Proposta
                        </b>
                    </td>
                    <td class="centro"><b>Data Fixa</b></td>
                    <td class="centro"><b>Processo</b></td>
                    <td class="centro">
                        <b>
                            Prorroga&ccedil;&atilde;o autom&aacute;tica
                        </b>
                    </td>
                    <td class="centro">
                        <b>
                            Plano de Execu&ccedil;&atilde;o Imediata
                        </b>
                    </td>
                </tr>
                <tr>
                    <td align="center" class="bold destaque-texto-primary">
                        <router-link v-if="dadosProjeto.idPreProjeto" :to="{ name: 'proposta', params: { idPronac: idPronac }}">
                            {{ dadosProjeto.idPreProjeto }}
                        </router-link>
                        <SalicTextoSimples v-else :texto="dadosProjeto.idPreProjeto"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.DataFixa"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.Processo"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.ProrrogacaoAutomatica"/>
                    </td>
                    <td align="center" class="destacar-celula">
                        <SalicTextoSimples :texto="dadosProjeto.PlanoExecucaoImediata"/>
                    </td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <td colspan="2" class="centro bold">
                        Per&iacute;odo de capta&ccedil;&atilde;o
                    </td>
                    <td colspan="2" class="centro bold">
                        Per&iacute;odo de execu&ccedil;&atilde;o
                    </td>
                    <td colspan="3" class="centro bold">
                        Per&iacute;odo Vigente
                    </td>
                </tr>
                <tr class="destacar">
                    <td class="centro">
                        <b>
                            Dt. In&iacute;cio
                        </b>
                    </td>
                    <td class="centro">
                        <b>
                            Dt. Final
                        </b>
                    </td>
                    <td class="centro">
                        <b>
                            Dt. In&iacute;cio
                        </b>
                    </td>
                    <td class="centro">
                        <b>
                            Dt. Final
                        </b>
                    </td>
                    <td class="centro">
                        <b>
                            Tipo de portaria
                        </b>
                    </td>
                    <td class="centro">
                        <b>
                            N&ordm; Portaria
                        </b>
                    </td>
                    <td class="centro">
                        <b>
                            Dt. Publica&ccedil;&atilde;o
                        </b>
                    </td>
                </tr>
                <tr>
                    <td align="center"
                        class="bold destacar-celula text-darken-2"
                        :class="[ isDataExpirada(dadosProjeto.DtFimCaptacao) ? 'orange-text' : 'green-text' ]"
                    >{{ dadosProjeto.DtInicioCaptacao | formatarData }}
                    </td>
                    <td align="center"
                        class="bold destacar-celula text-darken-2"
                        :class="[ isDataExpirada(dadosProjeto.DtFimCaptacao) ? 'orange-text' : 'green-text' ]"
                    >{{ dadosProjeto.DtFimCaptacao | formatarData }}
                    </td>
                    <td align="center"
                        class="bold destacar-celula text-darken-2"
                        :class="[ isDataExpirada(dadosProjeto.DtFimExecucao) ? 'orange-text' : 'green-text' ]"
                    >{{ dadosProjeto.DtInicioExecucao | formatarData }}
                    </td>
                    <td align="center"
                        class="bold destacar-celula text-darken-2"
                        :class="[ isDataExpirada(dadosProjeto.DtFimExecucao) ? 'orange-text' : 'green-text' ]"
                    >{{ dadosProjeto.DtFimExecucao | formatarData }}
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.NrPortariaVigente"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.TipoPortariaVigente"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.DtPublicacaoPortariaVigente"/>
                    </td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <td align="center" colspan="5">
                        <b>
                            Informa&ccedil;&otilde;es banc&aacute;rias
                        </b>
                    </td>
                </tr>
                <tr class="destacar">
                    <td class="centro" rowspan="2">
                        <b>
                            Ag&ecirc;ncia
                        </b>
                    </td>
                    <td class="centro" colspan="2">
                        <b>
                            N&uacute;meros das Contas
                        </b>
                    </td>
                    <td class="centro" rowspan="2" width="10%">
                        <b>
                            Conta Liberada
                        </b>
                    </td>
                    <td class="centro" rowspan="2">
                        <b>
                            Dt. Libera&ccedil;&atilde;o
                        </b>
                    </td>
                </tr>
                <tr class="destacar">
                    <td class="centro">
                        <b>
                            Capta&ccedil;&atilde;o
                        </b>
                    </td>
                    <td class="centro">
                        <b>
                            Movimenta&ccedil;&atilde;o
                        </b>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.AgenciaBancaria | formatarAgencia"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.ContaCaptacao | formatarConta"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.ContaMovimentacao | formatarConta"/>
                    </td>
                    <td align="center" class="destacar-celula">
                        <SalicTextoSimples :texto="dadosProjeto.ContaBancariaLiberada"/>
                    </td>
                    <td align="center">
                        <SalicTextoSimples :texto="dadosProjeto.DtLiberacaoDaConta"/>
                    </td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <td class="centro">
                        <b>
                            S&iacute;ntese do projeto
                        </b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <SalicTextoSimples :texto="dadosProjeto.ResumoProjeto"/>
                    </td>
                </tr>
            </table>

            <div v-if="dadosProjeto.EmAnaliseNaCNIC" class="row">
                <div style="background-color: #EF5350" class="darken-2 padding10 white-text">
                    A T E N &Ccedil; &Atilde; O: Projeto em an&aacute;lise pela Comiss&atilde;o Nacional
                    de Incentivo &agrave; Cultura-CNIC. Aguardar resultado da avalia&ccedil;&atilde;o.
                </div>
            </div>
            <table v-else class="tabela">
                <tr class="destacar">
                    <td align="center" colspan="4">
                        <b>
                            Situa&ccedil;&atilde;o do projeto
                        </b>
                    </td>
                </tr>
                <tr class="destacar">
                    <td align="center">
                        <b>
                            Dt.Situa&ccedil;&atilde;o
                        </b>
                    </td>
                    <td class="left-align">
                        <b>
                            Situa&ccedil;&atilde;o
                        </b>
                    </td>
                    <td class="left-align">
                        <b>
                            Provid&ecirc;ncia Tomada
                        </b>
                    </td>
                    <td align="center">
                        <b>
                            Localiza&ccedil;&atilde;o atual
                        </b>
                    </td>
                </tr>
                <tr>
                    <td align="center">{{ dadosProjeto.DtSituacao | formatarData }}</td>
                    <td class="left-align destacar-celula">
                        <SalicTextoSimples :texto="dadosProjeto.Situacao"/>
                    </td>
                    <td class="left-align">
                        <SalicTextoSimples :texto="dadosProjeto.ProvidenciaTomada"/>
                    </td>
                    <td align="center" class="bold">{{ dadosProjeto.LocalizacaoAtual }}</td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <td align="center" colspan="2"><b>Fase do projeto</b></td>
                </tr>
                <tr class="destacar">
                    <td class="left-align"><b>Fase</b></td>
                    <td align="center">
                        <b>
                            Dt. in&iacute;cio
                        </b>
                    </td>
                </tr>
                <tr>
                    <td class="left-align">{{ dadosProjeto.FaseProjeto }}</td>
                    <td align="center">{{ dadosProjeto.dtInicioFase | formatarData }}</td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <td align="center" colspan="3">
                        <b>
                            Normativo vigente na apresenta&ccedil;&atilde;o do projeto
                        </b>
                    </td>
                </tr>
                <tr class="destacar">
                    <td class="left-align"><b>Normativo</b></td>
                    <td align="center">
                        <b>
                            Dt. Publica&ccedil;&atilde;o
                        </b>
                    </td>
                    <td align="center">
                        <b>
                            Dt. Revoga&ccedil;&atilde;o
                        </b>
                    </td>
                </tr>
                <tr>
                    <td class="left-align destacar-celula">{{ dadosProjeto.Normativo }}</td>
                    <td align="center">{{ dadosProjeto.dtPublicacaoNormativo | formatarData }}</td>
                    <td align="center">{{ dadosProjeto.dtRevogacaoNormativo | formatarData }}</td>
                </tr>
            </table>

            <table class="tabela" v-if="dadosProjeto.DtArquivamento">
                <caption style="color: red !important;">Arquivado Definitivamente</caption>
                <tr class="destacar">
                    <td align="center"><b>Dt.Arquivamento</b></td>
                    <td align="center"><b>Cx.Inicial</b></td>
                    <td align="center"><b>Cx.Final</b></td>
                </tr>
                <tr>
                    <td align="center">{{ dadosProjeto.DtArquivamento | formatarData }}</td>
                    <td align="center">{{ dadosProjeto.CaixaInicio }}</td>
                    <td align="center">{{ dadosProjeto.CaixaFinal }}</td>
                </tr>
            </table>
            <ValoresDoProjeto :idPronac="idPronac" :dadosProjeto="dadosProjeto"/>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import { mapGetters } from 'vuex';
    import { utils } from '@/mixins/utils';
    import Carregando from '@/components/Carregando';
    import SalicTextoSimples from '@/components/SalicTextoSimples';
    import SalicFormatarCpfCnpj from '@/components/SalicFormatarCpfCnpj';
    import ValoresDoProjeto from './ValoresDoProjeto';

    export default {
        data() {
            return {
                loading: true,
                idPronac: this.$route.params.idPronac,
                ProponenteInabilitado: false,
                emAnaliseNaCNIC: false,
            };
        },
        mixins: [utils],
        components: {
            Carregando,
            SalicTextoSimples,
            SalicFormatarCpfCnpj,
            ValoresDoProjeto,
        },
        created() {
            if (Object.keys(this.dadosProjeto).length > 0) {
                this.loading = false;
            }
        },
        watch: {
            dadosProjeto(value) {
                if (Object.keys(value).length > 0) {
                    this.loading = false;
                    this.idPronac = this.dadosProjeto.idPronac;
                }
            },
        },
        computed: {
            ...mapGetters({
                dadosProjeto: 'projeto/projeto',
            }),
        },
        methods: {
            isDataExpirada(date) {
                return moment()
                    .diff(date, 'days') > 0;
            },

        },
        filters: {
            formatarData(date) {
                if (date.length === 0) {
                    return '-';
                }
                return moment(date)
                    .format('DD/MM/YYYY');
            },
            formatarAgencia(agencia) {
                // formato: 9999-9
                if (agencia.length === 5) {
                    agencia = agencia.replace(/(\d{4})(\S)/, '$1-$2');
                }
                return agencia;
            },
            formatarConta(conta) {
                // formato: 99999-9 ou 99999-x
                var regex = /^(0+)(\d+)(\S{1})$/;
                return conta.replace(regex, '$2-$3');
            },
        },
    };
</script>
