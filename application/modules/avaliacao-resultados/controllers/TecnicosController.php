<?php

class AvaliacaoResultados_TecnicosController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod = [];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
        $idOrgaoDestino = \Orgaos::ORGAO_SEFIC_ARQ_CGEPC;
        $idPerfilDestino = \Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS;

        $tblProjetos = new Projetos();
        $AgentesOrgao = $tblProjetos->buscarComboOrgaos($idOrgaoDestino, $idPerfilDestino);

        foreach ($AgentesOrgao as $agentes) {
            $dadosAgente[] = [
                'usu_codigo' => $agentes->usu_codigo,
                'usu_nome' => utf8_encode($agentes->usu_nome),
                'idperfil' => $idPerfilDestino,
                'idAgente' => $agentes->usu_codigo
            ];
        }

        $this->renderJsonResponse($dadosAgente, 200);
    }

    public function getAction()
    {
        $orgaoSuperior = $this->getRequest()->getParam('id');

        $tbOrgao = new \Orgaos();
        $idOrgaoDestino = $tbOrgao->obterUnidadeParaComprovacaoFinanceira($orgaoSuperior);
        $idPerfilDestino = \Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS;

        $tblProjetos = new Projetos();
        $AgentesOrgao = $tblProjetos->buscarComboOrgaos($idOrgaoDestino, $idPerfilDestino);

        foreach ($AgentesOrgao as $agentes) {
            $dadosAgente[] = [
                'usu_codigo' => $agentes->usu_codigo,
                'usu_org_superior' => $agentes->org_superior,
                'usu_nome' => utf8_encode($agentes->usu_nome),
                'idperfil' => $idPerfilDestino,
                'idAgente' => $agentes->usu_codigo
            ];
        }
        $this->renderJsonResponse($dadosAgente, 200);
    }

    public function headAction()
    {
    }

    public function postAction()
    {
        $this->renderJsonResponse(['post'], 200);
    }

    public function putAction()
    {
    }

    public function deleteAction()
    {
    }
}
