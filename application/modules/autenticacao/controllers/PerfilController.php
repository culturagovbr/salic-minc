<?php
/**
 * Login e autenticação
 * @author Equipe RUP - Politec
 * @author wouerner <wouerner@gmail.com>
 * @since 20/07/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 */

class Autenticacao_PerfilController extends MinC_Controller_Action_Abstract
{
    /**
     * Altera o pefil do usuário
     * @access public
     * @param void
     * @return void
     */
    public function alterarperfilAction()
    {
        $codGrupo = $this->getRequest()->getParam('codGrupo'); // grupo do usuário logado
        $codOrgao = $this->getRequest()->getParam('codOrgao'); // órgão do usuário logado
        $auth   = Zend_Auth::getInstance();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
        $GrupoAtivo->codGrupo = $codGrupo; // armazena o grupo ativo na sessão
        $GrupoAtivo->codOrgao = $codOrgao; // armazena o órgão ativo na sessão

        if ($GrupoAtivo->codGrupo == "1111" && $GrupoAtivo->codOrgao == "2222") {
            $tblSGCacesso = new Autenticacao_Model_Sgcacesso();
            $cpf = $auth->getIdentity()->usu_identificacao;
            $rsSGCacesso = $tblSGCacesso->buscar(array("Cpf = ? " => $cpf))->current()->toArray();
            $auth->getStorage()->write((object)$rsSGCacesso);

            $_SESSION["GrupoAtivo"]["codGrupo"] = $GrupoAtivo->codGrupo;
            $_SESSION["GrupoAtivo"]["codOrgao"] = $GrupoAtivo->codOrgao;
            parent::message("Seu perfil foi alterado no sistema. Voc&ecirc; ter&aacute; acesso a outras funcionalidades!", "principalproponente", "INFO");
        } else {
//            xd('AAAAAAAAAAAAAAAAAAAA', $codGrupo, $codOrgao);
            //Reescreve a sessao com o novo orgao superior
            $tblUsuario = new Autenticacao_Model_DbTable_Usuario();
            $codOrgaoMaxSuperior = $tblUsuario->recuperarOrgaoMaxSuperior($codOrgao);
            $_SESSION['Zend_Auth']['storage']->usu_org_max_superior = $codOrgaoMaxSuperior;

            // redireciona para a página inicial do sistema
            parent::message("Seu perfil foi alterado no sistema. Voc&ecirc; ter&aacute; acesso a outras funcionalidades!", "principal", "INFO");
        }
    }
}
