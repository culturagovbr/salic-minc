<?php

class Autenticacao_Model_Usuario extends MinC_Db_Model
{
    protected $_usu_codigo;
    protected $_usu_identificacao;
    protected $_usu_nome;
    protected $_usu_pessoa;
    protected $_usu_orgao;
    protected $_usu_sala;
    protected $_usu_ramal;
    protected $_usu_nivel;
    protected $_usu_exibicao;
    protected $_usu_SQL_login;
    protected $_usu_SQL_senha;
    protected $_usu_duracao_senha;
    protected $_usu_data_validade;
    protected $_usu_limite_utilizacao;
    protected $_usu_senha;
    protected $_usu_validacao;
    protected $_usu_status;
    protected $_usu_seguranca;
    protected $_usu_data_atualizacao;
    protected $_usu_conta_nt;
    protected $_usu_dica_intranet;
    protected $_usu_controle;
    protected $_usu_localizacao;
    protected $_usu_andar;
    protected $_usu_telefone;

    public function setUsuIdentificacao($usu_identificacao)
    {
        if(!Validacao::validarCPF($usu_identificacao)) {
            throw new Exception("O cpf informado &eacute; inv&aacute;lido");
        }

        $this->_usu_identificacao = $usu_identificacao;
    }

    public function getSenhaPadrao($cpf)
    {
        $this->_usu_senha = EncriptaSenhaDAO::encriptaSenha($cpf, substr($cpf, 0, 6));;
    }

    public function setUsuNome($usu_nome)
    {
        if(strlen($usu_nome) > 20) {
            throw new Exception("Nome completo n&atilde;o pode ultrapassar 20 caracteres");
        }

        $this->_usu_nome = $usu_nome;
    }

    public function criarNovoUsuario($idUsuario, $cpf, $nome, $idPessoa, $idUnidade) {

        $this->_usu_codigo = $idUsuario;
        $this->_usu_pessoa = $idPessoa;
        $this->_usu_orgao = $idUnidade;
        $this->_usu_sala = 0;
        $this->_usu_ramal = 0;
        $this->_usu_nivel = 9;
        $this->_usu_exibicao = 'S';
        $this->_usu_SQL_senha = 'B';
        $this->_usu_SQL_login = $cpf;
        $this->_usu_duracao_senha = 40;
        $this->_usu_data_validade = date("Y-m-d");
        $this->_usu_limite_utilizacao = date("Y-m-d");
        $this->_usu_validacao = '~XqkT@';
        $this->_usu_status = 1;
        $this->_usu_seguranca = '~Xqkg';
        $this->_usu_data_atualizacao = date("Y-m-d") ;
        $this->_usu_conta_nt = 0;
        $this->_usu_dica_intranet = 0;
        $this->_usu_localizacao = 1;
        $this->_usu_andar = null;
        $this->_usu_telefone =  null;

        $this->setUsuIdentificacao($cpf);
        $this->getSenhaPadrao($cpf);
        $this->setUsuNome($nome);

        return $this;
    }

    /**
     * Metodo criado pq atualmente o sistema nao persiste no banco um objeto com os atributos no formato atual
     * e esta tabela no banco de dados nao possui auto incremento, para salvar diretamente o objeto devem ocorrer
     * algumas refatoracoes na abstract.
     */
    public function obterUsuarioEmArray() {

        $usuario = [
            'usu_codigo' => $this->_usu_codigo,
            'usu_identificacao' => $this->_usu_identificacao,
            'usu_nome' =>  $this->_usu_nome,
            'usu_pessoa' =>  $this->_usu_pessoa,
            'usu_orgao' =>  $this->_usu_orgao,
            'usu_sala' =>  $this->_usu_sala,
            'usu_ramal' =>  $this->_usu_ramal,
            'usu_nivel' =>  $this->_usu_nivel,
            'usu_exibicao' =>  $this->_usu_exibicao,
            'usu_SQL_login' =>  $this->_usu_SQL_login,
            'usu_SQL_senha' =>  $this->_usu_SQL_senha,
            'usu_duracao_senha' =>  $this->_usu_duracao_senha,
            'usu_data_validade' =>  $this->_usu_data_validade,
            'usu_limite_utilizacao' =>  $this->_usu_limite_utilizacao,
            'usu_senha' =>  $this->_usu_senha,
            'usu_validacao' =>  $this->_usu_validacao,
            'usu_status' =>  $this->_usu_status,
            'usu_seguranca' =>  $this->_usu_seguranca,
            'usu_data_atualizacao' => $this->_usu_data_atualizacao,
            'usu_conta_nt' =>  $this->_usu_conta_nt,
            'usu_dica_intranet' =>  $this->_usu_dica_intranet,
            'usu_localizacao' =>  $this->_usu_localizacao,
            'usu_andar' =>  $this->_usu_andar,
            'usu_telefone' =>  $this->_usu_telefone
        ];

        return $usuario;
    }
}
