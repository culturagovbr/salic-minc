<?php

use Application\Modules\AvaliacaoResultados\Service\Fluxo\FluxoProjeto as FluxoProjetoService;

class AvaliacaoResultados_ProjetoInicioController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod  = [
        ];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function getAction(){

        $data = \TratarArray::utf8EncodeArray([]);
        $this->renderJsonResponse($data, 200);
    }

    public function indexAction(){

        $auth = Zend_Auth::getInstance();

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->codGrupo = $GrupoAtivo->codGrupo;
        $this->codOrgao = $GrupoAtivo->codOrgao;
        $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : null;

        $situacaoEncaminhamentoPrestacao = 1;
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search');
        $order = $this->getRequest()->getParam('order');

        $column = $order[0]['column']+1;
        $orderType = $order[0]['dir'];
        $order = $column.' '.$orderType;

        $tbPlanilhaAplicacao = new \AvaliacaoResultados_Model_DbTable_tbPlanilhaAprovacao();
        $projetos = $tbPlanilhaAplicacao->obterAnaliseFinanceiraVirtual(
            $this->codOrgao,
            $situacaoEncaminhamentoPrestacao
        );

        if (count($projetos) > 0) {
            foreach($projetos->toArray() as $coluna => $item){
                $projetosAnaliseFinanceiraVirtual[] = array_map('utf8_encode', $item);
            }

            $recordsTotal = $tbPlanilhaAplicacao->obterProjetosAnaliseFinanceiraVirtual(
                $this->codOrgao,
                $situacaoEncaminhamentoPrestacao,
                null,
                null,
                null,
                null
            );
            $recordsTotal = count($recordsTotal);

            $recordsFiltered = $tbPlanilhaAplicacao->obterProjetosAnaliseFinanceiraVirtual(
                $this->codOrgao,
                $situacaoEncaminhamentoPrestacao,
                null,
                null,
                null,
                $search
            );
            $recordsFiltered = count($recordsFiltered);
        }

        $this->_helper->json(
            [
                'code'=> 200,
                "items" => !empty($projetosAnaliseFinanceiraVirtual) ? $projetosAnaliseFinanceiraVirtual : [],
                'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
                'draw' => $draw,
                'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0,
            ]
        );
    }

    public function headAction(){}

    public function postAction(){}

    public function putAction(){}

    public function deleteAction(){}

}
