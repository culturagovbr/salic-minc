import Vue from 'vue';
import Router from 'vue-router';

import Visualizar from './visualizar/Visualizar';
import PlanilhaPropostaOriginal from './visualizar/components/incentivo/planilha/PlanilhaPropostaOriginal';
import PlanilhaPropostaAutorizada from './visualizar/components/incentivo/planilha/PlanilhaPropostaAutorizada';
import PlanilhaPropostaAdequada from './visualizar/components/incentivo/planilha/PlanilhaPropostaAdequada';
import PlanilhaHomologada from './visualizar/components/incentivo/planilha/PlanilhaHomologada';
import PlanilhaReadequada from './visualizar/components/incentivo/planilha/PlanilhaReadequada';
import RelacaoDePagamentos from './visualizar/components/incentivo/RelacaoDePagamentos';
import Convenente from './visualizar/components/convenio/Convenente';
import Proposta from './visualizar/components/incentivo/Proposta';
import CertidoesNegativas from './visualizar/components/outrasInformacoes/CertidoesNegativas';
import LocalRealizacaoDeslocamento from './visualizar/components/outrasInformacoes/LocalRealizacaoDeslocamento';
import DocumentosAssinados from './visualizar/components/outrasInformacoes/DocumentosAssinados';
import DadosComplementares from './visualizar/components/outrasInformacoes/DadosComplementaresView';
import DocumentosAnexados from './visualizar/components/outrasInformacoes/DocumentosAnexados';
import PlanoDistribuicaoIn2013 from './visualizar/components/outrasInformacoes/PlanoDistribuicaoIn2013';
import HistoricoEncaminhamento from './visualizar/components/outrasInformacoes/HistoricoEncaminhamento';
import PlanoDistribuicaoIn2017 from './visualizar/components/outrasInformacoes/PlanoDistribuicaoIn2017';
import ProvidenciaTomada from './visualizar/components/outrasInformacoes/ProvidenciaTomada';
import DiligenciaProjeto from './visualizar/components/outrasInformacoes/DiligenciaProjeto';
import Tramitacao from './visualizar/components/outrasInformacoes/Tramitacao';
import MarcasAnexadas from './visualizar/components/execucao/MarcasAnexadas';
import DadosReadequacoes from './visualizar/components/execucao/DadosReadequacoes';
import PedidoProrrogacao from './visualizar/components/execucao/PedidoProrrogacao';
import DadosFiscalizacao from './visualizar/components/execucao/DadosFiscalizacao';
import ContasBancarias from './visualizar/components/dadosBancarios/ContasBancarias';
import ConciliacaoBancariaView from './visualizar/components/dadosBancarios/ConciliacaoBancariaView';
import InconsistenciaBancaria from './visualizar/components/dadosBancarios/InconsistenciaBancaria';
import Liberacao from './visualizar/components/dadosBancarios/Liberacao';
import SaldoContas from './visualizar/components/dadosBancarios/SaldoContas';
import ExtratosBancariosView from './visualizar/components/dadosBancarios/ExtratosBancariosView';
import ExtratosBancariosConsolidado from './visualizar/components/dadosBancarios/ExtratosBancariosConsolidado';
import Captacao from './visualizar/components/dadosBancarios/Captacao';
import Devolucoes from './visualizar/components/dadosBancarios/Devolucoes';
import PagamentosConsolidados from './visualizar/components/prestacaoContas/PagamentosConsolidados';
import PagamentosUfMunicipio from './visualizar/components/prestacaoContas/PagamentosUfMunicipio';
import ExecucaoReceitaDespesaView from './visualizar/components/prestacaoContas/ExecucaoReceitaDespesaView';
import RelatorioFisico from './visualizar/components/prestacaoContas/RelatorioFisico';
import RelacaoPagamentoView from './visualizar/components/prestacaoContas/RelacaoPagamentoView';
import RelatorioCumprimentoObjeto from './visualizar/components/prestacaoContas/RelatorioCumprimentoObjeto';
import LaudoFinal from './visualizar/components/prestacaoContas/LaudoFinal';

import Aprovacao from './visualizar/components/analiseAprovacao/Aprovacao';
import Recurso from './visualizar/components/analiseAprovacao/Recurso';

// import retirados do webpackChunkName
import DadosProjeto from './visualizar/components/DadosProjeto';
import Proponente from './visualizar/components/incentivo/Proponente';

Vue.use(Router);

// const DadosProjeto = () => import(/* webpackChunkName: "dados-projeto" */ './visualizar/components/DadosProjeto');
// const Proponente = () => import(/* webpackChunkName: "proponente" */ './visualizar/components/incentivo/Proponente');

