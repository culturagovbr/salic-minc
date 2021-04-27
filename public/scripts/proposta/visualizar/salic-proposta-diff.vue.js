Vue.component('salic-proposta-diff', {
    template: `
        <div class="proposta">
            <div v-if="Object.keys(dadosHistorico).length > 2">
                <salic-proposta-alteracoes :idpreprojeto="idpreprojeto" :dadosAtuais="dadosAtuais" :dadosHistorico="dadosHistorico"></salic-proposta-alteracoes>
            </div>
            <div v-else-if="Object.keys(dadosAtuais).length > 2">
                <div class="card padding20">
                    <div class="nao-existe-versao-proposta">
                        <h4><i class="material-icons small left">report</i>Não existe versionamento de alterações para o projeto informado...</h4>
                        <p style="margin-left: 44px">O proponente não fez alterações no projeto no prazo estabelecido.</p>
                    </div>
                </div>
                <salic-proposta :idpreprojeto="idpreprojeto" :proposta="dadosAtuais"></salic-proposta>
            </div>
            <div v-else>
                <div class="card padding20">
                    <div class="center-align padding20">
                        <h5>Carregando ...</h5>
                    </div>
                </div>
            </div>
        </div>
    `,
    data: function () {
        return {
            dadosAtuais: {
                type: Object,
                default: function () {
                    return {}
                }
            },
            dadosHistorico: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        }
    },
    props: ['idpreprojeto', 'tipo'],
    mounted: function () {
        if (typeof this.idpreprojeto != 'undefined') {
            this.buscar_dados();
        }
    },
    methods: {
        buscar_dados: function () {
            let vue = this;
            $3.ajax({
                url: '/proposta/visualizar/obter-proposta-cultural-versionamento/idPreProjeto/' + vue.idpreprojeto + '/tipo/' + vue.tipo
            }).done(function (response) {
                vue.dadosAtuais = response.data.atual;
                vue.dadosHistorico = response.data.historico;
            });
        }
    }
});

