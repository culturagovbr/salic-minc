<?php

namespace Application\Modules\Admissibilidade\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoDevolver;

class Devolver implements IAcaoDevolver
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $projeto = $objTbProjetos->findBy(['IdPRONAC' => $modeloTbAssinatura->getIdPronac()]);

        $objOrgaos = new \Orgaos();
        $orgaoSuperior = $objOrgaos->obterOrgaoSuperior($projeto['Orgao']);

        $orgaoDestino = \Orgaos::ORGAO_SAV_DAP;
        if ($orgaoSuperior['Codigo'] == \Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $orgaoDestino = \Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI;
        }

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $objTbProjetos->alterarOrgao($orgaoDestino, $modeloTbAssinatura->getIdPronac());
        $objProjetos = new \Projetos();
        $objProjetos->alterarSituacao(
            $modeloTbAssinatura->getIdPronac(),
            null,
            \Projeto_Model_Situacao::PROJETO_DEVOLVIDO_PARA_ENQUADRAMENTO,
            'Projeto encaminhado para nova avalia&ccedil;&atilde;o do enquadramento'
        );

    }
}