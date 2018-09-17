<template>
    <div class="conteudo">
        <legend>Procurador</legend>
        <table class="tabela" v-if="Object(procuradores).length > 0">
            <tr class="destacar">
                <td width="20%" align="center"><b>CPF</b></td>
                <td align="center"><b>Nome</b></td>
            </tr>
            <tr v-for="procurador in procuradores" v-bind:key="procurador.idAgente">
                <td align="center">
                    <SalicFormatarCpfCnpj :cpf="procurador.CNPJCPF"/>
                </td>
                <td align="left">{{procurador.nome}}</td>
            </tr>
        </table>
        <table class="tabela" v-else>
            <tr>
                <td colspan="2" align="left"><em>N&atilde;o existe procurador cadastrado</em></td>
            </tr>
        </table>
    </div>
</template>

<script>
    import SalicFormatarCpfCnpj from '@/components/SalicFormatarCpfCnpj';

    export default {
        props: {
            procuradores: {},
        },
        components: {
            SalicFormatarCpfCnpj,
        },
    };
</script>
