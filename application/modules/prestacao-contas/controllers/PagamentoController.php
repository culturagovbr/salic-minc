<?php
class PrestacaoContas_PagamentoController extends MinC_Controller_Action_Abstract
{
    protected $idUsuario;

    public function init()
    {
        $PermissoesGrupo = [
            Autenticacao_Model_Grupos::PROPONENTE,
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
        $idpronac = $this->_request->getParam('idpronac');

        $this->view->idpronac = $idpronac;

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->obterItensAprovados($idpronac);

        $vlTotalComprovar = 0;
        $vlComprovado = 0;
        $vlAprovado = 0;
        foreach ($resposta as $item) {
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

    }

    public function planilhaPagamentoAction()
    {
        $idpronac = (int)$this->_request->getParam('idpronac');

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->obterItensAprovados($idpronac);

        $planilhaJSON = null;

        foreach($resposta as $item) {

            $produtoSlug = TratarString::criarSlug($item->Produto);
            $etapaSlug = TratarString::criarSlug($item->Etapa);
            $cidadeSlug = TratarString::criarSlug($item->Cidade);

            $planilhaJSON[$produtoSlug]['etapa'][$etapaSlug]['UF'][$item->Uf]['cidade'][$cidadeSlug]['itens'][] = [
                'item' => utf8_encode($item->Item),
                'varlorAprovado' => $item->vlAprovado,
                'varlorComprovado' => $item->vlComprovado,
                'comprovacaoValidada' => $item->ComprovacaoValidada,
                'idPlanilhaAprovacao' => $item->idPlanilhaAprovacao,
                'idPlanilhaItens' => $item->idPlanilhaItens,
            ];

            $planilhaJSON[$produtoSlug] += [
                'produto' => html_entity_decode(utf8_encode($item->Produto)),
                'cdProduto' => $item->cdProduto,
            ];

            $planilhaJSON[$produtoSlug]['etapa'][$etapaSlug] += [
                'etapa' => utf8_encode($item->Etapa),
                'cdEtapa' =>  $item->cdEtapa
            ];

            $planilhaJSON[$produtoSlug]['etapa'][$etapaSlug]['UF'][$item->Uf] += [
                'Uf' => $item->Uf,
                'cdUF' => $item->cdUF
            ];

            $planilhaJSON[$produtoSlug]['etapa'][$etapaSlug]['UF'][$item->Uf]['cidade'][$cidadeSlug] += [
                'cidade' => utf8_encode($item->Cidade),
                'cdCidade' => $item->cdCidade
            ];
        }

        $this->_helper->json($planilhaJSON);
    }

    public function planilhaDadosProjetoAction()
    {
        $idpronac = (int)$this->_request->getParam('idpronac');

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->planilhaAprovada($idpronac);

        $planilhaJSON = null;


        foreach($resposta as $item) {
            $vlComprovar = $item->vlAprovado - $item->vlComprovado;
            $vlTotalComprovar += $vlComprovar;

            $vlAprovado += $item->vlAprovado;
            $vlComprovado += $item->vlComprovado;

            $planilhaJSON = [
                'NomeProjeto' => html_entity_decode(utf8_encode($item->NomeProjeto)),
                'Pronac' => $item->Pronac,
                'vlAprovado' => $vlAprovado,
                'vlComprovado' => $vlComprovado,
                'vlComprovar' => $vlTotalComprovar
            ];
        }
        $this->_helper->json($planilhaJSON);
    }

    public function itemAction()
    {
        $idpronac = $this->_request->getParam('idpronac');
        $uf = $this->_request->getParam('uf');
        $idPlanilhaEtapa = $this->_request->getParam('etapa');
        $idMunicipio = $this->_request->getParam('cidade');
        $codigoProduto = $this->_request->getParam('produto');
        $idPlanilhaItem = $this->_request->getParam('idPlanilhaItens');

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $resposta = $planilhaAprovacaoModel->planilhaAprovada(
            $idpronac,
            $uf,
            $idPlanilhaEtapa,
            $codigoProduto,
            $idMunicipio,
            null,
            $idPlanilhaItem
        );

        $planilhaJSON = [
            'Etapa' => (utf8_encode($resposta[0]['Etapa'])),
            'Produto' => (utf8_encode($resposta[0]['Produto'])),
            'Item' => utf8_encode($resposta[0]['Item']),
            'vlAprovado' => $resposta[0]['vlAprovado'],
            'vlComprovado' => $resposta[0]['vlComprovado'],
            'uf' => $resposta[0]['uf'],
            'cidade' => utf8_encode($resposta[0]['cidade']),
        ];

        $this->_helper->json($planilhaJSON);
    }
}