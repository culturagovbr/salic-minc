<?php

use Application\Modules\Readequacao\Service\Readequacao\Readequacao as ReadequacaoService;
use Application\Modules\Documento\Service\Documento\Documento as DocumentoService;

class Readequacao_DadosReadequacaoController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::PROPONENTE,
            Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO,
            Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO,
        ];

        $permissionsPerMethod  = [
            'post' => [
                Autenticacao_Model_Grupos::PROPONENTE
            ]
        ];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        $subRoutes = [
            'readequacao/dados-readequacao/{idReadequacao}/documento/{idDocumento}'
        ];

        $this->registrarSubRoutes($subRoutes);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function getAction() {
        $data = [];
        $code = 200;

        $idReadequacao = $this->getRequest()->getParam('id');

        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $permissao = $readequacaoService->verificarPermissaoNoProjeto();

        if (!$permissao) {
            $data['permissao'] = false;
            $httpCode = 203;
            $data['message'] = utf8_decode('Você não tem permissão para acessar este projeto');
        } else {
            $data = $readequacaoService->buscar($idReadequacao);
        }

        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($data), $code);
    }

    public function indexAction(){
        $data = [];
        $code = 200;

        $idReadequacao = $this->getRequest()->getParam('idReadequacao');
        $idPronac = $this->getRequest()->getParam('idPronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $idTipoReadequacao = $this->getRequest()->getParam('idTipoReadequacao');
        $stEstagioAtual = $this->getRequest()->getParam('stEstagioAtual');

        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $permissao = $readequacaoService->verificarPermissaoNoProjeto();

        if (!$permissao) {
            $data['permissao'] = false;
            $code = 203;
            $data['message'] = 'Você não tem permissão para acessar este projeto';
            $this->customRenderJsonResponse($data, $code);
        } else {
            $code = 200;
            $data = $readequacaoService->buscarReadequacoes($idPronac, $idTipoReadequacao, $stEstagioAtual);
        }

        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($data), $code);
    }

    public function headAction(){}

    public function postAction(){
        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $permissao = $readequacaoService->verificarPermissaoNoProjeto();
        if (!$permissao) {
            $data['permissao'] = false;
            $code = 203;
            $data['message'] = 'Você não tem permissão para alterar esta readequação';
        } else {
            $code = 200;
            $response = $readequacaoService->salvar();
            if ($response) {
                $data = $response;
            }
        }
        $this->renderJsonResponse($data, $code);
    }

    public function putAction(){}

    public function deleteAction(){
        $data = [];
        $code = 200;
        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $permissao = $readequacaoService->verificarPermissaoNoProjeto();
        if (!$permissao) {
            $data['permissao'] = false;
            $code = 203;
            $data['message'] = 'Você não tem permissão para excluir esta readequação';
        } else {
            $response = $readequacaoService->remover();
            if ($response) {
                $data['error'] = false;
                $data['message'] = 'Readequação excluída';
            } else {
                $data['error'] = true;
                $data['message'] = 'Erro ao exclur readequação';
            }
        }
        $this->customRenderJsonResponse($data, $code);
    }
}
