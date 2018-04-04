<?php

/**
 * ResolverInconsistenciaController
 * @author Equipe RUP - Politec
 * @since 08/04/2015
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 */

class ResolverInconsistenciaController extends MinC_Controller_Action_Abstract
{
    public function incentivadorProponenteIguaisAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $model = new InconsistenciaBancariaModel();
        try {
            $model->resolverIncentivadorProponenteIguais($this->getRequest()->getParam('cnpjcpf'), $this->getRequest()->getParam('idInconsistencia'));
        } catch (Exception $exception) {
            $this->getResponse()->setHttpResponseCode(500)->setBody(htmlentities(utf8_encode($exception->getMessage())));
        }
    }
}
