<?php

class Projeto_MenuController extends Projeto_GenericController
{
    protected $idPronac;

    public function init()
    {
        parent::init();

        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('obter-menu', 'json')
            ->initContext('json');

        $this->idPronac = $this->_request->getParam("idPronac"); // pega o id do pronac via get

        if (strlen($this->idPronac) > 7) {
            $this->idPronac = Seguranca::dencrypt($this->idPronac);
        }
        $this->view->idPronac = $this->idPronac;
        $this->view->idPronacHash = Seguranca::encrypt($this->idPronac);
    }

    public function obterMenuAjaxAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            if (empty($this->idPronac)) {
                throw new Exception("Pronac &eacute; obrigat&oacute;rio");
            }

            $modelProjeto = new Projeto_Model_Menu();
            $menu = $modelProjeto->obterMenu($this->idPronac);

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $menu));
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }
}