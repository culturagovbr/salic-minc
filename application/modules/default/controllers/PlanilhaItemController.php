<?php
class PlanilhaItemController extends Zend_Controller_Action
{
    public function init()
    {
        parent::init();
        # context
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('pesquisar', 'json')
            ->initContext()
        ;
    }

    public function pesquisarAction()
    {
        $planilhaItemModel = new PlanilhaItem();
        $this->view->item = $planilhaItemModel->pesquisar($this->getRequest()->getParam('item'));
    }
}
