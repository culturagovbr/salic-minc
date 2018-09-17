<template>
    <div class="conteudo">
        <legend>E-mail</legend>
        <table class="tabela" v-if="Object(emails).length > 0">
            <tr class="destacar">
                <td width="25%"><b>Tipo</b></td>
                <td><b>E-mail</b></td>
            </tr>
            <tr v-for="email in emails" v-bind:key="email.idInternet">
                <td>{{email.tipo}}</td>
                <td>{{email.descricao}}</td>
            </tr>
        </table>
        <table class="tabela" v-else>
            <td colspan="2"><em>N&atilde;o existe email cadastrado!</em></td>
        </table>
    </div>
</template>

<script>

    export default{
        props: {
            emails: {},
        },
    };
</script>

