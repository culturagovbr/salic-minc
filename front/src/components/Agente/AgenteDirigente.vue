<template>
    <div class="conteudo">
        <legend>Dirigente</legend>
        <table
            v-if="dirigentes"
            class="tabela">
            <tr class="destacar">
                <td
                    width="20%"
                    align="center"><b>Nome/CPF</b></td>
                <td align="center"><b>Mandatos</b></td>
            </tr>
            <tr
                v-for="dirigente in dirigentes"
                :key="dirigente.idAgente">
                <td align="center">
                    <b>{{ dirigente.NomeDirigente }}</b> <br>
                    <SalicFormatarCpfCnpj :cpf="dirigente.CNPJCPFDirigente"/>
                </td>
                <td align="left">
                    <table
                        v-if="Object(dirigente.mandatos).length > 0"
                        class="tabela">
                        <tr
                            v-for="mandato in dirigente.mandatos"
                            :key="mandato.idAgentexVerificacao">
                            <td width="25%">
                                <b>Tipo de documento</b><br>
                                {{ mandato.Descricao }}
                            </td>
                            <td width="25%">
                                <b>Nº do documento</b><br>
                                {{ mandato.dsNumeroDocumento }}
                            </td>
                            <td width="25%">
                                <b>Per&iacute;odo de vig&ecirc;ncia</b><br>
                                {{ mandato.dtInicioMandato | formatarData }}&nbsp;
                                à &nbsp;{{ mandato.dtFimMandato | formatarData }}
                            </td>
                            <td width="25%">
                                <b>Arquivo</b><br>
                                <a :href="mandato.idArquivo">{{ mandato.nmArquivo }}</a>
                            </td>
                        </tr>
                    </table>
                    <table
                        v-else
                        class="tabela">
                        <tr colspan="2">
                            <td><em>N&atilde;o existem mandatos cadastrados!</em></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table
            v-else
            class="tabela">
            <tr>
                <td
                    colspan="2"
                    align="left">
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
    props: {
        dirigentes: {
            type: [Object, Array],
            default: () => {},
        },
    },
};
</script>
