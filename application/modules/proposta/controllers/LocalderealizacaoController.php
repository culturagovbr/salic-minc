<?php

/**
 * LocalDeRealizacaoController
 * @author Equipe RUP - Politec
 * @author wouerner <wouerner@gmail.com>
 * @since 15/12/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @copyright ? 2010 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class Proposta_LocalderealizacaoController extends Proposta_GenericController
{

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::init();

        //*******************************************
        //VALIDA ITENS DO MENU (Documento pendentes)
        //*******************************************
//        $model = new Proposta_Model_DbTable_DocumentosExigidos();
//        //$this->view->documentosPendentes = $model->buscarDocumentoPendente($get->idPreProjeto);
//        $this->view->documentosPendentes = $model->buscarDocumentoPendente($this->idPreProjeto);
//
//        if (!empty($this->view->documentosPendentes)) {
//            $verificarmenu = 1;
//            $this->view->verificarmenu = $verificarmenu;
//        } else {
//            $verificarmenu = 0;
//            $this->view->verificarmenu = $verificarmenu;
//        }

        //(Enviar Proposta ao MinC , Excluir Proposta)
//        $mov = new Proposta_Model_DbTable_TbMovimentacao();
//        $movBuscar = $mov->buscar(array('idprojeto = ?' => $idPreProjeto), array('idmovimentacao desc'), 1, 0)->current();
//
//        if (isset($movBuscar->Movimentacao) && $movBuscar->Movimentacao != 95) {
//            $enviado = 'true';
//            $this->view->enviado = $enviado;
//        } else {
//            $enviado = 'false';
//            $this->view->enviado = $enviado;
//        }

        $this->verificarPermissaoAcesso(true, false, false);

        //recupera ID do pre projeto (proposta)
        if (!empty($this->idPreProjeto)) {
            $this->view->idPreProjeto = $this->idPreProjeto;
        } else {
            parent::message("Necess&aacute;rio informar o n&uacute;mero da proposta.", "/proposta/manterpropostaincentivofiscal/listarproposta", "ERROR");
        }
    }

    /**
     * Metodo que monta grid de locais de realizacao
     *
     * @name indexAction
     * @return void
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @author wouerner <wouerner@gmail.com>
     * @since  17/08/2016
     */
    public function indexAction()
    {
        //RECUPERA OS LOCAIS DE REALIZACAO CADASTRADOS
        $arrBusca = array();
        $arrBusca['idprojeto'] = $this->idPreProjeto;
        $arrBusca['stabrangencia'] = 1;
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $rsAbrangencia = $tblAbrangencia->buscar($arrBusca);

        $arrDados = array("localizacoes" => $rsAbrangencia,
            "acaoAlterar" => $this->_urlPadrao . "/proposta/localderealizacao/form-local-de-realizacao",
            "acaoExcluir" => $this->_urlPadrao . "/proposta/localderealizacao/excluir");

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY DADOS
        $this->montaTela("localderealizacao/index.phtml", $arrDados);
    }

    /**
     *
     * Metodo que monta o formulario de editar local de realizacao
     * @param void
     * @return void
     */
    public function formLocalDeRealizacaoAction()
    {
        //recupera parametros
        $get = Zend_Registry::get('get');
        $idAbrangencia = $get->cod;

        //RECUPERA OS PAISES
        $table = new Agente_Model_DbTable_Pais();
        $arrPais = $table->fetchPairs('idPais', 'Descricao');

        //RECUPRA OS ESTADOS
        $mapperUf = new Agente_Model_UFMapper();
        $arrUf = $mapperUf->fetchPairs('idUF', 'Sigla');

        //RECUPERA LOCALIZACOES CADASTRADAS
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $arrBusca = array();
        $arrBusca['idProjeto'] = $this->idPreProjeto;
        $arrBusca['stAbrangencia'] = 1;
        if (!empty($idAbrangencia)) {
            $arrBusca['idAbrangencia'] = $idAbrangencia;
        }
        $arrAbrangencia = $tblAbrangencia->buscar($arrBusca);
        $arrCidades = "";
        # RECUPERA AS CIDADES
        if (!empty($arrAbrangencia[0]['idMunicipioIBGE'])) {
            $table = new Agente_Model_DbTable_Municipios();
            $arrCidades = $table->fetchPairs('idMunicipioIBGE', 'Descricao', array('idufibge' => $arrAbrangencia[0]['idUF']));
        }

        $arrDados = array("paises" => $arrPais,
            "estados" => $arrUf,
            'municipios' => $arrCidades,
            "localizacoes" => $arrAbrangencia,
            "idAbrangencia" => $idAbrangencia,
            "acao" => $this->_urlPadrao . "/localderealizacao/salvar-local-realizacao");

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY DADOS
        $this->montaTela("localderealizacao/formlocalderealizacao.phtml", $arrDados);
    }

    /**
     *
     * @param void
     * @return objeto
     * @deprecated Este metodo era usado para editar o local de realizacao, foi substitudo pelo metodo salvarlocaderealizacao @novain
     */
    public function salvarAction()
    {
        $post = Zend_Registry::get("post");
        $idAbrangencia = $post->cod;
        //instancia classe modelo
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();

        if (isset($_REQUEST['edital'])) {
            $edital = "&edital=s";
        } else {
            $edital = "";
        }
        $qtdeLocais = $post->qtdeLocais;
        $locais = array();
        $locaisinvalidos = array();
        xd($post);
        for ($i = 1; $i <= $qtdeLocais; $i++) {
            $pais = $post->__get("pais_" . $i);
            $uf = $post->__get("uf_" . $i);
            $municipio = $post->__get("cidade_" . $i);
            $local_c = $pais . $uf . $municipio;

            if (!in_array($local_c, $locaisinvalidos) || empty($local_c)) {
                $locais[$i]["idPais"] = $post->__get("pais_" . $i);

                if ($locais[$i]["idPais"] == 31) {
                    $locais[$i]["idUF"] = $post->__get("uf_" . $i);
                    $locais[$i]["idMunicipioIBGE"] = $post->__get("cidade_" . $i);
                } else {
                    $locais[$i]["idUF"] = "0";
                    $locais[$i]["idMunicipioIBGE"] = "0";
                }
            } else {
                parent::message("Registro j&aacute; cadastrado, transa&ccedil;&atilde;o cancelada!", "/proposta/localderealizacao/index?idPreProjeto=" . $this->idPreProjeto . $edital, "ALERT");
            }
            $locaisinvalidos[$i] = $local_c;
        }

//        try {
        $global = 0;
        //incluindo novos registros
        if (empty($idAbrangencia)) {
            //APAGA TODOS OS REGISTROS PARA CADASTRA-LOS NOVAMENTE
            // $tblAbrangencia->deleteBy(array('idprojeto' => $this->idPreProjeto, 'stabrangencia' => 1));
        } else {
            foreach ($locais as $d) {
                $p = $d['idPais'];
                if ($p == 31) {
                    $u = (int)$d['idUF'];
                    $m = (int)$d['idMunicipioIBGE'];
                } else {
                    $u = 0;
                    $m = 0;
                }
            }

            $resultado = $tblAbrangencia->verificarIgual($p, $u, $m, $this->idPreProjeto);

            if (count($resultado) > 0) {
                parent::message("Registro j&aacute; cadastrado, transa&ccedil;&atilde;o cancelada!", "/proposta/localderealizacao/index?idPreProjeto=" . $this->idPreProjeto . $edital, "ALERT");
                return;
            }
        }


        //INSERE LOCAIS DE REALIZACAO (tabela SAC.dbo.Abrangencia)
        for ($i = 1; $i <= count($locais); $i++) {
            $dados = array("idProjeto" => $this->idPreProjeto,
                "stAbrangencia" => 1,
                "Usuario" => $this->idUsuario,
                "idPais" => $locais[$i]["idPais"],
                "idUF" => ($locais[$i]["idPais"] == 31) ? $locais[$i]["idUF"] : 0,
                "idMunicipioIBGE" => ($locais[$i]["idPais"] == 31) ? $locais[$i]["idMunicipioIBGE"] : 0);

            $dados['stAbrangencia'] = 1;
            $dados['idAbrangencia'] = $idAbrangencia;


            if (!empty($dados["idProjeto"]) && !empty($dados["idPais"])) {
                $retorno = $tblAbrangencia->salvar($dados);
            }
        }
        if ($idAbrangencia) {
            parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "/proposta/localderealizacao/index?idPreProjeto=" . $this->idPreProjeto . $edital, "CONFIRM");
        } else {
            parent::message("Cadastro realizado com sucesso!", "/proposta/localderealizacao/index?idPreProjeto=" . $this->idPreProjeto . $edital, "CONFIRM");
        }

