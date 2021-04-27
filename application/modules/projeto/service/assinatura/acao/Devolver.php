<?php

namespace Application\Modules\Projeto\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoDevolver;

class Devolver implements IAcaoDevolver
{

    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;
        $objProjetos = new \Projetos();
        $objProjetos->alterarSituacao(
            $modeloTbAssinatura->getIdPronac(),
            null,
            \Projeto_Model_Situacao::PROJETO_APRECIADO_PELA_CNIC,
            'Projeto encaminhado para a homologa&ccedil;&atilde;o de execu&ccedil;&atilde;o'
        );
        return true;
    }
}
