<?php

class GerartermodeaprovacaoController extends MinC_Controller_Action_Abstract
{
    private $codGrupo = null;
    private $codOrgao = null;
    private $codOrgaoSuperior = null;
    private $tipoProjeto = null;
    private $nomeSecretario = null;
    private $nomeOrgao = null;
    private $arrCodOrgaosSEFIC = array();
    private $arrCodOrgaosSAV = array();

    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio às Leis de Incentivo à Cultura"; // titulo da pagina
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 103; // Coordenador de Analise
        $PermissoesGrupo[] = 127; // Coordenador Geral de Analise
        $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
        $PermissoesGrupo[] = 122; // Coordenador Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral Acompanhamento
        $PermissoesGrupo[] = 110; // Tecnico Analise
        $PermissoesGrupo[] = 121; // Tecnico Acompanhamento
        parent::perfil(1, $PermissoesGrupo);
        
        parent::init(); // chama o init() do pai GenericControllerNew
        
        $this->codGrupo       = $_SESSION['GrupoAtivo']['codGrupo'];
        $this->view->codGrupo = $_SESSION['GrupoAtivo']['codGrupo'];
        $this->codOrgao       = $_SESSION['GrupoAtivo']['codOrgao'];
        $this->view->codOrgao = $_SESSION['GrupoAtivo']['codOrgao'];
        
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        //$this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior))?$auth->getIdentity()->usu_org_max_superior:$auth->getIdentity()->usu_orgao;
        $this->arrCodOrgaosSEFIC = array('251','254','256','262','270','271','272');
        $this->arrCodOrgaosSAV   = array('160','166','167','168','169','171','179');

        if (in_array($this->codOrgao, $this->arrCodOrgaosSEFIC)) {
            $this->codOrgaoSuperior = 251;
        } elseif (in_array($this->codOrgao, $this->arrCodOrgaosSAV)) {
            $this->codOrgaoSuperior = 160;
        }

        if ($this->codGrupo == 103 || $this->codGrupo == 127  || $this->codGrupo == 110) { //103=Coord. de Analise   127=Coord. Geral de Analise   110=Tecnico de Analise
            $this->tipoProjeto = "analiseInicial";
        } elseif ($this->codGrupo == 122 || $this->codGrupo == 123 || $this->codGrupo == 121) { //122=Cood. de Acompanhamento  123=Cood. Geral de Acompanhamento  121=Tecnico Acompanhamento
            $this->tipoProjeto = "readequacao";
        }
        
        $post = Zend_Registry::get('post');
        
        if (empty($post->nomeSecretario) && empty($post->nomeOrgao)) {
            $tbSecretario = new tbSecretario();
            $rsOrgaoSecretario = $tbSecretario->buscar(array('idOrgao = ?' => $this->codOrgaoSuperior))->current();
            
            if (!empty($rsOrgaoSecretario->nmSecretario) && !empty($rsOrgaoSecretario->dsCargo)) {
                $this->nomeSecretario = $rsOrgaoSecretario->nmSecretario;
                $this->nomeOrgao      = $rsOrgaoSecretario->dsCargo;
            } else {
                $this->nomeSecretario = "";
                $this->nomeOrgao      = "";
                /*if($this->codOrgaoSuperior == 251){ //SEFIC
                    $this->nomeSecretario = "HENILTON PARENTE DE MENEZES";
                    $this->nomeOrgao      = "Secretário de Fomento e Incentivo à Cultura";
                }else{ //SAV
                    $this->nomeSecretario = "ANA PAULA DOURADO SANTANA";
                    $this->nomeOrgao      = "Secretária de Audiovisual";
                }*/
            }
        } else {
            $this->nomeSecretario = $post->nomeSecretario;
            $this->nomeOrgao      = $post->nomeOrgao;
        }
        
