<?php

namespace Application\Modules\AvaliacaoResultados\Service\Assinatura\Laudo\Acao;

use Application\Modules\AvaliacaoResultados\Service\Fluxo\Estado as EstadoService;

use MinC\Assinatura\Acao\IAcaoAssinar;

class Assinar implements IAcaoAssinar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $numeroDeAssinaturas = $assinatura->dbTableTbAssinatura->obterQuantidadeAssinaturasRealizadas();
        $idPronac = $assinatura->modeloTbAssinatura->getIdPronac();


        if ( $numeroDeAssinaturas == 1) {
            $proximoEstado = 11;
        } elseif ($numeroDeAssinaturas == 2) {
            $proximoEstado = 16;
        } elseif ( $numeroDeAssinaturas == 3) {
            $proximoEstado = 12;
        }

        if (isset($proximoEstado)) {
            $estadoService = new EstadoService();
            $estadoService->alterarEstado(
                [
                    'idPronac' => $idPronac,
                    'proximo' => $proximoEstado
                ]
            );
        }
    }
}
