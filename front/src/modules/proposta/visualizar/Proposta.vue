<template>
    <div>
        <div v-if="dados" class="proposta">
            <div v-if="loading" class="row">
                <Carregando :text="'Carregando proposta'"></Carregando>
            </div>
            <ul v-show="!loading" class="collapsible" data-collapsible="expandable">
                 <li>
                    <div class="collapsible-header">
                        <i class="material-icons">assignment</i>
                        <span v-if="dados.PRONAC">Projeto - {{dados.PRONAC}} - {{dados.NomeProjeto}}</span>
                        <span v-else>Proposta - {{idpreprojeto}} - {{dados.NomeProjeto}}</span>
                    </div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m12 l12 scroll">
                                 <PropostaIdentificacao
                                         :idpreprojeto="idpreprojeto"
                                         :proposta="dados">
                                 </PropostaIdentificacao>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>Hist&oacute;rico de avalia&ccedil;&otilde;es</div>
                    <div class="collapsible-body padding20">
                        <PropostaHistoricoAvaliacoes
                                :idpreprojeto="dados.idPreProjeto"
                                :proposta="dados"
                        ></PropostaHistoricoAvaliacoes>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>Hist&oacute;rico de sugest&otilde;es de enquadramento</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <PropostaHistoricoSugestoesEnquadramento
                            :idpreprojeto="dados.idPreProjeto"
                        ></PropostaHistoricoSugestoesEnquadramento>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>Hist&oacute;rico de solicita&ccedil;&otilde;es</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <PropostaHistoricoSolicitacoes
                            :idpreprojeto="dados.idPreProjeto"
                        ></PropostaHistoricoSolicitacoes>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">person</i>Proponente</div>
                    <div class="collapsible-body padding20">
                        <AgenteProponente :idagente="dados.idAgente"></AgenteProponente>
                        <AgenteUsuario :idusuario="dados.idUsuario"></AgenteUsuario>
                    </div>
                </li>

                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Ficha t&eacute;cnica</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.FichaTecnica"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Resumo</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.ResumoDoProjeto"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Objetivos</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.Objetivos"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Etapa de Trabalho</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.EtapaDeTrabalho"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Acessibilidade</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.Acessibilidade"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Especifica&ccedil;&otilde;es t&eacute;cnicas
                        do produto
                    </div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.EspecificacaoTecnica"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Sinopse de Obra</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.Sinopse"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Democratiza&ccedil;&atilde;o de Acesso</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.DemocratizacaoDeAcesso"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Justificativa</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.Justificativa"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>Descri&ccedil;&atilde;o de Atividades</div>
                    <div class="collapsible-body padding20" v-if="dados">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.DescricaoAtividade"></SalicTextoSimples>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">attachment</i>Documentos anexados</div>
                    <div class="collapsible-body padding20">
                        <PropostaDocumentos :proposta="dados"></PropostaDocumentos>
                    </div>
                </li>
                <li>
                    <div id="plano-distribuicao" class="collapsible-header"><i class="material-icons">equalizer</i>Plano
                        Distribui&ccedil;&atilde;o
                    </div>
                    <div class="collapsible-body padding20">
                        <PropostaPlanoDistribuicao
                                :arrayProdutos="dados.planodistribuicaoproduto"
                                :arrayDetalhamentos="dados.tbdetalhaplanodistribuicao"
                        ></PropostaPlanoDistribuicao>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>Fonte de Recurso</div>
                    <div class="collapsible-body padding20">
                        <PropostaFontesDeRecursos
                                :idpreprojeto="idpreprojeto">
                        </PropostaFontesDeRecursos>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">place</i>Local de realiza&ccedil;&atilde;o/Deslocamento
                    </div>
                    <div class="collapsible-body padding20">
                        <PropostaLocalRealizacaoDeslocamento
                                :idpreprojeto="idpreprojeto"></PropostaLocalRealizacaoDeslocamento>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">attach_money</i>Custos Vinculados</div>
                    <div class="collapsible-body padding20" v-if="dados">
                         <PropostaCustosVinculados
                                 :arrayCustos="dados.tbcustosvinculados"
                         ></PropostaCustosVinculados>
                    </div>
                </li>
                <li>
                    <div id="planilha-orcamentaria" class="collapsible-header"><i class="material-icons">attach_money</i>Planilha
                        or&ccedil;ament&aacute;ria
                    </div>
                    <div class="collapsible-body padding20">
                        <Planilha
                            :arrayPlanilha="dados.tbplanilhaproposta"
                        ></Planilha>
                    </div>
                </li>
            </ul>
        </div>
        <div v-else class="center-align">
            <div class="padding10 green white-text">Opa! Proposta n&atilde;o informada...</div>
        </div>
    </div>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import Planilha from '@/components/Planilha/Planilha';
import Carregando from '@/components/Carregando';
import SalicTextoSimples from '@/components/SalicTextoSimples';
import PropostaIdentificacao from './components/PropostaIdentificacao';
import PropostaHistoricoAvaliacoes from './components/PropostaHistoricoAvaliacoes';
import PropostaHistoricoSugestoesEnquadramento from './components/PropostaHistoricoSugestoesEnquadramento';
import PropostaHistoricoSolicitacoes from './components/PropostaHistoricoSolicitacoes';
import AgenteProponente from '../components/AgenteProponente';
import AgenteUsuario from '../components/AgenteUsuario';
import PropostaDocumentos from './components/PropostaDocumentos';
import PropostaPlanoDistribuicao from './components/PropostaPlanoDistribuicao';
import PropostaFontesDeRecursos from './components/PropostaFontesDeRecursos';
import PropostaLocalRealizacaoDeslocamento from './components/PropostaLocalRealizacaoDeslocamento';
import PropostaCustosVinculados from './components/PropostaCustosVinculados';

export default {
    name: 'Proposta',
    data() {
        return {
            dados: {
                type: Object,
                default() {
                    return {};
                },
            },
            loading: true,
        };
    },
    props: ['idpreprojeto', 'proposta'],
    components: {
        PropostaIdentificacao,
        PropostaHistoricoAvaliacoes,
        PropostaHistoricoSugestoesEnquadramento,
        PropostaHistoricoSolicitacoes,
        AgenteProponente,
        AgenteUsuario,
        SalicTextoSimples,
        Carregando,
        PropostaDocumentos,
        PropostaPlanoDistribuicao,
        PropostaFontesDeRecursos,
        PropostaLocalRealizacaoDeslocamento,
        PropostaCustosVinculados,
        Planilha,
    },
    mounted() {
        if (typeof this.idpreprojeto !== 'undefined' && typeof this.proposta === 'undefined') {
            this.buscarDadosProposta(this.idpreprojeto);
            this.dados = this.dadosProposta;
        }

        if (typeof this.proposta !== 'undefined') {
            this.dados = this.proposta;
            this.loading = false;
        }

        this.iniciarCollapsible();
    },
    watch: {
        dadosProposta(value) {
            this.dados = value;
            this.loading = false;
        },
    },
    computed: {
        ...mapGetters({
            dadosProposta: 'proposta/proposta',
        }),
    },
    methods: {
        ...mapActions({
            buscarDadosProposta: 'proposta/buscarDadosProposta',
        }),
        iniciarCollapsible() {
            // eslint-disable-next-line
            $3('.collapsible').each(function () {
                // eslint-disable-next-line
                $3(this).collapsible();
            });
        },
    },
};
</script>
