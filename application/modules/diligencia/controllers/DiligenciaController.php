<?php

class Diligencia_DiligenciaController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->addActionContext('post', 'json')
            ->initContext('json');
    }

    public function headAction(){}

    public function indexAction()
    {
         $data = array(1 => "to", 12 => 2);
         $this->view->assign('nozes', $data);
         $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction()
    {}

    public function postAction()
    {
        $idagente = (Zend_Auth::getInstance())->getIdentity()->usu_codigo;
        $idPronac = $this->getRequest()->getParam('idPronac');
        $solicitacao = $this->getRequest()->getParam('solicitacao');
        $idTipoDiligencia = $this->getRequest()->getParam('tpDiligencia');

        if (!$idPronac) {
            throw new Exception('Falta pronac');
        }

        $diligenciaDAO = new Diligencia();
        $auth = Zend_Auth::getInstance();

        /* @todo implementar regras */
        // caso ja tenha diligencia para o pronac
        $buscarDiligenciaResp = $diligenciaDAO->buscar(
            array(
                'idPronac = ?' => $idPronac,
                'DtResposta ?' => array(new Zend_Db_Expr('IS NULL')),
                'stEnviado = ?'=>'S' ),
            array('idDiligencia DESC'), 0, 0,
            $this->getRequest()->getParam('idProduto')
        );
        if (count($buscarDiligenciaResp) > 0) {
            $this->view->assign('data',['message' => 'Existe dilig&ecirc;ncia aguardando resposta!']);
            $this->getResponse()->setHttpResponseCode(405);
            return;
        }

        $dados = array(
            'idPronac' => $idPronac,
            'DtSolicitacao' => new Zend_Db_Expr('GETDATE()'),
            'Solicitacao' => $solicitacao,
            'idTipoDiligencia' => $idTipoDiligencia,
            'idProduto' => null,
            'stEstado' => 0,
            'stEnviado' => 'S',
            'idSolicitante' => $idagente
        );

        $rowDiligencia = $diligenciaDAO->inserir($dados);

        $this->view->assign('data',['message' => 'criado!']);
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function putAction()
    {
         $this->getResponse()->setHttpResponseCode(400);
    }

    public function deleteAction()
    {}
}
