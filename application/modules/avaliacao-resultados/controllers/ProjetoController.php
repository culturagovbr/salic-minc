<?php

use Application\Modules\AvaliacaoResultados\Service\Fluxo\FluxoProjeto as FluxoProjetoService;

class AvaliacaoResultados_ProjetoController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            // Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            // Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            // Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod  = [
        ];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function getAction() {
        $idPronac = $this->getRequest()->getParam('idPronac');
        $data = [];

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
//        $resposta = $planilhaAprovacaoModel->obterItensAprovados($idPronac);
        $consolidacao = $planilhaAprovacaoModel->consolidacaoValoresProjeto($idPronac);

        $projeto = new Projetos();
        $projeto = $projeto->buscarDadosCompletos($idPronac);

        $data['nomeProjeto'] = $projeto->current()['NomeProjeto'];
        $data['vlTotalComprovar'] = $consolidacao->valorAprovadoProjeto - $consolidacao->valorComprovado;
        $data['vlAprovado'] = $consolidacao->valorAprovadoProjeto;
        $data['vlComprovado'] = $consolidacao->valorComprovado;
        $data['pronac'] = $projeto->current()['AnoProjeto'] . $projeto->current()['Sequencial'];

        $diligencia = new Diligencia();
        $data['diligencia'] = $diligencia->existeDiligenciaAberta($idPronac);

        $fluxo = new FluxoProjetoService();
        $estado = $fluxo->estado($idPronac);
        $data['estado'] = $estado ? $estado->toArray() : null;

        $documento = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $data['documento'] = $documento->findBy([
            'idPronac = ?' => $idPronac,
            'idTipoDoAtoAdministrativo = ?' => 622,
            'cdSituacao = ?' => 1,
            'stEstado = ?' => 1]
        );

        $data = \TratarArray::utf8EncodeArray($data);

        $this->renderJsonResponse($data, 200);
    }

    public function indexAction(){}

    public function headAction(){}

    public function postAction(){}

    public function putAction(){}

    public function deleteAction(){}

}
