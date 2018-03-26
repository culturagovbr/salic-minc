<?php
/**
 * Controller Readequacao (Proponente, Respons�vel / Procurador)
 * Considera��es importantes:
 *  1. O Projeto tem que estar na Fase de Execu��o (2)
 *  2. O Projeto tem que estar Aprovado (SAC.dbo.Aprovacao):
 * 		2.1. Os dados da Portaria tem que estar preenchidos
 * 		2.2. O Tipo de Aprova��o deve ser 1
 *  3. Os Projetos devem estar vinculados ao Proponente/Respons�vel/Procurador
 * @author emanuel.sampaio <emanuelonline@gmail.com>
 * @since 30/03/2012
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://salic.cultura.gov.br
 * @copyright � 2012 - Minist�rio da Cultura - Todos os direitos reservados.
 */

class ReadequacaoController extends MinC_Controller_Action_Abstract
{
    /**
     * Vari�veis globais utilizada em toda a controller
     * @access private
     */
    private $_idResponsavel     = 0; // id do respons�vel logado, hoje obrigat�rio para acessar todas as p�ginas
    private $_idProponente      = 0; // id do proponente logado, opcional
    private $_idProcurador      = 0; // id do procurador logado, opcional
    private $_idAgente          = 0; // id do agente logado, opcional
    private $_idUsuario         = 0; // id do usu�rio interno do minc que est� logado no sistema, opcional
    private $_idAgenteProjeto   = 0; // id do proponente do projeto em quest�o
    private $_cpfLogado         = 0; // cpf do usu�rio logado, obrigat�rio para todas as p�ginas
    private $_idPronac          = 0; // id do pronac/projeto, obrigat�rio para todas as p�ginas
    private $_idPreProjeto      = 0; // c�digo pr�-projeto do projeto
    private $_idPedidoAlteracao = 0; // id do pedido de readequa��o do projeto, obrigat�rio caso exista solicita��o de readequa��o
    private $_vlTotalAprovado   = 0; // valor aprovado do projeto (SAC.dbo.Aprovacao)
    private $_vlTotalSolicitado = 0; // valor que o projeto ter� ap�s a solicita��o de readequa��o ser aprovada
    private $_tipoReadequacao   = ''; // tipo de readequa��o: redu��o, complementa��o ou remanejamento
    private $_stPedidoAlteracao = ''; // status do pedido de readequa��o atual (A - Ativo, I - Inativo, T - Tempor�rio)
    private $_urlMod            = ''; // monta a url padr�o do m�dulo (index)
    private $_urlAtual          = ''; // monta a url ativa (action aberta)



    /**
     * Vari�veis com os objetos de banco utilizados na controle
     * @access private
     * @var object (tabelas utilizadas)
     */
    private $Agentes;                                 // base AGENTES
    private $Municipios;                              // base AGENTES
    private $Nomes;                                   // base AGENTES
    private $Pais;                                    // base AGENTES
    private $tbProcuracao;                            // base AGENTES
    private $tbVinculo;                               // base AGENTES
    private $Uf;                                      // base AGENTES
    private $Visao;                                   // base AGENTES
    private $tbAlteracaoNomeProponente;               // base BDCORPORATIVO
    private $tbArquivo;                               // base BDCOORPORATIVO
    private $tbArquivoImagem;                         // base BDCOORPORATIVO
    private $tbPedidoAlteracaoProjeto;                // base BDCORPORATIVO
    private $tbPedidoAlteracaoXTipoAlteracao;         // base BDCORPORATIVO
    private $tbPedidoAltProjetoXArquivo;              // base BDCORPORATIVO
    private $tbProrrogacaoPrazo;                      // base BDCORPORATIVO
    private $Aprovacao;                               // base SAC
    private $Area;                                    // base SAC
    private $PreProjeto;                              // base SAC
    private $Produto;                                 // base SAC
    private $Projetos;                                // base SAC
    private $Segmento;                                // base SAC
    private $tbAbrangencia;                           // base SAC
    private $tbPlanilhaAprovacao;                     // base SAC
    private $tbPlanilhaEtapa;                         // base SAC
    private $tbPlanilhaUnidade;                       // base SAC
    private $tbPlanoDistribuicao;                     // base SAC
    private $tbProposta;                              // base SAC
    private $Verificacao;                             // base SAC



    /**
     * M�todo para verificar se o usu�rio logado tem permiss�o para acessar o projeto
     * OBS: SERVE APENAS PARA RESPONS�VEL E AGENTE (PROPONENTE E PROCURADOR)
     * @access private
     * @param integer $idPronac
     * @param integer $idResponsavel
     * @param integer $idAgente (Proponente ou Procurador)
     * @return void
     */
    private function verificarPermissaoProjetoProponente($idPronac = 0, $idResponsavel = 0, $idAgente = 0)
    {
        // objetos utilizados
        $this->Projetos  = new Projetos();
        $this->Aprovacao = new Aprovacao();

        $msgERRO = 'Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!';

        try {
            // o id do pronac � obrigat�rio
            if (isset($idPronac) && !empty($idPronac)) :

                /**
                 * Verifica:
                 * 	-> se o PRONAC existe
                 *  -> se o Projeto est� em Fase de Execu��o (2)
                 *  -> se o Tipo de Aprova��o � igual a 1
                 *  -> se existem informa��es da Portaria
                 */
                $whereVerificacao = array();
            $whereVerificacao['IdPRONAC = ?'] = $idPronac;
            $verificarProjeto = $this->Projetos->buscar($whereVerificacao);
            if (count($verificarProjeto) <= 0) :
                    throw new Exception('Nenhum projeto encontrado com o n&uacute;mero de Pronac informado!'); elseif (empty($verificarProjeto[0]->idProjeto)) :
                    parent::message('Somente ser&aacute; permitido solicitar altera&ccedil;&atilde;o de Projetos por meio do sistema para aqueles cadastrados a partir de Janeiro de 2009. Os outros casos dever&atilde;o ser solicitados por meio de of&iacute;cio.', 'consultardadosprojeto?idPronac=' . $idPronac, 'ERROR');
            endif;

            $whereVerificacao['TipoAprovacao = ?']             = 1;
            $whereVerificacao['PortariaAprovacao IS NOT NULL'] = '';
            if (count($this->Aprovacao->buscar($whereVerificacao)) <= 0) :
                    throw new Exception('O Projeto n�o est� na Fase de execu��o!');
            endif;


            // ========== valida��o caso o usu�rio logado seja somente respons�vel ==========
            if ($idResponsavel > 0 && $idAgente == 0) :

                    // verifica se o projeto est� com algum proponente que esteja vinculado ao respons�vel
                    $buscarProjetosProponente = count($this->tbVinculo->buscarProponentesProjetosResponsavel($idResponsavel, $idPronac));

            // verifica se o respons�vel tem procura��o
            $this->tbProcuracao = new Procuracao();
            $buscarProcuracao = $this->tbProcuracao->buscarProcuracaoProjeto(array('v.idUsuarioResponsavel = ?' => $idResponsavel, 'v.siVinculo = ?' => 2, 'vprp.idPreProjeto = ?' => $verificarProjeto[0]->idProjeto));

            if ($buscarProjetosProponente <= 0 || count($buscarProcuracao) <= 0) :
                        throw new Exception($msgERRO);
            endif;


            // ========== valida��o caso o usu�rio logado seja somente proponente ==========
            elseif ($idAgente > 0 && $idResponsavel == 0) :

                    // verifica se o projeto est� vinculado ao proponente
                    $buscarCpfAgente = $this->Agentes->buscar(array('idAgente = ?' => $idAgente));
            $cpfAgente       = (count($buscarCpfAgente) > 0) ? $buscarCpfAgente[0]->CNPJCPF : 0;
            $buscarProjeto   = count($this->Projetos->buscar(array('CgcCpf = ?' => $cpfAgente, 'IdPRONAC = ?' => $idPronac)));

            if ($buscarProjeto <= 0) :
                        throw new Exception($msgERRO);
            endif;


            // ========== valida��o caso o usu�rio logado seja respons�vel e proponente ao mesmo tempo ==========
            elseif ($idAgente > 0 && $idResponsavel > 0) :

                    // verifica se o projeto est� vinculado ao proponente
                    $buscarCpfAgente = $this->Agentes->buscar(array('idAgente = ?' => $idAgente));
            $cpfAgente       = (count($buscarCpfAgente) > 0) ? $buscarCpfAgente[0]->CNPJCPF : 0;
            $buscarProjeto   = count($this->Projetos->buscar(array('CgcCpf = ?' => $cpfAgente, 'IdPRONAC = ?' => $idPronac)));

            // verifica se o projeto est� com algum proponente que esteja vinculado ao respons�vel
            $buscarProjetosProponente = count($this->tbVinculo->buscarProponentesProjetosResponsavel($idResponsavel, $idPronac));

            // verifica se o respons�vel tem procura��o
            $this->tbProcuracao = new Procuracao();
            $buscarProcuracao = $this->tbProcuracao->buscarProcuracaoProjeto(array('v.idUsuarioResponsavel = ?' => $idResponsavel, 'v.siVinculo = ?' => 2, 'vprp.idPreProjeto = ?' => $verificarProjeto[0]->idProjeto));

            if ($buscarProjeto <= 0 && $buscarProjetosProponente <= 0 && count($buscarProcuracao) <= 0) :
                        throw new Exception($msgERRO);
            endif; else :
                    throw new Exception($msgERRO);
            endif; else :
                throw new Exception($msgERRO);
            endif;
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), 'principalproponente', 'ALERT');
        }
    } // fecha m�todo verificarPermissaoProjetoProponente()



    /**
     * M�todo que retorna o tipo de readequa��o.
     * O tipo � definido com base no valor aprovado do projeto e no valor que est� sendo solicitado pelo proponente na readequa��o:
     * - Caso o Valor Solicitado seja menor que o Valor Aprovado, o tipo ser�: redu��o;
     * - Caso o Valor Solicitado seja maior que o Valor Aprovado, o tipo ser�: complementa��o;
     * - Caso o Valor Solicitado seja igual ao Valor Aprovado, o tipo ser�: remanejamento.
     * @access private
     * @param void
     * @return string (retorna a descri��o do tipo de readequa��o)
     */
    private function verificarTipoReadequacao()
    {
        // objetos utilizados
        $this->Aprovacao           = new Aprovacao();
        $this->Projetos            = new Projetos();
        $this->tbPlanilhaAprovacao = new PlanilhaAprovacao();

        $projetoDados = $this->Projetos->buscarAnoProjetoSequencial($this->_idPronac);
        $fnTotalAprovadoProjeto = $this->Aprovacao->fnTotalAprovadoProjeto($projetoDados->AnoProjeto, $projetoDados->Sequencial);

        // busca o valor aprovado do projeto
        $whereAP = array();
        $whereAP['pr.IdPRONAC = ?']                  = $this->_idPronac;
        $whereAP['ap.TipoAprovacao = ?']             = 1;
        $whereAP['ap.PortariaAprovacao IS NOT NULL'] = '';
        $buscarVlAprovado = $this->Projetos->buscarProjetosAprovados($whereAP)->current();
        $this->_vlTotalAprovado = $buscarVlAprovado['AprovadoReal'] ? $buscarVlAprovado['AprovadoReal'] : 0;

        // busca o valor solicitado do projeto
        $whereSR = array();
        $whereSR['idPronac = ?']          = $this->_idPronac;
        $whereSR['idPedidoAlteracao = ?'] = $this->_idPedidoAlteracao;
        $whereSR['tpPlanilha = ?']        = 'SR';
        $whereSR['stAtivo = ?']           = 'N';
        $whereSR['tpAcao <> ?']           = 'E';
        $buscarVlSolicitado = $this->tbPlanilhaAprovacao->somarItensPlanilhaAprovacao($whereSR);
        $this->_vlTotalSolicitado = $buscarVlSolicitado['soma'] ? $buscarVlSolicitado['soma'] : 0;

        if ($this->_vlTotalAprovado != $fnTotalAprovadoProjeto->totalAprovado) {
            $tpReadequacaoNull = '';
            $this->_tipoReadequacao = $tpReadequacaoNull;
            return $tpReadequacaoNull;
        }
        $tpReadequacao[0] = 'Seu projeto sofreu redu&ccedil;&atilde;o.<br /><br />';
        $tpReadequacao[1] = 'Seu projeto sofreu complementa&ccedil;&atilde;o.<br /><br />';
        $tpReadequacao[2] = 'Seu projeto sofreu remanejamento.<br /><br />';

        if ($this->_vlTotalAprovado > $this->_vlTotalSolicitado) :
            $this->_tipoReadequacao = $tpReadequacao[0];
        return $tpReadequacao[0]; elseif ($this->_vlTotalAprovado < $this->_vlTotalSolicitado) :
            $this->_tipoReadequacao = $tpReadequacao[1];
        return $tpReadequacao[1]; else :
            $this->_tipoReadequacao = $tpReadequacao[2];
        return $tpReadequacao[2];
        endif;
    } // fecha m�todo verificarTipoReadequacao()



    /**
     * M�todo para salvar o pedido de solicita��o de readequa��o
     * @access private
     * @param $stPedidoAlteracao (andamento do pedido)
     * @param $siVerificacao (status do pedido)
     * @return void
     */
    private function salvarPedido($stPedidoAlteracao = 'T', $siVerificacao = 0)
    {
        $this->tbPedidoAlteracaoProjeto = new tbPedidoAlteracaoProjeto(); // objeto utilizado

        // filtro para altera��o
        $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

        // salva os dados do pedido
        if (empty($this->_idPedidoAlteracao)) : // CADASTRA
            $dadosPedido = array(
                'idPedidoAlteracao'  => null
                ,'IdPRONAC'          => $this->_idPronac
                ,'idSolicitante'     => $this->_idAgenteProjeto
                ,'dtSolicitacao'     => new Zend_Db_Expr('GETDATE()')
                ,'stPedidoAlteracao' => $stPedidoAlteracao
                ,'siVerificacao'     => $siVerificacao
            );
        $this->_idPedidoAlteracao = $this->tbPedidoAlteracaoProjeto->inserir($dadosPedido); else : // ALTERA
            $dadosPedido     = array(
                'stPedidoAlteracao' => $stPedidoAlteracao
                ,'idSolicitante'    => $this->_idAgenteProjeto
                ,'dtSolicitacao'    => new Zend_Db_Expr('GETDATE()')
            );
        $this->tbPedidoAlteracaoProjeto->alterar($dadosPedido, $whereItemPedido);
        endif;
    } // fecha m�todo salvarPedido()



    /**
     * M�todo para cadastrar os arquivos da solicita��o de readequa��o
     * @access private
     * @param array $_FILES
     * @param integer $idPedidoAlteracao
     * @param integer $status
     * @return void
     */
    private function cadastrarArquivos($_FILES, $idPedidoAlteracao, $tpAlteracaoProjeto)
    {
        // objetos utilizados
        $this->tbArquivo                  = new tbArquivo();
        $this->tbArquivoImagem            = new tbArquivoImagem();
        $this->tbPedidoAltProjetoXArquivo = new tbPedidoAltProjetoXArquivo();

        $valor = $_FILES['arquivo']['name'][0];

        if (!empty($valor)) :

            for ($i = 0; $i < count($_FILES["arquivo"]["name"]); $i++) :
                // pega as informa��es do arquivo
                $arquivoNome     = $_FILES['arquivo']['name'][$i]; // nome
                $arquivoTemp     = $_FILES['arquivo']['tmp_name'][$i]; // nome tempor�rio
                $arquivoTipo     = $_FILES['arquivo']['type'][$i]; // tipo
                $arquivoTamanho  = $_FILES['arquivo']['size'][$i]; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) :
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash

                    // cadastra dados do arquivo
        $dadosArquivo = array(
                        'idArquivo'          => null
                        ,'nmArquivo'         => $arquivoNome
                        ,'sgExtensao'        => $arquivoExtensao
                        ,'dsTipoPadronizado' => $arquivoTipo
                        ,'nrTamanho'         => $arquivoTamanho
                        ,'dtEnvio'           => new Zend_Db_Expr('GETDATE()')
                        ,'dsHash'            => $arquivoHash
                        ,'stAtivo'           => 'A');
        $idUltimoArquivo = $this->tbArquivo->inserir($dadosArquivo); // pega o id do �ltimo arquivo cadastrado

        // cadastra o bin�rio do arquivo
        $dadosBinario = array(
                        'idArquivo'  => $idUltimoArquivo
                        ,'biArquivo' => new Zend_Db_Expr("CONVERT(varbinary(MAX), {$arquivoBinario})")
                    );
        $this->tbArquivoImagem->inserir($dadosBinario);

        // cadastra o pedido de altera��o
        $dadosPedido = array(
                        'idArquivo'           => $idUltimoArquivo
                        ,'idPedidoAlteracao'  => $idPedidoAlteracao
                        ,'tpAlteracaoProjeto' => $tpAlteracaoProjeto
                    );
        $this->tbPedidoAltProjetoXArquivo->inserir($dadosPedido);
        endif;
        endfor;
        endif;
    } // fecha m�todo cadastrarArquivos()



    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        ini_set('memory_limit', '-1');

        // objetos pr�-carregados inicialmente
        $this->Agentes                  = new Agente_Model_DbTable_Agentes();
        $this->Visao                    = new Visao();
        $this->Projetos                 = new Projetos();
        $this->tbVinculo                = new Agente_Model_DbTable_TbVinculo();
        $this->Aprovacao                = new Aprovacao();
        $this->tbPlanilhaAprovacao      = new PlanilhaAprovacao();
        $this->tbPedidoAlteracaoProjeto = new tbPedidoAlteracaoProjeto();

        // monta a url principal do m�dulo e a atual
        $this->_urlMod    = $this->_request->getControllerName() . '/index/idpronac/' .$this->_request->getParam('idpronac');
        $this->_urlAtual  = $this->_request->getControllerName() . '/' . $this->_request->getActionName() . '/idpronac/' . $this->_request->getParam('idpronac');

        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        //SE CAIU A SECAO REDIRECIONA
        if (!$auth->hasIdentity()) {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl();
            JS::redirecionarURL($url);
        }
        $this->verificarPermissaoAcesso(false, true, false);

        //		$auth = Zend_Auth::getInstance(); // pega a autentica��o
