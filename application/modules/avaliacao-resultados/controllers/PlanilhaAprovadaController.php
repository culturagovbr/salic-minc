<?php

class AvaliacaoResultados_PlanilhaAprovadaController extends MinC_Controller_Rest_Abstract
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

    public function getAction(){
        $idPronac = $this->getRequest()->getParam('idPronac');
        $data = [];
        $code = 200;

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->planilhaAprovada($idPronac);
        $data = \TratarArray::utf8EncodeArray($resposta->toArray());
        $data = $this->filtrarPlanilhaParaAnalise($data);
        return $this->customRenderJsonResponse($data, $code);
    }

    private function filtrarPlanilhaParaAnalise($planilha)
    {
        $i = 0;

        $novaPlanilha = [];
        foreach ($planilha as $item) {
            $row = $item;
            $i++;
            unset($row['Pronac']);
            unset($row['NomeProjeto']);
            unset($row['Uf']);
            unset($row['Cidade']);
            unset($row['Item']);
            unset($row['Etapa']);
            unset($row['Descricao']);

            $row["Seq"] = $i;
            $row['Produto'] = !empty($item['cdProduto'])
                ? $item['Produto']
                : html_entity_decode('Administra&ccedil;&atilde;o do Projeto');


            $row['vlAComprovar'] = $row['vlAprovado'] - $row['vlComprovado'];

            // corrigir no front e depois remover
            $row['varlorComprovado'] = $row['vlComprovado'];
            $row['varlorAprovado'] = $row['vlAprovado'];
            $row['varlorAprovado'] = $row['vlAprovado'];
            $row['produto'] = $row['Produto'];
            $row['item'] = $row['descItem'];

            $novaPlanilha[] = $row;
        }

        return $novaPlanilha;
    }

    public function indexAction(){}

    public function headAction(){}

    public function postAction(){}

    public function putAction(){}

    public function deleteAction(){}

}
