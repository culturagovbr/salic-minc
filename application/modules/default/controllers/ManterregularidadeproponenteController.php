<?php

class ManterRegularidadeProponenteController extends MinC_Controller_Action_Abstract
{

    /**
     * cpf/cnpj pra consulta
     */
    private $cpfcnpj = 0;
    private $CQTF = 180;
    private $INSS = 180;
    private $FGTS = 29;
    private $proponente = '';

    /**
     * query string (vari�veis via get)
     */
    private $queryString = '';
    private $codGrupo = null;


    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {

        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 109; // T�cnico de An�lise
        $PermissoesGrupo[] = 110; // T�cnico de An�lise
        $PermissoesGrupo[] = 103; // Coordenador de An�lise
        $PermissoesGrupo[] = 127; // Coordenador - Geral de An�lise

        $PermissoesGrupo[] = 131; // Coordenador de Admissibilidade
        $PermissoesGrupo[] = 92;  // T�cnico de Admissibilidade

        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        $PermissoesGrupo[] = 108; // Acompanhamento - Coordenador

        $PermissoesGrupo[] = 138; // Coordenador de Avalia��o
        $PermissoesGrupo[] = 139; // T�cnico de Avalia��o

        $PermissoesGrupo[] = 125; // Coordenador de Presta��o de Contas
        $PermissoesGrupo[] = 124; // T�cnico de Presta��o de Contas

        $PermissoesGrupo[] = 134; // Coordenador de Fiscaliza��o
        $PermissoesGrupo[] = 135; // T�cnico de Fiscaliza��o

        $PermissoesGrupo[] = 131; // Coordenador de Admissibilidade
        $PermissoesGrupo[] = 140; // T�cnico de Admissibilidade

        parent::perfil(1, $PermissoesGrupo);

        parent::init();

        // cria a sessao com o grupo ativo
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->codGrupo = $GrupoAtivo->codGrupo;
        $this->view->codGrupo = $GrupoAtivo->codGrupo;

        if ($GrupoAtivo->codGrupo == 92 || $GrupoAtivo->codGrupo == 131 || $GrupoAtivo->codGrupo == 140) :
            $this->view->dsMod = 'Admissibilidade'; elseif ($GrupoAtivo->codGrupo == 103 || $GrupoAtivo->codGrupo == 109 || $GrupoAtivo->codGrupo == 110 || $GrupoAtivo->codGrupo == 127) :
            $this->view->dsMod = 'An&aacute;lise'; elseif ($GrupoAtivo->codGrupo == 125 || $GrupoAtivo->codGrupo == 124) :
            $this->view->dsMod = 'Presta&ccedil;&atilde;o de Contas'; else :
            $this->view->dsMod = 'Acompanhamento';
        endif;

        $cnpjcpf = $this->_request->getParam("cpfCnpj");
        if (strlen(retiraMascara($cnpjcpf)) > 11) {
            $this->proponente = "PJ";
            $this->view->proponente = "PJ";
        } else {
            $this->proponente = "PF";
            $this->view->proponente = "PF";
        }
        //$this->proponente
    }

    // fecha m�todo init()

    /**
     * Reescreve o m�todo preDispatch()
     * Pega o cpf/cnpj via get uma �nica vez
     * @access public
     * @param void
     * @return void
     */
    public function preDispatch()
    {
        // recebe os dados via get
        $this->cpfcnpj = $this->_request->getParam("cpfcnpj");
        $this->cpfcnpj = isset($this->cpfcnpj) && !empty($this->cpfcnpj) ? $this->cpfcnpj : 0;

        if (Validacao::validarCPF($this->cpfcnpj) && strlen($this->cpfcnpj) == 11) : // cpf
            $this->queryString = '/cpfcnpj/' . $this->cpfcnpj;
        $this->view->cpfcnpj = $this->cpfcnpj; elseif (Validacao::validarCNPJ($this->cpfcnpj) && strlen($this->cpfcnpj) == 14) : // cnpj
            $this->queryString = '/cpfcnpj/' . $this->cpfcnpj;
        $this->view->cpfcnpj = $this->cpfcnpj; else :
            $this->view->cpfcnpj = '';

        endif;
    }

    // fecha m�todo preDispatch()

