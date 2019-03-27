<?php

class PrestacaoContas_PlanilhaAprovacaoController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('get', 'json')
            ->initContext('json');
    }

    public function headAction(){}

    public function indexAction()
    {
    }

    public function getAction()
	{
        $idPronac = $this->_request->getParam("idPronac");
        $idPlanilhaItem = $this->_request->getParam("idPlanilhaItem");
        $uf = $this->getRequest()->getParam('uf');
        $municipio = $this->getRequest()->getParam('idmunicipio');
        $idPlanilhaEtapa = $this->getRequest()->getParam('idplanilhaetapa');
        $codigoProduto = $this->getRequest()->getParam('produto');
        $stItemAvaliado = $this->getRequest()->getParam('stItemAvaliado');
        $etapa = $this->getRequest()->getParam('etapa');

        $planilhaAprovacaoModel = new PlanilhaAprovacao();

        $projeto = $planilhaAprovacaoModel
            ->vwComprovacaoFinanceiraProjeto(
                $idPronac,
                $uf,
                $etapa,
                $codigoProduto,
                $municipio,
                null,
                $idPlanilhaItem
            );

        $data = [];

        foreach($projeto->toArray() as $key => $value) {
            $data[] =  array_map('utf8_encode', $value);
        }

        $this->view->assign('data', $data[0]);
        $this->getResponse()->setHttpResponseCode(200);
	}

    public function postAction()
    {}

    public function putAction()
    {}

    public function deleteAction()
    {}
}
