<?php

abstract class MinC_Controller_Rest_Abstract extends Zend_Controller_Action
{
    protected $_response;

    protected $_request;

    protected $_methodsPermissions = [];

    protected $_usu_codigo;

    protected $_codOrgaoSuperior;

    protected $_codGrupo;

    protected $_codOrgao;

    protected $_IdUsuario;

    protected $_checkUserIsLogged = false;

    protected $_subRoutes = [];


    const ALL_METHODS = '*';
    const COD_ORGAO_PROPONENTE = 1111;

    /**
     * Validação de perfis de acesso ou comportamentos excepcionais na inicialização
     */
    public function init()
    {
        $authInstance = Zend_Auth::getInstance();

        if ($this->_checkUserIsLogged) {
            $authInstance->hasIdentity()?: $this->permissionDenied();
        }

        $authObject = $authInstance->getIdentity();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('get', 'json')
            ->addActionContext('index', 'json')
            ->addActionContext('post', 'json')
            ->addActionContext('head', 'json')
            ->addActionContext('put', 'json')
            ->addActionContext('delete', 'json')
            ->initContext('json');

        $this->_request = $this->getRequest();
        $this->_response = $this->getResponse();

        $routeData = $this->verifySubRoutes();
        
        if($authInstance->hasIdentity() && !empty($authObject->usu_codigo)){
            //Usuario Interno
            $this->_usu_codigo = $authObject->usu_codigo;
            $this->_codOrgao = $authObject->usu_orgao;
            $this->_codGrupo = $GrupoAtivo->codGrupo;
            $this->_codOrgaoSuperior = $authObject->usu_org_max_superior;
        } else if ($authInstance->hasIdentity() && !empty($authObject->IdUsuario)){
            //Usuario externo
            $this->_codGrupo = self::COD_ORGAO_PROPONENTE;
            $this->_IdUsuario = $authObject->IdUsuario;
        }

        ($this->checkPermission())?: $this->permissionDenied();
    }

    final protected function setProtectedMethodsProfilesPermission(array $permissionsPerMethod)
    {
        $this->_methodsPermissions = $permissionsPerMethod;
    }

    final protected function setValidateUserIsLogged()
    {
        $this->_checkUserIsLogged = true;
    }

    final protected function registrarSubRoutes(array $subRoutes)
    {
        $this->_subRoutes = $subRoutes;
    }

    final private function checkPermission() : bool
    {
        $canAccess = true;
        $params = $this->_request->getParams();
        $requestedMethod = $params['action'];

        if (array_key_exists(self::ALL_METHODS, $this->_methodsPermissions)) {
            $permissionsForAllMethods = $this->_methodsPermissions[self::ALL_METHODS];
            return (in_array($this->_codGrupo, $permissionsForAllMethods))? true : false;
        }


        if (empty($this->_methodsPermissions[$requestedMethod])) {
            return $canAccess;
        }

        $permissionArray = $this->_methodsPermissions[$requestedMethod];
        return (in_array($this->_codGrupo, $permissionArray))? true : false;
    }

    final public function permissionDenied()
    {
        $this->getResponse()->setHttpResponseCode(403);

        $this->_helper->json([
            'error' =>[
                'code' => 403,
                'message' => 'Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!'
            ]
        ]);
    }

    /**
     * verifySubRoutes
     *
     * Adiciona compatibilidade com rotas REST aninhadas, recebendo nested urls 
     * e transformado numa forma em que a controller pode ler.
     * 
     * Busca as subrotas mapeadas; caso encontre uma que bate com o padrão,
     * redireciona para a controller na forma abaixo:
     *
     * readequacao/dados-readequacao/15522/documento/112
     *                         |
     *                         V
     * readequacao/dados-readequacao-documento/idReadequacao/15522/idDocumento/112
     *
     */    
    final protected function verifySubRoutes()
    {
        if (empty($this->_subRoutes)) {
            return;
        }
        
        $arrRequest = $this->getAllParams();
        $currentUrl = $arrRequest['module'] . '/' .  $arrRequest['controller'];
       
        if (array_key_exists('id', $arrRequest) && $arrRequest['action'] == 'get') {
            $currentUrl .= '/' . $arrRequest['id'];
        } else {
            $otherParams = $arrRequest;
            unset($otherParams['module']);
            unset($otherParams['controller']);
            unset($otherParams['action']);

            foreach ($otherParams as $key => $value) {
                $currentUrl .= '/' . $key;
                if ($value) {
                    $currentUrl .= '/' . $value;
                }
            }
        }
        
        $currentUrlPieces = preg_split('/\//', $currentUrl);
        $capturedParams = [];
        $capturedActions = [];
        
        if (array_key_exists('module', $arrRequest) &&
            array_key_exists('controller', $arrRequest) &&
            array_key_exists('action', $arrRequest) &&
            in_array('get', $arrRequest)            
        ) {
            foreach ($this->_subRoutes as $definedRoute) {
                $pieceMatch = [];
                $routePieces = preg_split('/\//', $definedRoute);
                
                if (count($currentUrlPieces) == count($routePieces)) {
                    foreach ($currentUrlPieces as $key => $piece) {
                        if ($routePieces[$key] == $currentUrlPieces[$key]) {
                            $pieceMatch[$key] = true;
                            $capturedActions[] = $piece;
                        } else if (!preg_match('/[{].*[}]/', $piece)) {
                            $keyName = preg_replace('/[{}]/', '', $routePieces[$key]);
                            $capturedParams[$keyName] = $piece;
                            $pieceMatch[$key] = true;
                        } else {
                            $pieceMatch[$key] = false;
                        }
                    }
                    
                    if (!in_array(false, $pieceMatch)) {
                        $moduleName = $capturedActions[0];
                        $controllerName = $capturedActions[1] . '-' . $capturedActions[2];
                        
                        $params = '';
                        foreach ($routeData['params'] as $key => $value) {
                            if ($value != '') {
                                $params .= "$key/$value/";
                            } else {
                                $params .= "$key/";
                            }
                        }
                        
                        $this->redirect($moduleName . '/' . $controllerName . '/' . $params);
                    }
                }
            }
        }
        return false;
    }

    final public function renderJsonResponse(array $data, int $code)
    {
        $this->getResponse()->setHttpResponseCode($code);

        $this->view->assign(
            'data',
            [
                'code' => $code,
                'items' => $data
            ]
        );
    }

    final public function customRenderJsonResponse(array $data, int $code)
    {
        $this->getResponse()->setHttpResponseCode($code);

        $this->view->assign($data);
    }

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    abstract public function indexAction();

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    abstract public function getAction();

    /**
     * The head action handles HEAD requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    abstract public function headAction();

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    abstract public function postAction();

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    abstract public function putAction();

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    abstract public function deleteAction();

}
