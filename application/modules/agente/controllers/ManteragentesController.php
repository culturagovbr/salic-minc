<?php

/**
 * ManterAgentesController
 * @author Equipe RUP - Politec
 * @since 09/08/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @link http://www.politec.com.br
 * @copyright © 2010 - Politec - Todos os direitos reservados.
 */
class Agente_ManterAgentesController extends MinC_Controller_Action_Abstract
{
    /**
     * @var integer (variavel com o id do usuario logado)
     * @access private
     */
    private $getIdUsuario = 0;


    /**
     * Reescreve o metodo init()
     *
     * @name init
     * @access public
     * @param void
     * @return void
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  25/08/2016
     *
     * @todo retirar esse tanto de consulta da init, deixando apenas nos metodos que utilizam.
     * @todo retirar os id fixos no codigo.
     */
    public function init()
    {
        $mapperVerificacao = new Agente_Model_VerificacaoMapper();
        $mapperUF = new Agente_Model_UFMapper();
        $mapperArea = new Agente_Model_AreaMapper();

        # Pega a autenticacao
        $auth = Zend_Auth::getInstance()->getIdentity();
        $arrAuth = array_change_key_case((array) $auth);

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        # define as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 144;  // Proponente
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 118; // Componente da Comissao
        $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento

        # pega do readequacao
        if (isset($arrAuth['cpf']) && !empty($arrAuth['cpf']) && isset($_GET['acao']) && $_GET['acao'] == 'cc' && isset($_GET['cpf']) && !empty($_GET['cpf'])) {
            parent::perfil(2); // scriptcase
        }

        # pega do readequacao
        if (isset($arrAuth['cpf']) && !empty($arrAuth['cpf']) && !isset($_GET['acao']) && !isset($_GET['cpf']) && empty($_GET['cpf'])) {
            parent::perfil(4, $PermissoesGrupo); // migracao e novo salic
        } elseif (isset($arrAuth['usu_codigo']) && !empty($arrAuth['usu_codigo'])) {
            parent::perfil(1, $PermissoesGrupo); // migracao e novo salic
        } else {
            parent::perfil(4, $PermissoesGrupo); // migracao e novo salic
        }

        # autenticacao novo salic
        if (isset($arrAuth['usu_codigo'])) {
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($arrAuth['usu_codigo']);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario["idAgente"] : 0;
        } else { // autenticacao scriptcase
            $this->getIdUsuario = (isset($_GET["IdUsuario"])) ? $_GET["IdUsuario"] : 0;
        }

        if (!$this->getIdUsuario) {
            $this->getIdUsuario = $arrAuth['IdUsuario'];
        }
        $Cpflogado = $this->getIdUsuario;
        $this->view->cpfLogado = $Cpflogado;
        $this->view->grupoativo = $GrupoAtivo->codGrupo;
        $this->view->comboestados = $mapperUF->fetchPairs('idUF', 'Sigla');
        $this->view->combotiposenderecos = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 2));
        $this->view->combotiposlogradouros = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 13));
        $this->view->comboareasculturais = $mapperArea->fetchPairs('codigo', 'descricao');
        $this->view->combotipostelefones = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 3));
        $this->view->combotiposemails = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 4, 'idverificacao' => array(28, 29)));

        //Monta o combo das visees disponiveis
        $visaoTable = new Agente_Model_DbTable_Visao();
        $visoes = $visaoTable->buscarVisao(null, null, true);

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $GrupoAtivo = $GrupoAtivo->codGrupo;

        $viesoesNew = array();
        if (isset($arrAuth['cpf'])) {
            $viesoesNew[144] = 'Proponente';
            $this->view->grupoativo = 144;
        } else {
            foreach ($visoes as $visaoGrupo) {
                if ($GrupoAtivo == 93 and ($visaoGrupo->idverificacao == 209 or $visaoGrupo->idverificacao == 216)) {
                    $viesoesNew[$visaoGrupo->idverificacao] = $visaoGrupo->descricao;
                }
                if ($GrupoAtivo == 94 and $visaoGrupo->idverificacao == 209) {
                    $viesoesNew[$visaoGrupo->idverificacao] = $visaoGrupo->descricao;
                }
                if ($GrupoAtivo == 97) {
                    $viesoesNew[$visaoGrupo->idverificacao] = $visaoGrupo->descricao;
                }
                if ($GrupoAtivo == 120 and $visaoGrupo->idverificacao == 210) {
                    $viesoesNew[$visaoGrupo->idverificacao] = $visaoGrupo->descricao;
                }
                if ($GrupoAtivo == 118 and $visaoGrupo->idverificacao == 210) {
                    $viesoesNew[$visaoGrupo->idverificacao] = $visaoGrupo->descricao;
                }
                if ($GrupoAtivo == 122 and ($visaoGrupo->idverificacao == 210 or $visaoGrupo->idverificacao == 216 or $GrupoAtivo == 123)) {
                    $viesoesNew[$visaoGrupo->idverificacao] = $visaoGrupo->descricao;
                }
            }
        }

        $this->view->combovisoes = $viesoesNew;
        parent::init();
    }

    /**
     * Metodo index()
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
    } // fecha metodo indexAction()


    /**
     * Metodo para realizar a buscar de agentes por cpf/cnpj ou por nome
     * @access public
     * @param void
     * @return void
     */
    public function buscaragenteAction()
    {
        // caso o formulario seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formulario
            $post = Zend_Registry::get('post');
            $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->cpf)); // deleta a mascara
            $nome = $post->nome;

            try {
                // validacao dos campos
                if (empty($cpf) && empty($nome)) {
                    throw new Exception("Dados obrigatórios não informados:<br /><br />É necessário informar o CPF/CNPJ ou o Nome!");
                } elseif (!empty($cpf) && strlen($cpf) != 11 && strlen($cpf) != 14) { // valida cnpj/cpf
                    throw new Exception("O CPF/CNPJ informado é inválido!");
                } elseif (!empty($cpf) && strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) { // valida cpf
                    throw new Exception("O CPF informado é inválido!");
                } elseif (!empty($cpf) && strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf)) { // valida cnpj
                    throw new Exception("O CNPJ informado é inválido!");
                } else {
                    // redireciona para a pagina com a busca dos dados com paginacao
                    $this->_redirect("agente/manteragentes/listaragente?cpf=" . $cpf . "&nome=" . $nome);
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                $this->view->message = $e->getMessage();
                $this->view->message_type = "ERROR";
                $this->view->cpf = !empty($cpf) ? Validacao::mascaraCPFCNPJ($cpf) : ''; // caso exista, adiciona a mascara
                $this->view->nome = $nome;
            }
        } // fecha if
    } // fecha metodo buscaragenteAction()


    /**
     * Metodo para listar os agentes com paginacao
     * @access public
     * @param void
     * @return void
     */
    public function listaragenteAction()
    {
        // recebe os dados via get
        $get = Zend_Registry::get('get');
        $cpf = $get->cpf;
        $nome = $get->nome;

        // realiza a busca por cpf e/ou nome
        $buscar = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf, $nome);

        if (!$buscar) {
            // redireciona para a pagina de cadastro de agentes, e, exibe uma notificacao relativa ao cadastro
            parent::message("Agente não cadastrado!<br /><br />Por favor, cadastre o mesmo no formulário abaixo!", "agente/manteragentes/agentes?acao=cc&cpf=" . $cpf . "&nome=" . $nome, "ALERT");
        } else {
            // ========== INICIO PAGINACAO ==========
            // criando a paginacao
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
            $paginator = Zend_Paginator::factory($buscar); // dados a serem paginados

            // pagina atual e quantidade de itens por pagina
            $currentPage = $this->_getParam('page', 1);
            $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(10); // 10 por pagina
            // ========== FIM PAGINACAO ==========

            $this->view->buscar = $paginator;
            $this->view->qtdAgentes = count($buscar); // quantidade de agentes
        } // fecha else
    }

    /**
     * Metodo com o formulario para cadastro de agentes
     * @access public
     * @param void
     * @return void
     */
    public function agentesAction()
    {
        if (isset($_POST['cep'])) {
            xd(1);
        }
    }

    /**
     * Metodo com o formulario para cadastro de dirigentes
     * @access public
     * @param void
     * @return void
     */
    public function dirigentesAction()
    {
        // configuracoes do layout padrao para o scriptcase
        // retira o topo e o rodape
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
    } // fecha metodo dirigentesAction()


    /**
     * Metodo com a pagina de alteracao de visao
     * @access public
     * @param void
     * @return void
     */
    public function alterarvisaoAction()
    {
        // recebe o id do agente via get
        $get = Zend_Registry::get('get');
        $idAgente = $get->idAgente;

        // busca todas as visoes
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $GrupoAtivo = $GrupoAtivo->codGrupo;

        $visaoTable = new Agente_Model_DbTable_Visao();
        $visoes = $visaoTable->buscarVisao(null, null, true);
        $a = 0;
        foreach ($visoes as $visaoGrupo) {
            if ($GrupoAtivo == 93 and $visaoGrupo->idVerificacao == 209) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 94 and $visaoGrupo->idVerificacao == 209) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 97) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 120 and $visaoGrupo->idVerificacao == 210) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 118 and $visaoGrupo->idVerificacao == 210) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            $a++;
        }
        $this->view->visao = $select;

        // busca todas as visoes do agente
        $visaoTable = new Agente_Model_DbTable_Visao();
        $visoes = $visaoTable->buscarVisao($idAgente);
        $a = 0;
        foreach ($visoes as $visaoGrupo) {
            if ($GrupoAtivo == 93 and ($visaoGrupo->Visao == 209 or $visaoGrupo->Visao == 144)) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 94 and ($visaoGrupo->Visao == 209 or $visaoGrupo->Visao == 144)) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 97) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 120 and ($visaoGrupo->Visao == 210 or $visaoGrupo->Visao == 144)) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 118 and ($visaoGrupo->Visao == 210 or $visaoGrupo->Visao == 144)) {
                $selectCad[$a]['idVerificacao'] = $visaoGrupo->Visao;
                $selectCad[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            $a++;
        }
        $this->view->visaoAgente = $visoes;

        // busca o agente pelo id
        $this->view->agente = Agente_Model_ManterAgentesDAO::buscarAgentes(null, null, $idAgente);

        // caso o formulario seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formulario
            $post = Zend_Registry::get('post');
            $idAgente = $post->idAgente;
            $visaoAgente = $post->visaoAgente;

            try {
                // ========== ATUALIZA AS VISOES DO AGENTE ==========

                // exclui todas as visoes do agente
                $visaoTable = new Agente_Model_DbTable_Visao();
                $excluir = $visaoTable->excluirVisao($idAgente);

                // cadastra todas as visoes do agente
                foreach ($visaoAgente as $visao) :
                    $dados = array(
                        'idAgente' => $idAgente,
                        'Visao' => $visao,
                        'Usuario' => $this->getIdUsuario, // código do usuario logado
                        'stAtivo' => 'A');
                $cadastrar = $visaoTable->cadastrarVisao($dados);
                endforeach;

                if ($cadastrar) {
                    parent::message("Alteração realizada com sucesso!", "manteragentes/alterarvisao?idAgente=" . $idAgente, "CONFIRM");
                } else {
                    throw new Exception("Erro ao efetuar alteração das visões do agente!");
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message = $e->getMessage();
                $this->vies->message_type = "ERROR";
            }
        } // fecha if
    } // fecha metodo alterarvisaoAction()


    /**
     * Metodo para salvar os dados do agente no banco de dados e fazer a busca assim que o cpf/cnpj for informado
     *
     * @name salvaragenteAction
     *
     * @author Ruy Junior Ferreira Silva
     * @since 31/08/2016
     */
    public function salvaragenteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $result = array();
        # caso o cpf/cnpj tenha sido informado
        if (isset($_REQUEST['cpf']) && !empty($_REQUEST['cpf'])) {
            $cpf = $_REQUEST['cpf'];
            $agentesTable = new Agente_Model_DbTable_Agentes();

            if ((strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) || (strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf))) {
                # cpf/cnpj invalidos
                $result[0]['msgCPF'] = 'not';
            } else {
                $result = $agentesTable->buscarAgentes($cpf);
                # caso o agente nao esteja cadastrado, realizara o cadastro de um novo
                if ($result) {
                    $result[0]['Agente'] = utf8_encode('cadastrado'); # o agente ja encontra-se cadastrado
                } else {
                    $data = array('cnpjcpf' => $cpf);
                    $agentesMapple = new Agente_Model_AgentesMapper();
                    $insere = $agentesMapple->save(new Agente_Model_Agentes($data));
                    $result[0]['Agente'] = 'novo';
                }
            }

            //$this->_helper->json($result);
            $this->_helper->json($result);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }


    /**
     * Metodo para salvar os dados do dirigente no banco de dados e fazer a busca
     * assim que o cpf/cnpj for informado
     * @access public
     * @param void
     * @return void
     */
    public function salvardirigenteAction()
    {
        $i = 0; // inicializa o contador
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $this->_helper->viewRenderer->setNoRender(true);
        $novos_valores = array(); // array com os dados do agente
        $v = ''; // flag verificadora de dados validos/invalidos

        if ($_REQUEST['cpf'] && $_REQUEST['idAgenteGeral']) { // caso o cpf/cnpj tenha sido informado
            $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($_REQUEST['cpf'])); // deleta as mascaras
            $idAgenteGeral = $_REQUEST['idAgenteGeral']; // idVinculoPrincipal

            // cpf/cnpj invalidos
            if ((strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) || (strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf))) {
                $v = 'not';
                $novos_valores[$i]['msgCPF'] = utf8_encode($v);
            } else { // cpf/cnpj validos
                $v = 'ok';
                $novos_valores[$i]['msgCPF'] = utf8_encode($v);

                // busca os dados do dirigente
                $dados = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf);

                // caso o dirigente nao esteja cadastrado, realizara o cadastro de um novo
                if (!$dados) {
                    // busca os dados do vinculo do dirigente (idVinculoPrincipal)
                    //$buscarAgente = Agente_Model_ManterAgentesDAO::buscarAgentes(null, null, $idAgenteGeral);

                    // cadastra o dirigente
                    $arrayCNPJCPF = array(
                        'CNPJCPF' => $cpf
                        //,'CNPJCPFSuperior' => $buscarAgente[0]->CNPJCPF
                    );
                    $insere = Agente_Model_ManterAgentesDAO::cadastrarAgente($arrayCNPJCPF);
                    $novos_valores[$i]['Agente'] = utf8_encode('novo');
                } else { // o agente ja encontra-se cadastrado, realizara a alteracao
                    $novos_valores[$i]['Agente'] = utf8_encode('cadastrado');
                }

                // busca os dados do agente pelo cpf/cnpj
                $novosdados = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf);

                foreach ($novosdados as $dado) :
                    $novos_valores[$i]['idAgente'] = utf8_encode($dado->idAgente);
                $novos_valores[$i]['Nome'] = utf8_encode($dado->Nome);
                $novos_valores[$i]['CEP'] = !empty($dado->CEP) ? utf8_encode(Mascara::addMaskCEP($dado->CEP)) : ' ';
                $novos_valores[$i]['UF'] = utf8_encode($dado->UF);
                $novos_valores[$i]['Cidade'] = utf8_encode($dado->Cidade);
                $novos_valores[$i]['dsCidade'] = utf8_encode($dado->dsCidade);
                $novos_valores[$i]['TipoEndereco'] = utf8_encode($dado->TipoEndereco);
                $novos_valores[$i]['TipoLogradouro'] = utf8_encode($dado->TipoLogradouro);
                $novos_valores[$i]['Logradouro'] = utf8_encode($dado->Logradouro);
                $novos_valores[$i]['Numero'] = utf8_encode($dado->Numero);
                $novos_valores[$i]['Complemento'] = utf8_encode($dado->Complemento);
                $novos_valores[$i]['Bairro'] = utf8_encode($dado->Bairro);
                $novos_valores[$i]['DivulgarEndereco'] = utf8_encode($dado->DivulgarEndereco);
                $novos_valores[$i]['EnderecoCorrespondencia'] = utf8_encode($dado->EnderecoCorrespondencia);

                // areas e segmentos
                $novos_valores[$i]['cdArea'] = utf8_encode($dado->cdArea);
                $novos_valores[$i]['dsArea'] = utf8_encode($dado->dsArea);
                $novos_valores[$i]['cdSegmento'] = utf8_encode($dado->cdSegmento);
                $novos_valores[$i]['dsSegmento'] = utf8_encode($dado->dsSegmento);
                endforeach;
            } // fecha else

            $this->_helper->json($novos_valores);
        } // fecha if
        else {
            $this->_helper->viewRenderer->setNoRender(true);
        }
    } // fecha metodo salvardirigenteAction()


    /**
     * Metodo para buscar todos os e-mails do agente
     * @access public
     * @param void
     * @return void
     */
    public function buscaremailsAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $this->_helper->viewRenderer->setNoRender(true);

        // caso o id do agente esteja definido
        if ($_REQUEST['idAgente']) {
            $Emails = Agente_Model_ManterAgentesDAO::buscarEmails($_REQUEST['idAgente']); // busca os e-mails do agente
            $novos_emails = array();

            $e = 0;
            foreach ($Emails as $dado) :
                $novos_emails[$e]['idInternet'] = utf8_encode($dado->idInternet);
            $novos_emails[$e]['idAgente'] = utf8_encode($dado->idAgente);
            $novos_emails[$e]['TipoInternet'] = utf8_encode($dado->TipoInternet);
            $novos_emails[$e]['tipo'] = utf8_encode($dado->tipo);
            $novos_emails[$e]['Descricao'] = utf8_encode($dado->Descricao);
            $novos_emails[$e]['Status'] = utf8_encode($dado->Status);
            $novos_emails[$e]['Divulgar'] = utf8_encode($dado->Divulgar);
            $e++;
            endforeach;

            $this->_helper->json($novos_emails);
        } // fecha if
    } // fecha metodo buscaremailsAction()


    /**
     * Metodo para buscar todos os telefones do agente
     * @access public
     * @param void
     * @return void
     */
    public function buscarfonesAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $this->_helper->viewRenderer->setNoRender(true);

        // caso o id do agente esteja definido
        if ($_REQUEST['idAgente']) {
            $Fones = Agente_Model_ManterAgentesDAO::buscarFones($_REQUEST['idAgente']); // busca todos os telefones do agente
            $novos_fones = array();

            $f = 0;
            foreach ($Fones as $dado) :
                $novos_fones[$f]['TipoTelefone'] = utf8_encode($dado->TipoTelefone);
            $novos_fones[$f]['dsTelefone'] = utf8_encode($dado->dsTelefone);
            $novos_fones[$f]['UF'] = utf8_encode($dado->UF);
            $novos_fones[$f]['ufSigla'] = utf8_encode($dado->ufSigla);
            $novos_fones[$f]['DDD'] = utf8_encode($dado->DDD);
            $novos_fones[$f]['Codigo'] = utf8_encode($dado->Codigo);
            $novos_fones[$f]['Numero'] = utf8_encode($dado->Numero);
            $novos_fones[$f]['Divulgar'] = utf8_encode($dado->Divulgar);
            $f++;
            endforeach;

            $this->_helper->json($novos_fones);
        } // fecha if
    } // fecha metodo buscarfonesAction()


    public function buscarenderecosAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $this->_helper->viewRenderer->setNoRender(true);

        // caso o id do agente esteja definido
        if ($_REQUEST['idAgente']) {
            $Enderecos = Agente_Model_ManterAgentesDAO::buscarEnderecos($_REQUEST['idAgente']); // busca todos os enderecos do agente
            $novos_enderecos = array();

            $E = 0;
            foreach ($Enderecos as $dado) :
                $enderecos[$E]['Logradouro'] = utf8_encode($dado->Logradouro);
            $enderecos[$E]['TipoLogradouro'] = $dado->TipoLogradouro;
            $enderecos[$E]['Numero'] = $dado->Numero;
            $enderecos[$E]['Bairro'] = utf8_encode($dado->Bairro);
            $enderecos[$E]['Complemento'] = utf8_encode($dado->Complemento);
            $enderecos[$E]['Cep'] = $dado->Cep;
            $enderecos[$E]['Status'] = utf8_encode($dado->Status);
            $enderecos[$E]['Divulgar'] = utf8_encode($dado->Divulgar);
            $enderecos[$E]['Usuario'] = utf8_encode($dado->Usuario);
            $enderecos[$E]['TipoEndereco'] = utf8_encode($dado->TipoEndereco);
            $enderecos[$E]['CodTipoEndereco'] = $dado->CodTipoEndereco;
            $enderecos[$E]['Municipio'] = utf8_encode($dado->Municipio);
            $enderecos[$E]['CodMun'] = $dado->CodMun;
            $enderecos[$E]['UF'] = utf8_encode($dado->UF);
            $enderecos[$E]['CodUF'] = $dado->CodUF;
            $E++;
            endforeach;

            $this->_helper->json($enderecos);
        } // fecha if
    } // fecha metodo buscarenderecosAction()


    /**
     * Metodo para buscar todos os dirigentes do agente
     * @access public
     * @param void
     * @return void
     */
    public function buscardirigentesAction()
    {
        $gmtDate = gmdate("D, d M Y H:i:s");
        header("Expires: {$gmtDate} GMT");
        header("Last-Modified: {$gmtDate} GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        $this->_helper->layout->disableLayout(); // desabilita o layout

        $post = Zend_Registry::get('post');
        $cnpjcpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->cnpj_cpf));
        $idAgenteGeral = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->idAgente));

        if (!empty($cnpjcpf) && !empty($idAgenteGeral)) {
            // busca os dirigentes vinculados ao cnpj/cpf informado
            //$Dirigentes = Agente_Model_ManterAgentesDAO::buscarVinculados($cnpjcpf);
            $Dirigentes = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, null, null, $idAgenteGeral);
            $this->view->Dirigentes = $Dirigentes;
        }
    } // fecha metodo buscardirigentesAction()


    /**
     * Metodo para buscar as areas e segmentos culturais do agente
     * @access public
     * @param void
     * @return void
     */
    public function buscaareasegmentoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $this->_helper->viewRenderer->setNoRender(true);

        // caso a area cultural esteja definida
        if ($_REQUEST['area']) {
            $novos_dados = array();
            $i = 0;

            // busca os agentes vinculados a area/segmento cultutal (, $_REQUEST['segmento'])
            $dados = TitulacaoConselheiroDAO::buscaAreaSegmento($_REQUEST['area']);

            // pega a quantidade de titulares na area
            $Q_titulares = TitulacaoConselheiroDAO::buscaTitularArea($_REQUEST['area']);
            $novos_dados[$i]['Q_titulares'] = utf8_encode($Q_titulares[0]->QTD);

            // pega a quantidade de suplentes na area
            $Q_suplentes = TitulacaoConselheiroDAO::buscaSuplentesArea($_REQUEST['area']);
            $novos_dados[$i]['Q_suplentes'] = utf8_encode($Q_suplentes[0]->QTD);

            // caso nao existam mais vagas para titular e suplentes
            if ($Q_titulares[0]->QTD >= 1 && $Q_suplentes[0]->QTD >= 2) {
                $novos_dados[$i]['msgAS'] = utf8_encode('A Área Cultural selecionada já conta com 1 Titular e 2 Suplentes!');
            } elseif ($Q_titulares[0]->QTD == 0 && $Q_suplentes[0]->QTD == 0) {
                $novos_dados[0]['Nome'] = 'Sem cadastro';
                $novos_dados[0]['Titular'] = '';
                $novos_dados[0]['msgAS'] = utf8_encode('Você pode cadastrar <strong> 1 </strong> Titular e <strong>  2 </strong> Suplente(s)!');
            } else {
                $titularesDisponives = 1 - (int)$Q_titulares[0]->QTD;
                $suplentesDisponives = 2 - (int)$Q_suplentes[0]->QTD;
                $novos_dados[$i]['msgAS'] = utf8_encode('Você pode cadastrar <strong>' . $titularesDisponives . '</strong> Titular e <strong>' . $suplentesDisponives . '</strong> Suplente(s)!');
            }

            // pega os nomes de titulares e suplentes cadastrados
            foreach ($dados as $dado) :
                $novos_dados[$i]['Nome'] = utf8_encode($dado->Nome);
            $novos_dados[$i]['Titular'] = (utf8_encode($dado->stTitular) == 1) ? '(Titular)' : '(Suplente)';
            $i++;
            endforeach;

            $this->_helper->json($novos_dados);
        } // fecha if
    } // fecha metodo buscaareasegmentoAction()


    /**
     * Metodo para buscar as visoes do agente
     * @access public
     * @param void
     * @return void
     */
    public function buscarvisaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $this->_helper->viewRenderer->setNoRender(true);

        // caso o id do agente esteja definido
        if ($_REQUEST['idAgente']) {
            $novos_dados = array();
            $i = 0;

            // busca as visoes vinculadas ao agente
            $visaoTable = new Agente_Model_DbTable_Visao();
            $dados = $visaoTable->buscarVisao($_REQUEST['idAgente']);

            foreach ($dados as $dado) :
                $novos_dados[$i]['Visao'] = utf8_encode($dado->Visao);
            $novos_dados[$i]['Descricao'] = utf8_encode($dado->Descricao);
            $novos_dados[$i]['verificacao'] = utf8_encode($dado->idVerificacao);
            $novos_dados[$i]['area'] = ($dado->area) ? utf8_encode($dado->area) : 'false';
            $i++;
            endforeach;

            $this->_helper->json($novos_dados);
        } // fecha if
    } // fecha metodo buscarvisaoAction()

    /**
     * Metodo para gravacao de todos os dados do agente
     *
     * @name gravaragentecompletoAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  06/09/2016
     */
    public function gravaragentecompletoAction()
    {
        if ($this->getRequest()->isPost()) {
            $arrPost = array_change_key_case($this->getRequest()->getPost());
            $arrPost['IdUsuario'] = $this->getIdUsuario;
            $arrPost['cpf'] = Mascara::delMaskCPF(Mascara::delMaskCNPJ($arrPost['cpf']));
            if ($arrPost['idagente'] === '') {
                $tblAgentes = new Agente_Model_DbTable_Agentes();
                $result = $tblAgentes->findBy(array('cnpjcpf' => $arrPost['cpf']));
                $arrPost['idagente'] = $result['idagente'];
            }

            $mprNomes = new Agente_Model_NomesMapper();
            //$mprNomes->beginTransaction();
            try {
                # Salvando o nome.
                $mprNomes->saveCustom($arrPost);

                # Salvando a visao.
                $mprVisao = new Agente_Model_VisaoMapper();
                $mprVisao->saveCustom($arrPost);

                # Salvando titulacao (area/segmento do componente da comissao
                $mprTitulacaoConselheiro = new Agente_Model_TbTitulacaoConselheiroMapper();
                $mprTitulacaoConselheiro->saveCustom($arrPost);

                # Salvando endereco.
                $mprEnderecoNacional = new Agente_Model_EnderecoNacionalMapper();
                $mprEnderecoNacional->saveCustom($arrPost);

                # Salvando telefone.
                $mprTelefones = new Agente_Model_TelefonesMapper();
                $mprTelefones->saveCustom($arrPost);

                # Salvando email.
                $mprInternet = new Agente_Model_InternetMapper();
                $mprNomes->beginTransaction();
                $mprInternet->saveCustom($arrPost);
                $mprNomes->commit();
            } catch (Exception $e) {
                $mprNomes->rollBack();
                parent::message("Erro ao salvar: " . $e->getMessage(), "/agente/manteragentes/agentes?acao=cc", "ERROR");
            }
            parent::message("Cadastro realizado com sucesso!", "/agente/manteragentes/agentes?acao=cc", "CONFIRM");
        }
        parent::message("Erro ao salvar: N&atilde;o existe dados para salvar!", "/agente/manteragentes/agentes?acao=cc", "ERROR");
    }


    /**
     * Metodo para gravacao de todos os dados do dirigente
     * @access public
     * @param void
     * @return void
     */
    public function gravardirigentecompletoAction()
    {
        // caso o formulario seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post = Zend_Registry::get('post');
            $idAgente = $post->idAgente; // id do dirigente
            $idAgenteGeral = $post->idAgenteGeral; // usuario associado ao dirigente
            $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->cpf)); // retira as mascaras
            $TipoNome = 18; // pessoa fisica
            $Usuario = $this->getIdUsuario; // id do usuario logado


            // ========== INICIO SALVAR NOME ==========
            $nome = $post->nome;

            try {
                // busca o nome do agente
                $busca = NomesDAO::buscarNome($idAgente);

                if (!$busca) { // faz a insercao do nome
                    $i = NomesDAO::gravarNome($idAgente, $TipoNome, $nome, 0, $Usuario);
                } else { // faz a alteracao do nome
                    $i = NomesDAO::atualizaNome($idAgente, $TipoNome, $nome, 0, $Usuario);
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message = "Erro ao salvar o nome: " . $e->getMessage();
            }
            // ========== FIM SALVAR NOME ==========


            // ========== INICIO SALVAR ENDERECO ==========
            $TipoEndereco = $post->tipoEndereco;
            $TipoLogradouro = $post->tipoLogradouro;
            $Logradouro = $post->logradouro;
            $Numero = $post->numero;
            $Bairro = $post->bairro;
            $Complemento = $post->complemento;
            $Cidade = $post->cidade;
            $UF = $post->uf;
            $Cep = Mascara::delMaskCEP($post->cep);
            $Divulgar = $post->divulgarEndereco;
            $Status = $post->enderecoCorrespondencia;

            $GravarEnderecoNacional = array( // insert
                'idAgente' => $idAgente,
                'TipoEndereco' => $TipoEndereco,
                'TipoLogradouro' => $TipoLogradouro,
                'Logradouro' => $Logradouro,
                'Numero' => $Numero,
                'Bairro' => $Bairro,
                'Complemento' => $Complemento,
                'Cidade' => $Cidade,
                'UF' => $UF,
                'Cep' => $Cep,
                'Status' => $Status,
                'Divulgar' => $Divulgar,
                'Usuario' => $Usuario);

            $AtualizarEnderecoNacional = array( // update
                'TipoEndereco' => $TipoEndereco,
                'TipoLogradouro' => $TipoLogradouro,
                'Logradouro' => $Logradouro,
                'Numero' => $Numero,
                'Bairro' => $Bairro,
                'Complemento' => $Complemento,
                'Cidade' => $Cidade,
                'UF' => $UF,
                'Cep' => $Cep,
                'Status' => $Status,
                'Divulgar' => $Divulgar,
                'Usuario' => $Usuario);

            try {
                // busca o endereco do agente
                $busca = Agente_Model_EnderecoNacionalDAO::buscarEnderecoNacional($idAgente);

                if (!$busca) { // faz a insercao do endereco
                    $i = Agente_Model_EnderecoNacionalDAO::gravarEnderecoNacional($GravarEnderecoNacional);
                } else { // faz a alteracao do endereco
                    $i = Agente_Model_EnderecoNacionalDAO::atualizaEnderecoNacional($idAgente, $AtualizarEnderecoNacional);
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message = "Erro ao salvar o endereço: " . $e->getMessage();
            }
            // ========== FIM SALVAR ENDERECO ==========


            // ========== INICIO SALVAR VISAO ==========
            $Visao = $post->visao;

            $GravarVisao = array( // insert
                'idAgente' => $idAgente,
                'Visao' => $Visao,
                'Usuario' => $Usuario,
                'stAtivo' => 'A');

            try {
                // busca as visoes do agente
                $visaoTable = new Agente_Model_DbTable_Visao();
                $busca = $visaoTable->buscarVisao($idAgente, $Visao);

                if (!$busca) { // faz a insercao da visao
                    $i = $visaoTable->cadastrarVisao($GravarVisao);
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message = "Erro ao salvar a visão: " . $e->getMessage();
            }
            // ========== FIM SALVAR VISAO ==========


            // ========== INICIO TELEFONES ==========
            // array com os telefones
            $tipoFones = $post->tipoFones;
            $ufFones = $post->ufFones;
            $dddFones = $post->dddFones;
            $Fones = $post->Fones;
            $divulgarFones = $post->divulgarFones;

            try {
                // exclui todos os telefones
                $delete = Agente_Model_Telefone::excluirTodos($idAgente);

                // cadastra todos os telefones
                for ($i = 0; $i < sizeof($Fones); $i++) {
                    $arrayTelefones = array(
                        'idAgente' => $idAgente,
                        'TipoTelefone' => $tipoFones[$i],
                        'UF' => $ufFones[$i],
                        'DDD' => $dddFones[$i],
                        'Numero' => $Fones[$i],
                        'Divulgar' => $divulgarFones[$i],
                        'Usuario' => $Usuario);

                    $insere = Agente_Model_Telefone::cadastrar($arrayTelefones);
                } // fecha for
            } // fecha try
            catch (Exception $e) {
                $this->view->message = "Erro ao salvar o componente: " . $e->getMessage();
            }
            // ========== FIM TELEFONES ==========


            // ========== INICIO E-MAILS ==========
            // array com os e-mails
            $tipoEmails = $post->tipoEmails;
            $Emails = $post->Emails;
            $divulgarEmails = $post->divulgarEmails;
            $enviarEmails = $post->enviarEmails;

            try {
                // exclui todos os e-mails
                $delete = Email::excluirTodos($idAgente);

                // cadastra todos os e-mails
                for ($i = 0; $i < sizeof($Emails); $i++) {
                    $arrayEmail = array(
                        'idAgente' => $idAgente,
                        'TipoInternet' => $tipoEmails[$i],
                        'Descricao' => $Emails[$i],
                        'Status' => $enviarEmails[$i],
                        'Divulgar' => $divulgarEmails[$i],
                        'Usuario' => $Usuario);

                    $insere = Email::cadastrar($arrayEmail);
                } // fecha for
            } // fecha try
            catch (Exception $e) {
                $this->view->message = "Erro ao salvar o componente: " . $e->getMessage();
            }
            // ========== FIM E-MAILS ==========


            // ========== INICIO DIRIGENTES ==========
            // busca os dados do associado ao dirigente (idVinculoPrincipal)
            //$buscarAgente = Agente_Model_ManterAgentesDAO::buscarAgentes(null, null, $idAgenteGeral);

            // busca o dirigente vinculado ao cnpj/cpf
            //$dadosDirigente = Agente_Model_ManterAgentesDAO::buscarVinculados($buscarAgente[0]->CNPJCPF, null, $idAgente, $idAgenteGeral, $idAgenteGeral);
            $dadosDirigente = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, $idAgente, $idAgenteGeral, $idAgenteGeral);

            // caso o agente nao esteja vinculado, realizara a vinculacao
            if (!$dadosDirigente) {
                // associa o dirigente ao cnpj/cpf
                $dadosVinculacao = array(
                    'idAgente' => $idAgente,
                    'idVinculado' => $idAgenteGeral,
                    'idVinculoPrincipal' => $idAgenteGeral,
                    'Usuario' => $Usuario);
                $vincular = Agente_Model_ManterAgentesDAO::cadastrarVinculados($dadosVinculacao);
            }
            // ========== FIM DIRIGENTES ==========
        }

        parent::message("Cadastro realizado com sucesso!", "manteragentes/dirigentes?acao=cc&idAgenteGeral=" . $idAgenteGeral, "CONFIRM");
    }
}
