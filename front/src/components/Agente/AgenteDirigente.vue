<template>
    <div class="conteudo">
        <legend>Dirigente</legend>
        <table class="tabela" v-if="dirigentes">
            <tr class="destacar">
                <td width="20%" align="center"><b>Nome/CPF</b></td>
                <td align="center"><b>Mandatos</b></td>
            </tr>
            <tr v-for="dirigente in dirigentes" v-bind:key="dirigente.idAgente">
                <td align="center">
                    <b>{{dirigente.NomeDirigente}}</b> <br>
                    <SalicFormatarCpfCnpj :cpf="dirigente.CNPJCPFDirigente"/>
                </td>
                <td align="left">
                    <table class='tabela' v-if="Object(dirigente.mandatos).length > 0">
                        <tr v-for="mandato in dirigente.mandatos" v-bind:key="mandato.idAgentexVerificacao">
                            <td width='25%'>
                                <b>Tipo de documento</b><br>
                                {{mandato.Descricao}}
                            </td>
                            <td width='25%'>
                                <b>Nº do documento</b><br>
                                {{mandato.dsNumeroDocumento}}
                            </td>
                            <td width='25%'>
                                <b>Per&iacute;odo de vig&ecirc;ncia</b><br>
                                {{mandato.dtInicioMandato | formatarData}}&nbsp;
                                à &nbsp;{{mandato.dtFimMandato | formatarData}}
                            </td>
                            <td width='25%'>
                                <b>Arquivo</b><br>
                                <a v-bind:href="mandato.idArquivo">{{mandato.nmArquivo}}</a>
                            </td>
                        </tr>
                    </table>
                    <table class='tabela' v-else>
                        <tr colspan='2'>
                            <td><em>N&atilde;o existem mandatos cadastrados!</em></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="tabela" v-else>
            <tr>
                <td colspan="2" align="left">
                    <em>N&atilde;o existe dirigente cadastrado!</em>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
    import moment from 'moment';
    import SalicFormatarCpfCnpj from '@/components/SalicFormatarCpfCnpj';

    export default {
        props: {
            dirigentes: {},
        },
        components: {
            SalicFormatarCpfCnpj,
        },
        filters: {
            formatarData(date) {
                if (date.length === 0) {
                    return '---';
                }
                return moment(date).format('DD/MM/YYYY');
            },
        },
    };
</script>
