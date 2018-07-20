<?php

namespace Application\Modules\Readequacao\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoFinalizar;

class Finalizar implements IAcaoFinalizar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $tbReadequacaoXParecerDbTable = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
        $tbReadequacaoXParecer = $tbReadequacaoXParecerDbTable->findBy([
            'idParecer' => $assinatura->modeloTbDocumentoAssinatura->getIdAtoDeGestao()
        ]);

        require_once APPLICATION_PATH . "/modules/readequacao/controllers/ReadequacoesController.php";

        $objReadequacao_ReadequacoesController = new \Readequacao_ReadequacoesController(
            $assinatura->request,
            $assinatura->response
        );
        $objReadequacao_ReadequacoesController->encaminharOuFinalizarReadequacaoChecklist(
            $tbReadequacaoXParecer['idReadequacao']
        );
    }
}