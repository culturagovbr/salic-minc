<template>
    <div class="alteracoes-proposta">
        <ul
            class="collapsible"
            data-collapsible="expandable">
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.identificacaoproposta,
                        dadosHistorico.identificacaoproposta
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">assignment</i>
                    <span v-if="dadosAtuais.PRONAC">
                        Projeto - {{ dadosAtuais.PRONAC }} - {{ dadosAtuais.NomeProjeto }}
                    </span>
                    <span v-else>
                        Proposta - {{ idpreprojeto }} - {{ dadosAtuais.NomeProjeto }}
                    </span>
                </div>
                <div class="collapsible-body padding20">
                    <div class="row">
                        <div class="col s12 m6 l6 scroll historico">
                            <PropostaIdentificacao
                                :idpreprojeto="idpreprojeto"
                                :proposta="dadosHistorico"/>
                        </div>
                        <div class="col s12 m6 l6 scroll atual">
                            <PropostaIdentificacao
                                :idpreprojeto="idpreprojeto"
                                :proposta="dadosAtuais"/>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="collapsible-header">
                    <i class="material-icons">history</i>
                    Hist&oacute;rico de avalia&ccedil;&otilde;es
                </div>
                <div class="collapsible-body padding10">
                    <div class="card padding10">
                        <PropostaHistoricoAvaliacoes
                            :idpreprojeto="idpreprojeto"/>
                    </div>
                </div>
            </li>
            <li>
                <div class="collapsible-header">
                    <i class="material-icons">history</i>
                    Hist&oacute;rico solicita&ccedil;&otilde;es
                </div>
                <div class="collapsible-body padding10">
                    <div class="card padding10">
                        <PropostaHistoricoSolicitacoes
                            :idpreprojeto="idpreprojeto"/>
                    </div>
                </div>
            </li>
            <li>
                <div class="collapsible-header">
                    <i class="material-icons">person</i>
                    Proponente
                </div>
                <div class="collapsible-body padding20">
                    <div class="row">
                        <div class="col s12 m12 12 scroll">
                            <AgenteProponente
                                :idagente="dadosAtuais.idAgente"/>
                            <AgenteUsuario
                                :idusuario="dadosAtuais.idUsuario"/>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.FichaTecnica,
                        dadosHistorico.FichaTecnica
                    )}"
                    class="collapsible-header"
                    i>
                    <i class="material-icons">subject</i>Ficha t&eacute;cnica
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples
                                        :texto="dadosHistorico.FichaTecnica"/>
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples
                                        :texto="dadosAtuais.FichaTecnica"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.ResumoDoProjeto,
                        dadosHistorico.ResumoDoProjeto
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>Resumo
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples
                                        :texto="dadosHistorico.ResumoDoProjeto"/>
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples
                                        :texto="dadosAtuais.ResumoDoProjeto"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.Objetivos,
                        dadosHistorico.Objetivos
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>Objetivos
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples
                                        :texto="dadosHistorico.Objetivos"/>
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples
                                        :texto="dadosAtuais.Objetivos"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.EtapaDeTrabalho,
                        dadosHistorico.EtapaDeTrabalho
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>Etapa de Trabalho
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples
                                        :texto="dadosHistorico.EtapaDeTrabalho"/>
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples
                                        :texto="dadosAtuais.EtapaDeTrabalho"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.Acessibilidade,
                        dadosHistorico.Acessibilidade
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>Acessibilidade
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples
                                        :texto="dadosHistorico.Acessibilidade"/>
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples
                                        :texto="dadosAtuais.Acessibilidade"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.EspecificacaoTecnica,
                        dadosHistorico.EspecificacaoTecnica
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>
                    Especifica&ccedil;&otilde;es t&eacute;cnicas do produto
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples
                                        :texto="dadosHistorico.EspecificacaoTecnica"
                                    />
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples :texto="dadosAtuais.EspecificacaoTecnica"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.Sinopse,
                        dadosHistorico.Sinopse
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>Sinopse de Obra
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples :texto="dadosHistorico.Sinopse"/>
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples :texto="dadosAtuais.Sinopse"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.DemocratizacaoDeAcesso,
                        dadosHistorico.DemocratizacaoDeAcesso
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>Democratiza&ccedil;&atilde;o de Acesso
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples
                                        :texto="dadosHistorico.DemocratizacaoDeAcesso"
                                    />
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples :texto="dadosAtuais.DemocratizacaoDeAcesso"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.Justificativa,
                        dadosHistorico.Justificativa
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>Justificativa
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples :texto="dadosHistorico.Justificativa"/>
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples :texto="dadosAtuais.Justificativa"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.DescricaoAtividade,
                        dadosHistorico.DescricaoAtividade
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>Descri&ccedil;&atilde;o de Atividades
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples :texto="dadosHistorico.DescricaoAtividade"/>
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples :texto="dadosAtuais.DescricaoAtividade"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.EstrategiadeExecucao,
                        dadosHistorico.EstrategiadeExecucao
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">subject</i>Outras informações
                </div>
                <div
                    v-if="dadosAtuais"
                    class="collapsible-body padding20">
                    <div class="card">
                        <table>
                            <tr>
                                <td class="original historico padding20">
                                    <SalicTextoSimples :texto="dadosHistorico.EstrategiadeExecucao"/>
                                </td>
                                <td class="changed atual padding20">
                                    <SalicTextoSimples :texto="dadosAtuais.EstrategiadeExecucao"/>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.abrangencia,
                        dadosHistorico.abrangencia
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">place</i>
                    Local de realiza&ccedil;&atilde;o/Deslocamento
                </div>
                <div class="collapsible-body padding20">
                    <div class="row">
                        <div class="col s12 m6 l6 scroll historico">
                            <PropostaLocalRealizacaoDeslocamento
                                :proposta="dadosHistorico"/>
                        </div>
                        <div class="col s12 m6 l6 scroll atual">
                            <PropostaLocalRealizacaoDeslocamento
                                :proposta="dadosAtuais"/>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.documentos_proposta,
                        dadosHistorico.documentos_proposta
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">attachment</i>Documentos anexados
                </div>
                <div class="collapsible-body padding20">
                    <div class="row">
                        <div class="col s12 m6 l6 scroll historico">
                            <PropostaDocumentos :proposta="dadosHistorico"/>
                        </div>
                        <div class="col s12 m6 l6 scroll atual">
                            <PropostaDocumentos :proposta="dadosAtuais"/>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div
                    id="plano-distribuicao"
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.tbdetalhaplanodistribuicao,
                        dadosHistorico.tbdetalhaplanodistribuicao
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">equalizer</i>Plano Distribui&ccedil;&atilde;o
                </div>
                <div class="collapsible-body padding20">
                    <div class="row">
                        <div class="col s12 m6 l6 scroll historico">
                            <PropostaPlanoDistribuicao
                                :array-produtos="dadosHistorico.planodistribuicaoproduto"
                                :array-detalhamentos="dadosHistorico.tbdetalhaplanodistribuicao"
                            />
                        </div>
                        <div class="col s12 m6 l6 scroll atual">
                            <PropostaPlanoDistribuicao
                                :array-produtos="dadosAtuais.planodistribuicaoproduto"
                                :array-detalhamentos="dadosAtuais.tbdetalhaplanodistribuicao"
                            />
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div
                    id="custos-vinculados"
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.tbcustosvinculados,
                        dadosHistorico.tbcustosvinculados
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">equalizer</i>Custos Vinculados
                </div>
                <div class="collapsible-body padding20">
                    <div class="row">
                        <div class="col s12 m6 l6 scroll historico">
                            <PropostaCustosVinculados
                                :proposta="dadosHistorico"
                            />
                        </div>
                        <div class="col s12 m6 l6 scroll atual">
                            <PropostaCustosVinculados
                                :proposta="dadosAtuais"
                            />
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div
                    id="planilha-orcamentaria"
                    :class="{'orange lighten-4': existe_diferenca(
                        dadosAtuais.tbplanilhaproposta,
                        dadosHistorico.tbplanilhaproposta
                    )}"
                    class="collapsible-header">
                    <i class="material-icons">attach_money</i>Planilha
                    or&ccedil;ament&aacute;ria
                </div>
                <div class="collapsible-body padding20 active">
                    <div class="row">
                        <div class="col s12 m6 l6 scroll historico">
                            <Planilha
                                :array-planilha="dadosHistorico.tbplanilhaproposta"/>
                        </div>
                        <div class="col s12 m6 l6 scroll atual">
                            <Planilha
                                :array-planilha="dadosAtuais.tbplanilhaproposta"/>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
