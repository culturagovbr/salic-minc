<template>
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
                                <PropostaIdentificacao
                                        :idpreprojeto="idpreprojeto"
                                        :proposta="dadosHistorico">
                                </PropostaIdentificacao>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <PropostaIdentificacao
                                        :idpreprojeto="idpreprojeto"
                                        :proposta="dadosAtuais">
                                </PropostaIdentificacao>
                            </div>
                        </div>
                    </div>
                </li>
                 <li>
                    <div class="collapsible-header"><i class="material-icons">history</i>Hist&oacute;rico de avalia&ccedil;&otilde;es</div>
                    <div class="collapsible-body padding10">
                        <div class="card padding10">
                            <PropostaHistoricoAvaliacoes
                                    :idpreprojeto="idpreprojeto">
                            </PropostaHistoricoAvaliacoes>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header"><i class="material-icons">person</i>Proponente</div>
                    <div class="collapsible-body padding20">
                        <div class="row">
                            <div class="col s12 m12 12 scroll">
                                <AgenteProponente
                                        :idagente="dadosAtuais.idAgente">
                                </AgenteProponente>
                                <AgenteUsuario
                                        :idusuario="dadosAtuais.idUsuario">
                                </AgenteUsuario>
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
                                        <SalicTextoSimples
                                                :texto="dadosHistorico.FichaTecnica">
                                        </SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <SalicTextoSimples
                                                :texto="dadosAtuais.FichaTecnica">
                                        </SalicTextoSimples>
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
                                        <SalicTextoSimples
                                                :texto="dadosHistorico.ResumoDoProjeto">
                                        </SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20">
                                        <SalicTextoSimples
                                                :texto="dadosAtuais.ResumoDoProjeto">
                                        </SalicTextoSimples>
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
                                        <SalicTextoSimples
                                                :texto="dadosHistorico.Objetivos">
                                        </SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <SalicTextoSimples
                                                :texto="dadosAtuais.Objetivos">
                                        </SalicTextoSimples>
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
                                        <SalicTextoSimples
                                                :texto="dadosHistorico.EtapaDeTrabalho">
                                        </SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <SalicTextoSimples
                                                :texto="dadosAtuais.EtapaDeTrabalho">
                                        </SalicTextoSimples>
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
                                        <SalicTextoSimples
                                                :texto="dadosHistorico.Acessibilidade">
                                        </SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <SalicTextoSimples
                                                :texto="dadosAtuais.Acessibilidade">
                                        </SalicTextoSimples>
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
                                        <SalicTextoSimples :texto="dadosHistorico.EspecificacaoTecnica"></SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <SalicTextoSimples :texto="dadosAtuais.EspecificacaoTecnica"></SalicTextoSimples>
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
                                        <SalicTextoSimples :texto="dadosHistorico.Sinopse"></SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <SalicTextoSimples :texto="dadosAtuais.Sinopse"></SalicTextoSimples>
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
                                        <SalicTextoSimples :texto="dadosHistorico.DemocratizacaoDeAcesso"></SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <SalicTextoSimples :texto="dadosAtuais.DemocratizacaoDeAcesso"></SalicTextoSimples>
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
                                        <SalicTextoSimples :texto="dadosHistorico.Justificativa"></SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <SalicTextoSimples :texto="dadosAtuais.Justificativa"></SalicTextoSimples>
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
                                        <SalicTextoSimples :texto="dadosHistorico.DescricaoAtividade"></SalicTextoSimples>
                                    </td>
                                    <td class="changed atual padding20" >
                                        <SalicTextoSimples :texto="dadosAtuais.DescricaoAtividade"></SalicTextoSimples>
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
                                <PropostaLocalRealizacaoDeslocamento
                                        :proposta="dadosHistorico"></PropostaLocalRealizacaoDeslocamento>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <PropostaLocalRealizacaoDeslocamento
                                        :proposta="dadosAtuais"></PropostaLocalRealizacaoDeslocamento>
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
                                <PropostaDocumentos :proposta="dadosHistorico"></PropostaDocumentos>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <PropostaDocumentos :proposta="dadosAtuais"></PropostaDocumentos>
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
                                <PropostaPlanoDistribuicao
                                        :arrayProdutos="dadosHistorico.planodistribuicaoproduto"
                                        :arrayDetalhamentos="dadosHistorico.tbdetalhaplanodistribuicao"
                                ></PropostaPlanoDistribuicao>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <PropostaPlanoDistribuicao
                                        :arrayProdutos="dadosAtuais.planodistribuicaoproduto"
                                        :arrayDetalhamentos="dadosAtuais.tbdetalhaplanodistribuicao"
                                ></PropostaPlanoDistribuicao>
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
                                <PropostaCustosVinculados
                                        :arrayCustos="dadosHistorico.tbcustosvinculados"
                                ></PropostaCustosVinculados>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <PropostaCustosVinculados
                                        :arrayCustos="dadosAtuais.tbcustosvinculados"
                                ></PropostaCustosVinculados>
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
                                <Planilha
                                        :arrayPlanilha="dadosHistorico.tbplanilhaproposta"></Planilha>
                            </div>
                            <div class="col s12 m6 l6 scroll atual">
                                <Planilha
                                        :arrayPlanilha="dadosAtuais.tbplanilhaproposta"></Planilha>
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
import AgenteProponente from '../../components/AgenteProponente';
import AgenteUsuario from '../../components/AgenteUsuario';
import PropostaLocalRealizacaoDeslocamento from './PropostaLocalRealizacaoDeslocamento';
import PropostaDocumentos from './PropostaDocumentos';
import PropostaPlanoDistribuicao from './PropostaPlanoDistribuicao';
import PropostaCustosVinculados from './PropostaCustosVinculados';

export default {
    name: 'PropostaAlteracoes',
    props: ['idpreprojeto', 'dadosAtuais', 'dadosHistorico'],
    components: {
        PropostaIdentificacao,
        PropostaHistoricoAvaliacoes,
        AgenteProponente,
        AgenteUsuario,
        SalicTextoSimples,
        PropostaLocalRealizacaoDeslocamento,
        PropostaDocumentos,
        PropostaPlanoDistribuicao,
        Planilha,
        PropostaCustosVinculados,
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
