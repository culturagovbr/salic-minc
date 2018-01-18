<?php 

class ManterbloqueiocontaController extends MinC_Controller_Action_Abstract
{
    private $getIdUsuario = 0; // c�digo do usu�rio logado
    private $getIdGrupo   = 0; // c�digo do grupo logado
    private $getIdOrgao   = 0; // c�digo do �rg�o logado
    private $intTamPag    = 10;

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {

        // pega o idAgente do usu�rio logado
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        
        /* ========== IN�CIO PERFIL ==========*/
        // define os grupos que tem acesso
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        //$PermissoesGrupo[] = ; // Coordenador de Avalia��o
        //$PermissoesGrupo[] = 134; // Coordenador de Fiscaliza��o
        //$PermissoesGrupo[] = 124; // T�cnico de Presta��o de Contas
        //$PermissoesGrupo[] = 125; // Coordenador de Presta��o de Contas
        //$PermissoesGrupo[] = 126; // Coordenador - Geral de Presta��o de Contas
        parent::perfil(1, $PermissoesGrupo); // perfil novo salic

        if (isset($auth->getIdentity()->usu_codigo)) { // autenticacao novo salic
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario['idAgente'] : 0;
        } else { // autenticacao espaco proponente
            $this->getIdUsuario = 0;
        }
        /* ========== FIM PERFIL ==========*/


        /* ========== IN�CIO �RG�O ========== */
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->getIdGrupo = $GrupoAtivo->codGrupo; // id do grupo ativo
        $this->getIdOrgao = $GrupoAtivo->codOrgao; // id do �rg�o ativo

        parent::init();
    }

    // fecha metodo init()
    /**
     * Redireciona para o fluxo inicial do sistema
     * @access public
     * @param void
     * @return void
    */
    public function indexAction()
    {
        $this->_forward("form-pesquisar-conta");
    }

    public function formPesquisarContaAction()
    {
    }
    
    public function formBloquearContaAction()
    {
        $post = Zend_Registry::get('post');
        $pronac = $post->pronac;
        
        $tbProjeto = new Projetos();
        $rsProjeto = $tbProjeto->buscar(array('Anoprojeto+Sequencial=?'=>$pronac))->current();
        
        if (!empty($rsProjeto)) {
            $tbContaBloqueada = new tbContaBloqueada();
            $where = array();
            $where['IdPRONAC = ?'] = $rsProjeto->IdPRONAC;
            $where['stEstado = ?'] = 1; //registro ativo
            $where['tpAcao <> ?'] = '0'; //(0 - conta desbloqueada)
            $where['tpIdentificacaoConta = ?'] = 1; //(1 - capta��o)
            $rsCBCaptacao = $tbContaBloqueada->buscar($where)->current();
            xd($rsCBCaptacao);
            
            $where['tpIdentificacaoConta = ?'] = 2; //(2-movimento)
            $rsCBMovimento = $tbContaBloqueada->buscar($where)->current();
            
            $this->view->projeto  = $rsProjeto;
            $this->view->contaCapatacaoBloqueada = $rsCBCaptacao;
            $this->view->contaMovimentoBloqueada = $rsCBMovimento;
        } else {
            parent::message("Nenhum projeto encontrado com o Pronac informado", "/manterbloqueioconta/form-pesquisar-conta", "ALERT");
        }
    }

