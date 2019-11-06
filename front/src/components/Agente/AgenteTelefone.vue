<template>
    <div class="conteudo">
        <legend>Telefone</legend>
        <table
            v-if="Object(telefones).length > 0"
            class="tabela">
            <tr class="destacar">
                <td><b>Tipo</b></td>
                <td class="centro"><b>UF</b></td>
                <td><b>N&uacute;mero</b></td>
                <td class="centro"><b>Divulgar</b></td>
            </tr>
            <tr
                v-for="telefone in telefones"
                :key="telefone.idTelefone">
                <td>{{ telefone.dstelefone }}</td>
                <td class="center-align">{{ telefone.ufsigla }}</td>
                <td class="left-align">{{ telefone.ddd }} {{ telefone.numero }}</td>
                <td class="center-align">{{ telefone.divulgar | formatarLabelSimOuNao }}</td>
            </tr>
        </table>
        <table
            v-else
            class="tabela">
            <tr>
                <td colspan="5"><em>N&atilde;o existe telefone cadastrado!</em></td>
            </tr>
        </table>
    </div>
</template>

<script>
import { utils } from '@/mixins/utils';

export default {
    mixins: [utils],
    props: {
        telefones: {
            type: [Object, Array],
            default: () => {},
        },
    },
};
</script>
