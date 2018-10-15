<?php

namespace Application\Modules\Assinatura\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IListaAcoesGerais;
use \Assinatura_Model_DbTable_TbAssinatura as TbAssinaturaDbTable,
    \Application\Modules\Readequacao\Service\Assinatura\Acao\ListaAcoesModulo as ListaAcoesReadequacao,
    \Application\Modules\PrestacaoContas\Service\Assinatura\Laudo\Acao\ListaAcoesModulo as ListaAcoesLaudoPrestacaoContas,
    \Application\Modules\Projeto\Service\Assinatura\Acao\ListaAcoesModulo as ListaAcoesHomologacaoProjeto,
    \Application\Modules\Parecer\Service\Assinatura\AnaliseCNIC\Acao\ListaAcoesModulo as ListaAcoesParecerAnaliseCNIC,
    \Application\Modules\Parecer\Service\Assinatura\AnaliseInicial\Acao\ListaAcoesModulo as ListaAcoesParecerAnaliseInicial,
    \Application\Modules\Admissibilidade\Service\Assinatura\Acao\ListaAcoesModulo as ListaAcoesEnquadramento;

class ListaAcoesGerais implements IListaAcoesGerais
{

    public function obterLista(): array
    {
        return [
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_PLANILHA_ORCAMENTARIA => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_ALTERACAO_RAZAO_SOCIAL => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_AGENCIA_BANCARIA => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_SINOPSE_OBRA => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_IMPACTO_AMBIENTAL => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_ESPECIFICACAO_TECNICA => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_ESTRATEGIA_EXECUCAO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_LOCAL_REALIZACAO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_ALTERACAO_PROPONENTE => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_PLANO_DISTRIBUICAO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_NOME_PROJETO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_PERIODO_EXECUCAO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_PLANO_DIVULGACAO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_RESUMO_PROJETO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_OBJETIVOS => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_JUSTIFICATIVA => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_ACESSIBILIDADE => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_DEMOCRATIZACAO_ACESSO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_ETAPAS_TRABALHO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_FICHA_TECNICA => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_SALDO_APLICACAO => new ListaAcoesReadequacao(),
            TbAssinaturaDbTable::TIPO_ATO_READEQUACAO_TRANSFERENCIA_RECURSOS => new ListaAcoesReadequacao(),
            
            TbAssinaturaDbTable::TIPO_ATO_ENQUADRAMENTO => new ListaAcoesEnquadramento(),
            TbAssinaturaDbTable::TIPO_ATO_LAUDO_PRESTACAO_CONTAS => new ListaAcoesLaudoPrestacaoContas(),
            TbAssinaturaDbTable::TIPO_ATO_HOMOLOGAR_PROJETO => new ListaAcoesHomologacaoProjeto(),

            TbAssinaturaDbTable::TIPO_ATO_ANALISE_CNIC => new ListaAcoesParecerAnaliseCNIC(),
            TbAssinaturaDbTable::TIPO_ATO_ANALISE_INICIAL => new ListaAcoesParecerAnaliseInicial(),
        ];
    }
}
