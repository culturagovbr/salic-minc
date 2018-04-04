<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vinculo
 *
 * @author tisomar
 */
class Vinculo extends MinC_Db_Table_Abstract
{
    protected $_banco = "AGENTES";
    protected $_name = "tbVinculo";

    /**
     * Grava registro. Se seja passado um ID ele altera um registro existente
     * @param array $dados - array com dados referentes as colunas da tabela no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @return ID do registro inserido/alterado ou FALSE em caso de erro
     */
    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblVinculo = new Vinculo();

        //DECIDINDO SE SERA FEITA UM INSERT OU UPDATE
        if (isset($dados['idVinculo'])) {
            $tmpRsVinculo = $tmpTblVinculo->find($dados['idVinculo'])->current();
        } else {
            $tmpRsVinculo = $tmpTblVinculo->createRow();
        }
        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['idUsuarioResponsavel'])) {
            $tmpRsVinculo->idUsuarioResponsavel = $dados['idUsuarioResponsavel'];
        }
        if (isset($dados['idAgenteProponente'])) {
            $tmpRsVinculo->idAgenteProponente = $dados['idAgenteProponente'];
        }
        if (isset($dados['dtVinculo'])) {
            $tmpRsVinculo->dtVinculo = $dados['dtVinculo'];
        }
        if (isset($dados['dsEmailVinculo'])) {
            $tmpRsVinculo->dsEmailVinculo = $dados['dsEmailVinculo'];
        }
        if (isset($dados['sivinculo'])) {
            $tmpRsVinculo->sivinculo = $dados['sivinculo'];
        }
        if (isset($dados['tpVinculo'])) {
            $tmpRsVinculo->tpVinculo = $dados['tpVinculo'];
        }

        //echo "<pre>";
        //print_r($tmpRsVinculo);
        //SALVANDO O OBJETO CRIADO
        $id = $tmpRsVinculo->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    public function verificaPermissaoAcessoProposta($idPreProjeto)
    {
        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rs = $tblProposta->buscar(array("idPreProjeto = ? " => $idPreProjeto, "1=1 OR idEdital IS NULL OR idEdital > 0" => "?", "idUsuario =?" => $this->idUsuario));
        return $rs->count();
    }

    public function indexAction()
    {
        $arrBusca = array();
        $arrBusca['stEstado = ?'] = 1;
        $arrBusca['idUsuario = ?'] = $this->idUsuario;
        // Chama o SQL
        $tblPreProjeto = new Proposta_Model_Preprojeto();
        $rsPreProjeto = $tblPreProjeto->buscar($arrBusca, array("idAgente ASC"));

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
        $this->montaTela("manterpropostaincentivofiscal/index.phtml", array("acaoAlterar" => $this->_urlPadrao . "/manterpropostaincentivofiscal/editar",
            "acaoExcluir" => $this->_urlPadrao . "/manterpropostaincentivofiscal/excluir",
            "dados" => $rsPreProjeto));
    }

    public function declaracaonovapropostaAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $post = Zend_Registry::get('post');

        if ($post->mecanismo == 1) { //mecanismo == 1 (proposta por incentivo fiscal)
            $url = $this->_urlPadrao . "/manterpropostaincentivofiscal/buscaproponente";
        } else {
            $url = $this->_urlPadrao . "/manterpropostaedital/editalconfirmar";
        }

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
        $this->montaTela("manterpropostaincentivofiscal/declaracaonovaproposta.phtml", array("acao" => $url,
            "agente" => $post->propronente));
    }

    public function buscaproponenteAction()
    {
        //recupera parametros
        $post = Zend_Registry::get('post');

        //se tentar acessar o metodo direto via url
        /* if(empty($post->cnpjcpf))
          {
          $this->montaTela("manterpropostaincentivofiscal/declaracaonovaproposta.phtml");
          return;
          }

          $pontos = array("." , "-", "/");
          $cpfcnpj = str_replace($pontos, "", $post->cnpjcpf);

          $arrBusca = array();
          $arrBusca['a.CNPJCPF = ?']=$cpfcnpj; */
        if (empty($post->idAgente)) {
            $this->montaTela("manterpropostaincentivofiscal/declaracaonovaproposta.phtml");
            return;
        }
        //VERIFICA SE PROPONETE JA ESTA CADASTRADO
        $arrBusca = array();
        $arrBusca['a.idAgente = ?'] = $post->idAgente;
        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsProponente = $tblAgente->buscarAgenteENome($arrBusca)->current();

        if (count($rsProponente) > 0) {
            
            //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
            $this->montaTela("manterpropostaincentivofiscal/identificacaodaproposta.phtml", array("proponente" => $rsProponente,
                "acao" => $this->_urlPadrao . "/manterpropostaincentivofiscal/salvar"));
        } else {
            $this->_redirect("/agente/manteragentes/agentes");
        }
    }

    public function validaagenciaAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $get = Zend_Registry::get('get');
        $agencia = $get->agencia;

        if ($agencia > 0) {
            $tblProposta = new Proposta_Model_DbTable_PreProjeto();
            $agencia = $tblProposta->buscaragencia($agencia);
            if (count($agencia) > 0) {
                echo "";
            } else {
                echo "Ag&ecirc;ncia inv&aacute;lida";
            }
        } else {
            echo "Ag&ecirc;ncia inv&aacute;lida";
        }
    }

    /**
     * Metodo responsavel por gravar a proposta em banco (INSERT e UPDATE)
     * @param void
     * @return objeto
     */
    public function salvarAction()
    {
        $post = Zend_Registry::get("post");
        $idPreProjeto = $post->idPreProjeto;
        $acao = $post->acao;

        $dtInicio = null;
        $dtInicioTemp = explode("/", $post->dtInicioDeExecucao);
        $dtInicio = $dtInicioTemp[2] . "/" . $dtInicioTemp[1] . "/" . $dtInicioTemp[0] . date(" H:i:s");

        $dtFim = null;
        $dtFimTemp = explode("/", $post->dtFinalDeExecucao);
        $dtFim = $dtFimTemp[2] . "/" . $dtFimTemp[1] . "/" . $dtFimTemp[0] . date(" H:i:s");

        $dtAtoTombamento = null;
        if ($post->dtAtoTombamento) {
            $dtAtoTombamentoTemp = explode("/", $post->dtAtoTombamento);
            $dtAtoTombamento = $dtAtoTombamentoTemp[2] . "/" . $dtAtoTombamentoTemp[1] . "/" . $dtAtoTombamentoTemp[0] . date(" H:i:s");
        }

        $idAgente = $post->idAgente;
        $nomeProjeto = str_replace("'", "", $post->nomeProjeto);
        $nomeProjeto = str_replace("\"", "", $nomeProjeto);
        $resumoDoProjeto = substr($post->resumoDoProjeto, 0, 950);
        $stDataFixa = $post->stDataFixa;
        $stPlanoAnual = $post->stPlanoAnual;
        $agenciaBancaria = $post->agenciaBancaria;
        $propostaAudioVisual = $post->propostaAudioVisual;
        $dtInicioDeExecucao = $dtInicio;
        $dtFinalDeExecucao = $dtFim;
        $nrAtoTombamento = $post->nrAtoTombamento;
        $dtAtoTombamento = $dtAtoTombamento;
        $esferaTombamento = $post->esferaTombamento;
        $objetivos = $post->objetivos;
        $justificativa = $post->justificativa;
        $acessibilidade = $post->acessibilidade;
        $democratizacaoDeAcesso = $post->democratizacaoDeAcesso;
        $etapaDeTrabalho = $post->etapaDeTrabalho;
        $fichaTecnica = $post->fichaTecnica;
        $sinopse = $post->sinopse;
        $impactoAmbiental = $post->impactoAmbiental;
        $especificacaoTecnica = $post->especificacaoTecnica;
        $informacoes = $post->informacoes;

        $dados = array("idAgente" => $idAgente,
            "NomeProjeto" => $nomeProjeto,
            "Mecanismo" => 1, //seguindo sistema legado
            "AgenciaBancaria" => $agenciaBancaria,
            "AreaAbrangencia" => $propostaAudioVisual,
            "DtInicioDeExecucao" => $dtInicioDeExecucao,
            "DtFinalDeExecucao" => $dtFinalDeExecucao,
            "NrAtoTombamento" => $nrAtoTombamento,
            "DtAtoTombamento" => $dtAtoTombamento,
            "EsferaTombamento" => $esferaTombamento,
            "ResumoDoProjeto" => $resumoDoProjeto,
            "Objetivos" => $objetivos,
            "Justificativa" => $justificativa,
            "Acessibilidade" => $acessibilidade,
            "DemocratizacaoDeAcesso" => $democratizacaoDeAcesso,
            "EtapaDeTrabalho" => $etapaDeTrabalho,
            "FichaTecnica" => $fichaTecnica,
            "Sinopse" => $sinopse,
            "ImpactoAmbiental" => $impactoAmbiental,
            "EspecificacaoTecnica" => $especificacaoTecnica, //No legado o que esta sendo gravado aqui e OUTRAS INFORMACOES
            "EstrategiadeExecucao" => $informacoes, //No legado o que esta sendo gravado aqui e ESPECIFICAO TECNICA
            "dtAceite" => date("Y/m/d H:i:s"),
            //"DtArquivamento"        => "",
            "stEstado" => 1,
            "stDataFixa" => $stDataFixa,
            "stPlanoAnual" => $stPlanoAnual,
            "idUsuario" => $this->idUsuario,
            "stTipoDemanda" => "NA", //seguindo sistema legado
                //"idEdital"                => null
        );

        $dados['idPreProjeto'] = $idPreProjeto;

        if (!empty($idPreProjeto)) {
            $mesagem = "Altera&ccedil;&atilde;o realizada com sucesso!";
        } else {
            $mesagem = "Cadastro realizado com sucesso!";
        }

        //CONECTA COM BANCO SAC
        $db = new Conexao(Zend_Registry::get('DIR_CONFIG'), "conexao_sac");

        //instancia classe modelo
        $tblPreProjeto = new Proposta_Model_Preprojeto();

        //$db = Zend_Db_Table::getDefaultAdapter();
        //$db->beginTransaction();

        try {
            //persiste os dados do Pre Projeto
            $idPreProjeto = $tblPreProjeto->salvar($dados);

            if ($acao == "incluir") {
                //persiste os dados de Movimentacao
                $tblMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
                $dados = array("idProjeto" => $idPreProjeto,
                    "Movimentacao" => "95", //Status = Proposta com Proponente
                    "DtMovimentacao" => date("Y/m/d H:i:s"),
                    "stEstado" => "0",
                    "Usuario" => $this->idUsuario); //$this->view->usuario->usu_codigo;

                $tblMovimentacao->salvar($dados);
            }
            //$db->commit();
            parent::message($mesagem, "/manterpropostaincentivofiscal/editar?idPreProjeto=" . $idPreProjeto, "CONFIRM");
            //$this->_redirect("/manterpropostaincentivofiscal/editar?idPreProjeto=".$idPreProjeto);
            return;
        } catch (Zend_Exception $ex) {
            //$db->rollback();
            
            parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o!", "/manterpropostaincentivofiscal/index?idPreProjeto=" . $idPreProjeto, "ERROR");
        }
    }

    /**
     * Metodo responsavel por carregar a proposta na tela apos uma nova proposta ser inclusa
     * @param $idPreProjeto
     * @return objeto
     */
    public function carregaProposta($idPreProjeto)
    {
        $arrBusca = array();
        $arrBusca['idPreProjeto = ?'] = $idPreProjeto;

        // Chama o SQL
        $tblPreProjeto = new Proposta_Model_Preprojeto();
        $rsPreProjeto = $tblPreProjeto->buscar($arrBusca)->current();

        $arrBuscaProponete = array();
        $arrBuscaProponete['a.idAgente = ?'] = $rsPreProjeto->idAgente;

        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsProponente = $tblAgente->buscarAgenteENome($arrBuscaProponete)->current();

        
        $arrDados = array("proposta" => $rsPreProjeto,
            "proponente" => $rsProponente);
        return $arrDados;
        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
        $this->montaTela("manterpropostaincentivofiscal/identificacaodaproposta.phtml", array("acao" => $this->_urlPadrao . "/manterpropostaincentivofiscal/salvar",
            "proposta" => $rsPreProjeto,
            "proponente" => $rsProponente));
    }

    /*
      public function novapropostaAction()
      {
      $idagente = $_POST["idagente"];
      $NomeProjeto = $_POST["NomeProjeto"];
      $ResumoDoProjeto = $_POST["ResumoDoProjeto"];
      $stDataFixa = $_POST["stDataFixa"];
      $stPlanoAnual = $_POST["stPlanoAnual"];
      $AgenciaBancaria = $_POST["AgenciaBancaria"];
      $propostaaudiovisual = $_POST["propostaaudiovisual"];
      $DtInicioDeExecucao = $_POST["DtInicioDeExecucao"];
      $NrAtoTombamento = $_POST["NrAtoTombamento"];
      $DtFinalDeExecucao = $_POST["DtFinalDeExecucao"];
      $DtAtoTombamento = $_POST["DtAtoTombamento"];
      $EsferaTombamento = $_POST["EsferaTombamento"];
      $Objetivos = $_POST["Objetivos"];
      $Justificativa = $_POST["Justificativa"];
      $Acessibilidade = $_POST["Acessibilidade"];
      $DemocratizacaoDeAcesso = $_POST["DemocratizacaoDeAcesso"];
      $EtapaDeTrabalho = $_POST["EtapaDeTrabalho"];
      $FichaTecnica = $_POST["FichaTecnica"];
      $Sinopse = $_POST["Sinopse"];
      $ImpactoAmbiental = $_POST["ImpactoAmbiental"];
      $EspecificacaoTecnica = $_POST["EspecificacaoTecnica"];
      $informacoes = $_POST["informacoes"];

      $dados = array(
      'idAgente'                  => $idagente,
      'NomeProjeto'               => $NomeProjeto,
      'Mecanismo'                 => '2',
      'AgenciaBancaria'           => $AgenciaBancaria,
      'AreaAbrangencia'           => '0', //N?o sei de onde vem esse dado
      'DtInicioDeExecucao'        => $DtInicioDeExecucao,
      'DtFinalDeExecucao'         => $DtFinalDeExecucao,
      'Justificativa'             => $Justificativa,
      'NrAtoTombamento'           => $NrAtoTombamento,
      'DtAtoTombamento'           => $DtAtoTombamento,
      'EsferaTombamento'          => $EsferaTombamento,
      'ResumoDoProjeto'           => $ResumoDoProjeto,
      'Objetivos'                 => $Objetivos,
      'Acessibilidade'            => $Acessibilidade,
      'DemocratizacaoDeAcesso'    => $DemocratizacaoDeAcesso,
      'EtapaDeTrabalho'           => $EtapaDeTrabalho,
      'FichaTecnica'              => $FichaTecnica,
      'Sinopse'                   => $Sinopse,
      'ImpactoAmbiental'          => $ImpactoAmbiental,
      'EspecificacaoTecnica'      => $EspecificacaoTecnica,
      'EstrategiadeExecucao'      => $EspecificacaoTecnica,
      'dtAceite'                  => new Zend_Db_Expr('GETDATE()'),
      'dtArquivamento'            => '',
      'stEstado'                  => '1',
      'stDataFixa'                => $stDataFixa,
      'stPlanoAnual'              => $stPlanoAnual,
      'idUsuario'                 => 777, //Id do Agente que esta executando a acao
      'stTipoDemanda'             => 'NA',
      'idEdital'                  => '0');

      $DadosInseridos = ManterpropostaincentivofiscalDAO::inserirProposta($dados);

      if ($DadosInseridos)
      {
      parent::message("Dados inseridos com sucesso!", "manterpropostaincentivofiscal", "CONFIRM");
      }
      else
      {
      parent::message("Nao foi poss�vel inserir os dados!", "manterpropostaincentivofiscal", "ERRO");
      }

      }
     */

    /**
     * Metodo responsavel por carregar os dados da proposta para alteracao
     * @param void
     * @return objeto
     */
    public function editarAction()
    {
        //recupera parametros
        $get = Zend_Registry::get('get');
        $idPreProjeto = $get->idPreProjeto;

        if (!empty($idPreProjeto)) {
            /* $result = $this->verificaPermissaoAcessoProposta($_REQUEST['idPreProjeto']);
              if($result<1){
              parent::message("Voc? n?o tem permiss?o para acessar esta Proposta ou esta n?o � uma Proposta por Incentivo Fiscal.", "/principal", "ALERT");
              die;
              } */

            $arrBusca = array();
            $arrBusca['idPreProjeto = ?'] = $idPreProjeto;

            // Chama o SQL
            $tblPreProjeto = new Proposta_Model_Preprojeto();
            $rsPreProjeto = $tblPreProjeto->buscar($arrBusca)->current();

            $arrBuscaProponete = array();
            $arrBuscaProponete['a.idAgente = ?'] = $rsPreProjeto->idAgente;
            $tblAgente = new Agente_Model_DbTable_Agentes();
            $rsProponente = $tblAgente->buscarAgenteENome($arrBuscaProponete)->current();

            //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
            $this->montaTela("manterpropostaincentivofiscal/identificacaodaproposta.phtml", array("acao" => $this->_urlPadrao . "/manterpropostaincentivofiscal/salvar",
                "proposta" => $rsPreProjeto,
                "proponente" => $rsProponente));
        } else {

            //chama o metodo index
            $this->_forward("index", "manterpropostaincentivofiscal");
        }
    }

    /**
     * Metodo responsavel por inativar uma proposta gravada
     * @param void
     * @return objeto
     */
    public function excluirAction()
    {
        $get = Zend_Registry::get("get");
        $idPreProjeto = $get->idPreProjeto;

        //BUSCANDO REGISTRO A SER ALTERADO
        $tblPreProjeto = new Proposta_Model_Preprojeto();
        $rsPreProjeto = $tblPreProjeto->find($idPreProjeto)->current();
        //altera Estado da proposta
        $rsPreProjeto->stEstado = 0;

        if ($rsPreProjeto->save()) {
            parent::message("Exclus&atilde;o realizada com sucesso!", "/manterpropostaincentivofiscal/listar-propostas", "CONFIRM");
        } else {
            parent::message("N&atilde;o foi possivel realizar a opera&ccedil;&atilde;o!", "/manterpropostaincentivofiscal/listar-propostas", "ERROR");
        }
    }

    public function enviarPropostaAction()
    {
        //recupera parametros
        $get = Zend_Registry::get('get');
        $idPreProjeto = $get->idPreProjeto;

        $erro = "";
        $msg = "";

        if (!empty($idPreProjeto)) {
            $arrResultado = $this->validarEnvioPropostaAoMinc($idPreProjeto);
            
            //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
            $this->montaTela("manterpropostaincentivofiscal/enviarproposta.phtml", array("acao" => $this->_urlPadrao . "/manterpropostaincentivofiscal/salvar",
                "erro" => $arrResultado['erro'],
                "resultado" => $arrResultado));
        } else {
            parent::message("Necessario informar o numero da proposta.", "/manterpropostaincentivofiscal/index", "ERROR");
        }
    }

    public function validarEnvioPropostaAoMinc($idPreProjeto)
    {

        //BUSCA DADOS DO PROJETO
        $arrBusca = array();
        $arrBusca['idPreProjeto = ?'] = $idPreProjeto;
        $tblPreProjeto = new Proposta_Model_Preprojeto();
        $rsPreProjeto = $tblPreProjeto->buscar($arrBusca)->current();

        /* ======== VERIFICA TODAS AS INFORMACOES NECESSARIAS AO ENVIO DA PROPOSTA ======= */

        $arrResultado = array();

        $arrResultado['erro'] = false;

        //valida mes de envio da proposta
        /* if(date("m") == "01" || date("m") == "12"){
          $arrResultado['prazoenvioproposta']['erro'] = true;
          $arrResultado['prazoenvioproposta']['msg'] = "Conforme Art 5? da Instru�?o Normativa n? 1, de 5 de outubro de 2010, nenhuma proposta poder� ser enviada ao MinC nos meses de DEZEMBRO e JANEIRO!";
          return $arrResultado;
          } */

        /*         * ******* MOVIMENTACAO ******** */
        //VERIFICA SE A PROPOSTA ESTA COM O MINC
        $Movimentacao = new Proposta_Model_DbTable_TbMovimentacao();
        $rsMovimentacao = $Movimentacao->buscarStatusAtualProposta($idPreProjeto);

        if ($rsMovimentacao->Movimentacao != 95) {
            $arrResultado['erro'] = true;
            $arrResultado['movimentacao']['erro'] = false;
            $arrResultado['movimentacao']['msg'] = "A Proposta Cultural encontra-se no Minist&eacute;rio da Cultura";
        } else {
            /* $arrResultado['erro'] = true;
              $arrResultado['movimentacao']['erro'] = false;
              $arrResultado['movimentacao']['msg'] = "A Proposta Cultural encontra-se no Minist&eacute;rio da Cultura"; */
        }


        /*         * ******* DADOS DO PROPONENTE ******** */
        $tblProponente = new Proponente();
        //$rsProponente = $tblProponente->buscar(array("a.idAgente = ?"=>$rsPreProjeto->idAgente))->current();

        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsProponente = $tblAgente->buscarAgenteENome(array("a.idAgente = ?" => $rsPreProjeto->idAgente))->current();

        $regularidade = Regularidade::buscarSalic($rsProponente->CNPJCPF);

        $dadosEndereco = Agente_Model_EnderecoNacionalDAO::buscarEnderecoNacional($rsPreProjeto->idAgente);

        $dadosEmail = Email::buscar($rsPreProjeto->idAgente);

        $dadosDirigente = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, null, null, $rsPreProjeto->idAgente);
        //$dadosDirigente = ManterAgentes::buscaDirigentes($rsProponente->CNPJCPF);

        $tblLocaisRealizacao = new Proposta_Model_DbTable_Abrangencia();
        $dadosLocais = $tblLocaisRealizacao->buscar(array("a.idProjeto" => $idPreProjeto, "a.stAbrangencia" => 1));

        $tblPlanoDivulgacao = new PlanoDeDivulgacao();
        $dadosPlanoDivulgacao = $tblPlanoDivulgacao->buscar(array("idProjeto =?" => $idPreProjeto))->toArray();

        $tblPlanoDistribuicao = new PlanoDistribuicao();
        $dadosPlanoDistribuicao = $tblPlanoDistribuicao->buscar(array("a.idProjeto =?" => $idPreProjeto), array("idProduto ASC"))->toArray();

        if (count($rsProponente) > 0) {
            //REGULARIDADE DO PROPONENTE
            if (count($regularidade) > 0) {
                if ($regularidade[0]->Habilitado == "S") {
                    $arrResultado['regularidadeproponente']['erro'] = false;
                    $arrResultado['regularidadeproponente']['msg'] = "Proponente em situa&ccedil;&atilde;o REGULAR no Minist&eacute;rio da Cultura";
                } else {
                    $arrResultado['erro'] = true;
                    $arrResultado['regularidadeproponente']['erro'] = true;
                    $arrResultado['regularidadeproponente']['msg'] = "Proponente em situa&ccedil;&atilde;o IRREGULAR no Minist&eacute;rio da Cultura";
                }
            } else {
                $arrResultado['regularidadeproponente']['erro'] = false;
                $arrResultado['regularidadeproponente']['msg'] = "Proponente em situa&ccedil;&atilde;o REGULAR no Minist&eacute;rio da Cultura";
            }

            //E-MAIL
            $blnEmail = false;
            if (count($dadosEmail) > 0) {
                foreach ($dadosEmail as $email) {
                    if ($email->Status == 1) {
                        $blnEmail = true;
                    }
                }
                if ($blnEmail === false) {
                    $arrResultado['erro'] = true;
                    $arrResultado['email']['erro'] = true;
                    $arrResultado['email']['msg'] = "E-mail do proponente inexistente";
                } else {
                    $arrResultado['email']['erro'] = false;
                    $arrResultado['email']['msg'] = "E-mail do proponente";
                }
            } else {
                $arrResultado['erro'] = true;
                $arrResultado['email']['erro'] = true;
                $arrResultado['email']['msg'] = "E-mail do proponente inexistente";
            }

            //ENDERECO
            $blnEndCorrespondencia = false;
            if (count($dadosEndereco) > 0) {
                foreach ($dadosEndereco as $endereco) {
                    if ($endereco->Status == 1) {
                        $blnEndCorrespondencia = true;
                    }
                }
                if ($blnEndCorrespondencia === false) {
                    $arrResultado['erro'] = true;
                    $arrResultado['endereco']['erro'] = true;
                    $arrResultado['endereco']['msg'] = "Dados cadastrais do proponente inexistente ou n&atilde;o h&aacute; endere&ccedil;o para correspond&ecirc;ncia selecionado";
                } else {
                    $arrResultado['endereco']['erro'] = false;
                    $arrResultado['endereco']['msg'] = "Dados cadastrais do proponente";
                }
            } else {
                $arrResultado['erro'] = true;
                $arrResultado['endereco']['erro'] = true;
                $arrResultado['endereco']['msg'] = "Dados cadastrais do proponente inexistente ou n&atilde;o h&aacute; endere&ccedil;o para correspond&ecirc;ncia selecionado";
            }

            //NATUREZA
            if ($rsProponente->TipoPessoa == 1) {
                $tblNatureza = new Natureza();
                $dadosNatureza = $tblNatureza->buscar(array("idAgente = ?" => $rsPreProjeto->idAgente));

                if (count($dadosNatureza) > 0) {
                    $arrResultado['dirigente']['erro'] = false;
                    $arrResultado['dirigente']['msg'] = "Natureza do proponente";
                } else {
                    $arrResultado['erro'] = true;
                    $arrResultado['dirigente']['erro'] = true;
                    $arrResultado['dirigente']['msg'] = "Natureza do proponente";
                }
            }

            //DIRIGENTE
            if ($rsProponente->TipoPessoa == 1) {
                if (count($dadosDirigente) > 0) {
                    $arrResultado['dirigente']['erro'] = false;
                    $arrResultado['dirigente']['msg'] = "Cadastro de Dirigente";
                } else {
                    $arrResultado['erro'] = true;
                    $arrResultado['dirigente']['erro'] = true;
                    $arrResultado['dirigente']['msg'] = "Cadastro de Dirigente";
                }
            }

            //LOCAIS DE RALIZACAO
            if (count($dadosLocais) > 0) {
                $arrResultado['locaisrealizacao']['erro'] = false;
                $arrResultado['locaisrealizacao']['msg'] = "Local de realiza&ccedil;&atilde;o da proposta";
            } else {
                $arrResultado['erro'] = true;
                $arrResultado['locaisrealizacao']['erro'] = true;
                $arrResultado['locaisrealizacao']['msg'] = "O Local de realiza&ccedil;&atilde;o da proposta n&atilde;o foi preenchido";
            }

            //PLANO DE DIVULGACAO
            if (count($dadosPlanoDivulgacao) > 0) {
                $arrResultado['planodivulgacao']['erro'] = false;
                $arrResultado['planodivulgacao']['msg'] = "Plano B&aacute;sico de Divulga&ccedil;&atilde;o";
            } else {
                $arrResultado['erro'] = true;
                $arrResultado['planodivulgacao']['erro'] = true;
                $arrResultado['planodivulgacao']['msg'] = "O Plano B&aacute;sico de Divulga&ccedil;&atilde;o n&atilde;o foi preenchido";
            }

            //PLANO DE DISTRIBUICAO
            if (count($dadosPlanoDistribuicao) > 0) {
                $arrResultado['planodistribuicao']['erro'] = false;
                $arrResultado['planodistribuicao']['msg'] = "Plano Distribui&ccedil;&atilde;o de Produto";

                //PLANILHA POR PRODUTO
                //inicializando variaveis
                $arrProdutoPlanilhaOrcamentaria = array();
                $arrProdutoPlanilhaCustoAdmin = array();
                $arrBuscaPlanilhaOrcamentaria = array(); //para planilhas orcamentarias onde idProduto <> 0
                $arrBuscaPlanilhaCustoAdmin = array(); //para planilhas orcamentarias onde idProduto = 0
                $qtdeProdutoPrincial = 0;
                $valorProjeto = 0;
                //instancia classe modelo PlanilhaProposta
                $tblPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                foreach ($dadosPlanoDistribuicao as $produto) {
                    //=========== PLANILHA ORCAMENTARIA ===============
                    $idProduto = $produto['idProduto'];
                    $arrBuscaPlanilhaOrcamentaria['idProjeto = ?'] = $idPreProjeto;
                    $arrBuscaPlanilhaOrcamentaria['idProduto = ?'] = $idProduto;
                    //$arrBuscaPlanilhaOrcamentaria['idEtapa <> ?']=4;

                    $planilhaOrcamentaria = $tblPlanilhaProposta->buscar($arrBuscaPlanilhaOrcamentaria);
                    //$planilha = PlanilhaPropostaDAO::buscarPlanilhaPorProjetoProduto($idPreProjeto, $idProduto);

                    if (count($planilhaOrcamentaria) > 0) {
                        $arrProdutoPlanilhaOrcamentaria['CONTEM'][] = $idProduto;

                        //realiza calculo para encontrar valor do projeto
                        for ($i = 0; $i < sizeof($planilhaOrcamentaria); $i++) {
                            $valorProjeto += ($planilhaOrcamentaria[$i]->Quantidade * $planilhaOrcamentaria[$i]->Ocorrencia * $planilhaOrcamentaria[$i]->ValorUnitario);
                        }
                    } else {
                        $arrProdutoPlanilhaOrcamentaria['NAO_CONTEM'][] = $idProduto;
                    }

                    //=========== PRODUTO PRINCIPAL ==========
                    if ($produto['stPrincipal'] == 1) {
                        $qtdeProdutoPrincial++;
                    }
                }//fecha FOREACH de Plano Distribuicao

                if (!empty($arrProdutoPlanilhaOrcamentaria['NAO_CONTEM'])) {
                    $arrResultado['erro'] = true;
                    $arrResultado['planilhaproduto']['erro'] = true;
                    $arrResultado['planilhaproduto']['msg'] = "Existe produto cadastrado sem a respectiva planilha or&ccedil;ament&aacute;ria lan&ccedil;ada";
                }

                //=========== PLANILHA CUSTO ADMINISTRATIVO ==========
                $arrBuscaPlanilhaCustoAdmin['idProjeto = ?'] = $idPreProjeto;
                $arrBuscaPlanilhaCustoAdmin['idProduto = ?'] = 0; //planilha de custo admin. n&atilde;o tem produto
                $arrBuscaPlanilhaCustoAdmin['idEtapa = ?'] = 4; //etapa 4 = Custo/Adminitrativo

                $planilhaCustoAdmin = $tblPlanilhaProposta->buscar($arrBuscaPlanilhaCustoAdmin);
                $valorCustoAdmin = 0;
                if (count($planilhaCustoAdmin) > 0) {
                    $arrResultado['planilhacustoadmin']['erro'] = false;
                    $arrResultado['planilhacustoadmin']['msg'] = "Planilha de custos administrativos lan&ccedil;ada";

                    //realiza calculo para encontrar custo administrativo do projeto
                    for ($i = 0; $i < sizeof($planilhaCustoAdmin); $i++) {
                        $valorCustoAdmin += ($planilhaCustoAdmin[$i]->Quantidade * $planilhaCustoAdmin[$i]->Ocorrencia * $planilhaCustoAdmin[$i]->ValorUnitario);
                    }
                } else {
                    $arrResultado['erro'] = true;
                    $arrResultado['planilhacustoadmin']['erro'] = true;
                    $arrResultado['planilhacustoadmin']['msg'] = "A planilha de custos administrativos do projeto n&atilde;o est&aacute; lan&ccedil;ada";
                }

                //calcula percentual do custo administrativo
                if ($valorProjeto > 0) {
                    $percentual = $valorCustoAdmin / $valorProjeto * 100;
                } else {
                    $percentual = 100;
                }


                if ($percentual > 15) {
                    $arrResultado['erro'] = true;
                    $arrResultado['percentualcustoadmin']['erro'] = true;
                    $arrResultado['percentualcustoadmin']['msg'] = "Custo administrativo superior a 15% do valor total do projeto";
                } else {
                    $arrResultado['percentualcustoadmin']['erro'] = false;
                    $arrResultado['percentualcustoadmin']['msg'] = "Custo administrativo inferior a 15% do valor total do projeto";
                }

                if ($qtdeProdutoPrincial <= 0) {
                    $arrResultado['erro'] = true;
                    $arrResultado['produtoprincipal']['erro'] = true;
                    $arrResultado['produtoprincipal']['msg'] = "N&atilde;o h&aacute; produto principal selecionado na proposta";
                } elseif ($qtdeProdutoPrincial > 1) {
                    $arrResultado['erro'] = true;
                    $arrResultado['produtoprincipal']['erro'] = true;
                    $arrResultado['produtoprincipal']['msg'] = "S&oacute; poder&aacute; haver um produto principal em cada proposta, a sua est&aacute; com mais de um produto";
                } else {
                    $arrResultado['produtoprincipal']['erro'] = false;
                    $arrResultado['produtoprincipal']['msg'] = "Produto principal";
                }
            } else {
                $arrResultado['erro'] = true;
                $arrResultado['planodistribuicao']['erro'] = true;
                $arrResultado['planodistribuicao']['msg'] = "O Plano Distribui&ccedil;&atilde;o de Produto n&atilde;o foi preenchido";
            }
        } else {
            $arrResultado['erro'] = true;
            $arrResultado['proponente']['erro'] = true;
            $arrResultado['proponente']['msg'] = "Dados cadastrais do proponente inexistente ou n&atilde;o h&aacute; endere&ccedil;o para correspond&ecirc;ncia selecionado";
        }

        return $arrResultado;
    }

    public function confirmarEnvioPropostaAoMincAction()
    {
        //recupera parametros
        $get = Zend_Registry::get('get');
        $idPreProjeto = $get->idPreProjeto;
        $valida = $get->valida;
        $idTecnico = null;
        $rsTecnicos = array();

        if (isset($_REQUEST['edital'])) {
            $edital = "&edital=s";
        } else {
            $edital = "";
        }

        if (!empty($idPreProjeto) && $valida == "s") {
            $tblPreProjeto = new Proposta_Model_Preprojeto();
            $tblAvaliacao = new Proposta_Model_AnalisarPropostaDAO();

            //recupera dados do projeto
            $rsPreProjeto = $tblPreProjeto->find($idPreProjeto)->current();

            if ($rsPreProjeto->AreaAbrangencia == 0) {
                $idOrgaoSuperior = 251;
            } else {
                $idOrgaoSuperior = 160;
            }

            //verifica se a proposta ja foi recebida por um tecnico
            $avaliacao = $tblAvaliacao->verificarAvaliacao($idPreProjeto);

            //SE A PROPOSTA JA FOI AVALIADA POR UM TECNICO E O MESMO ESTIVER ATIVO, ATRIBUI A AVALIACAO A ELE
            if (count($avaliacao) > 0) {
                if ($avaliacao[0]->ConformidadeOK == 0 || $avaliacao[0]->ConformidadeOK == 1) {
                    //verifica se o tecnico esta habilitado
                    $arrBusca = array();
                    $arrBusca['sis_codigo = '] = 21;
                    $arrBusca['gru_codigo = '] = 92;
                    $arrBusca['usu_codigo = '] = $avaliacao[0]->idTecnico;
                    $analista = AdmissibilidadeDAO::buscarAnalistas($arrBusca);

                    if (count($analista) > 0) {
                        if ($analista[0]->uog_status == 1) {
                            $idTecnico = $avaliacao[0]->idTecnico;
                        } else {
                            $idTecnico = null;
                            //recupera todos os tecnicos do orgao para fazer o balanceamento
                            $rsTecnicos = $tblPreProjeto->recuperarTecnicosOrgao($idOrgaoSuperior);
                        }
                    } else {
                        $idTecnico = null;
                        //recupera todos os tecnicos do orgao para fazer o balanceamento
                        $rsTecnicos = $tblPreProjeto->recuperarTecnicosOrgao($idOrgaoSuperior);
                    }
                }
            } else {
                //recupera todos os tecnicos do orgao para fazer o balanceamento
                $rsTecnicos = $tblPreProjeto->recuperarTecnicosOrgao($idOrgaoSuperior);
            }

            //SE A PROPOSTA NUNCA FOI AVALIADA OU SE O TECNICO Q A AVALIOU ESTA DESABILITADO FAZ O BALANCEAMENTO
            if (count($rsTecnicos) > 0 && $idTecnico == null) {
                $arrTecnicosPropostas = array();

                foreach ($rsTecnicos as $tecnico) {
                    $rsAvaliacaoPorTecnico = $tblAvaliacao->recuperarQtdePropostaTecnicoOrgao($tecnico->uog_orgao, $tecnico->usu_codigo);
                    $arrTecnicosPropostas[$tecnico->usu_codigo] = $rsAvaliacaoPorTecnico[0]->qtdePropostas;
                }
                asort($arrTecnicosPropostas);

                //PEGA O ID DO TECNICO Q TEM MENOS PROPOSTAS
                $ct = 1;
                foreach ($arrTecnicosPropostas as $chave => $valor) {
                    if ($ct == 1) {
                        $idTecnico = $chave;
                        $ct++;
                    } else {
                        break;
                    }
                }
            }

            //INICIA PERSISTENCIA DOS DADOS
            if ($idTecnico) {

//                $db = Zend_Db_Table::getDefaultAdapter();
//                $db->beginTransaction();

                try {

                    //======== PERSXISTE DADOS DA MOVIMENTACAO ==========/
                    //atualiza status da ultima movimentacao
                    $tblAvaliacao->updateEstadoMovimentacao($idPreProjeto);

                    //PERSISTE DADOS DA MOVIMENTACAO
                    $tblMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
                    $dados = array("idProjeto" => $idPreProjeto,
                        "Movimentacao" => "96", //satus
                        "DtMovimentacao" => date("Y/m/d H:i:s"),
                        "stEstado" => "0", //esta informacao estava fixa trigger
                        "Usuario" => $this->idUsuario);

                    $tblMovimentacao->salvar($dados);

                    //======== PERSXISTE DADOS DA AVALIACAO ==========/
                    //atualiza status da ultima avaliacao
                    $tblAvaliacao->updateEstadoAvaliacao($idPreProjeto);

                    $dados = array();
                    $dados['idPreProjeto'] = $idPreProjeto;
                    $dados['idTecnico'] = $idTecnico; //$this->idUsuario;
                    $dados['dtEnvio'] = "'" . date("Y/m/d H:i:s") . "'";
                    $dados['dtAvaliacao'] = "'" . date("Y/m/d H:i:s") . "'";
                    $dados['avaliacao'] = "";
                    $dados['conformidade'] = 9;
                    $dados['estado'] = 0;

                    //PERSISTE DADOS DA AVALIACAO PROPOSTA
                    $tblAvaliacao->inserirAvaliacao($dados);

//                    $db->commit();

                    parent::message("A Proposta foi enviado com sucesso ao Minist&eacute;rio da Cultura!", "/manterpropostaincentivofiscal/enviar-proposta?idPreProjeto=" . $idPreProjeto . $edital, "CONFIRM");
                    die();
                } catch (Exception $e) {
//                    $db->rollback();
                    
                    parent::message("A Proposta n&atilde;o foi enviado ao Minist&eacute;rio da Cultura.", "/manterpropostaincentivofiscal/enviar-proposta?idPreProjeto=" . $idPreProjeto . $edital, "ERROR");
                    die();
                }
            } else { //fecha IF se encontrou tecnicos para enviar a proposta
                parent::message("A Proposta n&atilde;o foi enviado ao Minist&eacute;rio da Cultura.", "/manterpropostaincentivofiscal/enviar-proposta?idPreProjeto=" . $idPreProjeto . $edital, "ERROR");
                die();
            }
        } else {
            parent::message("A Proposta n&atilde;o foi enviado ao Minist&eacute;rio da Cultura.", "/manterpropostaincentivofiscal/enviar-proposta?idPreProjeto=" . $idPreProjeto . $edital, "ERROR");
        }
    }

    /**
     * M?todo responsavel por validar as datas do formulario
     * @param void
     * @return objeto
     */
    public function validaDatasAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        //recupera parametros
        $get = Zend_Registry::get('get');
        $dtInicio = $get->dtInicio;
        $dtFim = $get->dtFim;

        $bln = "true";
        $script = "";
        $mensagem = "";

        $objData = new Data();

        //VERIFICA SE DATA INICIO E MAIOR QUE DATA FINAL
        if (!empty($get->dtInicio) && !empty($get->dtFim) && strlen($get->dtInicio) == 10 && strlen($get->dtFim) == 10) {
            $dtTemp = explode("/", $get->dtInicio);
            $dtInicio = $dtTemp[2] . $dtTemp[1] . $dtTemp[0];

            $dtTemp = null;
            $dtTemp = explode("/", $get->dtFim);
            $dtFim = $dtTemp[2] . $dtTemp[1] . $dtTemp[0];

            if ($dtInicio > $dtFim) {
                $mensagem = "<br><font color='red'>Data de in&iacute;cio n&atilde;o pode ser maior que a data final</font>";
                $bln = "false";
            }
            if (!$objData->validarData($get->dtInicio)) {
                $mensagem = "<br><font color='red'>Data de in&iacute;cio inv&aacute;lida</font>";
                $bln = "false";
            }
            if (!$objData->validarData($get->dtFim)) {
                $mensagem = "<br><font color='red'>Data final inv&aacute;lida</font>";
                $bln = "false";
            }
        }

        //VERIFICA SE DATA INICIO E MAIOR QUE 90 DIAS DA DATA ATUAL
        if (!empty($get->dtInicio) && strlen($get->dtInicio) == 10) {
            $dtTemp = explode("/", $get->dtInicio);
            $dtInicio = $dtTemp[2] . $dtTemp[1] . $dtTemp[0];

            $diffEmDias = $objData->CompararDatas(date("Ymd"), $dtInicio);
            if ($diffEmDias < 0 || $diffEmDias < 90) {
                $mensagem = "<br><font color='red'>A data inicial de realiza&ccedil;&atilde;o dever&aacute; ser no m&iacute;nimo 90 dias ap&oacute;s a data atual.</font>";
                $bln = "false";
            }

            if (!$objData->validarData($get->dtInicio)) {
                $mensagem = "<br><font color='red'>Data de in&iacute;cio inv&aacute;lida</font>";
                $bln = "false";
            }
            //verifica se a data inicio esta entre 01 de Fevereiro e 30 de Novembro
            //if($dtInicio >= date("Y")."0201" && $dtInicio <= date("Y")."1130"){
        }

        //VERIFICA SE DATA DO ATO E VALIDA, CASO ELA TENHA SIDO INFORMADA
        if (!empty($get->dtAto) && strlen($get->dtAto) == 10) {
            if (!$objData->validarData(trim($get->dtAto))) {
                $mensagem = "<br><font color='red'>Data tombamento inv&aacute;lida</font>";
                $bln = "false";
            }
        }



        $script = "\$('#blnDatasValidas').val(" . $bln . ");\n";
        $this->montaTela("manterpropostaincentivofiscal/mensagem.phtml", array("mensagem" => $mensagem,
            "script" => $script));
    }

    public function listarPropostasAction()
    {


        //BUSCA idAgente DO USUARIO LOGADO, que � o Responsavel ou o Proponente
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica�?o
        $usu_identificacao = isset($auth->getIdentity()->usu_identificacao) ? $auth->getIdentity()->usu_identificacao : $auth->getIdentity()->Cpf;
        $agentes = new Agente_Model_DbTable_Agentes();

        $idAgenteProponenteRs = $agentes->buscar(array("CNPJCPF = ?" => $usu_identificacao))->current();
        $idAgente = $idAgenteProponenteRs->idAgente;

        /*         * *************************************************************** */

        $this->montaTela("manterpropostaincentivofiscal/listarproposta.phtml", array("idUsuario" => $this->idUsuario,
            "idResponsavel" => $idAgente));
    }

    public function buscarProponentesVinculadosAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $get = Zend_Registry::get('get');
        $idResponsavel = $get->idResponsavel;

        $arrBusca = array();
        //$arrBusca['idUsuarioResponsavel = ?'] = $idResponsavel;
        $arrBusca['idUsuarioResponsavel = ?'] = $this->idUsuario;
        $arrBusca['sivinculo = ?'] = 1;
        
        $tblVinculo = new Vinculo();
        $rsVinculo = $tblVinculo->buscar($arrBusca);
        
        $options = "";
        $optionsTemp = "";
        $idsProponente = 0;
        $tblAgente = new Agente_Model_DbTable_Agentes();

        //==== MONTA COMBO COM TODOS OS PROPONENTES //
        foreach ($rsVinculo as $cahve => $valor) {
            //Descobrindo os dados do Agente/Proponente
            //$rsAgente = $tblAgente->buscar(array("idAgente = ? "=>$valor->idAgenteProponente))->current();
            $rsProponente = $tblAgente->buscarAgenteENome(array("a.idAgente = ? " => $valor->idAgenteProponente))->current();

            $cpfCnpj = $rsProponente->CNPJCPF;
            if (strlen($cpfCnpj) > 11) {
                $cpfCnpj = aplicaMascara($cpfCnpj, "99.999.999/9999-99");
            } else {
                $cpfCnpj = aplicaMascara($cpfCnpj, "999.999.999-99");
            }
            $optionsTemp .= "<option value='" . $rsProponente->idAgente . "'>" . $cpfCnpj . " - " . utf8_decode(htmlentities($rsProponente->Descricao)) . "</option>";

            $idsProponente = $rsProponente->idAgente . ",";
        }
        //==== FIM MONTA COMBO COM TODOS OS PROPONENTES //
        //==== INCLUI NA COMBO O USUARIO LOGADO //
        $rsProponente = $tblAgente->buscarAgenteENome(array("a.idAgente = ? " => $idResponsavel))->current();
        $cpfCnpj = $rsProponente->CNPJCPF;
        if (strlen($cpfCnpj) > 11) {
            $cpfCnpj = aplicaMascara($cpfCnpj, "99.999.999/9999-99");
        } else {
            $cpfCnpj = aplicaMascara($cpfCnpj, "999.999.999-99");
        }
        if (isset($rsProponente->idAgente)) {
            $optionsTemp .= "<option value='" . $rsProponente->idAgente . "'>" . $cpfCnpj . " - " . utf8_decode(htmlentities($rsProponente->Descricao)) . "</option>";
        }

        //retira ultima virgula
        if (!empty($idsProponente)) {
            $idsProponente = substr($idsProponente, 0, strlen($idsProponente) - 1);
        } else {
            $idsProponente .="," . $rsProponente->idAgente;
        }
        //==== FIM INCLUI NA COMBO O USUARIO LOGADO //
        if (isset($rsProponente->idAgente)) {
            $options .= "<option value='" . $idsProponente . "' selected>- TODOS -</option>";
        } else {
            $options .= "<option value='' selected>- Nenhum Proponente encontrado -</option>";
        }
        $options .= $optionsTemp;

        echo $options;
    }

    public function localizarPropostaAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $get = Zend_Registry::get('get');
        $idAgente = $get->idAgente;
        $mecanismo = $get->mecanismo;

        $arrIdsAgentes = explode(",", $idAgente);

        if (!empty($idAgente) && $idAgente != "null") {
            if (!empty($mecanismo)) {
                $arrPropostas = array();
                //PROPOSTAS POR INCENTIVO FISCAL
                $arrBusca = array();
                $arrBusca['a.stEstado = ?'] = 1;

                if ($mecanismo == "1") {
                    $arrBusca['a.idEdital IS NULL OR a.idEdital = 0'] = "?";
                } else {
                    $arrBusca['a.idEdital IS NOT NULL AND a.idEdital <> 0'] = "?";
                    $arrBusca["a.stTipoDemanda <> ?"] = "NA"; // Regra inserida por Danilo Lisboa pois ao exibir a proposta no proximo passo esse filtro eh usado.
                }

                if (count($arrIdsAgentes) > 1) {
                    $arrBusca['a.idAgente IN (?) '] = $arrIdsAgentes;
                } else {
                    $arrBusca['a.idAgente = ?'] = $idAgente;
                }
                
                $tblPreProjeto = new Proposta_Model_Preprojeto();
                //$rsPreProjeto = $tblPreProjeto->buscar($arrBusca,  array("idAgente ASC"));
                $rsPreProjeto = $tblPreProjeto->buscaCompleta($arrBusca, array("a.idAgente ASC"));

                //$this->view->propostas = $rsPreProjeto;

                $this->montaTela("manterpropostaincentivofiscal/localizarproposta.phtml", array("propostas" => $rsPreProjeto));
            } else {
                //parent::message("Informe um mecaninsmo.", "manterpropostaincentivofiscal/listar-propostas", "ALERT");
                echo "<br><br><div class='centro'><font color='red'>Informe um Mecaninsmo</font></div><br>";
            }
        } else {
            echo "<br><br><div class='centro'><font color='red'>Informe um Proponente</font></div><br>";
        }
    }
}
