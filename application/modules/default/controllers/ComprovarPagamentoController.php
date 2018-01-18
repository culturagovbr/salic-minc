<?php 
class ComprovarPagamentoController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::init();
        # context
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('pesquisar', 'json')
            ->addActionContext('deletar', 'json')
            ->initContext()
        ;
    }

    public function deletarAction()
    {
        $comprovanteParamentoModel = new ComprovantePagamento($this->getRequest()->getParam('comprovante'));
        $comprovanteParamentoModel->deletar();
        $this->_helper->json(['success'=> true]);
    }

    public function pesquisarAction()
    {
        $comprovanteParamentoModel = new ComprovantePagamento();
        $this->view->comprovantes = $comprovanteParamentoModel->pesquisarComprovantePorItem($this->getRequest()->getParam('item'));
        array_walk($this->view->comprovantes, function (&$value) {
            $value = array_map('utf8_encode', $value);
        });
    }
}
