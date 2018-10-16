<?php

use Application\Modules\AvaliacaoResultados\Service\Fluxo\FluxoProjetoAssinatura as FluxoProjetoAssinaturaService;

class AvaliacaoResultados_ProjetoAssinaturaController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod  = [];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction(){}

    public function getAction(){
        $projetosAssinaturaService = new FluxoProjetoAssinaturaService($this->getRequest(), $this->getResponse());
        $projetos = [];

        switch ($this->getRequest()->getParam('estado')) {
            case 'assinar':
                $projetos = $projetosAssinaturaService->obterProjetosAguardandoAssinaturaTecnico()->toArray();
                break;
            case 'em_assinatura':
                $projetos = $projetosAssinaturaService->obterProjetosAguardandoAssinaturasSuperiores()->toArray();
                break;
            case 'historico':
                $projetos = $projetosAssinaturaService->obterProjetosComAssinaturasFinalizadaPorTecnico()->toArray();
                break;
            default:
                $this->customRenderJsonResponse($projetos, 404);
        }

        $this->renderJsonResponse(
            \TratarArray::utf8EncodeArray($projetos),
            200);
    }

    public function headAction(){}

    public function postAction(){
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $assinaturaService = new \MinC\Assinatura\Servico\Assinatura(
            [
                'Despacho' => 'devolvi pq eu quis',
                'idTipoDoAto' => 622,
                'idPerfilDoAssinante' => $this->grupoAtivo->codGrupo,
                'idPronac' => 132451,
                'idDocumentoAssinatura' => 2262,
                'stEstado' => 1
            ]
        );
        $assinaturaService->devolver();
    }

    public function putAction(){}

    public function deleteAction(){}
}
