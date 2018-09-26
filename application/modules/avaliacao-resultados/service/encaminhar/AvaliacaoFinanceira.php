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

        $auth = \Zend_Auth::getInstance();
        $GrupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        $codGrupo = $GrupoAtivo->codGrupo;
        $codOrgao = $GrupoAtivo->codOrgao;
        $idAgenteOrigem = $auth->getIdentity()->usu_codigo;

        $model->setIdPronac($params['idPronac']);
        $model->setIdAgenteOrigem($idAgenteOrigem);
        $model->setDtInicioEncaminhamento((new \DateTime())->format('Y-m-d H:i:s'));
        $model->setIdOrgaoDestino($params['idOrgaoDestino']);
        $model->setIdOrgaoOrigem($codOrgao);
        $model->setIdAgenteDestino($params['idAgenteDestino']);
        $model->setCdGruposDestino($params['cdGruposDestino']);
        $model->setCdGruposOrigem($codGrupo);
        $model->setIdSituacaoEncPrestContas($params['idSituacaoEncPrestContas']);
        $model->setIdSituacao($params['idSituacao']);
        $model->setStAtivo(1);
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