<?php

namespace Application\Modules\Parecer\Service\Assinatura\AnaliseCNIC\Acao;

use MinC\Assinatura\Acao\IAcaoFinalizar;

class Finalizar implements IAcaoFinalizar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;

        $objProjetos = new \Projetos();
        $objProjetos->alterarSituacao(
            $modeloTbAssinatura->getIdPronac(),
            null,
            \Projeto_Model_Situacao::PROJETO_APRECIADO_PELA_CNIC,
            'Projeto apreciado pelo Componente da Comiss&atilde;o na Reuni&atilde;o da CNIC'
        );
    }

}
