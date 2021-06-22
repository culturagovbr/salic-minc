<?php

use Application\Modules\AvaliacaoResultados\Service\Assinatura\Parecer\DocumentoAssinatura as DocumentoAssinaturaService;
use Application\Modules\AvaliacaoResultados\Service\Assinatura\Laudo\DocumentoAssinatura as DocumentoAssinaturaLaudoService;

class AvaliacaoResultados_DashboardController extends MinC_Controller_Rest_Abstract
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

    public function indexAction()
    {
        $usuCodigo = Zend_Auth::getInstance()->getIdentity()->usu_codigo;
        $quantidadeProjetos = new \AvaliacaoResultados_Model_DbTable_FluxosProjeto();

        $emAndamento = $quantidadeProjetos->quantidadeProjetos([5], $usuCodigo);
        $assinar = $quantidadeProjetos->quantidadeProjetos([6], $usuCodigo);

        $this->renderJsonResponse(
            [
                'Em Analise' =>
                [
                    'valor' => $emAndamento->toArray()['quantidade'],
                    'url'=>'#/painel/aba-em-analise'
                ],
                'Assinar' => [
                    'valor' => $assinar->toArray()['quantidade'],
                    'url' => '#/painel/assinar'
                ]
            ],
            200
        );
    }

    public function getAction()
    {
    }

    public function headAction(){}

    public function postAction()
    {
    }

    public function putAction()
    {
    }

    public function deleteAction(){}
}