Vue.component('salic-proposta-alteracoes', {
    template: `
        <div class="alteracoes-proposta">
            <ul class="collapsible" data-collapsible="expandable">
                 <li>
                    <div class="collapsible-header"
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.identificacaoproposta, dadosHistorico.identificacaoproposta)}">
                        <i class="material-icons">assignment</i>
                        <span v-if="dadosAtuais.PRONAC">Projeto - {{dadosAtuais.PRONAC}} - {{dadosAtuais.NomeProjeto}}</span>
                        <span v-else>Proposta - {{idpreprojeto}} - {{dadosAtuais.NomeProjeto}}</span>
                    </div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m6 l6 scroll historico">
                                <salic-proposta-identificacao :idpreprojeto="idpreprojeto" :proposta="dadosHistorico"></salic-proposta-identificacao>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <salic-proposta-identificacao :idpreprojeto="idpreprojeto" :proposta="dadosAtuais"></salic-proposta-identificacao>
                            </div>
                        </div>
                    </div>
                </li>
                 <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>Hist&oacute;rico de avalia&ccedil;&otilde;es</div>
                    <div class="collapsible-body padding10">
                        <div class="card padding10">
                            <salic-proposta-historico-avaliacoes :idpreprojeto="idpreprojeto"></salic-proposta-historico-avaliacoes>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">person</i>Proponente</div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m12 12 scroll">
                                <salic-agente-proponente :idagente="dadosAtuais.idAgente"></salic-agente-proponente>
                                <salic-agente-usuario :idusuario="dadosAtuais.idUsuario"></salic-agente-usuario>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.FichaTecnica, dadosHistorico.FichaTecnica)}" i>
                        <i class="material-icons">subject</i>Ficha t&eacute;cnica
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                        <div class="card">
                            <table>
                                <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.FichaTecnica"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <salic-texto-simples :texto="dadosAtuais.FichaTecnica"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.ResumoDoProjeto, dadosHistorico.ResumoDoProjeto)}">
                        <i class="material-icons">subject</i>Resumo
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                        <div class="card">
                            <table>
                                <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.ResumoDoProjeto"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20">
                                        <salic-texto-simples :texto="dadosAtuais.ResumoDoProjeto"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.Objetivos, dadosHistorico.Objetivos)}">
                        <i class="material-icons">subject</i>Objetivos
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                        <div class="card">
                            <table>
                                <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.Objetivos"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <salic-texto-simples :texto="dadosAtuais.Objetivos"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.EtapaDeTrabalho, dadosHistorico.EtapaDeTrabalho)}">
                        <i class="material-icons">subject</i>Etapa de Trabalho
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                        <div class="card">
                            <table>
                                <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.EtapaDeTrabalho"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <salic-texto-simples :texto="dadosAtuais.EtapaDeTrabalho"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.Acessibilidade, dadosHistorico.Acessibilidade)}">
                        <i class="material-icons">subject</i>Acessibilidade
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                        <div class="card">
                            <table>
                                <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.Acessibilidade"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <salic-texto-simples :texto="dadosAtuais.Acessibilidade"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.EspecificacaoTecnica, dadosHistorico.EspecificacaoTecnica)}">
                        <i class="material-icons">subject</i>Especifica&ccedil;&otilde;es t&eacute;cnicas do produto
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                        <div class="card">
                            <table>
                                <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.EspecificacaoTecnica"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <salic-texto-simples :texto="dadosAtuais.EspecificacaoTecnica"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.Sinopse, dadosHistorico.Sinopse)}">
                        <i class="material-icons">subject</i>Sinopse de Obra
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                        <div class="card">
                            <table>
                                 <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.Sinopse"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <salic-texto-simples :texto="dadosAtuais.Sinopse"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.DemocratizacaoDeAcesso, dadosHistorico.DemocratizacaoDeAcesso)}">
                        <i class="material-icons">subject</i>Democratiza&ccedil;&atilde;o de Acesso
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                         <div class="card">
                            <table>
                                <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.DemocratizacaoDeAcesso"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <salic-texto-simples :texto="dadosAtuais.DemocratizacaoDeAcesso"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.Justificativa, dadosHistorico.Justificativa)}">
                        <i class="material-icons">subject</i>Justificativa
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                        <div class="card">
                            <table>
                                <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.Justificativa"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <salic-texto-simples :texto="dadosAtuais.Justificativa"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                 <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.DescricaoAtividade, dadosHistorico.DescricaoAtividade)}">
                        <i class="material-icons">subject</i>Descri&ccedil;&atilde;o de Atividades
                    </div>
                    <div class="collapsible-body padding20" v-if="dadosAtuais">
                        <div class="card">
                            <table>
                                <tr>
                                    <td class="original historico padding20">
                                        <salic-texto-simples :texto="dadosHistorico.DescricaoAtividade"></salic-texto-simples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <salic-texto-simples :texto="dadosAtuais.DescricaoAtividade"></salic-texto-simples>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.abrangencia, dadosHistorico.abrangencia)}">
                        <i class="material-icons">place</i>Local de realiza&ccedil;&atilde;o/Deslocamento
                    </div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m6 l6 scroll historico">
                                <salic-proposta-local-realizacao-deslocamento
                                :localizacoes="dadosHistorico"></salic-proposta-local-realizacao-deslocamento>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <salic-proposta-local-realizacao-deslocamento
                                :localizacoes="dadosAtuais"></salic-proposta-local-realizacao-deslocamento>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header" 
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.documentos_proposta, dadosHistorico.documentos_proposta)}">
                        <i class="material-icons">attachment</i>Documentos anexados
                    </div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m6 l6 scroll historico">
                                <salic-proposta-documentos :arrayDocumentos="dadosHistorico"></salic-proposta-documentos>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <salic-proposta-documentos :arrayDocumentos="dadosAtuais"></salic-proposta-documentos>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div id="plano-distribuicao" class="collapsible-header"
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.tbdetalhaplanodistribuicao, dadosHistorico.tbdetalhaplanodistribuicao)}">
                        <i class="material-icons">equalizer</i>Plano Distribui&ccedil;&atilde;o
                    </div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m6 l6 scroll historico">
                                <salic-proposta-plano-distribuicao
                                    :arrayProdutos="dadosHistorico.planodistribuicaoproduto"
                                    :arrayDetalhamentos="dadosHistorico.tbdetalhaplanodistribuicao"
                                ></salic-proposta-plano-distribuicao>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <salic-proposta-plano-distribuicao
                                    :arrayProdutos="dadosAtuais.planodistribuicaoproduto"
                                    :arrayDetalhamentos="dadosAtuais.tbdetalhaplanodistribuicao"
                                ></salic-proposta-plano-distribuicao>
                            </div>
                        </div>
                    </div>
                </li>
               <li>
                    <div id="planilha-orcamentaria" class="collapsible-header"
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.tbplanilhaproposta, dadosHistorico.tbplanilhaproposta)}">
                        <i class="material-icons">attach_money</i>Planilha
                        or&ccedil;ament&aacute;ria
                    </div>
                    <div class="collapsible-body padding20 active">
                        <div class="row">
                            <div class="col s12 m6 l6 scroll historico">
                                <salic-proposta-planilha-orcamentaria
                                :arrayPlanilha="dadosHistorico.tbplanilhaproposta"></salic-proposta-planilha-orcamentaria>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <salic-proposta-planilha-orcamentaria
                                :arrayPlanilha="dadosAtuais.tbplanilhaproposta"></salic-proposta-planilha-orcamentaria>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div id="custos-vinculados" class="collapsible-header"
                        v-bind:class="{'orange lighten-4': existe_diferenca(dadosAtuais.tbcustosvinculados, dadosHistorico.tbcustosvinculados)}">
                        <i class="material-icons">equalizer</i>Custos Vinculados
                    </div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m6 l6 scroll historico">
                                <salic-proposta-custos-vinculados
                                    :arrayCustos="dadosHistorico.tbcustosvinculados"
                                ></salic-proposta-custos-vinculados>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <salic-proposta-custos-vinculados
                                    :arrayCustos="dadosAtuais.tbcustosvinculados"
                                ></salic-proposta-custos-vinculados>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    `,
    props: ['idpreprojeto', 'dadosAtuais', 'dadosHistorico'],
    mounted: function () {
        this.iniciarCollapsible();
        if (this.dadosHistorico != 'undefined') {
            setTimeout(this.mostrar_diferenca, 1000)
        }
    },
    methods: {
        existe_diferenca: function (atual, historico) {

            if (typeof atual == 'object') {
                return JSON.stringify(atual) != JSON.stringify(historico);
            }

            return atual != historico;
        },
        mostrar_diferenca: function () {
            $(".alteracoes-proposta table tr").prettyTextDiff({
                cleanup: true,
                diffContainer: ".diff",
                debug: false
            });
        }, iniciarCollapsible: function () {
            $3('.collapsible').each(function () {
                $3(this).collapsible();
            });
        }
    }
});

