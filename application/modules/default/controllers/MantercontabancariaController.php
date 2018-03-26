<?php

class MantercontabancariaController extends MinC_Controller_Action_Abstract
{
    private $modal = "n";

    public function init()
    {
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);
        parent::init();

        //verifica se a funcionadade devera abrir em modal
        if ($this->_request->getParam("modal") == "s") {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $this->modal = "s";
            $this->view->modal = "s";
        } else {
            $this->modal = "n";
            $this->view->modal = "n";
        }
    }

    public function consultarAction()
    {
    }

    public function alterarAction()
    {
        if (isset($_POST['verifica']) and $_POST['verifica'] == 'a') {
            $agencia = $_POST['agencia'];

            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $ba = new BancoAgencia();
            $AgenciaDados = $ba->buscar(array('Agencia = ?' => $_POST['agencia']))->current();

            $a = 0;
            if (count($AgenciaDados)>0) {
                $this->_helper->json(array('resposta'=>true));
            } else {
                $this->_helper->json(array('resposta'=>false));
            }
            $this->_helper->viewRenderer->setNoRender(true);
        }


        $Usuario = new Autenticacao_Model_Usuario(); // objeto usu�rio
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $orgao = $GrupoAtivo->codOrgao;
        $pronac = $this->_request->getParam("pronac");

        $cb = new ContaBancaria();
        $resp = $cb->consultarDadosPorPronac($pronac, $orgao)->current();
        $PronacExistente = $cb->consultarDadosPorPronac($pronac)->current();
        $this->view->DadosBancarios = $resp;

        $tblProjeto = new Projetos();
        $rsProjeto = $tblProjeto->buscar(array('AnoProjeto+Sequencial=?'=>$pronac))->current();
        if (empty($rsProjeto)) {
            if ($this->modal == "s") {
                echo "<br/><br/><br/><br/><center><font color='red'>N&uacute;mero de Pronac inv&aacute;lido!!</font></center>";
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                parent::message("N&uacute;mero de Pronac inv&aacute;lido!", "mantercontabancaria/consultar", "ALERT");
            }
        }

        if (count($resp) < 1 && count($PronacExistente) > 0) {
            if ($this->modal == "s") {
                echo "<br/><br/><br/><br/><center><font color='red'>Voc� n�o tem acesso a esta unidade!</font></center>";
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                parent::message("Voc� n�o tem acesso a esta unidade!", "mantercontabancaria/consultar", "ALERT");
            }
        }

        if (count($resp) > 0) {
            $hd = new tbHistoricoExclusaoConta();
            $historicos = $hd->buscar(array('idContaBancaria = ?' => $resp->IdContaBancaria), array('idHistoricoExclusaoConta Desc'));
            $this->view->Historicos = $historicos;
        } else {
            if ($this->modal == "s") {
                echo "<br/><br/><br/><br/><center><font color='red'>Conta banc�ria inexistente!</font></center>";
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                parent::message("Conta banc�ria inexistente!", "mantercontabancaria/consultar", "ALERT");
            }
        }

        $cap = new Captacao();
        $resultado = $cap->buscar(array('AnoProjeto+Sequencial = ?' => $pronac));
        $resultado2 = $cap->TotalCaptacaoReal($pronac)->current();

        if (count($resultado)>0) {
            if ($resultado2->Soma > 0) {
                $this->view->captacao = true;
            }
        }
    }

    public function imprimirContaBancariaCadastradaAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $orgao = $GrupoAtivo->codOrgao;
        $pronac = $this->_request->getParam("pronac");

        $cb = new ContaBancaria();
        $resp = $cb->consultarDadosPorPronac($pronac, $orgao)->current();
        $this->view->DadosBancarios = $resp;

        if (count($resp) > 0) {
            $hd = new tbHistoricoExclusaoConta();
            $historicos = $hd->buscar(array('idContaBancaria = ?' => $resp->IdContaBancaria), array('idHistoricoExclusaoConta Desc'));
            $this->view->Historicos = $historicos;
        }

        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    public function salvarAction()
    {
        $Usuario = new Autenticacao_Model_Usuario(); // objeto usu�rio
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $orgao = $GrupoAtivo->codOrgao;
        $pronac = $this->_request->getParam('Pronac');
        $he = new tbHistoricoExclusaoConta();
        $cb = new ContaBancaria();
        $resp = $cb->consultarDadosPorPronac($pronac, $orgao)->current();
//            x($idagente->usu_codigo);

        $caminho = $this->_request->getParam("caminho"); //caminho de retorno caso a funcionalidade seja aberta em modal
        $ba = new BancoAgencia();
        $AgenciaDados = $ba->buscar(array('Agencia = ?' => $this->_request->getParam('Agencia')));
        
        if (count($AgenciaDados) > 0) {
            
            //INSERE OS DADOS NA TABELA DE HIST�RICO - SAC.dbo.tbHistoricoExclusaoConta
            $dadosInsert = array(
                'idContaBancaria' => $resp->IdContaBancaria,
                'Banco' => $resp->Banco,
                'Agencia' => $resp->Agencia,
                'ContaBloqueada' => $resp->ContaBloqueada,
                    'ContaLivre' => $resp->ContaLivre,
                'DtExclusao' => new Zend_Db_Expr('GETDATE()'),
                'Motivo' => $this->_request->getParam('justificativa'),
                'idUsuario' => $idagente['usu_codigo']
            );
            
            $id = $he->inserir($dadosInsert);

            $dados = array(
                'Banco' => '001',
                'Agencia' => $this->_request->getParam('Agencia'),
                'ContaBloqueada' => $this->_request->getParam('ContaBloqueada'),
                'ContaLivre' => $this->_request->getParam('ContaLivre'),
                'Logon' => $idagente['usu_codigo'],
                'DtLoteRemessaCL' => new Zend_Db_Expr('GETDATE()'),
                'DtLoteRemessaCB' => new Zend_Db_Expr('GETDATE()')
            );
            
            $id = $cb->alterar($dados, array('idContaBancaria = ?' => $resp->IdContaBancaria));
            
            //parent::message("Cadastro realizado com sucesso!", "mantercontabancaria/alterar?pronac=$pronac", "CONFIRM");
            
            if (!empty($caminho)) {
                parent::message("Cadastro realizado com sucesso!", $caminho, "CONFIRM");
            } else {
                parent::message("Cadastro realizado com sucesso!", "mantercontabancaria/alterar?pronac=" . $pronac, "CONFIRM");
            }
        } else {
            if (!empty($caminho)) {
                parent::message("Ag&ecirc;ncia n&atilde;o cadastrada!", $caminho, "ALERT");
            } else {
                parent::message("Ag&ecirc;ncia n&atilde;o cadastrada!", "mantercontabancaria/alterar?pronac=$pronac", "ALERT");
            }
        }
    }

    public function regularidadeProponenteAction()
    {
    }

    public function consultarregularidadeproponenteAction()
    {
        if (isset($_POST['cpfCnpj']) || isset($_GET['cpfCnpj'])) {
            if (isset($_POST['cpfCnpj'])) {
                $cnpjcpf = str_replace("/", "", str_replace("-", "", str_replace(".", "", $_POST['cpfCnpj'])));
                $cnpjcpf = Mascara::delMaskCPFCNPJ($cnpjcpf);
            } elseif (isset($_GET['cpfCnpj'])) {
                $cnpjcpf = $_GET['cpfCnpj'];
                $cnpjcpf = Mascara::delMaskCPFCNPJ($cnpjcpf);
            }

            if (strlen($cnpjcpf) == 11) {
                $this->proponente = "PF";
            } else {
                $this->proponente = "PJ";
            }

            if (empty($cnpjcpf)) {
                parent::message('Por favor, informe o campo CPF/CNPJ!', 'mantercontabancaria/regularidade-proponente', 'ALERT');
            }
            if ($this->proponente == "PF" && !Validacao::validarCPF($cnpjcpf)) {
                parent::message('Por favor, informe um CPF v&aacute;lido!', 'mantercontabancaria/regularidade-proponente', 'ALERT');
            }
            if ($this->proponente == "PJ" && !Validacao::validarCNPJ($cnpjcpf)) {
                parent::message('Por favor, informe um CNPJ v&aacute;lido!', 'mantercontabancaria/regularidade-proponente', 'ALERT');
            }

            $this->view->cgccpf = $cnpjcpf;
            $agentes = new Agente_Model_DbTable_Agentes();
            $interessados = new Interessado();
            $buscaAgentes = $agentes->buscar(array('CNPJCPF = ?' => $cnpjcpf));

            $buscaInteressados = $interessados->buscar(array('CgcCpf = ?' => $cnpjcpf));

            if (!$buscaAgentes[0] or !$buscaInteressados[0]) {
                parent::message("O Agente n&atilde;o est&aacute; cadastrado!", 'mantercontabancaria/regularidade-proponente', "ERROR");
            }

            $nomes = new Nomes();
            $buscaNomes = $nomes->buscar(array('idAgente = ?' => $buscaAgentes[0]->idAgente));
            $nomeProponente = $buscaNomes[0]->Descricao;
            $this->view->nomeProponente = $nomeProponente;

            $paRegularidade = new paRegularidade();
            $consultaRegularidade = $paRegularidade->exec($cnpjcpf);
            $this->view->resultadoRegularidade = $consultaRegularidade;

            $auth = Zend_Auth::getInstance(); // instancia da autentica��o
            if (strlen(trim($auth->getIdentity()->usu_identificacao)) == 11) {
                $cpfcnpjUsuario = Mascara::addMaskCPF(trim($auth->getIdentity()->usu_identificacao));
            } else {
                $cpfcnpjUsuario = Mascara::addMaskCNPJ(trim($auth->getIdentity()->usu_identificacao));
            }
            $this->view->dadosUsuarioConsulta = '( '. $cpfcnpjUsuario .' ) '.$auth->getIdentity()->usu_nome.' - '.date('d/m/Y').' �s '.date('h:i:s');
        } else {
            parent::message("Por favor, informe o campo CPF/CNPJ!", 'mantercontabancaria/regularidade-proponente', "ERROR");
        }
    }

    public function imprimirConsultaRegularidadeAction()
    {
        if (isset($_POST['cpfCnpj']) || isset($_GET['cpfCnpj'])) {
            if (isset($_POST['cpfCnpj'])) {
                $cnpjcpf = str_replace("/", "", str_replace("-", "", str_replace(".", "", $_POST['cpfCnpj'])));
                $cnpjcpf = Mascara::delMaskCPFCNPJ($cnpjcpf);
            } elseif (isset($_GET['cpfCnpj'])) {
                $cnpjcpf = $_GET['cpfCnpj'];
                $cnpjcpf = Mascara::delMaskCPFCNPJ($cnpjcpf);
            }

            if (strlen($cnpjcpf) == 11) {
                $this->proponente = "PF";
            } else {
                $this->proponente = "PJ";
            }

            if (empty($cnpjcpf)) {
                parent::message('Por favor, informe o campo CPF/CNPJ!', 'mantercontabancaria/regularidade-proponente', 'ALERT');
            }
            if ($this->proponente == "PF" && !Validacao::validarCPF($cnpjcpf)) {
                parent::message('Por favor, informe um CPF v&aacute;lido!', 'mantercontabancaria/regularidade-proponente', 'ALERT');
            }
            if ($this->proponente == "PJ" && !Validacao::validarCNPJ($cnpjcpf)) {
                parent::message('Por favor, informe um CNPJ v&aacute;lido!', 'mantercontabancaria/regularidade-proponente', 'ALERT');
            }

            $this->view->cgccpf = $cnpjcpf;
            $agentes = new Agente_Model_DbTable_Agentes();
            $interessados = new Interessado();
            $buscaAgentes = $agentes->buscar(array('CNPJCPF = ?' => $cnpjcpf));

            $buscaInteressados = $interessados->buscar(array('CgcCpf = ?' => $cnpjcpf));

            if (!$buscaAgentes[0] or !$buscaInteressados[0]) {
                parent::message("O Agente n&atilde;o est&aacute; cadastrado!", 'mantercontabancaria/regularidade-proponente', "ERROR");
            }

            $nomes = new Nomes();
            $buscaNomes = $nomes->buscar(array('idAgente = ?' => $buscaAgentes[0]->idAgente));
            $nomeProponente = $buscaNomes[0]->Descricao;
            $this->view->nomeProponente = $nomeProponente;

            $paRegularidade = new paRegularidade();
            $consultaRegularidade = $paRegularidade->exec($cnpjcpf);
            $this->view->resultadoRegularidade = $consultaRegularidade;

            $auth = Zend_Auth::getInstance(); // instancia da autentica��o
            if (strlen(trim($auth->getIdentity()->usu_identificacao)) == 11) {
                $cpfcnpjUsuario = Mascara::addMaskCPF(trim($auth->getIdentity()->usu_identificacao));
            } else {
                $cpfcnpjUsuario = Mascara::addMaskCNPJ(trim($auth->getIdentity()->usu_identificacao));
            }
            $this->view->dadosUsuarioConsulta = '( '. $cpfcnpjUsuario .' ) '.$auth->getIdentity()->usu_nome.' - '.date('d/m/Y').' �s '.date('H:i:s');
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        } else {
            parent::message("Por favor, informe o campo CPF/CNPJ!", 'mantercontabancaria/regularidade-proponente', "ERROR");
        }
    }
}
