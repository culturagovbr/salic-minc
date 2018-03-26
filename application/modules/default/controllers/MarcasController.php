<?php

class MarcasController extends MinC_Controller_Action_Abstract
{

    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */

    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica�?o
        $this->view->title = "Salic - Sistema de Apoio ?s Leis de Incentivo ? Cultura"; // t�tulo da p�gina
        // 3 => autentica�?o scriptcase e autentica�?o/permiss?o zend (AMBIENTE PROPONENTE E MINC)
        // utilizar quando a Controller ou a Action for acessada via scriptcase e zend
        // define as permiss?es

        $this->idusuario = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->codGrupo = $codGrupo; //  Grupo ativo na sess�o
        $this->codOrgao = $codOrgao;
        $this->view->codOrgao = $codOrgao;
        $this->view->codGrupo = $codGrupo;
        $this->view->grupoativo = $codGrupo;

        //$this->view->idUsuarioLogado = $idusuario;
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; //Tecnico de Acompanhamento

        parent::perfil(1, $PermissoesGrupo);
        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            if ($this->getIdUsuario) {
                $this->getIdUsuario = $this->getIdUsuario["idAgente"];
            } else {
                $this->getIdUsuario = 0;
            }
        } else {
            $this->getIdUsuario = $auth->getIdentity()->IdUsuario;
        }
        parent::init();
    }

    public function indexAction()
    {
        $tbArquivoImagem = new tbArquivoImagem();
        $tblUsuario = new Autenticacao_Model_Usuario();
        $rsUsuario = $tblUsuario->buscarOrgaoSuperior($this->codOrgao);
        $idOrgaoSuperior = $rsUsuario->idSecretaria;

        if ($idOrgaoSuperior == Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $lista = $tbArquivoImagem->listarMarcasAcompanhamentoArea('p.Area <> 2');
        } else {
            $lista = $tbArquivoImagem->listarMarcasAcompanhamentoArea('p.Area = 2');
        }
        $this->view->lista = $lista;
    }

    public function processarMarcasAction()
    {
        if ($_POST['justificativa'] != '') {
            $dados = array('stAtivoDocumentoProjeto' => $_POST['stAtivo']);
            $where = array('idDocumento = ?' => $_POST['documento']);

            $tbDocumentoProjeto = new tbDocumentoProjeto();
            $resultado = $tbDocumentoProjeto->update($dados, $where);
            if ($resultado) {
                if ($_POST['stAtivo'] == 'D') {
                    $msg = 'A Marca foi DEFERIDA com sucesso!';
                    $assunto = 'SALIC - Marca Deferida';
                } else {
                    $msg = 'A Marca foi INDEFERIDA com sucesso!';
                    $assunto = 'SALIC - Marca Indeferida';

                    $dados = array('dsDocumento' => utf8_decode($_POST['justificativa']));
                    $where = array('idDocumento = ?' => $_POST['documento']);
                    $tbDocumento = new tbDocumento();
                    $resultado2 = $tbDocumento->update($dados, $where);
                }

                $projetos = new Projetos();
                $ListaEmails = $projetos->buscarProjetoEmails($_POST['pronacId']);

                if (count($ListaEmails)>0) {
                    foreach ($ListaEmails as $lista) {
                        $EnviarEmails = new EmailDAO();
                        $EnviarEmails->enviarEmail($lista->Email, $assunto, utf8_decode($_POST['justificativa']));
                    }
                }
                $this->_helper->json(array('resposta'=>true, 'mensagem'=>$msg));
            } else {
                $this->_helper->json(array('resposta'=>false));
            }
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
