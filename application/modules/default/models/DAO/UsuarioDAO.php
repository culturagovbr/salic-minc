<?php

/**
 * Classe Usu�rio DAO
 * @author Equipe RUP - Politec
 * @since 12/07/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class UsuarioDAO extends MinC_Db_Table_Abstract
{
    protected $_banco = "tabelas";
    protected $_name = 'usuarios';
    protected $_schema = 'tabelas';

    /**
     * M�todo para buscar os dados do usu�rio de acordo com login e senha
     * @access public
     * @static
     * @param @username (cpf ou cnpj do usu�rio)
     * @param @password (senha do usu�rio criptografada)
     * @return bool
     */
    public static function login($username, $password)
    {
        // busca o usu�rio de acordo com o login e a senha
        $sql = "SELECT usu_codigo
					,usu_nome
					,usu_identificacao
					,usu_senha

				FROM TABELAS.dbo.Usuarios

				WHERE usu_identificacao = '" . $username . "'
					AND usu_senha = (SELECT TABELAS.dbo.fnEncriptaSenha('" . $username . "', '" . $password . "')
									 FROM TABELAS.dbo.Usuarios
									 WHERE usu_identificacao = '" . $username . "')
					AND usu_status = 1";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $buscar = $db->fetchAll($sql);

        if ($buscar) { // realiza a autentica��o
            // configura��es do banco
            $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'));
            $authAdapter->setTableName('dbo.Usuarios')// TABELAS.dbo.Usuarios
            ->setIdentityColumn('usu_identificacao')
                ->setCredentialColumn('usu_senha');

            // seta as credenciais informada pelo usu�rio
            $authAdapter
                ->setIdentity($buscar[0]->usu_identificacao)
                ->setCredential($buscar[0]->usu_senha);

            // tenta autenticar o usu�rio
            $auth = Zend_Auth::getInstance();
            $acesso = $auth->authenticate($authAdapter);

            // verifica se o acesso foi permitido
            if ($acesso->isValid()) {
                // pega os dados do usu�rio com exce��o da senha
                $authData = $authAdapter->getResultRowObject(null, 'usu_senha');

                // armazena os dados do usu�rio
                $objAuth = $auth->getStorage()->write($authData);

                return true;
            } // fecha if
            else { // caso n�o tenha sido validado
                return false;
            }
        } // fecha if
        else {
            return false;
        }
    } // fecha m�todo login()


    /**
     * M�todo para alterar a senha do usu�rio do sistema
     * @access public
     * @param @username (cpf ou cnpj do usu�rio)
     * @param @password (nova senha do usu�rio)
     * @return object
     */
    public function alterarSenha($username, $password)
    {
        $sql = "UPDATE Tabelas.dbo.Usuarios
					SET usu_senha = TABELAS.dbo.fnEncriptaSenha('" . $username . "', '" . $password . "')
				WHERE usu_identificacao = '" . $username . "'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo alterarSenha()


    /**
     * M�todo para buscar as unidades autorizadas do usu�rio do sistema
     * @access public
     * @param @usu_codigo (c�digo do usu�rio)
     * @param @sis_codigo (c�digo sistema)
     * @param @gru_codigo (c�digo do grupo)
     * @param @uog_orgao  (c�digo do �rg�o)
     * @return object
     */
    public function buscarUnidades($usu_codigo, $sis_codigo = null, $gru_codigo = null, $uog_orgao = null)
    {
        $sql = "SELECT usu_orgao
					,usu_orgaolotacao
					,uog_orgao
					,org_siglaautorizado
					,org_nomeautorizado
					,gru_codigo
					,gru_nome
					,org_superior
					,uog_status

				FROM TABELAS.dbo.vwUsuariosOrgaosGrupos

				WHERE usu_codigo = $usu_codigo ";

        if (!empty($sis_codigo)) {
            $sql .= "AND sis_codigo = $sis_codigo ";
        }
        if (!empty($gru_codigo)) {
            $sql .= "AND gru_codigo = $gru_codigo ";
        }
        if (!empty($uog_orgao)) {
            $sql .= "AND uog_orgao = $uog_orgao ";
        }

        $sql .= "ORDER BY org_siglaautorizado";
        //die($sql);

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }


    /**
     * Metodo para pegar o id do usuario logado
     * @access public
     * @static
     * @param integer
     * @return object
     */
    public static function getIdUsuario($usu_codigo)
    {
        $sql = "SELECT usu_codigo
					,idAgente
				FROM " . UsuarioDAO::getStaticTableName('tabelas') . ".USUARIOS u
					INNER JOIN " . UsuarioDAO::getStaticTableName('agentes') . ".AGENTES a ON (u.usu_identificacao = a.cnpjcpf)
				WHERE usu_codigo = $usu_codigo";
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            return $db->fetchRow($sql);
        } catch (Zend_Exception_Db $objException) {
            throw new Exception($objException->getMessage(), 0, $objException);
        }
    }


    /**
     * Metodo para buscar todos os dados do usuario de acordo com o seu codigo vindo do SCRIPTCASE
     * @access public
     * @static
     * @param @cod (codigo do usuario)
     * @author Vinicius Feitosa da Silva <viniciusfesil@mail.com>
     * @return object
     */
    public static function buscarUsuarioScriptcase($idusuario)
    {
        $objSGCAcesso = new Autenticacao_Model_Sgcacesso();
        return $objSGCAcesso->buscar(array('idusuario = ?' => $idusuario));
    }

    public static function buscarUsuario($cod)
    {
        $sql = "SELECT *
				FROM " . UsuarioDAO::getStaticTableName('tabelas', '.usuarios') . "
				WHERE usu_codigo = $cod";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        //echo $sql;die;
        return $db->fetchAll($sql);
    }

    public static function buscarUsuarioCpf($cpf)
    {
        $sql = "SELECT *
				FROM " . UsuarioDAO::getStaticTableName('tabelas', 'usuarios') . "
				WHERE usu_identificacao = $cpf";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * Metodo para buscar os dados do usuario de acordo com id (login scriptcase)
     * @access public
     * @static
     * @param @cod (codigo do usuario)
     * @return bool
     *
     * @author Vinicius Feitosa da Silva <viniciusfesil@mail.com>
     * @author Ruy Junior Ferreira Silva <ruyjfs@mail.com>
     * @since 25/0/2016
     *
     * @todo Remover o codigo comentado depois que testado em todos os ambientes;
     */
    public static function loginScriptcase($cod)
    {
        // busca pelo usuario no banco de dados
        # Pegando apenas o primeiro resultado da consulta, transformando em array e transformando as chaves em minusculas.
        $arrUser = UsuarioDAO::buscarUsuarioScriptcase($cod)->current();
        if ($arrUser) {
            $arrUser = array_change_key_case(UsuarioDAO::buscarUsuarioScriptcase($cod)->current()->toArray());
        }

//        $conexao = Zend_Registry::get('conexao_banco');

//
//        if ($conexao == "conexao_01") {
//            $conexao_scriptcase = "conexao_scriptcase_01";
//        } else if ($conexao == "conexao_02") {
//            $conexao_scriptcase = "conexao_scriptcase_02";
//        } else if ($conexao == "conexao_03") {
//            $conexao_scriptcase = "conexao_scriptcase_03";
//        } else if ($conexao == "conexao_04") {
//            $conexao_scriptcase = "conexao_scriptcase_04";
//        } else if ($conexao == "conexao_05") {
//            $conexao_scriptcase = "conexao_scriptcase_05";
//        } else if ($conexao == "conexao_xti") {
//            $conexao_scriptcase = "conexao_xti_controle_acesso";
//        }
//        ControleDeAcesso
        // configuracaes do banco de dados (seta uma nova conexao no arquivo config.ini)
//        $config = new Zend_Config_Ini('./application/configs/config.ini', $conexao_scriptcase);


//        $db = Zend_Db::factory($config->db);;
//        Zend_Db_Table::setDefaultAdapter($db);,

        if ($arrUser) {
//            $authAdapter = new Zend_Auth_Adapter_DbTable($db);
            $authAdapter = new Zend_Auth_Adapter_DbTable();
            $objSgcAcesso = new Autenticacao_Model_Sgcacesso();
            $authAdapter->setTableName($objSgcAcesso->getTableName())
                ->setIdentityColumn('cpf')
                ->setCredentialColumn('senha');

            // seta as credenciais informada pelo usuario
            $authAdapter
                ->setIdentity($arrUser['cpf'])
                ->setCredential($arrUser['senha']);

            // tenta autenticar o usuario
            $auth = Zend_Auth::getInstance();

            $acesso = $auth->authenticate($authAdapter);

            // verifica se o acesso foi permitido
            if ($acesso->isValid()) {
                $authData = $authAdapter->getResultRowObject(null, 'senha');
                $auth->getStorage()->write($authData);

                return true;
            }
        }
    }
}