//
        //		// atribui o CPF do usu�rio logado
        //		$this->_cpfLogado = isset($auth->getIdentity()->usu_identificacao) ? $auth->getIdentity()->usu_identificacao : 0;
        //		$this->_cpfLogado = isset($auth->getIdentity()->Cpf)               ? $auth->getIdentity()->Cpf               : $this->_cpfLogado;
//
        //		// verifica se o usu�rio logado � Agente
        //		$buscarAgente    = $this->Agentes->buscar(array('CNPJCPF = ?' => $this->_cpfLogado));
        //		$this->_idAgente = count($buscarAgente) > 0 ? $buscarAgente[0]->idAgente : 0;
//
        //		// verifica se o Agente logado possui vis�o de Proponente e Procurador
        //		$buscarVisaoProponente = count($this->Visao->buscar(array('idAgente = ?' => $this->_idAgente, 'Visao = ?' => '144')));
        //		$buscarVisaoProcurador = count($this->Visao->buscar(array('idAgente = ?' => $this->_idAgente, 'Visao = ?' => '247')));
        //		$this->_idProponente   = $buscarVisaoProponente > 0 ? $this->_idAgente : 0;
        //		$this->_idProcurador   = $buscarVisaoProcurador > 0 ? $this->_idAgente : 0;
//
        //		// verifica se o usu�rio logado � Interno (base TABELAS) ou Externo (base CONTROLEDEACESSO)
        //		$this->_idResponsavel = isset($auth->getIdentity()->IdUsuario)  ? $auth->getIdentity()->IdUsuario  : 0;
        //		$this->_idUsuario     = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : 0;
