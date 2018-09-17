<template>
    <div class="conteudo">
        <legend>Telefone</legend>
        <table class="tabela" v-if="Object(telefones).length > 0">
            <tr class="destacar">
                <td><b>Tipo</b></td>
                <td class="centro"><b>UF</b></td>
                <td><b>N&uacute;mero</b></td>
                <td class="centro"><b>Divulgar</b></td>
            </tr>
            <tr v-for="telefone in telefones" v-bind:key="telefone.idTelefone">
                <td>{{telefone.dstelefone}}</td>
                <td class="center-align">{{telefone.ufsigla}}</td>
                <td class="left-align">{{telefone.ddd}} {{telefone.numero}}</td>
                <td class="center-align">{{label_sim_ou_nao(telefone.divulgar)}}</td>
            </tr>
        </table>
        <table class='tabela' v-else>
            <tr>
                <td colspan="5"><em>N&atilde;o existe telefone cadastrado!</em></td>
            </tr>
        </table>
    </div>
</template>

<script>
    import { utils } from '@/mixins/utils';

    export default {
        props: {
            telefones: {},
        },
        mixins: [utils],
    };
</script>