const templateAjax = {
    template: '<div id="conteudo"></div>',
};

const routes = [
    {
        path: '/:idPronac',
        component: Visualizar,
        children: [
            {
                path: '',
                name: 'dadosprojeto',
                component: DadosProjeto,
                meta: {
                    title: 'Dados do Projeto',
                },
            },
            {
                path: 'proponente',
                name: 'proponente',
                component: Proponente,
                meta: {
                    title: 'Proponente',
                },
            },
            {
                path: 'convenente',
                name: 'convenente',
                component: Convenente,
                meta: {
                    title: 'Convenente',
                },
            },
            {
                path: 'planilha-proposta',
                name: 'planilhaproposta',
                component: PlanilhaPropostaOriginal,
                meta: {
                    title: 'Planilha de Solicita&ccedil;&atilde;o da Proposta Original',
                },
            },
            {
                path: 'planilha-autorizada',
                name: 'planilhaautorizada',
                component: PlanilhaPropostaAutorizada,
                meta: {
                    title: 'Planilha Autorizada para Captar',
                },
            },
            {
                path: 'planilha-adequada',
                name: 'planilhaadequada',
                component: PlanilhaPropostaAdequada,
                meta: {
                    title: 'Planilha Adequada &agrave; realidade de execu&ccedil;&atilde;o pelo proponente',
                },
            },
            {
                path: 'planilha-homologada',
                name: 'planilhahomologada',
                component: PlanilhaHomologada,
                meta: {
                    title: 'Planilha Homologada para execu&ccedil;&atilde;o',
                },
            },
            {
                path: 'planilha-aprovada',
                name: 'planilhaaprovada',
                component: PlanilhaHomologada,
                meta: {
                    title: 'Planilha Autorizada para Captar',
                },
            },
            {
                path: 'planilha-readequada',
                name: 'planilhareadequada',
                component: PlanilhaReadequada,
                meta: {
                    title: 'Planilha Readequada na execu&ccedil;&atilde;o',
                },
            },
            {
                path: 'relacao-de-pagamentos',
                name: 'relacaodepagamentos',
                component: RelacaoDePagamentos,
                meta: {
                    title: 'Rela&ccedil;&atilde;o de Pagamentos',
                },
            },
            {
                path: 'proposta',
                name: 'proposta',
                component: Proposta,
                meta: {
                    title: 'Proposta',
                },
            },
            {
                path: 'conteudo-dinamico',
                name: 'containerAjax',
                component: templateAjax,
            },
            {
                path: 'certidoes-negativas',
                name: 'CertidoesNegativas',
                component: CertidoesNegativas,
                meta: {
                    title: 'Certid&otilde;es Negativas',
                },
            },
            {
                path: 'local-realizacao-deslocamento',
                name: 'LocalRealizacaoDeslocamento',
                component: LocalRealizacaoDeslocamento,
                meta: {
                    title: 'Local de Realiza&ccedil;&atilde;o/Deslocamento',
                },
            },
            {
                path: 'documentos-assinados',
                name: 'DocumentosAssinados',
                component: DocumentosAssinados,
                meta: {
                    title: 'Documentos assinados',
                },
            },
            {

                path: 'dados-complementares',
                name: 'DadosComplementares',
                component: DadosComplementares,
                meta: {
                    title: 'Dados Complementares do Projeto',
                },
            },
            {
                path: 'documentos-anexados',
                name: 'DocumentosAnexados',
                component: DocumentosAnexados,
                meta: {
                    title: 'Documentos Anexados',

                },
            },
            {
                path: 'plano-distribuicao-in-2013',
                name: 'PlanoDistribuicao',
                component: PlanoDistribuicaoIn2013,
                meta: {
                    title: 'Plano de Distribui&ccedil;&atilde;o',
                },
            },
            {
                path: 'historico-encaminhamento',
                name: 'HistoricoEncaminhamento',
                component: HistoricoEncaminhamento,
                meta: {
                    title: 'Hist&oacute;rico Encaminhamento',
                },
            },
            {
                path: 'plano-distribuicao',
                name: 'PropostaPlanoDistribuicao',
                component: PlanoDistribuicaoIn2017,
                meta: {
                    title: 'Plano de Distribui&ccedil;&atilde;o',
                },
            },
            {
                path: 'providencia-tomada',
                name: 'ProvidenciaTomada',
                component: ProvidenciaTomada,
                meta: {
                    title: 'Provid&ecirc;ncia Tomada',
                },
            },
            {
                path: 'diligencias',
                name: 'DiligenciaProjeto',
                component: DiligenciaProjeto,
                meta: {
                    title: 'Dilig&ecirc;ncias do Projeto',
                },
            },
            {
                path: 'tramitacao',
                name: 'Tramitacao',
                component: Tramitacao,
                meta: {
                    title: 'Tramita&ccedil;&atilde;o',
                },
            },
            {
                path: 'marcas-anexadas',
                name: 'MarcasAnexadas',
                component: MarcasAnexadas,
                meta: {
                    title: 'Marcas Anexadas',
                },
            },
            {
                path: 'readequacoes',
                name: 'DadosReadequacoes',
                component: DadosReadequacoes,
                meta: {
                    title: 'Dados das Readequações',
                },
            },
            {
                path: 'pedido-prorrogacao',
                name: 'PedidoProrrogacao',
                component: PedidoProrrogacao,
                meta: {
                    title: 'Pedido de Prorrogação',
                },
            },
            {
                path: 'dados-fiscalizacao',
                name: 'DadosFiscalizacao',
                component: DadosFiscalizacao,
                meta: {
                    title: 'Dados Fiscalização',
                },
            },
            {
                path: 'contas-bancarias',
                name: 'ContasBancarias',
                component: ContasBancarias,
                meta: {
                    title: 'Contas Bancárias',
                },
            },
            {
                path: 'conciliacao-bancaria',
                name: 'ConciliacaoBancaria',
                component: ConciliacaoBancariaView,
                meta: {
                    title: 'Conciliação Bancária',
                },
            },
            {
                path: 'inconsistencia-bancaria',
                name: 'InconsistenciaBancaria',
                component: InconsistenciaBancaria,
                meta: {
                    title: 'Inconsistência Bancária',
                },
            },
            {
                path: 'liberacao',
                name: 'Liberecao',
                component: Liberacao,
                meta: {
                    title: 'Liberação',
                },
            },
            {
                path: 'saldo-contas',
                name: 'SaldoContas',
                component: SaldoContas,
                meta: {
                    title: 'Saldo das Contas',
                },
            },
            {
                path: 'extratos-bancarios',
                name: 'ExtratosBancarios',
                component: ExtratosBancariosView,
                meta: {
                    title: 'Extratos Bancários',
                },
            },
            {
                path: 'extratos-bancarios-consolidado',
                name: 'ExtratosBancariosConsolidado',
                component: ExtratosBancariosConsolidado,
                meta: {
                    title: 'Extratos Bancários Consolidado',
                },
            },
            {
                path: 'captacao',
                name: 'Captacao',
                component: Captacao,
                meta: {
                    title: 'Captação',
                },
            },
            {
                path: 'devolucoes',
                name: 'Devolucoes',
                component: Devolucoes,
                meta: {
                    title: 'Devoluções',
                },
            },
            {
                path: 'aprovacao',
                name: 'Aprovacao',
                component: Aprovacao,
                meta: {
                    title: 'Aprovação',
                },
            },
            {
                path: 'recurso',
                name: 'Recurso',
                component: Recurso,
                meta: {
                    title: 'Recurso',
                },
            },
            {
                path: 'pagamentos-consolidados',
                name: 'PagamentosConsolidados',
                component: PagamentosConsolidados,
                meta: {
                    title: 'Pagamentos Consolidados',
                },
            },
            {
                path: 'pagamentos-uf-municipio',
                name: 'PagamentosUfMunicipio',
                component: PagamentosUfMunicipio,
                meta: {
                    title: 'Pagamentos Por UF / Município',
                },
            },
            {
                path: 'execucao-receita-despesa',
                name: 'ExecucaoReceitaDespesa',
                component: ExecucaoReceitaDespesaView,
                meta: {
                    title: 'Execução da Receita e Despesa',
                },
            },
            {
                path: 'relatorio-fisico',
                name: 'RelatorioFisico',
                component: RelatorioFisico,
                meta: {
                    title: 'Relatório Físico',
                },
            },
            {
                path: 'relacao-pagamento',
                name: 'RelacaoPagamento',
                component: RelacaoPagamentoView,
                meta: {
                    title: 'Relação de Pagamentos',
                },
            },
            {
                path: 'relatorio-cumprimento-objeto',
                name: 'RelatorioCumprimentoObjeto',
                component: RelatorioCumprimentoObjeto,
                meta: {
                    title: 'Relatório de cumprimento do Objeto',
                },
            },
            {
                path: 'laudo-final',
                name: 'LaudoFinal',
                component: LaudoFinal,
                meta: {
                    title: 'Laudo Final',
                },
            },
        ],
    },
];

export default new Router({ routes });
