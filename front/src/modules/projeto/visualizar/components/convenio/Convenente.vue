<template>
    <div id="conteudo">
        <div v-if="loading" class="row">
            <Carregando :text="'Carregando proponente'"></Carregando>
        </div>
        <div v-else-if="dadosProponente.dados">
            <div class="row" v-if="dadosProjeto.ProponenteInabilitado">
                <div style="background-color: #EF5350; text-transform: uppercase" class="darken-2 padding10 white-text center-align">
                    <div><b>Proponente Inabilitado</b></div>
                </div>
            </div>
    
            <table class="tabela">
                <tr class="destacar">
                    <td><b>Pronac</b></td>
                    <td><b>Nome do Projeto</b></td>
                </tr>
                <tr>
                    <td>{{dadosProjeto.Pronac}}</td>
                    <td>{{dadosProjeto.NomeProjeto}}</td>
                </tr>
                <tr class="destacar">
                    <td><b>CNPJ / CPF</b></td>
                    <td><b>Convenente</b></td>
                </tr>
                <tr>
                    <td v-if="dadosProponente.dados.CNPJCPF">
                        <a v-if="!dadosProponente.dados.isProponente" :href="'/default/relatorio/resultado-projeto?cnpfcpf=' + dadosProponente.dados.CNPJCPF">
                            <SalicFormatarCpfCnpj :cpf="dadosProponente.dados.CNPJCPF" />
                        </a>
                        <SalicFormatarCpfCnpj v-else :cpf="dadosProponente.dados.CNPJCPF" />
                    </td>
                    <td v-else>Dado não informado!</td>
                    <td>{{dadosProponente.dados.Proponente}}</td>
                </tr>
            </table>
            <fieldset>
                <legend>Endereço</legend>
                <table class="tabela">
                    <tr class="destacar">
                        <td><b>Logradouro</b></td>
                        <td><b>Cidade</b></td>
                        <td class="center-align"><b>UF</b></td>
                        <td class="center-align"><b>CEP</b></td>
                    </tr>
                    <tr>
                        <td>{{dadosProponente.dados.Endereco}}</td>
                        <td>{{dadosProponente.dados.Municipio}}</td>
                        <td class="center-align">{{dadosProponente.dados.Uf}}</td>
                        <td class="center-align">
                            <SalicFormatarCep :cep="dadosProponente.dados.Cep" />
                        </td>
                    </tr>
                </table>
            </fieldset>
    
            <fieldset>
                <legend>Telefone</legend>
                <table v-if="dadosProponente.dados.TelefoneComercial 
                    || dadosProponente.dados.TelefoneCelular 
                    || dadosProponente.dados.TelefoneResidencial" class="tabela">
                    <tr v-if="dadosProponente.dados.TelefoneComercial">
                        <td width="20%"><b>Comercial</b></td>
                        <td>{{dadosProponente.dados.TelefoneComercial}}</td>
                    </tr>
                    <tr v-if="dadosProponente.dados.TelefoneCelular">
                        <td width="20%"><b>Celular</b></td>
                        <td>{{dadosProponente.dados.TelefoneCelular}}</td>
                    </tr>
                    <tr v-if="dadosProponente.dados.TelefoneResidencial">
                        <td width="20%"><b>Residencial</b></td>
                        <td>{{dadosProponente.dados.TelefoneResidencial}}</td>
                    </tr>
                </table>
                <table class='tabela' v-else>
                    <tr>
                        <td><em>N&atilde;o existe telefone cadastrado!</em></td>
                    </tr>
                </table>
            </fieldset>
    
            <fieldset>
                <legend>E-mail</legend>
                <table class="tabela" v-if="dadosProponente.dados.Email">
                    <tr class="destacar">
                        <td><b>E-mail</b></td>
                    </tr>
                    <tr>
                        <td>{{dadosProponente.dados.Email}}</td>
                    </tr>
                </table>
                <table class="tabela" v-else>
                    <td colspan="2"><em>N&atilde;o existe email cadastrado!</em></td>
                </table>
            </fieldset>
    
            <fieldset>
                <legend>Natureza</legend>
                <table class="tabela" v-if=" dadosProponente.dados.Natureza || dadosProponente.dados.Esfera || 
                                            dadosProponente.dados.Administracao || dadosProponente.dados.Utilidade ">
                    <tr class="destacar">
                        <td><b>Natureza</b></td>
                        <td><b>Esfera</b></td>
                        <td><b>Administra&ccedil;&atilde;o</b></td>
                        <td><b>Fins Lucrativos</b></td>
                    </tr>
                    <tr>
                        <td v-if="dadosProponente.dados.Natureza">{{dadosProponente.dados.Natureza}}</td>
                        <td v-else>Dado não informado!</td>
    
                        <td v-if="dadosProponente.dados.Esfera">{{dadosProponente.dados.Esfera}}</td>
                        <td v-else>Dado não informado!</td>
    
                        <td v-if="dadosProponente.dados.Administracao">{{dadosProponente.dados.Administracao}}</td>
                        <td v-else>Dado não informado!</td>
    
                        <td v-if="dadosProponente.dados.Utilidade">{{dadosProponente.dados.Utilidade}}</td>
                        <td v-else>Dado não informado!</td>
                    </tr>
                </table>
                <table class="tabela" v-else>
                    <tr>
                        <td colspan="2"><em>Dados não informados!</em></td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex';
    import Carregando from '@/components/Carregando';
    import SalicFormatarCep from '@/components/SalicFormatarCep';
    import SalicFormatarCpfCnpj from '@/components/SalicFormatarCpfCnpj';
    
    export default {
        data() {
            return {
                loading: true,
            };
        },
        components: {
            SalicFormatarCpfCnpj,
            SalicFormatarCep,
            Carregando,
        },
        created() {
            this.buscaProponente(this.dadosProjeto.idPronac);
    
            if (Object.keys(this.dadosProponente).length > 0) {
                this.loading = false;
            }
        },
        watch: {
            dadosProponente() {
                if (Object.keys(this.dadosProponente).length > 0) {
                    this.loading = false;
                }
            },
        },
        methods: {
            ...mapActions({
                buscaProponente: 'projeto/buscaProponente',
            }),
        },
        computed: {
            ...mapGetters({
                dadosProponente: 'projeto/proponente',
                dadosProjeto: 'projeto/projeto',
            }),
        },
    };
</script>