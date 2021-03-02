<?php

/**
 * Controller Disvincular Agentes
 * @author Equipe RUP - Politec
 * @since 07/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright  2010 - Ministerio da Cultura - Todos os direitos reservados.
 */

class VerProjetosController extends MinC_Controller_Action_Abstract
{
    private $blnProponente  = false;
    private $blnProcurador  = false;
    private $intFaseProjeto = 0;
    private $intTamPag 	    = 10;
    protected $cpfLogado 	    = 0;
    protected $idResponsavel  = 0;
    protected $idAgente 	    = 0;
    private $bln_readequacao = "false";
    private $idPreProjeto   = 0;
    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */

    public function init()
    {
        parent::init();
    }


    /**
     * Redireciona para o fluxo inicial do sistema
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout'));
        
        if (isset($_REQUEST['idPronac'])) {
            $idPronac = $_GET['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }


            $dados = array();
            $dados['idPronac'] = (int) $idPronac;
            if (is_numeric($dados['idPronac'])) {
                if (isset($dados['idPronac'])) {
                    $idPronac = $dados['idPronac'];
                    //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
                    $this->view->idPronac = $idPronac;
                    $this->view->menumsg = 'true';
                }

                $tblProjetos = new Projetos();
                $rst = $tblProjetos->buscarDadosUC75($idPronac);
//                $rst = ConsultarDadosProjetoDAO::obterDadosProjeto($dados);

                //DEFINIE LINK PARA PLANILHA DE VALOR APROVADO
                $pp = new PlanilhaProjeto();
                $pa = new PlanilhaAprovacao();
                $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                $buscarsomaaprovacaoC = $pa->somarPlanilhaAprovacao($idPronac, 206, "CO");
                $buscarsomaaprovacaoP = $pa->somarPlanilhaAprovacao($idPronac, 206, "SE");

                if (isset($buscarsomaaprovacaoP['soma']) && $buscarsomaaprovacaoP['soma']>0) {
                    $this->view->linkplanilha = "plenaria";
                } elseif (isset($buscarsomaaprovacaoC['soma']) && $buscarsomaaprovacaoC['soma']>0) {
                    $this->view->linkplanilha = "cnic";
                } else {
                    $this->view->linkplanilha = "inicial";
                }

                if (count($rst) > 0) {
                    $this->view->projeto = $rst[0];
                    $this->view->idpronac = $idPronac;
                    $this->view->idprojeto = $rst[0]->idProjeto;
                    if ($rst[0]->codSituacao == 'E12' || $rst[0]->codSituacao == 'E13' || $rst[0]->codSituacao == 'E15' || $rst[0]->codSituacao == 'E50' || $rst[0]->codSituacao == 'E59' || $rst[0]->codSituacao == 'E61' || $rst[0]->codSituacao == 'E62') {
                        $this->view->menuCompExec = 'true';
                    }
                    $this->view->situacaoProjeto = $rst[0]->codSituacao;

                    $geral = new ProponenteDAO();

                    $arrBusca['IdPronac = ?']=$idPronac;
                    $rsProjeto = $tblProjetos->buscar($arrBusca)->current();
                    $idPreProjeto = 0;

                    if (!empty($rsProjeto->idProjeto)) {
                        $idPreProjeto = $rsProjeto->idProjeto;
                    }

                    $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;
                    $dadosProjeto = $geral->execPaProponente($idPronac);
                    $this->view->dados = $dadosProjeto;
                    $this->view->dadosProjeto = $rsProjeto;


                    //VERIFICA SE O PROJETO EST� NA CNIC //
                    $Parecer = new Parecer_Model_DbTable_Parecer();
                    $dadosCNIC = $Parecer->verificaProjSituacaoCNIC($pronac);

                    $msgCNIC = 0;
                    if (count($dadosCNIC)) {
                        $msgCNIC = 1;
                    }
                    $this->view->msgCNIC = $msgCNIC;
                    // FIM - VERIFICA SE O PROJETO EST� NA CNIC //


                    //VERIFICA OS DADOS DE ARQUIVAMENTO, CASO EXISTA //
                    $ArquivamentoProjeto = array();
                    $tbArquivamento = new tbArquivamento();
                    $dadosArquivamentoProjeto = $tbArquivamento->conferirArquivamentoProjeto($pronac);
                    if (count($dadosArquivamentoProjeto)) {
                        $ArquivamentoProjeto = $dadosArquivamentoProjeto;
                    }
                    $this->view->dadosArquivamentoProjeto = $ArquivamentoProjeto;
                    // FIM - VERIFICA OS DADOS DE ARQUIVAMENTO, CASO EXISTA //


                    $verificarHabilitado = $geral->verificarHabilitado($rst[0]->CgcCPf);
                    if (count($verificarHabilitado)>0) {
                        $this->view->ProponenteInabilitado = 1;
                    }

                    //VALORES DO PROJETO
                    $planilhaproposta = new Proposta_Model_DbTable_PlanilhaProposta();
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

                    /*** Valida��o do Proponente Inabilitado ************************************/

                    $cpfLogado 		= $this->cpfLogado;
                    $cpfProponente 	= !empty($dadosProjeto[0]->CNPJCPF) ? $dadosProjeto[0]->CNPJCPF : '';
                    $respProponente     = 'R';
                    $inabilitado 	= 'N';


                    // Verificando se o Proponente est� inabilitado
                    $inabilitadoDAO = new Inabilitado();
                    $where['CgcCpf 		= ?'] = $cpfProponente;
                    $where['Habilitado 	= ?'] = 'N';
                    $busca = $inabilitadoDAO->Localizar($where)->count();

                    if ($busca > 0) {
                        $inabilitado 	= 'S';
                    }

                    if (!empty($idPreProjeto)) {

                                // Se for Respons�vel verificar se tem Procura��o
                        $procuracaoDAO = new Procuracao();
                        $procuracaoValida 	= 'N';

                        $wherePro['vprp.idPreProjeto = ?'] 		= $idPreProjeto;
                        $wherePro['v.idUsuarioResponsavel = ?'] = $this->idResponsavel;
                        $wherePro['p.siProcuracao = ?'] 		= 1;
                        $buscaProcuracao = $procuracaoDAO->buscarProcuracaoProjeto($wherePro)->count();
                        if ($buscaProcuracao > 0) {
                            $procuracaoValida 	= 'S';
                        }
                    } else {
                        $procuracaoValida 	= 'S';
                    }

                    $this->view->procuracaoValida = $procuracaoValida;
                    $this->view->respProponente = $respProponente;
                    $this->view->inabilitado 	= $inabilitado;

                    /****************************************************************************/

                    $tbemail = $geral->buscarEmail($idPronac);
                    $this->view->email = $tbemail;

                    $tbtelefone = $geral->buscarTelefone($idPronac);
                    $this->view->telefone = $tbtelefone;

                    $tblAgente = new Agente_Model_DbTable_Agentes();
                    if (isset($dadosProjeto[0]->CNPJCPF) && !empty($dadosProjeto[0]->CNPJCPF)) {
                        $rsAgente = $tblAgente->buscar(array('CNPJCPF=?'=>$dadosProjeto[0]->CNPJCPF))->current();
                        $this->view->CgcCpf = $dadosProjeto[0]->CNPJCPF;
                    }

                    $rsIdAgente = (isset($rsAgente->idAgente) && !empty($rsAgente->idAgente)) ? $rsAgente->idAgente : 0;

                    $rsDirigentes = $tblAgente->buscarDirigentes(array('v.idVinculoPrincipal =?'=>$rsIdAgente,'n.Status =?'=>0), array('n.Descricao ASC'));
//                    $tbDirigentes = $geral->buscarDirigentes($idPronac);
                    $this->view->dirigentes = $rsDirigentes;

                    //========== inicio codigo mandato dirigente ================
                    /*==================================================*/
                    $arrMandatos = array();

                    if (!empty($this->idPreProjeto)) {
                        $preProjeto = new Proposta_Model_DbTable_PreProjeto();
                        $Empresa = $preProjeto->buscar(array('idPreProjeto = ?' => $this->idPreProjeto))->current();
                        $idEmpresa = $Empresa->idAgente;

                        $tbDirigenteMandato = new tbAgentesxVerificacao();
                        foreach ($rsDirigentes as $dirigente) {
                            $rsMandato = $tbDirigenteMandato->listarMandato(array('idEmpresa = ?' => $idEmpresa, 'idDirigente = ?' => $dirigente->idAgente,'stMandato = ?' => 0));
                            $arrMandatos[$dirigente->NomeDirigente] = $rsMandato;
                        }
                    }
                    $this->view->mandatos = $arrMandatos;

                    //============== fim codigo dirigente ================
                    /*==================================================*/

