<?php
use Application\Modules\AvaliacaoResultados\Service\TipoAvaliacao as TipoAvaliacao;

class AvaliacaoResultados_TipoAvaliacaoRestController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $permissionsPerMethod  = ['*'];

        $this->setValidateUserIsLogged();
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function headAction()
    {
        $this->renderJsonResponse(["Nenhum conteúdo"], 204);
    }

    public function indexAction()
    {
        $this->renderJsonResponse(["Nenhum conteúdo"], 204);
    }

    public function getAction()
    {

        if (!isset($this->_request->idPronac)){
            $this->customRenderJsonResponse([], 422);
        }
        $tipoAvaliacaoService = new TipoAvaliacao($this->getRequest(), $this->getResponse());
        $resposta = $tipoAvaliacaoService->tipoAvaliacao();
        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($resposta), 200);
    }

    public function postAction()
    {
        // TODO: Implement postAction() method.
    }

    public function putAction()
    {
        // TODO: Implement putAction() method.
    }

    public function deleteAction()
    {
//        403 Proibido
        $this->customRenderJsonResponse(["Método não permitido"], 405);
    }
}
