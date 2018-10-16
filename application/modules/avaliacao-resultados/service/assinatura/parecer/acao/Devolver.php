<?php

namespace Application\Modules\AvaliacaoResultados\Service\Assinatura\Parecer\Acao;

use Application\Modules\AvaliacaoResultados\Service\Fluxo\Estado as EstadoService;

use MinC\Assinatura\Acao\IAcaoDevolver;

class Devolver implements IAcaoDevolver
{

    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $idPronac = $assinatura->modeloTbAssinatura->getIdPronac();

        $estadoService = new EstadoService();
        $estadoService->alterarEstado(
            [
                'idPronac' => $idPronac,
                'proximo' => 5
            ]
        );

    }
}