<script>
import SalicTextoSimples from '@/components/SalicTextoSimples';
import Planilha from '@/components/Planilha/Planilha';
import PropostaIdentificacao from './PropostaIdentificacao';
import PropostaHistoricoAvaliacoes from './PropostaHistoricoAvaliacoes';
import PropostaHistoricoSolicitacoes from './PropostaHistoricoSolicitacoes';
import AgenteProponente from '../../components/AgenteProponente';
import AgenteUsuario from '../../components/AgenteUsuario';
import PropostaLocalRealizacaoDeslocamento from './PropostaLocalRealizacaoDeslocamento';
import PropostaDocumentos from './PropostaDocumentos';
import PropostaPlanoDistribuicao from './PropostaPlanoDistribuicao';
import PropostaCustosVinculados from './PropostaCustosVinculados';

export default {
    name: 'PropostaAlteracoes',
    components: {
        PropostaIdentificacao,
        PropostaHistoricoAvaliacoes,
        PropostaHistoricoSolicitacoes,
        AgenteProponente,
        AgenteUsuario,
        SalicTextoSimples,
        PropostaLocalRealizacaoDeslocamento,
        PropostaDocumentos,
        PropostaPlanoDistribuicao,
        Planilha,
        PropostaCustosVinculados,
    },
    props: {
        idpreprojeto: {
            type: String,
            default: '',
        },
        dadosAtuais: {
            type: Object,
            default: () => {},
        },
        dadosHistorico: {
            type: Object,
            default: () => {},
        },
    },
    mounted() {
        this.iniciarCollapsible();
        if (this.dadosHistorico !== 'undefined') {
            setTimeout(this.mostrar_diferenca, 1000);
        }
    },
    methods: {
        existe_diferenca(atual, historico) {
            if (typeof atual === 'object') {
                return JSON.stringify(atual) !== JSON.stringify(historico);
            }

            return atual !== historico;
        },
        mostrar_diferenca() {
            /* eslint-disable */
                $(".alteracoes-proposta table tr").prettyTextDiff({
                    cleanup: true,
                    diffContainer: ".diff",
                    debug: false
                });
            }, iniciarCollapsible() {
                // eslint-disable-next-line
                $3('.collapsible').each(function () {
                    // eslint-disable-next-line
                    $3(this).collapsible();
                });
            },
        }
    };
</script>
