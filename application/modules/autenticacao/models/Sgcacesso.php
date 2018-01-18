<?php

class Autenticacao_Model_Sgcacesso extends MinC_Db_Table_Abstract
{
    protected $_name = 'sgcacesso';
    protected $_schema = 'controledeacesso';
    protected $_primary = 'Cpf';

    /**
     * @var Zend_Db_Table $instancia
     */
    private static $instancia;

    /**
     * @return Autenticacao_Model_Sgcacesso
     */
    public static function obterInstancia()
    {
        if (!self::$instancia) {
            self::$instancia = new Autenticacao_Model_Sgcacesso();
        }
        return self::$instancia;
    }

    public function init()
    {
        parent::init();
    }

    /**
     * Metodo para buscar os dados do usuï¿½rio de acordo com id (login scriptcase)
     * @access public
     * @dinamic
     * @param @cod (codigo do usuario)
     * @return bool
     */
    public static function loginScriptcase($cod)
    {
        // busca pelo usuario no banco de dados
        $buscar = self::obterInstancia()->buscar(array('IdUsuario = ?' => $cod));

        $conexao = Zend_Registry::get('conexao_banco');

        if ($conexao == "conexao_01") {
            $conexao_scriptcase = "conexao_scriptcase_01";
        } elseif ($conexao == "conexao_02") {
            $conexao_scriptcase = "conexao_scriptcase_02";
        } elseif ($conexao == "conexao_03") {
            $conexao_scriptcase = "conexao_scriptcase_03";
        } elseif ($conexao == "conexao_04") {
            $conexao_scriptcase = "conexao_scriptcase_04";
        } elseif ($conexao == "conexao_05") {
            $conexao_scriptcase = "conexao_scriptcase";
        }

        // configuracoes do banco de dados (seta uma nova conexao no arquivo config.ini)

        $config = new Zend_Config_Ini(
            APPLICATION_PATH. '/configs/application.ini',
            $conexao_scriptcase
        );

        $db = Zend_Db::factory($config->db);
        Zend_Db_Table::setDefaultAdapter($db);

        if ($buscar) { // realiza a autenticacao
            // configuracoes do banco
            $authAdapter = new Zend_Auth_Adapter_DbTable($db);
            $authAdapter->setTableName(self::obterInstancia()->getTableName())
                ->setIdentityColumn('cpf')
                ->setCredentialColumn('senha');

            // seta as credenciais informada pelo usuï¿½rio
            $authAdapter
                ->setIdentity($buscar[0]->Cpf)
                ->setCredential($buscar[0]->Senha);

            // tenta autenticar o usuï¿½rio
            $auth = Zend_Auth::getInstance();
            $acesso = $auth->authenticate($authAdapter);

            // verifica se o acesso foi permitido
            if ($acesso->isValid()) {
                // pega os dados do usuï¿½rio com exceï¿½ï¿½o da senha
                $authData = $authAdapter->getResultRowObject(null, 'senha');

                // armazena os dados do usuï¿½rio
                $objAuth = $auth->getStorage()->write($authData);

                return true;
            }
        }
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function login($username, $password)
    {
        $objSgcAcesso = $this->select();
        $objSgcAcesso->where('cpf = ?', $username);

        $senhaCriptografada = EncriptaSenhaDAO::encriptaSenha($username, $password);

        $objSgcAcesso->where("senha in ('{$senhaCriptografada}') as senha)");
        $scriptSenha = $this->fetchRow($objSgcAcesso);
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            $this,
            array(
                'cpf',
                'senha',
            )
        );
        $sql->where('cpf = ?', $username);
        $sql->where("senha  = ?", $objSgcAcesso);
        $buscar = $this->fetchRow($sql);

        if ($buscar) {
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter->setTableName($this->getTableName())// CONTROLEDEACESSO.dbo.sgcacesso
            ->setIdentityColumn('cpf')
                ->setCredentialColumn('senha');

            $authAdapter
                ->setIdentity($buscar['cpf'])
                ->setCredential($buscar['senha']);

            $auth = Zend_Auth::getInstance();
            $acesso = $auth->authenticate($authAdapter);

            if ($acesso->isValid()) {
                $authData = $authAdapter->getResultRowObject(null, 'senha');
                $auth->getStorage()->write($authData);
                return true;
            }
        }
    }

