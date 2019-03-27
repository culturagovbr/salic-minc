<?php

class Projeto_OrcamentoController extends Projeto_GenericController
{
    private $idPronac = 0;

    public function init()
    {
        parent::init();

        $this->idPronac = $this->_request->getParam("idPronac");
        if (strlen($this->idPronac) > 7) {
            $this->idPronac = Seguranca::dencrypt($this->idPronac);
        }

        if (empty($this->idPronac)) {
            throw new Exception("idPronac n&atilde;o informado");
        }

        $this->verificarPermissaoAcesso(false, true, false);

        $this->view->idPronac = $this->idPronac;
        $this->view->idPronacHash = Seguranca::encrypt($this->idPronac);

        $this->view->urlMenu = [
            'module' => 'projeto',
            'controller' => 'menu',
            'action' => 'obter-menu-ajax',
            'idPronac' => $this->view->idPronacHash
        ];

    }

    public function indexAction()
    {
    }

    public function obterPlanilhaHomologadaAjaxAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            $spRenderizarPlanilha = new Projeto_Model_DbTable_SpRenderizarPlanilhas();
            $planilha = $spRenderizarPlanilha->obterPlanilhaPorTipo(
                $this->idPronac,
                Projeto_Model_DbTable_SpRenderizarPlanilhas::TIPO_PLANILHA_HOMOLOGADA
            );

            if (empty($planilha)) {
                throw new Exception("Nenhuma planilha encontrada... ;(");
            }

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $planilha));
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(412);
            $this->_helper->json(array('data' => [], 'success' => 'false', 'msg' => $e->getMessage()));

        }
    }

    public function obterPlanilhaReadequadaAjaxAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            $spRenderizarPlanilha = new Projeto_Model_DbTable_SpRenderizarPlanilhas();
            $planilha = $spRenderizarPlanilha->obterPlanilhaPorTipo(
                $this->idPronac,
                Projeto_Model_DbTable_SpRenderizarPlanilhas::TIPO_PLANILHA_READEQUADA
            );

            if (empty($planilha)) {
                throw new Exception("Nenhuma planilha encontrada... ;(");
            }

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $planilha));
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(412);
            $this->_helper->json(array('data' => [], 'success' => 'false', 'msg' => $e->getMessage()));

        }
    }

    public function obterPlanilhaPropostaAdequadaAjaxAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            $tbProjetos = new Projeto_Model_DbTable_Projetos();
            $projeto = $tbProjetos->findBy(['idPronac = ?' => $this->idPronac]);

            $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
            $planilha = $preProjetoMapper->obterPlanilhaAdequacao($projeto['idProjeto'], $this->idPronac);

            if (empty($planilha)) {
                throw new Exception("Nenhuma planilha encontrada... ;(");
            }

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $planilha));
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(412);
            $this->_helper->json(array('data' => [], 'success' => 'false', 'msg' => $e->getMessage()));

        }
    }

    public function obterPlanilhaPropostaOriginalAjaxAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            $tbProjetos = new Projeto_Model_DbTable_Projetos();
            $projeto = $tbProjetos->findBy(['idPronac = ?' => $this->idPronac]);
            $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
            $planilha = $preProjetoMapper->obterPlanilhaOriginal($projeto['idProjeto']);

            if (empty($planilha)) {
                throw new Exception("Nenhuma planilha encontrada... ;(");
            }

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $planilha));
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(412);
            $this->_helper->json(array('data' => [], 'success' => 'false', 'msg' => $e->getMessage()));

        }
    }
}
