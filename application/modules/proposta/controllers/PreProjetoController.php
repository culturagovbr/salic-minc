<?php
class Proposta_PreProjetoController extends Proposta_GenericController
{
    private $blnPossuiDiligencias = 0;

    public function init()
    {
        parent::init();

        if (!empty($this->idPreProjeto)) {
            $this->view->idPreProjeto = $this->idPreProjeto;

            $this->verificarPermissaoAcesso(true, false, false);

            //VERIFICA SE A PROPOSTA TEM DILIGENCIAS
            $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $rsDiligencias = $PreProjeto->listarDiligenciasPreProjeto(array('pre.idpreprojeto = ?' => $this->idPreProjeto));
            $this->view->blnPossuiDiligencias = $rsDiligencias->count();

            $this->view->acao = $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/salvar";
        }

        // Busca na tabela apoio ExecucaoImediata
        $tableVerificacao = new Proposta_Model_DbTable_Verificacao();
        $listaExecucaoImediata = $tableVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idTipo' => 23), array('idVerificacao'));
        $this->view->listaExecucaoImediata = $listaExecucaoImediata;
    }

    public function verificaPermissaoAcessoProposta($idPreProjeto)
    {
        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rs = $tblProposta->buscar(array("idPreProjeto = ? " => $idPreProjeto, "1=1 OR idEdital IS NULL OR idEdital > 0" => "?", "idUsuario =?" => $this->idResponsavel));
        return $rs->count();
    }

    public function indexAction()
    {
        $idAgente = $this->getRequest()->getParam('idagente');
        $stEstado = $this->getRequest()->getParam('stestado');
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');
        $order = ($order[0]['dir'] != 1) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : array("idpreprojeto DESC");

        $idAgente = ((int)$idAgente == 0) ? $this->idAgente : (int)$idAgente;

        if (empty($idAgente)) {
            $this->_helper->json(array(
                "data" => 0,
                'recordsTotal' => 0,
                'draw' => 0,
                'recordsFiltered' => 0));
        }

        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();

        $rsPreProjeto = $tblPreProjeto->listar(
            $this->idAgente,
            $this->idResponsavel,
            $idAgente,
            array(),
            $order,
            $start,
            $length,
            $search,
            $stEstado
        );

        $Movimentacao = new Proposta_Model_DbTable_TbMovimentacao();

        $recordsTotal = 0;
        $recordsFiltered = 0;
        $aux = array();
        if (!empty($rsPreProjeto)) {
            foreach ($rsPreProjeto as $key => $proposta) {
                $proposta->nomeproponente = utf8_encode($proposta->nomeproponente);
                $proposta->nomeprojeto = utf8_encode($proposta->nomeprojeto);
                $proposta->situacao = utf8_encode($proposta->situacao);
                $rsStatusAtual = $Movimentacao->buscarMovimentacaoProposta($proposta->idpreprojeto);
                $proposta->situacao = isset($rsStatusAtual['MovimentacaoNome']) ? utf8_encode($rsStatusAtual['MovimentacaoNome']) : '';

                $aux[$key] = $proposta;
            }
            $recordsFiltered = $tblPreProjeto->propostasTotal($this->idAgente, $this->idResponsavel, $idAgente, array(), null, null, null, $search);
            $recordsTotal = $tblPreProjeto->propostasTotal($this->idAgente, $this->idResponsavel, $idAgente);
        }

        $this->_helper->json(array(
            "data" => !empty($aux) ? $aux : 0,
            'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0));
    }

    public function updateAction()
    {
        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');
        $stEstado = $this->getRequest()->getParam('stEstado');
        $DtArquivamento = $this->getRequest()->getParam('DtArquivamento');

        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $rsPreProjeto = $tblPreProjeto->find($idPreProjeto)->current();
        
        if ($DtArquivamento) {
            $rsPreProjeto->DtArquivamento = $DtArquivamento;
        }

        $rsPreProjeto->stEstado = $stEstado;

        try {
            $rsPreProjeto->save();
            $success = true;
            $message = "Opera&ccedil;&atilde;o realizada com sucesso!";
        } catch (Exception $e) {
            $message = "Erro ao realizar opera&ccedil;&atilde;o!";
            $success = false;
        }

        $this->_helper->json(
            [
                'data' => null,
                'success' => $success,
                'message' => $message
            ]
        );
    }
}
