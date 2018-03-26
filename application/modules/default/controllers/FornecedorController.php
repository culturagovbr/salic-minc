<?php 
class FornecedorController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::init();
        # context
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('pesquisar-fornecedor-item', 'json')
            ->initContext()
        ;
    }

    public function pesquisarFornecedorItemAction()
    {
        $fornecedorModel = new FornecedorModel();
        $result = $fornecedorModel->pesquisarFornecedorItem($this->getRequest()->getParam('item'));
        if ($result) {
            $this->view->fornecedor = array_map('utf8_encode', $result);
        }
    }
}