    /**
     * Formul�rio de consulta por cnpj/cpf
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
    }

    // fecha m�todo indexAction()

    /**
     * Cadastra as regularidades do proponente
     * @access public
     * @param void
     * @return void
     */
    public function manterregularidadeproponenteAction()
    {
        if (isset($_POST['pronacEnviado']) || isset($_GET['pronacEnviado'])) {
            $this->view->pronacEnviado = isset($_POST['pronacEnviado']) ? $_POST['pronacEnviado'] : $_GET['pronacEnviado'];
        }

        if ($this->_request->getParam("modal") == "s") {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $this->view->modal = "n";
        } else {
            $this->view->modal = "s";
        }
        $caminho = $this->_request->getParam("caminho");
        if (!empty($caminho)) {
            $this->view->caminho = $caminho;
        } else {
            $this->view->caminho = "";
        }

        if (isset($_POST['cpfCnpj']) || isset($_GET['cpfCnpj'])) {
            if (isset($_POST['cpfCnpj'])) {
                $cnpjcpf = str_replace("/", "", str_replace("-", "", str_replace(".", "", $_POST['cpfCnpj'])));
                $cnpjcpf = Mascara::delMaskCPFCNPJ($cnpjcpf);
            } elseif (isset($_GET['cpfCnpj'])) {
                $cnpjcpf = $_GET['cpfCnpj'];
                $cnpjcpf = Mascara::delMaskCPFCNPJ($cnpjcpf);
            }

            $natureza = new Natureza();
            $buscaDados = $natureza->pesquisaCEPIM($cnpjcpf);
            $this->view->habilitarCepim = 0;
            if (count($buscaDados)>0) {
                $this->view->habilitarCepim = 1;
            }

            if (empty($cnpjcpf)) {
                if ($this->_request->getParam("modal") == "s") {
                    echo "<br/><br/><br/><br/><center><font color='red'>Por favor, informe o campo CPF/CNPJ!</font></center>";
                    $this->_helper->viewRenderer->setNoRender(true);
                } else {
                    parent::message('Por favor, informe o campo CPF/CNPJ!', 'manterregularidadeproponente/index' . $this->queryString, 'ALERT');
                }
            }
            if ($this->proponente == "PF" && !Validacao::validarCPF($cnpjcpf)) {
                if ($this->_request->getParam("modal") == "s") {
                    echo "<br/><br/><br/><br/><center><font color='red'>Por favor, informe um CPF v&aacute;lido!</font></center>";
                    $this->_helper->viewRenderer->setNoRender(true);
                } else {
                    parent::message('Por favor, informe um CPF v&aacute;lido!', 'manterregularidadeproponente/index' . $this->queryString, 'ALERT');
                }
            }
            if ($this->proponente == "PJ" && !Validacao::validarCNPJ($cnpjcpf)) {
                if ($this->_request->getParam("modal") == "s") {
                    echo "<br/><br/><br/><br/><center><font color='red'>Por favor, informe um CNPJ v&aacute;lido!</font></center>";
                    $this->_helper->viewRenderer->setNoRender(true);
                } else {
                    parent::message('Por favor, informe um CNPJ v&aacute;lido!', 'manterregularidadeproponente/index' . $this->queryString, 'ALERT');
                }
            }

            $this->view->cgccpf = $_REQUEST['cpfCnpj'];
            $agentes = new Agente_Model_DbTable_Agentes();
            $interessados = new Interessado();
            $certidoesNegativas = new CertidoesNegativas();
            $buscaAgentes = $agentes->buscar(array('CNPJCPF = ?' => $cnpjcpf));

            $buscaInteressados = $interessados->buscar(array('CgcCpf = ?' => $cnpjcpf));

            if (!$buscaAgentes[0] or !$buscaInteressados[0]) {
                if ($this->_request->getParam("modal") == "s") {
                    echo "<br/><br/><br/><br/><center>O Agente n&atilde;o est&aacute; cadastrado!</font></center>";
                    $this->_helper->viewRenderer->setNoRender(true);
                } else {
                    parent::message("O Agente n&atilde;o est&aacute; cadastrado!", 'manterregularidadeproponente/index'. $this->queryString, "ERROR");
                }
            }

            $nomes = new Nomes();
            $buscaNomes = $nomes->buscar(array('idAgente = ?' => $buscaAgentes[0]->idAgente));
            $nomeProponente = $buscaNomes[0]->Descricao;
            $this->view->nomeProponente = $nomeProponente;

            $buscaCertidaoQF = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 49));
            if (!empty($buscaCertidaoQF[0])) {
                $this->view->cgccpfqf = $buscaCertidaoQF[0]->CgcCpf;
                $this->view->codigocertidaoqf = $buscaCertidaoQF[0]->CodigoCertidao;
                $this->view->dtemissaoqf = Data::tratarDataZend($buscaCertidaoQF[0]->DtEmissao, 'Brasileira');
//                    $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoQF[0]->DtValidade)), 1);
//                    $diasqf = (int) Data::CompararDatas($buscaCertidaoQF[0]->DtEmissao, Data::dataAmericana($dtValidade));
//                    $this->view->diasqf = $diasqf;
                $this->view->dtvalidadeqf = Data::tratarDataZend($buscaCertidaoQF[0]->DtValidade, 'Brasileira');
                $this->view->pronacqf = $buscaCertidaoQF[0]->AnoProjeto . $buscaCertidaoQF[0]->Sequencial;
                $this->view->logonqf = $buscaCertidaoQF[0]->Logon;
                $this->view->idcertidoesnegativasqf = $buscaCertidaoQF[0]->idCertidoesnegativas;
                $this->view->cdprotocolonegativaqf = $buscaCertidaoQF[0]->cdProtocoloNegativa;
                $this->view->cdsituacaocertidaoqf = $buscaCertidaoQF[0]->cdSituacaoCertidao;
                $this->view->idcertidaoqf = $buscaCertidaoQF[0]->idCertidoesnegativas;
                $this->view->buscarcqtf = Data::tratarDataZend($buscaCertidaoQF[0]->DtValidade, 'americano');
            } else {
                $this->view->cgccpfqf = "";
                $this->view->codigocertidaoqf = "";
                $this->view->dtemissaoqf = "";
                $this->view->dtvalidadeqf = "";
                $this->view->diasqf = "";
                $this->view->pronacqf = "";
                $this->view->logonqf = "";
                $this->view->idcertidoesnegativasqf = "";
                $this->view->cdprotocolonegativaqf = "";
                $this->view->cdsituacaocertidaoqf = "";
                $this->view->idcertidaoqf = "";
                $this->view->buscarcqtf = 'E';
            }

