<template>
    <div class="conteudo">
        <legend>E-mail</legend>
        <table
            v-if="Object(emails).length > 0"
            class="tabela">
            <tr class="destacar">
                <td width="25%"><b>Tipo</b></td>
                <td><b>E-mail</b></td>
            </tr>
            <tr
                v-for="email in emails"
                :key="email.idInternet">
                <td>{{ email.tipo }}</td>
                <td>{{ email.descricao }}</td>
            </tr>
        </table>
        <table
            v-else
            class="tabela">
            <td colspan="2"><em>N&atilde;o existe email cadastrado!</em></td>
        </table>
    </div>
</template>

<script>

export default{
    props: {
        emails: {
            type: [Object, Array],
            default: () => {},
        },
    },
};
</script>
