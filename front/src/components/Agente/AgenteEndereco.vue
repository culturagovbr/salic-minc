<template>
    <div class="conteudo">
        <legend>Endereço</legend>
        <table class="tabela" v-if="enderecos">
            <tr class="destacar">
                <td><b>Logradouro</b></td>
                <td><b>Cidade</b></td>
                <td class="center-align"><b>UF</b></td>
                <td class="center-align"><b>CEP</b></td>
            </tr>
            <tr v-for="endereco in enderecos" :key="endereco.idEndereco">
                <td>
                    {{endereco.tipoendereco}} -
                    {{endereco.dstipologradouro}} -
                    {{endereco.Logradouro}} -
                    {{endereco.Numero}} -
                    {{endereco.Bairro}}
                    <span v-if="endereco.Complemento && endereco.Complemento !== ' '">
                        - {{endereco.Complemento}}
                    </span>
                </td>
                <td>{{endereco.municipio}}</td>
                <td class="center-align">{{endereco.uf}}</td>
                <td class="center-align">
                    <SalicFormatarCep :cep="endereco.Cep"/>
                </td>
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
    import SalicFormatarCep from '@/components/SalicFormatarCep';

    export default {
        props: {
            enderecos: {},
        },
        components: {
            SalicFormatarCep,
        },
    };
</script>
