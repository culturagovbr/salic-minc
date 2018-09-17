<?php

class PrestacaoContas_ComprovantePagamentoController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('get', 'json')
            ->addActionContext('put', 'json')
            ->addActionContext('index', 'json')
            ->addActionContext('post', 'json')
            ->initContext('json');
    }

    public function headAction(){}

    public function indexAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        $idPlanilhaItem = $this->_request->getParam("idPlanilhaItem");
        $codigoProduto = $this->getRequest()->getParam('produto');
        $stItemAvaliado = $this->getRequest()->getParam('stItemAvaliado');
        $UF = $this->getRequest()->getParam('uf');
        $idmunicipio = $this->getRequest()->getParam('idmunicipio');
        $etapa = $this->getRequest()->getParam('etapa');

        $tipo = $this->getRequest()->getParam('tipo');

        $projetoModel = new Projetos();
        $projeto = $projetoModel->find($idpronac)->current();

        $dtInicioExecucao = new DateTime($projeto->DtInicioExecucao);
        $dtFimExecucao = new DateTime($projeto->DtFimExecucao);

        $vwComprovacoes = new PrestacaoContas_Model_vwComprovacaoFinanceiraProjetoPorItemOrcamentario();

        if ($tipo == 'internacional') {
            $comprovantes = $vwComprovacoes->comprovacoesInternacionais(
                $idPronac,
                $idPlanilhaItem,
                $stItemAvaliado,
                $codigoProduto,
                null,
                $etapa
            );
        } else {
            $comprovantes = $vwComprovacoes->comprovacoesNacionais(
                $idPronac,
                $idPlanilhaItem,
                $stItemAvaliado,
                $codigoProduto,
                null,
                $etapa
            );
        }

        $data = [];

        foreach($comprovantes->toArray() as $key => $value) {
            $data[] =  array_map('utf8_encode', $value);
        }

        $dataAux = [];
        foreach($data as $value) {
            $key =  $value['idComprovantePagamento'];
            $dataAux[$key] = $value;
            $dataAux[$key]['tipo'] = $value['tipo'];
            $dataAux[$key]['numero'] = $value['numero'];
            $dataAux[$key]['serie'] = $value['serie'];
            $dataAux[$key]['forma'] = $value['forma'];
            $dataAux[$key]['valor'] = $value['vlComprovacao'];
            $dataAux[$key]['justificativa'] = $value['dsJustificativaProponente'];
            $dataAux[$key]['dataPagamento'] = $value['dtPagamento'];
            $dataAux[$key]['dataEmissao'] = $value['dataEmissao'];
            $dataAux[$key]['numeroDocumento'] = $value['numeroDocumento'];
            $dataAux[$key]['nrDocumentoDePagamento'] = $value['nrDocumentoDePagamento'];
            $dataAux[$key]['fornecedor']['CNPJCPF'] = $value['CNPJCPF'];
            $dataAux[$key]['fornecedor']['nome'] = $value['nmFornecedor'];
            $dataAux[$key]['fornecedor']['endereco'] = $value['endereco'];
            $dataAux[$key]['fornecedor']['id'] = $value['id'];
            $dataAux[$key]['fornecedor']['pais'] = $value['pais'];
            $dataAux[$key]['fornecedor']['nacionalidade'] = $value['pais'];
            $dataAux[$key]['arquivo']['nome'] = $value['nmArquivo'];
            $dataAux[$key]['arquivo']['id'] = $value['idArquivo'];
            $dataAux[$key]['projeto']['dataInicioExecucao'] = $dtInicioExecucao;
            $dataAux[$key]['projeto']['dataFimExecucao'] = $dtFimExecucao;
        }
        $this->view->assign('data', $dataAux);
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction()
    {
        die('teste');
    }

    public function postAction()
    {
        $idPronac = $this->getRequest()->getParam('idpronac');
        $observacao = utf8_decode($this->getRequest()->getParam('observacao'));
        $situacao = $this->getRequest()->getParam('situacao');
        $idComprovantePagamento = $this->getRequest()->getParam('idcomprovantepagamento');

        if (!$idPronac) {
            throw new Exception('Falta pronac');
        }

        if (!$observacao && $situacao == 3) {
            throw new Exception('Falta observacao');
        }

        if (!$idComprovantePagamento) {
            throw new Exception('Falta Comprovante Pagamento');
        }

        $tblComprovantePag = new ComprovantePagamentoxPlanilhaAprovacao();
        $rsComprovantePag = $tblComprovantePag
            ->buscar( [ 'idComprovantePagamento = ?' => $idComprovantePagamento] )
            ->current();

        $rsComprovantePag->dtValidacao = date('Y/m/d H:i:s');
        $rsComprovantePag->dsJustificativa = $observacao;
        $rsComprovantePag->stItemAvaliado = $situacao;

        try {
            $rsComprovantePag->save();
            $this->view->assign('data',['message' => 'criado!']);
            $this->getResponse()->setHttpResponseCode(200);
        } catch (Exception $e) {
            $tblComprovantePag->getAdapter()->rollBack();
            $this->view->assign('data',['message' => $e->getMessage()]);
        }
    }

    public function putAction()
    {
         $this->getResponse()->setHttpResponseCode(400);
    }

    public function deleteAction()
    {}
}
