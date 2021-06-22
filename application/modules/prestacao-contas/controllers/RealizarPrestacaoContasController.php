<?php
class PrestacaoContas_RealizarPrestacaoContasController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $PermissoesGrupo = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO,
            Autenticacao_Model_Grupos::SECRETARIO
        ];

        $auth = Zend_Auth::getInstance();

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        isset($auth->getIdentity()->usu_codigo) ? $this->idUsuario = $auth->getIdentity()->usu_codigo : $this->idUsuario = $auth->getIdentity()->IdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->codGrupo = $GrupoAtivo->codGrupo;
            $this->codOrgao = $GrupoAtivo->codOrgao;
            $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : null;
        }

        parent::init();
    }

    public function indexAction()
    {
        $idpronac = $this->_request->getParam('idPronac');

        $this->view->idPronac = $idpronac;

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->obterItensAprovados($idpronac);

        $vlTotalComprovar = 0;
        $vlComprovado = 0;
        $vlAprovado = 0;
        foreach ($resposta as $item) {

            if ($item->vlAprovado == 0) {
                continue;
            }
            
            $vlComprovar = $item->vlAprovado - $item->vlComprovado;
            $vlTotalComprovar += $vlComprovar;

            $vlAprovado += $item->vlAprovado;
            $vlComprovado += $item->vlComprovado;

            $nomeProjeto = $item->NomeProjeto;
            $pronac = $item->Pronac;
        }

        $this->view->vlTotalComprovar = $vlTotalComprovar;
        $this->view->vlAprovado = $vlAprovado;
        $this->view->vlComprovado = $vlComprovado;
        $this->view->pronac = $pronac;
        $this->view->nomeProjeto = $nomeProjeto;



        $diligencia = new Diligencia();
        $this->view->existeDiligenciaAberta = $diligencia->existeDiligenciaAberta($idpronac);
    }

    public function planilhaAnaliseAction()
    {
        $idpronac = (int)$this->_request->getParam('idPronac');

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->planilhaAprovada($idpronac);

        $planilhaJSON = null;

        foreach($resposta as $item) {
            $planilhaJSON
            [$item->cdProduto]
            ['etapa']
            [$item->cdEtapa]
            ['UF']
            [$item->cdUF]
            ['cidade']
            [$item->cdCidade]
            ['itens']
            [$item->idPlanilhaItens] = [
                'item' => utf8_encode($item->Item),
                'varlorAprovado' => $item->vlAprovado,
                'varlorComprovado' => $item->vlComprovado,
                'comprovacaoValidada' => $item->ComprovacaoValidada,
                'idPlanilhaAprovacao' => $item->idPlanilhaAprovacao,
                'idPlanilhaItens' => $item->idPlanilhaItens,
                'ComprovacaoValidada' => $item->ComprovacaoValidada,
                'stItemAvaliado' => $item->stItemAvaliado,
            ];

            $planilhaJSON[$item->cdProduto] += [
                'produto' => html_entity_decode(utf8_encode($item->Produto)),
                'cdProduto' => $item->cdProduto,
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa] += [
                'etapa' => utf8_encode($item->Etapa),
                'cdEtapa' =>  $item->cdEtapa
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa]['UF'][$item->cdUF] += [
                'Uf' => $item->Uf,
                'cdUF' => $item->cdUF
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa]['UF'][$item->cdUF]['cidade'][$item->cdCidade] += [
                'cidade' => utf8_encode($item->Cidade),
                'cdCidade' => $item->cdCidade
            ];
        }

        $this->_helper->json($planilhaJSON);
    }

    public function planilhaAnaliseFiltrosAction()
    {
        $idpronac = (int)$this->_request->getParam('idPronac');

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->planilhaAprovada($idpronac);

        $planilhaJSON = null;

        foreach($resposta as $item) {
            $planilhaJSON
            [$item->cdProduto]
            ['etapa']
            [$item->cdEtapa]
            ['UF']
            [$item->cdUF]
            ['cidade']
            [$item->cdCidade]
            ['itens']
            [$item->stItemAvaliado]
            [$item->idPlanilhaItens] = [
                'item' => utf8_encode($item->Item),
                'varlorAprovado' => $item->vlAprovado,
                'varlorComprovado' => $item->vlComprovado,
                'comprovacaoValidada' => $item->ComprovacaoValidada,
                'idPlanilhaAprovacao' => $item->idPlanilhaAprovacao,
                'idPlanilhaItens' => $item->idPlanilhaItens,
                'ComprovacaoValidada' => $item->ComprovacaoValidada,
                'stItemAvaliado' => $item->stItemAvaliado,
            ];

            $planilhaJSON
            [$item->cdProduto]
            ['etapa']
            [$item->cdEtapa]
            ['UF']
            [$item->cdUF]
            ['cidade']
            [$item->cdCidade]
            ['itens']
            ['todos']
            [$item->idPlanilhaItens] = [
                'item' => utf8_encode($item->Item),
                'varlorAprovado' => $item->vlAprovado,
                'varlorComprovado' => $item->vlComprovado,
                'comprovacaoValidada' => $item->ComprovacaoValidada,
                'idPlanilhaAprovacao' => $item->idPlanilhaAprovacao,
                'idPlanilhaItens' => $item->idPlanilhaItens,
                'ComprovacaoValidada' => $item->ComprovacaoValidada,
                'stItemAvaliado' => $item->stItemAvaliado,
            ];

            $planilhaJSON[$item->cdProduto] += [
                'produto' => html_entity_decode(utf8_encode($item->Produto)),
                'cdProduto' => $item->cdProduto,
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa] += [
                'etapa' => utf8_encode($item->Etapa),
                'cdEtapa' =>  $item->cdEtapa
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa]['UF'][$item->cdUF] += [
                'Uf' => $item->Uf,
                'cdUF' => $item->cdUF
            ];

            $planilhaJSON[$item->cdProduto]['etapa'][$item->cdEtapa]['UF'][$item->cdUF]['cidade'][$item->cdCidade] += [
                'cidade' => utf8_encode($item->Cidade),
                'cdCidade' => $item->cdCidade
            ];
        }

        $this->_helper->json($planilhaJSON);
    }
}