    public function gravarBloqueioContaAction()
    {
        $post = Zend_Registry::get('post');
        $idPronac       = $post->idPronac;
        $tipoConta      = $post->tipoConta;
        $justificativa  = $post->justificativa;
        
        if (empty($idPronac) || empty($tipoConta) || empty($justificativa)) {
            parent::message("Dados obrigat&oacute;rios n&atilde;o informados!", "manterbloqueioconta/listar-contas-desbloqueio", "ALERT");
        }
        
        $tbContaBloqueada = new tbContaBloqueada();
        try {
            //============== INATIVA REGISTROS ===============
            $where = array();
            $where['IdPRONAC = ?'] = $idPronac;
            $where['stEstado = ?'] = 1; //registro ativo
            if ($tipoConta != "3") { //3 = ambas as contas
                $where['tpIdentificacaoConta = ?'] = $tipoConta;
            }
            $rsContaBloqueada = $tbContaBloqueada->buscar($where);
            foreach ($rsContaBloqueada as $conta) {
                $conta->stEstado = 0; //passa registro ativo para historico
                $conta->save();
            }

            //============== FAZ UPLOAD DO ARQUIVO ============
            if (count($_FILES) > 0) {
                $idArquivo = $this->gravarArquivoJudicialBloqueioConta($_FILES);
            }//fecha if FILES

            //============== INSERE BLOQUEIO DE CONTA ==========
            $dados = array();
            $dados['IdPRONAC']          = $idPronac;
            $dados['tpAcao']            = 2; //bloqueio judicial
            $dados['dtAcao']            = new Zend_Db_Expr('GETDATE()');
            $dados['dsJustificativa']   = $justificativa;
            $dados['idUsuario']         = $this->getIdUsuario;
            $dados['stEstado']          = 1;
            //$dados['idArquivo']       = $idArquivo;

            if ($tipoConta == "3") { //3 = ambas as contas
                //bloqueia conta captacao
                $dados['tpIdentificacaoConta'] = 1;
                $tbContaBloqueada->inserir($dados);

                //bloqueia conta movimento
                $dados['tpIdentificacaoConta'] = 2;
                $tbContaBloqueada->inserir($dados);
            } else {
                //bloqueia conta enviada
                $dados['tpIdentificacaoConta'] = $tipoConta;
                $tbContaBloqueada->inserir($dados);
            }
            
            parent::message("Conta(s) bloqueada(s) com sucesso!", "manterbloqueioconta", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao realizar opera&ccedil;&atilde;o. ".$e->getMessage(), "manterbloqueioconta", "ERROR");
        }
    }
    
    public function gravarArquivoJudicialBloqueioConta()
    {
        try {
            $arquivoNome     = $_FILES['arqDecisaoJudicial']['name']; // nome
            $arquivoTemp     = $_FILES['arqDecisaoJudicial']['tmp_name']; // nome tempor�rio
            $arquivoTipo     = $_FILES['arqDecisaoJudicial']['type']; // tipo
            $arquivoTamanho  = $_FILES['arqDecisaoJudicial']['size']; // tamanho

            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash
            }

            //VALIDA TAMANHO ARQUIVO
            if ($arquivoTamanho > 1024 * 1024 * 10) {
                parent::message("O arquivo deve ser menor que 10 MB", "manterbloqueioconta", "ERROR");
            }

            //VALIDA EXTENSAO ARQUIVO
            if (!in_array($arquivoExtensao, explode(',', 'pdf,PDF,doc,docx'))) {
                parent::message("Arquivo com extens&atilde;o Inv&aacute;lida", "manterbloqueioconta", "ERROR");
            }

            // ==================== PERSISTE DADOS DO ARQUIVO =================//
            $dadosArquivo = array(
                    'nmArquivo'         => $arquivoNome,
                    'sgExtensao'        => $arquivoExtensao,
                    'dsTipoPadronizado' => $arquivoTipo,
                    'nrTamanho'         => $arquivoTamanho,
                    'dtEnvio'           => new Zend_Db_Expr('GETDATE()'),
                    'dsHash'            => $arquivoHash,
                    'stAtivo'           => 'A');

            $tbArquivo = new tbArquivo();
            $idArquivo = $tbArquivo->inserir($dadosArquivo);


            // ================== PERSISTE DADOS ARQUIVO - BINARIO ============//
            $dadosBinario = array(
                    'idArquivo' => $idArquivo,
                    'biArquivo' => new Zend_Db_Expr("CONVERT(varbinary(MAX), {$arquivoBinario})"));

            $tbArquivoImagem = new tbArquivoImagem();
            $idArquivoImagem = $tbArquivoImagem->inserir($dadosBinario);

            // ================= PERSISTE DADOS DO DOCUMENTO ==================//
            /*$dadosDoc = array(
                    'idArquivo' => $idArquivo,
                    'idTipoDocumento' => '1',
                    'dsDocumento' => $observacao);

            $tbDocumento = new tbDocumento();
            $idDocumento = $tbDocumento->inserir($dadosDoc);
            $idDocumento = $idDocumento['idDocumento'];*/
        
            return $idArquivo;
            $this->_helper->viewRenderer->setNoRender(true);
        } catch (Exception $e) {
            parent::message("Erro ao enviar o arquivo anexado.", "manterbloqueioconta", "ERROR");
        }
    }
    
