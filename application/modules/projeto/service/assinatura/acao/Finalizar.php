<?php

namespace Application\Modules\Projeto\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoFinalizar;

class Finalizar implements IAcaoFinalizar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;

        $dbTableHomologacao = new \Projeto_Model_DbTable_TbHomologacao();
        $parecerHomologacao = $dbTableHomologacao->getBy([
            'idPronac' => $modeloTbAssinatura->getIdPronac(),
            'tpHomologacao' => '1'
        ]);

        $objProjetos = new \Projetos();

        if ($parecerHomologacao['stDecisao'] == \Projeto_Model_TbHomologacao::ST_DECISAO_INDEFERIDO) {
            $objProjetos->alterarSituacao(
                $modeloTbAssinatura->getIdPronac(),
                null,
                \Projeto_Model_Situacao::PROJETO_INDEFERIDO,
                'Projeto indeferido'
            );
            return true;
        }

        $dbTableEnquadramento = new \Projeto_Model_DbTable_Enquadramento();
        $enquadramentoProjeto = $dbTableEnquadramento->obterProjetoAreaSegmento(
            ['a.IdPRONAC = ?' => $modeloTbAssinatura->getIdPronac()]
        )->current();

        $orgaoDestinoSAV = (int)\Orgaos::ORGAO_SAV_DAP;
        $situacao = [
            'codigo' => \Projeto_Model_Situacao::ENCAMINHADO_PARA_INCLUSAO_EM_PORTARIA_REDUCAO,
            'providencia' => "Projeto encaminhado para elabora&ccedil;&atilde;o de portaria de redu&ccedil;&atilde;o or&ccedil;ament&aacute;ria"
        ];

        if ($enquadramentoProjeto['VlHomologadoIncentivo'] == $enquadramentoProjeto['VlAutorizadoACaptarIncentivo']) {
            $orgaoDestinoSAV = (int)\Orgaos::ORGAO_SAV_CAP;
            $situacao = [
                'codigo' => \Projeto_Model_Situacao::AUTORIZADA_CAPTACAO_RESIDUAL_DOS_RECURSOS,
                'providencia' => "Homologado a execu&ccedil;&atilde;o do projeto cultural"
            ];

        } else if ($enquadramentoProjeto['VlHomologadoIncentivo'] > $enquadramentoProjeto['VlAutorizadoACaptarIncentivo']) {
            $situacao = [
                'codigo' => \Projeto_Model_Situacao::ENCAMINHADO_PARA_INCLUSAO_EM_PORTARIA_COMPLEMENTACAO,
                'providencia' => "Projeto encaminhado para elabora&ccedil;&atilde;o de portaria de complementa&ccedil;&atilde;o or&ccedil;ament&aacute;ria"
            ];
        }

        $objProjetos->alterarSituacao(
            $modeloTbAssinatura->getIdPronac(),
            null,
            $situacao['codigo'],
            $situacao['providencia']
        );

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $dadosProjeto = $objTbProjetos->findBy(array(
            'IdPRONAC' => $modeloTbAssinatura->getIdPronac()
        ));

        $objOrgaos = new \Orgaos();
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($dadosProjeto['Orgao']);

        if ((int)$dadosOrgaoSuperior['Codigo'] == (int)\Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $orgaoDestino = (int)\Orgaos::ORGAO_GEAR_SACAV;
        } elseif ((int)$dadosOrgaoSuperior['Codigo'] == (int)\Orgaos::ORGAO_SUPERIOR_SAV) {
            $orgaoDestino = $orgaoDestinoSAV;
        }

        if (isset($orgaoDestino)) {
            $objTbProjetos->alterarOrgao($orgaoDestino, $modeloTbAssinatura->getIdPronac());
        }

        return true;
    }
}
