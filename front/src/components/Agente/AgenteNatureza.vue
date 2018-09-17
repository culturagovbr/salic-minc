<template>
    <div class="conteudo">
        <legend>Natureza</legend>
        <table class="tabela" v-if=" natureza.Direito || natureza.Esfera ||
                                natureza.Administracao || natureza.Utilidade ">
            <tr class="destacar">
                <td><b>Natureza</b></td>
                <td><b>Esfera</b></td>
                <td><b>Administra&ccedil;&atilde;o</b></td>
                <td><b>Fins Lucrativos</b></td>
            </tr>
            <tr>
                <td v-if="natureza.Direito">{{natureza.Direito}}</td>
                <td v-else>Dado não informado!</td>

                <td v-if="natureza.Esfera">{{natureza.Esfera}}</td>
                <td v-else>Dado não informado!</td>

                <td v-if="natureza.Administracao">{{natureza.Administracao}}</td>
                <td v-else>Dado não informado!</td>

                <td v-if="natureza.Utilidade">{{natureza.Utilidade}}</td>
                <td v-else>Dado não informado!</td>
            </tr>
        </table>
        <table class="tabela" v-else>
            <tr>
                <td colspan="2"><em>Dados não informados!</em></td>
            </tr>
        </table>
    </div>
</template>

<script>

    export default {
        props: {
            natureza: {},
        },
    };
</script>
