<?php

/**
 * Controller Projetos
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @link http://www.politec.com.br
 * @copyright 2010 - Politec - Todos os direitos reservados.
 */
class ProjetosController extends MinC_Controller_Action_Abstract
{

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        // autenticacao scriptcase (AMBIENTE PROPONENTE)
        parent::init();
    }

    // fecha metodo init()

    /**
     * Metodo que chama a view projetos (nehuma funcionalidade)
     * @access public
     * @param void
     * @return void
     */
    public function projetosAction()
    {
    }

    /**
     * Metodo que chama as funcoes de balancear projeto, copiar tabelas e alterar a situacao do projeto
     * @access public
     * @param void
     * @return void
     */
    public function balancearAction()
    {

        $auth 		 = Zend_Auth::getInstance(); // pega a autenticacao
        $idPronac 	 = $this->_request->getParam("idpronac"); // pega o id do pronac via get
        $servico 	 = $this->_request->getParam("servico"); // pega o id do pronac via get
        $tipousuario = $this->_request->getParam("idusuario"); // pega o id do pronac via get
        
        $tbDistParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        $w1['a.IdPRONAC = ?'] = $idPronac;
        $qntdProd = $tbDistParecer->QntdProdutosXValidados($w1);
        
        $w2['a.IdPRONAC = ?'] = $idPronac;
        $w2['a.stEstado = ?'] = 0;
        $w2['a.FecharAnalise in (?)'] = array(1,2);
        $qntdProdValidados = $tbDistParecer->QntdProdutosXValidados($w2);

        if ($qntdProdValidados != $qntdProd) { //Se n�o houver valida��o para cada produto, o sistema n�o deixa continuar.
            parent::message("Produto sem valida&ccedil;&atilde;o do Coordenador da Vinculada!", "gerenciarpareceres/index", "ERROR");
        }
                
        $planilhaAprovacao = new PlanilhaAprovacao();
        $buscarplanilhaaprovacao = $planilhaAprovacao->buscar(array("IdPRONAC = ?" => $idPronac, "tpPlanilha = ?"=>"CO"))->current();

        if (count($buscarplanilhaaprovacao) == 0) {
            try {
                // copia as tabelas
                $planilhaProjeto 	= new PlanilhaProjeto();
                $analiseConteudo 	= new Parecer_Model_DbTable_TbAnaliseDeConteudo();
                $analiseaprovacao 	= new AnaliseAprovacao();
                $projetos 		= new Projetos();
                $Distribuicao 		= new DistribuicaoProjetoComissao();
                $titulacaoConselheiro 	= new TitulacaoConselheiro();
                $arrParecerProduto      = array();

                //ANALISE DE CONTEUDO
                $RanaliseConteudo = $analiseConteudo->dadosAnaliseconteudo($idPronac);
                foreach ($RanaliseConteudo as $resu) {
                    $data = array(
                        'tpAnalise' 			=> 'CO',
                        'dtAnalise' 			=> new Zend_Db_Expr('GETDATE()'),
                        'idAnaliseConteudo'             => $resu->idAnaliseDeConteudo,
                        'IdPRONAC' 			=> $idPronac,
                        'idProduto' 			=> $resu->idProduto,
                        'stLei8313' 			=> $resu->Lei8313,
                        'stArtigo3' 			=> $resu->Artigo3,
                        'nrIncisoArtigo3' 		=> $resu->IncisoArtigo3,
                        'dsAlineaArt3' 			=> $resu->AlineaArtigo3,
                        'stArtigo18' 			=> $resu->Artigo18,
                        'dsAlineaArtigo18' 		=> $resu->AlineaArtigo18,
                        'stArtigo26' 			=> $resu->Artigo26,
                        'stLei5761' 			=> $resu->Lei5761,
                        'stArtigo27' 			=> $resu->Artigo27,
                        'stIncisoArtigo27_I'            => $resu->IncisoArtigo27_I,
                        'stIncisoArtigo27_II'           => $resu->IncisoArtigo27_II,
                        'stIncisoArtigo27_III'          => $resu->IncisoArtigo27_III,
                        'stIncisoArtigo27_IV'           => $resu->IncisoArtigo27_IV,
                        'stAvaliacao' 			=> $resu->ParecerFavoravel, //1=Favoravel(sim)    0=Desfavoravel(nao)
                        'dsAvaliacao' 			=> $resu->ParecerDeConteudo
                    );
                    
                    if ($resu->idProduto >= 1) {
                        $arrParecerProduto[$resu->idProduto] = $resu->ParecerFavoravel;
                    } else {
                        $arrParecerProduto[0] = 1; //Admistracao do Projeto, que nao possui codigo de produto
                    }
                    $analiseaprovacao->inserir($data);
                }
                
                //ANALISE DE CUSTO
                $Rplanilhaprojeto = $planilhaProjeto->dadosPlanilhaProjeto($idPronac);
                foreach ($Rplanilhaprojeto as $resu) {
                    $data = array(
                        'tpPlanilha'            => 'CO',
                        'dtPlanilha'            => new Zend_Db_Expr('GETDATE()'),
                        'idPlanilhaProjeto'     => $resu->idPlanilhaProjeto,
                        'idPlanilhaProposta'    => $resu->idPlanilhaProposta,
                        'IdPRONAC'              => $idPronac,
                        'idProduto'             => $resu->idProduto,
                        'idEtapa'               => $resu->idEtapa,
                        'idPlanilhaItem'        => $resu->idPlanilhaItem,
                        'dsItem'                => '',
                        'idUnidade'             => $resu->idUnidade,
                        'qtDias'                => $resu->QtdeDias,
                        'tpDespesa'             => $resu->TipoDespesa,
                        'tpPessoa'              => $resu->TipoPessoa,
                        'nrContraPartida'       => $resu->Contrapartida,
                        'nrFonteRecurso'        => $resu->FonteRecurso,
                        'idUFDespesa'           => $resu->UfDespesa,
                        'idMunicipioDespesa'    => $resu->MunicipioDespesa,
                        'dsJustificativa'       => $resu->Justificativa,
                        'stAtivo'               => 'S'
                    );
                    
                    //zera valores de produto desfavorecido
                    if (isset($arrParecerProduto[$resu->idProduto])) {
                        if ($arrParecerProduto[$resu->idProduto] == '1') {
                            //produto favorecido
                            $data['qtItem']       = $resu->Quantidade;
                            $data['nrOcorrencia'] = $resu->Ocorrencia;
                            $data['vlUnitario']   = $resu->ValorUnitario;
                        } else {
                            //produto desfavorecido
                            $data['qtItem']       = 0;
                            $data['nrOcorrencia'] = 0;
                            $data['vlUnitario']   = 0;
                        }
                    } else { //condicao para a Admistracao do Projeto, que nao possui codigo de produto
                        $data['qtItem']       = $resu->Quantidade;
                        $data['nrOcorrencia'] = $resu->Ocorrencia;
                        $data['vlUnitario']   = $resu->ValorUnitario;
                    }
                    
                    $inserirPlanilhaAprovacao = $planilhaAprovacao->inserir($data);
                }
                
                //VERIFICA QUANTOS PRODUTOS O PROJETO POSSUI POR AREA
                $rsProdutos = $tbDistParecer->BuscarQtdAreasProjetos($idPronac);
                $totalArea = $rsProdutos->QDTArea;
                if ($totalArea >= '2') {
                    $area = 7; //Area = Artes integradas
                } else {
                    //BUSCA AREA DO PROJETO
                    $areaProjeto = $projetos->BuscarAreaSegmentoProjetos($idPronac);
                    $area = $areaProjeto['area']; //Area do projeto
                }
            
                $Rtitulacao = $titulacaoConselheiro->buscarcomponentebalanceamento($area)->current();
                $dados = array(
                    'idPRONAC' 			=> $idPronac,
                    'idAgente' 			=> $Rtitulacao->idAgente,
                    'dtDistribuicao' 	=> new Zend_Db_Expr('GETDATE()'),
                    'idResponsavel' 	=> 0//$tipousuario
                );
                $Distribuicao->inserir($dados);
                // chama a funcao para alterar a situacao do projeto - Padrao C10
                $data = array(
                    'Situacao' 		=> 'C10',
                    'dtSituacao' 	=> new Zend_Db_Expr('GETDATE()')
                );
                //$where = "IdPRONAC = $idPronac";
                $where['IdPRONAC = ?'] = $idPronac;
                $projetos->alterar($data, $where);
                //echo 'Conselheiro = ' . $Rtitulacao->idAgente . '<br/>';
                
                parent::message("Projeto encaminhado para o Componente da Comiss&atilde;o. Conselheiro: ".$Rtitulacao->Nome, "gerenciarpareceres/index", "CONFIRM");
            } // fecha try
            catch (Exception $e) {

                parent::message("Error: ".$e->getMessage(), "gerenciarpareceres/index", "ERROR");
                die($e->getMessage());
            }

        } else {
            parent::message("Planilhas j&aacute; copiadas.", "gerenciarpareceres/index", "ALERT");
        }
        
        // colocar um else aqui!!!
        
        /*
        // redireciona para a pagina de projetos
        // $this->_redirect('projetos/projetos');
        if ($servico == 'producao') {
            $this->_redirect('http://sistemas.cultura.gov.br/salic/conGridGerenciarParecer/conGridGerenciarParecer.php');
        }
        if ($servico == 'homologa') {
            $this->_redirect('http://homologa.cultura.gov.br/salic/conGridGerenciarParecer/conGridGerenciarParecer.php');
        }
        if ($servico == 'teste') {
            $this->_redirect('http://sedna/salic/conGridGerenciarParecer/conGridGerenciarParecer.php');
        }
        if ($servico == 'desenvolvimento') {
            $this->_redirect('http://ask/salic/conGridGerenciarParecer/conGridGerenciarParecer.php');
        }
        //die('fim');
        */

    }

    // fecha metodo balancearAction()

    /**
     * M?todo que chama as fun??es de balancear projeto, copiar tabelas e alterar a situa??o do projeto
     * @access public
     * @param void
     * @return void
     */
    public function aprovacaodiretaAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess?o com o grupo ativo
            $codOrgao = $GrupoAtivo->codOrgao; //  �rg?o ativo na sess?o
            $this->view->codOrgao = $codOrgao;

        $idPronac = $_POST['idpronac'];
        $idusuario = $_POST['idusuario'];
        $tipo_doc = $_POST['tipo_doc'];
        //$cod_ect = $_POST['cod_ect'];
        $cod_ect = null;

        //pega as informa�?es do arquivo
            $arquivoNome 	= $_FILES['documento']['name']; // nome
            $arquivoTemp 	= $_FILES['documento']['tmp_name']; // nome tempor�rio
            $arquivoTipo 	= $_FILES['documento']['type']; // tipo
            $arquivoTamanho     = $_FILES['documento']['size']; // tamanho

            if (!empty($arquivoNome)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens?o
            }
        if (!empty($arquivoTemp)) {
            $arquivoBinario = Upload::setBinario($arquivoTemp); // bin�rio
                $arquivoHash 	= Upload::setHash($arquivoTemp); // hash
        }

        $tbDoc = array(
                'idPronac' 		=> $idPronac,
                'stEstado' 		=> 0,
                'imDocumento' 	=> null,
                'idTipoDocumento'   => $tipo_doc,
                'idUsuario' 	=> $idusuario,
                'dtDocumento' 	=> '2008/12/21',
                'NoArquivo' 	=> $arquivoNome,
                'TaArquivo' 	=> $arquivoTamanho,
                'idUsuarioJuntada'  => null ,
                'dtJuntada' 	=> null ,
                'idUnidadeCadastro' => $codOrgao,
                'CodigoCorreio'     => $cod_ect,
                'biDocumento' 	=> $arquivoBinario
            );

        $dados = "Insert into SAC.dbo.tbDocumento
                  (idPronac, stEstado, imDocumento, idTipoDocumento, idUsuario, dtDocumento, NoArquivo, TaArquivo, idUsuarioJuntada, dtJuntada, idUnidadeCadastro, CodigoCorreio, biDocumento)
                  values
                  (".$idPronac.", 0, null, ".$tipo_doc.", ".$idusuario.", GETDATE(), '".$arquivoNome."', ".$arquivoTamanho.", null, null, ".$codOrgao.", '".$cod_ect."', ".$arquivoBinario.")
            ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        try {
//                $db->beginTransaction();
                if (empty($arquivoTemp)) { // nome do arquivo
                    parent::message("Por favor, informe o arquivo!!", "gerenciarpareceres/index", "ALERT");
                } elseif (empty($arquivoTemp)) { // nome do arquivo
                    parent::message("Por favor, informe o arquivo!", "gerenciarpareceres/index", "ALERT");
                } elseif ($arquivoExtensao != 'pdf') { // extens?o do arquivo
                    parent::message("O arquivo deve ser PDF!", "gerenciarpareceres/index", "ALERT");
                } elseif ($arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                    parent::message("O arquivo n?o pode ser maior do que 10MB!", "gerenciarpareceres/index", "ALERT");
                } else {
                    $resultado = TramitarDocumentosDAO::cadDocumento($dados);
                    $tbHistoricoDoc = array(
                            'idPronac'              => $idPronac,
                            'idDocumento'           => $resultado,
                            'idUnidade'             => $codOrgao,
                            'dtTramitacaoEnvio'     => date('Y/m/d H:i:s'),
                            'idUsuarioEmissor'      => $idusuario,
                            'meDespacho'            => null,
                            'idLote'                => null,
                            'dtTramitacaoRecebida'  => null,
                            'idUsuarioReceptor'     => null,
                            'Acao'                  => 1,
                            'stEstado'              => 1
                    );
                    $resultado2 = TramitarDocumentosDAO::cadHistorico('SAC.dbo.tbHistoricoDocumento', $tbHistoricoDoc);
                }

            $this->_helper->viewRenderer->setNoRender();
            //$servico   = $this->_request->getParam("servico"); // pega o id do pronac via get

            $aprovacao = new Aprovacao();
            $parecer = new Parecer_Model_DbTable_Parecer();
            $planilhaaprovacao = new PlanilhaAprovacao();
            $projeto = new Projetos();
            $planilhaProjeto = new PlanilhaProjeto();
            $analiseConteudo = new Parecer_Model_DbTable_TbAnaliseDeConteudo();
            $analiseaprovacao = new AnaliseAprovacao();
            $Distribuicao = new DistribuicaoProjetoComissao();
            $reuniao = new Reuniao();
            $pauta = new Pauta();
            $sp = new paVerificarAtualizarSituacaoAprovacao();

            // copia as tabelas
            $buscarParecer = $parecer->buscar(array('idPronac = ?'=>$idPronac, 'stAtivo = ?'=>1))->current()->toArray();
            $Rplanilhaprojeto = $planilhaProjeto->buscar(array('idPRONAC = ?'=> $idPronac));
            foreach ($Rplanilhaprojeto as $resu) {
                $data = array(
                        'tpPlanilha'=>'CO',
                        'dtPlanilha'=> date('Y-m-d H:i:s'),
                        'idPlanilhaProjeto'=>$resu->idPlanilhaProjeto,
                        'idPlanilhaProposta'=>$resu->idPlanilhaProposta,
                        'IdPRONAC'=>$resu->idPRONAC,
                        'idProduto'=>$resu->idProduto,
                        'idEtapa'=>$resu->idEtapa,
                        'idPlanilhaItem'=>$resu->idPlanilhaItem,
                        'dsItem'=>'',
                        'idUnidade'=>$resu->idUnidade,
                        'qtItem'=>$resu->Quantidade,
                        'nrOcorrencia'=>$resu->Ocorrencia,
                        'vlUnitario'=>$resu->ValorUnitario,
                        'qtDias'=>$resu->QtdeDias,
                        'tpDespesa'=>$resu->TipoDespesa,
                        'tpPessoa'=>$resu->TipoPessoa,
                        'nrContraPartida'=>$resu->Contrapartida,
                        'nrFonteRecurso'=>$resu->FonteRecurso,
                        'idUFDespesa'=>$resu->UfDespesa,
                        'idMunicipioDespesa'=>$resu->MunicipioDespesa,
                        'dsJustificativa'=>$resu->Justificativa,
                        'stAtivo'=>'S'
                    );
                $inserirPlanilhaAprovacao = $planilhaaprovacao->inserir($data);
            }

            $RanaliseConteudo = $analiseConteudo->buscar(array('IdPRONAC = ?'=> $idPronac));
            foreach ($RanaliseConteudo as $resu) {
                $data = array(
                        'tpAnalise'=>'CO',
                        'dtAnalise'=>date('Y-m-d H:i:s'),
                        'idAnaliseConteudo'=>$resu->idAnaliseDeConteudo,
                        'IdPRONAC'=>$resu->idPronac,
                        'idProduto'=>$resu->idProduto,
                        'stLei8313'=>$resu->Lei8313,
                        'stArtigo3'=>$resu->Artigo3,
                        'nrIncisoArtigo3'=>$resu->IncisoArtigo3,
                        'dsAlineaArt3'=>$resu->AlineaArtigo3,
                        'stArtigo18'=>$resu->Artigo18,
                        'dsAlineaArtigo18'=>$resu->AlineaArtigo18,
                        'stArtigo26'=>$resu->Artigo26,
                        'stLei5761'=>$resu->Lei5761,
                        'stArtigo27'=>$resu->Artigo27,
                        'stIncisoArtigo27_I'=>$resu->IncisoArtigo27_I,
                        'stIncisoArtigo27_II'=>$resu->IncisoArtigo27_II,
                        'stIncisoArtigo27_III'=>$resu->IncisoArtigo27_III,
                        'stIncisoArtigo27_IV'=>$resu->IncisoArtigo27_IV,
                        'stAvaliacao'=>$resu->ParecerFavoravel,
                        'dsAvaliacao'=>$resu->ParecerDeConteudo
                    );
                    
                $analiseaprovacao->inserir($data);
            }

            $consolidarAprovacao = TratarString::escapeString($buscarParecer['ResumoParecer']);
            $somaPlanilhaAprovacao = $planilhaaprovacao->somarPlanilhaAprovacao($idPronac);
            $valoraprovacao = $somaPlanilhaAprovacao['soma'];
            $tipoAprovacao = $buscarParecer['TipoParecer'];
            $buscarprojetos = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
            $dados = array(
                    'IdPRONAC'=>$idPronac,
                    'AnoProjeto'=>$buscarprojetos['AnoProjeto'],
                    'Sequencial'=>$buscarprojetos['Sequencial'],
                    'TipoAprovacao'=>$tipoAprovacao,
                    'dtAprovacao'=>date('Y-m-d H:i:s'),
                    'ResumoAprovacao'=>$consolidarAprovacao,
                    'dtInicioCaptacao'=>date('Y-m-d H:i:s'),
                    'dtFimCaptacao'=>date('Y-12-31 11:59:59'),
                    'AprovadoReal'=> $valoraprovacao,
                    'AprovadoUfir'=> 0,
                    'AutorizadoUfir'=> 0,
                    'AutorizadoReal'=> 0,
                    'ConcedidoCusteioReal'=> 0,
                    'ConcedidoCapitalReal'=> 0,
                    'ContraPartidaReal'=> 0,
                    'Logon'=> $idusuario,
                );
            $inserir = $aprovacao->inserir($dados);
            $buscarReuniaoAberta = $reuniao->buscarReuniaoAberta();

            $dadosPauta = array(
                    "idNrReuniao"=>$buscarReuniaoAberta['idNrReuniao'],
                    "IdPRONAC"=>$idPronac,
                    "dtEnvioPauta"=>date("Y-m-d H:i:s"),
                    "stEnvioPlenario"=>"N",
                    "tpPauta"=>'1',
                    "stAnalise"=>'AR',
                    "dsAnalise"=> 'AD Referendum'
                );

            $inserirProjetoPauta = $pauta->inserir($dadosPauta);

            $verificarSituacao = $sp->expaVerificarAtualizarSituacaoAprovacao($idPronac);
//                $db->commit();
            parent::message("Projeto aprovado com sucesso!", "gerenciarpareceres/index", "CONFIRM");
        } catch (Zend_Exception $ex) {
//                $db->rollBack();
            parent::message("Erro ao realizar cadastro", "gerenciarpareceres/index", "ERROR");
        }
    } // fecha m?todo balancearAction()

    // fecha metodo balancearAction()

    /**
     * Metodo que chama as funcoes de balancear projeto, copiar tabelas e alterar a situacao do projeto
     * Envio para o componente da comissao (UC53)
     * @access public
     * @param void
     * @return void
     */
    public function enviarcomponentedacomissaoAction()
    {
        $idPronac = $this->_request->getParam("idpronac"); // pega o id do pronac via get
        $servico = $this->_request->getParam("servico"); // pega o id do pronac via get
        //$tipousuario   = $this->_request->getParam("idusuario"); // pega o id do pronac via get
        try {
            // copia as tabelas
            $planilhaProjeto = new PlanilhaProjeto();
            $planilhaAprovacao = new PlanilhaAprovacao();
            $analiseConteudo = new Parecer_Model_DbTable_TbAnaliseDeConteudo();
            $analiseaprovacao = new AnaliseAprovacao();
            $projetos = new Projetos();
            $Distribuicao = new DistribuicaoProjetoComissao();
            $titulacaoConselheiro = new TitulacaoConselheiro();
            $Rplanilhaprojeto = $planilhaProjeto->buscar(array('idPRONAC = ?' => $idPronac));
            foreach ($Rplanilhaprojeto as $resu) {
                $data = array(
                    'tpPlanilha' 			=> 'CO',
                    'dtPlanilha' 			=> new Zend_Db_Expr('GETDATE()'),
                    'idPlanilhaProjeto' 	=> $resu->idPlanilhaProjeto,
                    'idPlanilhaProposta' 	=> $resu->idPlanilhaProposta,
                    'IdPRONAC' 				=> $resu->idPRONAC,
                    'idProduto' 			=> $resu->idProduto,
                    'idEtapa' 				=> $resu->idEtapa,
                    'idPlanilhaItem' 		=> $resu->idPlanilhaItem,
                    'dsItem' 				=> '',
                    'idUnidade' 			=> $resu->idUnidade,
                    'qtItem' 				=> $resu->Quantidade,
                    'nrOcorrencia' 			=> $resu->Ocorrencia,
                    'vlUnitario' 			=> $resu->ValorUnitario,
                    'qtDias' 				=> $resu->QtdeDias,
                    'tpDespesa' 			=> $resu->TipoDespesa,
                    'tpPessoa' 				=> $resu->TipoPessoa,
                    'nrContraPartida' 		=> $resu->Contrapartida,
                    'nrFonteRecurso' 		=> $resu->FonteRecurso,
                    'idUFDespesa' 			=> $resu->UfDespesa,
                    'idMunicipioDespesa' 	=> $resu->MunicipioDespesa,
                    'dsJustificativa' 		=> $resu->Justificativa,
                    'stAtivo' 				=> 'S'
                );
                $inserirPlanilhaAprovacao = $planilhaAprovacao->InserirPlanilhaAprovacao($data);
            }


            $RanaliseConteudo = $analiseConteudo->buscar(array('IdPRONAC = ?' => $idPronac));

            foreach ($RanaliseConteudo as $resu) {
                $data = array(
                    'tpAnalise' 			=> 'CO',
                    'dtAnalise' 			=> new Zend_Db_Expr('GETDATE()'),
                    'idAnaliseConteudo' 	=> $resu->idAnaliseDeConteudo,
                    'IdPRONAC' 				=> $resu->idPronac,
                    'idProduto' 			=> $resu->idProduto,
                    'stLei8313' 			=> $resu->Lei8313,
                    'stArtigo3' 			=> $resu->Artigo3,
                    'nrIncisoArtigo3' 		=> $resu->IncisoArtigo3,
                    'dsAlineaArt3' 			=> $resu->AlineaArtigo3,
                    'stArtigo18' 			=> $resu->Artigo18,
                    'dsAlineaArtigo18' 		=> $resu->AlineaArtigo18,
                    'stArtigo26' 			=> $resu->Artigo26,
                    'stLei5761' 			=> $resu->Lei5761,
                    'stArtigo27' 			=> $resu->Artigo27,
                    'stIncisoArtigo27_I' 	=> $resu->IncisoArtigo27_I,
                    'stIncisoArtigo27_II' 	=> $resu->IncisoArtigo27_II,
                    'stIncisoArtigo27_III' 	=> $resu->IncisoArtigo27_III,
                    'stIncisoArtigo27_IV' 	=> $resu->IncisoArtigo27_IV,
                    'stAvaliacao' 			=> $resu->ParecerFavoravel,
                    'dsAvaliacao' 			=> $resu->ParecerDeConteudo
                );
                $analiseaprovacao->inserirAnaliseAprovacao($data);
            }

            // chama a funcao para fazer o balanceamento
            
            //VERIFICA QUANTOS PRODUTOS O PROJETO POSSUI POR AREA
            $tbDistParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $rsProdutos = $tbDistParecer->BuscarQtdAreasProjetos($idPronac);
            $totalArea = $rsProdutos->QDTArea;
            if ($totalArea >= '2') {
                $area = 7; //Area = Artes integradas
            } else {
                //BUSCA AREA DO PROJETO
                $areaProjeto = $projetos->BuscarAreaSegmentoProjetos($idPronac);
                $area = $areaProjeto['area']; //Area do projeto
            }
            
            $Rtitulacao = $titulacaoConselheiro->buscarComponenteBalanceamento($area);
            $dados = array(
                'idPRONAC' 			=> $idPronac,
                'idAgente' 			=> $Rtitulacao['idagente'],
                'dtDistribuicao' 	=> new Zend_Db_Expr('GETDATE()'),
                'idResponsavel' 	=> 0
            );

            $Distribuicao->inserirDistribuicaoProjetoComissao($dados);


            // chama a funcao para alterar a situacao do projeto - Padrao C10
            $data = array(
                'Situacao' => 'C10'
            );
            $where = "IdPRONAC = $idPronac";
            $projetos->alterarProjetos($data, $where);

            parent::message("O projeto foi enviado para o Componente da Comiss&atilde;o!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
        } // fecha try
        catch (Exception $e) {
            $this->view->message = $e->getMessage();
        }
    }

    // fecha metodo enviarcomponentedacomissaoAction()
}

// fecha class
