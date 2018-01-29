Vue.component('salic-proposta', {
    template: `
        <div v-if="idpreprojeto" class="proposta">
            <salic-proposta-identificacao :idpreprojeto="idpreprojeto" :proposta="dados"></salic-proposta-identificacao>
            <ul class="collapsible" data-collapsible="accordion">
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>Hist&oacute;rico</div>
                    <div class="collapsible-body padding20">
                        <salic-proposta-historico-avaliacoes :idpreprojeto="dados.idPreProjeto"
                                                             v-bind:proposta="dados"></salic-proposta-historico-avaliacoes>
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
                        <div id="html-plano-distribuicao"></div>
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
                        <div id="planilhaOrcamentariaMontada"></div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Ficha t&eacute;cnica</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <salic-texto-simples :texto="dados.FichaTecnica"></salic-texto-simples>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Resumo</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <salic-texto-simples :texto="dados.ResumoDoProjeto"></salic-texto-simples>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Objetivos</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <salic-texto-simples :texto="dados.Objetivos"></salic-texto-simples>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Etapa de Trabalho</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <salic-texto-simples :texto="dados.EtapaDeTrabalho"></salic-texto-simples>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Acessibilidade</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <salic-texto-simples :texto="dados.Acessibilidade"></salic-texto-simples>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Especifica&ccedil;&otilde;es t&eacute;cnicas
                        do produto
                    </div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <salic-texto-simples :texto="dados.Sinopse"></salic-texto-simples>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Sinopse de Obra</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <salic-texto-simples :texto="dados.EspecificacaoTecnica"></salic-texto-simples>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Impacto Ambiental</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <salic-texto-simples :texto="dados.ImpactoAmbiental"></salic-texto-simples>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Justificativa</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <salic-texto-simples :texto="dados.Justificativa"></salic-texto-simples>
                    </div>
                </li>
            </ul>
        </div>
        <div v-else class="center-align">
            <div class="padding10 green white-text">Opa! Proposta não informada...</div>
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
    props: ['idpreprojeto'],
    mounted: function () {
        this.buscar_dados();
    },
    methods: {
        buscar_dados: function () {
            let vue = this;
            $3.ajax({
                url: '/proposta/visualizar/obter-identificacao/idPreProjeto/' + vue.idpreprojeto
            }).done(function (response) {
                vue.dados = response.data;
            });
        }
    }
});