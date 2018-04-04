<?php

class Diligencia_GerenciarController extends MinC_Controller_Action_Abstract 
{
    public function init()
    {
        parent::init();
    }

    public function responderAction()
    {
        $idPronac = $this->getRequest()->getParam('idpronac');
        $this->verificarPermissaoAcesso(false, true, false);
        $this->dadosProjeto();

        $this->view->idpronac   = $this->getRequest()->getParam('idpronac');

        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $planilhaItemModel = new PlanilhaItem();

        $resposta   = $planilhaAprovacaoModel->buscarItensPagamento($this->view->idpronac); //Alysson - Altera��o da Query para n�o mostrar os itens excluidos

        $arrayA =   array();
        $arrayP =   array();

        if (is_object($resposta)) {
            foreach ($resposta as $val) {
                $modalidade = '';
                if ($val->idCotacao != '') {
                    $modalidade = 'Cota&ccedil;&atilde;o';
                    $idmod      =   'cot'.$val->idCotacao.'_'.$val->idFornecedorCotacao;
                }
                if ($val->idDispensaLicitacao != '') {
                    $modalidade = 'Dispensa';
                    $idmod      =   'dis'.$val->idDispensaLicitacao;
                }
                if ($val->idLicitacao != '') {
                    $modalidade =   'Licita&ccedil;&atilde;o';
                    $idmod      =   'lic'.$val->idLicitacao;
                }
                if ($val->idContrato != '') {
                    if ($modalidade != '') {
                        $modalidade .=   ' /';
                    }
                    $modalidade .=   ' Contrato';
                    $idmod      =   'con'.$val->idContrato;
                }
                if ($modalidade == '') {
                    $modalidade =   '-';
                    $idmod      =   'sem';
                }

                $itemComprovacao = $planilhaItemModel->pesquisar($val->idPlanilhaAprovacao);

                if ($val->tpCusto == 'A') {
                    $arrayA[$val->descEtapa][$val->uf.' '.$val->cidade][$val->idPlanilhaAprovacao] = array(
                        $val->descItem, $val->Total, $val->tpDocumento, $itemComprovacao->vlComprovado, $modalidade, $idmod
                    );
                }
                if ($val->tpCusto == 'P') {
                    $arrayP[$val->Descricao][$val->descEtapa][$val->uf.' '.$val->cidade][$val->idPlanilhaAprovacao] = array(
                        $val->descItem, $val->Total, $val->tpDocumento, $itemComprovacao->vlComprovado, $modalidade, $idmod
                    );
                }
            }
        }

        $this->view->incFiscaisA   = array('Administra&ccedil;&atilde;o do Projeto' =>$arrayA);
        $this->view->incFiscaisP   = array('Custo por Produto' =>$arrayP);

    }

    private function dadosProjeto()
    {

        /* =============================================================================== */
        /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
        /* =============================================================================== */
        $this->verificarPermissaoAcesso(false, true, false);
        $idpronac   = $this->getRequest()->getParam('idpronac');

        $projetosDAO    = new Projetos();
        $resposta       = $projetosDAO->buscar(array('IdPRONAC = ? '=>"{$idpronac}"));

        $this->view->pronac         = $resposta[0]->AnoProjeto.$resposta[0]->Sequencial;
        $this->view->nomeProjeto    = $resposta[0]->NomeProjeto;
    }

}