            $buscaCertidaoQE = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 70));
            if (!empty($buscaCertidaoQE[0])) {
                $this->view->cgccpfqe = $buscaCertidaoQE[0]->CgcCpf;
                $this->view->codigocertidaoqe = $buscaCertidaoQE[0]->CodigoCertidao;
                $this->view->dtemissaoqe = Data::tratarDataZend($buscaCertidaoQE[0]->DtEmissao, 'Brasileira');
                $this->view->dtvalidadeqe = Data::tratarDataZend($buscaCertidaoQE[0]->DtValidade, 'Brasileira');
//                    $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoQE[0]->DtValidade)), 1);
//                    $diasqe = (int) Data::CompararDatas($buscaCertidaoQE[0]->DtEmissao, Data::dataAmericana($dtValidade));
//                    $this->view->diasqe = $diasqe;
                $this->view->pronacqe = $buscaCertidaoQE[0]->AnoProjeto . $buscaCertidaoQE[0]->Sequencial;
                $this->view->logonqe = $buscaCertidaoQE[0]->Logon;
                $this->view->idcertidoesnegativasqe = $buscaCertidaoQE[0]->idCertidoesnegativas;
                $this->view->cdprotocolonegativaqe = $buscaCertidaoQE[0]->cdProtocoloNegativa;
                $this->view->cdsituacaocertidaoqe = $buscaCertidaoQE[0]->cdSituacaoCertidao;
                $this->view->idcertidaoqe = $buscaCertidaoQE[0]->idCertidoesnegativas;
            } else {
                $this->view->cgccpfqe = "";
                $this->view->codigocertidaoqe = "";
                $this->view->dtemissaoqe = "";
                $this->view->dtvalidadeqe = "";
                $this->view->diasqe = "";
                $this->view->pronacqe = "";
                $this->view->logonqe = "";
                $this->view->idcertidoesnegativasqe = "";
                $this->view->cdprotocolonegativaqe = "";
                $this->view->cdsituacaocertidaoqe = "";
                $this->view->idcertidaoqe = "";
            }

            $buscaCertidaoFGTS = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 51));
            if (!empty($buscaCertidaoFGTS[0])) {
                $this->view->cgccpffgts = $buscaCertidaoFGTS[0]->CgcCpf;
                $this->view->codigocertidaofgts = $buscaCertidaoFGTS[0]->CodigoCertidao;
                $this->view->dtemissaofgts = Data::tratarDataZend($buscaCertidaoFGTS[0]->DtEmissao, 'Brasileira');
                $this->view->dtvalidadefgts = Data::tratarDataZend($buscaCertidaoFGTS[0]->DtValidade, 'Brasileira');
//                    $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoFGTS[0]->DtValidade)), 1);
//                    $diasfgts = (int) Data::CompararDatas($buscaCertidaoFGTS[0]->DtEmissao, Data::dataAmericana($dtValidade));
//                    $this->view->diasfgts = $diasfgts;
                $this->view->pronacfgts = $buscaCertidaoFGTS[0]->AnoProjeto . $buscaCertidaoFGTS[0]->Sequencial;
                $this->view->logonfgts = $buscaCertidaoFGTS[0]->Logon;
                $this->view->idcertidoesnegativasfgts = $buscaCertidaoFGTS[0]->idCertidoesnegativas;
                $this->view->cdprotocolonegativafgts = $buscaCertidaoFGTS[0]->cdProtocoloNegativa;
                $this->view->cdsituacaocertidaofgts = $buscaCertidaoFGTS[0]->cdSituacaoCertidao;
                $this->view->idcertidaofgts = $buscaCertidaoFGTS[0]->idCertidoesnegativas;
                $this->view->buscarfgts = Data::tratarDataZend($buscaCertidaoFGTS[0]->DtValidade, 'americano');
            } else {
                $this->view->cgccpffgts = "";
                $this->view->codigocertidaofgts = "";
                $this->view->dtemissaofgts = "";
                $this->view->dtvalidadefgts = "";
                $this->view->diasfgts = "";
                $this->view->pronacfgts = "";
                $this->view->logonfgts = "";
                $this->view->idcertidoesnegativasfgts = "";
                $this->view->cdprotocolonegativafgts = "";
                $this->view->cdsituacaocertidaofgts = "";
                $this->view->idcertidaofgts = "";
                $this->view->buscarfgts = 'E';
            }

            $buscaCertidaoCADIN = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 244));
            if (!empty($buscaCertidaoCADIN[0])) {
                $this->view->cgccpfcadin = $buscaCertidaoCADIN[0]->CgcCpf;
                $this->view->codigocertidaocadin = $buscaCertidaoCADIN[0]->CodigoCertidao;
//                    $horaCadin = $buscaCertidaoCADIN[0]->DtEmissao;
//                    $horaCadin = date('H:i:s', strtotime($horaCadin));
//                    $this->view->horacadin = $horaCadin;
                $this->view->dtemissaocadin = Data::tratarDataZend($buscaCertidaoCADIN[0]->DtEmissao, 'Brasileira');
                $dtValidade = Data::somarData(Data::tratarDataZend($buscaCertidaoCADIN[0]->DtValidade, 'americano'), 1);
                $diascadin = (int) Data::CompararDatas($buscaCertidaoCADIN[0]->DtEmissao, Data::dataAmericana($dtValidade));
                $this->view->diascadin = $diascadin;
                $this->view->dtvalidadecadin = Data::tratarDataZend($buscaCertidaoCADIN[0]->DtValidade, 'Brasileira');
                $this->view->pronaccadin = $buscaCertidaoCADIN[0]->AnoProjeto . $buscaCertidaoCADIN[0]->Sequencial;
                $this->view->logoncadin = $buscaCertidaoCADIN[0]->Logon;
                $this->view->idcertidoesnegativascadin = $buscaCertidaoCADIN[0]->idCertidoesnegativas;
                $this->view->cdprotocolonegativacadin = $buscaCertidaoCADIN[0]->cdProtocoloNegativa;
                $this->view->idcertidaocadin = $buscaCertidaoCADIN[0]->idCertidoesnegativas;
                $this->view->buscarcadin = $buscaCertidaoCADIN;

                if ($buscaCertidaoCADIN[0]->cdSituacaoCertidao == 1) {
                    $this->view->cdsituacaocertidaocadin = "N&atilde;o pendente";
                } else {
                    $this->view->cdsituacaocertidaocadin = "Pendente";
                }
            } else {
                $this->view->cgccpfcadin = "";
                $this->view->codigocertidaocadin = "";
                $this->view->dtemissaocadin = "";
                $this->view->dtvalidadecadin = "";
                $this->view->horacadin = "";
                $this->view->diascadin = "";
                $this->view->pronaccadin = "";
                $this->view->logoncadin = "";
                $this->view->idcertidoesnegativascadin = "";
                $this->view->cdprotocolonegativacadin = "";
                $this->view->cdsituacaocertidaocadin = "Selecione";
                $this->view->idcertidaocadin = "";
                $this->view->buscarcadin = null;
            }

            /*$buscaCertidaoCEPIM = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 247));
            if (!empty($buscaCertidaoCEPIM[0])) {
                $this->view->cgccpfcepim = $buscaCertidaoCEPIM[0]->CgcCpf;
                $this->view->codigocertidaocepim = $buscaCertidaoCEPIM[0]->CodigoCertidao;
                $horaCepim = $buscaCertidaoCEPIM[0]->DtEmissao;
                $horaCepim = date('H:i:s', strtotime($horaCepim));
                $this->view->horacepim = $horaCepim;
                $dtEmissaoCepimFormatada = date("d/m/Y", strtotime($buscaCertidaoCEPIM[0]->DtEmissao));
                $this->view->dtemissaocepim = $dtEmissaoCepimFormatada;
                $dtValidadeCepimFormatada = date("d/m/Y", strtotime($buscaCertidaoCEPIM[0]->DtValidade));
                $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoCEPIM[0]->DtValidade)), 1);
                $diascepim = (int) Data::CompararDatas($buscaCertidaoCEPIM[0]->DtEmissao, Data::dataAmericana($dtValidade));
                $this->view->diascepim = $diascepim;
                $this->view->dtvalidadecepim = $dtValidadeCepimFormatada;
                $this->view->pronaccepim = $buscaCertidaoCEPIM[0]->AnoProjeto . $buscaCertidaoCEPIM[0]->Sequencial;
                $this->view->logoncepim = $buscaCertidaoCEPIM[0]->Logon;
                $this->view->idcertidoesnegativascepim = $buscaCertidaoCEPIM[0]->idCertidoesnegativas;
                $this->view->cdprotocolonegativacepim = $buscaCertidaoCEPIM[0]->cdProtocoloNegativa;
                $this->view->idcertidaocepim = $buscaCertidaoCEPIM[0]->idCertidoesnegativas;

                if ($buscaCertidaoCEPIM[0]->cdSituacaoCertidao == 1) {
                    $this->view->cdsituacaocertidaocepim = "N&atilde;o pendente";
                } else {
                    $this->view->cdsituacaocertidaocepim = "Pendente";
                }
            } else {
                $this->view->cgccpfcepim = "";
                $this->view->codigocertidaocepim = "";
                $this->view->dtemissaocepim = "";
                $this->view->dtvalidadecepim = "";
                $this->view->horacepim = "";
                $this->view->diascepim = "";
                $this->view->pronaccepim = "";
                $this->view->logoncepim = "";
                $this->view->idcertidoesnegativascepim = "";
                $this->view->cdprotocolonegativacepim = "";
                $this->view->cdsituacaocertidaocepim = "Selecione";
                $this->view->idcertidaocepim = "";
            }*/

            $buscaCertidaoINSS = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 52));

            if (!empty($buscaCertidaoINSS[0])) {
                $this->view->cgccpfinss = $buscaCertidaoINSS[0]->CgcCpf;
                $this->view->codigocertidaoinss = $buscaCertidaoINSS[0]->CodigoCertidao;
                $this->view->dtemissaoinss = Data::tratarDataZend($buscaCertidaoINSS[0]->DtEmissao, 'Brasileira');
//                    $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoINSS[0]->DtValidade)), 1);
//                    $diasinss = (int) Data::CompararDatas($buscaCertidaoINSS[0]->DtEmissao, Data::dataAmericana($dtValidade));
//                    $this->view->diasinss = $diasinss;
                $this->view->dtvalidadeinss = Data::tratarDataZend($buscaCertidaoINSS[0]->DtValidade, 'Brasileira');
                $this->view->pronacinss = $buscaCertidaoINSS[0]->AnoProjeto . $buscaCertidaoINSS[0]->Sequencial;
                $this->view->logoninss = $buscaCertidaoINSS[0]->Logon;
                $this->view->idcertidoesnegativasinss = $buscaCertidaoINSS[0]->idCertidoesnegativas;
                $this->view->cdprotocolonegativainss = $buscaCertidaoINSS[0]->cdProtocoloNegativa;
                $this->view->cdsituacaocertidaoinss = $buscaCertidaoINSS[0]->cdSituacaoCertidao;
                $this->view->idcertidaoinss = $buscaCertidaoINSS[0]->idCertidoesnegativas;
                $this->view->buscarinss = Data::tratarDataZend($buscaCertidaoINSS[0]->DtValidade, 'americano');
            } else {
                $this->view->cgccpfinss = "";
                $this->view->codigocertidaoinss = "";
                $this->view->dtemissaoinss = "";
                $this->view->dtvalidadeinss = "";
                $this->view->diasinss = "";
                $this->view->pronacinss = "";
                $this->view->logoninss = "";
                $this->view->idcertidoesnegativasinss = "";
                $this->view->cdprotocolonegativainss = "";
                $this->view->cdsituacaocertidaoinss = "";
                $this->view->idcertidaoinss = "";
                $this->view->buscarinss = 'E';
            }
            //}
        }
    }

    public function buscapronacAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        if (!empty($idPronac)) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            //$idPronac = $_GET['idPronac'];
            $buscaPronac = ManterRegularidadeProponenteDAO::buscaPronac($idPronac);

            if (!empty($buscaPronac)) {
                $result['existe'] = true;
                $this->_helper->json($result);
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                $result['existe'] = false;
                $this->_helper->json($result);
                $this->_helper->viewRenderer->setNoRender(true);
            }
        } else {
            $result['existe'] = true;
            $this->_helper->json($result);
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function salvacertidaoAction()
    {
        $auth = Zend_Auth::getInstance();
        $usu_codigo = $auth->getIdentity()->usu_codigo;
        $verificaqf = $this->_request->getParam("verificaqf");
        $verificafgts = $this->_request->getParam("verificafgts");
        $verificainss = $this->_request->getParam("verificainss");
        $verificacadin = $this->_request->getParam("verificacadin");
        $verificacepim = $this->_request->getParam("verificacepim");
        $cgccpf = Mascara::delMaskCNPJ($this->_request->getParam("cgcCpf"));

        $certidoesNegativas = new CertidoesNegativas();

        if ($verificaqf == 1) {
            $pronac = $this->_request->getParam("quitacaoFederalProjeto");
            $ano = addslashes(substr($pronac, 0, 2));
            $sequencial = addslashes(substr($pronac, 2, strlen($pronac)));
            $dtEmissao = Data::dataAmericana($this->_request->getParam("txtDtFut"));
            $dtValidade = Data::dataAmericana($this->_request->getParam("quitacaoFederalValidade"));
            $cdProtocoloNegativa = $this->_request->getParam("quitacaoFederalProtocolo");
            $idCertidao = $this->_request->getParam("idcertidaoqf");
            $codigoCertidao = 49;

            if (!empty($idCertidao)) {
                $rsCertidao = $certidoesNegativas->buscar(array("idCertidoesnegativas = ?" => $idCertidao))->current();
                $rsCertidao->CgcCpf = $cgccpf;
                $rsCertidao->CodigoCertidao = $codigoCertidao;
                $rsCertidao->DtEmissao = $dtEmissao;
                $rsCertidao->DtValidade = $dtValidade;
                $rsCertidao->AnoProjeto = $ano;
                $rsCertidao->Sequencial = $sequencial;
                $rsCertidao->Logon = $usu_codigo;
                $rsCertidao->cdProtocoloNegativa = $cdProtocoloNegativa;
                $rsCertidao->cdSituacaoCertidao = null;
                $rsCertidao->save();
            } else {
                $arrayqf = array(
                    'CgcCpf' => $cgccpf,
                    'CodigoCertidao' => $codigoCertidao,
                    'DtEmissao' => $dtEmissao,
                    'DtValidade' => $dtValidade,
                    'AnoProjeto' => $ano,
                    'Sequencial' => $sequencial,
                    'Logon' => $usu_codigo,
                    'cdProtocoloNegativa' => $cdProtocoloNegativa,
                    'cdSituacaoCertidao' => null
                );
                $insereCertidao = $certidoesNegativas->inserir($arrayqf);
            }
        }

        if ($verificafgts == 1) {
            $pronac = $this->_request->getParam("quitacaoFGTSProjeto");
            $ano = addslashes(substr($pronac, 0, 2));
            $sequencial = addslashes(substr($pronac, 2, strlen($pronac)));
            $dtEmissao = Data::dataAmericana($this->_request->getParam("txtDtFutFGTS"));
            $dtValidade = Data::dataAmericana($this->_request->getParam("quitacaoFGTSValidade"));
            $cdProtocoloNegativa = $this->_request->getParam("quitacaoFGTSProtocolo");
            $idCertidao = $this->_request->getParam("idcertidaofgts");
            $codigoCertidao = 51;


            if (!empty($idCertidao)) {
                $rsCertidao = $certidoesNegativas->buscar(array("idCertidoesnegativas = ?" => $idCertidao))->current();
                $rsCertidao->CgcCpf = $cgccpf;
                $rsCertidao->CodigoCertidao = $codigoCertidao;
                $rsCertidao->DtEmissao = $dtEmissao;
                $rsCertidao->DtValidade = $dtValidade;
                $rsCertidao->AnoProjeto = $ano;
                $rsCertidao->Sequencial = $sequencial;
                $rsCertidao->Logon = $usu_codigo;
                $rsCertidao->cdProtocoloNegativa = $cdProtocoloNegativa;
                $rsCertidao->cdSituacaoCertidao = null;
                $rsCertidao->save();
            } else {
                $arrayfgts = array(
                    'CgcCpf' => $cgccpf,
                    'CodigoCertidao' => $codigoCertidao,
                    'DtEmissao' => $dtEmissao,
                    'DtValidade' => $dtValidade,
                    'AnoProjeto' => $ano,
                    'Sequencial' => $sequencial,
                    'Logon' => $usu_codigo,
                    'cdProtocoloNegativa' => $cdProtocoloNegativa,
                    'cdSituacaoCertidao' => null
                );
                $insereCertidao = $certidoesNegativas->inserir($arrayfgts);
            }
        }


        if ($verificainss == 1) {
            $pronac = $this->_request->getParam("quitacaoINSSProjeto");
            $ano = addslashes(substr($pronac, 0, 2));
            $sequencial = addslashes(substr($pronac, 2, strlen($pronac)));
            $dtEmissao = Data::dataAmericana($this->_request->getParam("txtDtFutINSS"));
            $dtValidade = Data::dataAmericana($this->_request->getParam("quitacaoINSSValidade"));
            $cdProtocoloNegativa = $this->_request->getParam("quitacaoINSSProtocolo");
            $idCertidao = $this->_request->getParam("idcertidaoinss");
            $codigoCertidao = 52;


            if (!empty($idCertidao)) {
                $rsCertidao = $certidoesNegativas->buscar(array("idCertidoesnegativas = ?" => $idCertidao))->current();
                $rsCertidao->CgcCpf = $cgccpf;
                $rsCertidao->CodigoCertidao = $codigoCertidao;
                $rsCertidao->DtEmissao = $dtEmissao;
                $rsCertidao->DtValidade = $dtValidade;
                $rsCertidao->AnoProjeto = $ano;
                $rsCertidao->Sequencial = $sequencial;
                $rsCertidao->Logon = $usu_codigo;
                $rsCertidao->cdProtocoloNegativa = $cdProtocoloNegativa;
                $rsCertidao->cdSituacaoCertidao = null;
                $rsCertidao->save();
            } else {
                $arrayinss = array(
                    'CgcCpf' => $cgccpf,
                    'CodigoCertidao' => $codigoCertidao,
                    'DtEmissao' => $dtEmissao,
                    'DtValidade' => $dtValidade,
                    'AnoProjeto' => $ano,
                    'Sequencial' => $sequencial,
                    'Logon' => $usu_codigo,
                    'cdProtocoloNegativa' => $cdProtocoloNegativa,
                    'cdSituacaoCertidao' => null
                );
                $insereCertidao = $certidoesNegativas->inserir($arrayinss);
            }
        }

        if ($verificacadin == 1) {
            $pronac = $this->_request->getParam("quitacaoCADINProjeto");
            $ano = addslashes(substr($pronac, 0, 2));
            $sequencial = addslashes(substr($pronac, 2, strlen($pronac)));
            $dtEmissao = Data::dataAmericana($this->_request->getParam("txtDtFutCADIN"));
            $dtValidade = '';
            $cdProtocoloNegativa = $this->_request->getParam("quitacaoCADINProtocolo");
            $cdSituacaoCertidao = $this->_request->getParam("quitacaoCADINSituacao");
            $hora = $this->_request->getParam("quitacaoCADINHora");
            $idCertidao = $this->_request->getParam("idcertidaocadin");
            $codigoCertidao = 244;

            if (!empty($idCertidao)) {
                $rsCertidao = $certidoesNegativas->buscar(array("idCertidoesnegativas = ?" => $idCertidao))->current();
                $rsCertidao->CgcCpf = $cgccpf;
                $rsCertidao->CodigoCertidao = $codigoCertidao;
                $rsCertidao->DtEmissao = $dtEmissao . " " . $hora;
                $rsCertidao->DtValidade = $dtValidade;
                $rsCertidao->AnoProjeto = $ano;
                $rsCertidao->Sequencial = $sequencial;
                $rsCertidao->Logon = $usu_codigo;
                $rsCertidao->cdProtocoloNegativa = $cdProtocoloNegativa;
                $rsCertidao->cdSituacaoCertidao = $cdSituacaoCertidao;
                $rsCertidao->save();
            } else {
                $arraycadin = array(
                    'CgcCpf' => $cgccpf,
                    'CodigoCertidao' => $codigoCertidao,
                    'DtEmissao' => $dtEmissao . " " . $hora,
                    'DtValidade' => $dtValidade,
                    'AnoProjeto' => $ano,
                    'Sequencial' => $sequencial,
                    'Logon' => $usu_codigo,
                    'cdProtocoloNegativa' => $cdProtocoloNegativa,
                    'cdSituacaoCertidao' => $cdSituacaoCertidao
                );
                $insereCertidao = $certidoesNegativas->inserir($arraycadin);
            }
        }

        if ($verificacepim == 1) {
            $pronac = $this->_request->getParam("quitacaoCEPIMProjeto");
            $ano = addslashes(substr($pronac, 0, 2));
            $sequencial = addslashes(substr($pronac, 2, strlen($pronac)));
            $dtEmissao = Data::dataAmericana($this->_request->getParam("txtDtFutCEPIM"));
            $dtValidade = '';
            $cdProtocoloNegativa = $this->_request->getParam("quitacaoCEPIMProtocolo");
            $cdSituacaoCertidao = $this->_request->getParam("quitacaoCEPIMSituacao");
            $hora = $this->_request->getParam("quitacaoCEPIMHora");
            $idCertidao = $this->_request->getParam("idcertidaocepim");
            $codigoCertidao = 247;

            if (!empty($idCertidao)) {
                $rsCertidao = $certidoesNegativas->buscar(array("idCertidoesnegativas = ?" => $idCertidao))->current();
                $rsCertidao->CgcCpf = $cgccpf;
                $rsCertidao->CodigoCertidao = $codigoCertidao;
                $rsCertidao->DtEmissao = $dtEmissao . " " . $hora;
                $rsCertidao->DtValidade = $dtValidade;
                $rsCertidao->AnoProjeto = $ano;
                $rsCertidao->Sequencial = $sequencial;
                $rsCertidao->Logon = $usu_codigo;
                $rsCertidao->cdProtocoloNegativa = $cdProtocoloNegativa;
                $rsCertidao->cdSituacaoCertidao = $cdSituacaoCertidao;
                $rsCertidao->save();
            } else {
                $arraycepim = array(
                    'CgcCpf' => $cgccpf,
                    'CodigoCertidao' => $codigoCertidao,
                    'DtEmissao' => $dtEmissao . " " . $hora,
                    'DtValidade' => $dtValidade,
                    'AnoProjeto' => $ano,
                    'Sequencial' => $sequencial,
                    'Logon' => $usu_codigo,
                    'cdProtocoloNegativa' => $cdProtocoloNegativa,
                    'cdSituacaoCertidao' => $cdSituacaoCertidao
                );
                $insereCertidao = $certidoesNegativas->inserir($arraycepim);
            }
        }

        $caminho = $this->_request->getParam("caminho");
        if (!empty($caminho)) {
            parent::message("Cadastro realizado com sucesso!", $caminho, "CONFIRM");
        } else {
            if (isset($_POST['pronacEnviado']) || isset($_GET['pronacEnviado'])) {
                $pronacEnviado = isset($_POST['pronacEnviado']) ? $_POST['pronacEnviado'] : $_GET['pronacEnviado'];
                parent::message("Cadastro realizado com sucesso!", "/manterregularidadeproponente/manterregularidadeproponente?cpfCnpj=" . $cgccpf . "&pronacEnviado=" .$pronacEnviado, "CONFIRM");
            } else {
                parent::message("Cadastro realizado com sucesso!", "/manterregularidadeproponente/manterregularidadeproponente?cpfCnpj=" . $cgccpf, "CONFIRM");
            }
        }
    }

    public function imprimirAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        if (isset($_POST['cpfCnpj']) || isset($_GET['cpfCnpj'])) {
            if (isset($_POST['cpfCnpj'])) {
                $cnpjcpf = str_replace("/", "", str_replace("-", "", str_replace(".", "", $_POST['cpfCnpj'])));
                $cnpjcpf = Mascara::delMaskCPFCNPJ($cnpjcpf);
            } elseif (isset($_GET['cpfCnpj'])) {
                $cnpjcpf = $_GET['cpfCnpj'];
                $cnpjcpf = Mascara::delMaskCPFCNPJ($cnpjcpf);
            }

            $natureza = new Natureza();
            $buscaDados = $natureza->pesquisaCEPIM($cnpjcpf);
            $this->view->habilitarCepim = 0;
            if (count($buscaDados)>0) {
                $this->view->habilitarCepim = 1;
            }

            if (empty($cnpjcpf)) {
                parent::message('Por favor, informe o campo CPF/CNPJ!', 'manterregularidadeproponente/index' . $this->queryString, 'ALERT');
            } elseif (strlen($cnpjcpf) <= 11 && !Validacao::validarCPF($cnpjcpf)) {
                parent::message('Por favor, informe um CPF v&aacute;lido!', 'manterregularidadeproponente/index' . $this->queryString, 'ALERT');
            } elseif (strlen($cnpjcpf) > 11 && !Validacao::validarCNPJ($cnpjcpf)) {
                parent::message('Por favor, informe um CNPJ v&aacute;lido!', 'manterregularidadeproponente/index' . $this->queryString, 'ALERT');
            } else {
                $this->view->cgccpf = $_REQUEST['cpfCnpj'];
                $agentes                        = new Agente_Model_DbTable_Agentes();
                $nomes                          = new Nomes();
                $interessados                   = new Interessado();
                $certidoesNegativas             = new CertidoesNegativas();
//                $tblProjeto                     = New Projetos();
                $buscaAgentes                   = $agentes->buscar(array('CNPJCPF = ?' => $cnpjcpf));
                $idAgente                       = $buscaAgentes[0]->idAgente;
                $buscaNomeProponente            = $nomes->buscar(array('idAgente = ?' => $idAgente));
                $nomeProponente                 = $buscaNomeProponente[0]->Descricao;
                $this->view->cgccpf             = $cnpjcpf;
                $this->view->nomeproponente     = $nomeProponente;
//                $this->view->NrProjeto          = $rst[0]->NrProjeto;
//                $this->view->NomeProjeto        = $tblProjetos->buscarTodosDadosProjeto(array('CgcCpf = ?' => $buscaAgentes));

//                $rsProjeto = $tblProjeto->buscar(array("idPronac = ?"=>$get->idPronac))->current();
//                $this->view->projeto = $rsProjeto;

                $buscaInteressados = $interessados->buscar(array('CgcCpf = ?' => $cnpjcpf));

                $buscaCertidaoQF = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 49));

                if (!empty($buscaCertidaoQF[0])) {
                    $this->view->cgccpfqf = $buscaCertidaoQF[0]->CgcCpf;
                    $this->view->codigocertidaoqf = $buscaCertidaoQF[0]->CodigoCertidao;
                    $this->view->dtemissaoqf = Data::tratarDataZend($buscaCertidaoQF[0]->DtEmissao, 'Brasileira');
//                    $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoQF[0]->DtValidade)), 1);
//                    $diasqf = (int) Data::CompararDatas($buscaCertidaoQF[0]->DtEmissao, Data::dataAmericana($dtValidade));
//                    $this->view->diasqf = $diasqf;
                    $this->view->dtvalidadeqf = Data::tratarDataZend($buscaCertidaoQF[0]->DtValidade, 'Brasileira');
                    $this->view->pronacqf = $buscaCertidaoQF[0]->AnoProjeto . $buscaCertidaoQF[0]->Sequencial;
                    $this->view->logonqf = $buscaCertidaoQF[0]->Logon;
                    $this->view->idcertidoesnegativasqf = $buscaCertidaoQF[0]->idCertidoesnegativas;
                    $this->view->cdprotocolonegativaqf = $buscaCertidaoQF[0]->cdProtocoloNegativa;
                    $this->view->cdsituacaocertidaoqf = $buscaCertidaoQF[0]->cdSituacaoCertidao;
                    $this->view->idcertidaoqf = $buscaCertidaoQF[0]->idCertidoesnegativas;
                    $this->view->buscarcqtf = Data::tratarDataZend($buscaCertidaoQF[0]->DtValidade, 'americano');
                } else {
                    $this->view->cgccpfqf = "";
                    $this->view->codigocertidaoqf = "";
                    $this->view->dtemissaoqf = "";
                    $this->view->dtvalidadeqf = "";
                    $this->view->diasqf = "";
                    $this->view->pronacqf = "";
                    $this->view->logonqf = "";
                    $this->view->idcertidoesnegativasqf = "";
                    $this->view->cdprotocolonegativaqf = "";
                    $this->view->cdsituacaocertidaoqf = "";
                    $this->view->idcertidaoqf = "";
                    $this->view->buscarcqtf = "E";
                }

                $buscaCertidaoQE = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 70));
                if (!empty($buscaCertidaoQE[0])) {
                    $this->view->cgccpfqe = $buscaCertidaoQE[0]->CgcCpf;
                    $this->view->codigocertidaoqe = $buscaCertidaoQE[0]->CodigoCertidao;
                    $this->view->dtemissaoqe = Data::tratarDataZend($buscaCertidaoQE[0]->DtEmissao, 'Brasileira');
                    $this->view->dtvalidadeqe = Data::tratarDataZend($buscaCertidaoQE[0]->DtValidade, 'Brasileira');
//                    $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoQE[0]->DtValidade)), 1);
//                    $diasqe = (int) Data::CompararDatas($buscaCertidaoQE[0]->DtEmissao, Data::dataAmericana($dtValidade));
//                    $this->view->diasqe = $diasqe;
                    $this->view->pronacqe = $buscaCertidaoQE[0]->AnoProjeto . $buscaCertidaoQE[0]->Sequencial;
                    $this->view->logonqe = $buscaCertidaoQE[0]->Logon;
                    $this->view->idcertidoesnegativasqe = $buscaCertidaoQE[0]->idCertidoesnegativas;
                    $this->view->cdprotocolonegativaqe = $buscaCertidaoQE[0]->cdProtocoloNegativa;
                    $this->view->cdsituacaocertidaoqe = $buscaCertidaoQE[0]->cdSituacaoCertidao;
                    $this->view->idcertidaoqe = $buscaCertidaoQE[0]->idCertidoesnegativas;
                } else {
                    $this->view->cgccpfqe = "";
                    $this->view->codigocertidaoqe = "";
                    $this->view->dtemissaoqe = "";
                    $this->view->dtvalidadeqe = "";
                    $this->view->diasqe = "";
                    $this->view->pronacqe = "";
                    $this->view->logonqe = "";
                    $this->view->idcertidoesnegativasqe = "";
                    $this->view->cdprotocolonegativaqe = "";
                    $this->view->cdsituacaocertidaoqe = "";
                    $this->view->idcertidaoqe = "";
                }

                $buscaCertidaoFGTS = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 51));

                if (!empty($buscaCertidaoFGTS[0])) {
                    $this->view->cgccpffgts = $buscaCertidaoFGTS[0]->CgcCpf;
                    $this->view->codigocertidaofgts = $buscaCertidaoFGTS[0]->CodigoCertidao;
                    $this->view->dtemissaofgts = Data::tratarDataZend($buscaCertidaoFGTS[0]->DtEmissao, 'Brasileira');
                    $this->view->dtvalidadefgts = Data::tratarDataZend($buscaCertidaoFGTS[0]->DtValidade, 'Brasileira');
//                    $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoFGTS[0]->DtValidade)), 1);
//                    $diasfgts = (int) Data::CompararDatas($buscaCertidaoFGTS[0]->DtEmissao, Data::dataAmericana($dtValidade));
//                    $this->view->diasfgts = $diasfgts;
                    $this->view->pronacfgts = $buscaCertidaoFGTS[0]->AnoProjeto . $buscaCertidaoFGTS[0]->Sequencial;
                    $this->view->logonfgts = $buscaCertidaoFGTS[0]->Logon;
                    $this->view->idcertidoesnegativasfgts = $buscaCertidaoFGTS[0]->idCertidoesnegativas;
                    $this->view->cdprotocolonegativafgts = $buscaCertidaoFGTS[0]->cdProtocoloNegativa;
                    $this->view->cdsituacaocertidaofgts = $buscaCertidaoFGTS[0]->cdSituacaoCertidao;
                    $this->view->idcertidaofgts = $buscaCertidaoFGTS[0]->idCertidoesnegativas;
                    $this->view->buscarfgts = Data::tratarDataZend($buscaCertidaoFGTS[0]->DtValidade, 'americano');
                } else {
                    $this->view->cgccpffgts = "";
                    $this->view->codigocertidaofgts = "";
                    $this->view->dtemissaofgts = "";
                    $this->view->dtvalidadefgts = "";
                    $this->view->diasfgts = "";
                    $this->view->pronacfgts = "";
                    $this->view->logonfgts = "";
                    $this->view->idcertidoesnegativasfgts = "";
                    $this->view->cdprotocolonegativafgts = "";
                    $this->view->cdsituacaocertidaofgts = "";
                    $this->view->idcertidaofgts = "";
                    $this->view->buscarfgts = "E";
                }

                $buscaCertidaoCADIN = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 244));

                if (!empty($buscaCertidaoCADIN[0])) {
                    $this->view->cgccpfcadin = $buscaCertidaoCADIN[0]->CgcCpf;
                    $this->view->codigocertidaocadin = $buscaCertidaoCADIN[0]->CodigoCertidao;
//                    $horaCadin = $buscaCertidaoCADIN[0]->DtEmissao;
//                    $horaCadin = date('H:i:s', strtotime($horaCadin));
//                    $this->view->horacadin = $horaCadin;
                    $this->view->dtemissaocadin = Data::tratarDataZend($buscaCertidaoCADIN[0]->DtEmissao, 'Brasileira');
                    $dtValidade = Data::somarData(Data::tratarDataZend($buscaCertidaoCADIN[0]->DtValidade, 'americano'), 1);
                    $diascadin = (int) Data::CompararDatas($buscaCertidaoCADIN[0]->DtEmissao, Data::dataAmericana($dtValidade));
                    $this->view->diascadin = $diascadin;
                    $this->view->dtvalidadecadin = Data::tratarDataZend($buscaCertidaoCADIN[0]->DtValidade, 'Brasileira');
                    $this->view->pronaccadin = $buscaCertidaoCADIN[0]->AnoProjeto . $buscaCertidaoCADIN[0]->Sequencial;
                    $this->view->logoncadin = $buscaCertidaoCADIN[0]->Logon;
                    $this->view->idcertidoesnegativascadin = $buscaCertidaoCADIN[0]->idCertidoesnegativas;
                    $this->view->cdprotocolonegativacadin = $buscaCertidaoCADIN[0]->cdProtocoloNegativa;
                    $this->view->idcertidaocadin = $buscaCertidaoCADIN[0]->idCertidoesnegativas;
                    $this->view->buscarcadin = $buscaCertidaoCADIN;

                    if ($buscaCertidaoCADIN[0]->cdSituacaoCertidao == 1) {
                        $this->view->cdsituacaocertidaocadin = "N&atilde;o pendente";
                    } else {
                        $this->view->cdsituacaocertidaocadin = "Pendente";
                    }
                } else {
                    $this->view->cgccpfcadin = "";
                    $this->view->codigocertidaocadin = "";
                    $this->view->dtemissaocadin = "";
                    $this->view->dtvalidadecadin = "";
                    $this->view->horacadin = "";
                    $this->view->diascadin = "";
                    $this->view->pronaccadin = "";
                    $this->view->logoncadin = "";
                    $this->view->idcertidoesnegativascadin = "";
                    $this->view->cdprotocolonegativacadin = "";
                    $this->view->cdsituacaocertidaocadin = "Selecione";
                    $this->view->idcertidaocadin = "";
                    $this->view->buscarcadin = null;
                }

                $buscaCertidaoCEPIM = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 247));

                if (!empty($buscaCertidaoCEPIM[0])) {
                    $this->view->cgccpfcepim = $buscaCertidaoCEPIM[0]->CgcCpf;
                    $this->view->codigocertidaocepim = $buscaCertidaoCEPIM[0]->CodigoCertidao;
                    $horaCepim = $buscaCertidaoCEPIM[0]->DtEmissao;
                    $horaCepim = date('H:i:s', strtotime($horaCepim));
                    $this->view->horacepim = $horaCepim;
                    $this->view->dtemissaocepim = Data::tratarDataZend($buscaCertidaoCEPIM[0]->DtEmissao, 'Brasileira');
//                    $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoCEPIM[0]->DtValidade)), 1);
//                    $diascepim = (int) Data::CompararDatas($buscaCertidaoCEPIM[0]->DtEmissao, Data::dataAmericana($dtValidade));
//                    $this->view->diascepim = $diascepim;
                    $this->view->dtvalidadecepim = Data::tratarDataZend($buscaCertidaoCEPIM[0]->DtValidade, 'Brasileira');
                    $this->view->pronaccepim = $buscaCertidaoCEPIM[0]->AnoProjeto . $buscaCertidaoCEPIM[0]->Sequencial;
                    $this->view->logoncepim = $buscaCertidaoCEPIM[0]->Logon;
                    $this->view->idcertidoesnegativascepim = $buscaCertidaoCEPIM[0]->idCertidoesnegativas;
                    $this->view->cdprotocolonegativacepim = $buscaCertidaoCEPIM[0]->cdProtocoloNegativa;
                    $this->view->idcertidaocepim = $buscaCertidaoCEPIM[0]->idCertidoesnegativas;

                    if ($buscaCertidaoCEPIM[0]->cdSituacaoCertidao == 1) {
                        $this->view->cdsituacaocertidaocepim = "N&atilde;o pendente";
                    } else {
                        $this->view->cdsituacaocertidaocepim = "Pendente";
                    }
                } else {
                    $this->view->cgccpfcepim = "";
                    $this->view->codigocertidaocepim = "";
                    $this->view->dtemissaocepim = "";
                    $this->view->dtvalidadecepim = "";
                    $this->view->horacepim = "";
                    $this->view->diascepim = "";
                    $this->view->pronaccepim = "";
                    $this->view->logoncepim = "";
                    $this->view->idcertidoesnegativascepim = "";
                    $this->view->cdprotocolonegativacepim = "";
                    $this->view->cdsituacaocertidaocepim = "Selecione";
                    $this->view->idcertidaocepim = "";
                }

                $buscaCertidaoINSS = $certidoesNegativas->buscar(array('CgcCpf = ?' => $cnpjcpf, 'CodigoCertidao = ?' => 52));

                if (!empty($buscaCertidaoINSS[0])) {
                    $this->view->cgccpfinss = $buscaCertidaoINSS[0]->CgcCpf;
                    $this->view->codigocertidaoinss = $buscaCertidaoINSS[0]->CodigoCertidao;
                    $this->view->dtemissaoinss = Data::tratarDataZend($buscaCertidaoINSS[0]->DtEmissao, 'Brasileira');
//                    $dtValidade = Data::somarData(date('Y-m-d', strtotime($buscaCertidaoINSS[0]->DtValidade)), 1);
//                    $diasinss = (int) Data::CompararDatas($buscaCertidaoINSS[0]->DtEmissao, Data::dataAmericana($dtValidade));
//                    $this->view->diasinss = $diasinss;
                    $this->view->dtvalidadeinss = Data::tratarDataZend($buscaCertidaoINSS[0]->DtValidade, 'Brasileira');
                    $this->view->pronacinss = $buscaCertidaoINSS[0]->AnoProjeto . $buscaCertidaoINSS[0]->Sequencial;
                    $this->view->logoninss = $buscaCertidaoINSS[0]->Logon;
                    $this->view->idcertidoesnegativasinss = $buscaCertidaoINSS[0]->idCertidoesnegativas;
                    $this->view->cdprotocolonegativainss = $buscaCertidaoINSS[0]->cdProtocoloNegativa;
                    $this->view->cdsituacaocertidaoinss = $buscaCertidaoINSS[0]->cdSituacaoCertidao;
                    $this->view->idcertidaoinss = $buscaCertidaoINSS[0]->idCertidoesnegativas;
                    $this->view->buscarinss = Data::tratarDataZend($buscaCertidaoINSS[0]->DtValidade, 'americano');
                } else {
                    $this->view->cgccpfinss = "";
                    $this->view->codigocertidaoinss = "";
                    $this->view->dtemissaoinss = "";
                    $this->view->dtvalidadeinss = "";
                    $this->view->diasinss = "";
                    $this->view->pronacinss = "";
                    $this->view->logoninss = "";
                    $this->view->idcertidoesnegativasinss = "";
                    $this->view->cdprotocolonegativainss = "";
                    $this->view->cdsituacaocertidaoinss = "";
                    $this->view->idcertidaoinss = "";
                    $this->view->buscarinss = "E";
                }


                if (!$buscaAgentes[0] or !$buscaInteressados[0]) {
                    if ($this->cpfcnpj != 0) {
                        parent::message("O agente n&atilde;o est&aacute; cadastrado!", "liberarcontabancaria/index" . $this->queryString, "ERROR");
                    }
                    parent::message("O agente n&atilde;o est&aacute; cadastrado!", "manterregularidadeproponente/index" . $this->queryString, "ERROR");
                }
            }
        } else {
            parent::message('Dados obrigat&oacute;rios n&atilde;o informados!', 'manterregularidadeproponente/index' . $this->queryString, 'ERROR');
        }
    }

    public function excluirCertidaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $post = Zend_Registry::get('post');

        $CertidoesNegativas = new CertidoesNegativas();
        $exclusao = $CertidoesNegativas->delete(array('CgcCpf = ?' => $post->cpfcnpj, 'CodigoCertidao = ?' => $post->cod));
        if ($exclusao) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function datadiffAction()
    {
        $dtIni = $this->_request->getParam("dtIni");
        $dtFim = $this->_request->getParam("dtFim");
        $intervalo = "d";

        $dtIni = Data::dataAmericana($dtIni);
        $dtFim = Data::dataAmericana($dtFim);

        switch ($intervalo) {
            case 'y':
                $Q = 86400 * 365;
                break; //ano
            case 'm':
                $Q = 2592000;
                break; //mes
            case 'd':
                $Q = 86400;
                break; //dia
            case 'h':
                $Q = 3600;
                break; //hora
            case 'n':
                $Q = 60;
                break; //minuto
            default:
                $Q = 1;
                break; //segundo
        }
        $dias = round((strtotime($dtIni) - strtotime($dtFim)) / $Q);

        if (!empty($dias)) {
            $result['existe'] = true;
            $result['dias'] = $dias;
            $this->_helper->json($result);
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $result['existe'] = false;
            $this->_helper->json($result);
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function calculadataemissaoduracaoAction()
    {
        $tipo = $this->_request->getParam("tipo");
        $duracao = $this->_request->getParam("duracao");
        $dtEmissao = $this->_request->getParam("dtEmissao");

        if (empty($dtEmissao) && strlen($dtEmissao) < 10) {
            $result['data'] = false;
            $this->_helper->json($result);
            $this->_helper->json($result);
        }

        if (!empty($duracao)) {
            $objData = new Zend_Date($dtEmissao);
            $objData->addDay($duracao);
            $data = date('d/m/Y', $objData->getTimestamp());

            if (!empty($data)) {
                $result['existe'] = true;
                $result['data'] = $data;

                $this->_helper->json($result);
            } else {
                $result['data'] = false;
                $this->_helper->json($result);
            }
        }

        $arrDtTemp = explode("/", $dtEmissao);
        $dtTemp = $arrDtTemp[2] . $arrDtTemp[1] . $arrDtTemp[0];
        $result = array();
        if ($dtTemp > date("Ymd")) {
            $result['error'] = true;
            $result['msg'] = utf8_encode("Data de emiss&atilde;o deve ser menor ou igual a data atual");
            $this->_helper->json($result);
        } else {
            if (!empty($tipo)) {
                if ($tipo == 1) {
                    $qtdDias = $this->CQTF;
                } elseif ($tipo == 2) {
                    $qtdDias = $this->INSS;
                } else {
                    $qtdDias = $this->FGTS;
                }
            }

            $dtEmissao = $arrDtTemp[0].'-'.$arrDtTemp[1].'-'.$arrDtTemp[2];
            $dtEmissao = date('d/m/Y', strtotime("+".$qtdDias." days", strtotime($dtEmissao)));

            if (!empty($dtEmissao)) {
                $result['existe'] = true;
                $result['error'] = false;
                $result['data'] = $dtEmissao;
                $this->_helper->json($result);
            } else {
                $result['data'] = false;
                $result['error'] = false;
                $this->_helper->json($result);
            }
        }
    }
}
