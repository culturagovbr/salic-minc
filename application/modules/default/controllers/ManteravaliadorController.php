<?php

class ManteravaliadorController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p&aacute;gina
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario = new UsuarioDAO(); // objeto usu�rio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usu�rio estja autenticado
            // verifica as permiss�es
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 114; //Coordenador de Editais

            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc� Não tem permiss�o para acessar essa �rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org�os e grupos do usu�rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a vis�o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o
        } // fecha if
        else {
            // caso o usu�rio Não esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    public function indexAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idorgao = $auth->getIdentity()->usu_orgao;
        //$this->_redirect("tramitarprojetos/despacharprojetos");

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->view->codGrupo = $codGrupo;

        /*         * *************************************************************** */
    }

    public function cadastraravaliadorAction()
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

        /*         * *************************************************************** */
    }

    public function manteravaliadorAction()
    {

        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codGrupo = $codGrupo;
        $this->view->codOrgao = $codOrgao;

        $Orgao = new Orgaos();

        $NomeOrgao = $Orgao->pesquisarNomeOrgao($codOrgao);
        $this->view->nomeOrgao = $NomeOrgao;

        /*         * *************************************************************** */

        if (isset($_POST['cpf']) || isset($_GET['cpf'])) {
            if (isset($_POST['cpf'])) {
                $cpf = $_POST['cpf'];
                $this->view->cpf = $cpf;
            } elseif (isset($_GET['cpf'])) {
                $cpf = $_GET['cpf'];
                $this->view->cpf = $cpf;
            }
            //			xd($cpf);
            $buscaIdAgente = ManterAvaliadorDAO::buscaIdAgente($cpf);
            $this->view->buscaIdAgente = $buscaIdAgente;
            if (!empty($buscaIdAgente[0])) {
                foreach ($buscaIdAgente as $idAgente) {
                    $idAgente = $idAgente->idAgente;
                }
                $avaliador = ManterAvaliadorDAO::buscaAvaliador($cpf, $idAgente);
                $this->view->dadosAvaliador = $avaliador
                ;
                $avaliador = ManterAvaliadorDAO::buscaAvaliador($cpf, $idAgente);
                $this->view->nomeAvaliador = $avaliador[0]->nome;

                $this->view->idAgente = $idAgente;
                if ($idAgente) {
                    $editais = ManterAvaliadorDAO::buscaEditaisAtivos($idAgente);
                    $this->view->editais = $editais;
                }
                $idEdital = ManterAvaliadorDAO::listarEditaisAvaliador(); //BUSCA DA MODAL EDITAIS
                $this->view->dadosEditalAvaliador = $idEdital;
            } else {
                parent::message("CPF Não cadastrado!", "/manteravaliador/cadastraravaliador", "ERROR");
            }
//        if (empty($_GET['cpf'])) {
//        	if (empty($_POST['cpf'])){
//        		parent::message("Digite o CPF!", "manteravaliador/cadastraravaliador", "CONFIRM");
//        	}
//        }
        }
        if (isset($_POST['idEdit'])) { //DESVINCULAR
            $idAgente = $_POST['idAgen'];
            $idEdital = $_POST['idEdit'];
            $cpf = $_POST['cpf2'];
            $this->view->cpf = $cpf;
            $alterar = new tbAvaliadorEdital();
            $dados = array('stAtivo' => 'I');
            $where = "idAvaliador = $idAgente and idEdital = $idEdital";
            $atualizarProjeto = $alterar->alterarAvaliador($dados, $where);

            $buscaIdAgente = ManterAvaliadorDAO::buscaIdAgente($cpf);
            $this->view->buscaIdAgente = $buscaIdAgente;
            if ($buscaIdAgente) {
                foreach ($buscaIdAgente as $idAgente) {
                    $idAgente = $idAgente->idAgente;
                }
                $avaliador = ManterAvaliadorDAO::buscaAvaliador($cpf, $idAgente);
                $this->view->dadosAvaliador = $avaliador;
                $this->view->idAgente = $idAgente;
                if ($idAgente) {
                    $editais = ManterAvaliadorDAO::buscaEditaisAtivos($idAgente);
                    $this->view->editais = $editais;
                }
                $idEdital = ManterAvaliadorDAO::listarEditaisAvaliador(); //BUSCA DA MODAL EDITAIS
                $this->view->dadosEditalAvaliador = $idEdital;
            } else {
                $this->view->cpf = $cpf;
            }
            parent::message("Edital Desvinculado com Sucesso!", "/manteravaliador/manteravaliador?cpf={$cpf}", "CONFIRM");
        }

        if (isset($_POST['idAgente'])) { //VINCULAR
            $idAgente= $_POST['idAgente'];
            $idEdit= $_POST['idEdital2'];

            foreach ($idEdit as $idEdital) {
                //	        	$buscaEdital = ManterAvaliadorDAO::buscaEditais($idAgente, $idEdital);
                $alterar = new tbAvaliadorEdital();
                $vinculado = $alterar->buscar(array('idAvaliador = ?'=>$idAgente, 'idEdital = ?'=>$idEdital))->toArray();

                if ($vinculado) {
                    if ($vinculado[0]['stAtivo'] == 'I') {
                        $dados = array('stAtivo' => 'A');
                        $where = "idAvaliador = $idAgente and idEdital = $idEdital";
                        $atualizarProjeto = $alterar->alterarAvaliador($dados, $where);
                        parent::message("Edital Vinculado com Sucesso!", "/manteravaliador/manteravaliador?cpf={$cpf}", "CONFIRM");
                    } elseif ($vinculado[0]['stAtivo'] == 'A') {
                        parent::message("Edital j� vinculado!", "/manteravaliador/manteravaliador?cpf={$cpf}", "ALERT");
                    }
                } else {
                    $dadosInserir = array(
                        'idEdital' => $idEdital,
                        'idAvaliador' => $idAgente,
                        'stAtivo' => 'A'
                    );
                    $inserir = $alterar->inserirAvaliador($dadosInserir);
                }
            }

            parent::message("Edital Vinculado com Sucesso!", "/manteravaliador/manteravaliador?cpf={$cpf}", "CONFIRM");
        }
    }

    public function incluireditalAction()
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
        $this->view->usuario = $idusuario;

        $Orgao = new Orgaos();

        $NomeOrgao = $Orgao->pesquisarNomeOrgao($codOrgao);
        $this->view->nomeOrgao = $NomeOrgao;

        /* *************************************************************** */
    }
}