        $this->view->nomeSecretario = $this->nomeSecretario;
        $this->view->nomeOrgao      = $this->nomeOrgao;
    }

    // fecha metodo init()

    public function indexAction()
    {
        $pauta = new Pauta();
        $tblPauta = new tbPauta();
        $reuniao = new Reuniao();
        $pronac  = (isset($_POST['pronac'])) ? $_POST['pronac'] : "";
        
        if (isset($_POST['NrReuniao'])) {
            $NrReuniao = $_POST['NrReuniao'];
            $buscareuniao = $reuniao->buscar(array('NrReuniao = ?' => $NrReuniao));
            if ($buscareuniao->count() > 0) {
                $buscareuniao = $buscareuniao->current()->toArray();
                $idnrReuniao = $buscareuniao['idNrReuniao'];
                $reuniaoanterior = $NrReuniao;
            } else {
                $reuniaoaberta = $reuniao->buscarReuniaoAberta();
                $reuniaoanterior = $reuniaoaberta['NrReuniao'] - 1;
                $buscareuniao = $reuniao->buscar(array('NrReuniao = ?' => $reuniaoanterior));
                if ($buscareuniao->count() > 0) {
                    $buscareuniao = $buscareuniao->current()->toArray();
                    $idnrReuniao = $buscareuniao['idNrReuniao'];
                }
            }
            //$buscarPauta = $pauta->PautaAprovada($idnrReuniao);
            $arrBusca = array();
            $arrBusca['r.NrReuniao = ?'] = $NrReuniao;
            //$arrBusca[" TABELAS.dbo.fnCodigoOrgaoEstrutura(pr.Orgao, 1) = ? "] = $this->codOrgaoSuperior;
            $arrBusca['pr.Orgao = ?'] = $this->codOrgao;
            if ($this->tipoProjeto == "readequacao") {
                $arrBusca['par.TipoParecer <> ?'] = 1;
            } else {
                $arrBusca['par.TipoParecer = ?'] = 1;
            }
            if (!empty($pronac)) {
                $arrBusca['pr.AnoProjeto+pr.Sequencial = ?'] = $pronac;
            }
            $buscarPauta = $tblPauta->buscarProjetosTermoAprovacao($arrBusca);
            $projetos = array();
            $num = 0;
            foreach ($buscarPauta as $projetosCNIC) {
                $projetos[$num]['IdPRONAC'] = $projetosCNIC->IdPRONAC;
                $projetos[$num]['PRONAC'] = $projetosCNIC->pronac;
                $projetos[$num]['NomeProjeto'] = $projetosCNIC->NomeProjeto;
                $projetos[$num]['stAnalise'] = $projetosCNIC->stAnalise;
                $num++;
            }
            $this->view->buscartermo = $projetos;
            $this->view->nrReuniao   = $_POST['NrReuniao'];
        } else {
            $reuniaoaberta = $reuniao->buscarReuniaoAberta();
            $reuniaoanterior = $reuniaoaberta['NrReuniao'] - 1;
        }
        $this->view->ultimaCNIC = isset($reuniaoanterior) ? $reuniaoanterior : '';
        $buscardadosreuniao = $reuniao->buscar(array('NrReuniao = ?' => $reuniaoanterior));
        if ($buscardadosreuniao->count() > 0) {
            $buscardadosreuniao = $buscardadosreuniao->current()->toArray();
            $this->view->dadosultimaCNIC = $buscardadosreuniao;
        }
    }
    /*public function gerarpdfAction_BKP() {
        $this->_helper->layout->disableLayout();
        $pauta = new Pauta();
        $tblPauta = new tbPauta();
        $parecer = new Parecer_Model_DbTable_Parecer();
        $idpronac  = $_POST['idpronac'];
        $nrReuniao = $_POST['nrReuniao'];
        //$buscarpauta = $pauta->dadosiniciaistermoaprovacao($idpronac);

        //========== PROJETOS APROVADOS SEFIC e SAV - APROVACAO INICIAL =========================/
        $arrBusca = array();
        $arrBusca['tp.idPronac IN (?)']  = $idpronac;
        //$arrBusca['pr.Situacao = ?']     = "D03";
        //$arrBusca['pr.Situacao IN (?)']  = array('D03', 'D01');
        $arrBusca['tp.stAnalise IN (?)']  = array("AS","AC","AR");
        $arrBusca['par.TipoParecer = ?'] = 1;
        $arrBusca['r.NrReuniao = ?']     = $nrReuniao;
        $rsProjetosAprovados = $tblPauta->buscarProjetosTermoAprovacao($arrBusca);

        //========== PROJETOS INDEFERIDOS SEFIC e SAV - APROVACAO INICIAL =======================/
        $arrBusca = array();
        $arrBusca['tp.idPronac IN (?)']  = $idpronac;
        //$arrBusca['pr.Situacao IN (?)']  = array('A13', 'A14', 'A16', 'A17', 'A20', 'A23', 'A24', 'D14', 'A41');
        $arrBusca['tp.stAnalise NOT IN (?)'] = array("AS","AC","AR");
        $arrBusca['par.TipoParecer = ?'] = 1;
        $arrBusca['r.NrReuniao = ?']     = $nrReuniao;
        $rsProjetosIndeferidos = $tblPauta->buscarProjetosTermoAprovacao($arrBusca);

        //========== PROJETOS APROVADOS SEFIC e SAV - RECURSO ==========================/
        $arrBusca = array();
        $arrBusca['tp.idPronac IN (?)']   = $idpronac;
        $arrBusca['tp.stAnalise IN (?)']  = array("AS","AC","AR");
        $arrBusca['par.TipoParecer <> ?'] = 1;
        $arrBusca['r.NrReuniao = ?']      = $nrReuniao;
        $rsProjetosAprovadosRecurso = $tblPauta->buscarProjetosTermoAprovacao($arrBusca);

        //========== PROJETOS INDEFERIDOS SEFIC e SAV - RECUSROS =======================/
        $arrBusca = array();
        $arrBusca['tp.idPronac IN (?)']      = $idpronac;
        $arrBusca['tp.stAnalise NOT IN (?)'] = array("AS","AC","AR");
        $arrBusca['par.TipoParecer <> ?']    = 1;
        $arrBusca['r.NrReuniao = ?']         = $nrReuniao;
        $rsProjetosIndeferidosRecurso = $tblPauta->buscarProjetosTermoAprovacao($arrBusca);


        /**=================================================================*/
    /*=============== TRATAMENTO DE PROJETOS APROVADOS =================*/
    /**=================================================================*/
        
    /* === APROVACAO INICIAL - APROVADOS ==*
    $pautaSAVAprovados = array();
    $pautaSEFICAprovados = array();
    $ct1 = 0;
    $ct2 = 0;
    foreach ($rsProjetosAprovados as $dadosresultado) {

        //buscar orgao superior do projeto
        /*$orgao = $dadosresultado['Orgao'];
        $tbUsuariosorgaosgrupos = new Usuariosorgaosgrupos();
        $rsOrgao = $tbUsuariosorgaosgrupos->buscarOrgaoSuperior($orgao)->current();*
        //if(!empty($rsOrgao) && $rsOrgao->org_superior == 160){  //Projeto SAV

        if($dadosresultado['Area'] == "2" && $dadosresultado['Orgao'] != "262"){  //Projetos da SAV

            $pautaSAVAprovados[$ct1]['PRONAC'] = $dadosresultado['pronac'];
            $pautaSAVAprovados[$ct1]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
            $pautaSAVAprovados[$ct1]['TipoParecer'] = 'Aprova&ccedil;&atilde;o Inicial';
            $pautaSAVAprovados[$ct1]['NrReuniao'] = $dadosresultado['NrReuniao'];
            $pautaSAVAprovados[$ct1]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
            $pautaSAVAprovados[$ct1]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
            $pautaSAVAprovados[$ct1]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
            $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
            $pautaSAVAprovados[$ct1]['parecerista'] = $buscarparecer[0]->ResumoParecer;
            $pautaSAVAprovados[$ct1]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
            /*$buscarparecerConsolidado = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC'], "idTipoAgente = ?"=>10))->current();
            $pautaSAVAprovados[$ct1]['Consolidacao'] = (!empty($buscarparecerConsolidado)) ? $buscarparecerConsolidado->ResumoParecer : "";*
            $pautaSAVAprovados[$ct1]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
            /*if ($dadosresultado['stAnalise'] == 'AS' or $dadosresultado['stAnalise'] == "AC" or $dadosresultado['stAnalise'] == "AR") {
                $pautaSAVAprovados[$ct1]['stAnalise'] = 'Aprova&ccedil;&atilde;o';
            } else {
                $pautaSAVAprovados[$ct1]['stAnalise'] = "Indeferimento";
            }*
            $pautaSAVAprovados[$ct1]['analise'] = $dadosresultado['stAnalise'];
            $ct1++;
        }else{ //Projetos da SEFIC

            $pautaSEFICAprovados[$ct2]['PRONAC'] = $dadosresultado['pronac'];
            $pautaSEFICAprovados[$ct2]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
            $pautaSEFICAprovados[$ct2]['TipoParecer'] = 'Aprova&ccedil;&atilde;o Inicial';
            $pautaSEFICAprovados[$ct2]['NrReuniao'] = $dadosresultado['NrReuniao'];
            $pautaSEFICAprovados[$ct2]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
            $pautaSEFICAprovados[$ct2]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
            $pautaSEFICAprovados[$ct2]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
            $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
            $pautaSEFICAprovados[$ct2]['parecerista'] = $buscarparecer[0]->ResumoParecer;
            $pautaSEFICAprovados[$ct2]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
            /*$buscarparecerConsolidado = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC'], "idTipoAgente = ?"=>10))->current();
            $pautaSEFICAprovados[$ct2]['Consolidacao'] = (!empty($buscarparecerConsolidado)) ? $buscarparecerConsolidado->ResumoParecer : "";*
            $pautaSEFICAprovados[$ct2]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
            $pautaSEFICAprovados[$ct2]['analise'] = $dadosresultado['stAnalise'];
            $ct2++;
        }
    }

    /* === RECURSO - APROVADOS ==*
    $pautaSAVAprovadosRecurso = array();
    $pautaSEFICAprovadosRecurso = array();
    $ct1 = 0;
    $ct2 = 0;
    foreach ($rsProjetosAprovadosRecurso as $dadosresultado) {

        if($dadosresultado['Area'] == "2" && $dadosresultado['Orgao'] != "262"){  //Projetos da SAV

            $pautaSAVAprovadosRecurso[$ct1]['PRONAC'] = $dadosresultado['pronac'];
            $pautaSAVAprovadosRecurso[$ct1]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
            $pautaSAVAprovadosRecurso[$ct1]['TipoParecer'] = 'Recurso';
            $pautaSAVAprovadosRecurso[$ct1]['NrReuniao'] = $dadosresultado['NrReuniao'];
            $pautaSAVAprovadosRecurso[$ct1]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
            $pautaSAVAprovadosRecurso[$ct1]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
            $pautaSAVAprovadosRecurso[$ct1]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
            $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
            $pautaSAVAprovadosRecurso[$ct1]['parecerista'] = $buscarparecer[0]->ResumoParecer;
            $pautaSAVAprovadosRecurso[$ct1]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
            /*$buscarparecerConsolidado = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC'], "idTipoAgente = ?"=>10))->current();
            $pautaSAVAprovadosRecurso[$ct1]['Consolidacao'] = (!empty($buscarparecerConsolidado)) ? $buscarparecerConsolidado->ResumoParecer : "";*
            $pautaSAVAprovadosRecurso[$ct1]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
            $pautaSAVAprovadosRecurso[$ct1]['analise'] = $dadosresultado['stAnalise'];
            $ct1++;
        }else{ //Projetos da SEFIC

            $pautaSEFICAprovadosRecurso[$ct2]['PRONAC'] = $dadosresultado['pronac'];
            $pautaSEFICAprovadosRecurso[$ct2]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
            $pautaSEFICAprovadosRecurso[$ct2]['TipoParecer'] = 'Recurso';
            $pautaSEFICAprovadosRecurso[$ct2]['NrReuniao'] = $dadosresultado['NrReuniao'];
            $pautaSEFICAprovadosRecurso[$ct2]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
            $pautaSEFICAprovadosRecurso[$ct2]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
            $pautaSEFICAprovadosRecurso[$ct2]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
            $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
            $pautaSEFICAprovadosRecurso[$ct2]['parecerista'] = $buscarparecer[0]->ResumoParecer;
            $pautaSEFICAprovadosRecurso[$ct2]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
            /*$buscarparecerConsolidado = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC'], "idTipoAgente = ?"=>10))->current();
            $pautaSEFICAprovadosRecurso[$ct2]['Consolidacao'] = (!empty($buscarparecerConsolidado)) ? $buscarparecerConsolidado->ResumoParecer : "";*
            $pautaSEFICAprovadosRecurso[$ct2]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
            $pautaSEFICAprovadosRecurso[$ct2]['analise'] = $dadosresultado['stAnalise'];
            $ct2++;
        }
    }

    /**=================================================================*/
    /*=============== TRATAMENTO DE PROJETOS INDEFERIDOS ===============*/
    /**=================================================================*/
        
    /* === APROVACAO INICIAL - INDEFERIDOS ==*
    $pautaSAVIndeferidos = array();
    $pautaSEFICIndeferidos = array();
    $ct1 = 0;
    $ct2 = 0;
    foreach ($rsProjetosIndeferidos as $dadosresultado) {

        if($dadosresultado['Area'] == "2" && $dadosresultado['Orgao'] != "262"){  //Projetos da SAV

            $pautaSAVIndeferidos[$ct1]['PRONAC'] = $dadosresultado['pronac'];
            $pautaSAVIndeferidos[$ct1]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
            $pautaSAVIndeferidos[$ct1]['TipoParecer'] = 'Aprova&ccedil;&atilde;o Inicial';
            $pautaSAVIndeferidos[$ct1]['NrReuniao'] = $dadosresultado['NrReuniao'];
            $pautaSAVIndeferidos[$ct1]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
            $pautaSAVIndeferidos[$ct1]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
            $pautaSAVIndeferidos[$ct1]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
            $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
            $pautaSAVIndeferidos[$ct1]['parecerista'] = $buscarparecer[0]->ResumoParecer;
            $pautaSAVIndeferidos[$ct1]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
            /*$buscarparecerConsolidado = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC'], "idTipoAgente = ?"=>10))->current();
            $pautaSAVIndeferidos[$ct1]['Consolidacao'] = (!empty($buscarparecerConsolidado)) ? $buscarparecerConsolidado->ResumoParecer : "";*
            $pautaSAVIndeferidos[$ct1]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
            $pautaSAVIndeferidos[$ct1]['analise'] = $dadosresultado['stAnalise'];
            $ct1++;
        }else{ //Projetos da SEFIC

            $pautaSEFICIndeferidos[$ct2]['PRONAC'] = $dadosresultado['pronac'];
            $pautaSEFICIndeferidos[$ct2]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
            $pautaSEFICIndeferidos[$ct2]['TipoParecer'] = 'Aprova&ccedil;&atilde;o Inicial';
            $pautaSEFICIndeferidos[$ct2]['NrReuniao'] = $dadosresultado['NrReuniao'];
            $pautaSEFICIndeferidos[$ct2]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
            $pautaSEFICIndeferidos[$ct2]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
            $pautaSEFICIndeferidos[$ct2]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
            $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
            $pautaSEFICIndeferidos[$ct2]['parecerista'] = $buscarparecer[0]->ResumoParecer;
            $pautaSEFICIndeferidos[$ct2]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
            /*$buscarparecerConsolidado = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC'], "idTipoAgente = ?"=>10))->current();
            $pautaSEFICIndeferidos[$ct2]['Consolidacao'] = (!empty($buscarparecerConsolidado)) ? $buscarparecerConsolidado->ResumoParecer : "";*
            $pautaSEFICIndeferidos[$ct2]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
            $pautaSEFICIndeferidos[$ct2]['analise'] = $dadosresultado['stAnalise'];
            $ct2++;
        }
    }

    /* === RECURSO - INDEFERIDOS ==*
    $pautaSAVIndeferidosRecurso = array();
    $pautaSEFICIndeferidosRecurso = array();
    $ct1 = 0;
    $ct2 = 0;
    foreach ($rsProjetosIndeferidosRecurso as $dadosresultado) {

        if($dadosresultado['Area'] == "2" && $dadosresultado['Orgao'] != "262"){  //Projetos da SAV

            $pautaSAVIndeferidosRecurso[$ct1]['PRONAC'] = $dadosresultado['pronac'];
            $pautaSAVIndeferidosRecurso[$ct1]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
            $pautaSAVIndeferidosRecurso[$ct1]['TipoParecer'] = 'Recurso';
            $pautaSAVIndeferidosRecurso[$ct1]['NrReuniao'] = $dadosresultado['NrReuniao'];
            $pautaSAVIndeferidosRecurso[$ct1]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
            $pautaSAVIndeferidosRecurso[$ct1]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
            $pautaSAVIndeferidosRecurso[$ct1]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
            $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
            $pautaSAVIndeferidosRecurso[$ct1]['parecerista'] = $buscarparecer[0]->ResumoParecer;
            $pautaSAVIndeferidosRecurso[$ct1]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
            /*$buscarparecerConsolidado = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC'], "idTipoAgente = ?"=>10))->current();
            $pautaSAVIndeferidosRecurso[$ct1]['Consolidacao'] = (!empty($buscarparecerConsolidado)) ? $buscarparecerConsolidado->ResumoParecer : "";*
            $pautaSAVIndeferidosRecurso[$ct1]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
            $pautaSAVIndeferidosRecurso[$ct1]['analise'] = $dadosresultado['stAnalise'];
            $ct1++;
        }else{ //Projetos da SEFIC

            $pautaSEFICIndeferidosRecurso[$ct2]['PRONAC'] = $dadosresultado['pronac'];
            $pautaSEFICIndeferidosRecurso[$ct2]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
            $pautaSEFICIndeferidosRecurso[$ct2]['TipoParecer'] = 'Recurso';
            $pautaSEFICIndeferidosRecurso[$ct2]['NrReuniao'] = $dadosresultado['NrReuniao'];
            $pautaSEFICIndeferidosRecurso[$ct2]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
            $pautaSEFICIndeferidosRecurso[$ct2]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
            $pautaSEFICIndeferidosRecurso[$ct2]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
            $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
            $pautaSEFICIndeferidosRecurso[$ct2]['parecerista'] = $buscarparecer[0]->ResumoParecer;
            $pautaSEFICIndeferidosRecurso[$ct2]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
            /*$buscarparecerConsolidado = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC'], "idTipoAgente = ?"=>10))->current();
            $pautaSEFICIndeferidosRecurso[$ct2]['Consolidacao'] = (!empty($buscarparecerConsolidado)) ? $buscarparecerConsolidado->ResumoParecer : "";*
            $pautaSEFICIndeferidosRecurso[$ct2]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
            $pautaSEFICIndeferidosRecurso[$ct2]['analise'] = $dadosresultado['stAnalise'];
            $ct2++;
        }
    }

    //APROVACAO INICIAL
    $this->view->pautaSAVAprovados = $pautaSAVAprovados;
    $this->view->pautaSEFICAprovados = $pautaSEFICAprovados;
    $this->view->pautaSAVIndeferidos = $pautaSAVIndeferidos;
    $this->view->pautaSEFICIndeferidos = $pautaSEFICIndeferidos;

    //RECURSO
    $this->view->pautaSAVAprovadosRecurso = $pautaSAVAprovadosRecurso;
    $this->view->pautaSEFICAprovadosRecurso = $pautaSEFICAprovadosRecurso;
    $this->view->pautaSAVIndeferidosRecurso = $pautaSAVIndeferidosRecurso;
    $this->view->pautaSEFICIndeferidosRecurso = $pautaSEFICIndeferidosRecurso;

    //TRATAMENTO CASO NENHUMA DAS CONDIÇÕES SEJAM ATENDIDAS
    if( (count($pautaSAVAprovados) <= 0) &&
        (count($pautaSEFICAprovados) <= 0) &&
        (count($pautaSAVIndeferidos) <= 0) &&
        (count($pautaSEFICIndeferidos) <= 0) &&
        (count($pautaSAVAprovadosRecurso) <= 0) &&
        (count($pautaSEFICAprovadosRecurso) <= 0) &&
        (count($pautaSAVIndeferidosRecurso) <= 0) &&
        (count($pautaSEFICIndeferidosRecurso) <= 0))
    {
        parent::message("O(s) projeto(s) informado(s) não atende(m) às condições necessárias para ser(em) impresso(s) no Termo de Decisão.", "gerartermodeaprovacao/", "ALERT");
    }

    /*$pautaSAVAprovados[$ct1]['PRONAC'] = $dadosresultado['pronac'];
    $pautaSAVAprovados[$ct1]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
    $pautaSAVAprovados[$ct1]['TipoParecer'] = 'Aprova&ccedil;&atilde;o Inicial';
    $pautaSAVAprovados[$ct1]['NrReuniao'] = $dadosresultado['NrReuniao'];
    $pautaSAVAprovados[$ct1]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
    $pautaSAVAprovados[$ct1]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
    $pautaSAVAprovados[$ct1]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
    $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
    $pautaSAVAprovados[$ct1]['parecerista'] = $buscarparecer[0]->ResumoParecer;
    $pautaSAVAprovados[$ct1]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
    $pautaSAVAprovados[$ct1]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
    if ($dadosresultado['stAnalise'] == 'AS' or $dadosresultado['stAnalise'] == "AC" or $dadosresultado['stAnalise'] == "AR") {
        $pautaSAVAprovados[$ct1]['stAnalise'] = 'Aprova&ccedil;&atilde;o';
    } else {
        $pautaSAVAprovados[$ct1]['stAnalise'] = "Indeferimento";
    }
    $pautaSAVAprovados[$ct1]['analise'] = $dadosresultado['stAnalise'];
    $ct1++;*
    }
    */
    
    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $pauta = new Pauta();
        $tblPauta = new tbPauta();
        $parecer = new Parecer_Model_DbTable_Parecer();
        $idpronac  = $_POST['idpronac'];
        $nrReuniao = $_POST['nrReuniao'];
        //$buscarpauta = $pauta->dadosiniciaistermoaprovacao($idpronac);
        
        if ($this->tipoProjeto == "analiseInicial") {
            //========== PROJETOS APROVADOS SEFIC e SAV - APROVACAO INICIAL =========================/
            $arrBusca = array();
            $arrBusca['tp.idPronac IN (?)']  = $idpronac;
            //$arrBusca['pr.Situacao = ?']     = "D03";
            //$arrBusca['pr.Situacao IN (?)']  = array('D03', 'D01');
            $arrBusca['tp.stAnalise IN (?)']  = array("AS","AC","AR");
            $arrBusca['par.TipoParecer = ?'] = 1;
            $arrBusca['r.NrReuniao = ?']     = $nrReuniao;
            //$arrBusca[" TABELAS.dbo.fnCodigoOrgaoEstrutura(pr.Orgao, 1) = ? "] = $this->codOrgaoSuperior;
            $arrBusca['pr.Orgao = ?'] = $this->codOrgao;
            $rsProjetosAprovados = $tblPauta->buscarProjetosTermoAprovacao($arrBusca);

            //========== PROJETOS INDEFERIDOS SEFIC e SAV - APROVACAO INICIAL =======================/
            $arrBusca = array();
            $arrBusca['tp.idPronac IN (?)']  = $idpronac;
            //$arrBusca['pr.Situacao IN (?)']  = array('A13', 'A14', 'A16', 'A17', 'A20', 'A23', 'A24', 'D14', 'A41');
            $arrBusca['tp.stAnalise NOT IN (?)'] = array("AS","AC","AR");
            $arrBusca['par.TipoParecer = ?'] = 1;
            $arrBusca['r.NrReuniao = ?']     = $nrReuniao;
            //$arrBusca[" TABELAS.dbo.fnCodigoOrgaoEstrutura(pr.Orgao, 1) = ? "] = $this->codOrgaoSuperior;
            $arrBusca['pr.Orgao = ?'] = $this->codOrgao;
            $rsProjetosIndeferidos = $tblPauta->buscarProjetosTermoAprovacao($arrBusca);
        }
        
        if ($this->tipoProjeto == "readequacao") {
            //========== PROJETOS APROVADOS SEFIC e SAV - READEQUACAO ==========================/
            $arrBusca = array();
            $arrBusca['tp.idPronac IN (?)']   = $idpronac;
            $arrBusca['tp.stAnalise IN (?)']  = array("AS","AC","AR");
            $arrBusca['par.TipoParecer <> ?'] = 1;
            $arrBusca['r.NrReuniao = ?']      = $nrReuniao;
            //$arrBusca[" TABELAS.dbo.fnCodigoOrgaoEstrutura(pr.Orgao, 1) = ? "] = $this->codOrgaoSuperior;
            $arrBusca['pr.Orgao = ?'] = $this->codOrgao;
            $rsProjetosAprovadosReadequacao = $tblPauta->buscarProjetosTermoAprovacao($arrBusca);

            //========== PROJETOS INDEFERIDOS SEFIC e SAV - READEQUACAO =======================/
            $arrBusca = array();
            $arrBusca['tp.idPronac IN (?)']      = $idpronac;
            $arrBusca['tp.stAnalise NOT IN (?)'] = array("AS","AC","AR");
            $arrBusca['par.TipoParecer <> ?']    = 1;
            $arrBusca['r.NrReuniao = ?']         = $nrReuniao;
            //$arrBusca[" TABELAS.dbo.fnCodigoOrgaoEstrutura(pr.Orgao, 1) = ? "] = $this->codOrgaoSuperior;
            $arrBusca['pr.Orgao = ?'] = $this->codOrgao;
            $rsProjetosIndeferidosReadequacao = $tblPauta->buscarProjetosTermoAprovacao($arrBusca);
        }
        
        /**=================================================================*/
        /*=============== TRATAMENTO DE PROJETOS APROVADOS =================*/
        /**=================================================================*/
        
        $pautaAprovados = array();
        if ($this->tipoProjeto == "analiseInicial") {
            /* === APROVACAO INICIAL - APROVADOS ==*/
            $ct1 = 0;
            foreach ($rsProjetosAprovados as $dadosresultado) {
                $pautaAprovados[$ct1]['PRONAC'] = $dadosresultado['pronac'];
                $pautaAprovados[$ct1]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
                $pautaAprovados[$ct1]['TipoParecer'] = 'Aprova&ccedil;&atilde;o Inicial';
                $pautaAprovados[$ct1]['NrReuniao'] = $dadosresultado['NrReuniao'];
                $pautaAprovados[$ct1]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
                $pautaAprovados[$ct1]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
                $pautaAprovados[$ct1]['DtFinal']  = Data::DataporExtenso($dadosresultado['DtFinal']);
                $pautaAprovados[$ct1]['DtAssinatura'] = $dadosresultado['DtAssinatura'];
                $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
                $pautaAprovados[$ct1]['parecerista'] = $buscarparecer[0]->ResumoParecer;
                $pautaAprovados[$ct1]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
                $pautaAprovados[$ct1]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
                $pautaAprovados[$ct1]['analise'] = $dadosresultado['stAnalise'];
                $ct1++;
            }
        }
        /* === READEQUACAO - APROVADOS ==*/
        
        $pautaAprovadosReadequacao = array();
        if ($this->tipoProjeto == "readequacao") {
            $ct1 = 0;
            foreach ($rsProjetosAprovadosReadequacao as $dadosresultado) {
                $pautaAprovadosReadequacao[$ct1]['PRONAC'] = $dadosresultado['pronac'];
                $pautaAprovadosReadequacao[$ct1]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
                $pautaAprovadosReadequacao[$ct1]['TipoParecer'] = 'Readequação';
                $pautaAprovadosReadequacao[$ct1]['NrReuniao'] = $dadosresultado['NrReuniao'];
                $pautaAprovadosReadequacao[$ct1]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
                $pautaAprovadosReadequacao[$ct1]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
                $pautaAprovadosReadequacao[$ct1]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
                $pautaAprovadosReadequacao[$ct1]['DtAssinatura'] = $dadosresultado['DtAssinatura'];
                $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
                $pautaAprovadosReadequacao[$ct1]['parecerista'] = $buscarparecer[0]->ResumoParecer;
                $pautaAprovadosReadequacao[$ct1]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
                $pautaAprovadosReadequacao[$ct1]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
                $pautaAprovadosReadequacao[$ct1]['analise'] = $dadosresultado['stAnalise'];
                $ct1++;
            }
        }
        /**=================================================================*/
        /*=============== TRATAMENTO DE PROJETOS INDEFERIDOS ===============*/
        /**=================================================================*/
        
        $pautaIndeferidos = array();
        if ($this->tipoProjeto == "analiseInicial") {
            /* === APROVACAO INICIAL - INDEFERIDOS ==*/
            $ct1 = 0;
            foreach ($rsProjetosIndeferidos as $dadosresultado) {
                $pautaIndeferidos[$ct1]['PRONAC'] = $dadosresultado['pronac'];
                $pautaIndeferidos[$ct1]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
                $pautaIndeferidos[$ct1]['TipoParecer'] = 'Aprova&ccedil;&atilde;o Inicial';
                $pautaIndeferidos[$ct1]['NrReuniao'] = $dadosresultado['NrReuniao'];
                $pautaIndeferidos[$ct1]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
                $pautaIndeferidos[$ct1]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
                $pautaIndeferidos[$ct1]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
                $pautaIndeferidos[$ct1]['DtAssinatura'] = $dadosresultado['DtAssinatura'];
                $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
                $pautaIndeferidos[$ct1]['parecerista'] = $buscarparecer[0]->ResumoParecer;
                $pautaIndeferidos[$ct1]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
                $pautaIndeferidos[$ct1]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
                $pautaIndeferidos[$ct1]['analise'] = $dadosresultado['stAnalise'];
                $ct1++;
            }
        }
        /* === READEQUACAO - INDEFERIDOS ==*/
        $pautaIndeferidosReadequacao = array();
        if ($this->tipoProjeto == "readequacao") {
            $ct1 = 0;
            foreach ($rsProjetosIndeferidosReadequacao as $dadosresultado) {
                $pautaIndeferidosReadequacao[$ct1]['PRONAC'] = $dadosresultado['pronac'];
                $pautaIndeferidosReadequacao[$ct1]['NomeProjeto'] = $dadosresultado['NomeProjeto'];
                $pautaIndeferidosReadequacao[$ct1]['TipoParecer'] = 'Readequação';
                $pautaIndeferidosReadequacao[$ct1]['NrReuniao'] = $dadosresultado['NrReuniao'];
                $pautaIndeferidosReadequacao[$ct1]['mesReuniao'] = date('m', strtotime($dadosresultado['DtInicio']));
                $pautaIndeferidosReadequacao[$ct1]['DtInicio'] = date('d', strtotime($dadosresultado['DtInicio']));
                $pautaIndeferidosReadequacao[$ct1]['DtFinal'] = Data::DataporExtenso($dadosresultado['DtFinal']);
                $pautaIndeferidosReadequacao[$ct1]['DtAssinatura'] = $dadosresultado['DtAssinatura'];
                $buscarparecer = $parecer->buscar(array('idPRONAC = ?'=>$dadosresultado['IdPRONAC']));
                $pautaIndeferidosReadequacao[$ct1]['parecerista'] = $buscarparecer[0]->ResumoParecer;
                $pautaIndeferidosReadequacao[$ct1]['componente'] = isset($buscarparecer[1]->ResumoParecer) ? $buscarparecer[1]->ResumoParecer : '';
                $pautaIndeferidosReadequacao[$ct1]['Consolidacao'] = $dadosresultado['dsConsolidacao'];
                $pautaIndeferidosReadequacao[$ct1]['analise'] = $dadosresultado['stAnalise'];
                $ct1++;
            }
        }
        //APROVACAO INICIAL
        $this->view->pautaAprovados = $pautaAprovados;
        $this->view->pautaIndeferidos = $pautaIndeferidos;
        
        //READEQUACAO
        $this->view->pautaAprovadosReadequacao = $pautaAprovadosReadequacao;
        $this->view->pautaIndeferidosReadequacao = $pautaIndeferidosReadequacao;
        
        // mês por extenso
        $mes_extenso[1] = "janeiro";
        $mes_extenso[2] = "fevereiro";
        $mes_extenso[3] = "mar&ccedil;o";
        $mes_extenso[4] = "abril";
        $mes_extenso[5] = "maio";
        $mes_extenso[6] = "junho";
        $mes_extenso[7] = "julho";
        $mes_extenso[8] = "agosto";
        $mes_extenso[9] = "setembro";
        $mes_extenso[10] = "outubro";
        $mes_extenso[11] = "novembro";
        $mes_extenso[12] = "dezembro";
        
        $this->view->mesPorExtenso = $mes_extenso;
        
        //TRATAMENTO CASO NENHUMA DAS CONDIÇÕES SEJAM ATENDIDAS
        if ((count($pautaAprovados) <= 0) &&
            (count($pautaIndeferidos) <= 0) &&
            (count($pautaAprovadosReadequacao) <= 0) &&
            (count($pautaIndeferidosReadequacao) <= 0)) {
            parent::message("O(s) projeto(s) informado(s) não atende(m) às condições necessárias para ser(em) impresso(s) no Termo de Decisão.", "gerartermodeaprovacao/", "ALERT");
        }
        
        if ($this->codOrgaoSuperior == 251) {
            $this->montaTela("gerartermodeaprovacao/gerar-pdf-sefic.phtml", array());
        } else {
            $this->montaTela("gerartermodeaprovacao/gerar-pdf-sav.phtml", array());
        }
    }
}
