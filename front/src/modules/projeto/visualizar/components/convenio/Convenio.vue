<template>
    <div id="conteudo">
        <div>
            <div class="row" v-if="dadosProjeto.ProponenteInabilitado">
                <div style="background-color: #EF5350; text-transform: uppercase"
                     class="darken-2 padding10 white-text center-align">
                    <div><b>Proponente Inabilitado</b></div>
                </div>
            </div>

            <table class="tabela">
                <tr class="destacar">
                    <td><b>Pronac</b></td>
                    <td><b>Nome do Projeto</b></td>
                </tr>
                <tr>
                    <td>{{dadosProjeto.Pronac}}</td>
                    <td>{{dadosProjeto.NomeProjeto}}</td>
                </tr>
                <tr class="destacar">
                    <td><b>CNPJ / CPF</b></td>
                    <td><b>Convenente</b></td>
                </tr>
                <tr>
                    <td v-if="dadosProjeto.CNPJ_CPF">
                        <a v-if="!dadosProjeto.isProponente"
                           :href="'/default/relatorio/resultado-projeto?cnpfcpf=' + dadosProjeto.CNPJ_CPF">
                            <SalicFormatarCpfCnpj :cpf="dadosProjeto.CNPJ_CPF"/>
                        </a>
                        <SalicFormatarCpfCnpj v-else :cpf="dadosProjeto.CNPJ_CPF"/>
                    </td>
                    <td v-else>Dado não informado!</td>
                    <td>{{dadosProjeto.Proponente}}</td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <th align="center"><b>UF</b></th>
                    <th align="center"><b>Mecanismo</b></th>
                    <th align="center"><b>&Aacute;rea Cultural</b></th>
                    <th align="center"><b>Segmento Cultural</b></th>
                    <th align="center"><b>Processo</b></th>
                </tr>
                <tr>
                    <td align="center">{{dadosProjeto.UfProjeto}}</td>
                    <td align="center">{{dadosProjeto.Mecanismo}}</td>
                    <td align="center">{{dadosProjeto.Area}}</td>
                    <td align="center">{{dadosProjeto.Segmento}}</td>
                    <td align="center">{{dadosProjeto.Processo}}</td>
                </tr>
            </table>

            <table class="tabela" v-if="dadosProjeto.DtConvenio">
                <tr class="destacar">
                    <th align="center" rowspan="2"><b>Per&iacute;odo de vig&ecirc;ncia</b></th>
                    <th align="center" colspan="3"><b>Conv&ecirc;nio</b></th>
                </tr>
                <tr class="destacar">
                    <th align="center"><b>N&ordm; Conv&ecirc;nio</b></th>
                    <th align="center"><b>Dt. Conv&ecirc;nio</b></th>
                    <th align="center"><b>Dt. Publica&ccedil;&atilde;o</b></th>
                </tr>
                <tr>
                    <td align="center">{{dadosProjeto.DtConvenioPrimeiraVigencia}} &agrave;
                        {{dadosProjeto.DtConvenioUltimaVigencia}}
                    </td>
                    <!-- OBS:período entre essas datas-->
                    <td align="center">{{dadosProjeto.NrConvenio}}</td>
                    <td align="center">{{dadosProjeto.DtConvenio}}</td>
                    <td align="center">{{dadosProjeto.DtConvenioPublicacao}}</td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <th align="center">S&iacute;ntese do Projeto</th>
                </tr>
                <tr>
                    <td align="justify">{{dadosProjeto.ResumoProjeto}}</td>
                </tr>
                <tr class="destacar" v-if="dadosProjeto.Objeto">
                    <th align="center">Objeto</th>
                </tr>
                <tr v-if="dadosProjeto.Objeto">
                    <td align="justify">{{dadosProjeto.Objeto}}</td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <th align="center" colspan="4"><b>Situa&ccedil;&atilde;o do Projeto</b></th>
                </tr>
                <tr class="destacar">
                    <th>Dt. Situa&ccedil;&atilde;o</th>
                    <th>Situa&ccedil;&atilde;o</th>
                    <th>Provid&ecirc;ncia Tomada</th>
                    <th>Localiza&ccedil;&atilde;o Atual</th>
                </tr>
                <tr>
                    <td align="center">{{dadosProjeto.DtSituacao}}</td>
                    <td align="center">{{dadosProjeto.Situacao}}</td>
                    <td align="center">{{dadosProjeto.ProvidenciaTomada}}</td>
                    <td align="center">{{dadosProjeto.LocalizacaoAtual}}</td>
                </tr>
            </table>

            <table class="tabela" v-if="dadosProjeto.DtArquivamento">
                <tr class="destacar">
                    <th align="center" colspan="3" class="red-text"><b>Arquivado definitivamente</b></th>
                </tr>
                <tr class="destacar">
                    <th>Dt. Arquivamento</th>
                    <th>Nº inicial da caixa</th>
                    <th>Nº final da caixa</th>
                </tr>
                <tr>
                    <td align="center">{{dadosProjeto.DtArquivamento}}</td>
                    <td align="center">{{dadosProjeto.CaixaInicio}}</td>
                    <td align="center">{{dadosProjeto.CaixaFinal}}</td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <th align="center" colspan="4"><b>Solicitado</b></th>
                </tr>
                <tr class="destacar">
                    <th>Custeio(A)</th>
                    <th>Capital(B)</th>
                    <th>Solicitado(C=A+B)</th>
                </tr>
                <tr>
                    <td class="right-align">
                        <SalicFormatarValor :valor="dadosProjeto.SolicitadoCusteio"/>
                    </td>
                    <td class="right-align">
                        <SalicFormatarValor :valor="dadosProjeto.SolicitadoCapital"/>
                    </td>
                    <td class="right-align">
                        <SalicFormatarValor :valor="dadosProjeto.vlTotalSolicitado"/>
                    </td>
                </tr>
            </table>

            <table class="tabela">
                <tr class="destacar">
                    <th align="center" colspan="5"><b>Aprovado</b></th>
                </tr>
                <tr class="destacar">
                    <th><b>Custeio(D)</b></th>
                    <th><b>Capital(E)</b></th>
                    <th><b>Contrapartida(F)</b></th>
                    <th><b>Aprovado(G=D+E+F)</b></th>
                    <th><b>Conveniado(H=G+F)</b></th>
                </tr>
                <tr>
                    <td class="right-align">
                        <SalicFormatarValor :valor="dadosProjeto.ConcedidoCusteio"/>
                    </td>
                    <td class="right-align">
                        <SalicFormatarValor :valor="dadosProjeto.ConcedidoCapital"/>
                    </td>
                    <td class="right-align">
                        <SalicFormatarValor :valor="dadosProjeto.Contrapartida"/>
                    </td>
                    <td class="right-align" v-if="!dadosProjeto.ProponenteInabilitado">
                        <b>
                            <a :href="'/default/consultardadosprojeto/dados-convenio?idPronac=' + dadosProjeto.idPronac"
                               style="color: blue !important;">
                                <SalicFormatarValor :valor="dadosProjeto.vlTotalAprovado"/>
                            </a>
                        </b>
                    </td>
                    <td class="right-align" v-else><b>
                        <SalicFormatarValor :valor="dadosProjeto.vlTotalAprovado"/>
                    </b></td>
                    <td class="right-align">
                        <SalicFormatarValor :valor="dadosProjeto.ValorConvenio"/>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import { utils } from '@/mixins/utils';
    import Carregando from '@/components/Carregando';
    import SalicFormatarCpfCnpj from '@/components/SalicFormatarCpfCnpj';
    import SalicFormatarValor from '@/components/SalicFormatarValor';

    export default {
        mixins: [utils],
        components: {
            Carregando,
            SalicFormatarCpfCnpj,
            SalicFormatarValor,
        },
        computed: {
            ...mapGetters({
                dadosProjeto: 'projeto/projeto',
            }),
        },
    };
</script>
