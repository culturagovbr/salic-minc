<?php

namespace Application\Modules\AvaliacaoResultados\Service\Encaminhar;

class AvaliacaoFinanceira 
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    public function salvar($params) {
        $mapper = new \AvaliacaoResultados_Model_tbEncaminhamentoPrestacaoContasMapper();
        $model = new \AvaliacaoResultados_Model_tbEncaminhamentoPrestacaoContas();

        $model->setIdPronac($params['idPronac']);
        $model->setIdAgenteOrigem($params['idAgenteOrigem']);
        $model->setDtInicioEncaminhamento($params['dtInicioEncaminhamento']);
        $model->setIdOrgaoDestino($params['idOrgaoDestino']);
        $model->setIdOrgaoOrigem($params['idOrgaoOrigem']);
        $model->setIdAgenteDestino($params['idAgenteDestino']);
        $model->setCdGruposDestino($params['cdGruposDestino']);
        $model->setCdGruposOrigem($params['cdGruposOrigem']);
        $model->setDtFimEncaminhamento($params['dtFimEncaminhamento']);
        $model->setIdSituacaoEncPrestContas($params['idSituacaoEncPrestContas']);
        $model->setIdSituacao($params['idSituacao']);
        $model->setStAtivo($params['stAtivo']);
        $model->setDsJustificativa($params['dsJustificativa']);

        $id = $mapper->save($model);

        if ($id) {
            // altera todos os encaminhamentos anteriores para stAtivo = 0
            $mapper->update(
                ['stAtivo' => 0],
                ['idPronac = ?' => $params['idPronac'], 'idEncPrestContas != ?' => $id]
            );
        }
    }
}