    public function listarContasDesbloqueioAction()
    {
        $tbContaBloqueada = new tbContaBloqueada();
        $arrBusca = array();
        $arrBusca['cb.stEstado = ?'] = 1; //registro ativo
        $arrBusca['cb.tpIdentificacaoConta in (select TOP 1 max(tpIdentificacaoConta) from SAC..tbcontabloqueada where IdPRONAC = pr.IdPRONAC and stestado=1)'] = "(?)"; //pega apenas um registro por pronac
        $arrBusca['cb.tpAcao = ?']   = 1; //bloqueio sistemico
        $rsBloqueioSistemico = $tbContaBloqueada->buscarContasDesbloqueioSistemico($arrBusca);
        
        $arrBusca['cb.tpAcao = ?'] = 2; //bloqueio judicial
        $rsBloqueioJudicial  = $tbContaBloqueada->buscarContasDesbloqueioJudicial($arrBusca);
        
        $this->view->bloqueioSisitemico = $rsBloqueioSistemico;
        $this->view->bloqueioJudicial   = $rsBloqueioJudicial;
    }
    
    public function formDesbloquearContaAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        
        $post = Zend_Registry::get('post');
        $idPronac = $post->idPronac;
        $tipoDesbloqueio = $post->tipoDesbloqueio;
        
        $tbProjeto = new Projetos();
        $rsProjeto = $tbProjeto->buscar(array('IdPRONAC=?'=>$idPronac))->current();
        
