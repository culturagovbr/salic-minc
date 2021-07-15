<?php

namespace Application\Modules\AvaliacaoResultados\Service\LaudoFinal;

class Laudo
{
    public function obterProjetos($estadoId)
    {
        $model = new \AvaliacaoResultados_Model_DbTable_LaudoFinal();

        return $model->projetosLaudoFinal($estadoId)->toArray();
    }

    public function obterLaudo($idPronac)
    {
        $model = new \AvaliacaoResultados_Model_DbTable_LaudoFinal();
        return $model->laudoFinal($idPronac);
    }

    /** @todo se a regra for apenas um laudo por idPronac o idLaudoFinal nao é necessario */
    public function salvarLaudo($idLaudoFinal, $idPronac, $siManifestacao, $dsLaudoFinal)
    {
        $auth = \Zend_Auth::getInstance();
        $avaliacaoResultadosDbTable = new \AvaliacaoResultados_Model_DbTable_LaudoFinal;

        $laudoFinal = [
            'idPronac' => $idPronac,
            'idUsuario' => $auth->getIdentity()->usu_codigo,
            'dtLaudoFinal' => (new \DateTime())->format('Y-m-d'),
            'siManifestacao' => $siManifestacao,
            'dsLaudoFinal' => utf8_decode($dsLaudoFinal)
        ];

        $laudoSalvo = $avaliacaoResultadosDbTable->findBy(['idPronac = ?' => $idPronac]);

        $idLaudoFinal = !empty($laudoSalvo) ? $laudoSalvo['idLaudoFinal'] : '';

        if (empty($idLaudoFinal)) {
            return $avaliacaoResultadosDbTable->insert($laudoFinal);
        }

        return $avaliacaoResultadosDbTable->alterar($laudoFinal, ['idLaudoFinal = ?' => $idLaudoFinal]);
    }
}
