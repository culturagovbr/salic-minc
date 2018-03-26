<?php

class Proposta_VisualizarPlanoDistribuicaoController extends Proposta_GenericController
{
    public function init()
    {
        parent::init();
    }

    public function visualizarAction()
    {
        $this->_helper->layout->disableLayout();

        $arrBusca = array();
        $arrBusca['idprojeto'] = $this->idPreProjeto;

        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $rsAbrangencia = $tblAbrangencia->buscar($arrBusca);
        $this->view->abrangencias = $rsAbrangencia;

        $tblPlanoDistribuicao = new PlanoDistribuicao();

        $rsPlanoDistribuicao = $tblPlanoDistribuicao->buscar(
            array("a.idprojeto = ?" => $this->idPreProjeto, "a.stplanodistribuicaoproduto = ?" => 1),
            array("idplanodistribuicao DESC")
        );

        $this->view->planosDistribuicao=$rsPlanoDistribuicao;

        $this->view->idPreProjeto = $this->idPreProjeto;
        $this->abrangencias = $rsAbrangencia;
    }

    public function detalharAction()
    {
        $dados = $this->getRequest()->getParams();
        $detalhamento = new Proposta_Model_DbTable_TbDetalhamentoPlanoDistribuicaoProduto();
        $dados = $detalhamento->listarPorMunicicipioUF($dados);

        $this->_helper->json(array('data' => $dados->toArray(), 'success' => 'true'));
    }
}