        if (!empty($rsProjeto)) {
            $tbContaBloqueada = new tbContaBloqueada();
            $where = array();
            $where['IdPRONAC = ?'] = $rsProjeto->IdPRONAC;
            $where['stEstado = ?'] = 1; //registro ativo
            $where['tpAcao = ?'] = $tipoDesbloqueio; //(1- bloqueio sistemico / 2- bloqueio judicial)
            $where['tpIdentificacaoConta = ?'] = 1; //(1 - capta��o)
            $rsCBCaptacao = $tbContaBloqueada->buscar($where)->current();
            
            $where['tpIdentificacaoConta = ?'] = 2; //(2-movimento)
            $rsCBMovimento = $tbContaBloqueada->buscar($where)->current();
            
            $this->view->projeto  = $rsProjeto;
            $this->view->contaCapatacaoBloqueada = $rsCBCaptacao;
            $this->view->contaMovimentoBloqueada = $rsCBMovimento;
            $this->view->tipoDesbloqueio = $tipoDesbloqueio;
        } else {
            parent::message("Nenhum projeto encontrado com o Pronac informado", "/manterbloqueioconta/form-pesquisar-conta", "ALERT");
        }
    }
    
    public function gravarDesbloqueioContaAction()
    {
        $post = Zend_Registry::get('post');
        $idPronac       = $post->idPronac;
        $tipoConta      = $post->tipoConta;
        $justificativa  = $post->justificativa;
        
        if (empty($idPronac) || empty($tipoConta) || empty($justificativa)) {
            parent::message("Dados obrigat&oacute;rios n&atilde;o informados!", "manterbloqueioconta/listar-contas-desbloqueio", "ALERT");
        }
        
        $tbContaBloqueada = new tbContaBloqueada();
        try {
            //============== INATIVA REGISTROS ===============
            $where = array();
            $where['IdPRONAC = ?'] = $idPronac;
            $where['stEstado = ?'] = 1; //registro ativo
            if ($tipoConta != "3") { //3 = ambas as contas
                $where['tpIdentificacaoConta = ?'] = $tipoConta;
            }
            $rsContaBloqueada = $tbContaBloqueada->buscar($where);
            foreach ($rsContaBloqueada as $conta) {
                $conta->stEstado = 0; //passa registro ativo para historico
                $conta->save();
            }

            //============== FAZ UPLOAD DO ARQUIVO ============
            if (count($_FILES) > 0) {
                $idArquivo = $this->gravarArquivoJudicialBloqueioConta($_FILES);
            }//fecha if FILES

            //============== INSERE DESBLOQUEIO DE CONTA ==========
            $dados = array();
            $dados['IdPRONAC']          = $idPronac;
            $dados['tpAcao']            = 0; //desbloqueio
            $dados['dtAcao']            = new Zend_Db_Expr('GETDATE()');
            $dados['dsJustificativa']   = $justificativa;
            $dados['idUsuario']         = $this->getIdUsuario;
            $dados['stEstado']  = 1;
            //$dados['idArquivo']       = $idArquivo;

            if ($tipoConta == "3") { //3 = ambas as contas
                //desbloqueia conta captacao
                $dados['tpIdentificacaoConta'] = 1;
                $tbContaBloqueada->inserir($dados);

                //desbloqueia conta movimento
                $dados['tpIdentificacaoConta'] = 2;
                $tbContaBloqueada->inserir($dados);
            } else {
                //desbloqueia conta enviada
                $dados['tpIdentificacaoConta'] = $tipoConta;
                $tbContaBloqueada->inserir($dados);
            }
            
            parent::message("Conta(s) desbloqueada(s) com sucesso!", "manterbloqueioconta/listar-contas-desbloqueio", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao realizar opera&ccedil;&atilde;o. ".$e->getMessage(), "manterbloqueioconta/listar-contas-desbloqueio", "ERROR");
        }
    }
    
    public function listarContasBloqueadasAction()
    {
        $tbContaBloqueada = new tbContaBloqueada();
        //BUSCA AS CONTAS QUE PODEM SER DESBLOQUEADAS
        $arrBusca = array();
        $arrBusca['a.stEstado = ?'] = 1; //registro ativo
        $arrBusca['a.tpIdentificacaoConta in (select TOP 1 max(tpIdentificacaoConta) from SAC..tbcontabloqueada where IdPRONAC = b.IdPRONAC and stestado=1)'] = "(?)"; //pega apenas um registro por pronac
        $arrBusca['a.tpAcao = ?']   = 1; //bloqueio sistemico
        $rsContasDesbloqueioSistemico = $tbContaBloqueada->queryContasDesbloqueioSistemico($arrBusca);
        
        $arrIdsContasDesbloqueio = array();
        foreach ($rsContasDesbloqueioSistemico as $projeto) {
            $arrIdsContasDesbloqueio[] = $projeto->idContaBloqueada;
        }
        
        //BUSCA AS CONTAS QUE NAO PODEM SER DESBLOQUEADAS
        $arrBusca = array();
        if (count($arrIdsContasDesbloqueio)>0) {
            $arrBusca['cb.idContaBloqueada NOT IN (?)'] = $arrIdsContasDesbloqueio;
        } //id das contas que podem ser desbloqueadas
        $arrBusca['cb.stEstado = ?']         = 1; //registro ativo
        $arrBusca['cb.tpIdentificacaoConta in (select TOP 1 max(tpIdentificacaoConta) from SAC..tbcontabloqueada where IdPRONAC = pr.IdPRONAC and stestado=1)'] = "(?)"; //pega apenas um registro por pronac
        $arrBusca['cb.tpAcao = ?']           = 1; //bloqueio sistemico
        $rsContasBloqueadas = $tbContaBloqueada->buscarContasBloqueadas($arrBusca);
        $this->view->contasBloqueadas = $rsContasBloqueadas;
    }
    
    public function imprimirAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $this->view->titulo = $_POST['titulo'];
        $this->view->html = $_POST['html'];
    }
}
