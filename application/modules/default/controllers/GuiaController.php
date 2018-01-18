<?php
class GuiaController extends MinC_Controller_Action_Abstract
{
    /**
     * (non-PHPdoc)
     * @see GenericControllerNew::init()
     */
    public function init()
    {
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('cadastrar', 'json')
            ->addActionContext('atualizar', 'json')
            ->addActionContext('deletar', 'json')
            ->addActionContext('pesquisar', 'json')
            ->initContext()
        ;
    }

    /**
     * (non-PHPdoc)
     * @see GenericControllerNew::postDispatch()
     */
    public function postDispatch()
    {
    }

    /**
     *
     */
    public function cadastrarAction()
    {
        try {
            $guia = new GuiaModel(
                null,
                $this->getRequest()->getParam('categoria'),
                $this->getRequest()->getParam('nomeGuia'),
                $this->getRequest()->getParam('txtGuia')
            );
            $guia->cadastrar();
            $this->view->guia = $guia->toStdClass();
        } catch (Exception $e) {
            xd($e);
            $this->view->error = $e->getMessage();
        }
    }

    /**
     *
     */
    public function atualizarAction()
    {
        $guia = new GuiaModel(
            $this->getRequest()->getParam('guia'),
            $this->getRequest()->getParam('categoria'),
            $this->getRequest()->getParam('nomeGuia'),
            $this->getRequest()->getParam('txtGuia')
        );
        $guia->atualizar();
        $this->view->guia = $guia->toStdClass();
    }

    /**
     * @return void
     */
    public function deletarAction()
    {
        $guia = new GuiaModel($this->getRequest()->getParam('guia'));
        $this->view->guia = $guia->deletar();
    }

    /**
     *
     */
    public function pesquisarAction()
    {
        $guiaModel = new GuiaModel();
        $this->view->guia = $guiaModel->pesquisar($this->getRequest()->getParam('guia'));
    }
}