//
        //		// o usu�rio tem que estar cadastrado no banco TABELAS ou CONTROLEDEACESSO para ter acesso ao sistema
        //		if (empty($this->_idResponsavel) && empty($this->_idUsuario)) :
        //			parent::message('Voc� n�o tem permiss�o para acessar essa �rea do sistema!', 'index', 'ALERT');
        //		endif;

        parent::perfil(4); // perfil

        parent::init();
    } // fecha m�todo init()



    /**
     * Reescreve o m�todo preDispatch()
     * 1. Pega o id do Pronac via get uma �nica vez
     * 2. Valida se o usu�rio possui permiss�o para acessar o projeto
     * 3. Mostra/Oculta o bot�o 'Enviar Solicita��o'
     * 4. Pega o tipo de readequa��o: redu��o, complementa��o ou remanejamento
     * @access public
     * @param void
     * @return void
     */
    public function preDispatch()
    {
        // recebe o pronac
        $this->_idPronac = $this->_request->getParam('idpronac');

        if (strlen($this->_idPronac) > 7) {
            $this->_idPronac = Seguranca::dencrypt($this->_idPronac);
        }

        // busca os dados do projeto
        $whereProjeto        = array('p.IdPRONAC = ?' => $this->_idPronac);
        $buscarDadosProjeto  = $this->Projetos->buscarProjetoXProponente($whereProjeto)->current();
        $dadosProjeto        = count($buscarDadosProjeto) > 0 ? $buscarDadosProjeto        : '';
        $this->_idPreProjeto = isset($dadosProjeto['idProjeto']) ? $dadosProjeto['idProjeto'] : 0;

        // atribui o id do proponente do projeto
        $this->_idAgenteProjeto = isset($dadosProjeto['idAgente']) ? $dadosProjeto['idAgente'] : 0;

        // busca os id do �ltimo pedido de readequa��o n�o finalizado
        $wherePedido                    = array('IdPRONAC = ?' => $this->_idPronac, 'siVerificacao IN (?)' => array(0, 1));
        $orderPedido                    = array('idPedidoAlteracao DESC');
        $buscarPedidoAlteracao          = $this->tbPedidoAlteracaoProjeto->buscar($wherePedido, $orderPedido)->current();
        $this->_idPedidoAlteracao       = count($buscarPedidoAlteracao) > 0 ? $buscarPedidoAlteracao['idPedidoAlteracao'] : 0;

        // busca a situa��o do pedido de altera��o do projeto
        $this->_stPedidoAlteracao = count($buscarPedidoAlteracao) > 0 ? $buscarPedidoAlteracao['stPedidoAlteracao'] : $this->_stPedidoAlteracao;

        // manda os dados para a vis�o
        $this->view->projeto           = $dadosProjeto;
        $this->view->idPronac          = $this->_idPronac;
        $this->view->idPedidoAlteracao = $this->_idPedidoAlteracao;
        $this->view->stPedidoAlteracao = $this->_stPedidoAlteracao;
        $this->view->tipoReadequacao   = $this->verificarTipoReadequacao();
        $this->view->vlTotalAprovado   = $this->_vlTotalAprovado;
        $this->view->vlTotalSolicitado = $this->_vlTotalSolicitado;
        $this->view->pagina            = $this->_request->getActionName();
    } // fecha m�todo preDispatch()



    /**
     * Redireciona para o fluxo inicial do sistema
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        $idPronac = $this->_request->getParam('idpronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->_forward("proponente"); // despacha para o fluxo inicial
    }


    /**
     * M�todo respons�vel pela exclus�o dos arquivos da solicita��o de readequa��o
     * @access public
     * @param void
     * @return void
     */
    public function excluirArquivoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // caso tenha dados de formul�rio via post
        if ($this->getRequest()->isPost()) :

            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
        $idPedidoAlteracao = (int) Seguranca::tratarVarAjaxUFT8($post->idPedidoAlteracao);
        $idArquivo         = (int) Seguranca::tratarVarAjaxUFT8($post->idArquivo);
        $nmArquivo         = Seguranca::tratarVarAjaxUFT8($post->nmArquivo);

        // objetos utilizados
        $this->tbPedidoAltProjetoXArquivo = new tbPedidoAltProjetoXArquivo();
        $this->tbArquivo                  = new tbArquivo();
        $this->tbArquivoImagem            = new tbArquivoImagem();

        try {
            if (isset($idPedidoAlteracao) && isset($idArquivo) && !empty($idPedidoAlteracao) && !empty($idArquivo)) :
                    $wherePedido = array(
                        'idPedidoAlteracao = ?' => $idPedidoAlteracao
                        ,'idArquivo = ?'        => $idArquivo
                    );
            $whereArquivo = array('idArquivo = ?' => $idArquivo);

            $this->tbPedidoAltProjetoXArquivo->delete($wherePedido);
            $this->tbArquivoImagem->delete($whereArquivo);
            $this->tbArquivo->delete($whereArquivo);

            $this->view->nmArquivo = $nmArquivo; // manda o nome do arquivo para vis�o

            else :
                    throw new Exception('Erro ao excluir arquivo!');
            endif;
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $this->_urlMod, 'ERROR');
        }
        endif;
    } // fecha m�todo excluirArquivoAction()



    /**
     * M�todo respons�vel pelo envio da solicita��o de readequa��o
     * @access public
     * @param void
     * @return void
     */
    public function enviarSolicitacaoAction()
    { //jass
        // caso tenha dados de formul�rio via post
        if ($this->getRequest()->isPost()) {

                // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $stPedidoAlteracao = $post->finalizarPedido;

            if ($stPedidoAlteracao == 'I') { //finalizar a solicita��o = sim
                $tbPedidoAlteracaoProjeto = new tbPedidoAlteracaoProjeto();
                $dadosProdutos = $tbPedidoAlteracaoProjeto->verificarProdutoSemItem($this->_idPedidoAlteracao);
                if (count($dadosProdutos)>0) {
                    parent::message('N�o foi poss�vel concluir a a��o porque n�o h� planilha or�ament�ria correspondente ao(s) novo(s) produto(s) cadastrado(s)!', $this->_urlMod, 'ALERT');
                }
            }

            try {
                // faz a altera��o na situa��o do pedido
                $dados = array(
                        'idSolicitante'      => $this->_idAgenteProjeto
                        ,'dtSolicitacao'     => new Zend_Db_Expr('GETDATE()')
                        ,'stPedidoAlteracao' => $stPedidoAlteracao
                    );
                $where = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

                // atualiza a situa��o do pedido de readequa��o
                if ($this->tbPedidoAlteracaoProjeto->alterar($dados, $where)) {
                    $this->_stPedidoAlteracao = $stPedidoAlteracao;
                    if ($stPedidoAlteracao == 'I') {
                        parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', 'consultardadosprojeto/?idPronac='.Seguranca::encrypt($this->_idPronac), 'CONFIRM');
                    } else {
                        parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', $this->_urlMod, 'CONFIRM');
                    }
                } else {
                    throw new Exception('Erro ao enviar solicita&ccedil;&atilde;o');
                }
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), $this->_urlMod, 'ERROR');
            }
        }
    } // fecha m�todo enviarSolicitacaoAction()



    /**
     * M�todo para solicitar altera��o de dados do Proponente do Projeto
     * @access public
     * @param void
     * @return void
     */
    public function proponenteAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            // objetos utilizados
            $this->tbAlteracaoNomeProponente       = new tbAlteracaoNomeProponente();
            $this->tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $this->tbPedidoAltProjetoXArquivo      = new tbPedidoAltProjetoXArquivo();

            // busca os dados aprovados
            $buscarAP = $this->Projetos->buscarProjetoXProponente(array('p.IdPRONAC = ?' => $this->_idPronac))->current();
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o
            $buscarSR = $this->tbAlteracaoNomeProponente->buscarPedido(array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao))->current();
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o

            // busca o pedido (justificativa) da solicita��o de readequa��o
            $whereTipoReadequacao = array(
                        'p.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                        ,'x.tpAlteracaoProjeto IN (?)' => array(1, 2) // proponente
                );
            $buscarPedido = $this->tbPedidoAlteracaoXTipoAlteracao->buscarPedido($whereTipoReadequacao)->current();
            $this->view->pedido = $buscarPedido; // manda as informa��es para a vis�o

            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                        'x.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                        ,'x.tpAlteracaoProjeto IN (?)' => array(1, 2) // proponente
                );
            $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
            $this->view->arquivos = $buscarArquivo; // manda as informa��es para a vis�o
        } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
            }


        // ========== IN�CIO: FORMUL�RIO ENVIADO VIA POST ==========
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $cpfcnpj            = Mascara::delMaskCPFCNPJ($post->cpfcnpj);
            $nome               = $post->nome;
            $justificativa      = $post->justificativa;
            $stPedidoAlteracao  = $post->stPedidoAlteracao;
            $siVerificacao      = $post->siVerificacao;
            $tpAlteracaoProjeto = $post->tpAlteracaoProjeto;

            try {
                // valida��o dos dados
                if (empty($cpfcnpj) || empty($nome) || empty($justificativa)) :
                        throw new Exception('Dados obrigat&oacute;rios n&atilde;o informados!');
                endif;

                // atualiza o status do pedido de readequa��o
                $this->_stPedidoAlteracao = $stPedidoAlteracao;

                // salva os dados do pedido
                $this->salvarPedido($stPedidoAlteracao, $siVerificacao);

                // filtro para altera��o
                $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

                // salva os dados do item do pedido
                    if (count($this->tbAlteracaoNomeProponente->buscar($whereItemPedido)) <= 0) : // CADASTRA
                        $dadosItemPedido = array(
                                'idPedidoAlteracao' => $this->_idPedidoAlteracao
                                ,'nrCNPJCPF'        => $cpfcnpj
                                ,'nmProponente'     => $nome
                        );
                $this->tbAlteracaoNomeProponente->inserir($dadosItemPedido); else : // ALTERA
                        $dadosItemPedido = array(
                                'nrCNPJCPF'     => $cpfcnpj
                                ,'nmProponente' => $nome
                        );
                $this->tbAlteracaoNomeProponente->alterar($dadosItemPedido, $whereItemPedido);
                endif;

                // salva os dados da justificativa
                    if ($buscarAP['CNPJCPF'] != $cpfcnpj) : // justificativa de cpf do proponente
                        $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 2); // filtro para altera��o
                        if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) : // CADASTRA
                            $dadosJustificativa = array(
                                    'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                                    ,'dsJustificativa'    => $justificativa
                                    ,'tpAlteracaoProjeto' => 2 // cpf do proponente
                                    ,'stVerificacao'      => 0
                            );
                $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa); else : // ALTERA
                            $dadosJustificativa = array('dsJustificativa' => $justificativa);
                $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                endif; else :
                        $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 1); // filtro para altera��o
                        if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) : // CADASTRA
                            $dadosJustificativa = array(
                                    'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                                    ,'dsJustificativa'    => $justificativa
                                    ,'tpAlteracaoProjeto' => 1 // nome do proponente
                                    ,'stVerificacao'      => 0
                            );
                $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa); else : // ALTERA
                            $dadosJustificativa = array('dsJustificativa' => $justificativa);
                $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                endif;
                endif;

                // cadastra os arquivos
                $this->cadastrarArquivos($_FILES, $this->_idPedidoAlteracao, $tpAlteracaoProjeto);

                parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', $this->_urlAtual, 'CONFIRM');
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if
            // ========== FIM: FORMUL�RIO ENVIADO VIA POST ==========
    } // fecha m�todo proponenteAction()



    /**
     * M�todo com a grid para solicitar cadastros e altera��es de novos Produtos (Plano de Distribui��o)
     * @access public
     * @param void
     * @return void
     */
    public function produtosAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            $this->tbPlanoDistribuicao = new tbPlanoDistribuicao();

            // busca os dados aprovados
            $orderProduto = array('p.stPrincipal DESC', 'd.Descricao');
            $buscarAP = $this->tbPlanoDistribuicao->buscarProdutosAprovados(array('p.idProjeto = ?' => $this->_idPreProjeto, 'p.stPlanoDistribuicaoProduto = ?' => 1), $orderProduto);
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o
            $buscarSR = $this->tbPlanoDistribuicao->buscarProdutosSolicitados(array('p.idPedidoAlteracao = ?' => $this->_idPedidoAlteracao, 'p.tpAcao <> ?' => 'E', 'tpPlanoDistribuicao = ?' => 'SR'), $orderProduto);
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o
        } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
            }
    } // fecha m�todo produtosAction()



    /**
     * M�todo para inclus�es de novos Produtos (Plano de Distribui��o)
     * @access public
     * @param void
     * @return void
     */
    public function cadastrarProdutosAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            // objetos utilizados
            $this->Produto                         = new Produto();
            $this->Area                            = new Area();
            $this->Verificacao                     = new Verificacao();
            $this->PreProjeto                      = new Proposta_Model_DbTable_PreProjeto();
            $this->tbPlanoDistribuicao             = new tbPlanoDistribuicao();
            $this->tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $this->tbPedidoAltProjetoXArquivo      = new tbPedidoAltProjetoXArquivo();

            // busca os dados das combos
            $this->view->produto     = $this->Produto->buscar(array(), array('Descricao ASC'));
            $this->view->area        = $this->Area->buscar(array('Codigo <> ?' => 7), array('Descricao ASC'));
            $this->view->posicaoLogo = $this->Verificacao->buscarTipos(array('t.idTipo = ?' => 3), array('v.Descricao ASC'));

            // pega o c�digo do produto (se for vazio, faz o cadastro, caso contr�rio, faz a altera��o ou exclus�o)
            $idProduto = $this->_request->getParam('idproduto');
            $this->view->idProduto = $idProduto;

            // busca os dados aprovados
            if (!empty($idProduto)) :
                    $orderProdutoAP = array('p.stPrincipal DESC', 'd.Descricao');
            $whereProdutoAP = array('p.idProjeto = ?' => $this->_idPreProjeto, 'p.stPlanoDistribuicaoProduto = ?' => 1, 'p.idProduto = ?' => $idProduto);
            $buscarAP = $this->tbPlanoDistribuicao->buscarProdutosAprovados($whereProdutoAP, $orderProdutoAP);
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o
            $whereProdutoSR = array('p.idPedidoAlteracao = ?' => $this->_idPedidoAlteracao, 'p.tpAcao <> ?' => 'E', 'p.idProduto = ?' => $idProduto, 'tpPlanoDistribuicao = ?' => 'SR');
            $buscarSR = $this->tbPlanoDistribuicao->buscarProdutosSolicitados($whereProdutoSR, $orderProdutoAP);
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o
            else :
                    $this->view->dadosAP = '';
            $this->view->dadosSR = '';
            endif;
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
        }

        // ========== INICIO: FORMULARIO ENVIADO VIA POST ==========
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formulario
            $post = Zend_Registry::get('post');
            $tpProduto          = $post->tpProduto;
            $idArea             = $post->area;
            $idSegmento         = $post->segmento;
            $idPosicaoLogo      = $post->posicaoLogo;
            $qtdPatrocinador    = $post->patrocinador;
            $qtdDivulgacao      = $post->divulgacao;
            $qtdBeneficiario    = $post->beneficiario;
            $qtdNormal          = $post->normal;
            $qtdPromocional     = $post->promocional;
            $vlNormal           = Mascara::delMaskMoeda($post->vlNormal);
            $vlPromocional      = Mascara::delMaskMoeda($post->vlPromocional);
            $justificativa      = $post->dsJustificativa;
            $stPedidoAlteracao  = $post->stPedidoAlteracao;
            $siVerificacao      = $post->siVerificacao;
            $tpAlteracaoProjeto = $post->tpAlteracaoProjeto;
            $tpAcao             = $post->tpAcao;

            try {
                // validacao dos dados
                if (empty($idArea) || empty($idPosicaoLogo) || empty($justificativa)) :
                        throw new Exception('Dados obrigat&oacute;rios n&atilde;o informados!');
                endif;

                // atualiza o status do pedido de readequacao
                $this->_stPedidoAlteracao = $stPedidoAlteracao;

                // salva os dados do pedido
                $this->salvarPedido($stPedidoAlteracao, $siVerificacao);

                // filtro para alteracao
                $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

                // faz a copia da tabela original para a solicitada caso nao exista na solicitada, e, exista algum registro na aprovada
                $orderProdutoAP = array('p.stPrincipal DESC', 'd.Descricao');
                $whereProdutoAP = array('p.idProjeto = ?' => $this->_idPreProjeto, 'p.stPlanoDistribuicaoProduto = ?' => 1);
                $buscarAP = $this->tbPlanoDistribuicao->buscarProdutosAprovados($whereProdutoAP, $orderProdutoAP);
                $whereProdutoSR = array('p.idPedidoAlteracao = ?' => $this->_idPedidoAlteracao, 'p.tpAcao <> ?' => 'E');
                $buscarSR = $this->tbPlanoDistribuicao->buscarProdutosSolicitados($whereProdutoSR, $orderProdutoAP);

                if (count($buscarSR) <= 0 && count($buscarAP) > 0) {
                    foreach ($buscarAP as $d) {
                        $dadosCopia = array(
                                    'idPlanoDistribuicao'    => $d->idPlanoDistribuicao
                                    ,'cdArea'                => $d->cdArea
                                    ,'cdSegmento'            => $d->cdSegmento
                                    ,'idPedidoAlteracao'     => $this->_idPedidoAlteracao
                                    ,'idProduto'             => $d->idProduto
                                    ,'idPosicaoLogo'         => $d->idPosicaoLogo
                                    ,'qtPatrocinador'        => $d->qtdPatrocinador
                                    ,'qtProduzida'           => $d->qtdProduzida
                                    ,'qtOutros'              => $d->qtdOutros
                                    ,'qtVendaNormal'         => $d->qtdVendaNormal
                                    ,'qtVendaPromocional'    => $d->qtdVendaPromocional
                                    ,'vlUnitarioNormal'      => $d->vlUnitarioNormal
                                    ,'vlUnitarioPromocional' => $d->vlUnitarioPromocional
                                    ,'stPrincipal'           => $d->stPrincipal
                                    ,'tpAcao'                => 'N'
                                    ,'dtPlanoDistribuicao'   => new Zend_Db_Expr('GETDATE()')
                            );
                        //INSERE UMA C�PIA QUE NAO SER� ALTERADA - AP
                        $dadosCopia['tpPlanoDistribuicao'] = 'AP';
                        $this->tbPlanoDistribuicao->inserir($dadosCopia);

                        //INSERE UMA C�PIA QUE SER� ALTERADA COM OS DADOS FORNECIDOS PELO PROPONENTE - SR
                        $dadosCopia['tpPlanoDistribuicao'] = 'SR';
                        $this->tbPlanoDistribuicao->inserir($dadosCopia);
                    }
                }

                // salva os dados do item do pedido
                if (isset($post->produto) && !empty($post->produto)) {
                    $whereProduto = array_merge($whereItemPedido, array('idProduto = ?' => $post->produto, 'tpPlanoDistribuicao = ?' => 'SR'));
                } else {
                    $whereProduto = array_merge($whereItemPedido, array('idProduto = ?' => $idProduto, 'tpPlanoDistribuicao = ?' => 'SR'));
                }

                if (count($this->tbPlanoDistribuicao->buscar($whereProduto)) <= 0) { // CADASTRA
                    $dadosItemPedido = array(
                                'idPedidoAlteracao'      => $this->_idPedidoAlteracao
                                ,'idProduto'             => $post->produto
                                ,'cdArea'                => $idArea
                                ,'cdSegmento'            => $idSegmento
                                ,'idPosicaoLogo'         => $idPosicaoLogo
                                ,'qtProduzida'           => $qtdBeneficiario
                                ,'qtPatrocinador'        => $qtdPatrocinador
                                ,'qtOutros'              => $qtdDivulgacao
                                ,'qtVendaNormal'         => $qtdNormal
                                ,'qtVendaPromocional'    => $qtdPromocional
                                ,'vlUnitarioNormal'      => $vlNormal
                                ,'vlUnitarioPromocional' => $vlPromocional
                                ,'stPrincipal'           => 0
                                ,'tpAcao'                => $tpAcao
                                ,'tpPlanoDistribuicao'   => 'SR'
                                ,'dsjustificativa'       => $justificativa
                                ,'dtPlanoDistribuicao'   => new Zend_Db_Expr('GETDATE()')
                        );
                    $this->tbPlanoDistribuicao->inserir($dadosItemPedido);
                } else { // ALTERA ou EXCLUI
                    if ($tpAcao == 'E') {
                        //EXCLUI AS PLANILHAS RELACIONADAS AO PRODUTO DA READEQUACAO
                        $whereDeleteTbPlanilha = array(
                                'IdPRONAC = ?' => $this->_idPronac,
                                'idPedidoAlteracao = ?' => $this->_idPedidoAlteracao,
                                'idProduto = ?' => $idProduto,
                                'tpPlanilha = ?' => 'SR',
                                'tpAcao = ?' => 'I',
                                'stAtivo = ?' => 'N'
                            );
                        $this->tbPlanilhaAprovacao->delete($whereDeleteTbPlanilha);

                        //EXCLUI O PRODUTO DA READEQUACAO
                        $whereDeleteTbPlano = array(
                                'idPedidoAlteracao = ?' => $this->_idPedidoAlteracao,
                                'idProduto = ?' => $idProduto,
                                'tpPlanoDistribuicao = ?' => 'SR',
                                'stPrincipal = ?' => 0,
                                'tpAcao = ?' => 'I'
                            );
                        $this->tbPlanoDistribuicao->delete($whereDeleteTbPlano);
                    } else {
                        $dadosItemPedido = array(
                                'idPedidoAlteracao'      => $this->_idPedidoAlteracao
                                ,'cdArea'                => $idArea
                                ,'cdSegmento'            => $idSegmento
                                ,'idPosicaoLogo'         => $idPosicaoLogo
                                ,'qtProduzida'           => $qtdBeneficiario
                                ,'qtPatrocinador'        => $qtdPatrocinador
                                ,'qtOutros'              => $qtdDivulgacao
                                ,'qtVendaNormal'         => $qtdNormal
                                ,'qtVendaPromocional'    => $qtdPromocional
                                ,'vlUnitarioNormal'      => $vlNormal
                                ,'vlUnitarioPromocional' => $vlPromocional
                                ,'tpAcao'                => $tpAcao
                                ,'dsjustificativa'       => $justificativa
                                ,'dtPlanoDistribuicao'   => new Zend_Db_Expr('GETDATE()')
                            );
                        $this->tbPlanoDistribuicao->alterar($dadosItemPedido, $whereProduto);
                    }
                }

                // salva os dados da justificativa
                    $whereItemPedido = array_merge($whereItemPedido, array('tpAlteracaoProjeto = ?' => $tpAlteracaoProjeto)); // filtro para altera��o
                    if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) { // CADASTRA
                        $dadosJustificativa = array(
                                'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                                ,'dsJustificativa'    => $justificativa
                                ,'tpAlteracaoProjeto' => $tpAlteracaoProjeto
                                ,'stVerificacao'      => 0
                        );
                        $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa);
                    } else { // ALTERA
                        $dadosJustificativa = array('dsJustificativa' => $justificativa);
                        $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                    }
                parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', 'readequacao/produtos/idpronac/' . Seguranca::encrypt($this->_idPronac), 'CONFIRM');
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if
            // ========== FIM: FORMULARIO ENVIADO VIA POST ==========
    } // fecha metodo cadastrarProdutosAction()


    /**
     * M�todo para solicitar altera��o na Ficha T�cnica do Projeto
     * @access public
     * @param void
     * @return void
     */
    public function fichaTecnicaAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            // objetos utilizados
            $this->PreProjeto                      = new Proposta_Model_DbTable_PreProjeto();
            $this->tbProposta                      = new tbProposta();
            $this->tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $this->tbPedidoAltProjetoXArquivo      = new tbPedidoAltProjetoXArquivo();

            // busca os dados aprovados
            $buscarAP = $this->PreProjeto->buscar(array('idPreProjeto = ?' => $this->_idPreProjeto))->current();
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o
            $buscarSR = $this->tbProposta->buscarPedido(array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao))->current();
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o

            // busca o pedido (justificativa) da solicita��o de readequa��o
            $whereTipoReadequacao = array(
                'p.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(3) // ficha t�cnica
            );
            $buscarPedido = $this->tbPedidoAlteracaoXTipoAlteracao->buscarPedido($whereTipoReadequacao)->current();
            $this->view->pedido = $buscarPedido; // manda as informa��es para a vis�o

            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(3) // ficha t�cnica
            );
            $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
            $this->view->arquivos = $buscarArquivo; // manda as informa��es para a vis�o
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
        }


        // ========== IN�CIO: FORMUL�RIO ENVIADO VIA POST ==========
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $fichaSolicitada    = $post->fichaSolicitada;
            $justificativa      = $post->justificativa;
            $stPedidoAlteracao  = $post->stPedidoAlteracao;
            $siVerificacao      = $post->siVerificacao;
            $tpAlteracaoProjeto = $post->tpAlteracaoProjeto;

            try {
                // valida��o dos dados
                if (empty($fichaSolicitada) || empty($justificativa)) :
                    throw new Exception('Dados obrigat&oacute;rios n&atilde;o informados!');
                endif;

                // atualiza o status do pedido de readequa��o
                $this->_stPedidoAlteracao = $stPedidoAlteracao;

                // salva os dados do pedido
                $this->salvarPedido($stPedidoAlteracao, $siVerificacao);

                // filtro para altera��o
                $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

                // salva os dados do item do pedido
                if (count($this->tbProposta->buscar($whereItemPedido)) <= 0) : // CADASTRA
                    $dadosItemPedido = array(
                        'tpProposta'         => 'SA'
                        ,'dtProposta'        => new Zend_Db_Expr('GETDATE()')
                        ,'dsFichaTecnica'    => $fichaSolicitada
                        ,'idPedidoAlteracao' => $this->_idPedidoAlteracao
                    );
                $this->tbProposta->inserir($dadosItemPedido); else : // ALTERA
                    $dadosItemPedido = array(
                        'dtProposta'      => new Zend_Db_Expr('GETDATE()')
                        ,'dsFichaTecnica' => $fichaSolicitada
                    );
                $this->tbProposta->alterar($dadosItemPedido, $whereItemPedido);
                endif;

                // salva os dados da justificativa
                $whereItemPedido = array_merge($whereItemPedido, array('tpAlteracaoProjeto = ?' => $tpAlteracaoProjeto)); // filtro para altera��o
                if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) : // CADASTRA
                    $dadosJustificativa = array(
                        'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                        ,'dsJustificativa'    => $justificativa
                        ,'tpAlteracaoProjeto' => $tpAlteracaoProjeto
                        ,'stVerificacao'      => 0
                    );
                $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa); else : // ALTERA
                    $dadosJustificativa = array('dsJustificativa' => $justificativa);
                $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                endif;

                // cadastra os arquivos
                $this->cadastrarArquivos($_FILES, $this->_idPedidoAlteracao, $tpAlteracaoProjeto);

                parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', $this->_urlAtual, 'CONFIRM');
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if
        // ========== FIM: FORMUL�RIO ENVIADO VIA POST ==========
    } // fecha m�todo fichaTecnicaAction()



    /**
     * M�todo para solicitar cadastros, altera��es e inclus�es de novos Locais de Realiza��o
     * @access public
     * @param void
     * @return void
     */
    public function localRealizacaoAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            // objetos utilizados
            $this->Pais                            = new Pais();
            $this->Uf                              = new Uf();
            $this->PreProjeto                      = new Proposta_Model_DbTable_PreProjeto();
            $this->tbAbrangencia                   = new tbAbrangencia();
            $this->tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $this->tbPedidoAltProjetoXArquivo      = new tbPedidoAltProjetoXArquivo();

            // busca os dados das combos
            $this->view->pais = $this->Pais->buscar();
            $this->view->uf   = $this->Uf->buscar(array(), array('Sigla ASC'));

            // busca os dados aprovados
            $orderAbrangencia = array('p.Descricao', 'u.Sigla', 'm.Descricao');
            $buscarAP = $this->tbAbrangencia->buscarLocaisAprovados(array('a.idProjeto = ?' => $this->_idPreProjeto, 'a.stAbrangencia = ?' => 1), $orderAbrangencia);
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o
            $buscarSR = $this->tbAbrangencia->buscarLocaisSolicitados(array('a.idPedidoAlteracao = ?' => $this->_idPedidoAlteracao, 'a.tpAcao <> ?' => 'E'), $orderAbrangencia);
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o

            // busca o pedido (justificativa) da solicita��o de readequa��o
            $whereTipoReadequacao = array(
                'p.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(4) // local de realiza��o
            );
            $buscarPedido = $this->tbPedidoAlteracaoXTipoAlteracao->buscarPedido($whereTipoReadequacao)->current();
            $this->view->pedido = $buscarPedido; // manda as informa��es para a vis�o

            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(4) // local de realiza��o
            );
            $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
            $this->view->arquivos = $buscarArquivo; // manda as informa��es para a vis�o
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
        }


        // ========== IN�CIO: FORMUL�RIO ENVIADO VIA POST ==========
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $pais               = $post->pais;
            $uf                 = !empty($post->uf)     ? $post->uf     : 0;
            $cidade             = !empty($post->cidade) ? $post->cidade : 0;
            $justificativa      = $post->justificativa;
            $tpAcao             = $post->tpAcao;
            $dsExclusao         = !empty($post->justificativaExclusao) ? $post->justificativaExclusao : null;
            $stPedidoAlteracao  = $post->stPedidoAlteracao;
            $siVerificacao      = $post->siVerificacao;
            $tpAlteracaoProjeto = $post->tpAlteracaoProjeto;

            try {
                // valida��o dos dados
                if (empty($pais) || (empty($justificativa) && empty($dsExclusao))) :
                    throw new Exception('Dados obrigat&oacute;rios n&atilde;o informados!');
                endif;

                // atualiza o status do pedido de readequa��o
                $this->_stPedidoAlteracao = $stPedidoAlteracao;

                // salva os dados do pedido
                $this->salvarPedido($stPedidoAlteracao, $siVerificacao);

                // filtro para altera��o
                $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

                // faz a c�pia da tabela original para a solicitada caso n�o exista na solicitada, e, exista algum registro na aprovada
                if (count($this->tbAbrangencia->buscar(array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao))) <= 0 && count($buscarAP) > 0) :
                    foreach ($buscarAP as $d) :
                        $dadosCopia = array(
                            'tpAbrangencia'        => 'SA'
                            ,'idPais'              => $d->idPais
                            ,'idUF'                => $d->idUF
                            ,'idMunicipioIBGE'     => $d->idMunicipioIBGE
                            ,'tpAcao'              => 'N'
                            ,'idAbrangenciaAntiga' => $d->idAbrangencia
                            ,'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                            ,'dtRegistro'          => new Zend_Db_Expr('GETDATE()')
                        );
                $this->tbAbrangencia->inserir($dadosCopia);
                endforeach;
                endif;

                // salva os dados do item do pedido
                $whereTbAbrangencia = array();
                $whereTbAbrangencia['idPais = ?']            = $pais;
                $whereTbAbrangencia['idUF = ?']              = $uf;
                $whereTbAbrangencia['idMunicipioIBGE = ?']   = $cidade;
                $whereTbAbrangencia['idPedidoAlteracao = ?'] = $this->_idPedidoAlteracao;
                if (count($this->tbAbrangencia->buscar($whereTbAbrangencia)) <= 0) : // CADASTRA
                    $dadosItemPedido = array(
                        'tpAbrangencia'      => 'SA'
                        ,'idPais'            => $pais
                        ,'idUF'              => $uf
                        ,'idMunicipioIBGE'   => $cidade
                        ,'dtRegistro'        => new Zend_Db_Expr('GETDATE()')
                        ,'idPedidoAlteracao' => $this->_idPedidoAlteracao
                        ,'tpAcao'            => $tpAcao
                        ,'dsExclusao'        => $dsExclusao
                    );
                $this->tbAbrangencia->inserir($dadosItemPedido); else : // ALTERA
                    $dadosItemPedido = array(
                        'dtRegistro'  => new Zend_Db_Expr('GETDATE()')
                        ,'tpAcao'     => $tpAcao
                        ,'dsExclusao' => $dsExclusao
                    );
                $this->tbAbrangencia->alterar($dadosItemPedido, $whereTbAbrangencia);
                endif;

                // salva os dados da justificativa
                $whereItemPedido = array_merge($whereItemPedido, array('tpAlteracaoProjeto = ?' => $tpAlteracaoProjeto)); // filtro para altera��o
                if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) : // CADASTRA
                    $dadosJustificativa = array(
                        'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                        ,'dsJustificativa'    => $justificativa
                        ,'tpAlteracaoProjeto' => $tpAlteracaoProjeto
                        ,'stVerificacao'      => 0
                    );
                $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa); else : // ALTERA
                    $dadosJustificativa = array('dsJustificativa' => $justificativa);
                $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                endif;

                // cadastra os arquivos
                $this->cadastrarArquivos($_FILES, $this->_idPedidoAlteracao, $tpAlteracaoProjeto);

                parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', $this->_urlAtual, 'CONFIRM');
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if
        // ========== FIM: FORMUL�RIO ENVIADO VIA POST ==========
    } // fecha m�todo localRealizacaoAction()



    /**
     * M�todo para solicitar altera��es no Nome do Projeto
     * @access public
     * @param void
     * @return void
     */
    public function nomeProjetoAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            // objetos utilizados
            $this->PreProjeto                      = new Proposta_Model_DbTable_PreProjeto();
            $this->tbProposta                      = new tbProposta();
            $this->tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $this->tbPedidoAltProjetoXArquivo      = new tbPedidoAltProjetoXArquivo();

            // busca os dados aprovados
            $buscarAP = $this->Projetos->buscar(array('IdPRONAC = ?' => $this->_idPronac))->current();
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o
            $buscarSR = $this->tbProposta->buscarPedido(array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao))->current();
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o

            // busca o pedido (justificativa) da solicita��o de readequa��o
            $whereTipoReadequacao = array(
                'p.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(5) // nome do projeto
            );
            $buscarPedido = $this->tbPedidoAlteracaoXTipoAlteracao->buscarPedido($whereTipoReadequacao)->current();
            $this->view->pedido = $buscarPedido; // manda as informa��es para a vis�o

            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(5) // nome do projeto
            );
            $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
            $this->view->arquivos = $buscarArquivo; // manda as informa��es para a vis�o
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
        }


        // ========== IN�CIO: FORMUL�RIO ENVIADO VIA POST ==========
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $nome               = $post->nome;
            $justificativa      = $post->justificativa;
            $stPedidoAlteracao  = $post->stPedidoAlteracao;
            $siVerificacao      = $post->siVerificacao;
            $tpAlteracaoProjeto = $post->tpAlteracaoProjeto;

            try {
                // valida��o dos dados
                if (empty($nome) || empty($justificativa)) :
                    throw new Exception('Dados obrigat&oacute;rios n&atilde;o informados!');
                endif;

                // atualiza o status do pedido de readequa��o
                $this->_stPedidoAlteracao = $stPedidoAlteracao;

                // salva os dados do pedido
                $this->salvarPedido($stPedidoAlteracao, $siVerificacao);

                // filtro para altera��o
                $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

                // salva os dados do item do pedido
                if (count($this->tbProposta->buscar($whereItemPedido)) <= 0) : // CADASTRA
                    $dadosItemPedido = array(
                        'tpProposta'         => 'SA'
                        ,'dtProposta'        => new Zend_Db_Expr('GETDATE()')
                        ,'nmProjeto'         => $nome
                        ,'idPedidoAlteracao' => $this->_idPedidoAlteracao
                    );
                $this->tbProposta->inserir($dadosItemPedido); else : // ALTERA
                    $dadosItemPedido = array(
                        'dtProposta' => new Zend_Db_Expr('GETDATE()')
                        ,'nmProjeto' => $nome
                    );
                $this->tbProposta->alterar($dadosItemPedido, $whereItemPedido);
                endif;

                // salva os dados da justificativa
                $whereItemPedido = array_merge($whereItemPedido, array('tpAlteracaoProjeto = ?' => $tpAlteracaoProjeto)); // filtro para altera��o
                if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) : // CADASTRA
                    $dadosJustificativa = array(
                        'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                        ,'dsJustificativa'    => $justificativa
                        ,'tpAlteracaoProjeto' => $tpAlteracaoProjeto
                        ,'stVerificacao'      => 0
                    );
                $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa); else : // ALTERA
                    $dadosJustificativa = array('dsJustificativa' => $justificativa);
                $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                endif;

                // cadastra os arquivos
                $this->cadastrarArquivos($_FILES, $this->_idPedidoAlteracao, $tpAlteracaoProjeto);

                parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', $this->_urlAtual, 'CONFIRM');
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if
        // ========== FIM: FORMUL�RIO ENVIADO VIA POST ==========
    } // fecha m�todo nomeProjetoAction()



    /**
     * M�todo para solicitar prorroga��o de Prazo de Execu��o do Projeto
     * @access public
     * @param void
     * @return void
     */
    public function prazoExecucaoAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            // objetos utilizados
            $this->Projetos                        = new Projetos();
            $this->tbProrrogacaoPrazo              = new tbProrrogacaoPrazo();
            $this->tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $this->tbPedidoAltProjetoXArquivo      = new tbPedidoAltProjetoXArquivo();

            // busca os dados aprovados
            $buscarAP = $this->Projetos->buscarPeriodoExecucao($this->_idPronac);
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o
            $buscarSR = $this->tbProrrogacaoPrazo->buscarDados($this->_idPronac, 'E', $this->_idPedidoAlteracao)->current();
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o

            // busca o pedido (justificativa) da solicita��o de readequa��o
            $whereTipoReadequacao = array(
                'p.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(9) // prazo de execu��o
            );
            $buscarPedido = $this->tbPedidoAlteracaoXTipoAlteracao->buscarPedido($whereTipoReadequacao)->current();
            $this->view->pedido = $buscarPedido; // manda as informa��es para a vis�o

            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(9) // prazo de execu��o
            );
            $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
            $this->view->arquivos = $buscarArquivo; // manda as informa��es para a vis�o
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
        }


        // ========== IN�CIO: FORMUL�RIO ENVIADO VIA POST ==========
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $dtInicioExecucao   = Data::dataAmericana($post->dtInicioExecucaoSR);
            $dtFimExecucao      = Data::dataAmericana($post->dtFimExecucaoSR);
            $justificativa      = $post->justificativa;
            $stPedidoAlteracao  = $post->stPedidoAlteracao;
            $siVerificacao      = $post->siVerificacao;
            $tpAlteracaoProjeto = $post->tpAlteracaoProjeto;

            try {
                // valida��o dos dados
                if (empty($dtInicioExecucao) || empty($dtFimExecucao) || empty($justificativa)) :
                    throw new Exception('Dados obrigat&oacute;rios n&atilde;o informados!');
                endif;

                // atualiza o status do pedido de readequa��o
                $this->_stPedidoAlteracao = $stPedidoAlteracao;

                // salva os dados do pedido
                $this->salvarPedido($stPedidoAlteracao, $siVerificacao);

                // filtro para altera��o
                $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

                // salva os dados do item do pedido
                if (count($this->tbProrrogacaoPrazo->buscarDados($this->_idPronac, 'E', $this->_idPedidoAlteracao)) <= 0) : // CADASTRA
                    $dadosItemPedido = array(
                        'idPedidoAlteracao'  => $this->_idPedidoAlteracao
                        ,'tpProrrogacao'     => 'E'
                        ,'dtInicioNovoPrazo' => $dtInicioExecucao
                        ,'dtFimNovoPrazo'    => $dtFimExecucao
                    );
                $this->tbProrrogacaoPrazo->inserir($dadosItemPedido); else : // ALTERA
                    $dadosItemPedido = array(
                        'dtInicioNovoPrazo' => $dtInicioExecucao
                        ,'dtFimNovoPrazo'   => $dtFimExecucao
                    );
                $wherePrazo = array_merge($whereItemPedido, array('tpProrrogacao = ?' => 'E'));
                $this->tbProrrogacaoPrazo->alterar($dadosItemPedido, $wherePrazo);
                endif;

                // salva os dados da justificativa
                $whereItemPedido = array_merge($whereItemPedido, array('tpAlteracaoProjeto = ?' => $tpAlteracaoProjeto)); // filtro para altera��o
                if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) : // CADASTRA
                    $dadosJustificativa = array(
                        'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                        ,'dsJustificativa'    => $justificativa
                        ,'tpAlteracaoProjeto' => $tpAlteracaoProjeto
                        ,'stVerificacao'      => 0
                    );
                $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa); else : // ALTERA
                    $dadosJustificativa = array('dsJustificativa' => $justificativa);
                $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                endif;

                // cadastra os arquivos
                $this->cadastrarArquivos($_FILES, $this->_idPedidoAlteracao, $tpAlteracaoProjeto);

                parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', $this->_urlAtual, 'CONFIRM');
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if
        // ========== FIM: FORMUL�RIO ENVIADO VIA POST ==========
    } // fecha m�todo prazoExecucaoAction()



    /**
     * M�todo para solicitar prorroga��o de Prazo de Capta��o do Projeto
     * @access public
     * @param void
     * @return void
     */
    public function prazoCaptacaoAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            // objetos utilizados
            $this->Projetos                        = new Projetos();
            $this->tbProrrogacaoPrazo              = new tbProrrogacaoPrazo();
            $this->tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $this->tbPedidoAltProjetoXArquivo      = new tbPedidoAltProjetoXArquivo();

            // busca os dados aprovados de execu��o e capta��o
            $buscarExecAP = $this->Projetos->buscarPeriodoExecucao($this->_idPronac);
            $this->view->dadosExecAP = $buscarExecAP; // manda as informa��es para a vis�o
            $buscarAP = $this->Projetos->buscarPeriodoCaptacao($this->_idPronac, null, null, false, array('TipoAprovacao = ?' => 1, 'PortariaAprovacao IS NOT NULL' => ''));
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o (execu��o e capta��o)
            $buscarExecSR = $this->tbProrrogacaoPrazo->buscarDados($this->_idPronac, 'E', $this->_idPedidoAlteracao)->current();
            $this->view->dadosExecSR = $buscarExecSR; // manda as informa��es para a vis�o
            $buscarSR = $this->tbProrrogacaoPrazo->buscarDados($this->_idPronac, 'C', $this->_idPedidoAlteracao)->current();
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o

            // busca o pedido (justificativa) da solicita��o de readequa��o
            $whereTipoReadequacao = array(
                'p.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(8) // prazo de capta��o
            );
            $buscarPedido = $this->tbPedidoAlteracaoXTipoAlteracao->buscarPedido($whereTipoReadequacao)->current();
            $this->view->pedido = $buscarPedido; // manda as informa��es para a vis�o

            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(8) // prazo de capta��o
            );
            $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
            $this->view->arquivos = $buscarArquivo; // manda as informa��es para a vis�o
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
        }


        // ========== IN�CIO: FORMUL�RIO ENVIADO VIA POST ==========
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $dtInicioCaptacao   = Data::dataAmericana($post->dtInicioCaptacaoSR);
            $dtFimCaptacao      = Data::dataAmericana($post->dtFimCaptacaoSR);
            $justificativa      = $post->justificativa;
            $stPedidoAlteracao  = $post->stPedidoAlteracao;
            $siVerificacao      = $post->siVerificacao;
            $tpAlteracaoProjeto = $post->tpAlteracaoProjeto;

            try {
                // valida��o dos dados
                if (empty($dtInicioCaptacao) || empty($dtFimCaptacao) || empty($justificativa)) :
                    throw new Exception('Dados obrigat&oacute;rios n&atilde;o informados!');
                endif;

                // atualiza o status do pedido de readequa��o
                $this->_stPedidoAlteracao = $stPedidoAlteracao;

                // salva os dados do pedido
                $this->salvarPedido($stPedidoAlteracao, $siVerificacao);

                // filtro para altera��o
                $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

                // salva os dados do item do pedido
                if (count($this->tbProrrogacaoPrazo->buscarDados($this->_idPronac, 'C', $this->_idPedidoAlteracao)) <= 0) : // CADASTRA
                    $dadosItemPedido = array(
                        'idPedidoAlteracao'  => $this->_idPedidoAlteracao
                        ,'tpProrrogacao'     => 'C'
                        ,'dtInicioNovoPrazo' => $dtInicioCaptacao
                        ,'dtFimNovoPrazo'    => $dtFimCaptacao
                    );
                $this->tbProrrogacaoPrazo->inserir($dadosItemPedido); else : // ALTERA
                    $dadosItemPedido = array(
                        'dtInicioNovoPrazo' => $dtInicioCaptacao
                        ,'dtFimNovoPrazo'   => $dtFimCaptacao
                    );
                $wherePrazo = array_merge($whereItemPedido, array('tpProrrogacao = ?' => 'C'));
                $this->tbProrrogacaoPrazo->alterar($dadosItemPedido, $wherePrazo);
                endif;

                // salva os dados da justificativa
                $whereItemPedido = array_merge($whereItemPedido, array('tpAlteracaoProjeto = ?' => $tpAlteracaoProjeto)); // filtro para altera��o
                if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) : // CADASTRA
                    $dadosJustificativa = array(
                        'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                        ,'dsJustificativa'    => $justificativa
                        ,'tpAlteracaoProjeto' => $tpAlteracaoProjeto
                        ,'stVerificacao'      => 0
                    );
                $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa); else : // ALTERA
                    $dadosJustificativa = array('dsJustificativa' => $justificativa);
                $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                endif;

                // cadastra os arquivos
                $this->cadastrarArquivos($_FILES, $this->_idPedidoAlteracao, $tpAlteracaoProjeto);

                parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', $this->_urlAtual, 'CONFIRM');
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if
        // ========== FIM: FORMUL�RIO ENVIADO VIA POST ==========
    } // fecha m�todo prazoCaptacaoAction()



    /**
     * M�todo para solicitar altera��o na Proposta Pedag�gica do Projeto
     * @access public
     * @param void
     * @return void
     */
    public function propostaPedagogicaAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            // objetos utilizados
            $this->PreProjeto                      = new Proposta_Model_DbTable_PreProjeto();
            $this->tbProposta                      = new tbProposta();
            $this->tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $this->tbPedidoAltProjetoXArquivo      = new tbPedidoAltProjetoXArquivo();

            // busca os dados aprovados
            $buscarAP = $this->PreProjeto->buscar(array('idPreProjeto = ?' => $this->_idPreProjeto))->current();
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o
            $buscarSR = $this->tbProposta->buscarPedido(array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao))->current();
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o

            // busca o pedido (justificativa) da solicita��o de readequa��o
            $whereTipoReadequacao = array(
                'p.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(6) // proposta pedag�gica
            );
            $buscarPedido = $this->tbPedidoAlteracaoXTipoAlteracao->buscarPedido($whereTipoReadequacao)->current();
            $this->view->pedido = $buscarPedido; // manda as informa��es para a vis�o

            // busca os arquivos da solicita��o de readequa��o
            /*$whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $this->_idPedidoAlteracao
                ,'x.tpAlteracaoProjeto IN (?)' => array(6) // proposta pedag�gica
            );
            $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
            $this->view->arquivos = $buscarArquivo; // manda as informa��es para a vis�o*/
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
        }

        // ========== IN�CIO: FORMUL�RIO ENVIADO VIA POST ==========
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $especificacaoSolicitada  = $post->especificacaoSolicitada;
            $estrategiaExecSolicitada = $post->estrategiaExecSolicitada;
            $justificativa            = $post->justificativa;
            $stPedidoAlteracao        = $post->stPedidoAlteracao;
            $siVerificacao            = $post->siVerificacao;
            $tpAlteracaoProjeto       = $post->tpAlteracaoProjeto;

            try {
                // valida��o dos dados
                if (empty($especificacaoSolicitada) || empty($estrategiaExecSolicitada) || empty($justificativa)) :
                            throw new Exception('Dados obrigat&oacute;rios n&atilde;o informados!');
                endif;

                // atualiza o status do pedido de readequa��o
                $this->_stPedidoAlteracao = $stPedidoAlteracao;

                // salva os dados do pedido
                $this->salvarPedido($stPedidoAlteracao, $siVerificacao);

                // filtro para altera��o
                $whereItemPedido = array('idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);

                // salva os dados do item do pedido
                        if (count($this->tbProposta->buscar($whereItemPedido)) <= 0) : // CADASTRA
                            $dadosItemPedido = array(
                                'tpProposta'              => 'SA'
                                ,'dtProposta'             => new Zend_Db_Expr('GETDATE()')
                                ,'dsEspecificacaoTecnica' => $especificacaoSolicitada
                                ,'dsEstrategiaExecucao'   => $estrategiaExecSolicitada
                                ,'idPedidoAlteracao'      => $this->_idPedidoAlteracao
                            );
                $this->tbProposta->inserir($dadosItemPedido); else : // ALTERA
                            $dadosItemPedido = array(
                                'dtProposta'              => new Zend_Db_Expr('GETDATE()')
                                ,'dsEspecificacaoTecnica' => $especificacaoSolicitada
                                ,'dsEstrategiaExecucao'   => $estrategiaExecSolicitada
                            );
                $this->tbProposta->alterar($dadosItemPedido, $whereItemPedido);
                endif;

                // salva os dados da justificativa
                        $whereItemPedido = array_merge($whereItemPedido, array('tpAlteracaoProjeto = ?' => $tpAlteracaoProjeto)); // filtro para altera��o
                        if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) : // CADASTRA
                            $dadosJustificativa = array(
                                'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                                ,'dsJustificativa'    => $justificativa
                                ,'tpAlteracaoProjeto' => $tpAlteracaoProjeto
                                ,'stVerificacao'      => 0
                            );
                $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa); else : // ALTERA
                            $dadosJustificativa = array('dsJustificativa' => $justificativa);
                $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                endif;

                // cadastra os arquivos
                //$this->cadastrarArquivos($_FILES, $this->_idPedidoAlteracao, $tpAlteracaoProjeto);

                parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', $this->_urlAtual, 'CONFIRM');
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if
        // ========== FIM: FORMUL�RIO ENVIADO VIA POST ==========
    } // fecha m�todo propostaPedagogicaAction()



    /**
     * M�todo com a Planilha de Custos por Produtos do Projeto
     * @access public
     * @param void
     * @return void
     */
    public function custoProdutosAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            $this->tbPlanoDistribuicao = new tbPlanoDistribuicao();

            // busca os dados aprovados
            $orderProduto = array('p.stPrincipal DESC', 'd.Descricao');
            $buscarAP = $this->tbPlanoDistribuicao->buscarProdutosAprovados(array('p.idProjeto = ?' => $this->_idPreProjeto, 'p.stPlanoDistribuicaoProduto = ?' => 1), $orderProduto);
            $this->view->dadosAP = $buscarAP; // manda as informa��es para a vis�o

            // busca os dados com solicita��o de readequa��o
            $buscarSR = $this->tbPlanoDistribuicao->buscarProdutosSolicitados(array('p.idPedidoAlteracao = ?' => $this->_idPedidoAlteracao, 'p.tpAcao <> ?' => 'E', 'tpPlanoDistribuicao = ?' => 'SR'), $orderProduto);
            $this->view->dadosSR = $buscarSR; // manda as informa��es para a vis�o
        } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
            }
    } // fecha m�todo custoProdutosAction()


    /**
     * M�todo com a Planilha de Custos Administrativo do Projeto
     * @access public
     * @param void
     * @return void
     */
    public function custoAdministrativoAction()
    {
        $this->_redirect('readequacao/custo/idpronac/' . $this->_idPronac . '/idproduto/0');
    } // fecha m�todo custoAdministrativoAction()



    /**
     * M�todo com a Planilha de Custos (Tanto pra Produtos como para Administrativos)
     * @access public
     * @param void
     * @return void
     */
    public function custoAction()
    {
        try {
            // verifica se o usu�rio logado tem permiss�o para acessar o projeto
            $this->verificarPermissaoAcesso(false, true, false);

            // objetos utilizados
            $this->tbPlanilhaAprovacao             = new PlanilhaAprovacao();
            $this->tbPlanilhaEtapa                 = new PlanilhaEtapa();
            $this->tbPlanilhaUnidade               = new tbPlanilhaUnidade();
            $this->tbPlanilhaItens                 = new PlanilhaItens();
            $this->tbPlanoDistribuicao             = new tbPlanoDistribuicao();
            $this->Uf                              = new Uf();
            $this->Verificacao                     = new Verificacao();
            $this->tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();

            // pega o c�digo do produto
            $idProduto = $this->_request->getParam('idproduto');
            $idProduto = empty($idProduto) ? 0 : $idProduto;

            // busca os dados aprovado do produto
            $buscarProdutoAP = $this->tbPlanoDistribuicao->buscarProdutosAprovados(array('p.idProduto = ?' => $idProduto, 'p.idProjeto = ?' => $this->_idPreProjeto, 'p.stPlanoDistribuicaoProduto = ?' => 1));

            // busca os dados com solicita��o de readequa��o do produto
            $buscarProdutoSR = $this->tbPlanoDistribuicao->buscarProdutosSolicitados(array('p.idProduto = ?' => $idProduto, 'p.idPedidoAlteracao = ?' => $this->_idPedidoAlteracao, 'p.tpAcao <> ?' => 'E'));

            /*if (count($buscarProdutoAP) <= 0 && count($buscarProdutoSR) <= 0) :
            parent::message('Nenhum projeto encontrado para o Produto informado!', 'principalproponente', 'ALERT');
            endif;*/

            // atribui o nome do produto
            $nomeProduto = isset($buscarProdutoSR[0]['dsProduto']) ? $buscarProdutoSR[0]['dsProduto'] : $buscarProdutoAP[0]['dsProduto'];
            $nomeProduto = !empty($nomeProduto) ? $nomeProduto : 'Administra&ccedil;&atilde;o do Projeto';

            // pega a planilha aprovada e a planilha solicitada com os itens de custo do produto
            $orderPlanilha = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
            $whereAP       = array('PAP.tpPlanilha = ?' => 'CO', 'PAP.stAtivo = ?' => 'S', 'PAP.IdPRONAC = ?' => $this->_idPronac, 'PAP.idProduto = ?' => $idProduto);
            $whereSR       = array('PAP.tpPlanilha = ?' => 'SR', 'PAP.stAtivo = ?' => 'N', 'PAP.IdPRONAC = ?' => $this->_idPronac, 'PAP.idProduto = ?' => $idProduto, 'PAP.idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);
            $buscarAP = $this->tbPlanilhaAprovacao->buscarCustosReadequacao($whereAP, $orderPlanilha);
            $buscarSR = $this->tbPlanilhaAprovacao->buscarCustosReadequacao($whereSR, $orderPlanilha);

            // monta as combos
            $this->view->etapas   = $this->tbPlanilhaEtapa->buscar(array(), array('idPlanilhaEtapa ASC'));
            $this->view->unidades = $this->tbPlanilhaUnidade->buscar(array(), array('Descricao ASC'));
            $this->view->fontes   = $this->Verificacao->buscarTipos(array('t.idTipo = ?' => 5), array('v.Descricao ASC'));
            $this->view->uf       = $this->Uf->buscar(array(), array('Sigla ASC'));

            // monta a planilha aprovada
            $planAP = array();
            $cont = 0;
            foreach ($buscarAP as $r) :
                    $produto = empty($r->Produto) ? 'Administra&ccedil;&atilde;o do Projeto' : $r->Produto;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaAprovacao'] = $r->idPlanilhaAprovacao;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrFonteRecurso']      = $r->nrFonteRecurso;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['FonteRecurso']        = $r->FonteRecurso;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idProduto']           = $r->idProduto;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Produto']             = $r->Produto;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idEtapa']             = $r->idEtapa;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Etapa']               = $r->Etapa;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['UF']                  = $r->UF;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Cidade']              = $r->Cidade;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaItem']      = $r->idPlanilhaItem;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Item']                = $r->Item;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idUnidade']           = $r->idUnidade;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Unidade']             = $r->Unidade;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtItem']              = (int) $r->qtItem;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrOcorrencia']        = (int) $r->nrOcorrencia;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlUnitario']          = $r->vlUnitario;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlTotal']             = $r->vlTotal;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtDias']              = $r->qtDias;
            $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['dsJustificativa']     = $r->dsJustificativa;
            $cont++;
            endforeach;

            // monta a planilha solicitada
            $planSR = array();
            $cont = 0;
            foreach ($buscarSR as $r) :
                    $produto = empty($r->Produto) ? 'Administra&ccedil;&atilde;o do Projeto' : $r->Produto;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaAprovacao'] = $r->idPlanilhaAprovacao;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrFonteRecurso']      = $r->nrFonteRecurso;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['FonteRecurso']        = $r->FonteRecurso;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idProduto']           = $r->idProduto;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Produto']             = $r->Produto;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idEtapa']             = $r->idEtapa;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Etapa']               = $r->Etapa;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['UF']                  = $r->UF;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Cidade']              = $r->Cidade;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaItem']      = $r->idPlanilhaItem;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Item']                = $r->Item;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idUnidade']           = $r->idUnidade;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Unidade']             = $r->Unidade;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtItem']              = (int) $r->qtItem;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrOcorrencia']        = (int) $r->nrOcorrencia;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlUnitario']          = $r->vlUnitario;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlTotal']             = $r->vlTotal;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtDias']              = $r->qtDias;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['dsJustificativa']     = $r->dsJustificativa;
            $planSR[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['tpAcao']              = $r->tpAcao;
            $cont++;
            endforeach;

            // manda as informa��es do produto, a planilha aprovada e a solicitada para a vis�o
            $this->view->idProduto = $idProduto;
            $this->view->Produto   = $nomeProduto;
            $this->view->planAP    = $planAP;
            $this->view->planSR    = $planSR;
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $this->_urlAtual, 'ERROR');
        }


        // ========== IN�CIO: FORMUL�RIO ENVIADO VIA POST ==========
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formul�rio
            $post = Zend_Registry::get('post');
            $dadosEtapa = explode(':', $post->etapa);
            $dadosEtapa = $dadosEtapa[0];

            $idPlanilhaAprovacao    = $post->idPlanilhaAprovacao;
            $etapa                  = $dadosEtapa;
            $item                   = $post->item;
            $unidade                = $post->unidade;
            $qtd                    = (int) $post->qtd;
            $ocorrencia             = (int) $post->ocorrencia;
            $vlUnitario             = Mascara::delMaskMoeda($post->vlUnitario);
            $dias                   = $post->dias;
            $fonte                  = $post->fonte;
            $uf                     = $post->uf;
            $cidade                 = $post->cidade;
            $justificativa          = $post->justificativa;
            $stPedidoAlteracao      = $post->stPedidoAlteracao;
            $siVerificacao          = $post->siVerificacao;