//        }catch(Zend_Exception $ex) {
//            parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o! <br>", "/proposta/localderealizacao/index?idPreProjeto=".$this->idPreProjeto.$edital, "ERROR");
//        }
    }

    /**
     * Metodo responsavel por apagar um local de realiza&ccedil;&atilde;o gravado
     * @param void
     * @return objeto
     */
    public function excluirAction()
    {
        $excluir = false;

        $this->verificarPermissaoAcesso(true, false, false);

        $params = $this->getRequest()->getParams();

        if (!empty($params['cod'])) {
            // buscar municipio e estado desta abrangencia
            $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
            $abrangencia = $tblAbrangencia->findby(array('idabrangencia' => $params['cod']));

            // excluir itens orcamentarios desta abrangencia
            if (!empty($abrangencia)) {
                $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $excluir = $tbPlanilhaProposta->deleteBy(array('idProjeto' => $this->idPreProjeto, 'UfDespesa' => $abrangencia['idUF'], 'MunicipioDespesa' => $abrangencia['idMunicipioIBGE']));

                if ($excluir) {
                    $this->atualizarcustosvinculadosdaplanilha($this->idPreProjeto);
                }
            }

            //Exclui registro da tabela abrangencia
            $excluir = $tblAbrangencia->delete(array('idabrangencia = ?' => $params['cod']));
        }

        if ($excluir) {
            parent::message("Exclus&atilde;o realizada com sucesso!", "/proposta/localderealizacao/index/idPreProjeto/" . $this->idPreProjeto, "CONFIRM");
        } else {
            parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o!", "/proposta/localderealizacao/index/idPreProjeto/" . $this->idPreProjeto, "ERROR");
        }
    }

    /**
     * Metodo que retorna lista de locais de realizacao
     * @param void
     * @return objeto
     */
    public function consultarcomponenteAction()
    {
        //recebe o id via GET
        $get = Zend_Registry::get('get');
        $idProjeto = $get->idPreProjeto;
        $this->_helper->layout->disableLayout(); // desabilita o layout
        if (!empty($idProjeto) || $idProjeto == '0') {
            //RECUPERA OS LOCAIS DE REALIZACAO CADASTRADOS
            $arrBusca = array();
            $arrBusca['idProjeto'] = $idProjeto;
            $arrBusca['stAbrangencia'] = 1;

            $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
            $rsAbrangencia = $tblAbrangencia->buscar($arrBusca);
            $this->view->localizacoes = $rsAbrangencia;
        } else {
            return false;
        }
    }

    /**
     * formInserirAction
     *
     * @access public
     * @return void
     */
    public function formInserirAction()
    {
//        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');
//        $this->view->idPreProjeto = $idPreProjeto;

        # RECUPERA OS PAISES
        $tablePais = new Agente_Model_DbTable_Pais();
        $rsPais = $tablePais->fetchPairs('idPais', 'Descricao');
        $this->view->paises = $rsPais;

        # RECUPERA OS ESTADOS
        $mapperUf = new Agente_Model_UFMapper();
        $rsEstados = $mapperUf->fetchPairs('idUF', 'Descricao');
        $this->view->estados = $rsEstados;
    }

    /**
     * cidadesAction
     *
     * @access public
     * @return void
     */
    public function cidadesAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $post = Zend_Registry::get('post');
        $idEstado = $post->idEstado;

        # RECUPERA AS CIDADES
        $table = new Agente_Model_DbTable_Municipios();
        $arrCidades = $table->fetchPairs('idMunicipioIBGE', 'Descricao', array('idufibge' => $idEstado));
        $html = '';
        foreach ($arrCidades as $key => $cidades) {
            $html .= "<option value=\"{$key}\">{$cidades}</option>";
        }
        echo $html;
    }

    /**
     * Metódo que salva e edita o local de realizacao
     * Neste metodo, quando edita um local de realizacao o mesmo atualiza os itens da planilha orcamentaria.
     *
     * @access public
     * @return void
     */
    public function salvarLocalRealizacaoAction()
    {
        $post = Zend_Registry::get("post");
        $idAbrangencia = $post->cod;

        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();

        //RECUPERA LOCALIZACOES CADASTRADAS
        $arrBusca = array();
        $arrBusca['idProjeto'] = $this->idPreProjeto;
        $arrBusca['stAbrangencia'] = 1;
        $arrBusca['p.idPais'] = $post->pais;
        if ($post->pais == 31) {
            $arrBusca['u.idUF'] = $post->estados;
            $arrBusca['m.idMunicipioIBGE'] = $post->cidades;
        }

        $rsAbrangencia = $tblAbrangencia->buscar($arrBusca);

        $jacadastrado = false;
        if (count($rsAbrangencia) > 0) {
            if (empty($idAbrangencia)) {
                $jacadastrado = true;
            } elseif ($rsAbrangencia[0]['idAbrangencia'] != $idAbrangencia) {
                $jacadastrado = true;
            }
        }

        if ($jacadastrado) {
            parent::message("Local de realiza&ccedil;&atilde;o j&aacute; cadastrado!", "/proposta/localderealizacao/index/idPreProjeto/" . $this->idPreProjeto, "ALERT");
        }


        $pais = $post->pais;
        $estados = $post->estados;
        $cidades = $post->cidades;

        //INSERE LOCAIS DE REALIZACAO (tabela SAC.dbo.Abrangencia)
        $dadosAbrangencia = array(
            "idprojeto" => $this->idPreProjeto,
            "stabrangencia" => 1,
            "usuario" => $this->idUsuario,
            "idpais" => $pais,
            "iduf" => ($pais == 31) ? $estados : 0,
            "idmunicipioibge" => ($pais == 31) ? $cidades : 0
        );

        $msg = "Local de realiza&ccedil;&atilde;o cadastrado com sucesso!";

        if (!empty($dadosAbrangencia["idprojeto"]) && !empty($dadosAbrangencia["idpais"])) {
            if (empty($idAbrangencia)) {
                $retorno = $tblAbrangencia->insert($dadosAbrangencia);
            } else {
                $this->atualizarLocaldeRealizacaoDaPlanilha($idAbrangencia, $dadosAbrangencia["iduf"], $dadosAbrangencia["idmunicipioibge"]);

                $msg = "Local de realiza&ccedil;&atilde;o alterado com sucesso!";
                $whereAbrangencia['idAbrangencia = ?'] = $idAbrangencia;
                $retorno = $tblAbrangencia->update($dadosAbrangencia, $whereAbrangencia);
            }

            parent::message($msg, "/proposta/localderealizacao/index?idPreProjeto=" . $this->idPreProjeto, "CONFIRM");
        }
    }

    public function atualizarLocaldeRealizacaoDaPlanilha($idAbrangencia, $idUf, $idMunicipio)
    {
        // buscar municipio e estado desta abrangencia
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $abrangencia = $tblAbrangencia->findby(array('idabrangencia' => $idAbrangencia));

        // atualizar itens orcamentarios desta abrangencia
        if (!empty($abrangencia)) {
            $dadosAbrangenciaPlanilha = array(
                'UfDespesa' => $idUf,
                'MunicipioDespesa' => $idMunicipio
            );
            $wherePlanilha = array('idProjeto = ?' => $this->idPreProjeto, 'UfDespesa = ?' => $abrangencia['idUF'], 'MunicipioDespesa = ?' => $abrangencia['idMunicipioIBGE']);

            $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
            $retorno = $tbPlanilhaProposta->update($dadosAbrangenciaPlanilha, $wherePlanilha);

            return true;
        }
    }
}
