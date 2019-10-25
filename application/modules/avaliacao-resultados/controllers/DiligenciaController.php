<?php

use Application\Modules\AvaliacaoResultados\Service\ParecerTecnico\Diligencia as DiligenciaService;

class AvaliacaoResultados_DiligenciaController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod  = [
//            'index' => $profiles,
//            'post' => $profiles
        ];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function headAction(){}

    public function indexAction()
    {
        $diligencia = new DiligenciaService();
        if (!isset($this->_request->idPronac) ||
            !isset($this->_request->situacao) ||
            !isset($this->_request->tpDiligencia)) {
            $this->renderJsonResponse([message => 'Erro de requisição'], 400);
        } else {
            $coisa = $diligencia->listaDiligenciaPainel(['idPronac' => $this->_request->idPronac,
                'situacao' => $this->_request->idPronac,
                'tipoDiligencia' => $this->_request->tpDiligencia
            ]);
            if (!isset($coisa)){
                $this->renderJsonResponse([], 400);
            }elseif (isset($coisa)){
                $this->renderJsonResponse(\TratarArray::utf8EncodeArray($coisa), 200);
            }
        }
    }

    public function getAction()
    {
        $diligencia = new DiligenciaService();
        $coisa = $diligencia->listaDiligenciaPainel([
            'idPronac' => $this->getRequest()->getParam('id'),
            'situacao' => 'E17',
            'tipoDiligencia' => 174
        ]);

        if (!isset($coisa)){
            $this->renderJsonResponse([], 400);
        }elseif (isset($coisa)){
            $this->renderJsonResponse(\TratarArray::utf8EncodeArray($coisa), 200);
        }
    }

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

        if ($diligenciaDAO->existeDiligenciaAberta($idPronac)) {
            $this->view->assign('data',['message' => 'Existe dilig&ecirc;ncia aguardando resposta!']);
            $this->getResponse()->setHttpResponseCode(405);
            return;
        }

        $dados = [
            'idPronac' => $idPronac,
            'DtSolicitacao' => new Zend_Db_Expr('GETDATE()'),
            'Solicitacao' => utf8_decode($solicitacao),
            'idTipoDiligencia' => $idTipoDiligencia,
            'idProduto' => null,
            'stEstado' => 0,
            'stEnviado' => 'S',
            'idSolicitante' => $idagente
        ];

        $rowDiligencia = $diligenciaDAO->inserir($dados);
        $projeto = new Projetos();
        $projeto->alterarSituacao($idPronac, null, 'E17', 'Diligência na prestação de contas');

        $this->view->assign('data',['message' => 'criadosssss!']);
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function putAction()
    {
        $this->getResponse()->setHttpResponseCode(400);
    }

    public function deleteAction()
    {}
}
