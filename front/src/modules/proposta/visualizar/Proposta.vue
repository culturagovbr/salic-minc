<template>
    <div>
        <div
            v-if="dados"
            class="proposta">
            <div
                v-if="loading"
                class="row">
                <Carregando :text="'Carregando proposta'"/>
            </div>
            <ul
                v-show="!loading"
                class="collapsible"
                data-collapsible="expandable">
                <li>
                    <div class="collapsible-header">
                        <i class="material-icons">assignment</i>
                        <span v-if="dados.PRONAC">
                            Projeto - {{ dados.PRONAC }} - {{ dados.NomeProjeto }}
                        </span>
                        <span v-else>
                            Proposta - {{ idpreprojeto }} - {{ dados.NomeProjeto }}
                        </span>
                    </div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m12 l12 scroll">
                                <PropostaIdentificacao
                                    :idpreprojeto="idpreprojeto"
                                    :proposta="dados"/>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>
                        Hist&oacute;rico de avalia&ccedil;&otilde;es
                    </div>
                    <div class="collapsible-body padding20">
                        <PropostaHistoricoAvaliacoes
                            :idpreprojeto="dados.idPreProjeto"
                            :proposta="dados"
                        />
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>
                        Hist&oacute;rico de sugest&otilde;es de enquadramento
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <PropostaHistoricoSugestoesEnquadramento
                            :idpreprojeto="dados.idPreProjeto"
                        />
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>
                        Hist&oacute;rico de solicita&ccedil;&otilde;es
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <PropostaHistoricoSolicitacoes
                            :idpreprojeto="dados.idPreProjeto"
                        />
                    </div>
                </li>
                <li>
                    <div class="collapsible-header">
                        <i class="material-icons">person</i>
                        Proponente
                    </div>
                    <div class="collapsible-body padding20">
                        <AgenteProponente :idagente="dados.idAgente"/>
                        <AgenteUsuario :idusuario="dados.idUsuario"/>
                    </div>
                </li>

                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Ficha t&eacute;cnica
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.FichaTecnica"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Resumo
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.ResumoDoProjeto"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Objetivos
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.Objetivos"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Etapa de Trabalho
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.EtapaDeTrabalho"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Acessibilidade
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.Acessibilidade"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Especifica&ccedil;&otilde;es t&eacute;cnicas do produto
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.EspecificacaoTecnica"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Sinopse de Obra
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.Sinopse"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Democratiza&ccedil;&atilde;o de Acesso
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.DemocratizacaoDeAcesso"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Justificativa
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.Justificativa"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">subject</i>
                        Descri&ccedil;&atilde;o de Atividades
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <div class="card padding20">
                            <SalicTextoSimples :texto="dados.DescricaoAtividade"/>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">attachment</i>
                        Documentos anexados
                    </div>
                    <div class="collapsible-body padding20">
                        <PropostaDocumentos :proposta="dados"/>
                    </div>
                </li>
                <li>
                    <div
                        id="plano-distribuicao"
                        class="collapsible-header"><i class="material-icons">equalizer</i>Plano
                        Distribui&ccedil;&atilde;o
                    </div>
                    <div class="collapsible-body padding20">
                        <PropostaPlanoDistribuicao
                            :array-produtos="dados.planodistribuicaoproduto"
                            :array-detalhamentos="dados.tbdetalhaplanodistribuicao"
                        />
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>
                        Fonte de Recurso
                    </div>
                    <div class="collapsible-body padding20">
                        <PropostaFontesDeRecursos
                            :idpreprojeto="idpreprojeto"/>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">place</i>
                        Local de realiza&ccedil;&atilde;o/Deslocamento
                    </div>
                    <div class="collapsible-body padding20">
                        <PropostaLocalRealizacaoDeslocamento :proposta="dados"/>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">attach_money</i>
                        Custos Vinculados
                    </div>
                    <div
                        v-if="dados"
                        class="collapsible-body padding20">
                        <PropostaCustosVinculados
                            :array-custos="dados.tbcustosvinculados"
                        />
                    </div>
                </li>
                <li>
                    <div
                        id="planilha-orcamentaria"
                        class="collapsible-header"><i class="material-icons">attach_money</i>
                        Planilha or&ccedil;ament&aacute;ria
                    </div>
                    <div class="collapsible-body padding20">
                        <Planilha
                            :array-planilha="dados.tbplanilhaproposta"
                        />
                    </div>
                </li>
            </ul>
        </div>
        <div
            v-else
            class="center-align">
            <div class="padding20 card-panel">Opa! Proposta n&atilde;o encontrada...</div>
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
    props: {
        idpreprojeto: {
            type: String,
            default: '',
        },
        proposta: {
            type: Object,
            default: () => {},
        },
    },
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
    computed: {
        ...mapGetters({
            dadosProposta: 'proposta/proposta',
        }),
    },
    watch: {
        dadosProposta(value) {
            this.dados = value;
            this.loading = false;
        },
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
