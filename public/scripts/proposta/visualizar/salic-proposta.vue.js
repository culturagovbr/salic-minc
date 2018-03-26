Vue.component('salic-proposta', {
    template: `
        <div v-if="idpreprojeto || dados" class="proposta">
           
            <ul class="collapsible" data-collapsible="expandable">
                 <li>
                    <div class="collapsible-header">
                        <i class="material-icons">assignment</i>
                        <span v-if="proposta.PRONAC">Projeto - {{proposta.PRONAC}} - {{proposta.NomeProjeto}}</span>
                        <span v-else>Proposta - {{idpreprojeto}} - {{proposta.NomeProjeto}}</span>
                    </div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m12 l12 scroll">
                                 <salic-proposta-identificacao :idpreprojeto="idpreprojeto" :proposta="dados"></salic-proposta-identificacao>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>Hist&oacute;rico de avalia&ccedil;&otilde;es</div>
                    <div class="collapsible-body padding20">
                        <salic-proposta-historico-avaliacoes 
                            :idpreprojeto="dados.idPreProjeto"
                            :proposta="dados"
                        ></salic-proposta-historico-avaliacoes>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">person</i>Proponente</div>
                    <div class="collapsible-body padding20">
                        <salic-agente-proponente :idagente="dados.idAgente"></salic-agente-proponente>
                        <salic-agente-usuario :idusuario="dados.idUsuario"></salic-agente-usuario>
                    </div>
                </li>
                
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Ficha t&eacute;cnica</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.FichaTecnica"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Resumo</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.ResumoDoProjeto"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Objetivos</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.Objetivos"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Etapa de Trabalho</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.EtapaDeTrabalho"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Acessibilidade</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.Acessibilidade"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Especifica&ccedil;&otilde;es t&eacute;cnicas
                        do produto
                    </div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.EspecificacaoTecnica"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Sinopse de Obra</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.Sinopse"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Democratiza&ccedil;&atilde;o de Acesso</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.DemocratizacaoDeAcesso"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Justificativa</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.Justificativa"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Descri&ccedil;&atilde;o de Atividades</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <salic-texto-simples :texto="dados.DescricaoAtividade"></salic-texto-simples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">attachment</i>Documentos anexados</div>
                    <div class="collapsible-body padding20">
                        <salic-proposta-documentos :proposta="dados"></salic-proposta-documentos>
                    </div>
                </li>
                <li>
                    <div id="plano-distribuicao" class="collapsible-header"><i class="material-icons">equalizer</i>Plano
                        Distribui&ccedil;&atilde;o
                    </div>
                    <div class="collapsible-body padding20">
                        <salic-proposta-plano-distribuicao
                                    :arrayProdutos="dados.planodistribuicaoproduto"
                                    :arrayDetalhamentos="dados.tbdetalhaplanodistribuicao"
                        ></salic-proposta-plano-distribuicao>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>Fonte de Recurso</div>
                    <div class="collapsible-body padding20">
                        <salic-proposta-fontes-de-recursos :idpreprojeto="idpreprojeto"></salic-proposta-fontes-de-recursos>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">place</i>Local de realiza&ccedil;&atilde;o/Deslocamento
                    </div>
                    <div class="collapsible-body padding20">
                        <salic-proposta-local-realizacao-deslocamento
                            :idpreprojeto="idpreprojeto"></salic-proposta-local-realizacao-deslocamento>
                    </div>
                </li>
                <li>
                    <div id="planilha-orcamentaria" class="collapsible-header"><i class="material-icons">attach_money</i>Planilha
                        or&ccedil;ament&aacute;ria
                    </div>
                    <div class="collapsible-body padding20">
                        <salic-proposta-planilha-orcamentaria 
                            :arrayPlanilha="dados.tbplanilhaproposta"
                        ></salic-proposta-planilha-orcamentaria>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">attach_money</i>Custos Vinculados</div>
                    <div class="collapsible-body padding20" v-if="dados">
                         <salic-proposta-custos-vinculados
                                    :arrayCustos="dados.tbcustosvinculados"
                         ></salic-proposta-custos-vinculados>
                    </div>
                </li>
            </ul>
        </div>
        <div v-else class="center-align">
            <div class="padding10 green white-text">Opa! Proposta n&atilde;o informada...</div>
        </div>
    `,
    data: function () {
        return {
            dados: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        }
    },
    props: ['idpreprojeto', 'proposta'],
    mounted: function () {
        if(typeof this.idpreprojeto != 'undefined' && typeof this.proposta == 'undefined') {
            this.buscar_dados();
        }

        if(typeof this.proposta != 'undefined') {
            this.dados = this.proposta;
        }

        this.iniciarCollapsible();
    },
    methods: {
        buscar_dados: function () {
            let vue = this;
            $3.ajax({
                url: '/proposta/visualizar/obter-identificacao/idPreProjeto/' + vue.idpreprojeto
            }).done(function (response) {
                vue.dados = response.data;
            });
        },
        iniciarCollapsible: function () {
            $3('.collapsible').each(function () {
                $3(this).collapsible();
            });
        }
    }
});
