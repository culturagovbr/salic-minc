<?php

/**
 * Classe Usuario DAO
 * @author Equipe RUP - Politec
 * @since 12/07/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright - 2010 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class Autenticacao_Model_Usuario extends MinC_Db_Table_Abstract
{
    protected $_banco = "tabelas";
    protected $_name = 'usuarios';
    protected $_schema = 'tabelas';

    private $_usu_identificacao;
    private $_usu_senha;

    /**
     * @return mixed
     */
    public function getUsuIdentificacao()
    {
        return $this->_usu_identificacao;
    }

    /**
     * @param mixed $usu_identificacao
     */
    public function setUsuIdentificacao($usu_identificacao)
    {
        $this->_usu_identificacao = $usu_identificacao;
    }

    /**
     * @return mixed
     */
    public function getUsuSenha()
    {
        return $this->_usu_senha;
    }

    /**
     * @param mixed $usu_senha
     */
    public function setUsuSenha($usu_senha)
    {
        $this->_usu_senha = $usu_senha;
    }

    /**
     * funcao do banco de dados do sql server no formato PHP.
     *
     * @name encriptaSenha
     * @param $username
     * @param $password
     * @return string
     *
     * @author Ruy Junior Ferreira Silva ruyjfs@gmail.com
     * @since 12/08/2016
     *
     * Exemplo do formado do SQL Server:
     *
     *        BEGIN
     *    DECLARE
     * @w    varchar(30),
     * @s      varchar(15),
     * @t1    int,
     * @t2    int,
     * @k      int,
     * @i      int,
     * @j      int,
     * @f      int,
     * @v      int
     *
     *    SET @w = RTRIM(LTRIM(@p_senha))
     *    SET @t1 = LEN(RTRIM(LTRIM(@p_identificacao)))
     *    SET @t2 = LEN(@w)
     *    IF @t2 < 1
     *    BEGIN
     *        SET @p_senha = '??????'
     *        SET @w = '??????'
     *        SET @t2 = 6
     *    END
     *    WHILE LEN(@w) < 15
     *    BEGIN
     *        SET @w = @w + @w
     *    END
     *    SET @w = SUBSTRING(@w, 1, 15)
     *    SET @k = ASCII(SUBSTRING(@w, 1, 1)) + 2
     *    SET @s = ''
     *    SET @i = 0
     *    WHILE @i < 15
     *    BEGIN
     *        SET @i = @i + 1
     *        SET @v = (@t1 + @t2) * @k / @i
     *        SET @f = ASCII(SUBSTRING(@w, 1, 1))
     *        SET @w = SUBSTRING(@w, 2, 15)
     *        SET @j = ((@f * @k) + @t1 + (@t2 * @f)) / @i
     *        SET @v = @v + @j
     *        IF @v < 33
     *        BEGIN
     *            SET @v = @v + (@t1 * @i)
     *        END
     *        SET @j = @v % 94
     *        SET @s = @s + CHAR(33 + @j)
     *    END
     *    RETURN @s
     */
    private function encriptaSenha($username, $password)
    {
        $w = trim($password);
        $t1 = strlen(trim($username));
        $t2 = strlen($w);

        if ($t2 < 1) {
            $password = '??????';
            $w = '??????';
            $t2 = 6;
        }

        while (strlen($w) < 15) {
            $w = $w . $w;
        }

        $w = substr($w, 1, 15);
        $k = ord((substr($w, 1, 1))) + 2;
        $s = '';
        $i = 0;

        while ($i < 15) {
            $i = $i + 1;
            $v = ($t1 + $t2) * $k / $i;
            $f = ord(substr($w, 1, 1));
            $w = substr($w, 2, 15);
            $j = (($f * $k) + $t1 + ($t2 * $f)) / $i;
            $v = $v + $j;
            if ($v < 33) {
                $v = $v + ($t1 * $i);
            }
            $j = $v % 94;
            $s = $s . (33 + $j);
        }

        return $s;
    }

    public function login($username, $password)
    {
        // busca o usuario de acordo com o login e a senha
        $auxSenha = EncriptaSenhaDAO::encriptaSenha($username, $password);

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                $this->_name,
                array(
                'usu_codigo',
                'usu_nome',
                'usu_identificacao',
                'usu_senha',
                'usu_orgao'),
                $this->_schema
            )
            ->joinInner(
                array(
                'uog' => 'usuariosxorgaosxgrupos'),
                'uog.uog_usuario = usu_codigo AND uog_status = 1',
                array(),
                $this->_schema
            )
            ->where('usu_identificacao = ?', $username)
            ->where('usu_status  = ?', 1);
        $select->where("usu_senha  = ?", $auxSenha);

        $buscar = $this->fetchRow($select);

        if ($buscar) { // realiza a autenticacao
            // configuracoes do banco
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            // pegamos o zend_auth
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

            $authAdapter->setTableName($this->getTableName(null, null, false))// TABElLAS.dbo.Usuarios
            ->setIdentityColumn('usu_identificacao')
                ->setCredentialColumn('usu_senha');

            // seta as credenciais informada pelo usuario
            $authAdapter
                ->setIdentity($buscar['usu_identificacao'])
                ->setCredential($buscar['usu_senha']);

            // tenta autenticar o usuario
            $auth = Zend_Auth::getInstance();
            $acesso = $auth->authenticate($authAdapter);

            // verifica se o acesso foi permitido
            if ($acesso->isValid()) {
                // pega os dados do usuario com excecao da senha
                $authData = $authAdapter->getResultRowObject(null, 'usu_senha');

                $orgao_maximo_superior = $this->recuperarOrgaoMaxSuperior($buscar['usu_orgao']);

                // armazena os dados do usuario
                $objAuth = $auth->getStorage()->write($authData);

                //Grava o orgao superior na sessao do usuario
                $_SESSION['Zend_Auth']['storage']->usu_org_max_superior = $orgao_maximo_superior;

                return true;
            }
        }
    }

    public function loginSemCript($username, $password)
    {

        // busca o usuario de acordo com o login e a senha
        $senha = $this->select();
        $senha->from($this);
        $senha->where('usu_identificacao = ?', $username);
        $senha->where('usu_senha = ?', $password);

        $criptSenha = $this->fetchRow($senha);
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            $this,
            array(
                'usu_nome',
                'usu_identificacao',
                'usu_senha',
            )
        );
        $sql->where('usu_identificacao = ?', $username);
        $sql->where('usu_status  = ?', 1);
        $sql->where("usu_senha  = ?", $criptSenha['usu_senha']);
        $buscar = $this->fetchRow($sql);

        if ($buscar) { // realiza a autentica??o
            // configuracoes do banco
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            // pegamos o zend_auth

            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter->setTableName($this->_name)// TABELAS.dbo.Usuarios
            ->setIdentityColumn('usu_identificacao')
                ->setCredentialColumn('usu_senha');

            // seta as credenciais informada pelo usuario
            $authAdapter
                ->setIdentity($buscar['usu_identificacao'])
                ->setCredential($buscar['usu_senha']);

            // tenta autenticar o usu?rio
            $auth = Zend_Auth::getInstance();
            $acesso = $auth->authenticate($authAdapter);

            // verifica se o acesso foi permitido
            if ($acesso->isValid()) {
                // pega os dados do usu?rio com exce??o da senha
                $authData = $authAdapter->getResultRowObject(null, 'usu_senha');

                // armazena os dados do usu?rio
                $objAuth = $auth->getStorage()->write($authData);

                return true;
            } // fecha if
            else { // caso n?o tenha sido validado
                return false;
            }
        } // fecha if
        else {
            return false;
        }
    } // fecha m?todo login()


    /**
     * M?todo para alterar a senha do usu?rio do sistema
     * @access public
     * @param @username (cpf ou cnpj do usu?rio)
     * @param @password (nova senha do usu?rio)
     * @return object
     */
    public function alterarSenha($username, $password)
    {
        try {
            $this->_db->beginTransaction();
            $senha = $this->select();
            $senha->from(
                $this,
                array("dbo.fnEncriptaSenha('" . $username . "', '" . $password . "') as senha")
            );
            $senha->where('usu_identificacao = ?', $username);

            $criptSenha = $this->fetchRow($senha);

            $where = "usu_identificacao = '" . $username . "'";

            $update = $this->update(array('usu_senha' => $criptSenha['senha']), $where);
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            return false;
        }
    }

    /**
     * Novo m�todo para alterar a senha do usu�rio do sistema (Solicita��o CGTI - Dia 15/01/2015)
     * @access public
     * @param @cpf (cpf ou cnpj do usu�rio)
     * @param @password (nova senha do usu�rio)
     * @return object
     */
    public function alterarSenhaSalic($cpf, $password)
    {
        $cpf = trim($cpf);
        $password = trim($password);
        //parent::name('ausuarios', 'agentes');
        //$this->_db->beginTransaction();
        try {
            $db = Zend_DB_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            $sql = "UPDATE tabelas.dbo.usuarios
                        SET usu_senha = Tabelas.dbo.fnEncriptaSenha( '$cpf' , '$password' ),
                            usu_data_validade = getdate()+usu_duracao_senha,
                            usu_seguranca = Tabelas.dbo.fnSegurancaUsuarios
                            (usu_codigo,usu_identificacao,usu_nome,usu_pessoa,
                             usu_orgao,usu_sala,usu_ramal,usu_nivel,usu_exibicao,
                             usu_SQL_Login,usu_SQL_senha,usu_duracao_senha,
                             usu_data_validade,usu_limite_utilizacao,
                             Tabelas.dbo.fnEncriptaSenha( '$cpf' , '$password' ),usu_status),
                         usu_validacao = Tabelas.dbo.fnValidacaoUsuarios
                                     (usu_codigo,usu_identificacao,usu_nome)
                         WHERE usu_identificacao = '$cpf' ";

            $db->fetchAssoc($sql);
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            return false;
        }
    }

    public function buscarOrgao($usu_orgao)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'Orgaos',
            array("*"),
            "TABELAS.dbo"
        );
        $sql->where('org_codigo = ?', $usu_orgao);

        return $this->fetchAll($sql)->current();
    }

    public function buscarOrgaoSuperior($usu_orgao)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from('Orgaos', array('idSecretaria'), 'SAC.dbo');
        $sql->where('Codigo = ?', $usu_orgao);
        return $this->fetchRow($sql);
    }

    public function buscarUsuario()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->order('usu_nome');
        $select->from($this->_name);
        return $this->fetchAll($select);
    }


    public function pesquisarUsuarioOrgao($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->order($order);
        $select->from(
            array('u' => $this->_name)
        );
        $select->joinInner(
            array('o' => 'Orgaos'),
            'u.usu_orgao = o.org_codigo',
            array('o.org_sigla')
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($select);
    }

    public function nomeUsuario($usu_codigo)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'Usuarios',
            array("usu_nome"),
            "TABELAS.dbo"
        );
        $sql->where('usu_codigo = ?', $usu_codigo);

        return $this->fetchAll($sql)->current();
    }

    /**
     * Metodo para buscar as unidades autorizadas do usuario do sistema
     * @access public
     * @param @usu_codigo (c?digo do usu?rio)
     * @param @sis_codigo (c?digo sistema)
     * @param @gru_codigo (c?digo do grupo)
     * @param @uog_orgao  (c?digo do ?rg?o)
     * @return object
     */
    public function buscarUnidades($usu_codigo, $sis_codigo = null, $gru_codigo = null, $uog_orgao = null)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'vwusuariosorgaosgrupos',
            array(
                'usu_orgao'
            , 'usu_orgaolotacao'
            , 'uog_orgao'
            , 'org_siglaautorizado'
            , 'org_nomeautorizado'
            , 'gru_codigo'
            , 'gru_nome'
            , 'org_superior'
            , 'uog_status'
            , 'id_unico'
            ),
            $this->_schema
        );
        $sql->where('usu_codigo = ?', $usu_codigo);
        $sql->where('uog_status = ?', 1);

        if (!empty($sis_codigo)) {
            $sql->where('sis_codigo = ?', $sis_codigo);
        }
        if (!empty($gru_codigo)) {
            $sql->where('gru_codigo = ?', $gru_codigo);
        }
        if (!empty($uog_orgao)) {
            $sql->where('uog_orgao = ?', $uog_orgao);
        }
        $sql->where('gru_codigo <> ?', 129);
        $sql->order('org_siglaautorizado ASC');
        $sql->order('gru_nome ASC');
        return $this->fetchAll($sql);
    } // fecha metodo buscarUnidades()


    /**
     * Metodo para pegar o id do usu?rio logado
     * @access public
     * @static
     * @param integer
     * @return object
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     */
    public function getIdUsuario($usu_codigo = null, $usu_identificacao = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('u' => $this->_name),
            array(
                'u.usu_codigo',
                'u.usu_identificacao'
            ),
            $this->_schema
        );

        $select->joinInner(
            array('a' => 'agentes'),
            'u.usu_identificacao = a.cnpjcpf',
            array('a.idagente'),
            parent::getSchema('agentes')
        );

        if (!empty($usu_codigo)) {
            $select->where("usu_codigo = ? ", $usu_codigo);
        }
        if (!empty($usu_identificacao)) {
            $select->where("usu_identificacao = ? ", $usu_identificacao);
        }
        try {
            $result = $this->fetchRow($select);
            return ($result) ? $result->toArray() : $result;
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
    }

    public function inserirUsuarios($dados)
    {
        $insert = $this->insert($dados);
    }

    public function salvarDados($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblUsuario = new Autenticacao_Model_Usuario();

        $tmpTblUsuario = $tmpTblUsuario->createRow();

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['usu_codigo'])) {
            $tmpTblUsuario->usu_codigo = $dados['usu_codigo'];
        }
        if (isset($dados['usu_identificacao'])) {
            $tmpTblUsuario->usu_identificacao = $dados['usu_identificacao'];
        }
        if (isset($dados['usu_nome'])) {
            $tmpTblUsuario->usu_nome = $dados['usu_nome'];
        }
        if (isset($dados['usu_pessoa'])) {
            $tmpTblUsuario->usu_pessoa = $dados['usu_pessoa'];
        }
        if (isset($dados['usu_orgao'])) {
            $tmpTblUsuario->usu_orgao = $dados['usu_orgao'];
        }
        if (isset($dados['usu_sala'])) {
            $tmpTblUsuario->usu_sala = $dados['usu_sala'];
        }
        if (isset($dados['usu_ramal'])) {
            $tmpTblUsuario->usu_ramal = $dados['usu_ramal'];
        }
        if (isset($dados['usu_nivel'])) {
            $tmpTblUsuario->usu_nivel = $dados['usu_nivel'];
        }
        if (isset($dados['usu_exibicao'])) {
            $tmpTblUsuario->usu_exibicao = $dados['usu_exibicao'];
        }
        if (isset($dados['usu_SQL_login'])) {
            $tmpTblUsuario->usu_SQL_login = $dados['usu_SQL_login'];
        }
        if (isset($dados['usu_SQL_senha'])) {
            $tmpTblUsuario->usu_SQL_senha = $dados['usu_SQL_senha'];
        }
        if (isset($dados['usu_duracao_senha'])) {
            $tmpTblUsuario->usu_duracao_senha = $dados['usu_duracao_senha'];
        }
        if (isset($dados['usu_data_validade'])) {
            $tmpTblUsuario->usu_data_validade = $dados['usu_data_validade'];
        }
        if (isset($dados['usu_limite_utilizacao'])) {
            $tmpTblUsuario->usu_limite_utilizacao = $dados['usu_limite_utilizacao'];
        }
        if (isset($dados['usu_senha'])) {
            $tmpTblUsuario->usu_senha = $dados['usu_senha'];
        }
        if (isset($dados['usu_validacao'])) {
            $tmpTblUsuario->usu_validacao = $dados['usu_validacao'];
        }
        if (isset($dados['usu_status'])) {
            $tmpTblUsuario->usu_status = $dados['usu_status'];
        }
        if (isset($dados['usu_seguranca'])) {
            $tmpTblUsuario->usu_seguranca = $dados['usu_seguranca'];
        }
        if (isset($dados['usu_data_atualizacao'])) {
            $tmpTblUsuario->usu_data_atualizacao = $dados['usu_data_atualizacao'];
        }
        if (isset($dados['usu_conta_nt'])) {
            $tmpTblUsuario->usu_conta_nt = $dados['usu_conta_nt'];
        }
        if (isset($dados['usu_dica_intranet'])) {
            $tmpTblUsuario->usu_dica_intranet = $dados['usu_dica_intranet'];
        }
        if (isset($dados['usu_localizacao'])) {
            $tmpTblUsuario->usu_localizacao = $dados['usu_localizacao'];
        }
        if (isset($dados['usu_andar'])) {
            $tmpTblUsuario->usu_andar = $dados['usu_andar'];
        }
        if (isset($dados['usu_controle'])) {
            $tmpTblUsuario->usu_controle = $dados['usu_controle'];
        }
        if (isset($dados['usu_telefone'])) {
            $tmpTblUsuario->usu_telefone = $dados['usu_telefone'];
        }

        try {
            $id = $tmpTblUsuario->save();
        } catch (Exception $e) {
            xd($e);
        }

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblUsuario = new Autenticacao_Model_Usuario();

        if (isset($dados['usu_codigo'])) {
            $tmpTblUsuario = $tmpTblUsuario->buscar(array("usu_codigo = ?" => $dados['usu_codigo']))->current();
        } else {
            $tmpTblUsuario = $tmpTblUsuario->createRow();
        }


        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['usu_codigo'])) {
            $tmpTblUsuario->usu_codigo = $dados['usu_codigo'];
        }
        if (isset($dados['usu_identificacao'])) {
            $tmpTblUsuario->usu_identificacao = $dados['usu_identificacao'];
        }
        if (isset($dados['usu_nome'])) {
            $tmpTblUsuario->usu_nome = $dados['usu_nome'];
        }
        if (isset($dados['usu_pessoa'])) {
            $tmpTblUsuario->usu_pessoa = $dados['usu_pessoa'];
        }
        if (isset($dados['usu_orgao'])) {
            $tmpTblUsuario->usu_orgao = $dados['usu_orgao'];
        }
        if (isset($dados['usu_sala'])) {
            $tmpTblUsuario->usu_sala = $dados['usu_sala'];
        }
        if (isset($dados['usu_ramal'])) {
            $tmpTblUsuario->usu_ramal = $dados['usu_ramal'];
        }
        if (isset($dados['usu_nivel'])) {
            $tmpTblUsuario->usu_nivel = $dados['usu_nivel'];
        }
        if (isset($dados['usu_exibicao'])) {
            $tmpTblUsuario->usu_exibicao = $dados['usu_exibicao'];
        }
        if (isset($dados['usu_SQL_login'])) {
            $tmpTblUsuario->usu_SQL_login = $dados['usu_SQL_login'];
        }
        if (isset($dados['usu_SQL_senha'])) {
            $tmpTblUsuario->usu_SQL_senha = $dados['usu_SQL_senha'];
        }
        if (isset($dados['usu_duracao_senha'])) {
            $tmpTblUsuario->usu_duracao_senha = $dados['usu_duracao_senha'];
        }
        if (isset($dados['usu_data_validade'])) {
            $tmpTblUsuario->usu_data_validade = $dados['usu_data_validade'];
        }
        if (isset($dados['usu_limite_utilizacao'])) {
            $tmpTblUsuario->usu_limite_utilizacao = $dados['usu_limite_utilizacao'];
        }
        if (isset($dados['usu_senha'])) {
            $tmpTblUsuario->usu_senha = $dados['usu_senha'];
        }
        if (isset($dados['usu_validacao'])) {
            $tmpTblUsuario->usu_validacao = $dados['usu_validacao'];
        }
        if (isset($dados['usu_status'])) {
            $tmpTblUsuario->usu_status = $dados['usu_status'];
        }
        if (isset($dados['usu_seguranca'])) {
            $tmpTblUsuario->usu_seguranca = $dados['usu_seguranca'];
        }
        if (isset($dados['usu_data_atualizacao'])) {
            $tmpTblUsuario->usu_data_atualizacao = $dados['usu_data_atualizacao'];
        }
        if (isset($dados['usu_conta_nt'])) {
            $tmpTblUsuario->usu_conta_nt = $dados['usu_conta_nt'];
        }
        if (isset($dados['usu_dica_intranet'])) {
            $tmpTblUsuario->usu_dica_intranet = $dados['usu_dica_intranet'];
        }
        if (isset($dados['usu_localizacao'])) {
            $tmpTblUsuario->usu_localizacao = $dados['usu_localizacao'];
        }
        if (isset($dados['usu_andar'])) {
            $tmpTblUsuario->usu_andar = $dados['usu_andar'];
        }
        if (isset($dados['usu_controle'])) {
            $tmpTblUsuario->usu_controle = $dados['usu_controle'];
        }
        if (isset($dados['usu_telefone'])) {
            $tmpTblUsuario->usu_telefone = $dados['usu_telefone'];
        }


        $id = $tmpTblUsuario->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    public function pesquisarTotalUsuarioOrgao($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->order($order);
        $select->from(
            array('u' => $this->_name)
        );
        $select->joinInner(
            array('o' => 'Orgaos'),
            'u.usu_orgao = o.org_codigo '
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        $rows = $this->fetchAll($select);
        return $rows->count();
    }

    public function ECoordenador($idUsuario)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("uog" => "UsuariosXOrgaosXGrupos"),
            array("*"),
            "TABELAS.dbo"
        );
        $slct->joinInner(
            array("g" => "Grupos"),
            "g.gru_codigo = uog.uog_grupo",
            array(),
            "TABELAS.dbo"
        );
        $slct->where("g.gru_nome LIKE ? ", "%coordenador%");
        $slct->where("uog.uog_usuario = ? ", $idUsuario);
        $rows = $this->fetchAll($slct)->count();
        if ($rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function ECoordenadorGeral($idUsuario)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("uog" => "UsuariosXOrgaosXGrupos"),
            array("*"),
            "TABELAS.dbo"
        );
        $slct->joinInner(
            array("g" => "Grupos"),
            "g.gru_codigo = uog.uog_grupo",
            array(),
            "TABELAS.dbo"
        );
        $slct->where("g.gru_nome LIKE ? ", "%coordenador - geral%");
        $slct->where("uog.uog_usuario = ? ", $idUsuario);
        $rows = $this->fetchAll($slct)->count();
        if ($rows > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Metodo que recupera recursivamente o ultimo orgao superior de um orgao
     * @access public
     * @param $idOrgao
     * @return int $idOrgao
     */
    public function recuperarOrgaoMaxSuperior($idOrgao)
    {
        $objOrgaos = $this->select();
        $objOrgaos->setIntegrityCheck(false);
        $objOrgaos->from(
            array("orgaos"),
            array("org_superior"),
            $this->getSchema('tabelas')
        );

        $objOrgaos->where("org_codigo = ? ", $idOrgao);

        $row = $this->fetchRow($objOrgaos);
        if (isset($row->org_superior) && $row->org_superior > 1) {
            return $this->recuperarOrgaoMaxSuperior($row->org_superior);
        } else {
            return $idOrgao;
        }
    }


    /**
     * Metodo que recupera os agentes
     * @access public
     * @param $idOrgao
     * @return int $idOrgao
     */
    public function buscarUsuariosAvaliacao($perfil, $orgao = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => 'vwUsuariosOrgaosGrupos'),
            array('a.usu_codigo', 'a.usu_nome', 'a.gru_nome AS Perfil', 'a.gru_codigo AS idVerificacao'),
            "TABELAS.dbo"
        );
        $select->joinInner(
            array('b' => 'Agentes'),
            'a.usu_identificacao = b.CNPJCPF',
            array('b.idAgente'),
            "AGENTES.dbo"
        );
        $select->where("a.sis_codigo = ?", 21);
        $select->where("a.gru_codigo = ?", $perfil);

        if (!empty($orgao)) {
            $select->where("a.uog_orgao = ?", $orgao);
        }
        return $this->fetchAll($select);
    }

    /**
     * Metodo que recupera os agentes
     * @access public
     * @param $idOrgao
     * @return int $idOrgao
     */
    public function buscarTecnicos($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("u" => $this->_name),
            array("usu_codigo", "usu_nome")
        );
        $slct->joinInner(
            array("a" => "tbAvaliacaoProposta"),
            "u.usu_codigo = a.idTecnico",
            array(),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("p" => "PreProjeto"),
            "p.idPreProjeto = a.idProjeto",
            array(),
            "SAC.dbo"
        );

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


    /**
     * Buscar os dados do usuario de acordo com login e senha
     * @access public
     * @static
     * @param @username (cpf ou cnpj do usu?rio)
     * @param @password (senha do usu?rio criptografada)
     * @return bool
     */
    public function verificarSenha($username, $password)
    {
        // busca o usu?rio de acordo com o login e a senha
        $objUsuario = $this->select();
        $senha = EncriptaSenhaDAO::encriptaSenha($username, $password);

        $senhaScape = preg_replace("/'/", "''", $senha);

        $objUsuario->from(
            $this->_name,
            new Zend_Db_Expr("'{$senhaScape}' as senha"),
            $this->_schema
        );

        $objUsuario->where('usu_identificacao = ?', $username);

        $criptSenha = $this->fetchRow($objUsuario);

        $auxSenha = "";
        if (!empty($criptSenha['senha'])) {
            $auxSenha = $criptSenha['senha'];
        }

        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            $this->_name,
            array(
                'usu_codigo',
                'usu_nome',
                'usu_identificacao',
                'usu_senha',
                'usu_orgao'
            ),
            $this->_schema
        );

        $sql->joinInner(
            array("uog" => "usuariosxorgaosxgrupos"),
            "uog.uog_usuario = usu_codigo AND uog_status = 1",
            array(),
            $this->getSchema("tabelas")
        );
        $sql->where('usu_identificacao = ?', $username);
        $sql->where('usu_status  = ?', 1);
        $sql->where("usu_senha  = ?", $auxSenha);
        $buscar = $this->fetchRow($sql);

        if ($buscar) {
            return true;
        }
    }

    public function isUsuarioESenhaValidos()
    {
        if (!$this->getUsuIdentificacao()) {
            throw new Exception("Identificacao do usu&aacute;rio n&atilde;o informada.");
        }
        if (!$this->getUsuSenha()) {
            throw new Exception("Senha do usu&aacute;rio n&atilde;o informada.");
        }

        $senhaCriptografada = EncriptaSenhaDAO::encriptaSenha($this->getUsuIdentificacao(), $this->getUsuSenha());

        $objQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                $this->_name,
                array(
                    'usu_codigo',
                    'usu_nome',
                    'usu_identificacao',
                    'usu_senha',
                    'usu_orgao'
                ),
                $this->_schema
            )
            ->where('usu_identificacao = ?', $this->getUsuIdentificacao())
            ->where('usu_status  = ?', 1)
            ->where("usu_senha  = ?", $senhaCriptografada);
        if ($this->fetchRow($objQuery)) {
            return true;
        }
    }
}