//                $tpAlteracaoProjeto     = $post->tpAlteracaoProjeto;
                $tpAlteracaoProjeto     = 7; //Agora passa a ser o mesmo de produto.
                $tpAcao                 = $post->tpAcao;
            $dsItem                 = 'Produto';
            $tpDespesa              = 0;
            $nrContraPartida        = 0;
            $tpPlanilha             = 'SR';
            $stAtivo                = 'N';
            $tpPessoa               = 0;
            $tpPlanilhaEnviada      = $post->tpPlanilhaEnviada;

            try {
                // valida��o dos dados
                if (empty($etapa) || empty($item) || empty($unidade) || empty($vlUnitario) || empty($fonte) || empty($uf) || empty($cidade) || empty($justificativa)) :
                        throw new Exception('Dados obrigat&oacute;rios n&atilde;o informados!');
                endif;

                // atualiza o status do pedido de readequa��o
                $this->_stPedidoAlteracao = $stPedidoAlteracao;

                // salva os dados do pedido
                $this->salvarPedido($stPedidoAlteracao, $siVerificacao);

                // faz a c�pia da tabela original para a solicitada caso n�o exista na solicitada, e, exista algum registro na aprovada
                $wherePlanilhaAP  = array('PAP.tpPlanilha = ?' => 'CO', 'PAP.stAtivo = ?' => 'S', 'PAP.IdPRONAC = ?' => $this->_idPronac);
                $wherePlanilhaSR  = array('PAP.tpPlanilha = ?' => 'SR', 'PAP.stAtivo = ?' => 'N', 'PAP.IdPRONAC = ?' => $this->_idPronac, 'PAP.idPedidoAlteracao = ?' => $this->_idPedidoAlteracao);
                $buscarPlanilhaAP = $this->tbPlanilhaAprovacao->buscarCustosReadequacao($wherePlanilhaAP);
                $buscarPlanilhaSR = $this->tbPlanilhaAprovacao->buscarCustosReadequacao($wherePlanilhaSR);
                if (count($buscarPlanilhaSR) <= 0 && count($buscarPlanilhaAP) > 0) :
                        foreach ($buscarPlanilhaAP as $d) :
                            $dadosCopia = array(
                                    'tpPlanilha'              => $tpPlanilha
                                    ,'dtPlanilha'             => new Zend_Db_Expr('GETDATE()')
                                    ,'idPlanilhaProjeto'      => $d->idPlanilhaProjeto
                                    ,'idPlanilhaProposta'     => $d->idPlanilhaProposta
                                    ,'IdPRONAC'               => $d->IdPRONAC
                                    ,'idProduto'              => $d->idProduto
                                    ,'idEtapa'                => $d->idEtapa
                                    ,'idPlanilhaItem'         => $d->idPlanilhaItem
                                    ,'dsItem'                 => $d->dsItem
                                    ,'idUnidade'              => $d->idUnidade
                                    ,'qtItem'                 => $d->qtItem
                                    ,'nrOcorrencia'           => $d->nrOcorrencia
                                    ,'vlUnitario'             => $d->vlUnitario
                                    ,'qtDias'                 => $d->qtDias
                                    ,'tpDespesa'              => $d->tpDespesa
                                    ,'tpPessoa'               => $d->tpPessoa
                                    ,'nrContraPartida'        => $d->nrContraPartida
                                    ,'nrFonteRecurso'         => $d->nrFonteRecurso
                                    ,'idUFDespesa'            => $d->idUF
                                    ,'idMunicipioDespesa'     => $d->idMunicipio
                                    ,'dsJustificativa'        => null
                                    ,'idAgente'               => $this->_idAgenteProjeto
                                    ,'idPlanilhaAprovacaoPai' => $d->idPlanilhaAprovacao // vincula ao pai
                                    ,'idPedidoAlteracao'      => $this->_idPedidoAlteracao
                                    ,'tpAcao'                 => $stAtivo
                                    ,'idRecursoDecisao'       => null
                                    ,'stAtivo'                => $stAtivo
                            );
                $this->tbPlanilhaAprovacao->inserir($dadosCopia);
                endforeach;
                endif;

                // salva os dados do item do pedido
                    if ($tpAcao == 'I') : // CADASTRA
                        $dadosItemPedido = array(
                                'dtPlanilha'             => new Zend_Db_Expr('GETDATE()')
                                ,'IdPRONAC'              => $this->_idPronac
                                ,'idProduto'             => $idProduto
                                ,'idEtapa'               => $etapa
                                ,'idPlanilhaItem'        => $item
                                ,'dsItem'                => $dsItem
                                ,'idUnidade'             => $unidade
                                ,'qtItem'                => $qtd
                                ,'nrOcorrencia'          => $ocorrencia
                                ,'vlUnitario'            => $vlUnitario
                                ,'qtDias'                => $dias
                                ,'tpDespesa'             => $tpDespesa
                                ,'tpPessoa'              => $tpPessoa
                                ,'nrContraPartida'       => $nrContraPartida
                                ,'nrFonteRecurso'        => $fonte
                                ,'idUFDespesa'           => $uf
                                ,'idMunicipioDespesa'    => $cidade
                                ,'dsJustificativa'       => $justificativa
                                ,'idAgente'              => $this->_idAgenteProjeto
                                ,'idPedidoAlteracao'     => $this->_idPedidoAlteracao
                                ,'tpAcao'                => $tpAcao
                                ,'tpPlanilha'            => $tpPlanilha
                                ,'stAtivo'               => $stAtivo
                        );
                $this->tbPlanilhaAprovacao->inserir($dadosItemPedido); else : // ALTERACAO OU EXCLUSAO
                        if ($tpAcao == 'E') {
                            $whereBusca = array(
                                'idPlanilhaAprovacao = ?' => $_POST['idPlanilhaAprovacao'],
                                'tpPlanilha = ?' => 'SR',
                                'IdPRONAC = ?' => $this->_idPronac,
                                'idProduto = ?' => $idProduto,
                                'idPlanilhaAprovacaoPai IS NULL' => '',
                                'idPlanilhaProjeto IS NULL' => '',
                                'idPedidoAlteracao = ?' => $this->_idPedidoAlteracao,
                                'tpAcao = ?' => 'I',
                                'stAtivo = ?' => 'N'
                            );
                            $dadosItem = $this->tbPlanilhaAprovacao->buscar($whereBusca);
                            if (count($dadosItem)>0) {
                                $this->tbPlanilhaAprovacao->delete($whereBusca);
                            }
                        }

                $dadosItemUnico = $this->tbPlanilhaAprovacao->fetchRow(array('idPlanilhaAprovacao = ?' => $_POST['idPlanilhaAprovacao']));
                if ($dadosItemUnico && $dadosItemUnico->tpAcao == 'I') {
                    $tpAcao = 'I';
                }

                $whereTbPlanilha = array();
                if ($tpPlanilhaEnviada == 'SR') :
                            $whereTbPlanilha['idPlanilhaAprovacao = ?'] = $idPlanilhaAprovacao; else :
                            $whereTbPlanilha['idPlanilhaAprovacaoPai = ?'] = $idPlanilhaAprovacao;
                endif;
                $whereTbPlanilha['idPedidoAlteracao = ?']   = $this->_idPedidoAlteracao;
                $whereTbPlanilha['tpPlanilha = ?']          = $tpPlanilha;
                $dadosItemPedido = array(
                                'dtPlanilha'             => new Zend_Db_Expr('GETDATE()')
                                ,'idEtapa'               => $etapa
                                ,'idPlanilhaItem'        => $item
                                ,'dsItem'                => $dsItem
                                ,'idUnidade'             => $unidade
                                ,'qtItem'                => $qtd
                                ,'nrOcorrencia'          => $ocorrencia
                                ,'vlUnitario'            => $vlUnitario
                                ,'qtDias'                => $dias
                                ,'nrFonteRecurso'        => $fonte
                                ,'idUFDespesa'           => $uf
                                ,'idMunicipioDespesa'    => $cidade
                                ,'dsJustificativa'       => $justificativa
                                ,'idAgente'              => $this->_idAgenteProjeto
                                ,'tpAcao'                => $tpAcao
                        );
                $this->tbPlanilhaAprovacao->alterar($dadosItemPedido, $whereTbPlanilha);
                endif;

                // salva os dados da justificativa
                    $whereItemPedido = array('tpAlteracaoProjeto = ?' => $tpAlteracaoProjeto, 'idPedidoAlteracao = ?' => $this->_idPedidoAlteracao); // filtro para altera��o
                    if (count($this->tbPedidoAlteracaoXTipoAlteracao->buscar($whereItemPedido)) <= 0) : // CADASTRA
                        $dadosJustificativa = array(
                                'idPedidoAlteracao'   => $this->_idPedidoAlteracao
                                ,'dsJustificativa'    => $justificativa
                                ,'tpAlteracaoProjeto' => $tpAlteracaoProjeto
                                ,'stVerificacao'      => 0
                        );
                $this->tbPedidoAlteracaoXTipoAlteracao->inserir($dadosJustificativa); else : // ALTERA
                        $dadosJustificativa = array('dsJustificativa' => $justificativa);
                $this->tbPedidoAlteracaoXTipoAlteracao->alterar($dadosJustificativa, $whereItemPedido);
                endif;

                parent::message('Solicita&ccedil;&atilde;o realizada com sucesso!', $this->_urlAtual . '/idproduto/' . $idProduto, 'CONFIRM');
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if
            // ========== FIM: FORMUL�RIO ENVIADO VIA POST ==========
    } // fecha m�todo custoAction()



    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o do proponente
     * @access public
     * @param void
     * @return void
     */
    public function historicoProponenteAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // objetos
        $this->tbAlteracaoNomeProponente  = new tbAlteracaoNomeProponente();
        $this->tbPedidoAltProjetoXArquivo = new tbPedidoAltProjetoXArquivo();

        // busca os dados do hist�rico
        $where = array(
            'p.IdPRONAC = ?'                           => $this->_idPronac
            ,'p.siVerificacao IN (?)'                  => array(1, 2)
            ,'j.tpAlteracaoProjeto IN (?)'             => array(1, 2)
            ,'a.tpAlteracaoProjeto IN (?)'             => array(1, 2)
            ,'a.stAvaliacaoItemPedidoAlteracao IN (?)' => array('AP', 'IN'));
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbAlteracaoNomeProponente->historicoReadequacao($where, $order);

        // varre os dados para anexar os arquivos
        $dados       = array();
        $cachePedido = array();
        $cont        = 0;
        for ($i = 0; $i < count($buscar); $i++) :
            // busca os arquivos da solicita��o de readequa��o
            $buscarArquivo = array();
        $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $buscar[$i]['idPedidoAlteracao']
                ,'x.tpAlteracaoProjeto IN (?)' => array(1, 2) // proponente
            );

        $cacheItem = false;
        if (!in_array($buscar[$i]['idPedidoAlteracao'], $cachePedido)) : // verifica se faz parte do mesmo pedido
                $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
        $cachePedido[] = $buscar[$i]['idPedidoAlteracao'];
        $cacheItem     = true;
        $cont++;
        endif;

        $dados['item'][$i]               = ($cacheItem) ? $cont . '&ordf;' : '';
        $dados['CNPJCPF'][$i]            = $buscar[$i]['CNPJCPF'];
        $dados['NomeProponente'][$i]     = utf8_encode($buscar[$i]['NomeProponente']);
        $dados['idPedidoAlteracao'][$i]  = $buscar[$i]['idPedidoAlteracao'];
        $dados['idSolicitante'][$i]      = $buscar[$i]['idSolicitante'];
        $dados['dtSolicitacao'][$i]      = $buscar[$i]['dtSolicitacao'];
        $dados['hrSolicitacao'][$i]      = $buscar[$i]['hrSolicitacao'];
        $dados['dsProponente'][$i]       = $buscar[$i]['dsProponente'];
        $dados['tpAlteracaoProjeto'][$i] = $buscar[$i]['tpAlteracaoProjeto'] == 1 ? 'Nome do Proponente' : 'Troca de Agente';
        $dados['idAgenteAvaliador'][$i]  = $buscar[$i]['idAgenteAvaliador'];
        $dados['dtInicioAvaliacao'][$i]  = $buscar[$i]['dtInicioAvaliacao'];
        $dados['dtFimAvaliacao'][$i]     = $buscar[$i]['dtFimAvaliacao'];
        $dados['stAvaliacao'][$i]        = $buscar[$i]['stAvaliacao'];
        $dados['dsAvaliacao'][$i]        = $buscar[$i]['dsAvaliacao'];
        $dados['Arquivos'][$i]           = $buscarArquivo;
        endfor;

        $this->view->dados = $dados; // manda as informa��es para a vis�o
    } // fecha m�todo historicoProponenteAction()



    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o de produto
     * @access public
     * @param void
     * @return void
     */
    public function historicoProdutosAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        // objetos
        $this->tbPlanoDistribuicao = new tbPlanoDistribuicao();
        // busca os dados do historico
        $where = array(
                'p.IdPRONAC = ?' => $this->_idPronac,
                'h.tpPlanoDistribuicao = ?' => 'SR'
            );
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbPlanoDistribuicao->historicoReadequacao($where, $order);

        // varre os dados para anexar os arquivos
        $dados= array();
        $cachePedido = array();
        $cont = 0;
        for ($i = 0; $i < count($buscar); $i++) :
                $cacheItem = false;
        if (!in_array($buscar[$i]['idPedidoAlteracao'], $cachePedido)) { // verifica se faz parte do mesmo pedido
            $cachePedido[] = $buscar[$i]['idPedidoAlteracao'];
            $cacheItem = true;
            $cont++;
        }
        // busca os produtos alterados pelo t�cnico
        $whereTecnico = array(
                    'p.IdPRONAC = ?' => $this->_idPronac
                    ,'p.siVerificacao IN (?)' => array(1, 2)
                    ,'h.idPedidoAlteracao = ?' => $buscar[$i]['idPedidoAlteracao']);
        $whereTecnico = !empty($buscar[$i]['idPlanoDistribuicao']) ? array_merge($whereTecnico, array('h.idPlanoDistribuicao = ?' => $buscar[$i]['idPlanoDistribuicao'])) : array_merge($whereTecnico, array('h.idPlanoDistribuicao IS NULL' => ''));
        $orderTecnico = array('p.dtSolicitacao ASC');
        $buscarTecnico = $this->tbPlanoDistribuicao->historicoReadequacao($whereTecnico, $orderTecnico);

        $dados['item'][$i]                  = ($cacheItem) ? $cont . '&ordf;' : '';
        $dados['idPlano'][$i]               = $buscar[$i]['idPlano'];
        $dados['idPlanoDistribuicao'][$i]   = $buscar[$i]['idPlanoDistribuicao'];
        $dados['idProduto'][$i]             = $buscar[$i]['idProduto'];
        $dados['Produto'][$i]               = $buscar[$i]['Produto'];
        $dados['cdArea'][$i]                = $buscar[$i]['cdArea'];
        $dados['Area'][$i]                  = $buscar[$i]['Area'];
        $dados['cdSegmento'][$i]            = $buscar[$i]['cdSegmento'];
        $dados['Segmento'][$i]              = $buscar[$i]['Segmento'];
        $dados['idPosicaoLogo'][$i]         = $buscar[$i]['idPosicaoLogo'];
        $dados['PosicaoLogo'][$i]           = $buscar[$i]['PosicaoLogo'];
        $dados['qtdProduzida'][$i]          = $buscar[$i]['qtdProduzida'];
        $dados['qtdPatrocinador'][$i]       = $buscar[$i]['qtdPatrocinador'];
        $dados['qtdOutros'][$i]             = $buscar[$i]['qtdOutros'];
        $dados['qtdVendaNormal'][$i]        = $buscar[$i]['qtdVendaNormal'];
        $dados['qtdVendaPromocional'][$i]   = $buscar[$i]['qtdVendaPromocional'];
        $dados['vlUnitarioNormal'][$i]      = $buscar[$i]['vlUnitarioNormal'];
        $dados['vlUnitarioPromocional'][$i] = $buscar[$i]['vlUnitarioPromocional'];
        $dados['stPrincipal'][$i]           = $buscar[$i]['stPrincipal'];
        $dados['tpAcao'][$i]                = $buscar[$i]['tpAcao'];
        $dados['tpPlanoDistribuicao'][$i]   = $buscar[$i]['tpPlanoDistribuicao'];
        $dados['dtPlanoDistribuicao'][$i]   = $buscar[$i]['dtPlanoDistribuicao'];
        $dados['hrPlanoDistribuicao'][$i]   = $buscar[$i]['hrPlanoDistribuicao'];
        $dados['dsJustificativa'][$i]       = $buscar[$i]['dsJustificativa'];
        $dados['idPedidoAlteracao'][$i]     = $buscar[$i]['idPedidoAlteracao'];
        $dados['dtSolicitacao'][$i]         = $buscar[$i]['dtSolicitacao'];
        $dados['dadosTecnico'][$i]          = $buscarTecnico;
        endfor;

        $this->view->dados = $dados; // manda as informa��es para a vis�o
    } // fecha m�todo historicoProdutosAction()


    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o da ficha t�cnica
     * @access public
     * @param void
     * @return void
     */
    public function historicoFichaTecnicaAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // objetos
        $this->tbProposta                 = new tbProposta();
        $this->tbPedidoAltProjetoXArquivo = new tbPedidoAltProjetoXArquivo();

        // busca os dados do hist�rico
        $where = array(
            'p.IdPRONAC = ?'                           => $this->_idPronac
            ,'p.siVerificacao IN (?)'                  => array(1, 2)
            ,'j.tpAlteracaoProjeto IN (?)'             => array(3)
            ,'a.tpAlteracaoProjeto IN (?)'             => array(3)
            ,'a.stAvaliacaoItemPedidoAlteracao IN (?)' => array('AP', 'IN'));
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbProposta->historicoReadequacao($where, $order);

        // varre os dados para anexar os arquivos
        $dados = array();
        $cont  = 0;
        for ($i = 0; $i < count($buscar); $i++) :
            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $buscar[$i]['idPedidoAlteracao']
                ,'x.tpAlteracaoProjeto IN (?)' => array(3) // ficha t�cnica
            );
        $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
        $cont++;

        $dados['item'][$i]               = $cont . '&ordf;';
        $dados['dsFichaTecnica'][$i]     = utf8_encode($buscar[$i]['dsFichaTecnica']);
        $dados['idPedidoAlteracao'][$i]  = $buscar[$i]['idPedidoAlteracao'];
        $dados['idSolicitante'][$i]      = $buscar[$i]['idSolicitante'];
        $dados['dtSolicitacao'][$i]      = $buscar[$i]['dtSolicitacao'];
        $dados['hrSolicitacao'][$i]      = $buscar[$i]['hrSolicitacao'];
        $dados['dsProponente'][$i]       = $buscar[$i]['dsProponente'];
        $dados['idAgenteAvaliador'][$i]  = $buscar[$i]['idAgenteAvaliador'];
        $dados['dtInicioAvaliacao'][$i]  = $buscar[$i]['dtInicioAvaliacao'];
        $dados['dtFimAvaliacao'][$i]     = $buscar[$i]['dtFimAvaliacao'];
        $dados['stAvaliacao'][$i]        = $buscar[$i]['stAvaliacao'];
        $dados['dsAvaliacao'][$i]        = $buscar[$i]['dsAvaliacao'];
        $dados['Arquivos'][$i]           = $buscarArquivo;
        endfor;

        $this->view->dados = $dados; // manda as informa��es para a vis�o
    } // fecha m�todo historicoFichaTecnicaAction()



    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o de locais de realiza��o
     * @access public
     * @param void
     * @return void
     */
    public function historicoLocalRealizacaoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // objetos
        $this->tbAbrangencia              = new tbAbrangencia();
        $this->tbPedidoAltProjetoXArquivo = new tbPedidoAltProjetoXArquivo();

        // busca os dados do hist�rico
        $where = array(
            'p.IdPRONAC = ?'                           => $this->_idPronac
            ,'p.siVerificacao IN (?)'                  => array(1, 2)
            ,'j.tpAlteracaoProjeto IN (?)'             => array(4)
            ,'a.tpAlteracaoProjeto IN (?)'             => array(4)
            ,'a.stAvaliacaoItemPedidoAlteracao IN (?)' => array('AP', 'IN'));
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbAbrangencia->historicoReadequacao($where, $order);

        // varre os dados para anexar os arquivos
        $dados       = array();
        $cachePedido = array();
        $cont        = 0;
        for ($i = 0; $i < count($buscar); $i++) :
            // busca os arquivos da solicita��o de readequa��o
            $buscarArquivo = array();
        $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $buscar[$i]['idPedidoAlteracao']
                ,'x.tpAlteracaoProjeto IN (?)' => array(4) // local de realiza��o
            );

        $cacheItem = false;
        if (!in_array($buscar[$i]['idPedidoAlteracao'], $cachePedido)) : // verifica se faz parte do mesmo pedido
                $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
        $cachePedido[] = $buscar[$i]['idPedidoAlteracao'];
        $cacheItem     = true;
        $cont++;
        endif;

        $dados['item'][$i]                = ($cacheItem) ? $cont . '&ordf;' : '';
        $dados['idAbrangencia'][$i]       = $buscar[$i]['idAbrangencia'];
        $dados['idAbrangenciaAntiga'][$i] = $buscar[$i]['idAbrangenciaAntiga'];
        $dados['idPais'][$i]              = $buscar[$i]['idPais'];
        $dados['Pais'][$i]                = utf8_encode($buscar[$i]['Pais']);
        $dados['idUF'][$i]                = $buscar[$i]['idUF'];
        $dados['UF'][$i]                  = utf8_encode($buscar[$i]['UF']);
        $dados['idMunicipioIBGE'][$i]     = $buscar[$i]['idMunicipioIBGE'];
        $dados['Municipio'][$i]           = utf8_encode($buscar[$i]['Municipio']);
        $dados['tpAcao'][$i]              = $buscar[$i]['tpAcao'];
        $dados['idPedidoAlteracao'][$i]   = $buscar[$i]['idPedidoAlteracao'];
        $dados['idSolicitante'][$i]       = $buscar[$i]['idSolicitante'];
        $dados['dtSolicitacao'][$i]       = $buscar[$i]['dtSolicitacao'];
        $dados['hrSolicitacao'][$i]       = $buscar[$i]['hrSolicitacao'];
        $dados['dsProponente'][$i]        = utf8_encode($buscar[$i]['dsProponente']);
        $dados['tpAlteracaoProjeto'][$i]  = $buscar[$i]['tpAlteracaoProjeto'] == 1 ? 'Nome do Proponente' : 'Troca de Agente';
        $dados['idAgenteAvaliador'][$i]   = $buscar[$i]['idAgenteAvaliador'];
        $dados['dtInicioAvaliacao'][$i]   = $buscar[$i]['dtInicioAvaliacao'];
        $dados['dtFimAvaliacao'][$i]      = $buscar[$i]['dtFimAvaliacao'];
        $dados['stAvaliacao'][$i]         = $buscar[$i]['stAvaliacao'];
        $dados['dsAvaliacao'][$i]         = $buscar[$i]['dsAvaliacao'];
        $dados['idAvaliacao'][$i]         = $buscar[$i]['idAvaliacao'];
        $dados['stAvaliacaoItem'][$i]     = trim($buscar[$i]['stAvaliacaoItem']);
        $dados['dsAvaliacaoItem'][$i]     = utf8_encode($buscar[$i]['dsAvaliacaoItem']);
        $dados['dsExclusao'][$i]          = utf8_encode($buscar[$i]['dsExclusao']);
        $dados['Arquivos'][$i]            = $buscarArquivo;
        endfor;

        $this->view->dados = $dados; // manda as informa��es para a vis�o
    } // fecha m�todo historicoLocalRealizacaoAction()



    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o do nome do projeto
     * @access public
     * @param void
     * @return void
     */
    public function historicoNomeProjetoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // objetos
        $this->tbProposta                 = new tbProposta();
        $this->tbPedidoAltProjetoXArquivo = new tbPedidoAltProjetoXArquivo();

        // busca os dados do hist�rico
        $where = array(
            'p.IdPRONAC = ?'                           => $this->_idPronac
            ,'p.siVerificacao IN (?)'                  => array(1, 2)
            ,'j.tpAlteracaoProjeto IN (?)'             => array(5)
            ,'a.tpAlteracaoProjeto IN (?)'             => array(5)
            ,'a.stAvaliacaoItemPedidoAlteracao IN (?)' => array('AP', 'IN'));
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbProposta->historicoReadequacao($where, $order);

        // varre os dados para anexar os arquivos
        $dados = array();
        $cont  = 0;
        for ($i = 0; $i < count($buscar); $i++) :
            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $buscar[$i]['idPedidoAlteracao']
                ,'x.tpAlteracaoProjeto IN (?)' => array(5) // nome do projeto
            );
        $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
        $cont++;

        $dados['item'][$i]               = $cont . '&ordf;';
        $dados['nmProjeto'][$i]          = utf8_encode($buscar[$i]['nmProjeto']);
        $dados['idPedidoAlteracao'][$i]  = $buscar[$i]['idPedidoAlteracao'];
        $dados['idSolicitante'][$i]      = $buscar[$i]['idSolicitante'];
        $dados['dtSolicitacao'][$i]      = $buscar[$i]['dtSolicitacao'];
        $dados['hrSolicitacao'][$i]      = $buscar[$i]['hrSolicitacao'];
        $dados['dsProponente'][$i]       = $buscar[$i]['dsProponente'];
        $dados['idAgenteAvaliador'][$i]  = $buscar[$i]['idAgenteAvaliador'];
        $dados['dtInicioAvaliacao'][$i]  = $buscar[$i]['dtInicioAvaliacao'];
        $dados['dtFimAvaliacao'][$i]     = $buscar[$i]['dtFimAvaliacao'];
        $dados['stAvaliacao'][$i]        = $buscar[$i]['stAvaliacao'];
        $dados['dsAvaliacao'][$i]        = $buscar[$i]['dsAvaliacao'];
        $dados['Arquivos'][$i]           = $buscarArquivo;
        endfor;

        $this->view->dados = $dados; // manda as informa��es para a vis�o
    } // fecha m�todo historicoNomeProjetoAction()



    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o do prazo de execu��o
     * @access public
     * @param void
     * @return void
     */
    public function historicoPrazoExecucaoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // objetos
        $this->tbProrrogacaoPrazo         = new tbProrrogacaoPrazo();
        $this->tbPedidoAltProjetoXArquivo = new tbPedidoAltProjetoXArquivo();

        // busca os dados do hist�rico
        $where = array(
            'p.IdPRONAC = ?'                           => $this->_idPronac
            ,'p.siVerificacao IN (?)'                  => array(1, 2)
            ,'j.tpAlteracaoProjeto IN (?)'             => array(9)
            ,'a.tpAlteracaoProjeto IN (?)'             => array(9)
            ,'a.stAvaliacaoItemPedidoAlteracao IN (?)' => array('AP', 'IN')
            ,'h.tpProrrogacao = ?'                     => 'E'); // execu��o
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbProrrogacaoPrazo->historicoReadequacao($where, $order);

        // varre os dados para anexar os arquivos
        $dados = array();
        $cont  = 0;
        for ($i = 0; $i < count($buscar); $i++) :
            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $buscar[$i]['idPedidoAlteracao']
                ,'x.tpAlteracaoProjeto IN (?)' => array(9) // prazo execu��o
            );
        $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
        $cont++;

        $dados['item'][$i]               = $cont . '&ordf;';
        $dados['dtInicioNovoPrazo'][$i]  = $buscar[$i]['dtInicioNovoPrazo'];
        $dados['dtFimNovoPrazo'][$i]     = $buscar[$i]['dtFimNovoPrazo'];
        $dados['idPedidoAlteracao'][$i]  = $buscar[$i]['idPedidoAlteracao'];
        $dados['idSolicitante'][$i]      = $buscar[$i]['idSolicitante'];
        $dados['dtSolicitacao'][$i]      = $buscar[$i]['dtSolicitacao'];
        $dados['hrSolicitacao'][$i]      = $buscar[$i]['hrSolicitacao'];
        $dados['dsProponente'][$i]       = $buscar[$i]['dsProponente'];
        $dados['idAgenteAvaliador'][$i]  = $buscar[$i]['idAgenteAvaliador'];
        $dados['dtInicioAvaliacao'][$i]  = $buscar[$i]['dtInicioAvaliacao'];
        $dados['dtFimAvaliacao'][$i]     = $buscar[$i]['dtFimAvaliacao'];
        $dados['stAvaliacao'][$i]        = $buscar[$i]['stAvaliacao'];
        $dados['dsAvaliacao'][$i]        = $buscar[$i]['dsAvaliacao'];
        $dados['Arquivos'][$i]           = $buscarArquivo;
        endfor;

        $this->view->dados = $dados; // manda as informa��es para a vis�o
    } // fecha m�todo historicoPrazoExecucaoAction()



    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o do prazo de capta��o
     * @access public
     * @param void
     * @return void
     */
    public function historicoPrazoCaptacaoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // objetos
        $this->tbProrrogacaoPrazo         = new tbProrrogacaoPrazo();
        $this->tbPedidoAltProjetoXArquivo = new tbPedidoAltProjetoXArquivo();

        // busca os dados do hist�rico
        $where = array(
            'p.IdPRONAC = ?'                           => $this->_idPronac
            ,'p.siVerificacao IN (?)'                  => array(1, 2)
            ,'j.tpAlteracaoProjeto IN (?)'             => array(8)
            ,'a.tpAlteracaoProjeto IN (?)'             => array(8)
            ,'a.stAvaliacaoItemPedidoAlteracao IN (?)' => array('AP', 'IN')
            ,'h.tpProrrogacao = ?'                     => 'C'); // capta��o
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbProrrogacaoPrazo->historicoReadequacao($where, $order);

        // varre os dados para anexar os arquivos
        $dados = array();
        $cont  = 0;
        for ($i = 0; $i < count($buscar); $i++) :
            // busca os arquivos da solicita��o de readequa��o
            $whereArquivo = array(
                'x.idPedidoAlteracao = ?'      => $buscar[$i]['idPedidoAlteracao']
                ,'x.tpAlteracaoProjeto IN (?)' => array(8) // prazo capta��o
            );
        $buscarArquivo = $this->tbPedidoAltProjetoXArquivo->buscarArquivos($whereArquivo);
        $cont++;

        $dados['item'][$i]               = $cont . '&ordf;';
        $dados['dtInicioNovoPrazo'][$i]  = $buscar[$i]['dtInicioNovoPrazo'];
        $dados['dtFimNovoPrazo'][$i]     = $buscar[$i]['dtFimNovoPrazo'];
        $dados['idPedidoAlteracao'][$i]  = $buscar[$i]['idPedidoAlteracao'];
        $dados['idSolicitante'][$i]      = $buscar[$i]['idSolicitante'];
        $dados['dtSolicitacao'][$i]      = $buscar[$i]['dtSolicitacao'];
        $dados['hrSolicitacao'][$i]      = $buscar[$i]['hrSolicitacao'];
        $dados['dsProponente'][$i]       = $buscar[$i]['dsProponente'];
        $dados['idAgenteAvaliador'][$i]  = $buscar[$i]['idAgenteAvaliador'];
        $dados['dtInicioAvaliacao'][$i]  = $buscar[$i]['dtInicioAvaliacao'];
        $dados['dtFimAvaliacao'][$i]     = $buscar[$i]['dtFimAvaliacao'];
        $dados['stAvaliacao'][$i]        = $buscar[$i]['stAvaliacao'];
        $dados['dsAvaliacao'][$i]        = $buscar[$i]['dsAvaliacao'];
        $dados['Arquivos'][$i]           = $buscarArquivo;
        endfor;

        $this->view->dados = $dados; // manda as informa��es para a vis�o
    } // fecha m�todo historicoPrazoCaptacaoAction()



    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o da proposta pedagogica
     * @access public
     * @param void
     * @return void
     */
    public function historicoPropostaPedagogicaAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // objetos
        $this->tbProposta                 = new tbProposta();
        $this->tbPedidoAltProjetoXArquivo = new tbPedidoAltProjetoXArquivo();

        // busca os dados do hist�rico
        $where = array(
            'p.IdPRONAC = ?'                           => $this->_idPronac
            ,'p.siVerificacao IN (?)'                  => array(1, 2)
            ,'j.tpAlteracaoProjeto IN (?)'             => array(6)
            ,'a.tpAlteracaoProjeto IN (?)'             => array(6)
            ,'a.stAvaliacaoItemPedidoAlteracao IN (?)' => array('AP', 'IN'));
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbProposta->historicoReadequacao($where, $order);


        // varre os dados para anexar os arquivos
        $dados = array();
        $cont  = 0;
        for ($i = 0; $i < count($buscar); $i++) :
            $cont++;

        $dados['item'][$i]                   = $cont . '&ordf;';
        $dados['dsEspecificacaoTecnica'][$i] = utf8_encode($buscar[$i]['dsEspecificacaoTecnica']);
        $dados['idPedidoAlteracao'][$i]      = $buscar[$i]['idPedidoAlteracao'];
        $dados['idSolicitante'][$i]          = $buscar[$i]['idSolicitante'];
        $dados['dtSolicitacao'][$i]          = $buscar[$i]['dtSolicitacao'];
        $dados['hrSolicitacao'][$i]          = $buscar[$i]['hrSolicitacao'];
        $dados['dsProponente'][$i]           = utf8_encode($buscar[$i]['dsProponente']);
        $dados['idAgenteAvaliador'][$i]      = $buscar[$i]['idAgenteAvaliador'];
        $dados['dtInicioAvaliacao'][$i]      = $buscar[$i]['dtInicioAvaliacao'];
        $dados['dtFimAvaliacao'][$i]         = $buscar[$i]['dtFimAvaliacao'];
        $dados['stAvaliacao'][$i]            = $buscar[$i]['stAvaliacao'];
        $dados['dsAvaliacao'][$i]            = utf8_encode($buscar[$i]['dsAvaliacao']);
        $dados['dsJustificativa'][$i]        = utf8_encode($buscar[$i]['dsJustificativa']);
        $dados['dsEstrategiaExecucao'][$i]   = utf8_encode($buscar[$i]['dsEstrategiaExecucao']);
        endfor;

        $this->view->dados = $dados; // manda as informa��es para a vis�o
    } // fecha m�todo historicoPropostaPedagogicaAction()



    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o de custos por produtos
     * @access public
     * @param void
     * @return void
     */
    public function historicoCustoProdutosAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // objetos
        $this->tbPlanilhaAprovacao = new PlanilhaAprovacao();

        // busca os dados do hist�rico
        $where = array(
            'p.IdPRONAC = ?'                           => $this->_idPronac
            ,'p.siVerificacao IN (?)'                  => array(1, 2)
            ,'j.tpAlteracaoProjeto IN (?)'             => array(10)
            ,'a.tpAlteracaoProjeto IN (?)'             => array(10)
            ,'a.stAvaliacaoItemPedidoAlteracao IN (?)' => array('AP', 'IN', 'EA')
            ,'h.idProduto = ?'                         => 0);
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbPlanilhaAprovacao->historicoReadequacao($where, $order);

        // monta a planilha solicitada
        $plan = array();
        $cont = 0;
        foreach ($buscar as $r) :
            $produto = empty($r->Produto) ? 'Administra&ccedil;&atilde;o do Projeto' : $r->Produto;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaAprovacao'] = $r->idPlanilhaAprovacao;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrFonteRecurso']      = $r->nrFonteRecurso;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['FonteRecurso']        = $r->FonteRecurso;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idProduto']           = $r->idProduto;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Produto']             = $r->Produto;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idEtapa']             = $r->idEtapa;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Etapa']               = $r->Etapa;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['UF']                  = $r->UF;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Cidade']              = $r->Cidade;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaItem']      = $r->idPlanilhaItem;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Item']                = $r->Item;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idUnidade']           = $r->idUnidade;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Unidade']             = $r->Unidade;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtItem']              = (int) $r->qtItem;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrOcorrencia']        = (int) $r->nrOcorrencia;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlUnitario']          = $r->vlUnitario;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlTotal']             = $r->vlTotal;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtDias']              = $r->qtDias;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['dsJustificativa']     = $r->dsJustificativa;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['tpAcao']              = $r->tpAcao;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idAvaliacaoItem']     = $r->idAvaliacaoItem;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['stAvaliacaoItem']     = $r->stAvaliacaoItem;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['dsAvaliacaoItem']     = $r->dsAvaliacaoItem;
        $cont++;
        endforeach;

        $this->view->planSR = $plan; // manda as informa��es para a vis�o
    } // fecha m�todo historicoCustoProdutosAction()



    /**
     * M�todo para buscar o hist�rico com as solicita��es de altera��o de custos administrativos
     * @access public
     * @param void
     * @return void
     */
    public function historicoCustoAdministrativoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        // objetos
        $this->tbPlanilhaAprovacao = new PlanilhaAprovacao();

        // busca os dados do hist�rico
        $where = array(
            'p.IdPRONAC = ?'                           => $this->_idPronac
            ,'p.siVerificacao IN (?)'                  => array(1, 2)
            ,'j.tpAlteracaoProjeto IN (?)'             => array(10)
            ,'a.tpAlteracaoProjeto IN (?)'             => array(10)
            ,'a.stAvaliacaoItemPedidoAlteracao IN (?)' => array('AP', 'IN')
            ,'h.idProduto = ?'                         => 0);
        $order = array('p.dtSolicitacao ASC');
        $buscar = $this->tbPlanilhaAprovacao->historicoReadequacao($where, $order);

        // monta a planilha solicitada
        $plan = array();
        $cont = 0;
        foreach ($buscar as $r) :
            $produto = empty($r->Produto) ? 'Administra&ccedil;&atilde;o do Projeto' : $r->Produto;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaAprovacao'] = $r->idPlanilhaAprovacao;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrFonteRecurso']      = $r->nrFonteRecurso;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['FonteRecurso']        = $r->FonteRecurso;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idProduto']           = $r->idProduto;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Produto']             = $r->Produto;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idEtapa']             = $r->idEtapa;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Etapa']               = $r->Etapa;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['UF']                  = $r->UF;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Cidade']              = $r->Cidade;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaItem']      = $r->idPlanilhaItem;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Item']                = $r->Item;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idUnidade']           = $r->idUnidade;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Unidade']             = $r->Unidade;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtItem']              = (int) $r->qtItem;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrOcorrencia']        = (int) $r->nrOcorrencia;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlUnitario']          = $r->vlUnitario;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlTotal']             = $r->vlTotal;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtDias']              = $r->qtDias;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['dsJustificativa']     = $r->dsJustificativa;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['tpAcao']              = $r->tpAcao;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idAvaliacaoItem']     = $r->idAvaliacaoItem;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['stAvaliacaoItem']     = $r->stAvaliacaoItem;
        $plan[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['dsAvaliacaoItem']     = $r->dsAvaliacaoItem;
        $cont++;
        endforeach;

        $this->view->planSR = $plan; // manda as informa��es para a vis�o
    } // fecha m�todo historicoCustoAdministrativoAction()



    /**
     * M�todo para popular o formul�rio de cadastro de itens
     * @access public
     * @param void
     * @return void
     */
    public function popFormCustoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe os dados do formul�rio
        $post = Zend_Registry::get('post');

        if (isset($post->popularForm) && $post->popularForm == 'S') :

            $idPlanilhaAprovacao = $post->idPlanilhaAprovacao;

        // busca as informa��es do item da planilha
        $this->tbPlanilhaAprovacao = new PlanilhaAprovacao();
        $where = array('PAP.idPlanilhaAprovacao = ?' => $idPlanilhaAprovacao);
        $buscar = $this->tbPlanilhaAprovacao->buscarCustosReadequacao($where);
        foreach ($buscar as $r) :
                $vlJson['idEtapaEProduto']     = $r->idEtapa.':'.$r->idProduto;
        $vlJson['idEtapa']             = $r->idEtapa;
        $vlJson['idUnidade']           = $r->idUnidade;
        $vlJson['qtItem']              = $r->qtItem;
        $vlJson['nrOcorrencia']        = $r->nrOcorrencia;
        $vlJson['vlUnitario']          = $r->vlUnitario;
        $vlJson['vlTotal']             = $r->vlTotal;
        $vlJson['qtDias']              = $r->qtDias;
        $vlJson['nrFonteRecurso']      = $r->nrFonteRecurso;
        $vlJson['idUF']                = $r->idUF;
        $vlJson['idMunicipio']         = $r->idMunicipio;
        $vlJson['dsJustificativa']     = !is_null(utf8_encode($r->dsJustificativa)) ? utf8_encode($r->dsJustificativa) : '';
        $vlJson['idPlanilhaItem']      = $r->idPlanilhaItem;
        $vlJson['idPlanilhaAprovacao'] = $r->idPlanilhaAprovacao;
        $vlJson['tpPlanilha']          = $r->tpPlanilha;
        endforeach;
        $json = json_encode($vlJson);
        echo $json;
        $this->_helper->viewRenderer->setNoRender(true);
        endif;
    } // fecha m�todo popFormCustoAction()



    /**
     * M�todo para popular a combo de itens de custo de acordo com uma etapa
     * @access public
     * @param void
     * @return void
     */
    public function popItensCustoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe os dados do formul�rio
        $post = Zend_Registry::get('post');

        if (isset($post->popularEtapas) && $post->popularEtapas == 'S') :

            $idEtapa = $post->idEtapa;

        // busca os itens de acordo com a etapa
        $this->tbPlanilhaItens = new PlanilhaItens();
        $buscar = $this->tbPlanilhaItens->combo(array('tipp.idPlanilhaEtapa = ?' => $idEtapa), array('tpi.Descricao ASC'));
        $i = 0;
        foreach ($buscar as $r) :
                $vlJson[$i]['id']        = $r->id;
        $vlJson[$i]['descricao'] = utf8_encode($r->descricao);
        $i++;
        endforeach;
        $json = json_encode($vlJson);
        echo $json;
        $this->_helper->viewRenderer->setNoRender(true);
        endif;
    } // fecha m�todo popItensCustoAction()
} // fecha class