                    if (!empty($idPreProjeto)) {
                        //OUTROS DADOS PROPONENTE
                        $this->view->itensGeral = Proposta_Model_AnalisarPropostaDAO::buscarGeral($idPreProjeto);
                    }
                } else {
                    parent::message("Nenhum projeto encontrado com o n&uacute;mero de Pronac informado.", "listarprojetos/listarprojetos", "ERROR");
                }
            } else {
                parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
            }
        } else {
            parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
        }
    }

    public function index2Action()
    {
        if (isset($_REQUEST['idPronac'])) {
            $idPronac = $_GET['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }
            $verificaCompravacaoFinanceira = ConsultarDadosProjetoDAO::verificaComprovarExecucaoFinanceira($idPronac);

            if (!empty($verificaCompravacaoFinanceira)) {
                $this->view->menuCompExecFin = true;
            } else {
                $this->view->menuCompExecFin = false;
            }

            $dados = array();
            $dados['idPronac'] = (int) $_REQUEST['idPronac'];
            if (is_numeric($dados['idPronac'])) {
                if (isset($dados['idPronac'])) {
                    $idPronac = $dados['idPronac'];
                    //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
                    $this->view->idPronac = $idPronac;
                    $this->view->menumsg = 'true';
                }
                $rst = ConsultarDadosProjetoDAO::obterDadosProjeto($dados);

                if (count($rst) > 0) {
                    $this->view->projeto = $rst[0];
                    $this->view->idpronac = $_REQUEST['idPronac'];
                    $this->view->idprojeto = $rst[0]->idProjeto;
                    if ($rst[0]->codSituacao == 'E12' || $rst[0]->codSituacao == 'E13' || $rst[0]->codSituacao == 'E15' || $rst[0]->codSituacao == 'E50' || $rst[0]->codSituacao == 'E59' || $rst[0]->codSituacao == 'E61' || $rst[0]->codSituacao == 'E62') {
                        $this->view->menuCompExec = 'true';
                    }

                    $geral = new ProponenteDAO();
                    $tblProjetos = new Projetos();

                    $arrBusca['IdPronac = ?']=$idPronac;
                    $rsProjeto = $tblProjetos->buscar($arrBusca)->current();

                    $idPreProjeto = $rsProjeto->idProjeto;

                    $tbdados = $geral->buscarDadosProponente($idPronac);
                    $this->view->dados = $tbdados;

                    $tbemail = $geral->buscarEmail($idPronac);
                    $this->view->email = $tbemail;

                    $tbtelefone = $geral->buscarTelefone($idPronac);
                    $this->view->telefone = $tbtelefone;

                    $tblAgente = new Agente_Model_DbTable_Agentes();
                    $rsAgente = $tblAgente->buscar(array('CNPJCPF=?'=>$tbdados[0]->CgcCpf))->current();

                    $rsDirigentes = $tblAgente->buscarDirigentes(array('v.idVinculoPrincipal =?'=>$rsAgente->idAgente));
                    //$tbDirigentes = $geral->buscarDirigentes($idPronac);
                    $this->view->dirigentes = $rsDirigentes;
                    $arrMandatos = array();

                    $tbMandato = new tbMandato();
                    foreach ($rsDirigentes as $dirigente) {
                        $rsMandato = $tbMandato->listarMandato(array('idAgente = ?' => $dirigente->idAgente, 'stMandatoCancelado = ?' => 0));
                        $arrMandatos[$dirigente->idAgente] = $rsMandato;
                    }

                    xd($arrMandatos);
                    $this->view->mandatos = $buscarMandato;



                    $this->view->CgcCpf = $tbdados[0]->CgcCpf;

                    if (!empty($idPreProjeto)) {
                        //OUTROS DADOS PROPONENTE
                        $this->view->itensGeral = Proposta_Model_AnalisarPropostaDAO::buscarGeral($idPreProjeto);
                    }
                } else {
                    parent::message("Nenhum projeto encontrado com o n&uacute;mero de Pronac informado.", "listarprojetos/listarprojetos", "ERROR");
                }
            } else {
                parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
            }
        } else {
            parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
        }
    }

    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();

        if (isset($_REQUEST['idPronac'])) {
            $idPronac = $_REQUEST['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }
            $dados = array();
            $dados['idPronac'] = (int) $idPronac;
            if (is_numeric($dados['idPronac'])) {
                $rst = ConsultarDadosProjetoDAO::obterDadosProjeto($dados);
                if (count($rst) > 0) {
                    $this->view->projeto = $rst[0];
                    $this->view->idpronac = $idPronac;
                } else {
                    parent::message("Nenhum projeto encontrado com o n&uacute;mero de Pronac informado.", "listarprojetos/listarprojetos", "ERROR");
                }
            } else {
                parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
            }
        } else {
            parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
        }
    }

    public function planilhapdfAction()
    {
        $this->_helper->layout->disableLayout();

        $this->view->idPreProjeto = $_REQUEST['idPreProjeto'];
    }

    public function faseDoProjeto($idPronac)
    {
        if (!empty($idPronac)) {
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }
            $tblProjeto = new Projetos();
            $rsProjeto = $tblProjeto->buscar(array("IdPronac=?"=>$idPronac))->current();
            $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;
            $rsProjeto->DtFimExecucao = Data::tratarDataZend($rsProjeto->DtFimExecucao, 'americano');
            $dtFimPerExecucao = date('Ymd', strtotime($rsProjeto->DtFimExecucao));
            $dtAtual = date("Ymd");
            $diffDias = Data::CompararDatas($dtFimPerExecucao, $dtAtual);

            $tblAprovacao = new Aprovacao();
            $arrBuscaF1 = array();
            $arrBuscaF1['AnoProjeto+Sequencial = ?']= $pronac;
            $arrBuscaF1['TipoAprovacao = ?']= 1;
            $rsF1 = $tblAprovacao->buscar($arrBuscaF1);

            $arrBuscaF2 = array();
            $arrBuscaF2['AnoProjeto+Sequencial = ?']= $pronac;
            $arrBuscaF2['TipoAprovacao = ?']= 1;
            $arrBuscaF2['PortariaAprovacao IS NOT NULL']= '?';
            $rsF2 = $tblAprovacao->buscar($arrBuscaF2);

            $tbRelatorio = new tbRelatorio();
            $tbRelConsolidado = new tbRelatorioConsolidado();

            $arrBuscaRel = array();
            $rsF3 = array();
            $arrBuscaRel['idPronac = ?']=$idPronac;
            $arrBuscaRel['tpRelatorio = ?']='C';
            $arrBuscaRel['idDistribuicaoProduto is NOT NULL']='?';
            $rsRelatorio = $tbRelatorio->buscar($arrBuscaRel)->current();
            if (is_object($rsRelatorio) && count($rsRelatorio) > 0) {
                $arrBuscaF3 = array();
                $arrBuscaF3['idRelatorio = ?']= $rsRelatorio->idRelatorio;
                $rsF3 = $tbRelConsolidado->buscar($arrBuscaF3);
            }

            //situacoes fase Proj. Encerrado
            $arrSituacoes = array('E19','E22','L03');

            $tbRelatorioTec = new tbRelatorioTecnico();
            $arrBuscaF4 = array();
            $arrBuscaF4['idPronac = ?'] = $idPronac;
            $arrBuscaF4['cdGrupo IN (?)'] = array('125','126');
            $rsF4 = $tbRelatorioTec->buscar($arrBuscaF4);

            //FASE INICIAL
            if ($rsF1->count() == 0 && $rsF2->count() == 0) {
                $this->intFaseProjeto = 1;

                //FASE EXECUCAO
            } elseif ($rsF1->count() >= 1 && $rsF2->count() >= 1 && (!is_object($rsF3) || $rsF3->count() == 0)) {
                $this->intFaseProjeto = 2;

                //FASE FINAL
            } elseif ($rsF1->count() >= 1 && $rsF2->count() >= 1 && (is_object($rsF3) && $rsF3->count() >= 1) /*&& $diffDias > 30*/ && $rsF4->count() == 0) { //retirei a comparacao com os trinta dias para que entrem nessa fase projetoa que atendam a todas as condicoes mas ainda nao tiveram 30 dias passados da data fim de execucao
                $this->intFaseProjeto = 3;

                //FASE PROJETO ENCERRADO
            } elseif ($rsF1->count() >= 1 && $rsF2->count() >= 1 && (is_object($rsF3) && $rsF3->count() >= 1) && $diffDias > 30 && (in_array($rsProjeto->Situacao, $arrSituacoes) && $rsF4->count() >= 1)) {
                $this->intFaseProjeto = 4;
            }


            //FASE INICIAL
            /* nunca esteve na situacao E10 e nao ha registros na tabela captacao, os projetos por edital nao podem ser inclusos nessa condicao
             * para diferenciar pre-projetos de edital e fiscal quando o projeto nao tiver idProjeto deve-se utilizar o Mecanismo = 1
             * situacoes dessa fase = B11,B14,C10,C20,C30,D03,D11,D27
             * ENTENDIMENTO ATUAL - N�o ha registro na tabela aprovacao
             */

            //FASE DE EXECUCAO
            /* ja esteve na situacao E10 os projetos por edital nao podem ser inclusos nessa condicao
             * para diferenciar pre-projetos de edital e fiscal quando o projeto nao tiver idProjeto deve-se utilizar o Mecanismo = 1
             * ENTENDIMENTO ATUAL - Tem que haver um registro na tabela Aprovacao  com TipoAprovacao = 1 e com PortariaAprovacao.
             * pode-se utilizar a funcao (fnNrPortariaAprovacao) para checar essa informacao
             */

            //FASE FINAL
            /* 30 dias apos a data fim do periodo de execucao
             *
             */

            //FASE PROJETO ENCERRADO
            /* se tiver dados na tabela prestacao de contas com a analise de prestacao de contas ja finalizada
             * PROJETO nas situacoes E19, E22 e L03
             * Deve haver registro na tabela (tbRelatorioTecnico) com o codigo do grupo de COORD. DE PREST. DE CONTAS e COORD. GERAL DE PREST. DE CONTAS
             * e o Orgao onde o projeto esta e o 290(Arquivo)
             */
        }
    }

    // fecha matodo init()
    public function recuperarDadosProponenteAction()
    {
        $idpronac = $this->_request->getParam("idPronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $geral = new ProponenteDAO();
        $tblProjetos = new Projetos();

        $tbdados = $geral->buscarDadosProponente($idpronac);
        $this->view->dados = $tbdados;

        $tbemail = $geral->buscarEmail($idpronac);
        $this->view->email = $tbemail;

        $tbtelefone = $geral->buscarTelefone($idpronac);
        $this->view->telefone = $tbtelefone;

        $tbDirigentes = $geral->buscarDirigentes($idpronac);
        $this->view->dirigentes = $tbDirigentes;

        $this->view->CgcCpf = $tbdados[0]->CgcCpf;
    }

    public function certidoesNegativasAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $rs = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
        $this->view->projeto = $rs;

        $sv = new certidaoNegativa();
        $resultado = $sv->buscarCertidaoNegativa($rs->CgcCpf);
        $this->view->dados = $resultado;
    }

    public function dadosComplementaresAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tblProjeto = new Projetos();
        $projeto = $tblProjeto->buscar(array('IdPronac=?'=>$idPronac))->current();
        $this->view->dadosProjeto = $projeto;

        $this->view->idPronac = $idPronac;
        if (!empty($idPronac)) {
            $rsProjeto = $tblProjeto->buscar(array('IdPronac=?'=>$idPronac,'idProjeto IS NOT NULL'=>'?'))->current();
            $this->view->projeto = $rsProjeto;

            if (is_object($rsProjeto) && count($rsProjeto) > 0) {
                $tblProposta = new Proposta_Model_DbTable_PreProjeto();
                $rsProposta = $tblProposta->buscar(array('idPreProjeto=?'=>$rsProjeto->idProjeto))->current();
                $this->view->proposta = $rsProposta;
            }
        }
    }

    public function dadosProponenteAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        if (isset($_REQUEST['idPronac'])) {
            $idPronac = $_GET['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }

            $dados = array();
            $dados['idPronac'] = (int) $idPronac;
            if (is_numeric($dados['idPronac'])) {
                if (isset($dados['idPronac'])) {
                    $idPronac = $dados['idPronac'];
                    //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
                    $this->view->idPronac = $idPronac;
                    $this->view->menumsg = 'true';
                }
                $rst = ConsultarDadosProjetoDAO::obterDadosProjeto($dados);

                if (count($rst) > 0) {
                    $this->view->projeto = $rst[0];
                    $this->view->idpronac = $idPronac;
                    $this->view->idprojeto = $rst[0]->idProjeto;
                    if ($rst[0]->codSituacao == 'E12' || $rst[0]->codSituacao == 'E13' || $rst[0]->codSituacao == 'E15' || $rst[0]->codSituacao == 'E50' || $rst[0]->codSituacao == 'E59' || $rst[0]->codSituacao == 'E61' || $rst[0]->codSituacao == 'E62') {
                        $this->view->menuCompExec = 'true';
                    }

                    $geral = new ProponenteDAO();
                    $tblProjetos = new Projetos();

                    $arrBusca['IdPronac = ?']=$idPronac;
                    $rsProjeto = $tblProjetos->buscar($arrBusca)->current();
                    $idPreProjeto = 0;

                    if (!empty($rsProjeto->idProjeto)) {
                        $idPreProjeto = $rsProjeto->idProjeto;
                    }

                    $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;
                    $dadosProjeto = $geral->execPaProponente($idPronac);
                    $this->view->dados = $dadosProjeto;

                    $verificarHabilitado = $geral->verificarHabilitado($pronac);
                    if (count($verificarHabilitado)>0) {
                        $this->view->ProponenteInabilitado = 1;
                    }

                    $tbemail = $geral->buscarEmail($idPronac);
                    $this->view->email = $tbemail;

                    $tbtelefone = $geral->buscarTelefone($idPronac);
                    $this->view->telefone = $tbtelefone;

                    $tblAgente = new Agente_Model_DbTable_Agentes();
                    $rsAgente = $tblAgente->buscar(array('CNPJCPF=?'=>$dadosProjeto[0]->CNPJCPF))->current();

                    $rsIdAgente = (isset($rsAgente->idAgente) && !empty($rsAgente->idAgente)) ? $rsAgente->idAgente : 0;

                    $rsDirigentes = $tblAgente->buscarDirigentes(array('v.idVinculoPrincipal =?'=>$rsIdAgente,'n.Status =?'=>0), array('n.Descricao ASC'));
                    $this->view->dirigentes = $rsDirigentes;

                    //========== inicio codigo mandato dirigente ================
                    $arrMandatos = array();

                    if (!empty($this->idPreProjeto)) {
                        $preProjeto = new Proposta_Model_DbTable_PreProjeto();
                        $Empresa = $preProjeto->buscar(array('idPreProjeto = ?' => $this->idPreProjeto))->current();
                        $idEmpresa = $Empresa->idAgente;

                        $tbDirigenteMandato = new tbAgentesxVerificacao();
                        foreach ($rsDirigentes as $dirigente) {
                            $rsMandato = $tbDirigenteMandato->listarMandato(array('idEmpresa = ?' => $idEmpresa, 'idDirigente = ?' => $dirigente->idAgente,'stMandato = ?' => 0));
                            $arrMandatos[$dirigente->NomeDirigente] = $rsMandato;
                        }
                    }
                    $this->view->mandatos = $arrMandatos;
                } else {
                    parent::message("Nenhum projeto encontrado com o n&uacute;mero de Pronac informado.", "listarprojetos/listarprojetos", "ERROR");
                }
            } else {
                parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
            }
        } else {
            parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
        }
    }

    public function planoDeDistribuicaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

        if (!empty($idPronac)) {
            $buscarDistribuicao = RealizarAnaliseProjetoDAO::planodedistribuicao($idPronac);
            $this->view->dados = $buscarDistribuicao;
        }
    }

    public function localRealizacaoDeslocamentoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

        if (!empty($idPronac)) {
            $buscarLocalRealizacao = RealizarAnaliseProjetoDAO::localrealizacao($idPronac);
            $this->view->dadosLocalizacao = $buscarLocalRealizacao;

            $buscarDeslocamento = RealizarAnaliseProjetoDAO::deslocamento($idPronac);
            $this->view->dadosDeslocamento = $buscarDeslocamento;
        }
    }

    public function planoDeDivulgacaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

        if (!empty($idPronac)) {
            $buscarDivulgacao = RealizarAnaliseProjetoDAO::divulgacao($idPronac);
            $this->view->dados = $buscarDivulgacao;

            $pronac = $this->view->projeto->AnoProjeto . $this->view->projeto->Sequencial;
            $tbArquivoImagem = new tbArquivoImagem();
            $this->view->marcas = $tbArquivoImagem->marcasAnexadas($pronac);
        }
    }

    public function planilhaOrcamentariaAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();
            $this->view->tipoPlanilha = 1;
        }
    }

    public function planilhaOrcamentariaAprovadaAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

            $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
            $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, 6);

            if (count($planilhaOrcamentaria)>0) {
                $this->view->tipoPlanilha = 6;
            } else {
                $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, 3);
                if (count($planilhaOrcamentaria)>0) {
                    $this->view->tipoPlanilha = 3;
                } else {
                    $this->view->tipoPlanilha = 2;
                }
            }
        }
    }

    public function documentosAnexadosAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $projeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
        $this->view->projeto = $projeto;

        $tbDoc = new paDocumentos();
        $rs = $tbDoc->marcasAnexadas($idPronac);
        $this->view->registros = $rs;
    }

    public function readequacaoAction()
    {
        ini_set('memory_limit', '-1');
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // pega o idpronac
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = !empty($idPronac) ? $idPronac : 0;

        // objetos
        $Projetos      = new Projetos();
        $PreProjeto    = new Proposta_Model_DbTable_PreProjeto();
        $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();

        // busca os dados aprovados do proponente e do nome do projeto
        $buscarProponente = $Projetos->buscarProjetoXProponente(array('p.IdPRONAC = ?' => $idPronac))->current();
        $this->view->dadosProjeto = $buscarProponente; // manda as informa��es para a vis�o

        // busca os dados aprovados da ficha t�cnica e da proposta pedag�gica
        $buscarPedido = $PreProjeto->buscar(array('idPreProjeto = ?' => $this->idPreProjeto))->current();
        $this->view->dadosPedido = $buscarPedido; // manda as informa��es para a vis�o

        // busca os dados aprovados dos locais de realiza��o
        $orderAbrangencia = array('p.Descricao', 'u.Sigla', 'm.Descricao');
        $buscarLocais = $tbAbrangencia->buscarLocaisAprovados(array('a.idProjeto = ?' => $this->idPreProjeto, 'a.stAbrangencia = ?' => 1), $orderAbrangencia);
        $this->view->dadosLocais = $buscarLocais; // manda as informa��es para a vis�o

        // busca os dados aprovados do prazo de execu��o
        $buscarPrazoExecucao = $Projetos->buscarPeriodoExecucao($idPronac);
        $this->view->dadosPrazoExecucao = $buscarPrazoExecucao; // manda as informa��es para a vis�o

        $buscarPrazoCaptacao = $Projetos->buscarPeriodoCaptacao($idPronac, null, null, false, array('TipoAprovacao = ?' => 1, 'PortariaAprovacao IS NOT NULL' => ''));
        $this->view->dadosPrazoCaptacao = $buscarPrazoCaptacao; // manda as informa��es para a vis�o

        $buscarDistribuicao = RealizarAnaliseProjetoDAO::planodedistribuicao($idPronac);
        $this->view->dadosProdutos = $buscarDistribuicao;


        $tblPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $tblPlanilhaProjeto = new PlanilhaProjeto();
        $tblPlanilhaAprovacao = new PlanilhaAprovacao();
        $tblProjetos = new Projetos();

        $rsPlanilhaAtual = $tblPlanilhaAprovacao->buscar(array('IdPRONAC = ?'=>$idPronac, 'stAtivo = ?'=>'S'), array('dtPlanilha DESC'))->current();
        $status = (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') ? 'N' : 'S';

        $tblProjetos = new Projetos();
        $arrBusca['IdPronac = ?']=$idPronac;
        $rsProjeto = $tblProjetos->buscar($arrBusca)->current();
        $idPreProjeto = $rsProjeto->idProjeto;

        if (!empty($idPreProjeto)) {
            $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
            $pp = new PlanilhaProjeto();
            $pr = new Projetos();
            $PlanilhaDAO = new PlanilhaProjeto();
            $where = array('PP.idProjeto = ?' => $idPreProjeto);
            $buscarplanilha = $PlanilhaDAO->buscarAnaliseCustos($where);

            $planilhaproposta = array();
            $count = 0;
            $fonterecurso = null;
            foreach ($buscarplanilha as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaProjeto'] = $resuplanilha->idPlanilhaProjeto;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                $count++;

                $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                if (isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)) {
                    $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                    $this->view->totalproponenteAP = $buscarsomaproposta['soma'];
                } else {
                    $this->view->totalproponenteAP = '0.00';
                }

                $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                $this->view->planilhaUnidadeAP = $buscarPlanilhaUnidade;
                $this->view->planilhaAP = $planilhaproposta;
                $this->view->projetoAP = $buscarprojeto;
            }
        }


        $rsPlanilhaAtual = $tblPlanilhaAprovacao->buscar(array('IdPRONAC = ?'=>$idPronac, 'stAtivo = ?'=>'S'), array('dtPlanilha DESC'))->current();
        $status = (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') ? 'N' : 'S';

        $tblProjetos = new Projetos();
        $arrBusca['IdPronac = ?']=$idPronac;
        $rsProjeto = $tblProjetos->buscar($arrBusca)->current();
        $idPreProjeto = $rsProjeto->idProjeto;

        if (!empty($idPreProjeto)) {
            $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
            $pp = new PlanilhaProjeto();
            $pr = new Projetos();
            $PlanilhaDAO = new PlanilhaProjeto();
            $where = array('PP.idProjeto = ?' => $idPreProjeto);
            $buscarplanilha = $PlanilhaDAO->buscarAnaliseCustos($where);

            $planilhaproposta = array();
            $count = 0;
            $fonterecurso = null;
            foreach ($buscarplanilha as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaProjeto'] = $resuplanilha->idPlanilhaProjeto;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;
                $planilhaproposta[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                $count++;

                $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                if (isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)) {
                    $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                    $this->view->totalproponenteAAP = $buscarsomaproposta['soma'];
                } else {
                    $this->view->totalproponenteAAP = '0.00';
                }

                $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                $this->view->planilhaUnidadeAAP = $buscarPlanilhaUnidade;
                $this->view->planilhaAAP = $planilhaproposta;
                $this->view->projetoAAP = $buscarprojeto;
            }
        }
    }

    public function readequacoesAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $tblProjeto = new Projetos();
            $rsProjeto = $tblProjeto->buscar(array("IdPronac=?"=>$idPronac))->current();
            $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;
            $this->view->projeto = $rsProjeto;

            $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $dadosReadequacoes = $Readequacao_Model_DbTable_TbReadequacao->buscarDadosReadequacoes(array('a.idPronac = ?'=>$idPronac, 'a.siEncaminhamento <> ?'=>12))->toArray();

            $tbReadequacaoXParecer = new Readequacao_Model_DbTable_TbReadequacaoXParecer();
            foreach ($dadosReadequacoes as &$dr) {
                $dr['pareceres'] = $tbReadequacaoXParecer->buscarPareceresReadequacao(array('a.idReadequacao = ?'=>$dr['idReadequacao']))->toArray();
            }
            $this->view->readequacoes = $dadosReadequacoes;
        }
    }

    public function abrirDocumentosAnexadosAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $id  = $this->_request->getParam('id');
        $tipo  = $this->_request->getParam('tipo');
        $tipoDoc = null;
        $bln = "false";

        $tipoDoc = 0;
        if ($tipo == '1') {
            $tipoDoc = "tbDocumentosAgentes"; //SAC.dbo.tbDocumentosAgentes
        } elseif ($tipo == '2') {
            $tipoDoc = "tbDocumentosPreProjeto"; //SAC.dbo.tbDocumentosPreProjeto
        } elseif ($tipo == '3') {
            $tipoDoc = "tbDocumento"; //SAC.dbo.tbDocumento
        }

        // Configura��o o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        if ($tipo == 1 || $tipo == 2 || $tipo == 3) {
            // busca o arquivo
            $resultado = UploadDAO::abrirdocumentosanexados($id, $tipoDoc);
            if (count($resultado) > 0) {
                if ($tipo == 1) {
                    $this->forward("abrirdocumentosanexadosbinario", "anexospublicos", "", array('id'=>$id,'busca'=>$tipoDoc));
                } else {
                    $this->forward("abrirdocumentosanexados", "anexospublicos", "", array('id'=>$id,'busca'=>$tipoDoc));
                }
                $bln = "true";
            }
        } else {
            // busca o arquivo
            $resultado = UploadDAO::abrir($id);
            if (count($resultado) > 0) {
                $this->forward("abrir", "upload", "", array('id'=>$id));
                $bln = "true";
            }
        }

        if ($bln == "false") {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl()."/verprojetos/?idPronac={$idPronac}";
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage("N&atilde;o foi poss&iacute;vel abrir o arquivo especificado. Tente anex&aacute;-lo novamente.");
            $this->_helper->flashMessengerType->addMessage("ERROR");
            JS::redirecionarURL($url);
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function tramitacaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();
    }

    public function tramitacaoProjetoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (!empty($idPronac)) {
            $post   = Zend_Registry::get('post');
            $this->intTamPag = 10;

            $arrBusca = array();
            $pag = 1;
            //$get = Zend_Registry::get('get');
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim = $inicio + $this->intTamPag;

            $tblHistDoc = new tbHistoricoDocumento();
            $total = $tblHistDoc->buscarHistoricoTramitacaoProjeto(array("p.IdPronac =?"=>$idPronac), array(), null, null, true);


            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            $ordem = array("1 DESC");
            //if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }

            $rsHistDoc = $tblHistDoc->buscarHistoricoTramitacaoProjeto(array("p.IdPronac =?"=>$idPronac), $ordem, $tamanho, $inicio);

            $this->view->registros = $rsHistDoc;
            $this->view->pag = $pag;
            $this->view->total = $total;
            $this->view->inicio = ($inicio+1);
            $this->view->fim = $fim;
            $this->view->totalPag = $totalPag;
            $this->view->parametrosBusca = $_POST;
        }
    }

    public function tramitacaoDocumentoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (!empty($idPronac)) {
            $post   = Zend_Registry::get('post');
            $this->intTamPag = 10;

            $arrBusca = array();
            $pag = 1;
            //$get = Zend_Registry::get('get');
            if (isset($post->pagDoc)) {
                $pag = $post->pagDoc;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim = $inicio + $this->intTamPag;

            $arrBusca = array();
            $arrBusca['h.idDocumento <> ?']=0;
            $arrBusca['h.stEstado = ?']=1;
            $arrBusca['p.IdPronac =?']=$idPronac;
            $tblHistDoc = new tbHistoricoDocumento();
            $total = $tblHistDoc->buscarHistoricoTramitacaoDocumento($arrBusca, array(), null, null, true);


            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            $ordem = array("9 DESC");
            //if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }

            $rsHistDoc = $tblHistDoc->buscarHistoricoTramitacaoDocumento($arrBusca, $ordem, $tamanho, $inicio);

            $this->view->registros = $rsHistDoc;
            $this->view->pagDoc = $pag;
            $this->view->totalDoc = $total;
            $this->view->inicioDoc = ($inicio+1);
            $this->view->fimDoc = $fim;
            $this->view->totalPag = $totalPag;
            $this->view->parametrosBuscaDoc = $_POST;
        }
    }

    public function abrirDocumentoTramitacaoAction()
    {
        $id  = $this->_request->getParam("id");
        ;

        // Configura��o o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        // busca o arquivo
        $resultado = TramitarDocumentosDAO::buscarDoc($id);

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->view->message      = 'N�o foi poss�vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // l� os cabe�alhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $this->getResponse()
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $r->NoArquivo . '"')
                        ->setHeader("Connection", "close")
                        ->setHeader("Content-transfer-encoding", "binary")
                        ->setHeader("Cache-control", "private");

                if ($r->biDocumento == null) {
                    $this->getResponse()->setBody(base64_decode($r->imDocumento));
                } else {
                    $this->getResponse()->setBody($r->biDocumento);
                }
            }
        }
    }

    public function providenciaTomadaAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {

            //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
            if ($this->_request->getParam("qtde")) {
                $this->intTamPag = $this->_request->getParam("qtde");
            }
            $order = array();

            //==== parametro de ordenacao  ======//
            if ($this->_request->getParam("ordem")) {
                $ordem = $this->_request->getParam("ordem");
                if ($ordem == "ASC") {
                    $novaOrdem = "DESC";
                } else {
                    $novaOrdem = "ASC";
                }
            } else {
                $ordem = "ASC";
                $novaOrdem = "ASC";
            }

            //==== campo de ordenacao  ======//
            if ($this->_request->getParam("campo")) {
                $campo = $this->_request->getParam("campo");
                $order = array($campo." ".$ordem);
                $ordenacao = "&campo=".$campo."&ordem=".$ordem;
            } else {
                $campo = null;
                $order = array(1); //Contador (id da tabela)
                $ordenacao = null;
            }

            $pag = 1;
            $get = Zend_Registry::get('get');
            if (isset($get->pag)) {
                $pag = $get->pag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

            /* ================== PAGINACAO ======================*/
            $where = array();
            $where['p.IdPRONAC = ?'] = $get->idPronac;
            $this->view->idPronac = $get->idPronac;

            $tblHisSituacao = new HistoricoSituacao();
            $total = $tblHisSituacao->buscarHistoricosEncaminhamentoIdPronac($where, $order, null, null, true);
            $fim = $inicio + $this->intTamPag;

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

            $busca = $tblHisSituacao->buscarHistoricosEncaminhamentoIdPronac($where, $order, $tamanho, $inicio);
            $paginacao = array(
                "pag"=>$pag,
                "qtde"=>$this->intTamPag,
                "campo"=>$campo,
                "ordem"=>$ordem,
                "ordenacao"=>$ordenacao,
                "novaOrdem"=>$novaOrdem,
                "total"=>$total,
                "inicio"=>($inicio+1),
                "fim"=>$fim,
                "totalPag"=>$totalPag,
                "Itenspag"=>$this->intTamPag,
                "tamanho"=>$tamanho
            );

            $this->view->paginacao     = $paginacao;
            $this->view->qtdRegistros  = $total;
            $this->view->dados         = $busca;
            $this->view->intTamPag     = $this->intTamPag;
        } else {
            $idPronacCriptado = Seguranca::encrypt($idPronac);
            parent::message("N�o foi encontrado nenhum projeto!", "verprojetos?idPronac=$idPronacCriptado", "ERROR");
        }
    }

    public function imprimirProvidenciaTomadaAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //Contador (id da tabela)
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('post');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        $where = array();
        $where['p.IdPRONAC = ?'] = $get->idPronac;
        $this->view->idPronac = $get->idPronac;

        $tblHisSituacao = new HistoricoSituacao();
        $total = $tblHisSituacao->buscarHistoricosEncaminhamentoIdPronac($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tblHisSituacao->buscarHistoricosEncaminhamentoIdPronac($where, $order, $tamanho, $inicio);

        $nrPronac = '';
        $nrPronacNm = '';
        if (count($busca) > 0) {
            $nrPronac = ' - Pronac: '.$busca[0]->Pronac;
            $nrPronacNm = '_Pronac_'.$busca[0]->Pronac;
        }

        if (isset($get->xls) && $get->xls) {
            $html = '';
            $html .= '<table style="border: 1px">';
            if (!in_array($GrupoAtivo->codGrupo, array(90,91,94,104,105,115,118,130,1111))) {
                $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 16; font-weight: bold;" colspan="5">Consultar dados do Projeto - Provid�ncia Tomada'. $nrPronac .'</td></tr>';
                $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 10" colspan="5">Data do Arquivo: '. Data::mostraData() .'</td></tr>';
                $html .='<tr><td colspan="5"></td></tr>';
            } else {
                $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 16; font-weight: bold;" colspan="3">Consultar dados do Projeto - Provid�ncia Tomada'. $nrPronac .'</td></tr>';
                $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 10" colspan="3">Data do Arquivo: '. Data::mostraData() .'</td></tr>';
                $html .='<tr><td colspan="3"></td></tr>';
            }

            $html .= '<tr>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Dt. Situa��o</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Situa��o</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Provid�ncia Tomada</th>';
            if (!in_array($GrupoAtivo->codGrupo, array(90,91,94,104,105,115,118,130,1111))) {
                $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">CPF</th>';
                $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Nome</th>';
            }
            $html .= '</tr>';

            foreach ($busca as $v) {
                $nrCpf = trim($v->cnpjcpf);
                if ($nrCpf == '23969156149') {
                    $Cpf = '-';
                    $nomeUser = 'Sistema SALIC';
                } else {
                    $Cpf = (strlen($nrCpf) > 11) ? Mascara::addMaskCNPJ($nrCpf) : Mascara::addMaskCPF($nrCpf);
                    $nomeUser = $v->usuario;
                }

                $html .= '<tr>';
                $html .= '<td style="border: 1px dotted black;">'.Data::tratarDataZend($v->DtSituacao, 'Brasileira').'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->Situacao.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$v->ProvidenciaTomada.'</td>';
                if (!in_array($GrupoAtivo->codGrupo, array(90,91,94,104,105,115,118,130,1111))) {
                    $html .= '<td style="border: 1px dotted black;">'.$Cpf.'</td>';
                    $html .= '<td style="border: 1px dotted black;">'.$nomeUser.'</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</table>';

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=Providencia_Tomada".$nrPronacNm.".ods;");
            echo $html;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->view->nrPronac      = $nrPronac;
            $this->view->qtdRegistros  = $total;
            $this->view->dados         = $busca;
            $this->view->intTamPag     = $this->intTamPag;
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        }
    }

    public function recursoAction()
    {
        $mapperArea = new Agente_Model_AreaMapper();

        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $rsProjeto = $Projetos->buscar(array("IdPronac=?"=>$idPronac))->current();
            $this->view->projeto = $rsProjeto;

            // verifica se h� pedidos de reconsidera��o e de recurso
            $tbRecurso = new tbRecurso();
            $recursos = $tbRecurso->buscar(array('IdPRONAC = ?'=>$idPronac));
            $pedidoReconsideracao = 0;
            $pedidoRecurso = 0;

            if (count($recursos)>0) {
                foreach ($recursos as $r) {
                    if ($r->tpRecurso == 1) {
                        $pedidoReconsideracao = $r->idRecurso;
                        $dados = $tbRecurso->buscarDadosRecursos(array('idRecurso = ?'=>$r->idRecurso))->current();
                        $this->view->dadosReconsideracao = $dados;

                        if ($r->siRecurso == 0) {
                            $this->view->desistenciaReconsideracao = true;
                        } else {
                            $this->view->desistenciaReconsideracao = false;
                            $this->view->produtosReconsideracao = array();

                            if ($dados->siFaseProjeto == 2) {
                                if ($dados->tpSolicitacao == 'PI' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR') {
                                    $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
                                    $dadosProdutos = $PlanoDistribuicaoProduto->buscarProdutosProjeto($dados->IdPRONAC);
                                    $this->view->produtosReconsideracao = $dadosProdutos;

                                    $tipoDaPlanilha = 3; // 3=Planilha Or�ament�ria Aprovada Ativa
//                                    if($dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR'){
//                                        $tipoDaPlanilha = 4; // 4=Cortes Or�ament�rios Aprovados
//                                    }
                                    $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
                                    $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($dados->IdPRONAC, $tipoDaPlanilha);
                                    $this->view->planilhaReconsideracao = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, $tipoDaPlanilha);
                                }
                            }
                            if ($dados->tpSolicitacao == 'EN' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR' || $dados->tpSolicitacao == 'PI') {
                                $this->view->projetosENReconsideracao = $Projetos->buscaAreaSegmentoProjeto($dados->IdPRONAC);

                                $this->view->comboareasculturaisReconsideracao = $mapperArea->fetchPairs('codigo', 'descricao');
                                $objSegmentocultural = new Segmentocultural();
                                $this->view->combosegmentosculturaisReconsideracao = $objSegmentocultural->buscarSegmento($this->view->projetosENReconsideracao->cdArea);

                                $parecer = new Parecer_Model_DbTable_Parecer();
                                $this->view->ParecerReconsideracao = $parecer->buscar(array('IdPRONAC = ?' => $dados->IdPRONAC, 'TipoParecer in (?)' => array(1,7), 'stAtivo = ?' => 1))->current();
                            }
                        }
                    }

                    if ($r->tpRecurso == 2) {
                        $pedidoRecurso = $r->idRecurso;
                        $dados = $tbRecurso->buscarDadosRecursos(array('idRecurso = ?'=>$r->idRecurso))->current();
                        $this->view->dadosRecurso = $dados;

                        if ($r->siRecurso == 0) {
                            $this->view->desistenciaRecurso = true;
                        } else {
                            $this->view->desistenciaRecurso = false;
                            if ($dados->siFaseProjeto == 2) {
                                if ($dados->tpSolicitacao == 'PI' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR') {
                                    $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
                                    $dadosProdutos = $PlanoDistribuicaoProduto->buscarProdutosProjeto($dados->IdPRONAC);
                                    $this->view->produtosRecurso = $dadosProdutos;

                                    $tipoDaPlanilha = 2; // 2=Planilha Aprovada Parecerista
                                    if ($dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR') {
                                        $tipoDaPlanilha = 4; // 4=Cortes Or�ament�rios Aprovados
                                    }
                                    $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
                                    $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($dados->IdPRONAC, $tipoDaPlanilha);
                                    $this->view->planilhaRecurso = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, $tipoDaPlanilha);
                                }
                            }
                            if ($dados->tpSolicitacao == 'EN' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR' || $dados->tpSolicitacao == 'PI') {
                                $this->view->projetosENRecurso = $Projetos->buscaAreaSegmentoProjeto($dados->IdPRONAC);

                                $this->view->comboareasculturaisReconsideracao = $mapperArea->fetchPairs('codigo', 'descricao');
                                $objSegmentocultural = new Segmentocultural();
                                $this->view->combosegmentosculturaisRecurso = $objSegmentocultural->buscarSegmento($this->view->projetosENRecurso->cdArea);

                                $parecer = new Parecer_Model_DbTable_Parecer();
                                $this->view->ParecerRecurso = $parecer->buscar(array('IdPRONAC = ?' => $dados->IdPRONAC, 'TipoParecer = ?' => 7, 'stAtivo = ?' => 1))->current();
                            }
                        }
                    }
                }
            }
            $this->view->pedidoReconsideracao = $pedidoReconsideracao;
            $this->view->pedidoRecurso = $pedidoRecurso;
        }
    }

    public function aprovacaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $tblProjeto = new Projetos();
            $rsProjeto = $tblProjeto->buscar(array("IdPronac=?"=>$idPronac))->current();
            $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;
            $this->view->projeto = $rsProjeto;

            $tblAprovacao = new Aprovacao();
            $rsAprovacao = $tblAprovacao->buscaCompleta(array('a.AnoProjeto + a.Sequencial = ?'=>$pronac), array('a.idAprovacao ASC'));
            $this->view->dados = $rsAprovacao;
        }
    }

    public function dadosBancariosAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = $idPronac;

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

            $tblContaBancaria = new ContaBancaria();
            $rsContaBancaria = $tblContaBancaria->contaPorProjeto($idPronac);
            $this->view->dadosContaBancaria = $rsContaBancaria;

            $tbLiberacao = new Liberacao();
            $rsLiberacao = $tbLiberacao->liberacaoPorProjeto($idPronac);
            $this->view->dadosLiberacao = $rsLiberacao;
        }
    }

    public function dadosBancariosLiberacaoAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout'));

        $idPronac = $this->_request->getParam("idPronac");

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = $idPronac;

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

            $tblContaBancaria = new ContaBancaria();
            $rsContaBancaria = $tblContaBancaria->contaPorProjeto($idPronac);
            $this->view->dadosContaBancaria = $rsContaBancaria;

            $tbLiberacao = new Liberacao();
            $rsLiberacao = $tbLiberacao->liberacaoPorProjeto($idPronac);
            $this->view->dadosLiberacao = $rsLiberacao;
        }
    }

    public function dadosBancariosCaptacaoAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout'));


        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = $idPronac;

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

            //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
            if ($this->_request->getParam("qtde")) {
                $this->intTamPag = $this->_request->getParam("qtde");
            }
            $order = array();

            //==== parametro de ordenacao  ======//
            if ($this->_request->getParam("ordem")) {
                $ordem = $this->_request->getParam("ordem");
                if ($ordem == "ASC") {
                    $novaOrdem = "DESC";
                } else {
                    $novaOrdem = "ASC";
                }
            } else {
                $ordem = "ASC";
                $novaOrdem = "ASC";
            }

            //==== campo de ordenacao  ======//
            if ($this->_request->getParam("campo")) {
                $campo = $this->_request->getParam("campo");
                $order = array($campo." ".$ordem);
                $ordenacao = "&campo=".$campo."&ordem=".$ordem;
            } else {
                $campo = null;
                $order = array(8,4); //NomeProjeto, Dt.Recibo
                $ordenacao = null;
            }

            $pag = 1;
            $get = Zend_Registry::get('get');
            if (isset($get->pag)) {
                $pag = $get->pag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

            /* ================== PAGINACAO ======================*/
            $where = array();
            $where['p.idPronac = ?'] = $idPronac;

            if (isset($_GET['dtReciboInicio']) && !empty($_GET['dtReciboInicio']) && isset($_GET['dtReciboFim']) && empty($_GET['dtReciboFim'])) {
                $di = ConverteData($_GET['dtReciboInicio'], 13)." 00:00:00";
                $df = ConverteData($_GET['dtReciboInicio'], 13)." 23:59:59";
                $where["c.DtRecibo BETWEEN '$di' AND '$df'"] = '';
                $this->view->dtReciboInicio = $_GET['dtReciboInicio'];
                $this->view->dtReciboFim = $_GET['dtReciboFim'];
            }

            if (isset($_GET['dtReciboInicio']) && !empty($_GET['dtReciboInicio']) && isset($_GET['dtReciboFim']) && !empty($_GET['dtReciboFim'])) {
                $di = ConverteData($_GET['dtReciboInicio'], 13)." 00:00:00";
                $df = ConverteData($_GET['dtReciboFim'], 13)." 23:59:59";
                $where["c.DtRecibo BETWEEN '$di' AND '$df'"] = '';
                $this->view->dtReciboInicio = $_GET['dtReciboInicio'];
                $this->view->dtReciboFim = $_GET['dtReciboFim'];
            }

            $Captacao = new Captacao();
            $total = $Captacao->painelDadosBancariosCaptacao($where, $order, null, null, true);
            $fim = $inicio + $this->intTamPag;

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

            $busca = $Captacao->painelDadosBancariosCaptacao($where, $order, $tamanho, $inicio);

            $paginacao = array(
                    "pag"=>$pag,
                    "qtde"=>$this->intTamPag,
                    "campo"=>$campo,
                    "ordem"=>$ordem,
                    "ordenacao"=>$ordenacao,
                    "novaOrdem"=>$novaOrdem,
                    "total"=>$total,
                    "inicio"=>($inicio+1),
                    "fim"=>$fim,
                    "totalPag"=>$totalPag,
                    "Itenspag"=>$this->intTamPag,
                    "tamanho"=>$tamanho
             );

            $this->view->paginacao = $paginacao;
            $this->view->qtd       = $total;
            $this->view->dados     = $busca;
            $this->view->intTamPag = $this->intTamPag;
        }
    }

    public function imprimirDadosBancariosCaptacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->dadosBancariosCaptacaoAction();
    }

    public function dadosConvenioAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->buscar(array('idPronac = ?' => $idPronac))->current();
            $this->view->idPronac = $idPronac;
            $this->view->DadosProjeto = $DadosProjeto;

            $where = array();
            $where['AnoProjeto = ?'] = $DadosProjeto->AnoProjeto;
            $where['Sequencial = ?'] = $DadosProjeto->Sequencial;

            $Convenio = new Convenio();
            $this->view->dados = $Convenio->buscarDadosConvenios($where);
        }
    }

    public function imprimirDadosConvenioAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->buscar(array('idPronac = ?' => $idPronac))->current();
            $this->view->idPronac = $idPronac;
            $this->view->DadosProjeto = $DadosProjeto;

            $where = array();
            $where['AnoProjeto = ?'] = $DadosProjeto->AnoProjeto;
            $where['Sequencial = ?'] = $DadosProjeto->Sequencial;

            $Convenio = new Convenio();
            $this->view->dados = $Convenio->buscarDadosConvenios($where);
        }
    }

    public function historicoEncaminhamentoAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->idPronac = $idPronac;
            $this->view->DadosProjeto = $DadosProjeto;

            $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $this->view->dados = $tbDistribuirParecer->buscarHistoricoEncaminhamento(array('a.idPRONAC = ?'=>$idPronac));
        }
    }

    public function imprimirHistoricoEncaminhamentoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->idPronac = $idPronac;
            $this->view->DadosProjeto = $DadosProjeto;

            $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $this->view->dados = $tbDistribuirParecer->buscarHistoricoEncaminhamento(array('a.idPRONAC = ?'=>$idPronac));
        }
    }

    public function dadosRelacaoPagamentosAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;

            $tbComprovante = new tbComprovantePagamentoxPlanilhaAprovacao();
            $this->view->relacaoPagamentos = $tbComprovante->buscarRelacaoPagamentos($idPronac);
        }
    }

    public function pagamentosPorUfMunicipioAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;

            $tbComprovante = new tbComprovantePagamentoxPlanilhaAprovacao();
            $this->view->pagamentos = $tbComprovante->pagamentosPorUFMunicipio($idPronac);
        }
    }

    public function pagamentosConsolidadosPorUfMunicipioAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;

            $tbComprovante = new tbComprovantePagamentoxPlanilhaAprovacao();
            $this->view->pagamentos = $tbComprovante->pagamentosConsolidadosPorUfMunicipio($idPronac);
        }
    }

    public function carregarComprovantesComprovadosPorItemAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        $idPlanilhaAprovacao = $this->_request->getParam("idPlanilha");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {

            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;

            //Busca os dados do item
            $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
            $DadosItem = $tbPlanilhaAprovacao->buscar(array('idPlanilhaAprovacao = ?' => $idPlanilhaAprovacao));

            $tbComprovante = new tbComprovantePagamentoxPlanilhaAprovacao();
            if (count($DadosItem) > 0) {
                $DadosItem = $DadosItem[0];
                $resultado = $tbComprovante->buscarRelacaoPagamentos($idPronac, $idPlanilhaAprovacao);
                if ($DadosItem->tpPlanilha == 'RP') {
                    $resultado = $tbComprovante->buscarRelacaoPagamentos($idPronac, $DadosItem->idPlanilhaAprovacaoPai);
                }
            }
            $this->view->relacaoPagamentos = $resultado;
        }
    }

    public function execucaoReceitaDespesaAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;

            $tbComprovante = new tbComprovantePagamentoxPlanilhaAprovacao();
            $relExecRecDesp = $tbComprovante->buscarRelatorioExecucaoReceita($idPronac);
            $this->view->relExecRec = $relExecRecDesp;

            $relExecRecDesp = $tbComprovante->buscarRelatorioExecucaoDespesa($idPronac);
            $this->view->relExecDesp = $relExecRecDesp;
        }
    }

    public function relatorioFisicoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;

            $tbComprovante = new tbComprovantePagamentoxPlanilhaAprovacao();
            $relatorioFisico = $tbComprovante->buscarRelatorioFisico($idPronac);
            $this->view->relatorioFisico = $relatorioFisico;
        }
    }

    public function relatorioBensCapitalAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;

            $tbComprovante = new tbComprovantePagamentoxPlanilhaAprovacao();
            $relatorioBensDeCapital = $tbComprovante->buscarRelatorioBensDeCapital($idPronac);
            $this->view->relatorioBensDeCapital = $relatorioBensDeCapital;
        }
    }

    public function captacaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {
            $post   = Zend_Registry::get('post');
            $this->intTamPag = 10;

            $arrBusca = array();
            $pag = 1;
            //$get = Zend_Registry::get('get');
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim = $inicio + $this->intTamPag;

            $tblCaptacao = new Captacao();
            $rsCount = $tblCaptacao->buscaCompleta(array('idPronac = ?'=>$idPronac), array(), null, null, true);
            $total = $rsCount->total;
            $totalGeralCaptado = $rsCount->totalGeralCaptado;


            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            $ordem = array("10 ASC");
            //if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }

            $rsCaptacao = $tblCaptacao->buscaCompleta(array('idPronac = ?'=>$idPronac), $ordem, $tamanho, $inicio);

            $tProjeto = 0;
            $CgcCPfMecena = 0;
            $arrRegistros = array();
            foreach ($rsCaptacao as $captacao) {
                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['nome'] = $captacao->Nome;

                if ($CgcCPfMecena    !=  $captacao->CgcCPfMecena) {
                    $tIncentivador  =   0;
                    $qtRegistroI    =   0;
                    $CgcCPfMecena   =   $captacao->CgcCPfMecena;
                }

                $tIncentivador +=  $captacao->CaptacaoReal;
                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['totaIncentivador'] = number_format($tIncentivador, 2, ',', '.');
                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['TipoApoio']         =   $captacao->TipoApoio;
                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['NumeroRecibo']      =   $captacao->NumeroRecibo;
                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['DtTransferenciaRecurso']   =  !empty($captacao->DtTransferenciaRecurso) ? date('d/m/Y', strtotime($captacao->DtTransferenciaRecurso)) : '-';
                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['DtRecibo']          =   date('d/m/Y', strtotime($captacao->DtRecibo));
                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['CaptacaoReal']      =   number_format($captacao->CaptacaoReal, 2, ',', '.');
            }

            $arrRegistros['totalgeral'] = number_format($totalGeralCaptado, 2, ',', '.');

            $this->view->registros = $arrRegistros;
            $this->view->pag = $pag;
            $this->view->total = $total;
            $this->view->inicio = ($inicio+1);
            $this->view->fim = $fim;
            $this->view->totalPag = $totalPag;
            $this->view->parametrosBusca = $_POST;
        }
    }

    public function relatoriosTrimestraisAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idpronac = $this->_request->getParam("idPronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        if (!empty($idpronac)) {
            $Projetos = new Projetos();
            $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();
            $anoProjeto = $dadosProj->AnoProjeto;
            $sequencial = $dadosProj->Sequencial;

            $fnDtInicioRelatorioTrimestral = new fnDtInicioRelatorioTrimestral();
            $DtLiberacao = $fnDtInicioRelatorioTrimestral->dtInicioRelatorioTrimestral($idpronac);
            $intervalo = round(Data::CompararDatas($DtLiberacao->dtLiberacao, $dadosProj->DtFimExecucao));
            $this->view->inicioPeriodo = $DtLiberacao->dtLiberacao;

            $qtdRelatorioEsperado = ceil($intervalo/90);
            $this->view->qtdRelatorioEsperado = $qtdRelatorioEsperado;

            $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
            $qtdRelatorioCadastrados = $tbComprovanteTrimestral->buscarComprovantes(array('idPronac=?'=>$idpronac), true, array('nrComprovanteTrimestral')); //busca todos os relatorios
            $qtdRelCadastrados = !empty($qtdRelatorioCadastrados) ? $qtdRelatorioCadastrados->count() : 0;
            $this->view->qtdRelatorioCadastrados = $qtdRelCadastrados;
            $this->view->RelatorioCadastrados = $qtdRelatorioCadastrados;

            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idpronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;
        }
    }

    public function visualizarRelatorioAction()
    {
        $idpronac = $this->_request->getParam("idPronac");
        $nrrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $DadosRelatorio = $tbComprovanteTrimestral->buscarComprovantes(array('IdPRONAC = ?' => $idpronac, 'nrComprovanteTrimestral=?'=>$nrrelatorio));
        $this->view->DadosRelatorio = $DadosRelatorio;

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idpronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idpronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idpronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;

        $DadosCompMetas = $projetos->buscarMetasComprovadas($idpronac);
        $this->view->DadosCompMetas = $DadosCompMetas;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idpronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idpronac);
        $this->view->DadosComprovantes = $dadosComprovantes;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    public function imprimirRelatorioTrimestralAction()
    {
        $idpronac = $this->_request->getParam("idPronac"); //idPronac
        $nrrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $DadosRelatorio = $tbComprovanteTrimestral->buscarComprovantes(array('IdPRONAC = ?' => $idpronac, 'nrComprovanteTrimestral=?'=>$nrrelatorio));
        $this->view->DadosRelatorio = $DadosRelatorio;

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idpronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idpronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idpronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;

        $DadosCompMetas = $projetos->buscarMetasComprovadas($idpronac);
        $this->view->DadosCompMetas = $DadosCompMetas;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idpronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idpronac);
        $this->view->DadosComprovantes = $dadosComprovantes;
        $this->_helper->layout->disableLayout();// Desabilita o Zend Layout
    }


    public function relatorioFinalAction()
    {
        $idpronac = $this->_request->getParam("idPronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;
        $this->view->idPronac = $idpronac;

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $DadosRelatorio = $tbCumprimentoObjeto->buscarCumprimentoObjeto(array('idPronac = ?' => $idpronac));

        if (!empty($DadosRelatorio)) {
            $this->view->DadosRelatorio = $DadosRelatorio;

            $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac);
            $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

            $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idpronac);
            $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

            $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
            $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idpronac);
            $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

            $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
            $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idpronac);
            $this->view->PlanosCadastrados = $PlanosCadastrados;

            $DadosCompMetas = $projetos->buscarMetasComprovadas($idpronac);
            $this->view->DadosCompMetas = $DadosCompMetas;

            $DadosItensOrcam = $projetos->buscarItensComprovados($idpronac);
            $this->view->DadosItensOrcam = $DadosItensOrcam;

            $Arquivo = new Arquivo();
            $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idpronac);
            $this->view->DadosComprovantes = $dadosComprovantes;

            $tbTermoAceiteObra = new ComprovacaoObjeto_Model_DbTable_TbTermoAceiteObra();
            $AceiteObras = $tbTermoAceiteObra->buscarTermoAceiteObraArquivos(array('idPronac=?'=>$idpronac));
            $this->view->AceiteObras = $AceiteObras;

            $tbBensDoados = new ComprovacaoObjeto_Model_DbTable_TbBensDoados();
            $BensCadastrados = $tbBensDoados->buscarBensCadastrados(array('a.idPronac=?'=>$idpronac), array('b.Descricao'));
            $this->view->BensCadastrados = $BensCadastrados;

            if ($DadosRelatorio->siCumprimentoObjeto == 6) {
                $Usuario = new UsuarioDAO();
                $nmUsuarioCadastrador = $Usuario->buscarUsuario($DadosRelatorio->idTecnicoAvaliador);
                $nmChefiaImediata = $Usuario->buscarUsuario($DadosRelatorio->idChefiaImediata);
                $this->view->TecnicoAvaliador = $nmUsuarioCadastrador;
                $this->view->ChefiaImediata = $nmChefiaImediata;
            }
        }
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    public function imprimirRelatorioFinalAction()
    {
        $idpronac = $this->_request->getParam("idPronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $DadosRelatorio = $tbCumprimentoObjeto->buscarCumprimentoObjeto(array('idPronac = ?' => $idpronac));
        $this->view->DadosRelatorio = $DadosRelatorio;

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idpronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idpronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();

        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idpronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;

        $DadosCompMetas = $projetos->buscarMetasComprovadas($idpronac);
        $this->view->DadosCompMetas = $DadosCompMetas;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idpronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idpronac);
        $this->view->DadosComprovantes = $dadosComprovantes;

        $tbTermoAceiteObra = new ComprovacaoObjeto_Model_DbTable_TbTermoAceiteObra();
        $AceiteObras = $tbTermoAceiteObra->buscarTermoAceiteObraArquivos(array('idPronac=?'=>$idpronac));
        $this->view->AceiteObras = $AceiteObras;

        $tbBensDoados = new ComprovacaoObjeto_Model_DbTable_TbBensDoados();
        $BensCadastrados = $tbBensDoados->buscarBensCadastrados(array('a.idPronac=?'=>$idpronac), array('b.Descricao'));
        $this->view->BensCadastrados = $BensCadastrados;

        if ($DadosRelatorio->siCumprimentoObjeto == 6) {
            $Usuario = new UsuarioDAO();
            $nmUsuarioCadastrador = $Usuario->buscarUsuario($DadosRelatorio->idTecnicoAvaliador);
            $nmChefiaImediata = $Usuario->buscarUsuario($DadosRelatorio->idChefiaImediata);
            $this->view->TecnicoAvaliador = $nmUsuarioCadastrador;
            $this->view->ChefiaImediata = $nmChefiaImediata;
        }
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    public function remanejamentoMenorAction()
    {
        //REMANEJAMENTO MENOR OU IGUAL A 20%
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
        $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, 5);
        $planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, 5);
        $this->view->planilha = $planilha;
        $this->view->tipoPlanilha = 5;
    }

    public function remanejamentoMenorFinalizarAction()
    {
        //REMANEJAMENTO MENOR OU IGUAL A 20%
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

        //ARRAY PARA BUSCAR VALOR TOTAL DA PLANILHA ATIVA
        $where = array();
        $where['a.IdPRONAC = ?'] = $idPronac;
        $where['a.stAtivo = ?'] = 'S';

        //PLANILHA ATIVA - GRUPO A
        $where['a.idEtapa in (?)'] = array(1,2);
        $PlanilhaAtivaGrupoA = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

        //PLANILHA ATIVA - GRUPO B
        $where['a.idEtapa in (?)'] = array(3);
        $PlanilhaAtivaGrupoB = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

        //PLANILHA ATIVA - GRUPO C
        $where['a.idEtapa in (?)'] = array(4);
        $PlanilhaAtivaGrupoC = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

        //PLANILHA ATIVA - GRUPO D
        $where['a.idEtapa in (?)'] = array(5);
        $PlanilhaAtivaGrupoD = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();


        //ARRAY PARA BUSCAR VALOR TOTAL DA PLANILHA REMANEJADA
        $where = array();
        $where['a.IdPRONAC = ?'] = $idPronac;
        $where['a.tpPlanilha = ?'] = 'RP';
        $where['a.stAtivo = ?'] = 'N';

        //PLANILHA ATIVA - GRUPO A
        $where['a.idEtapa in (?)'] = array(1,2);
        $PlanilhaRemanejadaGrupoA = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

        //PLANILHA ATIVA - GRUPO B
        $where['a.idEtapa in (?)'] = array(3);
        $PlanilhaRemanejadaGrupoB = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

        //PLANILHA ATIVA - GRUPO C
        $where['a.idEtapa in (?)'] = array(4);
        $PlanilhaRemanejadaGrupoC = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

        //PLANILHA ATIVA - GRUPO D
        $where['a.idEtapa in (?)'] = array(5);
        $PlanilhaRemanejadaGrupoD = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

        //Os grupos est�o relacionados na tabela SAC.dbo.tbPlanilhaEtapa
        $valorTotalGrupoA = 0;
        $valorTotalGrupoB = 0;
        $valorTotalGrupoC = 0;
        $valorTotalGrupoD = 0;

        $valorTotalGrupoA = $PlanilhaAtivaGrupoA->Total-$PlanilhaRemanejadaGrupoA->Total;
        $valorTotalGrupoB = $PlanilhaAtivaGrupoB->Total-$PlanilhaRemanejadaGrupoB->Total;
        $valorTotalGrupoC = $PlanilhaAtivaGrupoC->Total-$PlanilhaRemanejadaGrupoC->Total;
        $valorTotalGrupoD = $PlanilhaAtivaGrupoD->Total-$PlanilhaRemanejadaGrupoD->Total;

        $erros = 0;
        if ($PlanilhaAtivaGrupoA->Total != $PlanilhaRemanejadaGrupoA->Total) {
            $erros++;
        }

        if ($PlanilhaAtivaGrupoB->Total != $PlanilhaRemanejadaGrupoB->Total) {
            $erros++;
        }

        if ($PlanilhaAtivaGrupoC->Total != $PlanilhaRemanejadaGrupoC->Total) {
            $erros++;
        }

        if ($PlanilhaAtivaGrupoD->Total != $PlanilhaRemanejadaGrupoD->Total) {
            $erros++;
        }

        $id = Seguranca::encrypt($idPronac);
        if ($erros > 0) {
            parent::message("<b>A T E N � � O !!!</b> Somente poder� finalizar a opera��o de remanejamento se os valores dos grupos A, B, C e D forem iguais a R$0,00 (zero real)!", "verprojetos/remanejamento-menor?idPronac=$id", "ERROR");
        } else {
            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $tblAgente = new Agente_Model_DbTable_Agentes();
            $rsAgente = $tblAgente->buscar(array('CNPJCPF=?'=>$auth->getIdentity()->Cpf))->current();

            $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $dadosReadequacao = array();
            $dadosReadequacao['idPronac'] = $idPronac;
            $dadosReadequacao['idTipoReadequacao'] = 1;
            $dadosReadequacao['dtSolicitacao'] = new Zend_Db_Expr('GETDATE()');
            $dadosReadequacao['idSolicitante'] = $rsAgente->idAgente;
            $dadosReadequacao['dsJustificativa'] = 'Readequa��o at� 20%';
            $dadosReadequacao['stAtendimento'] = 'D';
            $dadosReadequacao['siEncaminhamento'] = 11;
            $dadosReadequacao['stEstado'] = 0;
            $idReadequacao = $Readequacao_Model_DbTable_TbReadequacao->inserir($dadosReadequacao);

            /*if($idReadequacao > 0){
                $tbReadequacaoXtbTipoReadequacao = new tbReadequacaoXtbTipoReadequacao();
                $dadosReadequacaoTipo = array();
                $dadosReadequacaoTipo['idReadequacao'] = $idReadequacao;
                $dadosReadequacaoTipo['idTipoReadequacao'] = 1;
                $dadosReadequacaoTipo['dtSolicitacao'] = new Zend_Db_Expr('GETDATE()');
                $dadosReadequacaoTipo['idSolicitante'] = $rsAgente->idAgente;
                $dadosReadequacaoTipo['dsSolicitacao'] = 'Readequa��o at� 20%';
                $idReadequacaoTipo = $tbReadequacaoXtbTipoReadequacao->inserir($dadosReadequacaoTipo);
            } else {
                parent::message("Ocorreu um erro durante o cadastro do remanejamento!", "consultardadosprojeto?idPronac=$id", "ERROR");
            }*/

            if ($idReadequacao > 0) {
                $d = array('stAtivo' => 'S');
                $w = array('IdPRONAC = ?' => $idPronac, 'tpPlanilha = ?' => 'RP', 'stAtivo = ?' => 'N');
                $update = $tbPlanilhaAprovacao->update($d, $w);

                $d2 = array('stAtivo' => 'N');
                $w2 = array('IdPRONAC = ?' => $idPronac, 'tpPlanilha != ?' => 'RP', 'stAtivo = ?' => 'S');
                $tbPlanilhaAprovacao->update($d2, $w2);
                parent::message("O remanejamento foi finalizado com sucesso!", "verprojetos?idPronac=$id", "CONFIRM");
            } else {
                parent::message("Ocorreu um erro durante o cadastro do remanejamento!", "verprojetos?idPronac=$id", "ERROR");
            }
        }
    }

    public function carregarValorPorGrupoRemanejamentoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        try {
            $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

            //ARRAY PARA BUSCAR VALOR TOTAL DA PLANILHA ATIVA
            $where = array();
            $where['a.IdPRONAC = ?'] = $idPronac;
            $where['a.stAtivo = ?'] = 'S';

            //PLANILHA ATIVA - GRUPO A
            $where['a.idEtapa in (?)'] = array(1,2);
            $PlanilhaAtivaGrupoA = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //PLANILHA ATIVA - GRUPO B
            $where['a.idEtapa in (?)'] = array(3);
            $PlanilhaAtivaGrupoB = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //PLANILHA ATIVA - GRUPO C
            $where['a.idEtapa in (?)'] = array(4);
            $PlanilhaAtivaGrupoC = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //PLANILHA ATIVA - GRUPO D
            $where['a.idEtapa in (?)'] = array(5);
            $PlanilhaAtivaGrupoD = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();


            //ARRAY PARA BUSCAR VALOR TOTAL DA PLANILHA REMANEJADA
            $where = array();
            $where['a.IdPRONAC = ?'] = $idPronac;
            $where['a.tpPlanilha = ?'] = 'RP';
            $where['a.stAtivo = ?'] = 'N';
            $PlanilhaRemanejada = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //PLANILHA ATIVA - GRUPO A
            $where['a.idEtapa in (?)'] = array(1,2);
            $PlanilhaRemanejadaGrupoA = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //PLANILHA ATIVA - GRUPO B
            $where['a.idEtapa in (?)'] = array(3);
            $PlanilhaRemanejadaGrupoB = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //PLANILHA ATIVA - GRUPO C
            $where['a.idEtapa in (?)'] = array(4);
            $PlanilhaRemanejadaGrupoC = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //PLANILHA ATIVA - GRUPO D
            $where['a.idEtapa in (?)'] = array(5);
            $PlanilhaRemanejadaGrupoD = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //Os grupos est�o relacionados na tabela SAC.dbo.tbPlanilhaEtapa
            $valorTotalGrupoA = 0;
            $valorTotalGrupoB = 0;
            $valorTotalGrupoC = 0;
            $valorTotalGrupoD = 0;

            $valorTotalGrupoA = $PlanilhaAtivaGrupoA->Total-$PlanilhaRemanejadaGrupoA->Total;
            $valorTotalGrupoB = $PlanilhaAtivaGrupoB->Total-$PlanilhaRemanejadaGrupoB->Total;
            $valorTotalGrupoC = $PlanilhaAtivaGrupoC->Total-$PlanilhaRemanejadaGrupoC->Total;
            $valorTotalGrupoD = $PlanilhaAtivaGrupoD->Total-$PlanilhaRemanejadaGrupoD->Total;

            $dadosPlanilha = array();
            if ($PlanilhaAtivaGrupoA->Total == $PlanilhaRemanejadaGrupoA->Total) {
                $dadosPlanilha['GrupoA'] = utf8_encode('<span class="black bold">R$ '.number_format($valorTotalGrupoA, 2, ',', '.')).'</span>';
            } elseif ($PlanilhaAtivaGrupoA->Total < $PlanilhaRemanejadaGrupoA->Total) {
                $dadosPlanilha['GrupoA'] = utf8_encode('<span class="red bold">R$ '.number_format($valorTotalGrupoA, 2, ',', '.')).'</span>';
            } else {
                $dadosPlanilha['GrupoA'] = utf8_encode('<span class="blue bold">R$ '.number_format($valorTotalGrupoA, 2, ',', '.')).'</span>';
            }

            if ($PlanilhaAtivaGrupoB->Total == $PlanilhaRemanejadaGrupoB->Total) {
                $dadosPlanilha['GrupoB'] = utf8_encode('<span class="black bold">R$ '.number_format($valorTotalGrupoB, 2, ',', '.')).'</span>';
            } elseif ($PlanilhaAtivaGrupoB->Total < $PlanilhaRemanejadaGrupoB->Total) {
                $dadosPlanilha['GrupoB'] = utf8_encode('<span class="red bold">R$ '.number_format($valorTotalGrupoB, 2, ',', '.')).'</span>';
            } else {
                $dadosPlanilha['GrupoB'] = utf8_encode('<span class="blue bold">R$ '.number_format($valorTotalGrupoB, 2, ',', '.')).'</span>';
            }

            if ($PlanilhaAtivaGrupoC->Total == $PlanilhaRemanejadaGrupoC->Total) {
                $dadosPlanilha['GrupoC'] = utf8_encode('<span class="black bold">R$ '.number_format($valorTotalGrupoC, 2, ',', '.')).'</span>';
            } elseif ($PlanilhaAtivaGrupoC->Total < $PlanilhaRemanejadaGrupoC->Total) {
                $dadosPlanilha['GrupoC'] = utf8_encode('<span class="red bold">R$ '.number_format($valorTotalGrupoC, 2, ',', '.')).'</span>';
            } else {
                $dadosPlanilha['GrupoC'] = utf8_encode('<span class="blue bold">R$ '.number_format($valorTotalGrupoC, 2, ',', '.')).'</span>';
            }

            if ($PlanilhaAtivaGrupoD->Total == $PlanilhaRemanejadaGrupoD->Total) {
                $dadosPlanilha['GrupoD'] = utf8_encode('<span class="black bold">R$ '.number_format($valorTotalGrupoD, 2, ',', '.')).'</span>';
            } elseif ($PlanilhaAtivaGrupoD->Total < $PlanilhaRemanejadaGrupoD->Total) {
                $dadosPlanilha['GrupoD'] = utf8_encode('<span class="red bold">R$ '.number_format($valorTotalGrupoD, 2, ',', '.')).'</span>';
            } else {
                $dadosPlanilha['GrupoD'] = utf8_encode('<span class="blue bold">R$ '.number_format($valorTotalGrupoD, 2, ',', '.')).'</span>';
            }

            if (empty($PlanilhaRemanejada->Total) || $PlanilhaRemanejada->Total == 0) {
                $dadosPlanilha['GrupoA'] = utf8_encode('<span class="black bold">R$ '.number_format(0, 2, ',', '.')).'</span>';
                $dadosPlanilha['GrupoB'] = utf8_encode('<span class="black bold">R$ '.number_format(0, 2, ',', '.')).'</span>';
                $dadosPlanilha['GrupoC'] = utf8_encode('<span class="black bold">R$ '.number_format(0, 2, ',', '.')).'</span>';
                $dadosPlanilha['GrupoD'] = utf8_encode('<span class="black bold">R$ '.number_format(0, 2, ',', '.')).'</span>';
            }
            $this->_helper->json(array('resposta'=>true, 'dadosPlanilha'=>$dadosPlanilha));
        } catch (Zend_Exception $e) {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function carregarValorEntrePlanilhasAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $get = Zend_Registry::get('get');
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

        //BUSCAR VALOR TOTAL DA PLANILHA ATIVA
        $where = array();
        $where['a.IdPRONAC = ?'] = $idPronac;
        $where['a.stAtivo = ?'] = 'S';
        $PlanilhaAtiva = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();
        //x($PlanilhaAtiva->Total);

        //BUSCAR VALOR TOTAL DA PLANILHA DE REMANEJADA
        $where = array();
        $where['a.IdPRONAC = ?'] = $idPronac;
        $where['a.tpPlanilha = ?'] = 'RP';
        $where['a.stAtivo = ?'] = 'N';
        $where['a.tpAcao != ?'] = 'E';
        $PlanilhaRemanejada = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

        if ($PlanilhaRemanejada->Total > 0) {
            if ($PlanilhaAtiva->Total == $PlanilhaRemanejada->Total) {
                $statusPlanilha = 'neutro';
            } elseif ($PlanilhaAtiva->Total > $PlanilhaRemanejada->Total) {
                $statusPlanilha = 'positivo';
            } else {
                $statusPlanilha = 'negativo';
            }
        } else {
            $PlanilhaAtiva->Total = 0;
            $PlanilhaRemanejada->Total = 0;
            $statusPlanilha = 'neutro';
        }

        $this->montaTela(
            'verprojetos/carregar-valor-entre-planilhas.phtml',
            array(
            'statusPlanilha' => $statusPlanilha,
            'vlDiferencaPlanilhas' => 'R$ '.number_format(($PlanilhaAtiva->Total-$PlanilhaRemanejada->Total), 2, ',', '.')
            )
        );
    }

    public function remanejamentoReintegrarItemAction()
    {
        $this->_helper->layout->disableLayout();
        $idPlanilhaAprovacao = $this->_request->getParam("idPlanilha");
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsAgente = $tblAgente->buscar(array('CNPJCPF = ?'=>$auth->getIdentity()->Cpf));
        if ($rsAgente->count() > 0) {
            $idAgente = $rsAgente[0]->idAgente;
        }

        /* DADOS DO ITEM ATIVO */
        $where = array();
        $where['idPlanilhaAprovacao = ?'] = $idPlanilhaAprovacao;
        $where['stAtivo = ?'] = 'S';
        $planilhaAtiva = $tbPlanilhaAprovacao->buscar($where)->current();

        try {
            /* DADOS DO ITEM PARA EDICAO DO REMANEJAMENTO */
            $where = array();
            $where['idPlanilhaAprovacaoPai = ?'] = $idPlanilhaAprovacao;
            $where['tpPlanilha = ?'] = 'RP';
            $where['stAtivo = ?'] = 'N';
            $item = $tbPlanilhaAprovacao->buscar($where)->current();
            $item->qtItem = $planilhaAtiva->qtItem;
            $item->nrOcorrencia = $planilhaAtiva->nrOcorrencia;
            $item->vlUnitario = $planilhaAtiva->vlUnitario;
            $item->dsJustificativa = null;
            $item->idAgente = $idAgente;

            $dadosPlanilhaEditavel = array();
            $dadosPlanilhaEditavel['Quantidade'] = $planilhaAtiva->qtItem;
            $dadosPlanilhaEditavel['Ocorrencia'] = $planilhaAtiva->nrOcorrencia;
            $dadosPlanilhaEditavel['ValorUnitario'] = utf8_encode('R$ '.number_format($planilhaAtiva->vlUnitario, 2, ',', '.'));
            $dadosPlanilhaEditavel['TotalSolicitado'] = utf8_encode('R$ '.number_format(($planilhaAtiva->qtItem*$planilhaAtiva->nrOcorrencia*$planilhaAtiva->vlUnitario), 2, ',', '.'));
            $dadosPlanilhaEditavel['Justificativa'] = '';

            $x = $item->save();
            $this->_helper->json(array('resposta'=>true, 'dadosPlanilhaEditavel'=>$dadosPlanilhaEditavel));
        } catch (Zend_Exception $e) {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function remanejamentoReintegrarPlanilhaAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->_request->getParam("id");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

        try {
            $del = $tbPlanilhaAprovacao->delete(array('IdPRONAC = ?'=>$idPronac, 'tpPlanilha = ?'=>'RP', 'stAtivo = ?'=>'N'));
            if ($del > 0) {
                $planilhaAtiva = $tbPlanilhaAprovacao->buscar(array('IdPRONAC=?'=>$idPronac, 'StAtivo=?'=>'S'));
                $planilhaRP = array();
                foreach ($planilhaAtiva as $value) {
                    $planilhaRP['tpPlanilha'] = 'RP';
                    $planilhaRP['dtPlanilha'] = new Zend_Db_Expr('GETDATE()');
                    $planilhaRP['idPlanilhaProjeto'] = $value['idPlanilhaProjeto'];
                    $planilhaRP['idPlanilhaProposta'] = $value['idPlanilhaProposta'];
                    $planilhaRP['IdPRONAC'] = $value['IdPRONAC'];
                    $planilhaRP['idProduto'] = $value['idProduto'];
                    $planilhaRP['idEtapa'] = $value['idEtapa'];
                    $planilhaRP['idPlanilhaItem'] = $value['idPlanilhaItem'];
                    $planilhaRP['dsItem'] = $value['dsItem'];
                    $planilhaRP['idUnidade'] = $value['idUnidade'];
                    $planilhaRP['qtItem'] = $value['qtItem'];
                    $planilhaRP['nrOcorrencia'] = $value['nrOcorrencia'];
                    $planilhaRP['vlUnitario'] = $value['vlUnitario'];
                    $planilhaRP['qtDias'] = $value['qtDias'];
                    $planilhaRP['tpDespesa'] = $value['tpDespesa'];
                    $planilhaRP['tpPessoa'] = $value['tpPessoa'];
                    $planilhaRP['nrContraPartida'] = $value['nrContraPartida'];
                    $planilhaRP['nrFonteRecurso'] = $value['nrFonteRecurso'];
                    $planilhaRP['idUFDespesa'] = $value['idUFDespesa'];
                    $planilhaRP['idMunicipioDespesa'] = $value['idMunicipioDespesa'];
                    $planilhaRP['dsJustificativa'] = null;
                    $planilhaRP['idAgente'] = 0;
                    $planilhaRP['idPlanilhaAprovacaoPai'] = $value['idPlanilhaAprovacao'];
                    $planilhaRP['idReadequacao'] = $value['idReadequacao'];
                    $planilhaRP['tpAcao'] = $value['tpAcao'];
                    $planilhaRP['idRecursoDecisao'] = $value['idRecursoDecisao'];
                    $planilhaRP['stAtivo'] = 'N';
                    $tbPlanilhaAprovacao->inserir($planilhaRP);
                }
                $this->_helper->json(array('resposta'=>true));
            } else {
                $msg = utf8_encode('A planilha j� foi reintegrada.');
                $this->_helper->json(array('resposta'=>false, 'msg'=>$msg));
            }
        } catch (Zend_Exception $e) {
            $this->_helper->json(array('resposta'=>false, 'msg'=>'Ocorreu um erro durante o processo.'));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function remanejamentoAlterarItemAction()
    {
        $this->_helper->layout->disableLayout();
        $idPlanilhaAprovacao = $this->_request->getParam("idPlanilha");
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

        /* DADOS DO ITEM ATIVO */
        $where = array();
        $where['idPlanilhaAprovacao = ? or idPlanilhaAprovacaoPai = ?'] = $idPlanilhaAprovacao;
        $where['stAtivo = ?'] = 'S';
        $planilhaAtiva = $tbPlanilhaAprovacao->buscarDadosAvaliacaoDeItemRemanejamento($where);

        /* DADOS DO ITEM PARA EDICAO DO REMANEJAMENTO */
        $where = array();
        $where['idPlanilhaAprovacaoPai = ?'] = $idPlanilhaAprovacao;
        $where['tpPlanilha = ?'] = 'RP';
        $where['stAtivo = ?'] = 'N';
        $planilhaEditaval = $tbPlanilhaAprovacao->buscarDadosAvaliacaoDeItemRemanejamento($where);

        $dadosPlanilhaAtiva = array();
        $dadosPlanilhaEditavel = array();
        if (count($planilhaAtiva) > 0) {
            /* PROJETO */
            $Projetos = new Projetos();
            $projeto = $Projetos->buscar(array('IdPRONAC = ?' => $planilhaAtiva[0]->idPRONAC))->current();
            $dadosProjeto = array(
                'IdPRONAC' => $projeto->IdPRONAC,
                'PRONAC' => $projeto->AnoProjeto.$projeto->Sequencial,
                'NomeProjeto' => utf8_encode($projeto->NomeProjeto)
            );

            foreach ($planilhaAtiva as $registro) {
                //CALCULAR VALORES MINIMO E MAXIMO PARA VALIDACAO
                $vlAtual = @number_format(($registro['Quantidade']*$registro['Ocorrencia']*$registro['ValorUnitario']), 2, '', '');
                $vlAtualPerc = $vlAtual*20/100;

                //VALOR M�NIMO E M�XIMO DO ITEM ORIGINAL
                $vlAtualMin = $vlAtual-$vlAtualPerc;
                $vlAtualMax = $vlAtual+$vlAtualPerc;

                $dadosPlanilhaAtiva['idPlanilhaAprovacao'] = $registro['idPlanilhaAprovacao'];
                $dadosPlanilhaAtiva['idProduto'] = $registro['idProduto'];
                $dadosPlanilhaAtiva['descProduto'] = utf8_encode($registro['descProduto']);
                $dadosPlanilhaAtiva['idEtapa'] = $registro['idEtapa'];
                $dadosPlanilhaAtiva['descEtapa'] = utf8_encode($registro['descEtapa']);
                $dadosPlanilhaAtiva['idPlanilhaItem'] = $registro['idPlanilhaItem'];
                $dadosPlanilhaAtiva['descItem'] = utf8_encode($registro['descItem']);
                $dadosPlanilhaAtiva['idUnidade'] = $registro['idUnidade'];
                $dadosPlanilhaAtiva['descUnidade'] = utf8_encode($registro['descUnidade']);
                $dadosPlanilhaAtiva['Quantidade'] = $registro['Quantidade'];
                $dadosPlanilhaAtiva['Ocorrencia'] = $registro['Ocorrencia'];
                $dadosPlanilhaAtiva['ValorUnitario'] = utf8_encode('R$ '.number_format($registro['ValorUnitario'], 2, ',', '.'));
                $dadosPlanilhaAtiva['QtdeDias'] = $registro['QtdeDias'];
                $dadosPlanilhaAtiva['TotalSolicitado'] = utf8_encode('R$ '.number_format(($registro['Quantidade']*$registro['Ocorrencia']*$registro['ValorUnitario']), 2, ',', '.'));
                $dadosPlanilhaAtiva['ValorMinimoProItem'] = utf8_encode('R$ '.number_format(($registro['Quantidade']*$registro['Ocorrencia']*$registro['ValorUnitario'] - (($registro['Quantidade']*$registro['Ocorrencia']*$registro['ValorUnitario']) * 20/100)), 2, ',', '.'));
                $dadosPlanilhaAtiva['ValorMaximoProItem'] = utf8_encode('R$ '.number_format(($registro['Quantidade']*$registro['Ocorrencia']*$registro['ValorUnitario'] + (($registro['Quantidade']*$registro['Ocorrencia']*$registro['ValorUnitario']) * 20/100)), 2, ',', '.'));
                $dadosPlanilhaAtiva['vlMinimoValidacao'] = utf8_encode($vlAtualMin);
                $dadosPlanilhaAtiva['vlMaximoValidacao'] = utf8_encode($vlAtualMax);
                $dadosPlanilhaAtiva['ValorMinimoProItemValidacao'] = utf8_encode(number_format(($vlAtualMin), 2, '', ''));
                $dadosPlanilhaAtiva['ValorMaximoProItemValidacao'] = utf8_encode(number_format(($vlAtualMax), 2, '', ''));
                $dadosPlanilhaAtiva['Justificativa'] = utf8_encode($registro['Justificativa']);
            }

            if (count($planilhaEditaval) > 0) {
                foreach ($planilhaEditaval as $registroEditavel) {
                    $dadosPlanilhaEditavel['idPlanilhaAprovacao'] = $registroEditavel['idPlanilhaAprovacao'];
                    $dadosPlanilhaEditavel['idProduto'] = $registroEditavel['idProduto'];
                    $dadosPlanilhaEditavel['descProduto'] = utf8_encode($registroEditavel['descProduto']);
                    $dadosPlanilhaEditavel['idEtapa'] = $registroEditavel['idEtapa'];
                    $dadosPlanilhaEditavel['descEtapa'] = utf8_encode($registroEditavel['descEtapa']);
                    $dadosPlanilhaEditavel['idPlanilhaItem'] = $registroEditavel['idPlanilhaItem'];
                    $dadosPlanilhaEditavel['descItem'] = utf8_encode($registroEditavel['descItem']);
                    $dadosPlanilhaEditavel['idUnidade'] = $registroEditavel['idUnidade'];
                    $dadosPlanilhaEditavel['descUnidade'] = utf8_encode($registroEditavel['descUnidade']);
                    $dadosPlanilhaEditavel['Quantidade'] = $registroEditavel['Quantidade'];
                    $dadosPlanilhaEditavel['Ocorrencia'] = $registroEditavel['Ocorrencia'];
                    $dadosPlanilhaEditavel['ValorUnitario'] = utf8_encode('R$ '.number_format($registroEditavel['ValorUnitario'], 2, ',', '.'));
                    $dadosPlanilhaEditavel['QtdeDias'] = $registroEditavel['QtdeDias'];
                    $dadosPlanilhaEditavel['TotalSolicitado'] = utf8_encode('R$ '.number_format(($registroEditavel['Quantidade']*$registroEditavel['Ocorrencia']*$registroEditavel['ValorUnitario']), 2, ',', '.'));
                    $dadosPlanilhaEditavel['Justificativa'] = utf8_encode($registroEditavel['Justificativa']);
                    $dadosPlanilhaEditavel['idAgente'] = $registroEditavel['idAgente'];
                }
            } else {
                $dadosPlanilhaEditavel = $dadosPlanilhaAtiva;
            }

            $tbCompPagxPlanAprov = new tbComprovantePagamentoxPlanilhaAprovacao();
            $res = $tbCompPagxPlanAprov->buscarValorComprovadoDoItem($idPlanilhaAprovacao);
            $valoresDoItem = array(
                'vlComprovadoDoItem' => utf8_encode('R$ '.number_format($res->vlComprovado, 2, ',', '.')),
                'vlComprovadoDoItemValidacao' => utf8_encode(number_format($res->vlComprovado, 2, '', ''))
            );

            //$jsonEncode = json_encode($dadosPlanilha);
            $this->_helper->json(array('resposta'=>true, 'dadosPlanilhaAtiva'=>$dadosPlanilhaAtiva, 'dadosPlanilhaEditavel'=>$dadosPlanilhaEditavel, 'valoresDoItem'=>$valoresDoItem, 'dadosProjeto'=>$dadosProjeto));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function salvarAvaliacaoDoItemRemanejamentoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $auth = Zend_Auth::getInstance(); // pega a autentica��o

        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsAgente = $tblAgente->buscar(array('CNPJCPF = ?'=>$auth->getIdentity()->Cpf));
        if ($rsAgente->count() > 0) {
            $idAgente = $rsAgente[0]->idAgente;
        }

        $ValorUnitario = str_replace('.', '', $_POST['ValorUnitario']);
        $ValorUnitario = str_replace(',', '.', $ValorUnitario);
        $vlTotal = @number_format(($_POST['Quantidade']*$_POST['Ocorrencia']*$ValorUnitario), 2, '', '');

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $verificarPlanilhaRP = $tbPlanilhaAprovacao->buscar(array('IdPRONAC=?'=>$idPronac, 'tpPlanilha=?'=>'RP'));

        if (count($verificarPlanilhaRP)==0) {
            $planilhaAtiva = $tbPlanilhaAprovacao->buscar(array('IdPRONAC=?'=>$idPronac, 'StAtivo=?'=>'S'));
            $planilhaRP = array();
            foreach ($planilhaAtiva as $value) {
                $planilhaRP['tpPlanilha'] = 'RP';
                $planilhaRP['dtPlanilha'] = new Zend_Db_Expr('GETDATE()');
                $planilhaRP['idPlanilhaProjeto'] = $value['idPlanilhaProjeto'];
                $planilhaRP['idPlanilhaProposta'] = $value['idPlanilhaProposta'];
                $planilhaRP['IdPRONAC'] = $value['IdPRONAC'];
                $planilhaRP['idProduto'] = $value['idProduto'];
                $planilhaRP['idEtapa'] = $value['idEtapa'];
                $planilhaRP['idPlanilhaItem'] = $value['idPlanilhaItem'];
                $planilhaRP['dsItem'] = $value['dsItem'];
                $planilhaRP['idUnidade'] = $value['idUnidade'];
                $planilhaRP['qtItem'] = $value['qtItem'];
                $planilhaRP['nrOcorrencia'] = $value['nrOcorrencia'];
                $planilhaRP['vlUnitario'] = $value['vlUnitario'];
                $planilhaRP['qtDias'] = $value['qtDias'];
                $planilhaRP['tpDespesa'] = $value['tpDespesa'];
                $planilhaRP['tpPessoa'] = $value['tpPessoa'];
                $planilhaRP['nrContraPartida'] = $value['nrContraPartida'];
                $planilhaRP['nrFonteRecurso'] = $value['nrFonteRecurso'];
                $planilhaRP['idUFDespesa'] = $value['idUFDespesa'];
                $planilhaRP['idMunicipioDespesa'] = $value['idMunicipioDespesa'];
                $planilhaRP['dsJustificativa'] = null;
                $planilhaRP['idAgente'] = 0;
                $planilhaRP['idPlanilhaAprovacaoPai'] = $value['idPlanilhaAprovacao'];
                $planilhaRP['idReadequacao'] = $value['idReadequacao'];
                $planilhaRP['tpAcao'] = $value['tpAcao'];
                $planilhaRP['idRecursoDecisao'] = $value['idRecursoDecisao'];
                $planilhaRP['stAtivo'] = 'N';
                $tbPlanilhaAprovacao->inserir($planilhaRP);
            }
        }

        //BUSCA OS DADOS DO ITEM ORIGINAL PARA VALIDA��O DE VALORES
        $valoresItem = $tbPlanilhaAprovacao->buscar(array('IdPRONAC=?'=>$idPronac, 'StAtivo=?'=>'S', 'idPlanilhaAprovacao=?'=>$_POST['idPlanilha']))->current();
        $vlAtual = @number_format(($valoresItem['qtItem']*$valoresItem['nrOcorrencia']*$valoresItem['vlUnitario']), 2, '', '');
        $vlAtualPerc = $vlAtual*20/100;

        //VALOR M�NIMO E M�XIMO DO ITEM ORIGINAL
        $vlAtualMin = $vlAtual-$vlAtualPerc;
        $vlAtualMax = $vlAtual+$vlAtualPerc;

        //VERIFICA SE O VALOR TOTAL DOS DADOS INFORMADOR PELO PROPONENTE EST� ENTRE O M�NIMO E M�XIMO PERMITIDO - 20%
        if ($vlTotal < $vlAtualMin || $vlTotal > $vlAtualMax) {
            $this->_helper->json(array('resposta'=>false, 'msg'=>'O valor total do item desejado ultrapassou a margem de 20%.'));
            $this->_helper->viewRenderer->setNoRender(true);
        }

        $editarItem = $tbPlanilhaAprovacao->buscar(array('IdPRONAC=?'=>$idPronac, 'tpPlanilha=?'=>'RP', 'idPlanilhaAprovacaoPai=?'=>$_POST['idPlanilha']))->current();
        $editarItem->qtItem = $_POST['Quantidade'];
        $editarItem->nrOcorrencia = $_POST['Ocorrencia'];
        $editarItem->vlUnitario = $ValorUnitario;
        $editarItem->dsJustificativa = $_POST['Justificativa'];
        $editarItem->idAgente = $idAgente;
//        $editarItem->idAgente = $auth->getIdentity()->IdUsuario;
        $editarItem->save();

        $this->_helper->json(array('resposta'=>true, 'msg'=>'Dados salvos com sucesso!'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function prestacaoDeContasAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $this->view->parecerTecnico = array();
            $this->view->parecerChefe   = array();
            $this->view->parecerCoordenador = array();
            $this->view->dadosInabilitado   = array();
            $this->view->resultadoParecer   = null;
            $this->view->tipoInabilitacao   = null;

            $tblProjeto = new Projetos();
            $rsProjeto = $tblProjeto->buscar(array("IdPronac=?"=>$idPronac))->current();
            $this->view->projeto = $rsProjeto;
            $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;

            //resultado parecer
            if ($rsProjeto->Situacao == 'E19') {
                $this->view->resultadoParecer = 'Aprovado Integralmente';
            }
            if ($rsProjeto->Situacao == 'E22') {
                $this->view->resultadoParecer = 'Indeferido';
            }
            if ($rsProjeto->Situacao == 'L03') {
                $this->view->resultadoParecer = 'Aprovado com Ressalvas';
            }

            $tbRelatorioTecnico = new tbRelatorioTecnico();
            $rsParecerTecnico = $tbRelatorioTecnico->buscar(array('IdPRONAC=?'=>$idPronac,'cdGrupo=?'=>124))->current();
            $rsParecerChefe   = $tbRelatorioTecnico->buscar(array('IdPRONAC=?'=>$idPronac,'cdGrupo=?'=>132))->current();

            if (is_object($rsParecerTecnico) && is_object($rsParecerChefe)) {
                $this->view->parecerTecnico = $rsParecerTecnico;
                $this->view->parecerChefe   = $rsParecerChefe;
            }

            $rsParecerCoordenador = $tbRelatorioTecnico->buscar(array('IdPRONAC=?'=>$idPronac,'cdGrupo=?'=>126))->current();
            $this->view->parecerCoordenador   = $rsParecerCoordenador;

            $tblInabilitado = new Inabilitado();
            $rsInabilitado = $tblInabilitado->buscar(array('AnoProjeto+Sequencial=?'=>$pronac))->current();
            $this->view->dadosInabilitado = $rsInabilitado;

            if (is_object($rsInabilitado) && isset($rsInabilitado->idTipoInabilitado) && !empty($rsInabilitado->idTipoInabilitado)) {
                $tbTipoInabilitado =  new tbTipoInabilitado();
                $rsTipoInabilitado = $tbTipoInabilitado->buscar(array('idTipoInabilitado=?'=>$rsInabilitado->idTipoInabilitado))->current();
                if (is_object($rsTipoInabilitado)) {
                    $this->view->tipoInabilitacao = $rsTipoInabilitado->dsTipoInabilitado;
                }
            }
        }
    }

    public function analiseProjetoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
    }

    public function analiseParecerTecnicoConsolidadoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $tipoAnalise = $this->_request->getParam("tipoAnalise");
        if (!empty($idPronac)) {
            $this->view->resultAnaliseProjeto = array();
            $this->view->resultAnaliseProjetoCNIC = array();
            $this->view->resultAnaliseProjetoPlenaria = array();
            $this->view->fontesincentivo  = 0;
            $this->view->outrasfontes     = 0;
            $this->view->valorproposta    = 0;
            $this->view->valorparecerista = 0;
            $this->view->valorcomponente  = 0;
            $this->view->enquadramento = 'N&atilde;o Enquadrado';

            //INICIAL
            if ($tipoAnalise == "inicial") {
                $parecer = new Parecer_Model_DbTable_Parecer();
                $analiseparecer = $parecer->buscarParecer(array(1), $idPronac)->current();
                if (is_object($analiseparecer)) {
                    $this->view->resultAnaliseProjeto = $analiseparecer->toArray();
                }

                $projeto = new Projetos();
                $buscarPronac = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
                $idprojeto = !empty($buscarPronac['idProjeto']) ? $buscarPronac['idProjeto'] : 0;

                $this->view->resultAnaliseProduto = GerenciarPareceresDAO::projetosConsolidadosParte2($idPronac);

                $planilhaprojeto = new PlanilhaProjeto();
                $planilhaAprovacao = new PlanilhaAprovacao();
                $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                /*$parecerista = $planilhaprojeto->somarPlanilhaProjeto($idPronac);
                $this->view->valorparecerista = $parecerista['soma'];

                if(!empty($idprojeto)){
                    $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                    $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
                    $this->view->fontesincentivo  = $fonteincentivo['soma'];
                    $this->view->outrasfontes     = $outrasfontes['soma'];
                    $this->view->valorproposta    = $fonteincentivo['soma'] + $outrasfontes['soma'];
                }*/

                //TRATANDO SOMA DE PROJETO QUANDO ESTE FOR DE READEQUACAO
                $arrWhereSomaPlanilha = array();
                $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                if ($this->bln_readequacao == "false") {
//                    if(!empty($idprojeto)){
                    $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                    $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
                    $parecerista = $planilhaprojeto->somarPlanilhaProjeto($idPronac, 109);
//                    }
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

                $this->view->fontesincentivo  = $fonteincentivo['soma'];
                $this->view->outrasfontes     = $outrasfontes['soma'];
                $this->view->valorproposta    = $fonteincentivo['soma'] + $outrasfontes['soma'];
                $this->view->valorparecerista = $parecerista['soma'];

                $tbEnquadramento    = new Admissibilidade_Model_Enquadramento();
                $verificaEnquadramento = $tbEnquadramento->buscarDados($idPronac, null, false);

                if (is_object($verificaEnquadramento) && count($verificaEnquadramento) > 0) {
                    if ($verificaEnquadramento->Enquadramento == '2') {
                        $this->view->enquadramento = 'Artigo 18';
                    } elseif ($verificaEnquadramento->Enquadramento == '1') {
                        $this->view->enquadramento = 'Artigo 26';
                    } else {
                        $this->view->enquadramento = 'N�o Enquadrado';
                    }
                } else {
                    $this->view->enquadramento = 'N�o Enquadrado';
                }
            }
            //CNIC
            if ($tipoAnalise == "cnic") {
                $parecer = new Parecer_Model_DbTable_Parecer();
                $analiseparecer = $parecer->buscarParecer(array(6), $idPronac)->current();
                if (is_object($analiseparecer)) {
                    $this->view->resultAnaliseProjetoCNIC = $analiseparecer->toArray();
                }

                $projeto = new Projetos();
                $buscarPronac = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
                $idprojeto = !empty($buscarPronac['idProjeto']) ? $buscarPronac['idProjeto'] : 0;

                $tpPlanilha = 'CO';
                $analiseaprovacao = new AnaliseAprovacao();
                $produtos = $analiseaprovacao->buscarAnaliseProduto($tpPlanilha, $idPronac);
                $this->view->resultAnaliseProduto = $produtos;

                $planilhaAprovacao = new PlanilhaAprovacao();
                $rsPlanilhaAtual = $planilhaAprovacao->buscar(array('IdPRONAC = ?'=>$idPronac, 'stAtivo = ?'=>'S'), array('dtPlanilha DESC'))->current();
                $status = (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') ? 'N' : 'S';

                //TRATANDO SOMA DE PROJETO QUANDO ESTE FOR DE READEQUACAO
                $arrWhereSomaPlanilha = array();
                $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                if ($this->bln_readequacao == "false") {
//                    if(!empty($idprojeto)){
                    $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                    $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                    $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
//                    }
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
                    $valorparecerista = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWherePlanilhaPA);
                }

                $arrWhereSomaPlanilha = array();
                $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                $arrWhereSomaPlanilha['tpPlanilha = ? ']=$tpPlanilha;
                $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
                $arrWhereSomaPlanilha['stAtivo = ? ']=$status;
                $valorplanilha = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

                $this->view->fontesincentivo = $fonteincentivo['soma'];
                $this->view->outrasfontes    = $outrasfontes['soma'];
                $this->view->valorproposta   = $fonteincentivo['soma'] + $outrasfontes['soma'];
                $this->view->valorcomponente = $valorplanilha['soma'];

                $verificaEnquadramento = RealizarAnaliseProjetoDAO::verificaEnquadramento($idPronac, $tpPlanilha);

                if (count($verificaEnquadramento) > 0) {
                    if ($verificaEnquadramento[0]->stArtigo18 == true) {
                        $this->view->enquadramento = 'Artigo 18';
                    } elseif ($verificaEnquadramento[0]->stArtigo26 == true) {
                        $this->view->enquadramento = 'Artigo 26';
                    } else {
                        $this->view->enquadramento = 'N�o Enquadrado';
                    }
                } else {
                    $this->view->enquadramento = 'N�o Enquadrado';
                }
            }
            //PLENARIA
            if ($tipoAnalise == "plenaria") {
                $parecer = new Parecer_Model_DbTable_Parecer();
                $analiseparecer = $parecer->buscarParecer(array(10), $idPronac)->current();
                if (is_object($analiseparecer)) {
                    $this->view->resultAnaliseProjetoPlenaria = $analiseparecer->toArray();
                }

                $projeto = new Projetos();
                $buscarPronac = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
                $idprojeto = !empty($buscarPronac['idProjeto']) ? $buscarPronac['idProjeto'] : 0;

                $tpPlanilha = 'SE';
                $analiseaprovacao = new AnaliseAprovacao();
                $produtos = $analiseaprovacao->buscarAnaliseProduto($tpPlanilha, $idPronac);
                $this->view->resultAnaliseProduto = $produtos;

                //TRATANDO SOMA DE PROJETO QUANDO ESTE FOR DE READEQUACAO
                $planilhaAprovacao = new PlanilhaAprovacao();
                $arrWhereSomaPlanilha = array();
                $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                if ($this->bln_readequacao == "false") {
//                    if(!empty($idprojeto)){
                    $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                    $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                    $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
//                    }
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
                    $valorparecerista = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWherePlanilhaPA);
                }

                $arrWhereSomaPlanilha = array();
                $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                $arrWhereSomaPlanilha['tpPlanilha = ? ']=$tpPlanilha;
                $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
                $arrWhereSomaPlanilha['stAtivo = ? ']='S';
                $valorplanilha = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

                $this->view->fontesincentivo = $fonteincentivo['soma'];
                $this->view->outrasfontes    = $outrasfontes['soma'];
                $this->view->valorproposta   = $fonteincentivo['soma'] + $outrasfontes['soma'];
                $this->view->valorcomponente = $valorplanilha['soma'];

                $verificaEnquadramento = RealizarAnaliseProjetoDAO::verificaEnquadramento($idPronac, $tpPlanilha);

                if (count($verificaEnquadramento) > 0) {
                    if ($verificaEnquadramento[0]->stArtigo18 == true) {
                        $this->view->enquadramento = 'Artigo 18';
                    } elseif ($verificaEnquadramento[0]->stArtigo26 == true) {
                        $this->view->enquadramento = 'Artigo 26';
                    } else {
                        $this->view->enquadramento = 'N�o Enquadrado';
                    }
                } else {
                    $this->view->enquadramento = 'N�o Enquadrado';
                }
            }
        }
    }

    public function analiseDeConteudoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $tipoAnalise = $this->_request->getParam("tipoAnalise");

        $this->view->dadosAnaliseInicial  = array();
        $this->view->dadosAnaliseCnic     = array();
        $this->view->dadosAnalisePlenaria = array();

        if (!empty($idPronac)) {
            //INICIAL
            if ($tipoAnalise == "inicial") {
                $this->view->dadosAnaliseInicial = GerenciarPareceresDAO::pareceresTecnicos($idPronac);
            }
            //CNIC
            if ($tipoAnalise == "cnic") {
                $analise = new AnaliseAprovacao();
                $this->view->dadosAnaliseCnic = $analise->buscarAnaliseProduto('CO', $idPronac, array('PDP.stPrincipal DESC'));
            }
            //PLENARIA
            if ($tipoAnalise == "plenaria") {
                $analise = new AnaliseAprovacao();
                $this->view->dadosAnalisePlenaria = $analise->buscarAnaliseProduto('SE', $idPronac, array('PDP.stPrincipal DESC'));
            }
        }
    }

    public function analiseDeCustoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $tipoAnalise = $this->_request->getParam("tipoAnalise");
        if (!empty($idPronac)) {
            //INICIAL
            if ($tipoAnalise == "inicial") {
                $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $pp = new PlanilhaProjeto();
                $pr = new Projetos();
                $PlanilhaDAO = new PlanilhaProjeto();
                $where = array('PPJ.IdPRONAC = ?' => $idPronac);
                $buscarplanilha = $PlanilhaDAO->buscarAnaliseCustos($where);

                if ($this->bln_readequacao == "false") {
                    $planilhaprojeto = array();
                    $count = 0;
                    $fonterecurso = null;
                    foreach ($buscarplanilha as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaProjeto'] = $resuplanilha->idPlanilhaProjeto;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                        $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                        $count++;

                        $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                        if (isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)) {
                            $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                            $this->view->totalproponente = $buscarsomaproposta['soma'];
                        } else {
                            $this->view->totalproponente = '0.00';
                        }
                        $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                        $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                        $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
                        $this->view->planilha = $planilhaprojeto;
                        $this->view->projeto = $buscarprojeto;
                        $this->view->totalparecerista = $buscarsomaprojeto['soma'];
                    }
                } else {
                    $tblPlanilhaAprovacao = new PlanilhaAprovacao();
                    $tblPlanilhaAprovacao = new PlanilhaAprovacao();
                    $tblPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                    $tblPlanilhaProjeto = new PlanilhaProjeto();
                    $tblProjetos = new Projetos();
                    /******** Planilha aprovacao SR (Proponente - solicitada) ****************/
                    $arrBuscaPlanilha = array();
                    $arrBuscaPlanilha["pap.stAtivo = ? "] = 'N';
                    $arrBuscaPlanilha["pap.idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')"] = '(?)';

                    /******** Planilha aprovacao PA (Parecerista) ****************/
                    $resuplanilha = null;
                    $count = 0;
                    $buscarplanilhaPA = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idPronac, 'PA', $arrBuscaPlanilha);
                    foreach ($buscarplanilhaPA as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->qtItem;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->nrOcorrencia;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->qtDias;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->vlUnitario;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->vlTotal;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativa;
                        $count++;
                    }

                    $resuplanilha = null;
                    $count = 0;
                    $buscarplanilhaSR = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idPronac, 'SR', $arrBuscaPlanilha);

                    foreach ($buscarplanilhaSR as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->qtDias;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->qtItem;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->nrOcorrencia;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->vlUnitario;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->vlTotal;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->dsJustificativa;

                        $valorParecerista = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlparecerista'];
                        $valorSolicitado  = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlproponente'];
                        $reducao = $valorParecerista < $valorSolicitado ? 1 : 0;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['reducao'] = $reducao;
                        $count++;
                    }

                    $buscarprojeto = $tblProjetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

                    $arrWhereSomaPlanilha = array();
                    $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                    $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                    $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
                    $arrWhereSomaPlanilha['stAtivo = ? ']='N';
                    $arrWhereSomaPlanilha["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')");
                    $arrWhereSomaPlanilha["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';

                    $arrWhereSomaPlanilha['tpPlanilha = ? ']='SR';
                    $buscarsomaproposta = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
                    $arrWhereSomaPlanilha['tpPlanilha = ? ']='PA';
                    $buscarsomaprojeto = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

                    $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                    $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
                    $this->view->planilha = $planilhaaprovacao;
                    $this->view->projeto = $buscarprojeto;
                    $this->view->totalparecerista = $buscarsomaprojeto['soma'];
                    $this->view->totalproponente = $buscarsomaproposta['soma'];
                }
                $this->montaTela("/verprojetos/analise-de-custo-parecerista.phtml", array());
            }
            //CNIC
            if ($tipoAnalise == "cnic") {
                $tblPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $tblPlanilhaProjeto = new PlanilhaProjeto();
                $tblPlanilhaAprovacao = new PlanilhaAprovacao();
                $tblProjetos = new Projetos();

                $rsPlanilhaAtual = $tblPlanilhaAprovacao->buscar(array('IdPRONAC = ?'=>$idPronac, 'stAtivo = ?'=>'S'), array('dtPlanilha DESC'))->current();
                $status = (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') ? 'N' : 'S';

                $tipoplanilha = 'CO';
                if ($this->bln_readequacao == "false") {
                    $buscarplanilha = $tblPlanilhaAprovacao->buscarAnaliseCustos($idPronac, $tipoplanilha);

                    $planilhaaprovacao = array();
                    $count = 0;
                    $fonterecurso = null;
                    foreach ($buscarplanilha as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtitemcomp;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrocorrenciacomp;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlunitariocomp;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtdiascomp;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->VlSugeridoConselheiro;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativaConselheiro;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                        $count++;
                    }
                    $buscarprojeto = $tblProjetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                    $idProjetoX = !empty($buscarprojeto->idProjeto) ? $buscarprojeto->idProjeto : 0;
                    //$buscarsomaaprovacao = $pa->somarPlanilhaAprovacao($idPronac, 206, $tipoplanilha);
                    $buscarsomaproposta = $tblPlanilhaProposta->somarPlanilhaProposta($idProjetoX);
                    $buscarsomaprojeto = $tblPlanilhaProjeto->somarPlanilhaProjeto($idPronac);
                } else {
                    $buscarplanilhaCO = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idPronac, 'CO', array('PAP.stAtivo=?'=>$status));

                    $planilhaaprovacao = array();
                    $count = 0;
                    $fonterecurso = null;
                    foreach ($buscarplanilhaCO as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtItem;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrOcorrencia;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlUnitario;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtDias;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->vlTotal;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativa;
                        //$planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                        $count++;
                    }

                    /******** Planilha aprovacao SR (Proponente - solicitada) ****************/
                    $arrBuscaPlanilha = array();
                    $arrBuscaPlanilha["pap.stAtivo = ? "] = 'N';
                    $arrBuscaPlanilha["pap.idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')"] = '(?)';

                    $resuplanilha = null;
                    $count = 0;
                    $buscarplanilhaSR = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idPronac, 'SR', $arrBuscaPlanilha);

                    foreach ($buscarplanilhaSR as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->qtDias;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->qtItem;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->nrOcorrencia;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->vlUnitario;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->vlTotal;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->dsJustificativa;

                        $valorConselheiro = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlconselheiro'];
                        $valorSolicitado  = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlproponente'];
                        $reducao = $valorConselheiro < $valorSolicitado ? 1 : 0;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['reducao'] = $reducao;
                        $count++;
                    }

                    /******** Planilha aprovacao PA (Parecerista) ****************/
                    $resuplanilha = null;
                    $count = 0;
                    $buscarplanilhaPA = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idPronac, 'PA', $arrBuscaPlanilha);

                    foreach ($buscarplanilhaPA as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->qtItem;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->nrOcorrencia;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->qtDias;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->vlUnitario;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->vlTotal;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativa;
                        $count++;
                    }

                    $buscarprojeto = $tblProjetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

                    $arrWhereSomaPlanilha = array();
                    $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                    $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                    $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
                    $arrWhereSomaPlanilha['stAtivo = ? ']='N';
                    $arrWhereSomaPlanilha["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')");
                    $arrWhereSomaPlanilha["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';

                    $arrWhereSomaPlanilha['tpPlanilha = ? ']='SR';
                    $buscarsomaproposta = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
                    $arrWhereSomaPlanilha['tpPlanilha = ? ']='PA';
                    $buscarsomaprojeto = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
                }//feacha if bln_readequacao
                /**** FIM - CODIGO DE READEQUACAO ****/

                $arrWhereSomaPlanilha = array();
                $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                $arrWhereSomaPlanilha['tpPlanilha = ? ']='CO';
                $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
                $arrWhereSomaPlanilha['stAtivo = ? ']=$status;
                $buscarsomaaprovacao = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

                $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
                $this->view->planilha = $planilhaaprovacao;
                $this->view->projeto = $buscarprojeto;
                $this->view->totalcomponente = $buscarsomaaprovacao['soma'];
                $this->view->totalparecerista = $buscarsomaprojeto['soma'];
                $this->view->totalproponente = $buscarsomaproposta['soma'];

                $this->montaTela("/verprojetos/analise-de-custo-cnic.phtml", array());
            }
            //PLENARIA
            /*if($tipoAnalise == "plenaria_OLD")
            {
                $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $pp = new PlanilhaProjeto();
                $pa = new PlanilhaAprovacao();
                $pr = new Projetos();

                $tipoplanilha = 'SE';
                $buscarplanilha = $pa->buscarAnaliseCustos($idPronac, $tipoplanilha);

                $planilhaaprovacao = array();
                $count = 0;
                $fonterecurso = null;
                foreach ($buscarplanilha as $resuplanilha) {
                    $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtitemcomp;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrocorrenciacomp;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlunitariocomp;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtdiascomp;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->VlSugeridoConselheiro;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativaConselheiro;
                    $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                    $count++;
                }

                $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                $buscarsomaaprovacao = $pa->somarPlanilhaAprovacao($idPronac, 206, $tipoplanilha);
                if(isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)){
                    $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                    $this->view->totalproponente = $buscarsomaproposta['soma'];
                }else{
                 $this->view->totalproponente = '0.00';
                }
                $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
                $this->view->planilha = $planilhaaprovacao;
                $this->view->projeto = $buscarprojeto;
                $this->view->totalcomponente = $buscarsomaaprovacao['soma'];
                $this->view->totalparecerista = $buscarsomaprojeto['soma'];

                $this->montaTela("/consultardadosprojeto/analise-de-custo-plenaria.phtml", array());
            }*/
            //PLENARIA
            if ($tipoAnalise == "plenaria") {
                $tblPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $tblPlanilhaProjeto = new PlanilhaProjeto();
                $tblPlanilhaAprovacao = new PlanilhaAprovacao();
                $tblProjetos = new Projetos();

                $tipoplanilha = 'SE';

                if ($this->bln_readequacao == "false") {
                    $buscarplanilha = $tblPlanilhaAprovacao->buscarAnaliseCustos($idPronac, $tipoplanilha, array('PAP.stAtivo=?'=>'S'));

                    $planilhaaprovacao = array();
                    $count = 0;
                    $fonterecurso = null;
                    foreach ($buscarplanilha as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtitemcomp;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrocorrenciacomp;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlunitariocomp;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtdiascomp;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->VlSugeridoConselheiro;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativaConselheiro;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                        $count++;
                    }
                    $buscarprojeto = $tblProjetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                    $idProjetoX = !empty($buscarprojeto->idProjeto) ? $buscarprojeto->idProjeto : 0;
                    $buscarsomaproposta = $tblPlanilhaProposta->somarPlanilhaProposta($idProjetoX);
                    $buscarsomaprojeto = $tblPlanilhaProjeto->somarPlanilhaProjeto($idPronac);
                } else {
                    $buscarplanilhaSE = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idPronac, 'SE', array('PAP.stAtivo=?'=>'S'));

                    $planilhaaprovacao = array();
                    $count = 0;
                    $fonterecurso = null;
                    foreach ($buscarplanilhaSE as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtItem;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrOcorrencia;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlUnitario;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtDias;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->vlTotal;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativa;
                        //$planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                        $count++;
                    }

                    /******** Planilha aprovacao SR (Proponente - solicitada) ****************/
                    $arrBuscaPlanilha = array();
                    $arrBuscaPlanilha["pap.stAtivo = ? "] = 'N';
                    $arrBuscaPlanilha["pap.idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')"] = '(?)';

                    $resuplanilha = null;
                    $count = 0;
                    $buscarplanilhaSR = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idPronac, 'SR', $arrBuscaPlanilha);

                    foreach ($buscarplanilhaSR as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;

                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->qtDias;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->qtItem;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->nrOcorrencia;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->vlUnitario;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->vlTotal;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->dsJustificativa;

                        $valorConselheiro = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlconselheiro'];
                        $valorSolicitado  = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlproponente'];
                        $reducao = $valorConselheiro < $valorSolicitado ? 1 : 0;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['reducao'] = $reducao;
                        $count++;
                    }

                    /******** Planilha aprovacao PA (Parecerista) ****************/
                    $resuplanilha = null;
                    $count = 0;
                    $buscarplanilhaPA = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idPronac, 'PA', $arrBuscaPlanilha);

                    foreach ($buscarplanilhaPA as $resuplanilha) {
                        $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->Unidade;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->qtItem;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->nrOcorrencia;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->qtDias;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->vlUnitario;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->vlTotal;
                        $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativa;
                        $count++;
                    }

                    $buscarprojeto = $tblProjetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

                    $arrWhereSomaPlanilha = array();
                    $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                    $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                    $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
                    $arrWhereSomaPlanilha['stAtivo = ? ']='N';
                    $arrWhereSomaPlanilha["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')");
                    $arrWhereSomaPlanilha["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';

                    $arrWhereSomaPlanilha['tpPlanilha = ? ']='SR';
                    $buscarsomaproposta = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
                    $arrWhereSomaPlanilha['tpPlanilha = ? ']='PA';
                    $buscarsomaprojeto = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
                }//feacha if bln_readequacao

                $arrWhereSomaPlanilha = array();
                $arrWhereSomaPlanilha['idPronac = ?']=$idPronac;
                $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
                $arrWhereSomaPlanilha['tpPlanilha = ? ']='SE';
                $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
                $arrWhereSomaPlanilha['stAtivo = ? ']='S';
                $buscarsomaaprovacao = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

                $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
                $this->view->planilha = $planilhaaprovacao;
                $this->view->projeto = $buscarprojeto;
                $this->view->totalcomponente = $buscarsomaaprovacao['soma'];
                $this->view->totalparecerista = $buscarsomaprojeto['soma'];
                $this->view->totalproponente = $buscarsomaproposta['soma'];

                $this->montaTela("/verprojetos/analise-de-custo-plenaria.phtml", array());
            }
        }
    }


    public function analiseReadequacaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {
            $tbPedidoAlteracao = new tbPedidoAlteracaoProjeto();
            $arrBusca = array();
            $arrBusca['IdPRONAC =?']=$idPronac;
            $arrBusca['siVerificacao =?']= 1; //analise finalizada
            $rsPedidoAlteracao = $tbPedidoAlteracao->buscar($arrBusca);
            $this->view->dados = $rsPedidoAlteracao;
        }
    }

    public function tipoReadequacaoSolicitadaAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $idPedidoAlteracao = $this->_request->getParam("idPedidoAlteracao");

        if (!empty($idPedidoAlteracao)) {
            $tbPedidoAlteracaoXTipoAlterecao = new tbPedidoAlteracaoXTipoAlteracao();
            $arrBusca = array();
            $arrBusca['idPedidoAlteracao =?']= $idPedidoAlteracao; //analise finalizada
            $rsPedidoAlteracaoXTipoAlterecao = $tbPedidoAlteracaoXTipoAlterecao->buscaCompleta($arrBusca, array('ta.tpAlteracaoProjeto ASC'));
            $this->view->dados = $rsPedidoAlteracaoXTipoAlterecao;
        }
    }

    public function dadosFiscalizacaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $idFiscalizacao = $this->_request->getParam("idFiscalizacao");

        if (!empty($idPronac)) {
            $projetoDao = new Projetos();
            $tbFiscalizacao = new Fiscalizacao_Model_DbTable_TbFiscalizacao();
            $this->view->projeto = $projetoDao->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

            if (empty($idFiscalizacao)) {
                $this->view->infoProjeto = $tbFiscalizacao->projetosFiscalizacaoConsultar(array('Projetos.IdPRONAC = ?' => $idPronac), array('tbFiscalizacao.dtInicioFiscalizacaoProjeto ASC', 'tbFiscalizacao.dtFimFiscalizacaoProjeto ASC'));
            } else {
                $this->view->infoProjeto = $tbFiscalizacao->projetosFiscalizacaoConsultar(array('Projetos.IdPRONAC = ?' => $idPronac, 'tbFiscalizacao.idFiscalizacao = ?' => $idFiscalizacao), array('tbFiscalizacao.dtInicioFiscalizacaoProjeto ASC', 'tbFiscalizacao.dtFimFiscalizacaoProjeto ASC'));

                $OrgaoFiscalizadorDao = new Fiscalizacao_Model_DbTable_TbOrgaoFiscalizador();
                if ($idFiscalizacao) {
                    $this->view->dadosOrgaos = $OrgaoFiscalizadorDao->dadosOrgaos(array('tbOF.idFiscalizacao = ?' => $idFiscalizacao));
                }
                $ArquivoFiscalizacaoDao = new Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();
                if ($idFiscalizacao) {
                    $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $idFiscalizacao));
                }
                $RelatorioFiscalizacaoDAO = new Fiscalizacao_Model_DbTable_TbRelatorioFiscalizacao();
                $this->view->relatorioFiscalizacao = $RelatorioFiscalizacaoDAO->buscaRelatorioFiscalizacao($idFiscalizacao);

                $this->montaTela("/verprojetos/detalhes-dados-da-fiscalizacao.phtml", array());
                return;
            }
        }
    }

    public function diligenciasAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $tblProjeto = new Projetos();
            $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $projeto = $tblProjeto->buscar(array('IdPRONAC = ?' => $idPronac))->current();
            $this->view->projeto = $projeto;

            if (isset($projeto->idProjeto) && !empty($projeto->idProjeto)) {
                $this->view->diligenciasProposta = $tblPreProjeto->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $projeto->idProjeto,'aval.ConformidadeOK = ? '=>0));
            }
            $this->view->diligencias = $tblProjeto->listarDiligencias(array('pro.IdPRONAC = ?' => $idPronac, 'dil.stEnviado = ?' => 'S'));
        }
    }

    public function visualizarDiligenciaAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $idDiligencia = $this->_request->getParam("idDiligencia");
        $idDiligenciaPreProjeto = $this->_request->getParam("idDiligenciaPreProjeto");

        if (!empty($idPronac) && !empty($idDiligencia)) {
            $Projetosdao        = new Projetos();
            $PreProjetodao      = new Proposta_Model_DbTable_PreProjeto();
            $DocumentosExigidosDao  = new DocumentosExigidos();

            if (!empty($idDiligencia) && empty($idDiligenciaPreProjeto)) {
                $resp = $Projetosdao->listarDiligencias(array('pro.IdPRONAC = ?' => $this->view->idPronac, 'dil.idDiligencia = ?' => $idDiligencia));
                $this->view->nmCodigo       = 'PRONAC';
                $this->view->nmTipo         = 'DO PROJETO';
                $this->view->tipoDiligencia = $resp[0]->tipoDiligencia;
            }
            if (!empty($idDiligenciaPreProjeto)) {
                if ($idPronac) {
                    $projeto        = $Projetosdao->buscar(array('IdPRONAC = ?' => $idPronac));
                    $idPreProjeto   = $projeto[0]->idProjeto;
                }
                if (isset($projeto[0]->idProjeto)) {
                    $idPreProjeto   = $projeto[0]->idProjeto;
                }
                $resp           = $PreProjetodao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $idPreProjeto, ' aval.idAvaliacaoProposta = ?' => $idDiligencia));

                $this->view->nmCodigo   = 'Nr PROPOSTA';
                $this->view->nmTipo     = 'DA PROPOSTA';
                $this->view->Descricao  = $resp[0]->Descricao;
            }//fecha if Diligencia PreProjeto

            $this->view->stEnviado      = $resp[0]->stEnviado;
            $this->view->pronac         = $resp[0]->pronac;
            $this->view->nomeProjeto    = $resp[0]->nomeProjeto;
            //$this->view->Proponente = $rd[0]->Proponente;
            $this->view->dataSolicitacao    = date('d/m/Y H:i', strtotime($resp[0]->dataSolicitacao));
            if (isset($resp[0]->dataResposta) && $resp[0]->dataResposta != '') {
                $this->view->dataResposta       = date('d/m/Y H:i', strtotime($resp[0]->dataResposta));
            }
            $this->view->solicitacao        = $resp[0]->Solicitacao;
            $this->view->resposta           = $resp[0]->Resposta;
            if (isset($resp[0]->idCodigoDocumentosExigidos) && !empty($resp[0]->idCodigoDocumentosExigidos)) {
                $documento                      = $DocumentosExigidosDao->listarDocumentosExigido($resp[0]->idCodigoDocumentosExigidos);
                $this->view->DocumentosExigido  = $documento[0]->Descricao;
                $this->view->Opcao              = $documento[0]->Opcao;
            }
        }

        $arquivo = new Arquivo();
        $arquivos = $arquivo->buscarAnexosDiligencias($idDiligencia);
        $this->view->arquivos = $arquivos;
    }

    public function imprimirProjetoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $arrConteudoImpressao = $this->_request->getParam("conteudoImpressao");
        $this->view->arrConteudoImpressao = $arrConteudoImpressao;

        //VERIFICA FASE DO PROJETO
        $this->faseDoProjeto($idPronac);

        if (!empty($idPronac)) {
            //DADOS PRINCIPAIS
            $dados = array();
            $dados['idPronac'] = (int) $idPronac;

            try {
                $rst = ConsultarDadosProjetoDAO::obterDadosProjeto($dados);
                if (count($rst) > 0) {
                    $this->view->projeto = $rst[0];
                    $this->view->idpronac = $_REQUEST['idPronac'];
                    $this->view->idprojeto = $rst[0]->idProjeto;
                    if ($rst[0]->codSituacao == 'E12' || $rst[0]->codSituacao == 'E13' || $rst[0]->codSituacao == 'E15' || $rst[0]->codSituacao == 'E50' || $rst[0]->codSituacao == 'E59' || $rst[0]->codSituacao == 'E61' || $rst[0]->codSituacao == 'E62') {
                        $this->view->menuCompExec = 'true';
                    }

                    $geral = new ProponenteDAO();
                    $tblProjetos = new Projetos();

                    $arrBusca['IdPronac = ?']=$idPronac;
                    $rsProjeto = $tblProjetos->buscar($arrBusca)->current();

                    $idPreProjeto = $rsProjeto->idProjeto;

                    $tbdados = $geral->buscarDadosProponente($idPronac);
                    $this->view->proponente = $tbdados;

                    $this->view->NrProjeto       = $rst[0]->NrProjeto;
                    $this->view->NomeProjeto     = $rst[0]->NomeProjeto;
                    $this->view->NomeProponente  = $tbdados[0]->Nome;

                    $tbemail = $geral->buscarEmail($idPronac);
                    $this->view->email = $tbemail;

                    $tbtelefone = $geral->buscarTelefone($idPronac);
                    $this->view->telefone = $tbtelefone;

                    $tblAgente = new Agente_Model_DbTable_Agentes();
                    $rsAgente = $tblAgente->buscar(array('CNPJCPF=?'=>$tbdados[0]->CgcCpf))->current();

                    $rsDirigentes = $tblAgente->buscarDirigentes(array('v.idVinculoPrincipal =?'=>$rsAgente->idAgente));
                    //$tbDirigentes = $geral->buscarDirigentes($idPronac);
                    $this->view->dirigentes = $rsDirigentes;

                    $this->view->CgcCpf = $tbdados[0]->CgcCpf;
                    $this->view->itensGeral = array();
                    $this->view->proposta   = array();

                    if (!empty($idPreProjeto)) {
                        //OUTROS DADOS PROPONENTE
                        $this->view->itensGeral = Proposta_Model_AnalisarPropostaDAO::buscarGeral($idPreProjeto);

                        if (in_array('dadoscomplementares', $arrConteudoImpressao)) {
                            //DADOS COMPLEMENTARES
                            $tblProposta = new Proposta_Model_DbTable_PreProjeto();
                            $rsProposta = $tblProposta->buscar(array('idPreProjeto=?'=>$idPreProjeto))->current();
                            $this->view->proposta = $rsProposta;
                        }
                    }

                    //PLANO DE DISTRIBUICAO
                    if (in_array('planodistribuicao', $arrConteudoImpressao)) {
                        $buscarDistribuicao = RealizarAnaliseProjetoDAO::planodedistribuicao($idPronac);
                        $this->view->distribuicao = $buscarDistribuicao;
                    }

                    //LOCAL DE REALIZACAO e DESLOCAMENTO
                    if (in_array('localrealizacao_deslocamento', $arrConteudoImpressao)) {
                        $buscarLocalRealizacao = RealizarAnaliseProjetoDAO::localrealizacao($idPronac);
                        $this->view->dadosLocalizacao = $buscarLocalRealizacao;

                        //DESLOCAMENTO
                        $buscarDeslocamento = RealizarAnaliseProjetoDAO::deslocamento($idPronac);
                        $this->view->dadosDeslocamento = $buscarDeslocamento;
                    }

                    //DIVULGACAO
                    if (in_array('planodivulgacao', $arrConteudoImpressao)) {
                        $buscarDivulgacao = RealizarAnaliseProjetoDAO::divulgacao($idPronac);
                        $this->view->divulgacao = $buscarDivulgacao;
                    }

                    $tblProjetos = new Projetos();

                    //PLANILHA ORCAMENTARIA
                    $this->view->itensPlanilhaOrcamentaria = array();
                    if (in_array('planilhaorcamentaria', $arrConteudoImpressao)) {
                        if (!empty($idPreProjeto)) {
                            $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
                            $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($rsProjeto->IdPRONAC, 3); // 3=Planilha Or�ament�ria Aprovada Ativa
                            if (count($planilhaOrcamentaria)>0) {
                                $tipoPlanilha = 3;
                            } else {
                                $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($rsProjeto->IdPRONAC, 2);
                                $tipoPlanilha = 2;

                                if (count($planilhaOrcamentaria)>0) {
                                    $tipoPlanilha = 2;
                                } else {
                                    $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($rsProjeto->IdPRONAC, 1);
                                    $tipoPlanilha = 1;
                                }
                            }
                            $planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, $tipoPlanilha);
                            $this->view->tipoPlanilha = $tipoPlanilha;
                            $this->view->planilha = $planilha;
                        }
                    }

                    //DOCUMENTOS ANEXADOS
                    $idAgente = null;
                    if (in_array('documentosanexados', $arrConteudoImpressao)) {
                        $tblAgente = new Agente_Model_DbTable_Agentes();
                        $rsAgente = $tblAgente->buscar(array('CNPJCPF = ?'=>$rsProjeto->CgcCpf));
                        if ($rsAgente->count() > 0) {
                            $idAgente = $rsAgente[0]->idAgente;
                        }
                        if (count($rsProjeto) > 0 && !empty($idAgente)) {
                            $ordem = array();
                            $ordem = array("3 DESC");
                            //if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }
                            $tbDoc = new Proposta_Model_DbTable_TbDocumentosAgentes();
                            $rsDocs = $tbDoc->buscatodosdocumentos($idAgente, $rsProjeto->idProjeto, $rsProjeto->IdPRONAC);
                            $this->view->registrosDocAnexados = $rsDocs;
                        }
                    }

                    //DILIGENCIAS
                    $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
                    if (in_array('diligencias', $arrConteudoImpressao)) {
                        if (isset($_POST['diligenciasProposta']) && !empty($_POST['diligenciasProposta'])) {
                            $this->view->checkDiligenciasProposta = true;
                            if (!empty($idPreProjeto)) {
                                $this->view->diligenciasProposta = $tblPreProjeto->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $idPreProjeto,'aval.ConformidadeOK = ? '=>0));
                            }
                        }
                        if (isset($_POST['diligenciasProjeto']) && !empty($_POST['diligenciasProjeto'])) {
                            $this->view->checkDiligenciasProjeto = true;
                            $this->view->diligenciasProjeto = $tblProjetos->listarDiligencias(array('pro.IdPRONAC = ?' => $idPronac));
                        }
                    }

                    //PARECER CONSOLIDADO
                    if (in_array('parecer-consolidado', $arrConteudoImpressao)) {
                        $Parecer = new Parecer_Model_DbTable_Parecer();
                        $this->view->identificacaoParecerConsolidado = $Parecer->identificacaoParecerConsolidado($idPronac);

                        $vwMemoriaDeCalculo = new vwMemoriaDeCalculo();
                        $this->view->memoriaDeCalculo = $vwMemoriaDeCalculo->busca($idPronac);

                        $tbAnaliseDeConteudo = new Parecer_Model_DbTable_TbAnaliseDeConteudo();
                        $this->view->outrasInformacoesParecer = $tbAnaliseDeConteudo->buscarOutrasInformacoes($idPronac);

                        $tbPauta = new tbPauta();
                        $this->view->parecerDoComponenteComissao = $tbPauta->parecerDoComponenteComissao($idPronac);

                        $tbConsolidacaoVotacao = new tbConsolidacaoVotacao();
                        $this->view->consolidacaoPlenaria = $tbConsolidacaoVotacao->consolidacaoPlenaria($idPronac);
                    }

                    //TRAMITACAO DE PROJETO e TRAMITACAO DE DOCUMENTOS
                    if (in_array('tramitacao', $arrConteudoImpressao)) {
                        $ordem = array();
                        $ordem = array("2 ASC");
                        $tblHistDoc = new tbHistoricoDocumento();
                        $rsHistDoc = $tblHistDoc->buscarHistoricoTramitacaoProjeto(array("p.IdPronac =?"=>$idPronac), $ordem);
                        $this->view->registrosHisTramProjeto = $rsHistDoc;

                        //TRAMITACAO DE DOCUMENTOS
                        $arrBusca = array();
                        $arrBusca['h.idDocumento <> ?']=0;
                        $arrBusca['h.stEstado = ?']=1;
                        $arrBusca['p.IdPronac =?']=$idPronac;
                        $ordem = array();
                        $ordem = array("2 ASC");
                        $rsHistDoc = $tblHistDoc->buscarHistoricoTramitacaoDocumento($arrBusca, $ordem);
                        $this->view->registrosHisTramDoc = $rsHistDoc;
                    }

                    $tblProjeto = new Projetos();

                    //PROVIDENCIA TOMADA
                    if (in_array('providenciatomada', $arrConteudoImpressao)) {
                        $rsProjeto = $tblProjeto->buscar(array("IdPronac=?"=>$idPronac))->current();
                        $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;

                        $ordem = array();
                        $ordem = array("4 ASC");
                        $tblHisSituacao = new HistoricoSituacao();
                        $rsHisSituacao = $tblHisSituacao->buscar(array('AnoProjeto+Sequencial = ?'=>$pronac), $ordem);
                        $this->view->registrosProvTomada = $rsHisSituacao;
                    }

                    //CERTIDOES NEGATIVAS
                    if (in_array('certidoes', $arrConteudoImpressao)) {
                        $Projetos = new Projetos();
                        $rs = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();

                        $sv = new certidaoNegativa();
                        $resultado = $sv->buscarCertidaoNegativa($rs->CgcCpf);
                        $this->view->certidoes = $resultado;
                    }

                    //REGLARIDADE PROPONENTE
                    if (in_array('regularidadeproponente', $arrConteudoImpressao)) {
                        $Projetos = new Projetos();
                        $rs = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();

                        $paRegularidade = new paRegularidade();
                        $consultaRegularidade = $paRegularidade->exec($rs->CgcCpf);
                        $this->view->regularidadeproponente = $consultaRegularidade;

                        $agentes = new Agente_Model_DbTable_Agentes();
                        $buscaAgentes = $agentes->buscar(array('CNPJCPF = ?' => $rs->CgcCpf));
                        $this->view->regularidadeCgccpf = $rs->CgcCpf;

                        $nomes = new Nomes();
                        $buscaNomes = $nomes->buscar(array('idAgente = ?' => $buscaAgentes[0]->idAgente));
                        $nomeProponente = $buscaNomes[0]->Descricao;
                        $this->view->regularidadeProponente = $nomeProponente;

                        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
                        if (strlen(trim($auth->getIdentity()->usu_identificacao)) == 11) {
                            $cpfcnpjUsuario = Mascara::addMaskCPF(trim($auth->getIdentity()->usu_identificacao));
                        } else {
                            $cpfcnpjUsuario = Mascara::addMaskCNPJ(trim($auth->getIdentity()->usu_identificacao));
                        }
                        $this->view->dadosUsuarioConsulta = '( '. $cpfcnpjUsuario .' ) '.$auth->getIdentity()->usu_nome.' - '.date('d/m/Y').' �s '.date('h:i:s');
                    }

                    // ----------------------------------------------------------------------
                    // ---------------------- FASE 2 - EXECUAO DO PROJETO -------------------
                    // ----------------------------------------------------------------------
                    if ($this->intFaseProjeto == '2' || $this->intFaseProjeto == '3' || $this->intFaseProjeto == '4') {

                        //RECURSOS
                        if (in_array('analiseprojeto', $arrConteudoImpressao)) {
                            $buscarProjetos = $tblProjetos->buscarProjetosSolicitacaoRecurso($idPronac);

                            // busca as solicita��es de recurso do projeto
                            $this->tbRecurso         = new tbRecurso();
                            $buscarRecursos          = $this->tbRecurso->buscarDados($idPronac);
                            $buscarRecursosPlanilha  = $this->tbRecurso->buscarDados($idPronac); // necess�rio chamar o mesmo m�todo para jogar na vis�o sem erros

                            // manda os dados para a vis�o
                            $this->view->projetosRecurso  = $buscarProjetos;
                            $this->view->recursos         = $buscarRecursos;
                            $this->view->recursosPlanilha = $buscarRecursosPlanilha;
                        }

                        //APROVACAO
                        if (in_array('aprovacao', $arrConteudoImpressao)) {
                            $rsProjeto = $tblProjetos->buscar(array("IdPronac=?"=>$idPronac))->current();
                            $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;

                            $tblAprovacao = new Aprovacao();
                            $rsAprovacao = $tblAprovacao->buscaCompleta(array('a.AnoProjeto + a.Sequencial = ?'=>$pronac), array('a.idAprovacao ASC'));
                            $this->view->dadosAprovacao = $rsAprovacao;
                        }

                        // =================================== ANALISE DO PROJETO =====================================
                        if (in_array('analiseprojeto', $arrConteudoImpressao)) {
                            // === INICIAL == PARECER CONSOLIDADO
                            $this->view->resultAnaliseProjeto = array();
                            $this->view->resultAnaliseProjetoCNIC = array();
                            $this->view->resultAnaliseProjetoPlenaria = array();
                            $this->view->fontesincentivo  = 0;
                            $this->view->outrasfontes     = 0;
                            $this->view->valorproposta    = 0;
                            $this->view->valorparecerista = 0;
                            $this->view->valorcomponente  = 0;
                            $this->view->enquadramento = 'N&atilde;o Enquadrado';

                            $parecer = new Parecer_Model_DbTable_Parecer();
                            $analiseparecer = $parecer->buscarParecer(array(1), $idPronac)->current();
                            if (is_object($analiseparecer)) {
                                $this->view->resultAnaliseProjeto = $analiseparecer->toArray();
                            }

                            $projeto = new Projetos();
                            $buscarPronac = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
                            $idprojeto = $buscarPronac['idProjeto'];

                            $this->view->resultAnaliseProduto = GerenciarPareceresDAO::projetosConsolidadosParte2($idPronac);

                            $planilhaprojeto = new PlanilhaProjeto();
                            $parecerista = $planilhaprojeto->somarPlanilhaProjeto($idPronac);
                            $this->view->valorparecerista = $parecerista['soma'];

                            if (!empty($idprojeto)) {
                                $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                                $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                                $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
                                $this->view->fontesincentivo  = $fonteincentivo['soma'];
                                $this->view->outrasfontes     = $outrasfontes['soma'];
                                $this->view->valorproposta    = $fonteincentivo['soma'] + $outrasfontes['soma'];
                            }

                            $tbEnquadramento    = new Admissibilidade_Model_Enquadramento();
                            $verificaEnquadramento = $tbEnquadramento->buscarDados($idPronac, null, false);

                            if (is_object($verificaEnquadramento) && count($verificaEnquadramento) > 0) {
                                if ($verificaEnquadramento->Enquadramento == '2') {
                                    $this->view->enquadramento = 'Artigo 18';
                                } elseif ($verificaEnquadramento->Enquadramento == '1') {
                                    $this->view->enquadramento = 'Artigo 26';
                                } else {
                                    $this->view->enquadramento = 'N&atilde;o Enquadrado';
                                }
                            } else {
                                $this->view->enquadramento = 'N&atilde;o Enquadrado';
                            }

                            // === INICIAL == ANALISE DE CONTEUDO
                            $this->view->dadosAnaliseInicial = GerenciarPareceresDAO::pareceresTecnicos($idPronac);

                            // === INICIAL == ANALISE DE CUSTO
                            $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
                            $pp = new PlanilhaProjeto();
                            $pr = new Projetos();
                            $PlanilhaDAO = new PlanilhaProjeto();
                            $where = array('PPJ.IdPRONAC = ?' => $idPronac);
                            $buscarplanilha = $PlanilhaDAO->buscarAnaliseCustos($where);

                            $planilhaprojeto = array();
                            $count = 0;
                            $fonterecurso = null;
                            foreach ($buscarplanilha as $resuplanilha) {
                                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaProjeto'] = $resuplanilha->idPlanilhaProjeto;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $count++;
                            }
                            $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                            if (isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)) {
                                $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                                $this->view->totalproponenteInicial = $buscarsomaproposta['soma'];
                            } else {
                                $this->view->totalproponenteInicial = '0.00';
                            }
                            $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                            $this->view->planilhaInicial = $planilhaprojeto;
                            $this->view->totalpareceristaInicial = $buscarsomaprojeto['soma'];

                            // === CNIC == PARECER CONSOLIDADO
                            $parecer = new Parecer_Model_DbTable_Parecer();
                            $analiseparecer = $parecer->buscarParecer(array(6), $idPronac)->current();
                            if (is_object($analiseparecer)) {
                                $this->view->resultAnaliseProjetoCNIC = $analiseparecer->toArray();
                            }

                            $projeto = new Projetos();
                            $buscarPronac = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
                            $idprojeto = $buscarPronac['idProjeto'];

                            $tpPlanilha = 'CO';
                            $analiseaprovacao = new AnaliseAprovacao();
                            $produtos = $analiseaprovacao->buscarAnaliseProduto($tpPlanilha, $idPronac);
                            $this->view->resultAnaliseProdutoCNIC = $produtos;

                            $planilhaAprovacao = new PlanilhaAprovacao();
                            $valor = $planilhaAprovacao->somarPlanilhaAprovacao($idPronac, 206, $tpPlanilha);
                            $this->view->valorcomponenteCNIC  = $valor['soma'];

                            if (!empty($idprojeto)) {
                                $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                                $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                                $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
                                $this->view->fontesincentivoCNIC  = $fonteincentivo['soma'];
                                $this->view->outrasfontesCNIC     = $outrasfontes['soma'];
                                $this->view->valorpropostaCNIC    = $fonteincentivo['soma'] + $outrasfontes['soma'];
                            }

                            $verificaEnquadramento = RealizarAnaliseProjetoDAO::verificaEnquadramento($idPronac, $tpPlanilha);

                            if (count($verificaEnquadramento) > 0) {
                                if ($verificaEnquadramento[0]->stArtigo18 == true) {
                                    $this->view->enquadramentoCNIC = 'Artigo 18';
                                } elseif ($verificaEnquadramento[0]->stArtigo26 == true) {
                                    $this->view->enquadramentoCNIC = 'Artigo 26';
                                } else {
                                    $this->view->enquadramentoCNIC = 'N&atilde;o Enquadrado';
                                }
                            } else {
                                $this->view->enquadramentoCNIC = 'N&atilde;o Enquadrado';
                            }

                            // === CNIC == ANALISE DE CONTEUDO
                            $analise = new AnaliseAprovacao();
                            $this->view->dadosAnaliseCnic = $analise->buscarAnaliseProduto('CO', $idPronac, array('PDP.stPrincipal DESC'));

                            // === CNIC == ANALISE DE CUSTO
                            $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
                            $pp = new PlanilhaProjeto();
                            $pa = new PlanilhaAprovacao();
                            $pr = new Projetos();

                            $tipoplanilha = 'CO';
                            $buscarplanilhaCNIC = $pa->buscarAnaliseCustos($idPronac, $tipoplanilha);

                            $planilhaaprovacao = array();
                            $count = 0;
                            $fonterecurso = null;
                            foreach ($buscarplanilhaCNIC as $resuplanilha) {
                                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtitemcomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrocorrenciacomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlunitariocomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtdiascomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->VlSugeridoConselheiro;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativaConselheiro;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                                $count++;
                            }
                            $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                            $buscarsomaaprovacao = $pa->somarPlanilhaAprovacao($idPronac, 206, $tipoplanilha);
                            if (isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)) {
                                $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                                $this->view->totalproponenteCNIC = $buscarsomaproposta['soma'];
                            } else {
                                $this->view->totalproponenteCNIC = '0.00';
                            }
                            $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                            $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                            $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
                            $this->view->planilhaCNIC = $planilhaaprovacao;
                            $this->view->totalcomponenteCNIC = $buscarsomaaprovacao['soma'];
                            $this->view->totalpareceristaCNIC = $buscarsomaprojeto['soma'];

                            // === PLENARIA == PARECER CONSOLIDADO
                            $parecer = new Parecer_Model_DbTable_Parecer();
                            $analiseparecer = $parecer->buscarParecer(array(10), $idPronac)->current();
                            if (is_object($analiseparecer)) {
                                $this->view->resultAnaliseProjetoPlenaria = $analiseparecer->toArray();
                            }

                            $projeto = new Projetos();
                            $buscarPronac = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
                            $idprojeto = $buscarPronac['idProjeto'];

                            $tpPlanilha = 'SE';
                            $analiseaprovacao = new AnaliseAprovacao();
                            $produtos = $analiseaprovacao->buscarAnaliseProduto($tpPlanilha, $idPronac);
                            $this->view->resultAnaliseProdutoPlenaria = $produtos;

                            $planilhaAprovacao = new PlanilhaAprovacao();
                            $valor = $planilhaAprovacao->somarPlanilhaAprovacao($idPronac, 206, $tpPlanilha);
                            $this->view->valorcomponentePlenaria  = $valor['soma'];

                            if (!empty($idprojeto)) {
                                $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                                $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                                $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
                                $this->view->fontesincentivoPlenaria  = $fonteincentivo['soma'];
                                $this->view->outrasfontesPlenaria     = $outrasfontes['soma'];
                                $this->view->valorpropostaPlenaria    = $fonteincentivo['soma'] + $outrasfontes['soma'];
                            }

                            $verificaEnquadramento = RealizarAnaliseProjetoDAO::verificaEnquadramento($idPronac, $tpPlanilha);

                            if (count($verificaEnquadramento) > 0) {
                                if ($verificaEnquadramento[0]->stArtigo18 == true) {
                                    $this->view->enquadramentoPlenaria = 'Artigo 18';
                                } elseif ($verificaEnquadramento[0]->stArtigo26 == true) {
                                    $this->view->enquadramentoPlenaria = 'Artigo 26';
                                } else {
                                    $this->view->enquadramentoPlenaria = 'N&atilde;o Enquadrado';
                                }
                            } else {
                                $this->view->enquadramentoPlenaria = 'N&atilde;o Enquadrado';
                            }

                            // === PLENARIA == ANALISE DE CONTEUDO
                            $analise = new AnaliseAprovacao();
                            $this->view->dadosAnalisePlenaria = $analise->buscarAnaliseProduto('SE', $idPronac, array('PDP.stPrincipal DESC'));

                            // === PLENARIA == ANALISE DE CUSTO
                            $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
                            $pp = new PlanilhaProjeto();
                            $pa = new PlanilhaAprovacao();
                            $pr = new Projetos();

                            $tipoplanilha = 'SE';
                            $buscarplanilhaPlenaria = $pa->buscarAnaliseCustos($idPronac, $tipoplanilha);

                            $planilhaaprovacao = array();
                            $count = 0;
                            $fonterecurso = null;
                            foreach ($buscarplanilhaPlenaria as $resuplanilha) {
                                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtitemcomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrocorrenciacomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlunitariocomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtdiascomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->VlSugeridoConselheiro;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativaConselheiro;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                                $count++;
                            }

                            $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                            $buscarsomaaprovacao = $pa->somarPlanilhaAprovacao($idPronac, 206, $tipoplanilha);
                            if (isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)) {
                                $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                                $this->view->totalproponentePlenaria = $buscarsomaproposta['soma'];
                            } else {
                                $this->view->totalproponentePlenaria = '0.00';
                            }
                            $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                            $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                            $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
                            $this->view->planilhaPlenaria = $planilhaaprovacao;
                            $this->view->totalcomponentePlenaria = $buscarsomaaprovacao['soma'];
                            $this->view->totalpareceristaPlenaria = $buscarsomaprojeto['soma'];
                        }//feccha if(in_array('analiseprojeto',$arrConteudoImpressao))

                        // === DADOS BANCARIOS e CAPTACAO
                        if (in_array('dadosbancarios', $arrConteudoImpressao)) {
                            $tblContaBancaria = new ContaBancaria();
                            $rsContaBancaria = $tblContaBancaria->contaPorProjeto($idPronac);
                            $this->view->dadosContaBancaria = $rsContaBancaria;

                            $tbLiberacao =   new Liberacao();
                            $rsLiberacao   =   $tbLiberacao->liberacaoPorProjeto($idPronac);
                            $this->view->dadosLiberacao = $rsLiberacao;

                            // === CAPTACAO
                            $tblCaptacao = new Captacao();
                            $rsCount = $tblCaptacao->buscaCompleta(array('idPronac = ?'=>$idPronac), array(), null, null, true);
                            $totalGeralCaptado = $rsCount->totalGeralCaptado;

                            $ordem = array("10 ASC");
                            $rsCaptacao = $tblCaptacao->buscaCompleta(array('idPronac = ?'=>$idPronac), $ordem);

                            $tProjeto = 0;
                            $CgcCPfMecena = 0;
                            $arrRegistros = array();
                            foreach ($rsCaptacao as $captacao) {
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['nome'] = $captacao->Nome;

                                if ($CgcCPfMecena    !=  $captacao->CgcCPfMecena) {
                                    $tIncentivador  =   0;
                                    $qtRegistroI    =   0;
                                    $CgcCPfMecena   =   $captacao->CgcCPfMecena;
                                }

                                $tIncentivador +=  $captacao->CaptacaoReal;
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['totaIncentivador'] = number_format($tIncentivador, 2, ',', '.');
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['TipoApoio']         =   $captacao->TipoApoio;
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['NumeroRecibo']      =   $captacao->NumeroRecibo;
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['DtChegadaRecibo']   =   date('d/m/Y', strtotime($captacao->DtChegadaRecibo));
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['DtRecibo']          =   date('d/m/Y', strtotime($captacao->DtRecibo));
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['CaptacaoReal']      =   number_format($captacao->CaptacaoReal, 2, ',', '.');
                            }

                            $arrRegistros['totalgeral'] = number_format($totalGeralCaptado, 2, ',', '.');

                            $this->view->registrosCaptacao = $arrRegistros;
                        }

                        // === RELATORIOS TRIMESTRAIS
                        if (in_array('relatoriostrimestrais', $arrConteudoImpressao)) {
                            $tbRelatorio = new tbRelatorio();
                            $buscarDivulgacao = RealizarAnaliseProjetoDAO::divulgacaoProjetosGeral($idPronac);
                            $this->view->Divulgacao = $buscarDivulgacao;

                            $projetos = new Projetos();
                            $DadosProjetosProdutos = $projetos->buscarTodosDadosProjetoProdutos($idPronac);
                            $this->view->DadosProjetosProdutos = $DadosProjetosProdutos;

                            $DadosProjetos = $projetos->buscarTodosDadosProjeto($idPronac);
                            $this->view->DadosProjetos = $DadosProjetos;

                            $DadosAnteriores = $tbRelatorio->dadosRelatoriosAnteriores($idPronac);
                            $this->view->DadosAnteriores = $DadosAnteriores;

                            //acessibilidade
                            $AssebilidadeAnterior = $tbRelatorio->dadosAcessoAnteriores($idPronac, 1);
                            $this->view->AssebilidadeAnterior = $AssebilidadeAnterior;

                            //democratizacao
                            $AssebilidadeAnterior = $tbRelatorio->dadosAcessoAnteriores($idPronac, 2);
                            $this->view->DemocratizacaoAnterior = $AssebilidadeAnterior;

                            //comprovante anexados - execucao
                            $tbDocumento = new tbComprovanteExecucao();
                            $tbDocumentoDados = $tbDocumento->buscarDocumentosPronac2($idPronac, "T");
                            $this->view->DocumentosExecucao = $tbDocumentoDados;

                            //dados beneficiarios
                            $result_bn = $tbRelatorio->dadosBeneficiarioAnteriores($idPronac);
                            $this->view->BeneficiarioAnterior = $result_bn;

                            //comprovante anexados - beneficiario
                            $tbDocumento2 = new tbComprovanteBeneficiario();
                            $tbDocumentoDados2 = $tbDocumento2->buscarDocumentosPronac2($idPronac, "T");
                            $this->view->DocumentosBeneficiario = $tbDocumentoDados2;

                            //data liberacao - tbLiberacao
                            $result_lib = $tbRelatorio->dadosRelatorioLiberacao($idPronac)->current();
                            $this->view->RelatorioLiberacao = $result_lib;
                        }

                        // === DADOS DA FISCALIZACAO
                        if (in_array('dadosfiscalizacao', $arrConteudoImpressao)) {
                            $arrRegistros = array();
                            //$this->view->registrosFiscalizacao = $arrRegistros;
                            $tbFiscalizacao = new Fiscalizacao_Model_DbTable_TbFiscalizacao();
                            $arrProjetos = $tbFiscalizacao->projetosFiscalizacaoConsultar(array('Projetos.IdPRONAC = ?' => $idPronac), array('tbFiscalizacao.dtInicioFiscalizacaoProjeto ASC', 'tbFiscalizacao.dtFimFiscalizacaoProjeto ASC'));
                            $arrIdFiscalizacao = array();

                            $projetoDao = new Projetos();
                            $OrgaoFiscalizadorDao = new Fiscalizacao_Model_DbTable_TbOrgaoFiscalizador();
                            $ArquivoFiscalizacaoDao = new Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();
                            $RelatorioFiscalizacaoDAO = new Fiscalizacao_Model_DbTable_TbRelatorioFiscalizacao();

                            foreach ($arrProjetos as $chave => $projeto) {
                                if (isset($projeto->idFiscalizacao) && $projeto->idFiscalizacao!="") {
                                    $this->view->infoProjeto = $tbFiscalizacao->projetosFiscalizacaoConsultar(array('Projetos.IdPRONAC = ?' => $idPronac, 'tbFiscalizacao.idFiscalizacao = ?' => $projeto->idFiscalizacao), array('tbFiscalizacao.dtInicioFiscalizacaoProjeto ASC', 'tbFiscalizacao.dtFimFiscalizacaoProjeto ASC'));
                                    $arrRegistros[$chave]['infoProjeto'] =$this->view->infoProjeto;

                                    if ($projeto->idFiscalizacao) {
                                        $this->view->dadosOrgaos = $OrgaoFiscalizadorDao->dadosOrgaos(array('tbOF.idFiscalizacao = ?' => $projeto->idFiscalizacao));
                                        $arrRegistros[$chave]['dadosOrgaos'] = $this->view->dadosOrgaos;
                                    }
                                    if ($projeto->idFiscalizacao) {
                                        $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $projeto->idFiscalizacao));
                                        $arrRegistros[$chave]['arquivos'] = $this->view->arquivos;
                                    }
                                    $this->view->relatorioFiscalizacao = $RelatorioFiscalizacaoDAO->buscaRelatorioFiscalizacao($projeto->idFiscalizacao);
                                    $arrRegistros[$chave]['relatorioFiscalizacao'] = $this->view->relatorioFiscalizacao;
                                }
                            }
                            $this->view->registrosFiscalizacao = $arrRegistros;
                        }
                    } //FASE 2 e 3

                    // ----------------------------------------------------------------------
                    // ---------------------- FASE 4 - PROJETO ENCERRADO  -------------------
                    // ----------------------------------------------------------------------
                    if ($this->intFaseProjeto == '4') {
                        //RELTORIO FINAL
                        if (in_array('relatoriofinal', $arrConteudoImpressao)) {
                            $this->view->relatorio = array();
                            $this->view->relatorioConsolidado = array();
                            $this->view->beneficiario = array();
                            $this->view->movel = array();
                            $this->view->guiaFNC = array();
                            $this->view->comprovantesExecucao = array();
                            $this->view->imovel = array();
                            $this->view->idAcessoA = array();
                            $this->view->idAcessoB = array();
                            $this->view->idRelatorioConsolidado = array();
                            $this->view->acessibilidade = array();
                            $this->view->democratizacao = array();
                            $this->view->RelatorioConsolidado = array();

                            $tblRelatorio = new tbRelatorio();
                            $rsRelatorio = $tblRelatorio->buscar(array("idPRONAC = ?"=>$idPronac,"tpRelatorio = ?"=>'C',"idAgenteAvaliador > ?"=>0))->current();
                            $this->view->relatorio = $rsRelatorio;

                            $rsRelatorioConsolidado = array();
                            if (isset($rsRelatorio) && count($rsRelatorio) > 0) {
                                $tblRelatorioConsolidado = new tbRelatorioConsolidado();
                                $rsRelatorioConsolidado = $tblRelatorioConsolidado->consultarDados(array("idRelatorio = ?"=>$rsRelatorio->idRelatorio))->current();
                                $this->view->relatorioConsolidado = $rsRelatorioConsolidado;

                                $tblBeneficiario = new tbBeneficiario();
                                $rsBeneficiario = $tblBeneficiario->buscar(array("idRelatorio = ?"=>$rsRelatorio->idRelatorio))->current();
                                $this->view->beneficiario = $rsBeneficiario;

                                if (isset($rsRelatorio->idDistribuicaoProduto) && $rsRelatorio->idDistribuicaoProduto) {
                                    $tblDistribuicaoProduto = new tbDistribuicaoProduto();
                                    $rsDistribuicaoProduto = $tblDistribuicaoProduto->buscarDistribuicaoProduto($rsRelatorio->idDistribuicaoProduto);
                                    $this->view->movel = $rsDistribuicaoProduto;
                                }

                                if (!empty($rsDistribuicaoProduto->current()->idDocumento)) {
                                    $tblDocumento = new tbDocumento();
                                    $rsDocumento = $tblDocumento->buscardocumentosrelatorio($rsDistribuicaoProduto->current()->idDocumento);
                                    $this->view->guiaFNC = $rsDocumento;
                                }

                                //Recuperando dados de tbComprovanteExecucao
                                $tblTbComprovanteExecucao = new tbComprovanteExecucao();
                                $rsTbComprovanteExecucao = $tblTbComprovanteExecucao->buscarDocumentosPronac6($rsRelatorio->idPRONAC, "C");
                                $this->view->comprovantesExecucao = $rsTbComprovanteExecucao;
                            }

                            if (isset($rsRelatorioConsolidado) && count($rsRelatorioConsolidado) > 0) {
                                $tblImovel = new tbImovel();
                                $rsImovel = $tblImovel->buscar(array("idImovel = ?"=>$rsRelatorioConsolidado->idImovel))->current();
                                $this->view->imovel = $rsImovel;
                            }

                            if (isset($rsImovel) && count($rsImovel) > 0) {
                                $tblDocumento = new tbDocumento();
                                $rsDocumentoImovel = $tblDocumento->buscardocumentosrelatorio($rsImovel['idDocumento']);
                                $this->view->ComprovanteCotacao = $rsDocumentoImovel;
                            }

                            $tblAcesso = new Acesso();
                            $rsAcesso = $tblAcesso->consultarAcessoPronac($idPronac, 1);  // Acessibilidade
                            if (isset($rsAcesso[0]->idAcesso)) {
                                $this->view->idAcessoA = $rsAcesso[0]->idAcesso;
                                $rsAcesso2 = $tblAcesso->consultarAcessoPronac($idPronac, 2);  // Democratiza�?o
                                $this->view->idAcessoB = $rsAcesso2[0]->idAcesso;
                            }

                            if (isset($rsAcesso2) && count($rsAcesso2) > 0) {
                                $tbRelConsolidado = new tbRelatorioConsolidado();
                                $rsRel = $tbRelConsolidado->consultarDados2($rsAcesso2[0]->idRelatorioConsolidado);
                                if (is_object($rsRel)) {
                                    $this->view->idRelatorioConsolidado = $rsRel[0]->idRelatorioConsolidado;
                                }

                                $this->view->acessibilidade = $rsAcesso->current();
                                $this->view->democratizacao = $rsAcesso2->current();
                                $this->view->RelatorioConsolidado = $rsRel->current();
                            }
                        }

                        //PRESTACAO DE CONTAS
                        if (in_array('pretacaocontas', $arrConteudoImpressao)) {
                            $this->view->parecerTecnico = array();
                            $this->view->parecerChefe   = array();
                            $this->view->parecerCoordenador = array();
                            $this->view->dadosInabilitado   = array();
                            $this->view->resultadoParecer   = null;
                            $this->view->tipoInabilitacao   = null;

                            //resultado parecer
                            if ($rsProjeto->Situacao == 'E19') {
                                $this->view->resultadoParecer = 'Aprovado Integralmente';
                            }
                            if ($rsProjeto->Situacao == 'E22') {
                                $this->view->resultadoParecer = 'Indeferido';
                            }
                            if ($rsProjeto->Situacao == 'L03') {
                                $this->view->resultadoParecer = 'Aprovado com Ressalvas';
                            }

                            $tbRelatorioTecnico = new tbRelatorioTecnico();
                            $rsParecerTecnico = $tbRelatorioTecnico->buscar(array('IdPRONAC=?'=>$idPronac,'cdGrupo=?'=>124))->current();
                            $rsParecerChefe   = $tbRelatorioTecnico->buscar(array('IdPRONAC=?'=>$idPronac,'cdGrupo=?'=>132))->current();

                            if (is_object($rsParecerTecnico) && is_object($rsParecerChefe)) {
                                $this->view->parecerTecnico = $rsParecerTecnico;
                                $this->view->parecerChefe   = $rsParecerChefe;
                            }

                            $rsParecerCoordenador = $tbRelatorioTecnico->buscar(array('IdPRONAC=?'=>$idPronac,'cdGrupo=?'=>126))->current();
                            $this->view->parecerCoordenador   = $rsParecerCoordenador;

                            $tblInabilitado = new Inabilitado();
                            $rsInabilitado = $tblInabilitado->buscar(array('AnoProjeto+Sequencial=?'=>$pronac))->current();
                            $this->view->dadosInabilitado = $rsInabilitado;

                            if (is_object($rsInabilitado) && isset($rsInabilitado->idTipoInabilitado) && !empty($rsInabilitado->idTipoInabilitado)) {
                                $tbTipoInabilitado =  new tbTipoInabilitado();
                                $rsTipoInabilitado = $tbTipoInabilitado->buscar(array('idTipoInabilitado=?'=>$rsInabilitado->idTipoInabilitado))->current();
                                if (is_object($rsTipoInabilitado)) {
                                    $this->view->tipoInabilitacao = $rsTipoInabilitado->dsTipoInabilitado;
                                }
                            }
                        }
                    } //FASE 4
                }
            } catch (Zend_Exception $e) {
                $url = Zend_Controller_Front::getInstance()->getBaseUrl()."/listarprojetos/listarprojetos";
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->flashMessenger->addMessage("N�o foi poss�vel realizar concluir a opera��o para impress�o do projeto.".$e->getMessage());
                $this->_helper->flashMessengerType->addMessage("ERROR");
                JS::redirecionarURL($url);
                $this->_helper->viewRenderer->setNoRender(true);
                //parent::message("N�o foi poss�vel realizar a opera��o!".$ex->getMessage(), "/manterpropostaincentivofiscal/index?idPreProjeto=" . $idPreProjeto, "ERROR");
            }
        }
    }

    public function imprimirProjetoOLDAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $pagImpressao = $this->_request->getParam("pagImpressao");

        //VERIFICA FASE DO PROJETO
        $this->faseDoProjeto($idPronac);

        if (empty($pagImpressao) || $pagImpressao=='0') {
            $this->view->pagImpressao = 1;
            $pagImpressao = 1;
        } else {
            $this->view->pagImpressao = $pagImpressao;
        }

        $this->view->intFaseProjeto = $this->intFaseProjeto;

        $numPagina = '<b>primeira</b>';
        if ($pagImpressao == '1') {
            $numPagina = '<b>segunda</b>';
        }
        if ($pagImpressao == '2') {
            $numPagina = '<b>terceira</b>';
        }

        if ($this->intFaseProjeto=='0' || $this->intFaseProjeto=='1') {
            $qtdePag = 1;
        }
        $msg = "O retatorio contem 1(uma) p�gina, deseja imprimi-la?";
        if ($this->intFaseProjeto=='2' || $this->intFaseProjeto=='3' || $this->intFaseProjeto=='4') {
            $qtdePag = 3;
        }
        $msg = "O retatorio contem 3(tr�s) p�ginas, deseja imprimir a {$numPagina} pagina?";

        $this->view->msgImpressao =  $msg;
        $this->view->qtdePagImpressao =  $qtdePag;

        if (!empty($idPronac)) {
            //DADOS PRINCIPAIS
            $dados = array();
            $dados['idPronac'] = (int) $idPronac;

            try {
                $rst = ConsultarDadosProjetoDAO::obterDadosProjeto($dados);
                if (count($rst) > 0) {
                    $this->view->projeto = $rst[0];
                    $this->view->idpronac = $_REQUEST['idPronac'];
                    $this->view->idprojeto = $rst[0]->idProjeto;
                    if ($rst[0]->codSituacao == 'E12' || $rst[0]->codSituacao == 'E13' || $rst[0]->codSituacao == 'E15' || $rst[0]->codSituacao == 'E50' || $rst[0]->codSituacao == 'E59' || $rst[0]->codSituacao == 'E61' || $rst[0]->codSituacao == 'E62') {
                        $this->view->menuCompExec = 'true';
                    }

                    $geral = new ProponenteDAO();
                    $tblProjetos = new Projetos();

                    $arrBusca['IdPronac = ?']=$idPronac;
                    $rsProjeto = $tblProjetos->buscar($arrBusca)->current();

                    $idPreProjeto = $rsProjeto->idProjeto;

                    $tbdados = $geral->buscarDadosProponente($idPronac);
                    $this->view->proponente = $tbdados;

                    $this->view->NrProjeto       = $rst[0]->NrProjeto;
                    $this->view->NomeProjeto     = $rst[0]->NomeProjeto;
                    $this->view->NomeProponente  = $tbdados[0]->Nome;

                    $tbemail = $geral->buscarEmail($idPronac);
                    $this->view->email = $tbemail;

                    $tbtelefone = $geral->buscarTelefone($idPronac);
                    $this->view->telefone = $tbtelefone;

                    $tblAgente = new Agente_Model_DbTable_Agentes();
                    $rsAgente = $tblAgente->buscar(array('CNPJCPF=?'=>$tbdados[0]->CgcCpf))->current();

                    $rsDirigentes = $tblAgente->buscarDirigentes(array('v.idVinculoPrincipal =?'=>$rsAgente->idAgente));
                    //$tbDirigentes = $geral->buscarDirigentes($idPronac);
                    $this->view->dirigentes = $rsDirigentes;

                    $this->view->CgcCpf = $tbdados[0]->CgcCpf;
                    $this->view->itensGeral = array();
                    $this->view->proposta   = array();

                    if (!empty($idPreProjeto)) {
                        //OUTROS DADOS PROPONENTE
                        $this->view->itensGeral = Proposta_Model_AnalisarPropostaDAO::buscarGeral($idPreProjeto);

                        //DADOS COMPLEMENTARES
                        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
                        $rsProposta = $tblProposta->buscar(array('idPreProjeto=?'=>$idPreProjeto))->current();
                        $this->view->proposta = $rsProposta;
                    }

                    if ($pagImpressao == '1') {
                        //PLANO DE DISTRIBUICAO
                        $buscarDistribuicao = RealizarAnaliseProjetoDAO::planodedistribuicao($idPronac);
                        $this->view->distribuicao = $buscarDistribuicao;

                        //LOCAL DE REALIZACAO
                        $buscarLocalRealizacao = RealizarAnaliseProjetoDAO::localrealizacao($idPronac);
                        $this->view->dadosLocalizacao = $buscarLocalRealizacao;

                        //DESLOCAMENTO
                        $buscarDeslocamento = RealizarAnaliseProjetoDAO::deslocamento($idPronac);
                        $this->view->dadosDeslocamento = $buscarDeslocamento;

                        //DIVULGACAO
                        $buscarDivulgacao = RealizarAnaliseProjetoDAO::divulgacao($idPronac);
                        $this->view->divulgacao = $buscarDivulgacao;

                        //PLANILHA ORCAMENTARIA
                        $this->view->itensPlanilhaOrcamentaria = array();

                        $tblProjetos = new Projetos();

                        if (!empty($idPreProjeto)) {
                            $this->view->itensPlanilhaOrcamentaria  = Proposta_Model_AnalisarPropostaDAO::buscarPlanilhaOrcamentaria($idPreProjeto);

                            $buscarProduto = ManterorcamentoDAO::buscarProdutos($idPreProjeto);
                            $this->view->Produtos = $buscarProduto;

                            $buscarEtapa = ManterorcamentoDAO::buscarEtapasProdutos($idPreProjeto);
                            $this->view->Etapa = $buscarEtapa;

                            $buscarItem = ManterorcamentoDAO::buscarItensProdutos($idPreProjeto);
                            $this->view->Item = $buscarItem;

                            $this->view->AnaliseCustos = PreProjeto::analiseDeCustos($idPreProjeto);
                        }

                        //DOCUMENTOS ANEXADOS
                        $idAgente = null;

                        $tblAgente = new Agente_Model_DbTable_Agentes();
                        $rsAgente = $tblAgente->buscar(array('CNPJCPF = ?'=>$rsProjeto->CgcCpf));
                        if ($rsAgente->count() > 0) {
                            $idAgente = $rsAgente[0]->idAgente;
                        }
                        if (count($rsProjeto) > 0 && !empty($idAgente)) {
                            $ordem = array();
                            $ordem = array("3 DESC");
                            //if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }
                            $tbDoc = new Proposta_Model_DbTable_TbDocumentosAgentes();
                            $rsDocs = $tbDoc->buscatodosdocumentos($idAgente, $rsProjeto->idProjeto, $rsProjeto->IdPRONAC);
                            $this->view->registrosDocAnexados = $rsDocs;
                        }

                        //DILIGENCIAS
                        $tblPreProjeto      = new Proposta_Model_DbTable_PreProjeto();
                        if (!empty($idPreProjeto)) {
                            $this->view->diligenciasProposta = $tblPreProjeto->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $idPreProjeto,'aval.ConformidadeOK = ? '=>0));
                        }
                        $this->view->diligenciasProjeto = $tblProjetos->listarDiligencias(array('pro.IdPRONAC = ?' => $idPronac));

                        //TRAMITACAO DE PROJETO
                        $ordem = array();
                        $ordem = array("2 ASC");
                        $tblHistDoc = new tbHistoricoDocumento();
                        $rsHistDoc = $tblHistDoc->buscarHistoricoTramitacaoProjeto(array("p.IdPronac =?"=>$idPronac), $ordem);
                        $this->view->registrosHisTramProjeto = $rsHistDoc;

                        //TRAMITACAO DE DOCUMENTOS
                        $arrBusca = array();
                        $arrBusca['h.idDocumento <> ?']=0;
                        $arrBusca['h.stEstado = ?']=1;
                        $arrBusca['p.IdPronac =?']=$idPronac;
                        $ordem = array();
                        $ordem = array("2 ASC");
                        $rsHistDoc = $tblHistDoc->buscarHistoricoTramitacaoDocumento($arrBusca, $ordem);
                        $this->view->registrosHisTramDoc = $rsHistDoc;

                        //PROVIDENCIA TOMADA
                        $tblProjeto = new Projetos();
                        $rsProjeto = $tblProjeto->buscar(array("IdPronac=?"=>$idPronac))->current();
                        $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;

                        $ordem = array();
                        $ordem = array("4 ASC");
                        $tblHisSituacao = new HistoricoSituacao();
                        $rsHisSituacao = $tblHisSituacao->buscar(array('AnoProjeto+Sequencial = ?'=>$pronac), $ordem);
                        $this->view->registrosProvTomada = $rsHisSituacao;
                    } //fecha pagImpressao 1

                    // ----------------------------------------------------------------------
                    // ---------------------- FASE 2 - EXECUAO DO PROJETO -------------------
                    // ----------------------------------------------------------------------
                    if ($this->intFaseProjeto == '2' || $this->intFaseProjeto == '3') {
                        if ($pagImpressao == '2') {
                            //RECURSOS
                            $buscarProjetos = $tblProjetos->buscarProjetosSolicitacaoRecurso($idPronac);

                            // busca as solicita��es de recurso do projeto
                            $this->tbRecurso         = new tbRecurso();
                            $buscarRecursos          = $this->tbRecurso->buscarDados($idPronac);
                            $buscarRecursosPlanilha  = $this->tbRecurso->buscarDados($idPronac); // necess�rio chamar o mesmo m�todo para jogar na vis�o sem erros

                            // manda os dados para a vis�o
                            $this->view->projetosRecurso  = $buscarProjetos;
                            $this->view->recursos         = $buscarRecursos;
                            $this->view->recursosPlanilha = $buscarRecursosPlanilha;

                            //APROVACAO
                            $rsProjeto = $tblProjetos->buscar(array("IdPronac=?"=>$idPronac))->current();
                            $pronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;

                            $tblAprovacao = new Aprovacao();
                            $rsAprovacao = $tblAprovacao->buscaCompleta(array('a.AnoProjeto + a.Sequencial = ?'=>$pronac), array('a.idAprovacao ASC'));
                            $this->view->dadosAprovacao = $rsAprovacao;

                            // =================================== ANALISE DO PROJETO =====================================

                            // === INICIAL == PARECER CONSOLIDADO
                            $this->view->resultAnaliseProjeto = array();
                            $this->view->resultAnaliseProjetoCNIC = array();
                            $this->view->resultAnaliseProjetoPlenaria = array();
                            $this->view->fontesincentivo  = 0;
                            $this->view->outrasfontes     = 0;
                            $this->view->valorproposta    = 0;
                            $this->view->valorparecerista = 0;
                            $this->view->valorcomponente  = 0;
                            $this->view->enquadramento = 'N&atilde;o Enquadrado';

                            $parecer = new Parecer_Model_DbTable_Parecer();
                            $analiseparecer = $parecer->buscarParecer(array(1), $idPronac)->current();
                            if (is_object($analiseparecer)) {
                                $this->view->resultAnaliseProjeto = $analiseparecer->toArray();
                            }

                            $projeto = new Projetos();
                            $buscarPronac = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
                            $idprojeto = $buscarPronac['idProjeto'];

                            $this->view->resultAnaliseProduto = GerenciarPareceresDAO::projetosConsolidadosParte2($idPronac);

                            $planilhaprojeto = new PlanilhaProjeto();
                            $parecerista = $planilhaprojeto->somarPlanilhaProjeto($idPronac);
                            $this->view->valorparecerista = $parecerista['soma'];

                            if (!empty($idprojeto)) {
                                $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                                $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                                $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
                                $this->view->fontesincentivo  = $fonteincentivo['soma'];
                                $this->view->outrasfontes     = $outrasfontes['soma'];
                                $this->view->valorproposta    = $fonteincentivo['soma'] + $outrasfontes['soma'];
                            }

                            $tbEnquadramento    = new Admissibilidade_Model_Enquadramento();
                            $verificaEnquadramento = $tbEnquadramento->buscarDados($idPronac, null, false);

                            if (is_object($verificaEnquadramento) && count($verificaEnquadramento) > 0) {
                                if ($verificaEnquadramento->Enquadramento == '2') {
                                    $this->view->enquadramento = 'Artigo 18';
                                } elseif ($verificaEnquadramento->Enquadramento == '1') {
                                    $this->view->enquadramento = 'Artigo 26';
                                } else {
                                    $this->view->enquadramento = 'N&atilde;o Enquadrado';
                                }
                            } else {
                                $this->view->enquadramento = 'N&atilde;o Enquadrado';
                            }

                            // === INICIAL == ANALISE DE CONTEUDO
                            $this->view->dadosAnaliseInicial = GerenciarPareceresDAO::pareceresTecnicos($idPronac);

                            // === INICIAL == ANALISE DE CUSTO
                            $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
                            $pp = new PlanilhaProjeto();
                            $pr = new Projetos();
                            $PlanilhaDAO = new PlanilhaProjeto();
                            $where = array('PPJ.IdPRONAC = ?' => $idPronac);
                            $buscarplanilha = $PlanilhaDAO->buscarAnaliseCustos($where);

                            $planilhaprojeto = array();
                            $count = 0;
                            $fonterecurso = null;
                            foreach ($buscarplanilha as $resuplanilha) {
                                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaProjeto'] = $resuplanilha->idPlanilhaProjeto;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $planilhaprojeto[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $count++;
                            }
                            $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                            if (isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)) {
                                $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                                $this->view->totalproponenteInicial = $buscarsomaproposta['soma'];
                            } else {
                                $this->view->totalproponenteInicial = '0.00';
                            }
                            $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                            $this->view->planilhaInicial = $planilhaprojeto;
                            $this->view->totalpareceristaInicial = $buscarsomaprojeto['soma'];

                            // === CNIC == PARECER CONSOLIDADO
                            $parecer = new Parecer_Model_DbTable_Parecer();
                            $analiseparecer = $parecer->buscarParecer(array(6), $idPronac)->current();
                            if (is_object($analiseparecer)) {
                                $this->view->resultAnaliseProjetoCNIC = $analiseparecer->toArray();
                            }

                            $projeto = new Projetos();
                            $buscarPronac = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
                            $idprojeto = $buscarPronac['idProjeto'];

                            $tpPlanilha = 'CO';
                            $analiseaprovacao = new AnaliseAprovacao();
                            $produtos = $analiseaprovacao->buscarAnaliseProduto($tpPlanilha, $idPronac);
                            $this->view->resultAnaliseProdutoCNIC = $produtos;

                            $planilhaAprovacao = new PlanilhaAprovacao();
                            $valor = $planilhaAprovacao->somarPlanilhaAprovacao($idPronac, 206, $tpPlanilha);
                            $this->view->valorcomponenteCNIC  = $valor['soma'];

                            if (!empty($idprojeto)) {
                                $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                                $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                                $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
                                $this->view->fontesincentivoCNIC  = $fonteincentivo['soma'];
                                $this->view->outrasfontesCNIC     = $outrasfontes['soma'];
                                $this->view->valorpropostaCNIC    = $fonteincentivo['soma'] + $outrasfontes['soma'];
                            }

                            $verificaEnquadramento = RealizarAnaliseProjetoDAO::verificaEnquadramento($idPronac, $tpPlanilha);

                            if (count($verificaEnquadramento) > 0) {
                                if ($verificaEnquadramento[0]->stArtigo18 == true) {
                                    $this->view->enquadramentoCNIC = 'Artigo 18';
                                } elseif ($verificaEnquadramento[0]->stArtigo26 == true) {
                                    $this->view->enquadramentoCNIC = 'Artigo 26';
                                } else {
                                    $this->view->enquadramentoCNIC = 'N&atilde;o Enquadrado';
                                }
                            } else {
                                $this->view->enquadramentoCNIC = 'N&atilde;o Enquadrado';
                            }

                            // === CNIC == ANALISE DE CONTEUDO
                            $analise = new AnaliseAprovacao();
                            $this->view->dadosAnaliseCnic = $analise->buscarAnaliseProduto('CO', $idPronac, array('PDP.stPrincipal DESC'));

                            // === CNIC == ANALISE DE CUSTO
                            $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
                            $pp = new PlanilhaProjeto();
                            $pa = new PlanilhaAprovacao();
                            $pr = new Projetos();

                            $tipoplanilha = 'CO';
                            $buscarplanilhaCNIC = $pa->buscarAnaliseCustos($idPronac, $tipoplanilha);

                            $planilhaaprovacao = array();
                            $count = 0;
                            $fonterecurso = null;
                            foreach ($buscarplanilhaCNIC as $resuplanilha) {
                                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtitemcomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrocorrenciacomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlunitariocomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtdiascomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->VlSugeridoConselheiro;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativaConselheiro;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                                $count++;
                            }
                            $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                            $buscarsomaaprovacao = $pa->somarPlanilhaAprovacao($idPronac, 206, $tipoplanilha);
                            if (isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)) {
                                $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                                $this->view->totalproponenteCNIC = $buscarsomaproposta['soma'];
                            } else {
                                $this->view->totalproponenteCNIC = '0.00';
                            }
                            $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                            $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                            $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
                            $this->view->planilhaCNIC = $planilhaaprovacao;
                            $this->view->totalcomponenteCNIC = $buscarsomaaprovacao['soma'];
                            $this->view->totalpareceristaCNIC = $buscarsomaprojeto['soma'];

                            // === PLENARIA == PARECER CONSOLIDADO
                            $parecer = new Parecer_Model_DbTable_Parecer();
                            $analiseparecer = $parecer->buscarParecer(array(10), $idPronac)->current();
                            if (is_object($analiseparecer)) {
                                $this->view->resultAnaliseProjetoPlenaria = $analiseparecer->toArray();
                            }

                            $projeto = new Projetos();
                            $buscarPronac = $projeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current()->toArray();
                            $idprojeto = $buscarPronac['idProjeto'];

                            $tpPlanilha = 'SE';
                            $analiseaprovacao = new AnaliseAprovacao();
                            $produtos = $analiseaprovacao->buscarAnaliseProduto($tpPlanilha, $idPronac);
                            $this->view->resultAnaliseProdutoPlenaria = $produtos;

                            $planilhaAprovacao = new PlanilhaAprovacao();
                            $valor = $planilhaAprovacao->somarPlanilhaAprovacao($idPronac, 206, $tpPlanilha);
                            $this->view->valorcomponentePlenaria  = $valor['soma'];

                            if (!empty($idprojeto)) {
                                $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                                $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                                $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
                                $this->view->fontesincentivoPlenaria  = $fonteincentivo['soma'];
                                $this->view->outrasfontesPlenaria     = $outrasfontes['soma'];
                                $this->view->valorpropostaPlenaria    = $fonteincentivo['soma'] + $outrasfontes['soma'];
                            }

                            $verificaEnquadramento = RealizarAnaliseProjetoDAO::verificaEnquadramento($idPronac, $tpPlanilha);

                            if (count($verificaEnquadramento) > 0) {
                                if ($verificaEnquadramento[0]->stArtigo18 == true) {
                                    $this->view->enquadramentoPlenaria = 'Artigo 18';
                                } elseif ($verificaEnquadramento[0]->stArtigo26 == true) {
                                    $this->view->enquadramentoPlenaria = 'Artigo 26';
                                } else {
                                    $this->view->enquadramentoPlenaria = 'N&atilde;o Enquadrado';
                                }
                            } else {
                                $this->view->enquadramentoPlenaria = 'N&atilde;o Enquadrado';
                            }

                            // === PLENARIA == ANALISE DE CONTEUDO
                            $analise = new AnaliseAprovacao();
                            $this->view->dadosAnalisePlenaria = $analise->buscarAnaliseProduto('SE', $idPronac, array('PDP.stPrincipal DESC'));

                            // === PLENARIA == ANALISE DE CUSTO
                            $ppr = new Proposta_Model_DbTable_TbPlanilhaProposta();
                            $pp = new PlanilhaProjeto();
                            $pa = new PlanilhaAprovacao();
                            $pr = new Projetos();

                            $tipoplanilha = 'SE';
                            $buscarplanilhaPlenaria = $pa->buscarAnaliseCustos($idPronac, $tipoplanilha);

                            $planilhaaprovacao = array();
                            $count = 0;
                            $fonterecurso = null;
                            foreach ($buscarplanilhaPlenaria as $resuplanilha) {
                                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtitemcomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrocorrenciacomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlunitariocomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtdiascomp;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->VlSugeridoConselheiro;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativaConselheiro;
                                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                                $count++;
                            }

                            $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
                            $buscarsomaaprovacao = $pa->somarPlanilhaAprovacao($idPronac, 206, $tipoplanilha);
                            if (isset($buscarprojeto->idProjeto) && !empty($buscarprojeto->idProjeto)) {
                                $buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto->idProjeto);
                                $this->view->totalproponentePlenaria = $buscarsomaproposta['soma'];
                            } else {
                                $this->view->totalproponentePlenaria = '0.00';
                            }
                            $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                            $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
                            $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
                            $this->view->planilhaPlenaria = $planilhaaprovacao;
                            $this->view->totalcomponentePlenaria = $buscarsomaaprovacao['soma'];
                            $this->view->totalpareceristaPlenaria = $buscarsomaprojeto['soma'];

                            // === DADOS BANCARIOS
                            $tblContaBancaria = new ContaBancaria();
                            $rsContaBancaria = $tblContaBancaria->contaPorProjeto($idPronac);
                            $this->view->dadosContaBancaria = $rsContaBancaria;

                            $tbLiberacao =   new Liberacao();
                            $rsLiberacao   =   $tbLiberacao->liberacaoPorProjeto($idPronac);
                            $this->view->dadosLiberacao = $rsLiberacao;

                            // === CAPTACAO
                            $tblCaptacao = new Captacao();
                            $rsCount = $tblCaptacao->buscaCompleta(array('idPronac = ?'=>$idPronac), array(), null, null, true);
                            $totalGeralCaptado = $rsCount->totalGeralCaptado;

                            $ordem = array("10 ASC");
                            $rsCaptacao = $tblCaptacao->buscaCompleta(array('idPronac = ?'=>$idPronac), $ordem);

                            $tProjeto = 0;
                            $CgcCPfMecena = 0;
                            $arrRegistros = array();
                            foreach ($rsCaptacao as $captacao) {
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['nome'] = $captacao->Nome;

                                if ($CgcCPfMecena    !=  $captacao->CgcCPfMecena) {
                                    $tIncentivador  =   0;
                                    $qtRegistroI    =   0;
                                    $CgcCPfMecena   =   $captacao->CgcCPfMecena;
                                }

                                $tIncentivador +=  $captacao->CaptacaoReal;
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['totaIncentivador'] = number_format($tIncentivador, 2, ',', '.');
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['TipoApoio']         =   $captacao->TipoApoio;
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['NumeroRecibo']      =   $captacao->NumeroRecibo;
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['DtChegadaRecibo']   =   date('d/m/Y', strtotime($captacao->DtChegadaRecibo));
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['DtRecibo']          =   date('d/m/Y', strtotime($captacao->DtRecibo));
                                $arrRegistros['incentivador'][$captacao->CgcCPfMecena]['recibo'][$captacao->NumeroRecibo]['CaptacaoReal']      =   number_format($captacao->CaptacaoReal, 2, ',', '.');
                            }

                            $arrRegistros['totalgeral'] = number_format($totalGeralCaptado, 2, ',', '.');

                            $this->view->registrosCaptacao = $arrRegistros;

                            // === RELATORIOS TRIMESTRAIS
                            $tbRelatorio = new tbRelatorio();
                            $buscarDivulgacao = RealizarAnaliseProjetoDAO::divulgacaoProjetosGeral($idPronac);
                            $this->view->Divulgacao = $buscarDivulgacao;

                            $projetos = new Projetos();
                            $DadosProjetosProdutos = $projetos->buscarTodosDadosProjetoProdutos($idPronac);
                            $this->view->DadosProjetosProdutos = $DadosProjetosProdutos;

                            $DadosProjetos = $projetos->buscarTodosDadosProjeto($idPronac);
                            $this->view->DadosProjetos = $DadosProjetos;

                            $DadosAnteriores = $tbRelatorio->dadosRelatoriosAnteriores($idPronac);
                            $this->view->DadosAnteriores = $DadosAnteriores;

                            //ACESSIBILIDADE
                            $AssebilidadeAnterior = $tbRelatorio->dadosAcessoAnteriores($idPronac, 1);
                            $this->view->AssebilidadeAnterior = $AssebilidadeAnterior;

                            //DEMOCRATIZACAO
                            $AssebilidadeAnterior = $tbRelatorio->dadosAcessoAnteriores($idPronac, 2);
                            $this->view->DemocratizacaoAnterior = $AssebilidadeAnterior;

                            //COMPROVANTES ANEXADOS - EXECUCAO
                            $tbDocumento = new tbComprovanteExecucao();
                            $tbDocumentoDados = $tbDocumento->buscarDocumentosPronac2($idPronac, "T");
                            $this->view->DocumentosExecucao = $tbDocumentoDados;

                            //DADOS BENEFICI�RIO
                            $result_bn = $tbRelatorio->dadosBeneficiarioAnteriores($idPronac);
                            $this->view->BeneficiarioAnterior = $result_bn;

                            //COMPROVANTES ANEXADOS - BENEFICIARIO
                            $tbDocumento2 = new tbComprovanteBeneficiario();
                            $tbDocumentoDados2 = $tbDocumento2->buscarDocumentosPronac2($idPronac, "T");
                            $this->view->DocumentosBeneficiario = $tbDocumentoDados2;

                            //DATA DE LIBERACAO - tbLiberacao
                            $result_lib = $tbRelatorio->dadosRelatorioLiberacao($idPronac)->current();
                            $this->view->RelatorioLiberacao = $result_lib;
                        } //fecha pagImpressao 2

                        if ($pagImpressao == '3') {
                            // === DADOS DA FISCALIZACAO
                            $arrRegistros = array();
                            //$this->view->registrosFiscalizacao = $arrRegistros;
                            $tbFiscalizacao = new Fiscalizacao_Model_DbTable_TbFiscalizacao();
                            $arrProjetos = $tbFiscalizacao->projetosFiscalizacaoConsultar(array('Projetos.IdPRONAC = ?' => $idPronac), array('tbFiscalizacao.dtInicioFiscalizacaoProjeto ASC', 'tbFiscalizacao.dtFimFiscalizacaoProjeto ASC'));
                            $arrIdFiscalizacao = array();

                            $OrgaoFiscalizadorDao = new Fiscalizacao_Model_DbTable_TbOrgaoFiscalizador();
                            $ArquivoFiscalizacaoDao = new Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();
                            $RelatorioFiscalizacaoDAO = new Fiscalizacao_Model_DbTable_TbRelatorioFiscalizacao();

                            foreach ($arrProjetos as $chave => $projeto) {
                                if (isset($projeto->idFiscalizacao) && $projeto->idFiscalizacao!="") {
                                    $this->view->infoProjeto = $tbFiscalizacao->projetosFiscalizacaoConsultar(array('Projetos.IdPRONAC = ?' => $idPronac, 'tbFiscalizacao.idFiscalizacao = ?' => $projeto->idFiscalizacao), array('tbFiscalizacao.dtInicioFiscalizacaoProjeto ASC', 'tbFiscalizacao.dtFimFiscalizacaoProjeto ASC'));
                                    $arrRegistros[$chave]['infoProjeto'] =$this->view->infoProjeto;

                                    if ($projeto->idFiscalizacao) {
                                        $this->view->dadosOrgaos = $OrgaoFiscalizadorDao->dadosOrgaos(array('tbOF.idFiscalizacao = ?' => $projeto->idFiscalizacao));
                                        $arrRegistros[$chave]['dadosOrgaos'] = $this->view->dadosOrgaos;
                                    }
                                    if ($projeto->idFiscalizacao) {
                                        $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $projeto->idFiscalizacao));
                                        $arrRegistros[$chave]['arquivos'] = $this->view->arquivos;
                                    }
                                    $this->view->relatorioFiscalizacao = $RelatorioFiscalizacaoDAO->buscaRelatorioFiscalizacao($projeto->idFiscalizacao);
                                    $arrRegistros[$chave]['relatorioFiscalizacao'] = $this->view->relatorioFiscalizacao;
                                }
                            }
                            $this->view->registrosFiscalizacao = $arrRegistros;
                        } //FASE 2 e 3

                        // ----------------------------------------------------------------------
                        // ---------------------- FASE 4 - PROJETO ENCERRADO  -------------------
                        // ----------------------------------------------------------------------
                        if ($this->intFaseProjeto == '4') {

                            //RELTORIOS FINAIS
                            $this->view->relatorio = array();
                            $this->view->relatorioConsolidado = array();
                            $this->view->beneficiario = array();
                            $this->view->movel = array();
                            $this->view->guiaFNC = array();
                            $this->view->comprovantesExecucao = array();
                            $this->view->imovel = array();
                            $this->view->idAcessoA = array();
                            $this->view->idAcessoB = array();
                            $this->view->idRelatorioConsolidado = array();
                            $this->view->acessibilidade = array();
                            $this->view->democratizacao = array();
                            $this->view->RelatorioConsolidado = array();

                            $tblRelatorio = new tbRelatorio();
                            $rsRelatorio = $tblRelatorio->buscar(array("idPRONAC = ?"=>$idPronac,"tpRelatorio = ?"=>'C',"idAgenteAvaliador > ?"=>0))->current();
                            $this->view->relatorio = $rsRelatorio;

                            $rsRelatorioConsolidado = array();
                            if (isset($rsRelatorio) && count($rsRelatorio) > 0) {
                                $tblRelatorioConsolidado = new tbRelatorioConsolidado();
                                $rsRelatorioConsolidado = $tblRelatorioConsolidado->consultarDados(array("idRelatorio = ?"=>$rsRelatorio->idRelatorio))->current();
                                $this->view->relatorioConsolidado = $rsRelatorioConsolidado;

                                $tblBeneficiario = new tbBeneficiario();
                                $rsBeneficiario = $tblBeneficiario->buscar(array("idRelatorio = ?"=>$rsRelatorio->idRelatorio))->current();
                                $this->view->beneficiario = $rsBeneficiario;

                                if (isset($rsRelatorio->idDistribuicaoProduto) && $rsRelatorio->idDistribuicaoProduto) {
                                    $tblDistribuicaoProduto = new tbDistribuicaoProduto();
                                    $rsDistribuicaoProduto = $tblDistribuicaoProduto->buscarDistribuicaoProduto($rsRelatorio->idDistribuicaoProduto);
                                    $this->view->movel = $rsDistribuicaoProduto;
                                }

                                if (!empty($rsDistribuicaoProduto->current()->idDocumento)) {
                                    $tblDocumento = new tbDocumento();
                                    $rsDocumento = $tblDocumento->buscardocumentosrelatorio($rsDistribuicaoProduto->current()->idDocumento);
                                    $this->view->guiaFNC = $rsDocumento;
                                }

                                //Recuperando dados de tbComprovanteExecucao
                                $tblTbComprovanteExecucao = new tbComprovanteExecucao();
                                $rsTbComprovanteExecucao = $tblTbComprovanteExecucao->buscarDocumentosPronac6($rsRelatorio->idPRONAC, "C");
                                $this->view->comprovantesExecucao = $rsTbComprovanteExecucao;
                            }

                            if (isset($rsRelatorioConsolidado) && count($rsRelatorioConsolidado) > 0) {
                                $tblImovel = new tbImovel();
                                $rsImovel = $tblImovel->buscar(array("idImovel = ?"=>$rsRelatorioConsolidado->idImovel))->current();
                                $this->view->imovel = $rsImovel;
                            }

                            if (isset($rsImovel) && count($rsImovel) > 0) {
                                $tblDocumento = new tbDocumento();
                                $rsDocumentoImovel = $tblDocumento->buscardocumentosrelatorio($rsImovel['idDocumento']);
                                $this->view->ComprovanteCotacao = $rsDocumentoImovel;
                            }

                            $tblAcesso = new Acesso();
                            $rsAcesso = $tblAcesso->consultarAcessoPronac($idPronac, 1);  // Acessibilidade
                            if (isset($rsAcesso[0]->idAcesso)) {
                                $this->view->idAcessoA = $rsAcesso[0]->idAcesso;
                                $rsAcesso2 = $tblAcesso->consultarAcessoPronac($idPronac, 2);  // Democratizacao
                                $this->view->idAcessoB = $rsAcesso2[0]->idAcesso;
                            }

                            if (isset($rsAcesso2) && count($rsAcesso2) > 0) {
                                $tbRelConsolidado = new tbRelatorioConsolidado();
                                $rsRel = $tbRelConsolidado->consultarDados2($rsAcesso2[0]->idRelatorioConsolidado);
                                if (is_object($rsRel)) {
                                    $this->view->idRelatorioConsolidado = $rsRel[0]->idRelatorioConsolidado;
                                }

                                $this->view->acessibilidade = $rsAcesso->current();
                                $this->view->democratizacao = $rsAcesso2->current();
                                $this->view->RelatorioConsolidado = $rsRel->current();
                            }

                            //PRESTACAO DE CONTAS
                            $this->view->parecerTecnico = array();
                            $this->view->parecerChefe   = array();
                            $this->view->parecerCoordenador = array();
                            $this->view->dadosInabilitado   = array();
                            $this->view->resultadoParecer   = null;
                            $this->view->tipoInabilitacao   = null;

                            //resultado parecer
                            if ($rsProjeto->Situacao == 'E19') {
                                $this->view->resultadoParecer = 'Aprovado Integralmente';
                            }
                            if ($rsProjeto->Situacao == 'E22') {
                                $this->view->resultadoParecer = 'Indeferido';
                            }
                            if ($rsProjeto->Situacao == 'L03') {
                                $this->view->resultadoParecer = 'Aprovado com Ressalvas';
                            }

                            $tbRelatorioTecnico = new tbRelatorioTecnico();
                            $rsParecerTecnico = $tbRelatorioTecnico->buscar(array('IdPRONAC=?'=>$idPronac,'cdGrupo=?'=>124))->current();
                            $rsParecerChefe   = $tbRelatorioTecnico->buscar(array('IdPRONAC=?'=>$idPronac,'cdGrupo=?'=>132))->current();

                            if (is_object($rsParecerTecnico) && is_object($rsParecerChefe)) {
                                $this->view->parecerTecnico = $rsParecerTecnico;
                                $this->view->parecerChefe   = $rsParecerChefe;
                            }

                            $rsParecerCoordenador = $tbRelatorioTecnico->buscar(array('IdPRONAC=?'=>$idPronac,'cdGrupo=?'=>126))->current();
                            $this->view->parecerCoordenador   = $rsParecerCoordenador;

                            $tblInabilitado = new Inabilitado();
                            $rsInabilitado = $tblInabilitado->buscar(array('AnoProjeto+Sequencial=?'=>$pronac))->current();
                            $this->view->dadosInabilitado = $rsInabilitado;

                            if (is_object($rsInabilitado) && isset($rsInabilitado->idTipoInabilitado) && !empty($rsInabilitado->idTipoInabilitado)) {
                                $tbTipoInabilitado =  new tbTipoInabilitado();
                                $rsTipoInabilitado = $tbTipoInabilitado->buscar(array('idTipoInabilitado=?'=>$rsInabilitado->idTipoInabilitado))->current();
                                if (is_object($rsTipoInabilitado)) {
                                    $this->view->tipoInabilitacao = $rsTipoInabilitado->dsTipoInabilitado;
                                }
                            }
                        } //FASE 4
                    } //fecha pagImpressao 3
                }
            } catch (Zend_Exception $e) {
                $url = Zend_Controller_Front::getInstance()->getBaseUrl()."/listarprojetos/listarprojetos";
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->flashMessenger->addMessage("N�o foi poss�vel realizar concluir a opera��o para impress�o do projeto.".$e->getMessage());
                $this->_helper->flashMessengerType->addMessage("ERROR");
                JS::redirecionarURL($url);
                $this->_helper->viewRenderer->setNoRender(true);
                //parent::message("N�o foi poss�vel realizar a opera��o!".$ex->getMessage(), "/manterpropostaincentivofiscal/index?idPreProjeto=" . $idPreProjeto, "ERROR");
            }
        }
    }

    public function marcasAnexadasAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idpronac = $this->_request->getParam("idPronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $Projetos = new Projetos();
        $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $dadosProj;
        $pronac = $dadosProj->AnoProjeto.$dadosProj->Sequencial;

        $tbArquivoImagem = new tbArquivoImagem();
        $marcas = $tbArquivoImagem->marcasAnexadas($pronac);
        $this->view->Marcas = $marcas;
        $this->view->pronac = $pronac;
    }

    public function abrirAction()
    {
        // recebe o id do arquivo via get
        $get = Zend_Registry::get('get');
        $id = (int) isset($get->id) ? $get->id : $this->_request->getParam('id');

        // Configura��o o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        // busca o arquivo
        $resultado = UploadDAO::abrir($id);

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
            die("N&atilde;o existe o arquivo especificado");
            $this->view->message = 'N�o foi poss�vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // l� os cabe�alhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $this->getResponse()
                        ->setHeader('Content-Type', $r->dsTipoPadronizado)
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                        //->setHeader("Connection", "close")
                        //->setHeader("Content-transfer-encoding", "binary")
                        //->setHeader("Cache-control", "private")
                        ->setBody($r->biArquivo);
            } // fecha foreach
        } // fecha else
    }



    public function pedidoProrrogacaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idpronac = $this->_request->getParam("idPronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        //****** Lista de Prorroga��es *****//
        $prorrogacao = new Prorrogacao();
        $DadosProrrogacoes = $prorrogacao->buscarProrrogacoes($idpronac);
        $this->view->DadosProrrogacoes = $DadosProrrogacoes;
    }

    public function formImprimirProjetoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $Projetos = new Projetos();
        $p = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();
        $this->view->Situacao = $p->Situacao;
    }
    public function imprimirAction()
    {
        $this->_helper->layout->disableLayout();// Desabilita o Zend Layout
        $html = $this->_request->getParam("html");
        $this->view->html = $html;
    }
    /**
    * M�todo formatarReal()
    * Converte para o formato Brasileiro
    * @param moeda
    * @return String
    */
    public function formatarReal($moeda)
    {
        if (!empty($moeda)) {
            $moeda = number_format($moeda, 2, ',', '.');
            return "R$ " . $moeda;
        } else {
            return "";
        }
    }

    /**
     *
     */
    public function devolucoesDoIncentivadorAction()
    {
        $this->view->itemMenu = 'devolucoes-do-incentivador';

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();
        $this->view->idPronac = $idPronac;
        # aportes
        $whereData = array('idPronac = ?' => $idPronac);
        if ($this->getRequest()->getParam('dtDevolucaoInicio')) {
            $whereData['dtLote >= ?'] = ConverteData($this->getRequest()->getParam('dtDevolucaoInicio'), 13);
        }
        if ($this->getRequest()->getParam('dtDevolucaoFim')) {
            $whereData['dtLote <= ?'] = ConverteData($this->getRequest()->getParam('dtDevolucaoFim'), 13);
        }

        $aporteModel = new tbAporteCaptacao();
        $this->view->dados = $aporteModel->pesquisarDevolucoesIncentivador($whereData);
        $this->view->dataDevolucaoInicio = $this->getRequest()->getParam('dtDevolucaoInicio');
        $this->view->dataDevolucaoFim = $this->getRequest()->getParam('dtDevolucaoFim');
    }
}