    public function loginSemCript($username, $password)
    {
        // busca o usu?rio de acordo com o login e a senha


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            $this,
            array(
                'cpf',
                'senha',
            )
        );
        $select->where('cpf = ?', $username);
        /* if ($password != MinC_Controller_Action_Abstract::validarSenhaInicial()) { */
        $select->where("senha  = ?", $password);
        /* } */

        $buscar = $this->fetchRow($select);

        if ($buscar) { // realiza a autenticacao
            // configura??es do banco
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            // pegamos o zend_auth

            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter->setTableName($this->getTableName(null, null, false)) // CONTROLEDEACESSO.dbo.sgcacesso
            ->setIdentityColumn('cpf')
                ->setCredentialColumn('senha');

            // seta as credenciais informada pelo usu?rio
            $authAdapter
                ->setIdentity($buscar['cpf'])
                ->setCredential($buscar['senha']);

            // tenta autenticar o usu?rio
            $auth = Zend_Auth::getInstance();

            $acesso = $auth->authenticate($authAdapter);

            // verifica se o acesso foi permitido
            if ($acesso->isValid()) {
                // pega os dados do usu?rio com exce??o da senha
                $authData = $authAdapter->getResultRowObject(null, 'senha');

                // armazena os dados do usu?rio
                $auth->getStorage()->write($authData);

                return true;
            }
        }
    }

    /**
     * @param array $dados
     * @return mixed
     */
    public function salvar($dados)
    {
        try {
            if (isset($dados['IdUsuario']) && !empty($dados['IdUsuario'])) {
                $objSgcAcesso = $this->buscar(array("IdUsuario = ?" => $dados['IdUsuario']))->current();
            } else {
                $objSgcAcesso = $this->createRow();
            }

            if (isset($dados['Cpf'])) {
                $objSgcAcesso->Cpf = $dados['Cpf'];
            }
            if (isset($dados['Nome'])) {
                $objSgcAcesso->Nome = $dados['Nome'];
            }
            if (isset($dados['DtNascimento'])) {
                $objSgcAcesso->DtNascimento = $dados['DtNascimento'];
            }
            if (isset($dados['Email'])) {
                $objSgcAcesso->Email = $dados['Email'];
            }
            if (isset($dados['Senha'])) {
                $objSgcAcesso->Senha = $dados['Senha'];
            }
            if (isset($dados['DtCadastro'])) {
                $objSgcAcesso->DtCadastro = $dados['DtCadastro'];
            }
            if (isset($dados['Situacao'])) {
                $objSgcAcesso->Situacao = $dados['Situacao'];
            }
            if (isset($dados['DtSituacao'])) {
                $objSgcAcesso->DtSituacao = $dados['DtSituacao'];
            }
            if (isset($dados['id_login_cidadao'])) {
                $objSgcAcesso->id_login_cidadao = $dados['id_login_cidadao'];
            }
            return $objSgcAcesso->save();
        } catch (Exception $objException) {
            throw new Exception($objException->getMessage(), 0, $objException);
        }
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscar_($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();
        $slct->from($this, array("*", "dtnascimento" => new Zend_Db_Expr("CONVERT(CHAR(20),dtnascimento, 120)")));

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        return $this->fetchAll($slct);
    }


    public function hasCPFCadastrado($cpf)
    {
        $sgcAcessoBuscaCpf = $this->buscar(array("Cpf = ?" => $cpf))->toArray();
        if (!empty($sgcAcessoBuscaCpf)) {
            return false;
        }
        return true;
    }

    public function hasEmailCadastrado($email)
    {
        $sgcAcessoBuscaEmail = $this->buscar(array("Email = ?" => $email))->toArray();
        if (!empty($sgcAcessoBuscaEmail)) {
            return false;
        }
        return true;
    }

    public function porCPF($cpf)
    {
        $objSgcAcesso = $this->select();
        $objSgcAcesso->where('cpf = ?', $cpf);

        $resultado = $this->fetchRow($objSgcAcesso);
        return $resultado;
    }
}
