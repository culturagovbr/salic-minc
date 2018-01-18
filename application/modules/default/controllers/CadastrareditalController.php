<?php

class CadastrareditalController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $auth = Zend_Auth::getInstance();// instancia da autenticacao
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sessao
        $codOrgao = $GrupoAtivo->codOrgao; //  Orgao ativo na sessao
        $this->view->codOrgao = $codOrgao;
        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC72
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_Usuario(); // objeto usu�rio
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
            $this->idusuario = $auth->getIdentity()->usu_codigo;
            $this->view->idUsuarioLogado = $this->idusuario;
            isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
        } else {
            $this->idusuario = $auth->getIdentity()->IdUsuario;
        }

        // verifica as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 114;  // Coordenador de Editais
        $PermissoesGrupo[] = 97;  // Gestor salic
        $PermissoesGrupo[] = 1111; //Proponente
        //parent::perfil(1, $PermissoesGrupo);

        parent::init();
    } 

    public function indexAction()
    {
        $this->_redirect("cadastraredital/consultaralterareditais");
    }


    /**
     * M�todo para buscar/salvar os dados do edital
     */
    public function dadosgeraisAction()
    {
        $auth = Zend_Auth::getInstance();// instancia da autenticacao
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idorgao = $auth->getIdentity()->usu_orgao;
        $usu_identificacao = $auth->getIdentity()->usu_identificacao;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sessao
        $codOrgao = $GrupoAtivo->codOrgao; //  Orgao ativo na sessao
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

        $buscarClassificacaoDocumento = new tbClassificaDocumento();
        $resultadoClassificaDocumento = $buscarClassificacaoDocumento->buscar(array('idClassificaDocumento not in (?)'=>array(23,24,25)), array('dsClassificaDocumento ASC'))->toArray();

        $this->view->resultadoClassificaDocumento  = $resultadoClassificaDocumento;

        $buscarOrgao = new Orgaos();
        $resultadoOrgaos = $buscarOrgao->buscar(array('Sigla != ?'=>''), array('Sigla ASC'))->toArray();

        $this->view->resultadoOrgaos  = $resultadoOrgaos;

        if (isset($_POST['idorgao'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $iduf = $_POST['idorgao'];
            $date = getdate();
            $data = $date['year'];

            $buscarPI = new SimcAtividade();
            $pi = $buscarPI->buscarPI(array('o.Codigo = ?'=>$iduf, 'atianopi = ?'=> $data, 'ati.atistatuspi = ?'=>"A"));

            $a = 0;
            $Array = array();
            foreach ($pi as $Dados) {
                $Array[$a]['idPi'] = $Dados->atiid;
                $Array[$a]['pi'] = $Dados->pi;
                $a++;
            }

            if (empty($Array)) {
                $Array['semdados'] = 'semdados';
            }

            $this->_helper->json($Array);
            $this->_helper->viewRenderer->setNoRender(true);
        }

        // caso j� exista um edital cadastrado
        if (!empty($_GET['idEdital'])  &&  !empty($idusuario)) {
            $idEdital = $_GET['idEdital'];
            $this->view->idUsuarioLogado = $idusuario;

            $dadosEdital = new Edital();
            $buscaEdital = $dadosEdital->buscaEditalFormDocumento($idusuario, $idEdital)->toArray();
            //busca os PI�s da secretaria cadastrada no Edital
            $buscarPI = new SimcAtividade();
            $pi = $buscarPI->buscarPI(array('o.Codigo = ?'=>$buscaEdital[0]['idOrgao'], 'atianopi = ?'=> date('Y'), 'ati.atistatuspi = ?'=>"A"))->toArray();
            $classificacaoDocumento  = $buscaEdital[0]['idClassificaDocumento'];
            if (count($classificacaoDocumento)>0) {
                $buscarClassificacaoDocumento = new tbClassificaDocumento();
                $resultadoClassificaDocumento = $buscarClassificacaoDocumento->buscar(array('idClassificaDocumento = ?'=> $classificacaoDocumento))->current()->toArray();

                $dadosFasesEdital = new tbEditalXtbFaseEdital();
                $buscaFasesEdital = $dadosFasesEdital->buscar(array( 'idEdital = ?' => $idEdital ))->toArray();

                $dadosOrgao = new Orgaos();
                $buscaOrgao = $dadosOrgao->buscar(array( 'Codigo = ?' => $buscaEdital[0]['idOrgao'] ))->toArray();
                $this->view->sigla = $buscaOrgao[0]['Sigla'];
            } else {
                parent::message("Edital n&atilde;o encontrado.", "/cadastraredital/consultaralterareditais", "ERROR");
            }

            if (!empty($buscaFasesEdital)) {
                $this->view->recurso = $buscaFasesEdital[0]['qtDiasRecurso'];
                $this->view->julg = $buscaFasesEdital[0]['qtDiasJulgamento'];

                foreach ($buscaFasesEdital as $FasesEdital) {
                    if ($FasesEdital['idFaseEdital'] == 1) {
                        $dataIni1 = data::tratarDataZend($FasesEdital['dtIniFase'], "brasileiro");
                        $dataFim1 = data::tratarDataZend($FasesEdital['dtFimFase'], "brasileiro");
                        $this->view->dtIniFase1 = $dataIni1;
                        $this->view->dtFimFase1 = $dataFim1;
                    } elseif ($FasesEdital['idFaseEdital'] == 2) {
                        $dataIni2 = data::tratarDataZend($FasesEdital['dtIniFase'], "brasileiro");
                        $dataFim2 = data::tratarDataZend($FasesEdital['dtFimFase'], "brasileiro");
                        $this->view->dtIniFase2 = $dataIni2;
                        $this->view->dtFimFase2 = $dataFim2;
                    } elseif ($FasesEdital['idFaseEdital'] == 3) {
                        $dataIni3 = data::tratarDataZend($FasesEdital['dtIniFase'], "brasileiro");
                        $dataFim3 = data::tratarDataZend($FasesEdital['dtFimFase'], "brasileiro");
                        $this->view->dtIniFase3 = $dataIni3;
                        $this->view->dtFimFase3 = $dataFim3;
                    } elseif ($FasesEdital['idFaseEdital'] == 4) {
                        $dataIni4 = data::tratarDataZend($FasesEdital['dtIniFase'], "brasileiro");
                        $dataFim4 = data::tratarDataZend($FasesEdital['dtFimFase'], "brasileiro");
                        $this->view->dtIniFase4 = $dataIni4;
                        $this->view->dtFimFase4 = $dataFim4;
                    } elseif ($FasesEdital['idFaseEdital'] == 5) {
                        $dataIni5 = data::tratarDataZend($FasesEdital['dtIniFase'], "brasileiro");
                        $dataFim5 = data::tratarDataZend($FasesEdital['dtFimFase'], "brasileiro");
                        $this->view->dtIniFase5 = $dataIni5;
                        $this->view->dtFimFase5 = $dataFim5;
                    } elseif ($FasesEdital['idFaseEdital'] == 6) {
                        $dataIni6 = data::tratarDataZend($FasesEdital['dtIniFase'], "brasileiro");
                        $dataFim6 = data::tratarDataZend($FasesEdital['dtFimFase'], "brasileiro");
                        $this->view->dtIniFase6 = $dataIni6;
                        $this->view->dtFimFase6 = $dataFim6;
                    } elseif ($FasesEdital['idFaseEdital'] == 7) {
                        $dataIni7 = data::tratarDataZend($FasesEdital['dtIniFase'], "brasileiro");
                        $dataFim7 = data::tratarDataZend($FasesEdital['dtFimFase'], "brasileiro");
                        $this->view->dtIniFase7 = $dataIni7;
                        $this->view->dtFimFase7 = $dataFim7;
                    } elseif ($FasesEdital['idFaseEdital'] == 8) {
                        if (!empty($FasesEdital['idFaseEdital'])) {
                            $dataIni8 = data::tratarDataZend($FasesEdital['dtIniFase'], "brasileiro");
                            $dataFim8 = data::tratarDataZend($FasesEdital['dtFimFase'], "brasileiro");
                            $this->view->dtIniFase8 = $dataIni8;
                            $this->view->dtFimFase8 = $dataFim8;
                        } else {
                            $this->view->dtIniFase8 = "";
                            $this->view->dtFimFase8 = "";
                        }
                    } else {
                        if (!empty($FasesEdital['idFaseEdital'])) {
                            $dataIni9 = data::tratarDataZend($FasesEdital['dtIniFase'], "brasileiro");
                            $dataFim9 = data::tratarDataZend($FasesEdital['dtFimFase'], "brasileiro");
                            $this->view->dtIniFase9 = $dataIni9;
                            $this->view->dtFimFase9 = $dataFim9;
                        } else {
                            $this->view->dtIniFase9 = "";
                            $this->view->dtFimFase9 = "";
                        }
                    }
                } // fecha foreach
            } // fecha if

            $this->view->idClassificaDocumento = $resultadoClassificaDocumento['idClassificaDocumento'];
            $this->view->dsClassificaDocumento = $resultadoClassificaDocumento['dsClassificaDocumento'];
            $this->view->buscaEdital = $buscaEdital;
            $this->view->pi = $pi;
        } // fecha if ( !empty ( $_GET['idEdital'] ) )


        // caso os dados sejam enviados via post
        if ($_POST) {
            $nomeEdital = $_POST['nomeEdital'];
            $classificaDocumento = $_POST['classificaDocumento'];
            $modalidadeDocumento = $_POST['modalidadeDocumento'];
            $numeroEdital = $_POST['numeroEdital'];
            $orgao = $_POST['orgao'];
            $celulaOrcamentaria = $_POST['celulaOrcamentaria'];
            $qtAvaliadores = $_POST['qtAvaliadores'];
            $tipoFundo = $_POST['tipoFundo'];
            $objeto = $_POST['objeto'];
            $diasRec = $_POST['diasRec'];
            $diasJulg = $_POST['diasJulg'];
            $faseElabIni = $_POST['faseElabIni'];
            $faseElabFim = $_POST['faseElabFim'];
            $faseInscIni = $_POST['faseInscIni'];
            $faseInscFim = $_POST['faseInscFim'];
            $faseHabIni = $_POST['faseHabIni'];
            $faseHabFim = $_POST['faseHabFim'];
            $faseSelIni = $_POST['faseSelIni'];
            $faseSelFim = $_POST['faseSelFim'];
            $faseHomIni = $_POST['faseHomIni'];
            $faseHomFim = $_POST['faseHomFim'];
            $faseDivIni = $_POST['faseDivIni'];
            $faseDivFim = $_POST['faseDivFim'];
            $fasePagIni = $_POST['fasePagIni'];
            $fasePagFim = $_POST['fasePagFim'];
            $faseAcoIni = $_POST['faseAcoIni'];
            $faseAcoFim = $_POST['faseAcoFim'];
            $fasePrestIni = $_POST['fasePrestIni'];
            $fasePrestFim = $_POST['fasePrestFim'];
            $atiid = $_POST['pi'];

            $date = getdate();
            $data = $date['year'];

            $buscarPI = new SimcAtividade();
            $pi = $buscarPI->buscarPI(array('ati.atiid = ?'=>$atiid, 'o.Codigo = ?'=>$orgao, 'atianopi = ?'=> $data, 'ati.atistatuspi = ?'=>"A"));

            foreach ($pi as $Dados) {
                $PiDisponivel = $Dados['atiorcamento'];
            }
            $PiDisponivel = $PiDisponivel/100;

            if (!empty($_POST['idEdital'])) {
                $idEdital = $_POST['idEdital'];
            }
            if (!empty($_POST['nrFormDocumento'])) {
                $nrFormDocumento = $_POST['nrFormDocumento'];
            }
            if (!empty($_POST['nrVersaoDocumento'])) {
                $nrVersaoDocumento = $_POST['nrVersaoDocumento'];
            }

            $insereDadosEdital = new Edital();
            try {
                if (empty($_POST['idEdital'])) { // cadastro
                    $dados = array('idOrgao' => $orgao,
                    'NrEdital' => $numeroEdital,
                    'DtEdital' => new Zend_Db_Expr('GETDATE()'),
                    'CelulaOrcamentaria' => $celulaOrcamentaria,
                    'Objeto' => $objeto,
                    'Logon' => $idusuario,
                    'qtAvaliador' => $qtAvaliadores,
                    'stDistribuicao' => 'M',
                    'stAdmissibilidade' => 'S',
                    'cdTipoFundo' => $tipoFundo,
                    'idAti' => $atiid
                    //, 'piDisponivel' => $PiDisponivel
                    );
    
                    $idEdital = $insereDadosEdital->salvar($dados);
                } else { // altera��o
                    $dados = array('idEdital' => $idEdital,
                    'idOrgao' => $orgao,
                    'NrEdital' => $numeroEdital,
                    'DtEdital' => new Zend_Db_Expr('GETDATE()'),
                    'CelulaOrcamentaria' => $celulaOrcamentaria,
                    'Objeto' => $objeto,
                    'Logon' => $idusuario,
                    'qtAvaliador' => $qtAvaliadores,
                    'stDistribuicao' => 'M',
                    'stAdmissibilidade' => 'S',
                    'cdTipoFundo' => $tipoFundo,
                    'idAti' => $atiid);
    
                    $idEdital = $insereDadosEdital->salvar($dados);
                }
            } catch (Exception $e) {
                parent::message("Erro ao realizar opera&ccedil;&atilde;o. ".$e->getMessage(), "/cadastraredital/dadosgerais", "ERROR");
            }
            $arrFases[0][0] = $faseElabIni;
            $arrFases[0][1] = $faseElabFim;
            $arrFases[0][2] = 1;
            $arrFases[1][0] = $faseInscIni;
            $arrFases[1][1] = $faseInscFim;
            $arrFases[1][2] = 2;
            $arrFases[2][0] = $faseHabIni;
            $arrFases[2][1] = $faseHabFim;
            $arrFases[2][2] = 3;
            $arrFases[3][0] = $faseSelIni;
            $arrFases[3][1] = $faseSelFim;
            $arrFases[3][2] = 4;
            $arrFases[4][0] = $faseHomIni;
            $arrFases[4][1] = $faseHomFim;
            $arrFases[4][2] = 5;
            $arrFases[5][0] = $faseDivIni;
            $arrFases[5][1] = $faseDivFim;
            $arrFases[5][2] = 6;
            $arrFases[6][0] = $fasePagIni;
            $arrFases[6][1] = $fasePagFim;
            $arrFases[6][2] = 7;
            if (!empty($_POST['faseAcoIni']) && !empty($_POST['faseAcoFim'])) {
                $arrFases[7][0] = $faseAcoIni;
                $arrFases[7][1] = $faseAcoFim;
                $arrFases[7][2] = 8;
            }
            if (!empty($_POST['fasePrestIni']) && !empty($_POST['fasePrestFim'])) {
                $arrFases[8][0] = $fasePrestIni;
                $arrFases[8][1] = $fasePrestFim;
                $arrFases[8][2] = 9;
            }

            $insereDadosFaseEdital = new tbEditalXtbFaseEdital();
            try {
                foreach ($arrFases as $fases) {
                    $dataIniFase =  Data::dataAmericana($fases[0]);
                    $dataFimFase =  Data::dataAmericana($fases[1]);

                    $dados = array('idFaseEdital' => $fases[2],
                        'idEdital' => $idEdital,
                        'dtIniFase' => $dataIniFase,
                        'dtFimFase' => $dataFimFase,
                        'qtDiasRecurso' => $diasRec,
                        'qtDiasJulgamento' => $diasJulg);

                    if (!empty($_POST['idEdital'])) { // altera��o
                        $where = " idFaseEdital = " . $fases[2] . " AND idEdital = " . $idEdital;

                        $verificaFaseEdital = $insereDadosFaseEdital->buscar(array('idEdital = ?' => $idEdital, 'idFaseEdital = ?' => $fases[2]))->toArray();

                        if (empty($verificaFaseEdital[0])) {
                            $idFasesEdital = $insereDadosFaseEdital->salvar($dados);
                        } else {
                            $idFasesEdital = $insereDadosFaseEdital->alterar($dados, $where);
                        }

                        $idFasesEdital = $insereDadosFaseEdital->alterar($dados, $where);
                    } else { // cadastro
                        $idFasesEdital = $insereDadosFaseEdital->salvar($dados);
                    }
                } // fecha foreach fases
            } catch (Exception $e) {
                parent::message("Erro ao realizar opera&ccedil;&atilde;o. ".$e->getMessage(), "/cadastraredital/dadosgerais", "ERROR");
            }

            $inserirNrFormDocumento = new tbFormDocumento();
            try {
                if (empty($nrFormDocumento)) { // cadastro
                    $dadosFormDocumento = array('nrVersaoDocumento' => 1,
                        'nmFormDocumento' => $nomeEdital,
                        'dsFormDocumento' => 'Formul�rio de Edital',
                        'stFormDocumento' => 'A',
                        'dtCadastramento' => new Zend_Db_Expr('GETDATE()'),
                        'idClassificaDocumento' => $classificaDocumento,
                        'stModalidadeDocumento' => $modalidadeDocumento,
                        'idEdital' => $idEdital);
                    $idFormDocumento = $inserirNrFormDocumento->salvar($dadosFormDocumento);
                    $nrFormDocumento   = $idFormDocumento['nrFormDocumento'];
                    $nrVersaoDocumento = $idFormDocumento['nrVersaoDocumento'];

                    parent::message("Cadastro realizado com sucesso!", "/cadastraredital/dadosgerais?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idUsuario={$idusuario}&idEdital={$idEdital}", "CONFIRM");
                } else { // altera��o
                    $dadosFormDocumento = array('nmFormDocumento' => $nomeEdital,
                        'dsFormDocumento' => 'Formul�rio de Edital',
                        'stFormDocumento' => 'A',
                        'dtCadastramento' => new Zend_Db_Expr('GETDATE()'),
                        'idClassificaDocumento' => $classificaDocumento,
                        'stModalidadeDocumento' => $modalidadeDocumento);
                    $whereFormDocumento = array(
                        'nrFormDocumento = ?'=>$nrFormDocumento,
                        'nrVersaoDocumento = ?'=>$nrVersaoDocumento,
                        'idEdital = ?'=>$idEdital
                      );
                    //" WHERE nrFormDocumento = " . $nrFormDocumento . " AND nrVersaoDocumento = $nrVersaoDocumento AND idEdital = $idEdital";
                    $idFormDocumento = $inserirNrFormDocumento->update($dadosFormDocumento, $whereFormDocumento);

                    parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "/cadastraredital/dadosgerais?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idUsuario={$idusuario}&idEdital={$idEdital}", "CONFIRM");
                }
            } catch (Exception $e) {
                parent::message("Erro ao realizar opera&ccedil;&atilde;o. ".$e->getMessage(), "/cadastraredital/dadosgerais", "ERROR");
            }
        } // fecha if ($_POST)
    } // fecha m�todo dadosgeraisAction()



    /**
     * M�todo para cadastro/busca de crit�rios de avalia��o
     */
    public function criteriosavaliacaoAction()
    {
        $auth = Zend_Auth::getInstance();// instancia da autenticacao
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idorgao = $auth->getIdentity()->usu_orgao;
        $usu_identificacao = $auth->getIdentity()->usu_identificacao;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sessao
        $codOrgao = $GrupoAtivo->codOrgao; //  Orgao ativo na sessao
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

        // joga o nome do edital no t�tulo
        if (isset($_GET['idEdital'])) {
            // joga o nome do edital no t�tulo
            $tbFormDocumentoDAO =   new tbFormDocumento();
            $edital                 =   $tbFormDocumentoDAO->buscar(array('idEdital = ?'=>$_GET['idEdital']));
            $this->view->nmEdital   =   $edital[0]->nmFormDocumento;
        }

        // busca todos os crit�rios cadastrados
        if (isset($_GET['nrFormDocumento']) && isset($_GET['nrVersaoDocumento'])) {
            $nrFormDocumento = $_GET['nrFormDocumento'];
            $nrVersaoDocumento = $_GET['nrVersaoDocumento'];

            $dadosBuscaTbPergunta = array('fd.idEdital = ?' => $_GET['idEdital'],
                                          'fd.idClassificaDocumento = ?' => 25,
                                          'rv.nrVersaoDocumento = ?' => $nrVersaoDocumento);

            $dadosTbpergunta = new tbPergunta();
            $buscarDadosTbPergunta = $dadosTbpergunta->buscarDados($dadosBuscaTbPergunta, 'pd.nrOrdemPergunta')->toArray();
            $this->view->dadosPergunta = $buscarDadosTbPergunta;
        }


        // caso os dados sejam enviados via post
        if ($_POST) {
            /* n�o est� sendo utilizado
            // recadastra a posi��o
            if ( isset ( $_POST['operacao'] ) )
            {
                $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

                 $nrOrdemPergunta = $_POST['nrOrdemPergunta'];
                 $nrPergunta = $_POST['nrPergunta'];
                 $nrFormDocumento = $_POST['nrFormDocumento'];
                 $nrVersaoDocumento = $_POST['nrVersaoDocumento'];
                 $tbPerguntaFormDocto = new tbPerguntaFormDocto();
                 $buscaPerguntaFormDocto = $tbPerguntaFormDocto->buscarPergunta($nrOrdemPergunta, $nrPergunta, $nrFormDocumento, $nrVersaoDocumento);
                 $dados = array(
                                'nrOrdemPergunta'=>$nrOrdemPergunta
                            );
                 $where = 'nrPergunta = '.$nrPergunta.'and nrFormDocumento = '.$nrFormDocumento.'and nrVersaoDocumento = '.$nrVersaoDocumento;
                 $idPerguntaFormDocto = $tbPerguntaFormDocto->update($dados,$where);
                 die;
            }*/

            if (empty($_POST['nrPergunta']) &&  !empty($_POST['acao'])) { // cadastro
                $nrFormDocumento = $_POST['nrFormDocumento'];
                $nrVersaoDocumento = $_POST['nrVersaoDocumento'];
                $nrPeso = $_POST['nrPeso'];
                $dsPergunta = $_POST['dsPergunta'];
                $dsLabelPergunta = $_POST['dsLabelPergunta'];
                $vlMinOpcao= $_POST['nrNotaInicio'];
                $vlMaxOpcao = $_POST['nrNotaFim'];
                $vlVariacaoOpcao = $_POST['nrNotaVariacao'];
                $idEdital = $_POST['idEdital'];
                $dtCadastramento = new Zend_Db_Expr('GETDATE()');
                $idEdital = $_POST['idEdital'];

                $tbFormDocumentoDAO =   new tbFormDocumento();
                $result = $tbFormDocumentoDAO->inserir(array(
                    'idEdital'              =>  $idEdital,
                    'nrVersaoDocumento'     =>  $nrVersaoDocumento,
                    'nmFormDocumento'       =>  $_POST['dsPergunta'],
                    'dsFormDocumento'       =>  'Crit�rio de Avalia��o',
                    'idClassificaDocumento' =>  25,
                    'dtCadastramento'       =>  new Zend_Db_Expr('GETDATE()'),
                    'stFormDocumento'       =>  'A'
                ));
                $nrFormDocumentoCriterio   = $result['nrFormDocumento'];
                $nrVersaoDocumentoCriterio = $result['nrVersaoDocumento'];

                $agentes = new Agente_Model_DbTable_Agentes();
                $buscarAgente = $agentes->buscar(array("CNPJCPF = ?" => $usu_identificacao))->current()->toArray();
                $idAgente = $buscarAgente['idAgente'];
                $dadosPergunta = array('stTipoRespPergunta' => "O",
                                       'dtCadastramento' => $dtCadastramento,
                                       'dsPergunta' => $dsPergunta,
                                       'idPessoaCadastro' => $idAgente);
                $tbPergunta = new tbPergunta();
                $nrPergunta = $tbPergunta->salvar($dadosPergunta);

                $tbPerguntaFormDocto = new tbPerguntaFormDocto();
                $buscaPerguntaFormDocto = $tbPerguntaFormDocto->buscar(array('nrFormDocumento = ?' => $nrFormDocumentoCriterio), array('nrOrdemPergunta desc'))->current();
                if (!empty($buscaPerguntaFormDocto)) {
                    $dadosBuscaArray = $buscaPerguntaFormDocto->toArray();
                    $nrOrdemPergunta = $dadosBuscaArray['nrOrdemPergunta'];
                    $nrOrdemPergunta = $nrOrdemPergunta + 1;
                } else {
                    $nrOrdemPergunta = 1;
                }
                $dadosPerguntaFormDocto = array('nrFormDocumento' => $nrFormDocumentoCriterio,
                                                'nrVersaoDocumento' => $nrVersaoDocumentoCriterio,
                                                'nrPergunta' => $nrPergunta,
                                                'nrFormDocumentoPai' => $nrFormDocumentoCriterio,
                                                'nrVersaoFormDocumentoPai' => $nrVersaoDocumento,
                                                'nrPerguntaPai' => $nrPergunta,
                                                'nrOrdemPergunta' => $nrOrdemPergunta,
                                                'dsLabelPergunta' => $dsLabelPergunta,
                                                'nrPeso' => $nrPeso);
                $nrPerguntaFormDocto = $tbPerguntaFormDocto->salvar($dadosPerguntaFormDocto);


                $tbOpcaoResposta = new tbOpcaoResposta();
                $dadosOpcaoResposta = array('nrFormDocumento' => $nrFormDocumentoCriterio,
                                            'nrVersaoDocumento' => $nrVersaoDocumentoCriterio,
                                            'nrPergunta' => $nrPergunta,
                                            'nrOrdemOpcao' => 1,
                                            'dsOpcao' => '',
                                            'stTipoObjetoPgr' => "CB");
                $nrOpcao = $tbOpcaoResposta->salvar($dadosOpcaoResposta);

                $tbOpcaoRespostaVariavel = new tbOpcaoRespostaVariavel();
                $dadosOpcaoRespostaVariavel =  array('nrFormDocumento' => $nrFormDocumentoCriterio,
                                                    'nrVersaoDocumento' => $nrVersaoDocumentoCriterio,
                                                    'nrPergunta' => $nrPergunta,
                                                    'nrOpcao' => $nrOpcao['nrOpcao'],
                                                    'vlMinOpcao' => $vlMinOpcao,
                                                    'vlMaxOpcao' => $vlMaxOpcao,
                                                    'vlVariacaoOpcao' => $vlVariacaoOpcao);
                $nrOpcaoRespostaVariavel = $tbOpcaoRespostaVariavel->salvar($dadosOpcaoRespostaVariavel);

                parent::message("Cadastro realizado com sucesso!", "/cadastraredital/criteriosavaliacao?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idUsuario={$idusuario}&idEdital={$idEdital}", "CONFIRM");
            } // fim if cadastro

            if (!empty($_POST['nrPergunta']) &&  !empty($_POST['acao'])) { // altera��o
                $nrFormDocumento = $_POST['nrFormDocumento'];
                $nrVersaoDocumento = $_POST['nrVersaoDocumento'];
                $nrPeso = $_POST['nrPeso'];
                $dsPergunta = $_POST['dsPergunta'];
                $dsLabelPergunta = $_POST['dsLabelPergunta'];
                $vlMinOpcao= $_POST['nrNotaInicio'];
                $vlMaxOpcao = $_POST['nrNotaFim'];
                $vlVariacaoOpcao = $_POST['nrNotaVariacao'];
                $dtCadastramento = new Zend_Db_Expr('GETDATE()');
                $idEdital = $_POST['idEdital'];
                $nrPergunta = $_POST['nrPergunta'];

                $dadosBuscaTbPergunta = array('rv.nrPergunta = ?' => $nrPergunta);
                $dadosTbpergunta = new tbPergunta();
                $buscarDadosTbPergunta = $dadosTbpergunta->buscarDados($dadosBuscaTbPergunta, 'pd.nrOrdemPergunta')->toArray();
                $nrFormDocumentoCriterio   = $buscarDadosTbPergunta[0]['nrFormDocumento'];
                $nrVersaoDocumentoCriterio = $buscarDadosTbPergunta[0]['nrVersaoDocumento'];

                $tbFormDocumento = new tbFormDocumento();
                $dadosFormDocumento = array('nmFormDocumento' => $dsPergunta,
                    'dsFormDocumento' => 'Crit�rio de Avalia��o',
                    'stFormDocumento' => 'A',
                    'dtCadastramento' => new Zend_Db_Expr('GETDATE()'),
                    'idClassificaDocumento' => 25);
                $whereFormDocumento = array(
                    'nrFormDocumento = ?'=>$nrFormDocumentoCriterio,
                    'nrVersaoDocumento = ?'=>$nrVersaoDocumentoCriterio,
                    'idEdital = ?'=>$idEdital);
                $tbFormDocumento = $tbFormDocumento->update($dadosFormDocumento, $whereFormDocumento);

                $tbPergunta = new tbPergunta();
                $buscaPergunta = $tbPergunta->buscar(array('nrPergunta = ?' => $nrPergunta))->current();
                $buscaPergunta->dsPergunta = $_POST['dsPergunta'];
                $idPergunta = $buscaPergunta->save();

                $tbPerguntaFormDocto = new tbPerguntaFormDocto();
                $buscaPerguntaFormDocto = $tbPerguntaFormDocto->buscar(array('nrPergunta = ?' => $nrPergunta
                     ,'nrFormDocumento = ?'   => $nrFormDocumentoCriterio
                     ,'nrVersaoDocumento = ?' => $nrVersaoDocumentoCriterio))->current();

                $buscaPerguntaFormDocto->dsLabelPergunta = $_POST['dsLabelPergunta'];
                $buscaPerguntaFormDocto->nrPeso = $_POST['nrPeso'];
                $idPerguntaFormDocto = $buscaPerguntaFormDocto->save();

                $tbOpcaoResposta = new tbOpcaoResposta();
                $buscaOpcaoResposta = $tbOpcaoResposta->buscar(array('nrFormDocumento = ?' => $nrFormDocumentoCriterio,
                                                                      'nrVersaoDocumento = ?' => $nrVersaoDocumentoCriterio,
                                                                      'nrPergunta = ?' => $nrPergunta))->current();
                $nrOpcao = $buscaOpcaoResposta->nrOpcao;

                $tbOpcaoRespostaVariavel = new tbOpcaoRespostaVariavel();
                $buscaOpcaoRespostaVariavel = $tbOpcaoRespostaVariavel->buscar(array('nrFormDocumento = ?' => $nrFormDocumentoCriterio,
                                                                                      'nrVersaoDocumento = ?' => $nrVersaoDocumentoCriterio,
                                                                                      'nrPergunta = ?' => $nrPergunta,
                                                                                      'nrOpcao = ?' => $nrOpcao))->current();
                $buscaOpcaoRespostaVariavel->vlMinOpcao = $vlMinOpcao;
                $buscaOpcaoRespostaVariavel->vlMaxOpcao = $vlMaxOpcao;
                $buscaOpcaoRespostaVariavel->vlVariacaoOpcao = $vlVariacaoOpcao;
                $idOpcaoRespostaVariavel = $buscaOpcaoRespostaVariavel->save();

                parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "/cadastraredital/criteriosavaliacao?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idUsuario={$idusuario}&idEdital=$idEdital", "CONFIRM");
            } // fim if altera��o

            if (isset($_POST['acaoD'])) { // altera��o e exclus�o
                $nrFormDocumento = $_POST['nrFormDocumento'];
                $nrVersaoDocumento = $_POST['nrVersaoDocumento'];
                $nrPergunta = $_POST['nrPergunta'];
                $idEdital = $_POST['idEdital'];

                // busca as informacoes da pergunta
                $dadosBuscaTbPergunta = array('rv.nrPergunta = ?' => $nrPergunta);
                $dadosTbpergunta = new tbPergunta();
                $buscarDadosTbPergunta = $dadosTbpergunta->buscarDados($dadosBuscaTbPergunta, 'pd.nrOrdemPergunta')->toArray();

                if ($_POST['acaoD'] == "0") { // busca os dados para efetuar a altera��o
                    $this->view->dadosCriterios = $buscarDadosTbPergunta;
                } else { // efetua a exclus�o
                    $nrFormDocumentoCriterio   = $buscarDadosTbPergunta[0]['nrFormDocumento'];
                    $nrVersaoDocumentoCriterio = $buscarDadosTbPergunta[0]['nrVersaoDocumento'];

                    $tbOpcaoRespostaVariavel = new tbOpcaoRespostaVariavel();
                    $buscaOpcaoRespostaVariavel = $tbOpcaoRespostaVariavel->buscar(array('nrFormDocumento = ?' => $nrFormDocumentoCriterio,
                                                                                         'nrVersaoDocumento = ?' => $nrVersaoDocumentoCriterio,
                                                                                         'nrPergunta = ?' => $nrPergunta))->current();
                    $buscaOpcaoRespostaVariavel->delete();

                    $tbOpcaoResposta = new tbOpcaoResposta();
                    $buscaOpcaoResposta = $tbOpcaoResposta->buscar(array('nrFormDocumento = ?' => $nrFormDocumentoCriterio,
                                                                         'nrVersaoDocumento = ?' => $nrVersaoDocumentoCriterio,
                                                                         'nrPergunta = ?' => $nrPergunta))->current();
                    $buscaOpcaoResposta->delete();

                    $tbPerguntaFormDocto = new tbPerguntaFormDocto();
                    $buscaPerguntaFormDocto = $tbPerguntaFormDocto->buscar(array('nrFormDocumento = ?' => $nrFormDocumentoCriterio,
                                                                                 'nrVersaoDocumento = ?' => $nrVersaoDocumentoCriterio,
                                                                                 'nrPergunta = ?' => $nrPergunta))->current();
                    $buscaPerguntaFormDocto->delete();


                    $tbPergunta = new tbPergunta();
                    $buscaPergunta = $tbPergunta->buscar(array('nrPergunta = ?' => $nrPergunta))->current();
                    $buscaPergunta->delete();


                    $tbFormDocumento = new tbFormDocumento();
                    $buscaFormDocumento = $tbFormDocumento->buscar(array('nrFormDocumento = ?' => $nrFormDocumentoCriterio,
                                                                         'nrVersaoDocumento = ?' => $nrVersaoDocumentoCriterio))->current();
                    $buscaFormDocumento->delete();

                    parent::message("Exclus&atilde;o realizada com sucesso!", "/cadastraredital/criteriosavaliacao?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idUsuario={$idusuario}&idEdital={$idEdital}", "CONFIRM");
                }
            } // fim if exclus�o
        } // fim post
    } // fecha m�todo criteriosavaliacaoAction()



    /**
     * M�todo para cadastro/busca de formas de pagamento
     */
    public function formapagamentoAction()
    {
        $auth = Zend_Auth::getInstance();// instancia da autentica??o
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idorgao = $auth->getIdentity()->usu_orgao;
        $usu_identificacao = $auth->getIdentity()->usu_identificacao;
        $this->view->idUsuario = $idusuario;

        $post                          =   Zend_Registry::get('post');

        $operacao = $post->operacao;
        $nrPergunta = $post->nrPergunta;

        $get                           =   Zend_Registry::get('get');

        $nrFormDocumento = $get->nrFormDocumento;
        $this->view->nrFormDocumento = $nrFormDocumento;
        $nrVersaoDocumento = $get->nrVersaoDocumento;
        $this->view->nrVersaoDocumento = $nrVersaoDocumento;
        $idEdital = $get->idEdital;
        $this->view->idEdital  = $idEdital;

        $tbFormDocumentoDAO =   new tbFormDocumento();
        $edital                 =   $tbFormDocumentoDAO->buscar(array('idEdital = ?'=>$_GET['idEdital']));
        $this->view->nmEdital   =   $edital[0]->nmFormDocumento;

        //if ($_POST) {
        $objNrformDocumentoDAO = new tbFormDocumento();
        $objNrformDocumento = $objNrformDocumentoDAO->buscaNrFormDocumento($idEdital);

        $buscaPIEdital = new Edital();
        $dadosPiEdital = $buscaPIEdital->buscaEditalFormDocumento($idusuario, $idEdital);

        foreach ($dadosPiEdital as $PiEdital) {
            $idPi = $PiEdital['idAti'];
            $idOrgao = $PiEdital['idOrgao'];
        }

        $date = getdate();
        $data = $date['year'];
        $buscarPI = new SimcAtividade();
        $pi = $buscarPI->buscarPI(array('o.Codigo = ?'=>$idOrgao, 'atianopi = ?'=> $data, 'ati.atistatuspi = ?'=>"A"));

        foreach ($pi as $dadosPI) {
            $acaid = $dadosPI['acaid'];
            $atiseqpi = $dadosPI['atiseqpi'];
            $atiprojeto = $dadosPI['_atiprojeto'];
            $atiid = $dadosPI['atiid'];
            $orgao = $dadosPI['secretaria'];
        }

        $dadosPI = $buscarPI->buscarValoresPI(array('aca.acaid = ?'=>$acaid, 'atiseqpi = ?'=>$atiseqpi, '_atiprojeto = ?'=>$atiprojeto, 'atiid = ?'=>$atiid, 'ati.uexid = ?'=>$orgao));
        $this->view->dadosPI = $dadosPI;

        if (isset($objNrformDocumento)) {
            $nrFormDocumentoPagamento = $objNrformDocumento['nrFormDocumento'];
        } else {
            $dados = array(
                'idEdital'=>                $idEdital,
                'nrVersaoDocumento'=>       $nrVersaoDocumento,
                'nmFormDocumento'=>         'Pagamento de Edital',
                'dsFormDocumento'=>         'Pagamento de Edital',
                'idClassificaDocumento'=>   '24',
                'dtCadastramento'=>         new Zend_Db_Expr('GETDATE()'),
                'stFormDocumento'=>         'A',
            );
            $inserir = new tbFormDocumento();
            $nrFormDocumentoPagamento = $inserir->inserir($dados);
            $nrFormDocumentoPagamento = $nrFormDocumentoPagamento['nrFormDocumento'];
        }

        $this->view->nrFormDocumentoPagamento = $nrFormDocumentoPagamento;
//        $ListaPerguntasDao = new tbPerguntaFormDocto();
//        $listaPerguntas = $ListaPerguntasDao->listaPerguntas($nrFormDocumento, $nrVersaoDocumento);
//        $this->view->listaPerguntas = $listaPerguntas;

//        if($nrPergunta){
//            $FormaPagamentoDao = new tbPergunta();
//            $pergunta = $FormaPagamentoDao->procurarPergunta($nrPergunta);
//            $this->view->pergunta = $pergunta['dsPergunta'];
//        }

        if (empty($nrPergunta) and $operacao == 'inserirPergunta') {
            $nrFormDocumento = $nrFormDocumentoPagamento;
            $nrVersaoDocumento = $_POST['nrVersaoDocumento'];
            $dsPergunta = $_POST['dsPergunta'];
            $idEdital = $_POST['idEdital'];
            $dtCadastramento = new Zend_Db_Expr('GETDATE()');

            $agentes = new Agente_Model_DbTable_Agentes();

            $buscarAgente = $agentes->buscar(array("CNPJCPF = ?" => $usu_identificacao))->current()->toArray();

            $idAgente = $buscarAgente['idAgente'];

            $dadosPergunta = array('stTipoRespPergunta' => "O",
                'dtCadastramento' => $dtCadastramento,
                'dsPergunta' => $dsPergunta,
                'idPessoaCadastro' => $idAgente);

            $tbPerguntaDao = new tbPergunta();
            $nrPergunta = $tbPerguntaDao->inserir($dadosPergunta);

            $this->view->nrPergunta = $nrPergunta;
            $pergunta = $tbPerguntaDao->procurarPergunta($nrPergunta);
            $this->view->pergunta = $pergunta['dsPergunta'];

            $dadosPerguntaFormDocto = array('nrFormDocumento' => $nrFormDocumentoPagamento,
                                                'nrVersaoDocumento' => $nrVersaoDocumento,
                                                'nrPergunta' => $nrPergunta,
                                                'nrOrdemPergunta' => 1,
                                                'dsLabelPergunta' => $dsPergunta);

            $tbPerguntaFormDocto = new tbPerguntaFormDocto();
            $nrPerguntaFormDocto = $tbPerguntaFormDocto->inserir($dadosPerguntaFormDocto);

            //$_POST['dsPergunta'] = 0;
                //parent::message("Cadastro realizado com sucesso!", "/cadastraredital/formapagamento?nrPergunta={$nrPergunta}&nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}" , "ALERT");
        } elseif ($nrPergunta !='' and $operacao == 'alterarPergunta') {
            $nrPergunta = $post->nrPergunta;
            $dsPergunta = $post->dsPergunta;

            $data = array('dsPergunta'=>$dsPergunta);
            $where= 'nrPergunta ='.$nrPergunta;
            $alteraPerguntaDao = new tbPergunta();
            $alteraPergunta = $alteraPerguntaDao->update($data, $where);

            $this->view->nrPergunta = $nrPergunta;
            $this->view->pergunta = $dsPergunta;
        } else {
            $this->view->nrPergunta = $nrPergunta;
        }
        unset($_POST);
        //} // fim post
    } // fecha m�todo formapagamentoAction()



    public function listaformapagamentoAction()
    {
        $this->_helper->layout->disableLayout();

        $post                           =   Zend_Registry::get('post');

        $nrPergunta = $post->nrPergunta;
        $this->view->nrPergunta = $nrPergunta;
        $nrFormDocumento = $post->nrFormDocumento;
        $nrFormDocumentoPagamento = $post->nrFormDocumentoPagamento;
        $this->view->nrFormDocumento = $nrFormDocumento;
        $nrVersaoDocumento = $post->nrVersaoDocumento;
        $this->view->nrVersaoDocumento = $nrVersaoDocumento;
        $this->view->idEdital  = $post->idEdital;
        ;
        $ListaPerguntasDao = new tbPerguntaFormDocto();
        $listaPerguntas = $ListaPerguntasDao->listaPerguntas($nrFormDocumentoPagamento, $nrVersaoDocumento);

        $auth = Zend_Auth::getInstance();// instancia da autentica??o
        $idusuario = $auth->getIdentity()->usu_codigo;
        $this->view->idUsuario = $idusuario;


        $this->view->listaPerguntas = $listaPerguntas;
    } // fecha m�todo listaformapagamentoAction()



    public function listaparcelasAction()
    {
        $this->_helper->layout->disableLayout();

        $post                           =   Zend_Registry::get('post');

        $idEdital = $post->idEdital;
        $this->view->idEdital = $idEdital;

        $idUsuario = $post->idUsuario;
        $this->view->idUsuario = $idUsuario;

        $nrPergunta = $post->nrPergunta;
        $this->view->nrPergunta = $nrPergunta;

        $nrFormDocumento = $post->nrFormDocumentoPagamento;
        $this->view->nrFormDocumento = $nrFormDocumento;

        $nrVersaoDocumento = $post->nrVersaoDocumento;
        $this->view->nrVersaoDocumento = $nrVersaoDocumento;

        $listaFormaPagamentoDAO = new tbOpcaoResposta();
        $listaFormaPagamento = $listaFormaPagamentoDAO->buscarparcelas($nrFormDocumento, $nrVersaoDocumento, $nrPergunta);
        $this->view->listaFormaPagamento = $listaFormaPagamento;
    } // fecha m�todo listaparcelasAction()



    /**
     * M�todo para cadastrar/buscar as op��es de forma de pagamento
     */
    public function formapagamentoopcoesAction()
    {
        $this->_helper->layout->disableLayout();

        $post =  Zend_Registry::get('post');

        if (!$post->operacao) {
            $post =  Zend_Registry::get('get');
        }
        $operacao = $post->operacao;
        $nrFormDocumento = $post->nrFormDocumentoPagamento;
        $nrVersaoDocumento = $post->nrVersaoDocumento;
        $nrPergunta = $post->nrPergunta;
        $nrOpcao = $post->nrOpcao;
        $dsPergunta = $post->dsPergunta;
        $dsPagamento  = $post->dsPagamento;
        $dsOpcao = $post->valorApoio;
        $qtdParcelas = $post->qtdParcelas;
        $vlParcela = $post->vlParcela;
        $nrParcelaPrestConta = $post->nrParcelaPrestConta;
        $idEdital = $post->idEdital;
        $idUsuario = $post->idUsuario;
        $nrFormDocumentoPagamento = $post->nrFormDocumentoPagamento;


        switch ($operacao) {
            case "inserirOpcao":
                $this->_helper->layout->disableLayout();
                $dsPagamento = $post->dsPagamento;
                $verificar = true;
                $dadosFormaPagamento = array(
                                            'nrFormDocumento'=>$nrFormDocumento,
                                            'nrVersaoDocumento'=>$nrVersaoDocumento,
                                            'nrPergunta'=>$nrPergunta,
                                            'dsOpcao'=>$dsOpcao,
                                            'dsLabelResposta'=>$dsPagamento,
                                            'stTipoObjetoPgr'=>'RB'
                                            );
                $insereFormaPagamentoDAO = new tbOpcaoResposta();
                $insereFormaPagamento = $insereFormaPagamentoDAO->inserir($dadosFormaPagamento);
                if ($insereFormaPagamento) {
                    $nrOpcao = $insereFormaPagamento['nrOpcao'];
                    foreach ($vlParcela as $k=>$val) {
                        $val = preg_replace("#\.#", "", $val);
                        $val = preg_replace("#\,#", ".", $val);
                        $vlParcela = $val;
                        if ($k==0) {
                            $nrParcelaPrestConta = null;
                        } elseif ($nrParcelaPrestConta[$k-1]=='') {
                            $nrParcelaPrestConta = null;
                        } else {
                            $nrParcelaPrestConta = $nrParcelaPrestConta[$k-1];
                        }
                        $nrParcela = $k+1;

                        $dadosInsereParcelas = array(
                                                    'nrFormDocumento'=>$nrFormDocumento,
                                                    'nrVersaoDocumento'=>$nrVersaoDocumento,
                                                    'nrPergunta'=>$nrPergunta,
                                                    'nrOpcao'=>$nrOpcao,
                                                    'nrParcela'=>$nrParcela,
                                                    'vlParcela'=>$vlParcela,
                                                    'nrParcelaPrestConta'=>$nrParcelaPrestConta
                                                );

                        $InsereParcelasDao = new tbPagamento();
                        $InsereParcelas = $InsereParcelasDao->inserir($dadosInsereParcelas);
                        if (!$InsereParcelas) {
                            $verificar = false;
                        }
                    }
                    if ($verificar) {
                        parent::message("Cadastro realizado com sucesso!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "CONFIRM");
//                        $this->_helper->json(array("retorno"=>"INSERIR","mensagem"=>"Forma de Pagamento inclu&iacute;da com sucesso!"));
                    } else {
                        parent::message("Erro ao tentar incluir as parcelas!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                        $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar inserir as Parcelas."));
                    }
                } else {
                    parent::message("Erro ao tentar incluir as parcelas!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                    $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar inserir a Forma de Pagamento."));
                }

                break;
            case "pesquisa":
                $this->_helper->layout->disableLayout();
                $pesquisaFormaPagamentoDao = new tbOpcaoResposta();
                $valorArray = $pesquisaFormaPagamentoDao->pesquisaFormaPagamento($nrFormDocumento, $nrVersaoDocumento, $nrPergunta, $nrOpcao);

                foreach ($valorArray as $key => $value) {
                    $valorArray[$key] = Conversor::iso88591ParaUtf8_Array($value);
                }
                echo Conversor::jsonEncodeParaIso88591($valorArray);

                //$this->pesquisaFormaPagamento($this->formaPagamento);

                break;
            case "alterar":
                $alteraFormaPagamentoDao = new tbOpcaoResposta();
//                update BDCORPORATIVO.scQuiz.tbOpcaoResposta set
//                    dsOpcao = ? ,dsLabelResposta = ?
//                    where nrFormDocumento = ? and nrVersaoDocumento = ? and nrPergunta = ? and nrOpcao = ?
                $data = array(
                                'dsOpcao'=>$dsOpcao,
                                'dsLabelResposta'=>$dsPagamento
                            );
                $where = 'nrFormDocumento = '.$nrFormDocumento.' and nrVersaoDocumento = '.$nrVersaoDocumento.' and nrPergunta = '.$nrPergunta.' and nrOpcao = '.$nrOpcao;
                $alteraFormaPagamento = $alteraFormaPagamentoDao->update($data, $where);

                $dsPagamento = $post->dsPagamento;
                if ($alteraFormaPagamento) {
                    $excluirParcelasDAO = new tbPagamento();
                    $where = array('nrFormDocumento = ?' => $nrFormDocumento,
                                   'nrVersaoDocumento = ?' => $nrVersaoDocumento,
                                   'nrPergunta = ?' => $nrPergunta,
                                   'nrOpcao = ?' =>$nrOpcao);
                    $excluirParcelas = $excluirParcelasDAO->buscar($where);

                    if (count($excluirParcelas)>0) {
                        $excluirParcelasDAO->delete($where);
                        $vlParcela = $post->vlParcela;
                        $nrParcelaPrestConta = $post->nrParcelaPrestConta;
                        $verificar = true;
                        foreach ($vlParcela as $k=>$val) {
//
                            $val = preg_replace("#\.#", "", $val);
                            $val = preg_replace("#\,#", ".", $val);
                            $vlParcela = $val;
//                            $formaPagamento->setVlParcela($val);
                            if ($k==0) {
                                $nrParcelaPrestConta=null;
                            } elseif ($nrParcelaPrestConta[$k-1]=='') {
                                $nrParcelaPrestConta=null;
                            } else {
                                $nrParcelaPrestConta = $nrParcelaPrestConta[$k-1];
                            }
                            $nrParcela= $k+1;
                            $dadosPagamento = array(
                                                    'nrFormDocumento'=>$nrFormDocumento,
                                                    'nrVersaoDocumento'=>$nrVersaoDocumento,
                                                    'nrPergunta'=>$nrPergunta,
                                                    'nrOpcao'=>$nrOpcao,
                                                    'nrParcela'=>$nrParcela,
                                                    'vlParcela'=>$vlParcela,
                                                    'nrParcelaPrestConta'=>$nrParcelaPrestConta
                                                    );

                            $tbPagamentoDao = new tbPagamento();
                            $tbPagamento = $tbPagamentoDao->inserir($dadosPagamento);
                            if (!$tbPagamento) {
                                $verificar = false;
                            }
                        }
                        if ($verificar) {
                            parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "CONFIRM");
//                            $this->_helper->json(array("retorno"=>"ALTERAR","mensagem"=>"Forma de Pagamento alterada com sucesso!"));
                        } else {
                            parent::message("Erro ao tentar incluir as parcelas!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                            $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar cadastrar as Parcelas."));
                        }
                    } else {
                        parent::message("Erro ao tentar incluir as parcelas!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                        $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar excluir as Parcelas."));
                    }
                } else {
                    parent::message("Erro ao tentar alterar a forma de pagamento!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                    $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar alterar a Forma de Pagamento."));
                }
                break;
            case "excluir":

                $excluirParcelasDAO = new tbPagamento();

                $where = array('nrFormDocumento = ?' => $nrFormDocumentoPagamento,
                               'nrVersaoDocumento = ?' => $nrVersaoDocumento,
                               'nrPergunta = ?' => $nrPergunta,
                               'nrOpcao = ?' =>$nrOpcao);

                $excluirParcelas = $excluirParcelasDAO->buscar($where);

                if (count($excluirParcelas)>0) {
                    $excluirParcelasDAO->delete($where);

                    $excluirformapagamentoDAO = new tbOpcaoResposta();
                    $excluirformapagamento = $excluirformapagamentoDAO->buscar($where);

                    if (count($excluirformapagamento)) {
                        $excluirformapagamentoDAO->delete($where);

                        parent::message("Exclus&atilde;o realizada com sucesso!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "CONFIRM");

//                        $this->_helper->json(array("retorno"=>"EXCLUIR","mensagem"=>"Forma de Pagamento excluida com sucesso!"));
                    } else {
                        parent::message("Erro ao tentar excluir a Forma de Pagamento!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                        $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar excluir a Forma de Pagamento."));
                    }
                } else {
                    parent::message("Erro ao tentar excluir as parcelas!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                    $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar excluir as Parcelas."));
                }
                break;
            case "excluirfp":
                //$post =  Zend_Registry::get('get');
                //$idEdital = $post->idEdital;
                //$idUsuario = $post->idUsuario;
                //$nrFormDocumento = $post->nrFormDocumentoPagamento;
                $this->_helper->layout->disableLayout();
                $listaOpcaoRespostaDAO = new tbOpcaoResposta();
                $listaOpcaoResposta = $listaOpcaoRespostaDAO->listaOpcaoResposta($nrFormDocumento, $nrVersaoDocumento, $nrPergunta);
                $verificar = true;
                if (isset($listaOpcaoResposta) && count($listaOpcaoResposta)>0) {
                    foreach ($listaOpcaoResposta as $OpcaoResposta) {
                        $nrOpcao = $OpcaoResposta->nrOpcao;
                        $excluirParcelasDAO = new tbPagamento();

                        $where = array('nrFormDocumento = ?' => $nrFormDocumento,
                                       'nrVersaoDocumento = ?' => $nrVersaoDocumento,
                                       'nrPergunta = ?' => $nrPergunta,
                                        'nrOpcao = ?' =>$nrOpcao);

                        $excluirParcelas = $excluirParcelasDAO->buscar($where);

                        if (count($excluirParcelas)>0) {
                            if ($excluirParcelasDAO->delete($where)) {
                                $excluiFormaPagamentoDAO = new tbOpcaoResposta();
                                $where = array('nrFormDocumento = ?' => $nrFormDocumento,
                                               'nrVersaoDocumento = ?' => $nrVersaoDocumento,
                                               'nrPergunta = ?' => $nrPergunta,
                                               'nrOpcao = ?' =>$nrOpcao);
                                $excluiFormaPagamento = $excluiFormaPagamentoDAO->buscar($where);

                                if (count($excluiFormaPagamento)>0) {
                                    $excluiFormaPagamentoDAO->delete($where);

                                    $verificar=true;
                                } else {
                                    $verificar=false;
                                    parent::message("Erro ao tentar excluir a Forma de Pagamento!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                                    $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar excluir a Forma de Pagamento."));
                                }
                            } else {
                                $verificar=false;
                                parent::message("Erro ao tentar excluir as parcelas!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                                $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar excluir as Parcelas."));
                            }
                        } else {
                            $verificar=true;
                        }
                    }
                }
                if ($verificar) {
                    $excluirPerguntaFormDoctoDAO = new tbPerguntaFormDocto();
                    $where = array('nrFormDocumento = ?' => $nrFormDocumento,
                                   'nrVersaoDocumento = ?' => $nrVersaoDocumento,
                                   'nrPergunta = ?' => $nrPergunta);
                    $excluirPerguntaFormDocto = $excluirPerguntaFormDoctoDAO->buscar($where);
                    if (count($excluirPerguntaFormDocto)>0) {
                        $excluirPerguntaFormDoctoDAO->delete($where);
                        parent::message("Exclus&atilde;o realizada com sucesso!", "/Cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "CONFIRM");
//                        $this->_helper->json(array("retorno"=>"EXCLUIR","mensagem"=>"Forma de Pagamento excluida com sucesso!"));
                    } else {
                        parent::message("Erro ao tentar excluir!", "/cadastraredital/formapagamento?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
//                        $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar excluir a PerguntaFormDocto."));
                    }
                }
                break;
            case "pesquisafp":
                $pesquisaPerguntaFpDao = new tbPergunta();
                $pesquisaPergunta = $pesquisaPerguntaFpDao->procurarPergunta($nrPergunta);
                $this->view->nrPergunta = $nrPergunta;
                $this->_helper->json(array('nrPergunta'=>$pesquisaPergunta->nrPergunta,'dsPergunta'=>  utf8_encode($pesquisaPergunta->dsPergunta)));
                break;
        }
    } // fecha m�todo formapagamentoopcoesAction()



    public function consultaralterareditaisAction()
    {
        $auth = Zend_Auth::getInstance();// instancia da autentica??o
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idorgao = $auth->getIdentity()->usu_orgao;
        $usu_identificacao = $auth->getIdentity()->usu_identificacao;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess?o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess?o
        $codOrgao = $GrupoAtivo->codOrgao; //  ?rg?o ativo na sess?o
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

        $dadosEdital = new Edital();
        $buscaEdital = $dadosEdital->buscaEditalFormDocumento($idusuario)->toArray();
        $this->view->buscaEdital = $buscaEdital;
    } // fecha m�todo consultaralterareditaisAction()



    public function propostacustomizavelAction()
    {
        $auth = Zend_Auth::getInstance();// instancia da autentica??o
        $idusuario = $auth->getIdentity()->usu_codigo;

        $nrFormDocumento    = $_GET['nrFormDocumento'];
        $idEdital           = $_GET['idEdital'];
        $nrVersaoDocumento  = $_GET['nrVersaoDocumento'];

        $tbFormDocumentoDAO =   new tbFormDocumento();

        $edital                 =   $tbFormDocumentoDAO->buscar(array('idEdital = ?'=>$_GET['idEdital']));
        $this->view->nmEdital   =   $edital[0]->nmFormDocumento;


        $this->view->nmEdital   =   $edital[0]->nmFormDocumento;

        $this->view->nrFormDocumento    = $nrFormDocumento;
        $this->view->idEdital           = $idEdital;
        $this->view->nrVersaoDocumento  = $nrVersaoDocumento;
        $this->view->idUsuario          = $idusuario;
    } // fecha m�todo propostacustomizavelAction()



    public function listaguiaAction()
    {
        $nrformDocumento = $_GET['nrFormDocumento'];
        $nrVersaoDocumento = $_GET['nrVersaoDocumento'];
        $idEdital = $_GET['idEdital'];

        $this->_helper->layout->disableLayout();
        $edital = $this->listaGuiaDigital($idEdital);
        $this->view->listaGuiasEdital   =   $edital;
        $auth = Zend_Auth::getInstance();// instancia da autentica??o
        if (isset($auth->getIdentity()->usu_codigo)) {
            $idUsuario = $auth->getIdentity()->usu_codigo;
            $this->view->idUsuario   =   $idUsuario;
        } else {
            $idUsuario = $auth->getIdentity()->IdUsuario;
            $this->view->idUsuario   =   $idUsuario;
        }
    } // fecha m�todo listaguiaAction()



    private function listaGuiaDigital($idEdital)
    {
        $tbFormDocumentoDAO =   new tbFormDocumento();

        $edital             =   $tbFormDocumentoDAO->buscar(array('idEdital = ?'=>$idEdital,
                                                                'idClassificaDocumento = ?'=>23));

        return $edital;
    } // fecha m�todo listaGuiaDigital($idEdital)



    public function operacoescustomizavelAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);

        $tbFormDocumentoDAO =   new tbFormDocumento();
        $post       = Zend_Registry::get('post');
        $get        = Zend_Registry::get('get');

        if (!$post->operacao) {
            $post =  Zend_Registry::get('get');
        }

        $idEdital           =   $post->idEdital;
        $nrVersaoDocumento  =   $post->nrVersaoDocumento;
        $nmFormDocumento    =   $post->nmFormDocumento;
        $dsFormDocumento    =   $post->dsFormDocumento;
        $nrFormDocumento    =   $post->nrFormDocumento;
        $operacao           =   $post->operacao;
        $nrPergunta         =   $post->nrPergunta;
        $nrOrdemPergunta    =   $post->nrOrdemPergunta;
        $dsPergunta         =   $post->dsPergunta;
        $dsLabelPergunta    =   $post->dsLabelPergunta;
        $stTipoObjetoPgr    =   $post->stTipoObjetoPgr;
        $dsOpcaoR           =   $post->dsOpcaoR;
        $justificativa      =   $post->justificativa;
        $nrOpcao            =   $post->nrOpcao;
        $idPreProjeto       =   $post->idPreProjeto;

        $auth = Zend_Auth::getInstance();// instancia da autentica??o
        if (isset($auth->getIdentity()->usu_codigo)) {
            $idusuario = $auth->getIdentity()->usu_codigo;
        } else {
            $idusuario = $auth->getIdentity()->IdUsuario;
        }
        $where = array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento);
        switch ($operacao) {
            case "inserir":
                $result = $tbFormDocumentoDAO->inserir(array(
                                                'idEdital'              =>  $idEdital,
                                                'nrVersaoDocumento'     =>  $nrVersaoDocumento,
                                                'nmFormDocumento'       =>  $nmFormDocumento,
                                                'dsFormDocumento'       =>  $dsFormDocumento,
                                                'idClassificaDocumento' =>  23,
                                                'dtCadastramento'       =>  new Zend_Db_Expr('GETDATE()'),
                                                'stFormDocumento'       =>  'A'
                    ));

                if ($result) {
                    parent::message("Cadastro realizado com sucesso!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "CONFIRM");
//                    $this->_helper->json(array("retorno"=>"INSERIR","mensagem"=>"Guia de edital inclu&iacute;da com sucesso!"));
                } else {
                    $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar inserir a guia de edital."));
                }
                break;
            case "pesquisa":
                $edital             =   $tbFormDocumentoDAO->buscar($where);
                $result = array();
                foreach ($edital as $val) {
                    $result['nmFormDocumento']      = utf8_encode($val->nmFormDocumento);
                    $result['nrFormDocumento']      = $val->nrFormDocumento;
                    $result['nrVersaoDocumento']    = $val->nrVersaoDocumento;
                    $result['dsFormDocumento']      = utf8_encode($val->dsFormDocumento);
                }
                $this->_helper->json($result);
                break;
            case "alterar":
                $result = $tbFormDocumentoDAO->update(array(
                                                'nmFormDocumento'       =>  $nmFormDocumento,
                                                'dsFormDocumento'       =>  $dsFormDocumento,
                          ), $where);
                if ($result) {
                    parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "CONFIRM");
//                    $this->_helper->json(array("retorno"=>"ALTERAR","mensagem"=>"Guia de edital alterada com sucesso!"));
                } else {
                    parent::message("Erro ao tentar alterar a guia de edital!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "ALERT");
//                    $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar alterar a guia de edital."));
                }
                break;
            case "excluir":
                $tbPerguntaDAO          =   new tbPergunta();
                $tbPerguntaFormDoctoDAO =   new tbPerguntaFormDocto();
                $tbOpcaoRespostaDAO     =   new tbOpcaoResposta();
                $tbFormDocumento        =   new tbFormDocumento();

                $nrFormDocumento = $_GET['nrFormDocumento'];
                $nrVersaoDocumento = $_GET['nrVersaoDocumento'];
                $nrFormDocURL = $_GET['nrFormDocURL'];

                $where = array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento);
                $listaPerguntaFormDocto = $tbPerguntaFormDoctoDAO->buscar($where);

                $tbOpcaoRespostaDAO->delete($where);
                $tbPerguntaFormDoctoDAO->delete($where);
                $tbFormDocumento->delete($where);

                if (is_object($listaPerguntaFormDocto) && count($listaPerguntaFormDocto) > 0) {
                    foreach ($listaPerguntaFormDocto as $pergunta) {
                        $tbPerguntaDAO->delete(array('nrPergunta = ?'=>$pergunta->nrPergunta));
                    }
                }

                $idEdital = $_GET['idEdital'];
                $idusuario = $_GET['idUsuario'];

                $FormExclusao = $tbFormDocumento->buscar($where);

                if (count($FormExclusao) > 0) {
                    parent::message("Erro ao tentar excluir a guia de edital!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocURL}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "ALERT");
//                        $this->_helper->json(array("retorno"=>"EXCLUIR","mensagem"=>"Guia de edital excluida com sucesso!"));
                } else {
                    parent::message("Exclus&atilde;o realizada com sucesso!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocURL}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "CONFIRM");
//                        $this->_helper->json(array("retorno"=>"ERRO","mensagem"=>"Erro ao tentar excluir a guia de edital."));
                }
                break;

            case 'tipoIT':
                //$this->questionario->setDsRespostaSubj(utf8_decode($_POST["resposta_{$this->questionario->getNrOpcao()}"]));
                $info = array(
                            'idPreProjeto'	=>  $idPreProjeto,
                            'idUsuario'		=>  $idusuario,
                            'nrFormDocumento'	=>  $nrFormDocumento,
                            'nrOpcao'		=>  $nrOpcao,
                            'nrPergunta'	=>  $nrPergunta,
                            'nrVersaoDocumento'	=>  $nrVersaoDocumento,
                            'dsRespostaSubj'    =>  utf8_decode($_POST["resposta_{$nrOpcao}"]),
                            'operacao'          =>  $operacao
                            );

                $this->cadastraAtualizaRespostaQuestoes($info);
                break;
            case 'tipoTA':
                $info = array(
                            'idPreProjeto'	=>  $idPreProjeto,
                            'idUsuario'		=>  $idusuario,
                            'nrFormDocumento'	=>  $nrFormDocumento,
                            'nrOpcao'		=>  $nrOpcao,
                            'nrPergunta'	=>  $nrPergunta,
                            'nrVersaoDocumento'	=>  $nrVersaoDocumento,
                            'dsRespostaSubj'    =>  utf8_decode($_POST["resposta_{$nrOpcao}"]),
                            'operacao'          =>  $operacao
                            );

                $this->cadastraAtualizaRespostaQuestoes($info);
                break;
            case 'tipoDT':
                $info = array(
                            'idPreProjeto'	=>  $idPreProjeto,
                            'idUsuario'		=>  $idusuario,
                            'nrFormDocumento'	=>  $nrFormDocumento,
                            'nrOpcao'		=>  $nrOpcao,
                            'nrPergunta'	=>  $nrPergunta,
                            'nrVersaoDocumento'	=>  $nrVersaoDocumento,
                            'dsRespostaSubj'    =>  utf8_decode($_POST["resposta_{$nrOpcao}"]),
                            'operacao'          =>  $operacao
                            );

                $this->cadastraAtualizaRespostaQuestoes($info);
                break;
            case 'tipoNR':
                $info = array(
                            'idPreProjeto'	=>  $idPreProjeto,
                            'idUsuario'		=>  $idusuario,
                            'nrFormDocumento'	=>  $nrFormDocumento,
                            'nrOpcao'		=>  $nrOpcao,
                            'nrPergunta'	=>  $nrPergunta,
                            'nrVersaoDocumento'	=>  $nrVersaoDocumento,
                            'dsRespostaSubj'    =>  utf8_decode($_POST["resposta_{$nrOpcao}"]),
                            'operacao'          =>  $operacao
                            );

                $this->cadastraAtualizaRespostaQuestoes($info);
                break;
            case 'tipoCK':

                if (isset($_POST["resposta_{$nrOpcao}"])) {
                    $dsResposta = $_POST["resposta_{$nrOpcao}"];
                } else {
                    $dsResposta = "";
                }
                $info = array(
                            'idPreProjeto'	=>  $idPreProjeto,
                            'idUsuario'		=>  $idusuario,
                            'nrFormDocumento'	=>  $nrFormDocumento,
                            'nrOpcao'		=>  $nrOpcao,
                            'nrPergunta'	=>  $nrPergunta,
                            'nrVersaoDocumento'	=>  $nrVersaoDocumento,
                            'dsRespostaSubj'    =>  utf8_decode($dsResposta),
                            'operacao'          =>  $operacao
                            );


                if ($info['dsRespostaSubj'] != '') {
                    $this->cadastraAtualizaRespostaQuestoes($info);
                } else {
                    $where = array(
                        'nrFormDocumento = ?'   =>  $info['nrFormDocumento'],
                        'nrVersaoDocumento = ?' =>  $info['nrVersaoDocumento'],
                        'nrPergunta = ?'        =>  $info['nrPergunta'],
                        'idProjeto = ?'         =>  $info['idPreProjeto'],
                        'idPessoaCadastro = ?'  =>  $info['idUsuario'],
                        'nrOpcao = ?'           =>  $info['nrOpcao']
                     );
                    $tbRespostaDAO = new tbResposta();
                    $resposta = $tbRespostaDAO->buscar($where);
                    if (is_object($resposta) and $resposta->count() > 0) {
                        if ($tbRespostaDAO->delete($where)) {
                            $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                        } else {
                            $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro excluir '.$info['nrOpcao'].'.')));
                        }
                    }
                }


                break;
            case 'tipoIC':

                if (isset($_POST["resposta_{$nrPergunta}_{$nrOpcao}"])) {
                    $dsResposta = $_POST["resposta_{$nrPergunta}_{$nrOpcao}"];
                } else {
                    $dsResposta = "";
                }

                $info = array(
                            'idPreProjeto'	=>  $idPreProjeto,
                            'idUsuario'		=>  $idusuario,
                            'nrFormDocumento'	=>  $nrFormDocumento,
                            'nrOpcao'		=>  $nrOpcao,
                            'nrPergunta'	=>  $nrPergunta,
                            'nrVersaoDocumento'	=>  $nrVersaoDocumento,
                            'dsRespostaSubj'    =>  utf8_decode($dsResposta),
                            'operacao'          =>  $operacao
                            );
                if ($info['dsRespostaSubj'] != '') {
                    $this->cadastraAtualizaRespostaQuestoes($info);
                } else {
                    $where = array(
                        'nrFormDocumento = ?'   =>  $info['nrFormDocumento'],
                        'nrVersaoDocumento = ?' =>  $info['nrVersaoDocumento'],
                        'nrPergunta = ?'        =>  $info['nrPergunta'],
                        'idProjeto = ?'         =>  $info['idPreProjeto'],
                        'idPessoaCadastro = ?'  =>  $info['idUsuario'],
                        'nrOpcao = ?'           =>  $info['nrOpcao']
                     );
                    $tbRespostaDAO = new tbResposta();
                    $resposta = $tdRespostaDAO->buscar($where);
                    if (is_object($resposta) and $resposta->count() > 0) {
                        if ($tbRespostaDAO->delete($where)) {
                            $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                        } else {
                            $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro excluir '.$info['nrOpcao'].'.')));
                        }
                    }
                }
                break;
            case 'tipoCB':

                if (isset($_POST["resposta_{$nrPergunta}"])) {
                    $nrOpcao = $_POST["resposta_{$nrPergunta}"];
                } else {
                    $nrOpcao = "";
                }

                $tbOpcaoRespostaDAO = new tbOpcaoResposta();

                $where  =   array(
                                'nrFormDocumento = ?'   =>  $nrFormDocumento,
                                'nrVersaoDocumento = ?' =>  $nrVersaoDocumento,
                                'nrPergunta = ?'        =>  $nrPergunta,
                                'nrOpcao = ?'           =>  $nrOpcao
                            );

                $resp       =   $tbOpcaoRespostaDAO->buscar($where);
                $info = array(
                            'idPreProjeto'	=>  $idPreProjeto,
                            'idUsuario'		=>  $idusuario,
                            'nrFormDocumento'	=>  $nrFormDocumento,
                            'nrOpcao'		=>  $nrOpcao,
                            'nrPergunta'	=>  $nrPergunta,
                            'nrVersaoDocumento'	=>  $nrVersaoDocumento,
                            'dsRespostaSubj'    =>  $resp[0]->dsOpcao,
                            'operacao'          =>  $operacao
                            );

                $this->cadastraAtualizaRespostaQuestoes($info);
                break;
            case 'tipoRB':

                if (isset($_POST["resposta_{$nrPergunta}"])) {
                    $nrOpcao = $_POST["resposta_{$nrPergunta}"];
                } else {
                    $nrOpcao = "";
                }

                $tbOpcaoRespostaDAO = new tbOpcaoResposta();

                $where  =   array(
                                'nrFormDocumento = ?'   =>  $nrFormDocumento,
                                'nrVersaoDocumento = ?' =>  $nrVersaoDocumento,
                                'nrPergunta = ?'        =>  $nrPergunta,
                                'nrOpcao = ?'           =>  $nrOpcao
                            );

                $resp       =   $tbOpcaoRespostaDAO->buscar($where);
                $info = array(
                            'idPreProjeto'	=>  $idPreProjeto,
                            'idUsuario'		=>  $idusuario,
                            'nrFormDocumento'	=>  $nrFormDocumento,
                            'nrOpcao'		=>  $nrOpcao,
                            'nrPergunta'	=>  $nrPergunta,
                            'nrVersaoDocumento'	=>  $nrVersaoDocumento,
                            'dsRespostaSubj'    =>  $resp[0]->dsOpcao,
                            'operacao'          =>  $operacao
                            );

                $this->cadastraAtualizaRespostaQuestoes($info);
                break;
            case 'tipoIR':

                if (isset($_POST["resposta_{$nrPergunta}"])) {
                    $nrOpcao = $_POST["resposta_{$nrPergunta}"];
                } else {
                    $nrOpcao = "";
                }
                $dsRespostaSubj     =   utf8_decode($_POST["resposta_{$nrPergunta}_{$nrOpcao}"]);
                $tbOpcaoRespostaDAO = new tbOpcaoResposta();

                if (empty($dsRespostaSubj)) {
                    $where  =   array(
                                'nrFormDocumento = ?'   =>  $nrFormDocumento,
                                'nrVersaoDocumento = ?' =>  $nrVersaoDocumento,
                                'nrPergunta = ?'        =>  $nrPergunta,
                                'nrOpcao = ?'           =>  $nrOpcao
                            );

                    $resp           =   $tbOpcaoRespostaDAO->buscar($where);
                    $dsRespostaSubj =   $resp[0]->dsOpcao;
                }


                $info = array(
                            'idPreProjeto'	=>  $idPreProjeto,
                            'idUsuario'		=>  $idusuario,
                            'nrFormDocumento'	=>  $nrFormDocumento,
                            'nrOpcao'		=>  $nrOpcao,
                            'nrPergunta'	=>  $nrPergunta,
                            'nrVersaoDocumento'	=>  $nrVersaoDocumento,
                            'dsRespostaSubj'    =>  $dsRespostaSubj,
                            'operacao'          =>  $operacao
                            );

                $this->cadastraAtualizaRespostaQuestoes($info);
                break;
            case 'ordenar':
                $tbPerguntaFormDoctoDAO =   new tbPerguntaFormDocto();
                $data   =   array('nrOrdemPergunta'=>$nrOrdemPergunta);
                $where  =   array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento,'nrPergunta = ?'=>$nrPergunta);

                $retorno = $tbPerguntaFormDoctoDAO->update($data, $where);
                if ($retorno) {
                    $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Ordena??o Salva.')));
                } else {
                    $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro Ordena&ccedil;&atilde;o Perguntas.'.$retorno)));
                }
                break;
            /*case 'ordenarOpcao':
                if($this->dao->alterarPosicaoOpcao($this->questionario))
                    $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Ordena??o Salva.')));
                else
                    $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro Ordena??o Opcao.')));
                break;*/
            case 'perguntas':
                $dsPergunta = $get->term;

                $palavras = explode(" ", $dsPergunta);
                $where = array();
                foreach ($palavras as $value) {
                    if (trim($value)) {
                        $where[' dsPergunta like ? ']='%'.utf8_decode($value).'%';
                    }
                }
                $tbPerguntaDAO = new tbPergunta();

                $resposta = $tbPerguntaDAO->buscar($where);
                $retorno = array();
                foreach ($resposta as $pergunta) {
                    $retorno[] = utf8_encode($pergunta->dsPergunta);
                }
                $this->_helper->json($retorno);
                break;
            case 'cadastroPergunta':
//            	xd($_POST);
                $tbPerguntaDAO          =   new tbPergunta();
                $tbPerguntaFormDoctoDAO =   new tbPerguntaFormDocto();
                $tbOpcaoRespostaDAO     =   new tbOpcaoResposta();
                $auth = Zend_Auth::getInstance();// instancia da autentica??o
                $idusuario = $auth->getIdentity()->usu_codigo;

                $dados          =   array(
                                        'stTipoRespPergunta'=>  $this->stTipoRespPergunta,
                                        'dsPergunta'        =>  $dsPergunta,
                                        'idPessoaCadastro'  =>  $idusuario,
                                        'dtCadastramento'   =>  new Zend_Db_Expr('GETDATE()')
                                    );

                $nrPergunta = $tbPerguntaDAO->inserir($dados);
                if ($nrPergunta) {
                    $questao                =   $tbPerguntaDAO->montarQuestionario($nrFormDocumento, $nrVersaoDocumento);
                    $nrOrdemPergunta        =   (count($questao)+1);
                    $dados                  =   array(
                                                    'nrFormDocumento'   =>  $nrFormDocumento,
                                                    'nrVersaoDocumento' =>  $nrVersaoDocumento,
                                                    'nrPergunta'        =>  $nrPergunta,
                                                    'dsLabelPergunta'   =>  $dsLabelPergunta,
                                                    'nrOrdemPergunta'   =>  $nrOrdemPergunta
                                                );
                    $idPerguntaFormDocto    =   $tbPerguntaFormDoctoDAO->inserir($dados);

                    if ($idPerguntaFormDocto) {
                        if ($stTipoObjetoPgr == 'TA' or $stTipoObjetoPgr == 'IT' or $stTipoObjetoPgr == 'DT' or $stTipoObjetoPgr == 'NR') {
                            $dsOpcao    =   '';
                            $dados      =   array(
                                                'nrFormDocumento'   =>  $nrFormDocumento,
                                                'nrVersaoDocumento' =>  $nrVersaoDocumento,
                                                'nrPergunta'        =>  $nrPergunta,
                                                'dsOpcao'           =>  $dsOpcao,
                                                'stTipoObjetoPgr'   =>  $stTipoObjetoPgr
                                            );
                            $idOpcaoResposta = $tbOpcaoRespostaDAO->inserir($dados);
                            if ($idOpcaoResposta) {
                                parent::message("Cadastro realizado com sucesso!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "CONFIRM");
//                                $this->_helper->json(array('retorno'=>'INSERIR','mensagem'=>''));
                            } else {
                                $this->_helper->json(array('retorno'=>'ERRO','mensagem'=>utf8_encode('Erro cadastro OpcaoResposta TA IT.')));
                            }
                        } else {
                            $validado           =   true;
                            foreach ($dsOpcaoR as $key => $value) {
                                $dsOpcao            =   $value;
                                $StTipoObjetoPgrAux =   '';
                                if ($stTipoObjetoPgr == 'CK' and isset($justificativa[$key]) and $justificativa[$key] == 1) {
                                    $StTipoObjetoPgrAux = 'IC';
                                }
                                if ($stTipoObjetoPgr == 'RB' and isset($justificativa[$key]) and $justificativa[$key] == 1) {
                                    $StTipoObjetoPgrAux = 'IR';
                                }
                                $dados      =   array(
                                                'nrFormDocumento'   =>  $nrFormDocumento,
                                                'nrVersaoDocumento' =>  $nrVersaoDocumento,
                                                'nrPergunta'        =>  $nrPergunta,
                                                'dsOpcao'           =>  $dsOpcao,
                                                'stTipoObjetoPgr'   =>  ($StTipoObjetoPgrAux)? $StTipoObjetoPgrAux:$stTipoObjetoPgr
                                            );
                                $idOpcaoResposta = $tbOpcaoRespostaDAO->inserir($dados);
                                if (!$idOpcaoResposta) {
                                    $validado = false;
                                }
                            }
                            if ($validado) {
                                parent::message("Cadastro realizado com sucesso!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "CONFIRM");
//                              $this->_helper->json(array('retorno'=>'INSERIR','mensagem'=>''));
                            } else {
                                $this->_helper->json(array('retorno'=>'ERRO','mensagem'=>utf8_encode('Erro cadastro OpcaoResposta CK CB RB.')));
                            }
                        }
                    } else {
                        $this->_helper->json(array('retorno'=>'ERRO','mensagem'=>utf8_encode('Erro cadastro perguntaformdocto.')));
                    }
                } else {
                    $this->_helper->json(array('retorno'=>'ERRO','mensagem'=>utf8_encode('Erro cadastro pergunta.')));
                }
                break;
            case 'excluirQuestao':
                $where = array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento,'nrPergunta = ?'=>$nrPergunta);
                $tbRespostaDAO          =   new tbResposta();
                $tbOpcaoRespostaDAO     =   new tbOpcaoResposta();
                $tbPerguntaFormDoctoDAO =   new tbPerguntaFormDocto();
                $tbPerguntaDAO          =   new tbPergunta();

                $tbOpcaoRespostaDAO->delete($where);

                if ($tbPerguntaFormDoctoDAO->delete($where)) {
                    parent::message("Exclus&atilde;o realizada com sucesso!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "CONFIRM");
                }

                $listaPerguntaFormDocto = $tbPerguntaFormDoctoDAO->buscar($where);
                if (count($listaPerguntaFormDocto) > 0) {
                    foreach ($listaPerguntaFormDocto as $pergunta) {
                        $tbPerguntaDAO->delete(array('nrPergunta = ?'=>$pergunta->nrPergunta));
                    }
                }

                break;
            case 'buscarQuestao':
                $where = array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento,'nrPergunta = ?'=>$nrPergunta);

                $tbPerguntaDAO      =   new tbPergunta();
                $questao            =   $tbPerguntaDAO->montarQuestionario($nrFormDocumento, $nrVersaoDocumento, $nrPergunta);

                $tbOpcaoRespostaDAO =   new tbOpcaoResposta();
                $opcoes             =   $tbOpcaoRespostaDAO->buscar($where, array('nrOrdemOpcao'));

                $stTipoObjetoPgr = 0;
                if (is_object($opcoes)) {
                    $stTipoObjetoPgr    =   $opcoes[0]->stTipoObjetoPgr;
                    if ($stTipoObjetoPgr == 'IR') {
                        $stTipoObjetoPgr = 'RB';
                    }
                    if ($stTipoObjetoPgr == 'IC') {
                        $stTipoObjetoPgr = 'CK';
                    }
                }
                $this->_helper->json(array('result'=>true,'nrPergunta'=>$questao[0]->nrPergunta,'dsPergunta'=>utf8_encode($questao[0]->dsPergunta),'dsLabelPergunta'=>utf8_encode($questao[0]->dsLabelPergunta),'stTipoObjetoPgr'=>$stTipoObjetoPgr));
                break;
            case 'alterarPergunta':
                $tbPerguntaDAO          =   new tbPergunta();
                $tbPerguntaFormDoctoDAO =   new tbPerguntaFormDocto();
                $tbOpcaoRespostaDAO     =   new tbOpcaoResposta();

                $dados          =   array(
                                        'dsPergunta'    => $dsPergunta,
                                    );
                $where          =   array(
                                        'nrPergunta = ?'    =>  $nrPergunta
                                    );
                $res = $tbPerguntaDAO->update($dados, $where);
                if ($res) {
                    $dados  =   array(
                                    'dsLabelPergunta'   =>  $dsLabelPergunta
                                );
                    $where  =   array(
                                    'nrFormDocumento = ?'   =>  $nrFormDocumento,
                                    'nrVersaoDocumento = ?' =>  $nrVersaoDocumento,
                                    'nrPergunta = ?'        =>  $nrPergunta
                                );
                    $idPerguntaFormDocto    =   $tbPerguntaFormDoctoDAO->update($dados, $where);
                    if ($idPerguntaFormDocto) {
                        if ($tbOpcaoRespostaDAO->delete($where)) {
                            if ($stTipoObjetoPgr == 'TA' or $stTipoObjetoPgr == 'IT') {
                                $dsOpcao    =   '';
                                $dados      =   array(
                                                    'nrFormDocumento'   =>  $nrFormDocumento,
                                                    'nrVersaoDocumento' =>  $nrVersaoDocumento,
                                                    'nrPergunta'        =>  $nrPergunta,
                                                    'dsOpcao'           =>  $dsOpcao,
                                                    'stTipoObjetoPgr'   =>  $stTipoObjetoPgr
                                                );
                                $idOpcaoResposta = $tbOpcaoRespostaDAO->inserir($dados);
                                if ($idOpcaoResposta) {
                                    parent::message("Altera��o realizado com sucesso!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "CONFIRM");
//                                    $this->_helper->json(array('retorno'=>'ALTERAR','mensagem'=>''));
                                } else {
                                    $this->_helper->json(array('retorno'=>'ERRO','mensagem'=>utf8_encode('Erro cadastro OpcaoResposta TA IT.')));
                                }
                            } else {
                                $validado           =   true;
                                foreach ($dsOpcaoR as $key => $value) {
                                    $dsOpcao            =   $value;
                                    $StTipoObjetoPgrAux =   '';
                                    if ($stTipoObjetoPgr == 'CK' and isset($justificativa[$key]) and $justificativa[$key] == 1) {
                                        $StTipoObjetoPgrAux = 'IC';
                                    }
                                    if ($stTipoObjetoPgr == 'RB' and isset($justificativa[$key]) and $justificativa[$key] == 1) {
                                        $StTipoObjetoPgrAux = 'IR';
                                    }
                                    $dados      =   array(
                                                    'nrFormDocumento'   =>  $nrFormDocumento,
                                                    'nrVersaoDocumento' =>  $nrVersaoDocumento,
                                                    'nrPergunta'        =>  $nrPergunta,
                                                    'dsOpcao'           =>  $dsOpcao,
                                                    'stTipoObjetoPgr'   =>  ($StTipoObjetoPgrAux)? $StTipoObjetoPgrAux:$stTipoObjetoPgr
                                                );
                                    $idOpcaoResposta = $tbOpcaoRespostaDAO->inserir($dados);
                                    if (!$idOpcaoResposta) {
                                        $validado = false;
                                    }
                                }
                                if ($validado) {
                                    parent::message("Altera��o realizado com sucesso!", "/cadastraredital/propostacustomizavel?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idusuario}", "CONFIRM");
//                                    $this->_helper->json(array('retorno'=>'ALTERAR','mensagem'=>''));
                                } else {
                                    $this->_helper->json(array('retorno'=>'ERRO','mensagem'=>utf8_encode('Erro cadastro OpcaoResposta CK CB RB.')));
                                }
                            }
                        } else {
                            $this->_helper->json(array('retorno'=>'ERRO','mensagem'=>utf8_encode('Erro excluir alterar OpcaoResposta.')));
                        }
                    } else {
                        $this->_helper->json(array('retorno'=>'ERRO','mensagem'=>utf8_encode('Erro alterar perguntaformdocto.')));
                    }
                } else {
                    $this->_helper->json(array('retorno'=>'ERRO','mensagem'=>utf8_encode('Erro alterar pergunta.')));
                }
                break;
        }
    } // fecha m�todo operacoescustomizavelAction()



    private function cadastraAtualizaRespostaQuestoes($info)
    {
        $tdRespostaDAO = new tbResposta();

        $where = array(
                        'nrFormDocumento = ?'   =>  $info['nrFormDocumento'],
                        'nrVersaoDocumento = ?' =>  $info['nrVersaoDocumento'],
                        'nrPergunta = ?'        =>  $info['nrPergunta'],
                        'idProjeto = ?'         =>  $info['idPreProjeto'],
                        'idPessoaCadastro = ?'  =>  $info['idUsuario']
                     );
        if (!($info['operacao'] == 'tipoCB' or $info['operacao'] == 'tipoRB'  or $info['operacao'] == 'tipoIR')) {
            $where['nrOpcao = ?']   =   $info['nrOpcao'];
        }
        $resposta = $tdRespostaDAO->buscar($where);
        if (is_object($resposta) and $resposta->count() > 0) {
            $data = array(
                        'dtResposta'        =>  new Zend_Db_Expr('GETDATE()'),
                        'dsRespostaSubj'    =>  $info['dsRespostaSubj']
                    );
            switch ($info['operacao']) {
                case 'tipoDT':
                    if ($tdRespostaDAO->update($data, $where)) {
                        $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                    } else {
                        $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro atualiza�?o DT.')));
                    }
                   break;
                case 'tipoNR':
                    if ($tdRespostaDAO->update($data, $where)) {
                        $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                    } else {
                        $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro atualiza�?o NR.')));
                    }
                   break;
                case 'tipoIT':
                    if ($tdRespostaDAO->update($data, $where)) {
                        $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                    } else {
                        $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro atualiza�?o IT.')));
                    }
                   break;
                case 'tipoTA':
                    if ($tdRespostaDAO->update($data, $where)) {
                        $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                    } else {
                        $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro atualiza&ccedil;&atilde;o TA.')));
                    }
                   break;
                case 'tipoCK':
                    if ($tdRespostaDAO->update($data, $where)) {
                        $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                    } else {
                        $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro atualiza&ccedil;&atilde;o TA.')));
                    }
                   break;
                case 'tipoIC':
                    if ($tdRespostaDAO->update($data, $where)) {
                        $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                    } else {
                        $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro atualiza&ccedil;&atilde;o TA.')));
                    }
                   break;
                case 'tipoCB':
                    $data['nrOpcao']   =   $info['nrOpcao'];
                    if ($tdRespostaDAO->update($data, $where)) {
                        $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                    } else {
                        $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro atualiza&ccedil;&atilde;o CB.')));
                    }
                    break;
                case 'tipoRB':
                    $data['nrOpcao']   =   $info['nrOpcao'];
                    if ($tdRespostaDAO->update($data, $where)) {
                        $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                    } else {
                        $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro atualiza&ccedil;&atilde;o RB.')));
                    }
                    break;
                case 'tipoIR':
                    $data['nrOpcao']   =   $info['nrOpcao'];
                    if ($tdRespostaDAO->update($data, $where)) {
                        $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
                    } else {
                        $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro atualiza&ccedil;&atilde;o IR.')));
                    }
                    break;
            }
        } else {
            $dados = array(
                        'nrFormDocumento'   =>  $info['nrFormDocumento'],
                        'nrVersaoDocumento' =>  $info['nrVersaoDocumento'],
                        'nrPergunta'        =>  $info['nrPergunta'],
                        'idProjeto'         =>  $info['idPreProjeto'],
                        'idPessoaCadastro'  =>  $info['idUsuario'],
                        'nrOpcao'           =>  $info['nrOpcao'],
                        'dtResposta'        =>  new Zend_Db_Expr('GETDATE()'),
                        'dsRespostaSubj'    =>  $info['dsRespostaSubj']
                     );


            if ($tdRespostaDAO->insert($dados)==0) {
                $this->_helper->json(array('result'=>true,'mensagem'=>utf8_encode('Cadastro realizado com sucesso.')));
            } else {
                $this->_helper->json(array('result'=>false,'mensagem'=>utf8_encode('Erro cadastro '.$info['operacao'].'.')));
            }
        }
    } // fecha m�todo cadastraAtualizaRespostaQuestoes($info)



    public function opcaoadicionadasAction()
    {
        $this->_helper->layout->disableLayout();
        $post       = Zend_Registry::get('post');
        $nrFormDocumento    =   $post->nrFormDocumento;
        $nrVersaoDocumento  =   $post->nrVersaoDocumento;
        $nrPergunta         =   $post->nrPergunta;

        $where = array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento,'nrPergunta = ?'=>$nrPergunta);

        $tbOpcaoRespostaDAO =   new tbOpcaoResposta();
        $this->view->opcoes             =   $tbOpcaoRespostaDAO->buscar($where, array('nrOrdemOpcao'));
    } // fecha m�todo opcaoadicionadasAction()



    public function questoesadicionadasAction()
    {
        $this->_helper->layout->disableLayout();
        $post       = Zend_Registry::get('post');
        $nrFormDocumento    =   $post->nrFormDocumento;
        $nrVersaoDocumento  =   $post->nrVersaoDocumento;

        $tbPerguntaDAO              =   new tbPergunta();
        $this->view->questoes       =   $tbPerguntaDAO->montarQuestionario($nrFormDocumento, $nrVersaoDocumento);

        $this->view->nrFormDocumento    =   $nrFormDocumento;
        $this->view->nrVersaoDocumento  =   $nrVersaoDocumento;
    } // fecha m�todo questoesadicionadasAction()



    public function cadastrarquestionarioAction()
    {
        $this->_helper->layout->disableLayout();
        $post       = Zend_Registry::get('post');
        $auth = Zend_Auth::getInstance();// instancia da autentica??o
        $idusuario = $auth->getIdentity()->usu_codigo;
        $nrFormDocumento    =   $post->nrFormDocumento;
        $nrVersaoDocumento  =   $post->nrVersaoDocumento;

        $tbFormDocumentoDAO =   new tbFormDocumento();
        $edital                 =   $tbFormDocumentoDAO->buscar(array('nrFormDocumento = ?'=>$nrFormDocumento));
        $this->view->idEdital   =   $edital[0]->idEdital;

        $tbFormDocumentoDAO             =   new tbFormDocumento();
        $FormDocumento                  =   $tbFormDocumentoDAO->buscar(array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento));
        $this->view->nmFormDocumento    =   $FormDocumento[0]->nmFormDocumento;

        $this->view->nrFormDocumento    =   $nrFormDocumento;
        $this->view->nrVersaoDocumento  =   $nrVersaoDocumento;
        $this->view->idusuario          =   $idusuario;
    }



    // variaveis para o questionario (Proposta Customiz?vel)
    private $idPreProjeto;
    private $stSomenteLeitura;
    private $nrVersaoDocumento;
    private $nrFormDocumento;
    private $nrPergunta;
    private $dsPergunta;
    private $nrOrdemPergunta;
    private $dsLabelPergunta;
    private $nrOpcao;
    private $operacao;
    private $Movimentacao       =   95;
    private $stEstado           =   0;
    private $stTipoRespPergunta =   'O';
    // variaveis para o questionario (Proposta Customiz?vel)
    public function visualizarguiaAction()
    {
        $post                           =   Zend_Registry::get('post');

        $idEdital = isset($_POST['idEdital']) ? $_POST['idEdital'] : $_GET['idEdital'];
        $edital = $this->listaGuiaDigital($idEdital);

        $this->idPreProjeto             =   $this->_request->getParam('idPreProjeto');
        $abilitalayout            =   $this->_request->getParam('abilitalayout');
        if (empty($abilitalayout)) {
            $this->_helper->layout->disableLayout();
        }


        $this->view->idPreProjeto       =   $this->idPreProjeto;
        $this->view->listaGuiasEdital   =   $edital;
    } // fecha m�todo visualizarguiaAction()



    public function visualizarquestionarioAction()
    {
        $this->_helper->layout->disableLayout();
        $get                                  =   Zend_Registry::get('get');
        $this->idPreProjeto                   =   $get->idPreProjeto;
        $this->nrFormDocumento                =   $get->nrFormDocumento;
        $this->nrVersaoDocumento              =   $get->nrVersaoDocumento;
        $this->vincularProjetoDocumento();
        $this->desabilitaQuestoes();

        $tbFormDocumentoDAO             =   new tbFormDocumento();
        $FormDocumento                  =   $tbFormDocumentoDAO->buscar(array('nrFormDocumento = ?'=>$this->nrFormDocumento,'nrVersaoDocumento = ?'=>$this->nrVersaoDocumento));
        $this->view->nmFormDocumento    =   $FormDocumento[0]->nmFormDocumento;
        $this->view->stSomenteLeitura   =   $this->stSomenteLeitura;
        $this->view->questionario       =   $this->montarQuestionario();
    } // fecha m�todo visualizarquestionarioAction()



    private function montarQuestionario()
    {
        $tbPerguntaDAO  =   new tbPergunta();
        $questoes       =   $tbPerguntaDAO->montarQuestionario($this->nrFormDocumento, $this->nrVersaoDocumento);
        $questionario   =   '';
        if (is_object($questoes) and count($questoes) > 0) {
            foreach ($questoes as $questao) {
                $this->nrPergunta       =   $questao->nrPergunta;
                $this->dsPergunta       =   $questao->dsPergunta;
                $this->nrOrdemPergunta  =   $questao->nrOrdemPergunta;
                $this->dsLabelPergunta  =   $questao->dsLabelPergunta;

                $questionario   .=   utf8_encode("

                        <table width='100%'>
                            <tr class='destacar'>
                                <td><b>Quest&atilde;o {$this->nrOrdemPergunta}: {$this->dsPergunta}</b></td>
                            </tr>
                ");
                if (trim($this->dsLabelPergunta) != '') {
                    $questionario   .=   utf8_encode("
                                <tr>
                                    <td>
                                        {$this->dsLabelPergunta}
                                    </td>
                                </tr>
                    ");
                }
                $questionario   .=   utf8_encode("
                            <tr>
                                <td>
                                    {$this->montarQuestoes()}
                                </td>
                            </tr>
                        </table>
                ");
            }
        } else {
            $questionario = "<table width='100%'>
                                <tr class='destacar'>
                                    <td align='center'><b>Este documento ainda n&atilde;o possui question&aacute;rio cadastrado.</b></td>                                </tr>
                             </table>";
        }

        return $questionario;
    } // fecha m�todo montarQuestionario()



    private function montarQuestoes()
    {
        $tbPerguntaDAO  =   new tbPergunta();
        $alternativas       =   $tbPerguntaDAO->montarAlternativa($this->nrFormDocumento, $this->nrVersaoDocumento, $this->nrPergunta);
        $resp = '';
        $disable = '';
        if ($this->stSomenteLeitura == 'S') {
            $disable = 'disabled';
        }
        if (is_object($alternativas) and count($alternativas) > 0) {
            foreach ($alternativas as $alternativa) {
                $this->nrOpcao  =   $alternativa->nrOpcao;
                $this->operacao =   $alternativa->stTipoObjetoPgr;
                $tbRespostaDAO  =   new tbResposta();
                $resposta = '';
                if ($this->idPreProjeto) {
                    $where          =   array(
                                            'nrFormDocumento = ?'       =>$this->nrFormDocumento
                                            ,'nrVersaoDocumento = ?'    =>$this->nrVersaoDocumento
                                            ,'nrPergunta = ?'           =>$this->nrPergunta
                                            ,'idProjeto = ?'            =>$this->idPreProjeto
                                            ,'nrOpcao = ?'              =>$this->nrOpcao
                                        );
                    $result = $tbRespostaDAO->buscar($where);

                    if ($result->count() > 0) {
                        $resposta = $result;
                    }
                }
                switch ($this->operacao) {
                    case 'IT':
                        $resp .= $this->MontarInput($resposta, $disable);
                        break;
                    case 'TA':
                        $resp .= $this->MontarTextArea($resposta, $disable);
                        break;
                    case 'CK':
                        $resp .= $this->MontarCheckGroup($alternativa, $resposta, $disable);
                        break;
                    case 'IC':
                        $resp .= $this->MontarCheckGroupJust($alternativa, $resposta, $disable);
                        break;
                    case 'CB':
                        $resp .= $this->MontarComboBox($alternativa, $resposta);
                        break;
                    case 'RB':
                        $resp .= $this->MontarRadio($alternativa, $resposta, $disable);
                        break;
                    case 'IR':
                        $resp .= $this->MontarRadioJust($alternativa, $resposta, $disable);
                        break;
                    case 'DT':
                        $resp .= $this->MontarInputData($resposta, $disable);
                        break;
                    case 'NR':
                        $resp .= $this->MontarInputNumero($resposta, $disable);
                        break;

                }
            }
        }
        if ($this->operacao == 'CB') {
            $resp = $this->defaultQuestao("
                        <select class=\"{$this->operacao}\" {$disable} name=\"resposta_{$this->nrPergunta}\">
                            <option value=\"\" > Selecione </option>
                            {$resp}
                        </select>
                    ");
        }
        if ($this->operacao == 'RB' or $this->operacao == 'IR') {
            $resp = $this->defaultQuestao($resp);
        }
        return $resp;
    } // fecha m�todo montarQuestoes()



    private function desabilitaQuestoes()
    {
        $auth                       =   Zend_Auth::getInstance();
        $this->stSomenteLeitura = 'S';
        $stSomenteLeituraAux = $this->_request->getParam('stSomenteLeitura');
        if ($stSomenteLeituraAux != $this->stSomenteLeitura && $this->idPreProjeto && !isset($auth->getIdentity()->usu_codigo)) {
            $tbMovimentacaoDAO = new Proposta_Model_DbTable_TbMovimentacao();
            $where = array(
                'idProjeto = ?'     =>  $this->idPreProjeto
                ,'Movimentacao = ?' =>  $this->Movimentacao
                ,'stEstado = ?'     =>  $this->stEstado
            );
            if (count($tbMovimentacaoDAO->buscar($where))>0) {
                $this->stSomenteLeitura = '';
            }
        }
    } // fecha m�todo desabilitaQuestoes()



    private function vincularProjetoDocumento()
    {
        $get                        =   Zend_Registry::get('get');
        $auth                       =   Zend_Auth::getInstance();// instancia da autentica??o
        if (isset($auth->getIdentity()->usu_codigo)) {
            $idusuario =  $auth->getIdentity()->usu_codigo;
        } else {
            $idusuario =  $auth->getIdentity()->IdUsuario;
        }
        $tbFormDocumentoProjetoDAO  =   new tbFormDocumentoProjeto();
        $idPreProjeto               =   $get->idPreProjeto;
        $nrFormDocumento            =   $get->nrFormDocumento;
        $nrVersaoDocumento          =   $get->nrVersaoDocumento;
        $where                      =   array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento,'idProjeto = ?'=>$idPreProjeto);
        if ($idPreProjeto) {
            if (count($tbFormDocumentoProjetoDAO->buscar($where)) == 0) {
                $cad = array('nrFormDocumento'=>$nrFormDocumento,'nrVersaoDocumento'=>$nrVersaoDocumento,'idProjeto'=>$idPreProjeto,'idPessoaCadastro'=>$idusuario,'dtIniValidade'=>'1900-01-01','dtFimValidade'=>'1900-01-01');
                $tbFormDocumentoProjetoDAO->inserir($cad);
            }
        }
    } // fecha m�todo vincularProjetoDocumento()



    private function excluirQuestionario()
    {
        if (!$_POST) {
            $nrVersaoDocumento  =   $_GET['nrVersaoDocumento'];
            $nrFormDocumento    =   $_GET['nrFormDocumento'];
            $where = array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento);
        } else {
            $nrVersaoDocumento  =   $_POST['nrVersaoDocumento'];
            $nrFormDocumento    =   $_POST['nrFormDocumento'];
            $where = array('nrFormDocumento = ?'=>$nrFormDocumento,'nrVersaoDocumento = ?'=>$nrVersaoDocumento);
        }

        $tbRespostaDAO          =   new tbResposta();
        $tbOpcaoRespostaDAO     =   new tbOpcaoResposta();
        $tbPerguntaFormDoctoDAO =   new tbPerguntaFormDocto();
        $tbPerguntaDAO          =   new tbPergunta();

        if (count($tbRespostaDAO->buscar($where))==0) {
            return array(true,'');
        } else {
            if ($tbRespostaDAO->delete($where)) {
                if (count($tbOpcaoRespostaDAO->buscar($where))==0) {
                    return array(true,'');
                } else {
                    if ($tbOpcaoRespostaDAO->delete($where)) {
                        $listaPerguntaFormDocto = $tbPerguntaFormDoctoDAO->buscar($where);
                        $verificar = false;
                        if (is_object($listaPerguntaFormDocto)) {
                            foreach ($listaPerguntaFormDocto as $pergunta) {
                                if ($tbPerguntaDAO->delete(array('nrPergunta = ?'=>$pergunta->nrPergunta))) {
                                    $verificar = true;
                                } else {
                                    $verificar = false;
                                }
                            }
                        } else {
                            $verificar = true;
                        }
                        if ($verificar) {
                            if ($tbPerguntaFormDoctoDAO->delete($where)) {
                                return array(true,'');
                            } else {
                                return array(false,"Erro ao tentar excluir a Pergunta Form Docto.");
                            }
                        } else {
                            return array(false,"Erro ao tentar excluir a Pergunta.");
                        }
                    } else {
                        return array(false,"Erro ao tentar excluir a Opcao Resposta.");
                    }
                }
            } else {
                return array(false,"Erro ao tentar excluir a Resposta.");
            }
        }
    } // fecha m�todo excluirQuestionario()



    private function defaultQuestao($add)
    {
        $auth                       =   Zend_Auth::getInstance();// instancia da autentica??o
        $idusuario                  =  $this->idusuario;

        return "
            <form action=\"\" method=\"post\" title=\"{$this->dsPergunta}\">
                <input type=\"hidden\" name=\"operacao\" value=\"tipo{$this->operacao}\"/>
                <input type=\"hidden\" name=\"nrFormDocumento\"     value=\"{$this->nrFormDocumento}\" />
                <input type=\"hidden\" name=\"nrVersaoDocumento\"   value=\"{$this->nrVersaoDocumento}\" />
                <input type=\"hidden\" name=\"nrPergunta\"          value=\"{$this->nrPergunta}\" />
                <input type=\"hidden\" name=\"idUsuario\"           value=\"{$idusuario}\" />
                <input type=\"hidden\" name=\"idPreProjeto\"        value=\"{$this->idPreProjeto}\" />
                <input type=\"hidden\" name=\"nrOpcao\"             value=\"{$this->nrOpcao}\" />
                {$add}
                <br />
                <div></div>
            </form>
        ";
    } // fecha m�todo defaultQuestao($add)



    private function MontarInput($resposta, $disable)
    {
        $valor = '';
        if (is_object($resposta) and $resposta->count()>0) {
            $valor = $resposta[0]->dsRespostaSubj;
        }
        $resp = "<input class=\"{$this->operacao}\" size=\"88\" name=\"resposta_{$this->nrOpcao}\" type=\"text\" {$disable} value=\"{$valor}\" />";
        return $this->defaultQuestao($resp);
    }
    private function MontarInputNumero($resposta, $disable)
    {
        $valor = '';
        if (is_object($resposta) and $resposta->count()>0) {
            $valor = $resposta[0]->dsRespostaSubj;
        }
        $resp = "<input class=\"{$this->operacao} numero\" size=\"10\" maxlength=\"5\" name=\"resposta_{$this->nrOpcao}\" type=\"text\" {$disable} value=\"{$valor}\" />";
        return $this->defaultQuestao($resp);
    }
    private function MontarInputData($resposta, $disable)
    {
        $valor = '';
        if (is_object($resposta) and $resposta->count()>0) {
            $valor = $resposta[0]->dsRespostaSubj;
        }
        $resp = "<input class=\"{$this->operacao} input_simples\" size=\"10\" name=\"resposta_{$this->nrOpcao}\" id=\"resposta_{$this->nrOpcao}\" type=\"text\" {$disable} value=\"{$valor}\" onkeyup=\"mascara(this, format_data)\" maxlength=\"10\"/>";
        return $this->defaultQuestao($resp);
    }

    private function MontarTextArea($resposta, $disable)
    {
        $valor = '';
        if (is_object($resposta) and $resposta->count()>0) {
            $valor = $resposta[0]->dsRespostaSubj;
        }
        $fix = date('Ymdhisu');

        $resp = "
        <textarea class=\"edicaoRica_{$this->nrOpcao}_{$fix}\" name=\"resposta_{$this->nrOpcao}\" class=\"{$this->operacao}_{$fix}\" id=\"resposta_{$this->nrOpcao}_{$fix}\" {$disable}>{$valor}</textarea>
        <script>
        CKEDITOR.replaceAll('edicaoRica_{$this->nrOpcao}_{$fix}');
        </script>
        ";
        return $this->defaultQuestao($resp);
    } // fecha m�todo MontarTextArea()



    private function MontarCheckGroup($alternativa, $resposta, $disable)
    {
        $valor = '';
        if (is_object($resposta) and $resposta->count()>0) {
            $valor = 'checked';
        }
        $label = $alternativa['dsOpcao'];
        $resp = "<input class=\"{$this->operacao}\" name=\"resposta_{$this->nrOpcao}\" type=\"checkbox\" {$disable} {$valor} value=\"{$label}\" />{$label}";
        return $this->defaultQuestao($resp);
    } // fecha m�todo MontarCheckGroup()



    private function MontarCheckGroupJust($alternativa, $resposta, $disable)
    {
        $valor = '';
        $valorAux = '';
        if (is_object($resposta) and $resposta->count()>0) {
            $valor = 'checked';
            $valorAux = $resposta[0]->dsRespostaSubj;
        }
        $label = $alternativa['dsOpcao'];
        $resp = "<input class=\"{$this->operacao}\" name=\"resposta_{$this->nrOpcao}\" type=\"checkbox\" {$disable} {$valor} value=\"{$label}\" />{$label} <br />Justificativa:<br /> <textarea {$disable} class=\"{$this->operacao}\" style=\"width:100%;\" name=\"resposta_{$this->nrPergunta}_{$this->nrOpcao}\" >{$valorAux}</textarea>";

        //$resp = "<input class=\"{$this->operacao}\" name=\"resposta_{$this->nrOpcao}\" type=\"checkbox\" {$disable} {$valor} value=\"{$label}\" />{$label} <input value=\"{$valorAux}\" type=\"text\" size=\"50\" {$disable} class=\"{$this->operacao}\" name=\"resposta_{$this->nrPergunta}_{$this->nrOpcao}\" />";
        return $this->defaultQuestao($resp);
    } // fecha m�todo MontarCheckGroupJust()



    private function MontarComboBox($alternativa, $resposta)
    {
        $valor = '';
        if (is_object($resposta) and $resposta->count()>0) {
            $valor = 'selected';
        }
        $label = $alternativa['dsOpcao'];
        $resp = "<option {$valor} value=\"{$this->nrOpcao}\">{$label}</option>";
        return $resp;
    } // fecha m�todo MontarComboBox()



    private function MontarRadio($alternativa, $resposta, $disable)
    {
        $valor = '';
        if (is_object($resposta) and $resposta->count()>0) {
            $valor = 'checked';
        }
        $label = $alternativa['dsOpcao'];
        $resp = "<input name=\"resposta_{$this->nrPergunta}\" type=\"radio\" class=\"{$this->operacao}\"  {$disable} {$valor} value=\"{$this->nrOpcao}\" />{$label}<br /><br />";
        return $resp;
    } // fecha m�todo MontarRadio()



    private function MontarRadioJust($alternativa, $resposta, $disable)
    {
        $valor = '';
        $valorAux = '';
        if (is_object($resposta) and $resposta->count()>0) {
            $valor = 'checked';
            $valorAux = $resposta[0]->dsRespostaSubj;
        }
        $label = $alternativa['dsOpcao'];

        $resp = "<input name=\"resposta_{$this->nrPergunta}\" type=\"radio\" class=\"{$this->operacao}\" {$disable} {$valor} value=\"{$this->nrOpcao}\" />{$label}<br />Justificativa:<br /> <textarea {$disable} class=\"{$this->operacao}\" style=\"width:100%;\"  name=\"resposta_{$this->nrPergunta}_{$this->nrOpcao}\" >{$valorAux}</textarea><br /><br />";
        //$resp = "<input name=\"resposta_{$this->nrPergunta}\" type=\"radio\" class=\"{$this->operacao}\" {$disable} {$valor} value=\"{$this->nrOpcao}\" />{$label} <input value=\"{$valorAux}\" type=\"text\" size=\"50\" {$disable} class=\"{$this->operacao}\" name=\"resposta_{$this->nrPergunta}_{$this->nrOpcao}\" /><br /><br />";
        return $resp;
    } // fecha m�todo MontarRadioJust()



    public function acessaravaliadorAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idorgao = $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codGrupo = $codGrupo;
        $this->view->codOrgao = $codOrgao;

        $Orgao = new Orgaos();

        $NomeOrgao = $Orgao->pesquisarNomeOrgao($codOrgao);
        $this->view->nomeOrgao = $NomeOrgao;

        $tbFormDocumentoDAO =   new tbFormDocumento();
        $edital                 =   $tbFormDocumentoDAO->buscar(array('idEdital = ?'=>$_GET['idEdital']));

        $this->view->nmEdital   =   $edital[0]->nmFormDocumento;
        /*         * *************************************************************** */
    } // fecha m�todo acessaravaliadorAction()



    public function vinculareditaisAction()
    {
        $nrFormDocumento = $_GET['nrFormDocumento'];
        $nrVersaoDocumento = $_GET['nrVersaoDocumento'];
        //$idAgente = $_POST['idAgente'];
        $idEdital = $_GET['idEdital'];
        $idUsuario = $_GET['idUsuario'];

        $tbFormDocumentoDAO =   new tbFormDocumento();
        $edital                 =   $tbFormDocumentoDAO->buscar(array('idEdital = ?'=>$_GET['idEdital']));
        $this->view->nmEdital   =   $edital[0]->nmFormDocumento;
        $this->view->nrFormDocumento    = $nrFormDocumento;
        $this->view->idEdital           = $idEdital;

        if (isset($_GET['cpf'])) {
            $cpf = $_GET['cpf'];
            $this->view->cpf = $cpf;
        } else {
            $cpf = $_POST['cpf'];
            $this->view->cpf = $cpf;
        }
        $buscaIdAgente = ManterAvaliadorDAO::buscaIdAgente($cpf);
        if (!empty($buscaIdAgente[0])) {
            if (isset($_POST['idAgente'])) {
                $idAgente = $_POST['idAgente'];
                $this->view->idAgente = $idAgente;
            } else {
                $agentes = new Agente_Model_DbTable_Agentes();
                $agente = $agentes->BuscaAgente($cpf)->toArray();
                $idAgente = $agente[0]['idAgente'];
                $this->view->idAgente = $idAgente;
            }


            $avaliador = ManterAvaliadorDAO::buscaAvaliador($cpf, $idAgente);
            $this->view->nomeAvaliador = $avaliador[0]->nome;

            $avaliador = ManterAvaliadorDAO::buscaAvaliador($cpf, $idAgente);
            $this->view->dadosAvaliador = $avaliador;

            $editais = ManterAvaliadorDAO::buscaEditaisAtivos($idAgente);
            $this->view->editais = $editais;

            $dadosEdital = ManterAvaliadorDAO::listarEditaisAvaliador(); //BUSCA DA MODAL EDITAIS
            $this->view->dadosEditalAvaliador = $dadosEdital;

            // ========== IN�CIO PAGINA��O ==========
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
            $paginator = Zend_Paginator::factory($dadosEdital); // dados a serem paginados

            // p�gina atual e quantidade de �tens por p�gina
            $currentPage = $this->_getParam('page', 1);
            $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(5);
            $this->view->dadosEditalAvaliador = $paginator;
            $this->view->qtdDoc    = count($dadosEdital); // quantidade

            // ========== FIM PAGINA��O ==========
        } else {
            parent::message("CPF n�o cadastrado!", "/cadastraredital/acessaravaliador?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}", "ALERT");
        }
        if (isset($_POST['idEdit'])) {
            x(3);//Desvincular
            $idAgente = $_POST['idAgen'];
            $idEdital = $_POST['idEdit'];

            $this->view->cpf = $cpf;

            $alterar = new tbAvaliadorEdital();
            $dados = array('stAtivo' => 'I');
            $where = "idAvaliador = $idAgente and idEdital = $idEdital";
            $atualizarProjeto = $alterar->alterarAvaliador($dados, $where);

            $avaliador = ManterAvaliadorDAO::buscaAvaliador($cpf, $idAgente);
            $this->view->nomeAvaliador = $avaliador[0]->nome;

            $this->view->idAgente = $idAgente;
            if ($idAgente) {
                $editais = ManterAvaliadorDAO::buscaEditaisAtivos($idAgente);
                $this->view->editais = $editais;
            }

            parent::message("Edital desvinculado com sucesso!", "/cadastraredital/vinculareditais?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}&cpf={$cpf}", "CONFIRM");
        }

        if (isset($_POST['cpf2'])) { //Vincular

            $cpf = $_POST['cpf2'];
            $agentes = new Agente_Model_DbTable_Agentes();
            $agente = $agentes->BuscaAgente($cpf)->toArray();
            $idAgente = $agente[0]['idAgente'];
            $this->view->idAgente = $idAgente;

            $idEdit= $_GET['idEdital'];

            $alterar = new tbAvaliadorEdital();
            $vinculado = $alterar->buscar(array('idAvaliador = ?'=>$idAgente, 'idEdital = ?'=>$idEdital))->toArray();

            if ($vinculado) {
                if ($vinculado[0]['stAtivo'] == 'A') {
                    parent::message("Edital j� vinculado!", "/cadastraredital/vinculareditais?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}&cpf={$cpf}", "ALERT");
                } elseif ($vinculado[0]['stAtivo'] == 'I') {
                    $dados = array('stAtivo' => 'A');
                    $where = "idAvaliador = $idAgente and idEdital = $idEdital";
                    $atualizarProjeto = $alterar->update($dados, $where);

                    parent::message("Edital vinculado com sucesso!", "/cadastraredital/vinculareditais?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}&cpf={$cpf}", "CONFIRM");
                }
            } else {
                $dados = array('stAtivo' => 'A');
                $where = "idAvaliador = $idAgente and idEdital = $idEdital";
                $atualizarProjeto = $alterar->alterarAvaliador($dados, $where);

                $dadosInserir = array(
                    'idEdital' => $idEdital,
                    'idAvaliador' => $idAgente,
                    'stAtivo' => 'A'
                );
                $inserir = $alterar->inserirAvaliador($dadosInserir);
                parent::message("Edital vinculado com sucesso!", "/cadastraredital/vinculareditais?nrFormDocumento={$nrFormDocumento}&nrVersaoDocumento={$nrVersaoDocumento}&idEdital={$idEdital}&idUsuario={$idUsuario}&cpf={$cpf}", "CONFIRM");
            }
        }
    } // fecha m�todo vinculareditaisAction()

    public static function saldoPiMenosValorEdital($nrFormDocumento, $idAti)
    {
        $buscar = new tbPagamento();
        $valor = $buscar->buscarValorInserido($nrFormDocumento);
        $valorJaInserido = null;
        $saldoPiMenosPiAtual = null;

        $valorJaInserido = number_format($valor[0]->parcelas, 2, ',', '.');

        $saldoPi = Edital::saldoPi($idAti);

        $valorGasto = number_format($saldoPi[0]->parcelas, 2, ',', '.');

        $valorPi = Edital::recuperaValorPi($idAti);
        $valorPi = number_format($valorPi[0]->valor, 2, ',', '.');

        $saldoPi = str_replace(".", "", $valorPi)-$valorGasto;

        $saldoPi = number_format($saldoPi, 2, ',', '.');

        $saldoPiMenosPiAtual = str_replace(".", "", $saldoPi) - str_replace(".", "", $valorJaInserido);

        $saldoPiMenosPiAtual = number_format($saldoPiMenosPiAtual, 2, ',', '.');

        return $saldoPiMenosPiAtual;
    }

    public static function recuperaIdAti($idEdital)
    {
        $buscar = new Edital();
        $idAti = null;
        $idAti = $buscar->buscarIdPi($idEdital);

        return $idAti;
    }
} // fecha class
