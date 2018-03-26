<?php
/**
 * Controller Disvincular Agentes
 * @since 07/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @author Equipe RUP - Politec
 * @author Vinícius Feitosa da Silva <viniciusfesil@gmail.com>
 */
class Admissibilidade_GerenciarparecertecnicoController extends MinC_Controller_Action_Abstract
{
    private $bln_readequacao = "false";

    /**
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p�gina
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario = new UsuarioDAO(); // objeto usu�rio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usu�rio esteja autenticado
            // verifica as permiss�es
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 131; // Coordenador de Admissibilidade
            $PermissoesGrupo[] = 92;  // Tecnico de Admissibilidade
            $PermissoesGrupo[] = 97;  // Gestor do SALIC
            $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
            $PermissoesGrupo[] = 94;  // Parecerista
            $PermissoesGrupo[] = 121; // Tecnico
            $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
            $PermissoesGrupo[] = 103;
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org�os e grupos do usu�rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            // manda os dados para a vis�o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o
        } // fecha if
        else { // caso o usu�rio n�o esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    // fecha m�todo init()
    /**
     * Redireciona para o fluxo inicial do sistema
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        /** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao = $auth->getIdentity()->usu_orgao;
            
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
            //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
            $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
            
            $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        //$this->view->idorgao = $idorgao;
            /******************************************************************/
    }
    
    public function parecertecnicoAction()
    {
    }

    public function novoparecerAction()
    {
        if ($_REQUEST) {
            //$post = Zend_Registry::get('request');
            $pronac = $this->_request->getParam('pronac');
            $ano = addslashes(substr($pronac, 0, 2));
            $sequencial = addslashes(substr($pronac, 2, strlen($pronac)));


            $arrBusca = array(
               'tbr.anoprojeto =?' => $ano,
               'tbr.sequencial =?' => $sequencial,
           );
            $parecer = new Admissibilidade_Model_DbTable_Gerenciarparecertecnico();
            $validapronac = $parecer->VerificaPronac($arrBusca);
            if ($validapronac > 0) {
                $listaparecer = $parecer->listar_parecer($arrBusca);
                $this->view->parecer = $listaparecer;
            } else {
                parent::message("PRONAC n�o localizado", "/admissibilidade/gerenciarparecertecnico/parecertecnico", "ERROR");
            }
        }
    }
  
    
    public function imprimiretiquetaAction()
    {
        $pronac = null;
        $idpronac = null;
        if (isset($_POST['vpronac']) or isset($_GET['idPronac'])) {
            $pronac = $_POST['vpronac'];
            $etiquetaApenas = $_POST['etiqueta'];
            if (!empty($_GET['etiqueta'])) {
                $etiquetaApenas = $_GET['etiqueta'];
            }
            $this->view->etiquetaApenas = $etiquetaApenas;

            /*if(isset( $_GET['idPronac'] )){
                $idpronac = $_GET['idPronac'];
            }*/

            //$busca = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::BuscaProjeto($pronac);
            $tblProjeto = new Projetos();
            if (!empty($pronac)) {
                $rsProjeto = $tblProjeto->buscar(array('AnoProjeto + Sequencial=?'=>$pronac))->current();
                if (empty($rsProjeto)) {
                    parent::message("Pronac inexistente na base de dados!", "/admissibilidade/gerenciarparecertecnico/imprimiretiqueta", "ERROR");
                } else {
                    $this->_redirect('/admissibilidade/gerenciarparecertecnico/dadosetiqueta?pronac='.$pronac.'&etiqueta='.$etiquetaApenas);
                }
            } else {
                parent::message("Pronac inexistente na base de dados!", "/admissibilidade/gerenciarparecertecnico/imprimiretiqueta", "ERROR");
            }
        }
    }
    
    public function dadosetiquetaAction()
    {
        //ini_set('max_execution_time', 500);

        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        if (isset($_GET['pronac'])) {
            $pronac = $_GET['pronac'];
            $etiquetaApenas = "nao";
            if (!empty($_GET['etiqueta'])) {
                $etiquetaApenas = $_GET['etiqueta'];
            }
            $this->view->etiquetaApenas = $etiquetaApenas;

            $barcodeOptions = array('text' => $pronac);
            $rendererOptions = array();

            $documentRoot = explode("/index.php", $_SERVER["DOCUMENT_ROOT"].$_SERVER["PHP_SELF"]);
            $documentRoot = str_replace("//", "/", $documentRoot[0]);
            $caminho = $documentRoot."/public/barcode/imagem-".$pronac.".jpg";
//            $caminho = "../public/barcode/imagem-".$pronac.".jpg";
            $imageResource = Zend_Barcode::draw(
                'code39',
                'image',
                $barcodeOptions,
                $rendererOptions
            );
            imagejpeg($imageResource, $caminho);

            $caminhoView = "../public/barcode/imagem-".$pronac.".jpg";
            $this->view->caminho = $caminhoView;

            $projetosDAO = new Projetos();
            $consulta = array('pro.AnoProjeto + pro.Sequencial = ?'=>$pronac);
            $resp = $projetosDAO->buscarEditalProjeto($consulta);
            $idPreProjeto = $resp->idPreProjeto;
            if (!empty($resp->idEdital)) {
                $this->view->edital = true;
            } else {
                $this->view->edital = false;
            }

            //DADOS DA ETIQUETA
            $dados = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::dadosEtiqueta($pronac);
            $this->view->DadosEtiqueta = $dados;

            $dao = new Proposta_Model_AnalisarPropostaDAO();
            $this->view->itensGeral                 = $dao->buscarGeral($idPreProjeto);
            $propostaPorEdital = false;
            if ($this->view->itensGeral[0]->idEdital && $this->view->itensGeral[0]->idEdital != 0) {
                $propostaPorEdital = true;
            }

            $this->view->itensTelefone              = $dao->buscarTelefone($this->view->itensGeral[0]->idAgente);
            $this->view->itensPlanosDistribuicao    = $dao->buscarPlanoDeDistribucaoProduto($idPreProjeto);

            $this->view->itensFonteRecurso          = $dao->buscarFonteDeRecurso($idPreProjeto);
            $this->view->itensLocalRealiazacao      = $dao->buscarLocalDeRealizacao($idPreProjeto);
            $this->view->itensDeslocamento          = $dao->buscarDeslocamento($idPreProjeto);
            $this->view->itensPlanoDivulgacao       = $dao->buscarPlanoDeDivulgacao($idPreProjeto);

            $tblDocsPreProjeto = new Proposta_Model_DbTable_TbDocumentosPreProjeto();
            $rsDocsPreProjeto = $tblDocsPreProjeto->buscar(array("idProjeto = ?"=>$idPreProjeto));
            $this->view->itensDocumentoPreProjeto = $rsDocsPreProjeto;
            $this->view->itensDocumentoAgente       = $dao->buscarDocumentoAgente($this->view->itensGeral[0]->idAgente);
            $this->view->itensHistorico             = $dao->buscarHistorico($idPreProjeto);

            $this->view->itensPlanilhaOrcamentaria  = $dao->buscarPlanilhaOrcamentaria($idPreProjeto);

            $buscarProduto = ManterorcamentoDAO::buscarProdutos($idPreProjeto);
            $this->view->Produtos = $buscarProduto;

            $buscarEtapa = ManterorcamentoDAO::buscarEtapasProdutos($idPreProjeto);
            $this->view->Etapa = $buscarEtapa;

            $buscarItem = ManterorcamentoDAO::buscarItensProdutos($idPreProjeto);
            $this->view->Item = $buscarItem;
            $this->view->AnaliseCustos = PreProjeto::analiseDeCustos($idPreProjeto);
            $this->view->idPreProjeto = $idPreProjeto;

            $buscarIdPronac = $projetosDAO->buscarIdPronac($pronac);
            $idPronac = $buscarIdPronac->IdPRONAC;

            $rst = $projetosDAO->buscarDadosUC75($idPronac);
            $this->view->projeto = $rst[0];

            //UNIDADES DE AN�LISE
            $vwProjetoDistribuidoVinculada = new vwProjetoDistribuidoVinculada();
            $this->view->unidadesAnalise = $vwProjetoDistribuidoVinculada->buscarUnidades(array('Pronac = ?'=>$pronac), array('Produto','DescricaoAnalise'));

            //VALORES DO PROJETO
            $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
            $planilhaprojeto = new PlanilhaProjeto();
            $planilhaAprovacao = new PlanilhaAprovacao();

            $rsPlanilhaAtual = $planilhaAprovacao->buscar(array('IdPRONAC = ?'=>$idPronac), array('dtPlanilha DESC'))->current();
            $tpPlanilha = (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') ? 'SE' : 'CO';

            $arrWhereSomaPlanilha = array();
            $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
            if ($this->bln_readequacao == "false") {
                $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idPreProjeto, 109);
                $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idPreProjeto, false, 109);
                $parecerista    = $planilhaprojeto->somarPlanilhaProjeto($idPreProjeto, 109);
            } else {
                $arrWhereFontesIncentivo = $arrWhereSomaPlanilha;
                $arrWhereFontesIncentivo['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                $arrWhereFontesIncentivo['tpPlanilha = ? ']='SR';
                $arrWhereFontesIncentivo['stAtivo = ? ']='N';
                $arrWhereFontesIncentivo['NrFonteRecurso = ? ']='109';
                $arrWhereFontesIncentivo["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')");
                $arrWhereFontesIncentivo["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';
                $fonteincentivo = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereFontesIncentivo);

                $arrWhereOutrasFontes = $arrWhereSomaPlanilha;
                $arrWhereOutrasFontes['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                $arrWhereOutrasFontes['tpPlanilha = ? ']='SR';
                $arrWhereOutrasFontes['stAtivo = ? ']='N';
                $arrWhereOutrasFontes['NrFonteRecurso <> ? ']='109';
                $arrWhereOutrasFontes["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')");
                $arrWhereOutrasFontes["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';
                $outrasfontes = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereOutrasFontes);

                $arrWherePlanilhaPA = $arrWhereSomaPlanilha;
                $arrWherePlanilhaPA['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                $arrWherePlanilhaPA['tpPlanilha = ? ']='PA';
                $arrWherePlanilhaPA['stAtivo = ? ']='N';
                $arrWherePlanilhaPA['NrFonteRecurso = ? ']='109';
                $arrWherePlanilhaPA["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')");
                $arrWherePlanilhaPA["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';
                $parecerista = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWherePlanilhaPA);
            }
            //valor do componetne
            $arrWhereSomaPlanilha = array();
            $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
            $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
            $arrWhereSomaPlanilha['tpPlanilha = ? ']=$tpPlanilha;
            $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
            $arrWhereSomaPlanilha['stAtivo = ? ']='S';
            $componente = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

            $valoresProjeto = new ArrayObject();
            $valoresProjeto['fontesincentivo']  = $fonteincentivo['soma'];
            $valoresProjeto['outrasfontes']     = $outrasfontes['soma'];
            $valoresProjeto['valorproposta']    = $fonteincentivo['soma'] + $outrasfontes['soma'];
            $valoresProjeto['valorparecerista'] = $parecerista['soma'];
            $valoresProjeto['valorcomponente']  = $componente['soma'];
            $this->view->valoresDoProjeto = $valoresProjeto;

            $tblCaptacao = new Captacao();
            $rsCount = $tblCaptacao->buscaCompleta(array('idPronac = ?'=>$idPronac), array(), null, null, true);
            $this->view->totalGeralCaptado = $rsCount->totalGeralCaptado;
            /***************** FIM  - MODO NOVO ********************/


            if ($propostaPorEdital) {
                $tbFormDocumentoDAO = new tbFormDocumento();
                $edital = $tbFormDocumentoDAO->buscar(array('idEdital = ?'=>$this->view->itensGeral[0]->idEdital,'idClassificaDocumento not in (?,24,25)'=>23));

                $arrPerguntas = array();
                $arrRespostas = array();
                $tbPerguntaDAO = new tbPergunta();
                $tbRespostaDAO = new tbResposta();

                foreach ($edital as $registro) {
                    $questoes = $tbPerguntaDAO->montarQuestionario($registro["nrFormDocumento"], $registro["nrVersaoDocumento"]);
                    $questionario = '';
                    if (is_object($questoes) and count($questoes) > 0) {
                        foreach ($questoes as $questao) {
                            $resposta = '';
                            $where = array(
                                    'nrFormDocumento = ?'       =>$registro["nrFormDocumento"]
                                    ,'nrVersaoDocumento = ?'    =>$registro["nrVersaoDocumento"]
                                    ,'nrPergunta = ?'           =>$questao->nrPergunta
                                    ,'idProjeto = ?'            =>$idPreProjeto
                            );
                            $resposta = $tbRespostaDAO->buscar($where);
                            $arrPerguntas[$registro["nrFormDocumento"]]["titulo"] = $registro["nmFormDocumento"];
                            $arrPerguntas[$registro["nrFormDocumento"]]["pergunta"][] = $questao->toArray();
                            $arrRespostas[] = $resposta->toArray();
                        }
                    }
                }
                $this->view->perguntas = $arrPerguntas;
                $this->view->respostas = $arrRespostas;
            }
        }
        
        if (isset($_POST['html']) && isset($_POST['pronac'])&& isset($_POST['caminho'])) {
            ini_set('max_execution_time', 500);
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            /* converte para l� os arquivos html do word */
            //$html = mb_convert_encoding($_POST['html'], 'UTF-8', 'HTML-ENTITIES');
            //$html = $this->strip_quotes($this->unhtmlentities($_POST['html']));
            $filter = new Zend_Filter();
            //$filter->addFilter(new Zend_Filter_Alnum());
            //$filter->addFilter(new Zend_Filter_Alpha());
            //$filter->addFilter(new Zend_Filter_Digits());
            //$filter->addFilter(new Zend_Filter_HtmlEntities());
            $filter->addFilter(new Zend_Filter_StringTrim());
            $filter->addFilter(new Zend_Filter_StripTags());

//            $html = $filter->filter($_POST['html']);
            $html = $_POST['html'];
//            echo $html;
//            die;

            $pdf = new PDFCreator($html);
//            $pdf = new PDF($html, 'pdf');
            $pdf->gerarPdf();
//            $pdf->gerarRelatorio();
//            die;
            /*
            $html = $_POST['html'];
            $pdf = new PDF($html, 'pdf');
            echo $pdf->gerarRelatorio();*/
        }
    }
    
    public function gerarcodigodebarrasAction()
    {
        //$this->_helper->layout->disableLayout();
        
        $pronac = $_GET['pronac'];
        $barcodeOptions = array('text' => $pronac);
        $rendererOptions = array();

   
        $codigo = Zend_Barcode::factory(
                'code39',
   
             'image',
   
             $barcodeOptions,
   
             $rendererOptions
            )->draw();
        copy($codigo, 'D:/imagem');
    }
    
    public function imprimirparecertecnicoAction()
    {
        if (isset($_POST['vpronac'])) {
            $pronac = $_POST['vpronac'];
            $busca = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::BuscaProjeto($pronac);
            if (empty($busca)) {
                parent::message("Pronac Inexistente na Base de Dados!", "/admissibilidade/gerenciarparecertecnico/imprimirparecertecnico", "ALERT");
            } else {
                $this->_redirect('/admissibilidade/gerenciarparecertecnico/dadoshtml?pronac='.$pronac);
            }
        }
        
         
//     	echo $_POST['html'];
//      die();
    }
    
    public function dadoshtmlAction()
    {
        if (isset($_GET['pronac'])) {
            $pronac = $_GET['pronac'];

            $parecer = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::ParecerTecnico($pronac);
            $this->view->ParecerTecnico = $parecer;
            
            $analise = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::AnaliseConteudo($pronac);
            $this->view->AnaliseConteudo = $analise;

            $fonte = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::FonteRecurso($pronac);
            $this->view->FonteRecurso = $fonte;
            $produto = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::Produto($pronac);
            $this->view->Produto = $produto;
            
            $etapa = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::Etapa($pronac);
            $this->view->Etapa = $etapa;
            
            $uf = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::Uf($pronac);
            $this->view->Uf = $uf;
            
            $item = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::Item($pronac);
            $this->view->Item = $item;
            
            $unidade = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::Unidade($pronac);
            $this->view->Unidade = $unidade;
        }
        
        if (isset($_POST['html'])) {
            //echo ($_POST['html']);die;

            $html = $_POST['html'];

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
    
            $pdf = new PDF($html, 'pdf');
            $pdf->gerarRelatorio();
        
            //$this->_redirect('/gerenciarparecertecnico/imprimirparecertecnico');
        }
    }


    public function incluirparecerAction()
    {
        if ($_POST) {
            $auth = Zend_Auth::getInstance();                            //pega a autentica��o do usuario
            $Parecerista = $auth->getIdentity()->usu_nome;               //nome do usuario logado no sistema
            $Usuario = $auth->getIdentity()->usu_codigo;                 //codigo do usuario
            $post = Zend_Registry::get('post');                          //pega os post
            $pronac = addslashes($_POST['pronac']);                         //pega o post pronac
            $ano = addslashes(substr($pronac, 0, 2));                      //separa o ano do pronac
            $sequencial = addslashes(substr($pronac, 2, strlen($pronac))); //separa a sequencia


            $arrBusca = array( //busca para pegar os dados do projeto
               'tbr.anoprojeto =?' => $ano,
               'tbr.sequencial =?' => $sequencial,
            );

            $parecer = new Admissibilidade_Model_DbTable_Gerenciarparecertecnico();
            $validapronac = $parecer->VerificaPronac($arrBusca);
            if ($validapronac = 1) {
                $arrBusca = array( //busca para pegar os dados do projeto
                        'tbr.anoprojeto =?' => $ano,
                        'tbr.sequencial =?' => $sequencial,
                    );
                $dados = $parecer->listar_parecer($arrBusca);

                $dados_inserir = array(
                        "idPronac"              => $dados[0]->idPronac,
                        "idEnquadramento"       => $dados[0]->idEnquadramento,
                        "AnoProjeto"            => $dados[0]->AnoProjeto ,
                        "Sequencial"            => $dados[0]->Sequencial ,
                        "TipoParecer"           => $post->TipoParecer ,
                        "ParecerFavoravel"      => $post->ParecerFavoravel ,
                        "DtParecer"             => ConverteData(date("d/m/Y H:i:s"), 6) ,
                        "Parecerista"           => $Parecerista ,
                        "NumeroReuniao"         => $dados[0]->NumeroReuniao ,
                        "ResumoParecer"         => $post->ResumoParecer ,
                        "SugeridoCusteioReal"   => $post->SugeridoCusteioReal ,
                        "SugeridoCapitalReal"   => $post->SugeridoCapitalReal ,
                        "Atendimento"           => 'N' ,
                        "Logon"                 => $Usuario ,
                        "stAtivo"               => 1 ,
                        "idTipoAgente"          => 1
                    );
                $inserirparecer = Admissibilidade_Model_DbTable_Gerenciarparecertecnico::inserirparecer($dados_inserir);
                if ($inserirparecer) {
                    parent::message("Parecer inserido com sucesso!", "/admissibilidade/gerenciarparecertecnico/listaparecer?pronac=".$dados[0]->AnoProjeto."".$dados[0]->Sequencial."", "CONFIRM");
                } else {
                    parent::message("Ocorreu error em salvar Parecer", "/admissibilidade/gerenciarparecertecnico/parecertecnico", "ERROR");
                }
            } else {
                parent::message("PRONAC n�o localizado", "/admissibilidade/gerenciarparecertecnico/parecertecnico", "ERROR");
            }
        } else {
            parent::message("PRONAC n�o localizado", "/admissibilidade/gerenciarparecertecnico/parecertecnico", "ERROR");
        }
    }

    public function listaparecerAction()
    {
        $pronac = 0;
        
        if (!empty($_GET['pronac'])) {
            $pronac = addslashes($_GET['pronac']);
        } else {
            if (!empty($_POST['pronac'])) {
                $pronac = addslashes($_POST['pronac']);
            } else {
                parent::message("Informe o PRONAC", "/admissibilidade/gerenciarparecertecnico/parecertecnico", "ALERT");
            }
        }
        
        if ((int)$pronac > 0) {
            $ano = substr($pronac, 0, 2);
            $sequencial = addslashes(substr($pronac, 2, strlen($pronac)));

            $arrBusca = array(
               'tbr.anoprojeto =?' => $ano,
               'tbr.sequencial =?' => $sequencial,
           );
            $parecer = new Admissibilidade_Model_DbTable_Gerenciarparecertecnico();
            $validapronac = $parecer->VerificaPronac($arrBusca);
            if ($validapronac > 0) {
                $listaparecer = $parecer->listar_parecer($arrBusca);
                $this->view->listaparecer = $listaparecer;
            } else {
                parent::message("PRONAC n�o localizado", "/admissibilidade/gerenciarparecertecnico/parecertecnico", "ERROR");
            }
        }
    }

    public function dadosdoparecerAction()
    {
        $post = Zend_Registry::get('get');
        $parecer = addslashes($post->parecer);

        $arrBusca = array(
               'idParecer =?' => $parecer
           );
        $parecer = new Admissibilidade_Model_DbTable_Gerenciarparecertecnico();
        $validaparecer = $parecer->VerificaParecer($arrBusca);

        if ($validaparecer > 0) {
            $exibeparecer = $parecer->listar_parecer($arrBusca);
            $this->view->dados = $exibeparecer;
        } else {
            parent::message("Parecer n�o localizado", "/admissibilidade/gerenciarparecertecnico/parecertecnico", "ERROR");
        }
    }

    public function gerarpdfparecerAction()
    {
        $this->_helper->layout->disableLayout();
        $post = Zend_Registry::get('get');
        $pronac = addslashes($post->pronac);
        $ano = addslashes(substr($pronac, 0, 2));
        $sequencial = addslashes(substr($pronac, 2, strlen($pronac)));

        $arrBusca = array(
               'tbr.anoprojeto =?' => $ano,
               'tbr.sequencial =?' => $sequencial,
           );
        $parecer = new Admissibilidade_Model_DbTable_Gerenciarparecertecnico();
        $validapronac = $parecer->VerificaPronac($arrBusca);
        if ($validapronac > 0) {
            $listaparecer = $parecer->listar_parecer($arrBusca);
            $this->view->listaparecer = $listaparecer;

            $campo = array("Nome do Projeto","Area","Segmento","Resumo do Parecer","Tipo de Parecer","Parecer Favoravel","Data do Parecer","SugeridoReal","SugeridoCusteioReal","SugeridoCapitalReal","idParecer","idEnquadramento","AnoProjeto","Sequencial","Parecerista","SugeridoUfir","Atendimento");
            $html = "<html>";
            $html .= "<style> table{width:800px; font-size:9pt} td, th{border-bottom:1px #EEE solid;}th{background-color: #EEE;}</style>";
            $html .= "<center><h2>Impress�o Parecer</h2></center>";
            for ($x=0;$x < count($listaparecer);$x++) {
                $html .= "<h4>Parecer n.".($x+1)."</h4>";
                $html .= "<table>";

                for ($i=0;$i < count($campo);$i++) {
                    $html .= "<tr>
                                <td><b>".$campo[$i]."</b></td>
                                <td>";
                    switch ($i) {
                        case 0:
                            $html .= $listaparecer[$x]->NomeProjeto;
                            break;
                        case 1:
                            $html .= $listaparecer[$x]->AreaDescricao;
                            break;
                        case 2:
                            $html .= $listaparecer[$x]->SegmentoDescricao;
                            break;
                        case 3:
                            $html .= str_replace("\n", "<br>", strip_tags($listaparecer[0]->ResumoParecer));
                            break;
                        case 4:
                            $html .= $listaparecer[$x]->TipoParecer;
                            break;
                        case 5:
                            $html .= $listaparecer[$x]->ParecerFavoravel;
                            break;
                        case 6:
                            $html .= ConverteData(strtotime($listaparecer[$x]->DtParecer), 5);
                            break;
                        case 7:
                            $html .= number_format($listaparecer[$x]->SugeridoReal, 2, ',', '.');
                            break;
                        case 8:
                            $html .= number_format($listaparecer[$x]->SugeridoCusteioReal, 2, ',', '.');
                            break;
                        case 9:
                            $html .= number_format($listaparecer[$x]->SugeridoCapitalReal, 2, ',', '.');
                            break;
                        case 10:
                            $html .= $listaparecer[$x]->idParecer;
                            break;
                        case 11:
                            $html .= $listaparecer[$x]->idEnquadramento;
                            break;
                        case 12:
                            $html .= $listaparecer[$x]->AnoProjeto;
                            break;
                        case 13:
                            $html .= $listaparecer[$x]->Sequencial;
                            break;
                        case 14:
                            $html .= $listaparecer[$x]->Parecerista;
                            break;
                        case 15:
                            $html .= number_format($listaparecer[$x]->SugeridoUfir, 2, ',', '.');
                            break;
                        case 16:
                            $html .= $listaparecer[$x]->Atendimento;
                            break;
                      }


                    $html .="</td></tr>";
                }

                $html .= "</table><br>";
            }
            //x($html);
            $html .= "</html>";
            $pdf = new PDF($html, "pdf");
            xd($pdf->gerarRelatorio());
        } else {
            parent::message("PRONAC n�o localizado", "/admissibilidade/gerenciarparecertecnico/parecertecnico", "ERROR");
        }
    }
}
