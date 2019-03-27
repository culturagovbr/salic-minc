<?php

namespace Application\Modules\Fiscalizacao\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoDevolver;

class Devolver implements IAcaoDevolver
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbDocumentoAssinatura = $assinatura->modeloTbDocumentoAssinatura;

        $FiscalizacaoDAO = new \Fiscalizacao_Model_DbTable_TbFiscalizacao();
        $FiscalizacaoDAO->alteraSituacaoProjeto(
            \Fiscalizacao_Model_TbFiscalizacao::ST_FISCALIZACAO_OFICIALIZADA
            , $modeloTbDocumentoAssinatura->getIdAtoDeGestao()
        );
    }
}
