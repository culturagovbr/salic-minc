<?php

class DiligenciaRestController extends Minc_Controller_AbstractRest
{
    public function init()
    {
        parent::init();
    }

    public function postAction()
    {
    }
    
    public function indexAction()
    {
    }

    public function getAction()
    {
        $idDiligencia = $this->_request->getParam('id');
        $modelDiligencia = new tbDiligencia();
        $resultado = $modelDiligencia->buscarPorIdDiligencia($idDiligencia);
        $diligencia = (object) $resultado->toArray();

        if ($diligencia) {
            # Formatando dados
            $diligencia->dataSolicitacao = $diligencia->DtSolicitacao? date('d/m/Y H', strtotime($diligencia->DtSolicitacao)). 'h'. date('i', strtotime($diligencia->DtSolicitacao)): null;
            $diligencia->texto = html_entity_decode(utf8_encode($diligencia->texto), ENT_COMPAT, 'UTF-8');
        }

        # Resposta do serviço.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($diligencia));
    }
    
    public function putAction()
    {
    }

    public function deleteAction()
    {
    }
}
