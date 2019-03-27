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
        $uf = $this->getRequest()->getParam('uf');
        $idUf = $this->getRequest()->getParam('idUf');
        $idMunicipio = $this->getRequest()->getParam('idmunicipio');
        $etapa = $this->getRequest()->getParam('etapa');

        $tipo = $this->getRequest()->getParam('tipo');

        $projetoModel = new Projetos();
        $projeto = $projetoModel->find($idPronac)->current();

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
                $etapa,
                $idUf,
                $idMunicipio
            );
        } else {
            $comprovantes = $vwComprovacoes->comprovacoesNacionais(
                $idPronac,
                $idPlanilhaItem,
                $stItemAvaliado,
                $codigoProduto,
                null,
                $etapa,
                $idUf,
                $idMunicipio
            );
        }

        $data = [];

        foreach($comprovantes->toArray() as $key => $value) {
            $data[] =  array_map('utf8_encode', $value);
        }

        $dataAux = [];
        foreach($data as $chave => $value) {
            $key =  $value['idComprovantePagamento'];
            $dataAux[$chave] = $value;
            $dataAux[$chave]['tipo'] = $value['tipo'];
            $dataAux[$chave]['numero'] = $value['numero'];
            $dataAux[$chave]['serie'] = $value['serie'];
            $dataAux[$chave]['forma'] = $value['forma'];
            $dataAux[$chave]['valor'] = $value['vlComprovacao'];
            $dataAux[$chave]['justificativa'] = $value['dsJustificativaProponente'];
            $dataAux[$chave]['dataPagamento'] = $value['dtPagamento'];
            $dataAux[$chave]['dataEmissao'] = $value['dataEmissao'];
            $dataAux[$chave]['numeroDocumento'] = $value['numeroDocumento'];
            $dataAux[$chave]['nrDocumentoDePagamento'] = $value['nrDocumentoDePagamento'];
            $dataAux[$chave]['fornecedor']['CNPJCPF'] = $value['CNPJCPF'];
            $dataAux[$chave]['fornecedor']['nome'] = $value['nmFornecedor'];
            $dataAux[$chave]['fornecedor']['endereco'] = $value['endereco'];
            $dataAux[$chave]['fornecedor']['id'] = $value['id'];
            $dataAux[$chave]['fornecedor']['pais'] = $value['pais'];
            $dataAux[$chave]['fornecedor']['nacionalidade'] = $value['pais'];
            $dataAux[$chave]['arquivo']['nome'] = $value['nmArquivo'];
            $dataAux[$chave]['arquivo']['id'] = $value['idArquivo'];
            $dataAux[$chave]['projeto']['dataInicioExecucao'] = $dtInicioExecucao;
            $dataAux[$chave]['projeto']['dataFimExecucao'] = $dtFimExecucao;
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
