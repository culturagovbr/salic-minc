<?php

/**
 * Classe responsável por fazer a autenticação Utilizando o Login Cidadão.
 * @author Vinícius Feitosa da Silva <viniciusfesil@mail.com>
 * @since 06/10/16 11:25
 */
class Autenticacao_LogincidadaoController extends MinC_Auth_Controller_AOAuth
{
    /**
     * @author Vinícius Feitosa da Silva <viniciusfesil@mail.com>
     * @author Cleber Santos <oclebersantos@gmail.com>
     * @return void
     */
    public function successAction()
    {
        $sgcAcesso = new Autenticacao_Model_Sgcacesso();
        $sgcAcesso->loginSemCript(1, 1);


        $this->_helper->viewRenderer->setNoRender(true);
        try {
            $objSgcAcesso = new Autenticacao_Model_Sgcacesso();
            $objIdentity = Zend_Auth::getInstance()->getIdentity();

            $this->validarAcesso($objIdentity->auth['raw']);

            $cpf = $objIdentity->auth['raw']['cpf'];
            $id = $objIdentity->auth['raw']['id'];

            $arraySGCAcesso = $objSgcAcesso->buscar(array('cpf = ?' => $cpf))->toArray();

            $senhaCriptografada = EncriptaSenhaDAO::encriptaSenha($cpf, $id);
            if ($senhaCriptografada != $arraySGCAcesso[0]['Senha']) {
                $senhaCriptografada = $arraySGCAcesso[0]['Senha'];
            }

            $objSgcAcesso->loginSemCript($cpf, $senhaCriptografada);
            //$this->_helper->flashMessenger->addMessage("Bem vindo!");
            $urlRedirecionamento = '/principalproponente';
        } catch (Exception $objException) {
            $this->_helper->flashMessenger->addMessage($objException->getMessage());
            $urlRedirecionamento = '/autenticacao';
        }
        $this->redirect($urlRedirecionamento);
    }

    /**
     * @author Vinícius Feitosa da Silva <viniciusfesil@mail.com>
     * @return void
     */
    public function errorAction()
    {
        parent::message("Não foi possível autenticar na aplicação.", "/autenticacao", "ALERT");
    }

    /**
     * @access public
     * @return void
     * @author Vinícius Feitosa da Silva <viniciusfesil@gmail.com>
     * @author Cleber Santos <oclebersantos@gmail.com>
     */
    public function cadastrarusuarioAction()
    {
        try {
            if (!$_POST) {
                throw new Exception("Tentativa de acesso inv&aacute;lido.");
            }

            $objPost = $this->getAllParams();

            $cpf = Mascara::delMaskCNPJ(Mascara::delMaskCPF($objPost["cpf"]));
            $senhaCriptografada = EncriptaSenhaDAO::encriptaSenha($cpf, $objPost["id"]);
            $arrayDTNascimento = explode("T", $objPost["birthdate"]);
            $dtNascimento = $arrayDTNascimento[0];

            $dados = array(
                "Cpf" => $cpf,
                "Nome" => $objPost["full_name"],
                "DtNascimento" => $dtNascimento,
                "Email" => $objPost["email"],
                "Senha" => $senhaCriptografada,
                "DtCadastro" => date("Y-m-d"),
                "Situacao" => 3,
                "DtSituacao" => date("Y-m-d"),
                "id_login_cidadao" => $objPost["id"]
            );

            $this->validarCadastro($dados);

            $sgcAcesso = new Autenticacao_Model_Sgcacesso();
            $pkSgcAcessoSave = $sgcAcesso->salvar($dados);

            $objAgentes = new Agente_Model_DbTable_Agentes();
            $buscarAgente = $objAgentes->buscar(array('cnpjcpf = ?' => $cpf));
            $idAgenteProp = count($buscarAgente) > 0 ? $buscarAgente[0]->idAgente : 0;
            $objVisao = new Visao();
            $buscarVisao = $objVisao->buscar(array('visao = ?' => 144, 'stativo = ?' => 'A', 'idagente = ?' => $idAgenteProp));

            if (count($buscarVisao->toArray()) > 0) {
                $tbVinculo = new Agente_Model_DbTable_TbVinculo();
                $idResp = $sgcAcesso->buscar(array('Cpf = ?' => $pkSgcAcessoSave));
                $dadosVinculo = array(
                    'idAgenteProponente' => $idAgenteProp
                    ,'dtVinculo' => new Zend_Db_Expr('GETDATE()')
                    ,'siVinculo' => 2
                    ,'idUsuarioResponsavel' => $idResp[0]->IdUsuario
                );
                $tbVinculo->inserir($dadosVinculo);
            }

            parent::message("Bem vindo!", "/principalproponente", "CONFIRM");
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/autenticacao", "ALERT");
        }
    }

    /**
     * @param array $dados
     * @author Vinícius Feitosa da Silva <viniciusfesil@mail.com>
     * @return void
     */
    private function validarCadastro(array $dados)
    {
        $objSgcAcesso = new Autenticacao_Model_Sgcacesso();
        if (!$objSgcAcesso->hasCPFCadastrado($dados['cpf'])) {
            throw new Exception('CNPJ ou CPF j&aacute; cadastrado.');
        }

        if (!$objSgcAcesso->hasEmailCadastrado($dados['email'])) {
            throw new Exception('E-mail j&aacute; cadastrado');
        }
    }

    private function validarAcesso(array $dados)
    {
        if (!$dados['cpf']) {
            throw new Exception('O sistema precisa que seja concedido acesso ao CPF.');
        }

        if (!$dados['email']) {
            throw new Exception('O sistema precisa que seja concedido acesso ao E-Mail.');
        }
    }
}
