<?php
/**
 * @author Caio Lucena <caioflucena@gmail.com>
 */
class RespostaController extends MinC_Controller_Action_Abstract
{
    /**
     * (non-PHPdoc)
     * @see GenericControllerNew::init()
     */
    public function init()
    {
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('cadastrar', 'json')
            ->addActionContext('deletar', 'json')
            ->initContext()
        ;
    }

    /**
     * (non-PHPdoc)
     * @see GenericControllerNew::postDispatch()
     */
    public function postDispatch()
    {
        ;
    }

    /**
     *
     */
    public function cadastrarAction()
    {
        try {
            $resposta = new RespostaModel(
                null,
                $this->getRequest()->getParam('tipoResposta'),
                $this->getRequest()->getParam('questao'),
                $this->getRequest()->getParam('nome')
            );
            $resposta->cadastrar();
            $this->view->resposta = $resposta->toStdClass();
        } catch (Exception $e) {
            echo '<pre>';
            print_r($e);
            die;
            $this->view->error = $e;
        }
    }

    /**
     * @return void
     */
    public function deletarAction()
    {
        $resposta = new RespostaModel($this->getRequest()->getParam('resposta'));
        $this->view->resposta = $resposta->deletar();
    }
}
