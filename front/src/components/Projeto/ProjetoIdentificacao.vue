<template>
    <div class="proponente">
        <table class="tabela" v-if="projeto.ProponenteInabilitado" style="background-color: red;">
            <tr style="background-color: red;">
                <td align="center" style="text-transform: uppercase; color: red;">
                    <b>Proponente Inabilitado</b>
                </td>
            </tr>
        </table>

        <table class="tabela">
            <tr class="destacar">
                <td><b>PRONAC</b></td>
                <td><b>Nome do Projeto</b></td>
            </tr>
            <tr>
                <td>{{ projeto.Pronac }}</td>
                <td>{{ projeto.NomeProjeto }}</td>
            </tr>
            <tr class="destacar">
                <td><b>CNPJ/CPF</b></td>
                <td><b>Proponente</b></td>
            </tr>
            <tr>
                <td>
                        <span v-if="projeto.idUsuarioExterno"><SalicFormatarCpfCnpj
                            :cpf="projeto.CgcCPf"/></span>
                    <a v-else
                       :href="'/default/relatorio/resultado-projeto?cnpfcpf=' + projeto.CgcCPf">
                        <SalicFormatarCpfCnpj :cpf="projeto.CgcCPf"/>
                    </a>
                </td>
                <td>
                    <span v-html="projeto.Proponente"></span>
                </td>
            </tr>
        </table>
    </div>
</template>

<script type="text/javascript">

    import SalicFormatarCpfCnpj from '@/components/SalicFormatarCpfCnpj';

    export default {
        props: {
            projeto: {},
        },
        components: {
            SalicFormatarCpfCnpj,
        },
    };
</script>
