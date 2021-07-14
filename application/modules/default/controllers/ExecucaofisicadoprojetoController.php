<?php

class ExecucaofisicadoprojetoController extends MinC_Controller_Action_Abstract
{
    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da página

        parent::init();
    }

    /**
     * Redireciona para o fluxo inicial do sistema
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        // despacha para buscarpronac.phtml
        $this->forward("buscardocumentos");
    }



    /**
     * M�todo com o formul�rio para buscar o PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function buscarpronacAction()
    {
        // autentica��o scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe o pronac via post
            $post   = Zend_Registry::get('post');
            $pronac = $post->pronac;

            try {
                // verifica se o pronac veio vazio
                if (empty($pronac)) {
                    throw new Exception("Por favor, informe o PRONAC!");
                }
                // busca o pronac no banco
                else {
                    // integra��o MODELO e VIS�O

                    // busca de acordo com o pronac no banco
                    $resultado = ProjetoDAO::buscar($pronac);

                    // caso o PRONAC Não esteja cadastrado
                    if (!$resultado) {
                        throw new Exception("Registro Não encontrado!");
                    }
                    // caso o PRONAC esteja cadastrado,
                    // vai para a página de busca dos documentos (comprovantes) do pronac
                    else {
                        // redireciona o pronac para a página com seus documentos (comprovantes)
                        $this->redirect("execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac);
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "execucaofisicadoprojeto/buscarpronac", "ERROR");
            }
        } // fecha if
    } // fecha m�todo buscarpronacAction()



    /**
     * M�todo para buscar os documentos (comprovantes) do PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function buscardocumentosAction()
    {
        // autentica��o scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // recebe o pronac via get
        $get    = Zend_Registry::get('get');
        $pronac = $get->pronac;

        try {
            // verifica se o pronac veio vazio
            if (empty($pronac)) {
                throw new Exception("Por favor, informe o PRONAC!");
            }
            // valida o n�mero do pronac
            elseif (strlen($pronac) > 20) {
                throw new Exception("O N� do PRONAC � inv�lido!");
            } else {
                // integra��o MODELO e VIS�O

                // busca de acordo com o pronac no banco
                $resultPronac = ProjetoDAO::buscar($pronac);

                // caso o PRONAC Não esteja cadastrado
                if (!$resultPronac) {
                    throw new Exception("Registro Não encontrado!");
                }
                // caso o PRONAC esteja cadastrado, vai para a página de busca
                // dos seus documentos (comprovantes)
                else {
                    // manda o pronac para a vis�o
                    $this->view->buscarPronac = $resultPronac;

                    // pega o id do pronac
                    $idPronac = $resultPronac[0]->IdPRONAC;

                    // busca os documentos (comprovantes) do pronac
                    // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PAR�METRO ==========
                    $resultComprovantes = ComprovanteExecucaoFisicaDAO::buscarDocumentos($idPronac);

                    // caso Não existam comprovantes cadastrados
                    if (!$resultComprovantes) {
                        $this->view->message      = "Nenhum comprovante cadastrado para o PRONAC N� " . $pronac . "!";
                        $this->view->message_type = "ALERT";
                    } else {
                        // busca o hist�rico dos comprovantes
                        for ($i = 0; $i < sizeof($resultComprovantes); $i++) :
                            // adiciona os comprovantes no novo array
                            $arrayComprovantes[$i] = array('comprovantes' => $resultComprovantes[$i]);

                        // hist�rico
                        $resultHistorico = ComprovanteExecucaoFisicaDAO::buscarHistorico(
                                $resultComprovantes[$i]->idComprovante,
                                $resultComprovantes[$i]->idComprovanteAnterior
                            );
                        // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PAR�METRO ==========

                        // adiciona o hist�rico no seu respectivo comprovante
                        if (sizeof($resultHistorico) > 0) :
                                array_push($arrayComprovantes[$i], array('historicos' => $resultHistorico));
                        endif;
                        endfor;


                        // ========== INÍCIO PAGINAÇÂO ==========
                        //criando a pagina�ao
                        Zend_Paginator::setDefaultScrollingStyle('Sliding');
                        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
                        $paginator = Zend_Paginator::factory($arrayComprovantes); // dados a serem paginados

                        // página atual e quantidade de �tens por página
                        $currentPage = $this->_getParam('page', 1);
                        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
                        // ========== FIM PAGINAÇÂO ==========


                        // manda os comprovantes e seu hist�rico para a vis�o
                        //$this->view->buscarComprovantes = $arrayComprovantes;
                        $this->view->paginacao          = $paginator;
                        $this->view->qtdComprovantes    = count($resultComprovantes); // quantidade de comprovantes
                    }
                }
            } // fecha else
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "execucaofisicadoprojeto/buscarpronac", "ERROR");
        }
    } // fecha m�todo buscardocumentosAction()



    /**
     * M�todo com o formul�rio para cadastrar documento do PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function cadastrardocumentosAction()
    {
        // autentica��o scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // combo com os tipos de documentos
        $this->view->combotipodocumento = TipoDocumentoDAO::buscar();

        // caso o formul�rio seja enviado via post
        // cadastra o documento
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post          = Zend_Registry::get('post');
            $pronac        = $post->pronac;
            $idPronac 	   = (int) $post->idPronac;
            $tipoDocumento = $post->tipoDocumento;
            $titulo        = $post->titulo;
            $descricao     = $post->descricao;

            // pega as informa��es do arquivo
            $arquivoNome     = $_FILES['arquivo']['name']; // nome
            $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
            $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho
            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                $arquivoHash     = Upload::setHash($arquivoTemp); // hash
            }

            try {
                // integra��o MODELO e VIS�O

                // busca de acordo com o pronac no banco
                $resultado = ProjetoDAO::buscar($pronac);

                // caso o PRONAC Não esteja cadastrado
                if (!$resultado) {
                    parent::message("Registro Não encontrado!", "execucaofisicadoprojeto/buscarpronac", "ERROR");
                }
                // caso o PRONAC esteja cadastrado, vai para a página de busca
                else {
                    $this->view->buscarPronac = $resultado;
                }

                // valida os campos vazios
                if (empty($tipoDocumento)) {
                    throw new Exception("Por favor, informe o tipo de documento!");
                } elseif (empty($titulo) || $titulo == 'Digite o t�tulo do comprovante...') {
                    throw new Exception("Por favor, informe o t�tulo do documento!");
                } elseif (strlen($titulo) < 2 || strlen($titulo) > 100) {
                    throw new Exception("O t�tulo do documento � inv�lido! A quantidade m�nima � de 2 caracteres!");
                } elseif (empty($descricao) || $descricao == 'Digite o texto do comprovante...') {
                    throw new Exception("Por favor, informe a Descrição do documento!");
                } elseif (strlen($descricao) < 20 || strlen($descricao) > 500) {
                    throw new Exception("A Descrição do documento � inv�lida! S�o permitidos entre 20 e 500 caracteres!");
                } elseif (empty($arquivoTemp)) { // nome do arquivo
                    throw new Exception("Por favor, informe o arquivo!");
                } elseif ($arquivoExtensao == 'exe' || $arquivoExtensao == 'bat' ||
                $arquivoTipo == 'application/exe' || $arquivoTipo == 'application/x-exe' ||
                $arquivoTipo == 'application/dos-exe' || strlen($arquivoExtensao) > 5) { // extens�o do arquivo
                    throw new Exception("A extens�o do arquivo � inv�lida!");
                } elseif ($arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                    throw new Exception("O arquivo Não pode ser maior do que 10MB!");
                } elseif (ArquivoDAO::verificarHash($arquivoHash)) { // hash do arquivo
                    throw new Exception("O arquivo enviado j� est� cadastrado na base de dados! Por favor, informe outro!");
                }
                // faz o cadastro no banco de dados
                else {
                    // cadastra dados do arquivo
                    $dadosArquivo = array(
                        'nmArquivo'         => $arquivoNome,
                        'sgExtensao'        => $arquivoExtensao,
                        'dsTipoPadronizado' => $arquivoTipo,
                        'nrTamanho'         => $arquivoTamanho,
                        'dtEnvio'           => new Zend_Db_Expr('GETDATE()'),
                        'dsHash'            => $arquivoHash,
                        'stAtivo'           => 'A');
                    $cadastrarArquivo = ArquivoDAO::cadastrar($dadosArquivo);

                    // pega o id do �ltimo arquivo cadastrado
                    $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
                    $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

                    // cadastra o bin�rio do arquivo
                    $dadosBinario = array(
                        'idArquivo' => $idUltimoArquivo,
                        'biArquivo' => $arquivoBinario);
                    $cadastrarBinario = ArquivoImagemDAO::cadastrar($dadosBinario);


                    // cadastra dados do comprovante
                    $dadosComprovante = array(
                        'idPRONAC'             => $idPronac,
                        'idTipoDocumento'      => $tipoDocumento,
                        'nmComprovante'        => $titulo,
                        'dsComprovante'        => $descricao,
                        'idArquivo'            => $idUltimoArquivo,
                        'idSolicitante'        => 9997, // ===== MUDAR ID =====
                        'dtEnvioComprovante'   => new Zend_Db_Expr('GETDATE()'),
                        'stParecerComprovante' => 'AG',
                        'stComprovante'        => 'A');
                    $cadastrarComprovante = ComprovanteExecucaoFisicaDAO::cadastrar($dadosComprovante);

                    // pega o id do �ltimo comprovante cadastrado
                    $idUltimoComprovante = ComprovanteExecucaoFisicaDAO::buscarIdComprovante();
                    $idUltimoComprovante = (int) $idUltimoComprovante[0]->id;

                    // atualiza o id do comprovante anterior
                    $dadosComprovante = array('idComprovanteAnterior' => $idUltimoComprovante);
                    $alterarComprovante = ComprovanteExecucaoFisicaDAO::alterar($dadosComprovante, $idUltimoComprovante);

                    if ($cadastrarArquivo && $cadastrarComprovante) {
                        parent::message("Cadastro realizado com sucesso!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "CONFIRM");
                    } else {
                        parent::message("Erro ao realizar cadastro!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "ERROR");
                    }
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = "ERROR";
                $this->view->tipoDocumento = $tipoDocumento;
                $this->view->titulo        = $titulo;
                $this->view->descricao     = $descricao;
            }
        }
        // quando a página � aberta
        else {
            // recebe o pronac via get
            $get    = Zend_Registry::get('get');
            $pronac = $get->pronac;

            try {
                // verifica se o pronac veio vazio
                if (empty($pronac)) {
                    throw new Exception("Por favor, informe o PRONAC!");
                } else {
                    // integra��o MODELO e VIS�O

                    // busca o PRONAC no banco
                    $resultado = ProjetoDAO::buscar($pronac);

                    // caso o PRONAC Não esteja cadastrado
                    if (!$resultado) {
                        throw new Exception("Regisitro Não encontrado!");
                    }
                    // caso o PRONAC esteja cadastrado, vai para a página de busca
                    else {
                        $this->view->buscarPronac = $resultado;
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "execucaofisicadoprojeto/buscarpronac", "ERROR");
            }
        } // fecha else
    } // fecha m�todo cadastrardocumentosAction()



    /**
     * M�todo com o formul�rio para alterar documento do PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function alterardocumentosAction()
    {
        // autentica��o scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // combo com os tipos de documentos
        $this->view->combotipodocumento = TipoDocumentoDAO::buscar();

        // caso o formul�rio seja enviado via post
        // altera do documento
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post          = Zend_Registry::get('post');
            $pronac        = $post->pronac;
            $idPronac      = (int) $post->idPronac;
            $doc           = (int) $post->idComprovante;
            $idArquivo     = (int) $post->idArquivo;
            $tipoDocumento = (int) $post->tipoDocumento;
            $titulo        = $post->titulo;
            $descricao     = $post->descricao;

            // pega as informa��es do arquivo
            $arquivoNome     = $_FILES['arquivo']['name']; // nome
            $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
            $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho
            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                $arquivoHash     = Upload::setHash($arquivoTemp); // hash
            }

            try {
                // integra��o MODELO e VIS�O

                // busca o PRONAC no banco
                $resultadoPronac = ProjetoDAO::buscar($pronac);

                // busca o Comprovante de acordo com o id no banco
                $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                // caso o PRONAC ou o Comprovante Não estejam cadastrados
                if (!$resultadoPronac || !$resultadoComprovante) {
                    parent::message("Registro Não encontrado!", "execucaofisicadoprojeto/buscarpronac", "ERROR");
                }
                // caso o PRONAC e o Comprovante estejam cadastrados, vai para a página de busca
                else {
                    $this->view->buscarPronac = $resultadoPronac;
                    $this->view->buscarDoc    = $resultadoComprovante;
                }

                // valida os campos vazios
                if (empty($tipoDocumento)) {
                    throw new Exception("Por favor, informe o tipo de documento!");
                } elseif (empty($titulo)) {
                    throw new Exception("Por favor, informe o t�tulo do documento!");
                } elseif (strlen($titulo) < 2 || strlen($titulo) > 100) {
                    throw new Exception("O t�tulo do documento � inv�lido! A quantidade m�nima � de 2 caracteres!");
                } elseif (empty($descricao)) {
                    throw new Exception("Por favor, informe a Descrição do documento!");
                } elseif (strlen($descricao) < 20 || strlen($descricao) > 500) {
                    throw new Exception("A Descrição do documento � inv�lida! S�o permitidos entre 20 e 500 caracteres!");
                } elseif (!empty($arquivoTemp) && ($arquivoExtensao == 'exe' || $arquivoExtensao == 'bat' ||
                $arquivoTipo == 'application/exe' || $arquivoTipo == 'application/x-exe' ||
                $arquivoTipo == 'application/dos-exe' || strlen($arquivoExtensao) > 5)) { // extens�o do arquivo
                    throw new Exception("A extens�o do arquivo � inv�lida!");
                } elseif (!empty($arquivoTemp) && $arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                    throw new Exception("O arquivo Não pode ser maior do que 10MB!");
                } elseif (!empty($arquivoTemp) && ArquivoDAO::verificarHash($arquivoHash)) { // hash do arquivo
                    throw new Exception("O arquivo enviado j� est� cadastrado na base de dados! Por favor, informe outro!");
                }
                // faz a altera��o no banco de dados
                else {
                    // altera o arquivo caso o mesmo tenha sido enviado
                    if (!empty($arquivoTemp)) {
                        // altera dados do arquivo
                        $dadosArquivo = array(
                            'nmArquivo'         => $arquivoNome,
                            'sgExtensao'        => $arquivoExtensao,
                            'dsTipoPadronizado' => $arquivoTipo,
                            'nrTamanho'         => $arquivoTamanho,
                            'dtEnvio'           => new Zend_Db_Expr('GETDATE()'),
                            'dsHash'            => $arquivoHash);
                        $alterarArquivo = ArquivoDAO::alterar($dadosArquivo, $idArquivo);

                        // altera o bin�rio do arquivo
                        $dadosBinario = array('biArquivo' => $arquivoBinario);
                        $alterarBinario = ArquivoImagemDAO::alterar($dadosBinario, $idArquivo);
                    } // fecha if

                    // altera dados do comprovante
                    $dadosComprovante = array(
                        'idPRONAC'             => $idPronac,
                        'idTipoDocumento'      => $tipoDocumento,
                        'nmComprovante'        => $titulo,
                        'dsComprovante'        => $descricao,
                        'idArquivo'            => $idArquivo,
                        'idSolicitante'        => 9997, // ===== MUDAR ID =====
                        'dtEnvioComprovante'   => new Zend_Db_Expr('GETDATE()'));
                    $alterarComprovante = ComprovanteExecucaoFisicaDAO::alterar($dadosComprovante, $doc);

                    if ($alterarComprovante) {
                        parent::message("Altera��o realizada com sucesso!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "CONFIRM");
                    } else {
                        parent::message("Erro ao realizar altera��o!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "ERROR");
                    }
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = "ERROR";
                $this->view->tipoDocumento = $tipoDocumento;
                $this->view->titulo        = $titulo;
                $this->view->descricao     = $descricao;
            }
        }
        // quando a página � aberta
        else {
            // recebe o pronac e comprovante via get
            $get    = Zend_Registry::get('get');
            $pronac = $get->pronac;
            $doc    = $get->doc;

            try {
                // verifica se o pronac ou o id do comprovante vieram vazios
                if (empty($pronac) || empty($doc)) {
                    throw new Exception("Por favor, informe o PRONAC e o Comprovante!");
                } else {
                    // integra��o MODELO e VIS�O

                    // busca o PRONAC no banco
                    $resultadoPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante Não estejam cadastrados
                    if (!$resultadoPronac || !$resultadoComprovante) {
                        throw new Exception("Registro Não encontrado!");
                    }
                    // caso o PRONAC e o Comprovante estejam cadastrados, vai para a página de busca
                    else {
                        $this->view->buscarPronac = $resultadoPronac;
                        $this->view->buscarDoc    = $resultadoComprovante;
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "execucaofisicadoprojeto/buscarpronac", "ERROR");
            }
        } // fecha else
    } // fecha m�todo alterardocumentosAction()



    /**
     * M�todo com o formul�rio para substituir documento do PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function substituirdocumentosAction()
    {
        // autentica��o scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // combo com os tipos de documentos
        $this->view->combotipodocumento = TipoDocumentoDAO::buscar();

        // caso o formul�rio seja enviado via post
        // realiza a substitui��o do documento
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post                     = Zend_Registry::get('post');
            $pronac                   = $post->pronac;
            $idPronac                 = (int) $post->idPronac;
            $doc                      = (int) $post->idComprovante;
            $idComprovanteAnterior    = (int) $post->idComprovanteAnterior;
            $idArquivo                = (int) $post->idArquivo;
            $tipoDocumento            = (int) $post->tipoDocumento;
            $titulo                   = $post->titulo;
            $descricao                = $post->descricao;
            $justificativa            = $post->justificativa;
            $justificativaCoordenador = $post->justificativaCoordenador;

            // pega as informa��es do arquivo
            $arquivoNome     = $_FILES['arquivo']['name']; // nome
            $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
            $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho
            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                $arquivoHash     = Upload::setHash($arquivoTemp); // hash
            }

            try {
                // integra��o MODELO e VIS�O

                // busca o PRONAC no banco
                $resultadoPronac = ProjetoDAO::buscar($pronac);

                // busca o Comprovante de acordo com o id no banco
                $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                // caso o PRONAC ou o Comprovante Não estejam cadastrados
                if (!$resultadoPronac || !$resultadoComprovante) {
                    parent::message("Registro Não encontrado!", "execucaofisicadoprojeto/buscarpronac", "ERROR");
                }
                // caso o PRONAC e o Comprovante estejam cadastrados, vai para a página de busca
                else {
                    $this->view->buscarPronac = $resultadoPronac;
                    $this->view->buscarDoc    = $resultadoComprovante;
                }

                // valida os campos vazios
                if (empty($tipoDocumento)) {
                    throw new Exception("Por favor, informe o tipo de documento!");
                } elseif (empty($titulo)) {
                    throw new Exception("Por favor, informe o t�tulo do documento!");
                } elseif (strlen($titulo) < 2 || strlen($titulo) > 100) {
                    throw new Exception("O t�tulo do documento � inv�lido! A quantidade m�nima � de 2 caracteres!");
                } elseif (empty($descricao)) {
                    throw new Exception("Por favor, informe a Descrição do documento!");
                } elseif (strlen($descricao) < 20 || strlen($descricao) > 500) {
                    throw new Exception("A Descrição do documento � inv�lida! S�o permitidos entre 20 e 500 caracteres!");
                } elseif (empty($justificativa) || $justificativa == "Digite a justificativa...") {
                    throw new Exception("Por favor, informe a justificativa do documento!");
                } elseif (strlen($justificativa) < 20 || strlen($justificativa) > 500) {
                    throw new Exception("A justificativa do documento � inv�lida! S�o permitidos entre 20 e 500 caracteres!");
                } elseif (!empty($arquivoTemp) && ($arquivoExtensao == 'exe' || $arquivoExtensao == 'bat' ||
                $arquivoTipo == 'application/exe' || $arquivoTipo == 'application/x-exe' ||
                $arquivoTipo == 'application/dos-exe' || strlen($arquivoExtensao) > 5)) { // extens�o do arquivo
                    throw new Exception("A extens�o do arquivo � inv�lida!");
                } elseif (!empty($arquivoTemp) && $arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                    throw new Exception("O arquivo Não pode ser maior do que 10MB!");
                } elseif (!empty($arquivoTemp) && ArquivoDAO::verificarHash($arquivoHash)) { // hash do arquivo
                    throw new Exception("O arquivo enviado j� est� cadastrado na base de dados! Por favor, informe outro!");
                }
                // faz a inserá�o (substitui��o) no banco de dados
                else {
                    // cadastra o arquivo caso o mesmo tenha sido enviado
                    if (!empty($arquivoTemp)) {
                        // altera dados do arquivo
                        $dadosArquivo = array(
                            'nmArquivo'         => $arquivoNome,
                            'sgExtensao'        => $arquivoExtensao,
                            'dsTipoPadronizado' => $arquivoTipo,
                            'nrTamanho'         => $arquivoTamanho,
                            'dtEnvio'           => new Zend_Db_Expr('GETDATE()'),
                            'dsHash'            => $arquivoHash,
                            'stAtivo'           => 'A');
                        $substituirArquivo = ArquivoDAO::cadastrar($dadosArquivo);

                        // pega o id do �ltimo arquivo cadastrado
                        $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
                        $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

                        // cadastrar o bin�rio do arquivo
                        $dadosBinario = array(
                            'idArquivo' => $idUltimoArquivo,
                            'biArquivo' => $arquivoBinario);
                        $substituirBinario = ArquivoImagemDAO::cadastrar($dadosBinario);

                        // cadastra dados do comprovante
                        $dadosComprovante = array(
                            'idPRONAC'                   => $idPronac,
                            'idTipoDocumento'            => $tipoDocumento,
                            'nmComprovante'              => $titulo,
                            'dsComprovante'              => $descricao,
                            'dsJustificativaAlteracao'   => $justificativa,
                            'dsJustificativaCoordenador' => $justificativaCoordenador,
                            'idArquivo'                  => $idUltimoArquivo,
                            'idSolicitante'              => 9997, // ===== MUDAR ID =====
                            'dtEnvioComprovante'         => new Zend_Db_Expr('GETDATE()'),
                            'stParecerComprovante'       => 'AG',
                            'stComprovante'              => 'A',
                            'idComprovanteAnterior'      => $idComprovanteAnterior);
                        $substituirComprovante = ComprovanteExecucaoFisicaDAO::cadastrar($dadosComprovante, $doc);
                    } // fecha if
                    // Não cadastra o arquivo
                    // pega a refer�ncia do arquivo cadastrado com o comprovante anterior
                    else {
                        // cadastra dados do comprovante
                        $dadosComprovante = array(
                            'idPRONAC'                   => $idPronac,
                            'idTipoDocumento'            => $tipoDocumento,
                            'nmComprovante'              => $titulo,
                            'dsComprovante'              => $descricao,
                            'dsJustificativaAlteracao'   => $justificativa,
                            'dsJustificativaCoordenador' => $justificativaCoordenador,
                            'idArquivo'                  => $idArquivo,
                            'idSolicitante'              => 9997, // ===== MUDAR ID =====
                            'dtEnvioComprovante'         => new Zend_Db_Expr('GETDATE()'),
                            'stParecerComprovante'       => 'AG',
                            'stComprovante'              => 'A',
                            'idComprovanteAnterior'      => $idComprovanteAnterior);
                        $substituirComprovante = ComprovanteExecucaoFisicaDAO::cadastrar($dadosComprovante, $doc);
                    }

                    if ($substituirComprovante) {
                        parent::message("Solicitação de substitui��o realizada com sucesso!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "CONFIRM");
                    } else {
                        parent::message("Erro ao realizar Solicitação de substitui��o!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "ERROR");
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                $this->view->message                  = $e->getMessage();
                $this->view->message_type             = "ERROR";
                $this->view->tipoDocumento            = $tipoDocumento;
                $this->view->titulo                   = $titulo;
                $this->view->descricao                = $descricao;
                $this->view->justificativa            = $justificativa;
                $this->view->justificativaCoordenador = $justificativaCoordenador;
            }
        }
        // quando a página � aberta
        else {
            // recebe o pronac via get
            $get    = Zend_Registry::get('get');
            $pronac = $get->pronac;
            $doc    = (int) $get->doc;

            try {
                // verifica se o pronac ou o id do comprovante vieram vazios
                if (empty($pronac) || empty($doc)) {
                    throw new Exception("Por favor, informe o PRONAC e o Comprovante!");
                } else {
                    // integra��o MODELO e VIS�O

                    // busca o PRONAC no banco
                    $resultadoPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante Não estejam cadastrados
                    if (!$resultadoPronac || !$resultadoComprovante) {
                        throw new Exception("Registro Não encontrado!");
                    }
                    // caso o PRONAC e o Comprovante estejam cadastrados, vai para a página de busca
                    else {
                        $this->view->buscarPronac = $resultadoPronac;
                        $this->view->buscarDoc    = $resultadoComprovante;
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "execucaofisicadoprojeto/buscarpronac", "ERROR");
            }
        } // fecha else
    } // fecha m�todo substituirdocumentosAction()



    /**
     * M�todo com o formul�rio para visualiza��o de documento agardando avalia��o
     * @access public
     * @param void
     * @return void
     */
    public function visualizardocumentosAction()
    {
        // autentica��o scriptcase e autentica��o/permiss�o zend (AMBIENTE PROPONENTE E MINC)
        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        parent::perfil(3, $PermissoesGrupo);



        // recebe os dados via get
        $get    = Zend_Registry::get('get');
        $pronac = $get->pronac;
        $doc    = (int) $get->doc;

        try {
            // verifica se o pronac e o comprovante vieram vazios
            if (empty($pronac) || empty($doc)) {
                throw new Exception("Por favor, informe o PRONAC e o Comprovante!");
            } else {
                // integra��o MODELO e VIS�O

                // busca o PRONAC no banco
                $resultadoPronac = ProjetoDAO::buscar($pronac);

                // busca o Comprovante de acordo com o id no banco
                $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                // caso o PRONAC ou o Comprovante Não estejam cadastrados
                if (!$resultadoPronac || !$resultadoComprovante) {
                    throw new Exception("Registro Não encontrado!");
                }
                // caso o PRONAC e o Comprovante estejam cadastrados, vai para a página de busca
                else {
                    $this->view->buscarPronac = $resultadoPronac;
                    $this->view->buscarDoc    = $resultadoComprovante;
                }
            } // fecha else
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "execucaofisicadoprojeto/buscarpronac", "ERROR");
        }
    } // fecha m�todo visualizacaodedocumentosAction()





    /**
     * ====================
     * T�CNICO
     * ====================
     */



    /**
     * M�todo para buscar projetos com comprovantes aguardando avalia��o
     * @access public
     * @param void
     * @return void
     */
    public function aguardandoavaliacaoAction()
    {
        // autentica��o e permiss�es zend (AMBIENTE MINC)
        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // caso o formul�rio seja enviado via post
        // realiza a busca de acordo com os par�metros enviados
        if ($this->getRequest()->isPost()) {
            // recebe o pronac via post
            $post      = Zend_Registry::get('post');
            $pronac    = ($post->pronac == 'Digite o Pronac...' ? '' : $post->pronac);
            $status    = $post->status;

            if ($post->dt_inicio == '00/00/0000') {
                $post->dt_inicio = "";
            }
            if ($post->dt_fim == '00/00/0000') {
                $post->dt_fim = "";
            }
            $dt_inicio = (!empty($post->dt_inicio)) ? (Data::dataAmericana($post->dt_inicio) . " 00:00:00") : $post->dt_inicio;
            $dt_fim    = (!empty($post->dt_fim))    ? (Data::dataAmericana($post->dt_fim) . " 23:59:59")    : $post->dt_fim;

            // data a ser validada
            $dt_begin = $dt_inicio;
            $dt_end   = $dt_fim;
            $dt_begin = explode(" ", $dt_begin);
            $dt_end   = explode(" ", $dt_end);

            try {
                // valida o n�mero do pronac
                if (!empty($pronac) && strlen($pronac) > 20) {
                    throw new Exception("O N� do PRONAC � inv�lido!");
                }
                // valida as datas
                elseif (!empty($dt_inicio) && !Data::validarData(Data::dataBrasileira($dt_begin[0]))) {
                    throw new Exception("A data inicial � inv�lida!");
                } elseif (!empty($dt_fim) && !Data::validarData(Data::dataBrasileira($dt_end[0]))) {
                    throw new Exception("A data final � inv�lida!");
                } else {
                    // busca os projetos com comprovantes
                    $resultado = ComprovanteExecucaoFisicaDAO::buscarProjetos($pronac, $status, $dt_inicio, $dt_fim);
                }
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "execucaofisicadoprojeto/aguardandoavaliacao", "ERROR");
            }
        } // fecha if
        // busca todos os pronac com status aguardando avalia��o
        else {
            $resultado = ComprovanteExecucaoFisicaDAO::buscarProjetos();
        } // fecha else


        // ========== INÍCIO PAGINAÇÂO ==========
        //criando a pagina�ao
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($resultado); // dados a serem paginados

        // página atual e quantidade de �tens por página
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
        // ========== FIM PAGINAÇÂO ==========


        // manda para a vis�o
        $this->view->paginacao = $paginator;
        $this->view->qtd       = count($resultado); // quantidade
    } // fecha m�todo aguardandoavaliacaoAction()



    /**
     * M�todo para buscar os documentos (comprovantes) do PRONAC 'Em Avalia��o'
     * @access public
     * @param void
     * @return void
     */
    public function comprovantesemavaliacaoAction()
    {
        // autentica��o e permiss�es zend (AMBIENTE MINC)
        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // recebe o pronac via get
        $get    = Zend_Registry::get('get');
        $pronac = $get->pronac;

        try {
            // verifica se o pronac veio vazio
            if (empty($pronac)) {
                throw new Exception("Por favor, informe o PRONAC!");
            }
            // valida o n�mero do pronac
            elseif (strlen($pronac) > 20) {
                throw new Exception("O N� do PRONAC � inv�lido!");
            } else {
                // integra��o MODELO e VIS�O

                // busca o PRONAC no banco
                $resultPronac = ProjetoDAO::buscar($pronac);

                // caso o PRONAC Não esteja cadastrado
                if (!$resultPronac) {
                    throw new Exception("Registro Não encontrado!");
                }
                // caso o PRONAC esteja cadastrado, vai para a página de busca
                // dos seus documentos (comprovantes)
                else {
                    // manda o pronac para a vis�o
                    $this->view->buscarPronac = $resultPronac;

                    // pega o id do pronac
                    $idPronac = $resultPronac[0]->IdPRONAC;

                    // busca os documentos (comprovantes) do pronac
                    // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PAR�METRO ==========
                    $resultComprovantes = ComprovanteExecucaoFisicaDAO::buscarDocumentos($idPronac);

                    // caso Não existam comprovantes cadastrados
                    if (!$resultComprovantes) {
                        $this->view->message      = "Nenhum comprovante cadastrado para o PRONAC N� " . $pronac . "!";
                        $this->view->message_type = "ALERT";
                    } else {
                        // busca o hist�rico dos comprovantes
                        for ($i = 0; $i < sizeof($resultComprovantes); $i++) :
                            // adiciona os comprovantes no novo array
                            $arrayComprovantes[$i] = array('comprovantes' => $resultComprovantes[$i]);

                        // hist�rico
                        $resultHistorico = ComprovanteExecucaoFisicaDAO::buscarHistorico(
                                $resultComprovantes[$i]->idComprovante,
                                $resultComprovantes[$i]->idComprovanteAnterior
                            );
                        // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PAR�METRO ==========

                        // adiciona o hist�rico no seu respectivo comprovante
                        if (sizeof($resultHistorico) > 0) :
                                array_push($arrayComprovantes[$i], array('historicos' => $resultHistorico));
                        endif;
                        endfor;


                        // ========== INÍCIO PAGINAÇÂO ==========
                        //criando a pagina�ao
                        Zend_Paginator::setDefaultScrollingStyle('Sliding');
                        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
                        $paginator = Zend_Paginator::factory($arrayComprovantes); // dados a serem paginados

                        // página atual e quantidade de �tens por página
                        $currentPage = $this->_getParam('page', 1);
                        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
                        // ========== FIM PAGINAÇÂO ==========


                        // manda os comprovantes e seu hist�rico para a vis�o
                        //$this->view->buscarComprovantes = $arrayComprovantes;
                        $this->view->paginacao          = $paginator;
                        $this->view->qtdComprovantes    = count($resultComprovantes); // quantidade de comprovantes
                    }
                }
            } // fecha else
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "execucaofisicadoprojeto/aguardandoavaliacao", "ERROR");
        }
    } // fecha m�todo comprovantesemavaliacaoAction()



    /**
     * M�todo para avaliar os comprovantes
     * @access public
     * @param void
     * @return void
     */
    public function avaliarcomprovanteAction()
    {
        // autentica��o e permiss�es zend (AMBIENTE MINC)
        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post     = Zend_Registry::get("post");
            $pronac   = $post->pronac;
            $idPronac = (int) $post->idPronac;
            $doc      = (int) $post->doc;
            $parecer  = $post->parecer;

            try {
                // verifica se o pronac ou o comprovante veio vazio
                if (empty($pronac) || empty($doc)) {
                    throw new Exception("Por favor, informe o PRONAC e o Comprovante!");
                } else {
                    // integra��o MODELO e VIS�O

                    // busca o PRONAC no banco
                    $resultPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante Não estejam cadastrados
                    if (!$resultPronac || !$resultComprovante) {
                        parent::message("Registro Não encontrado!", "execucaofisicadoprojeto/aguardandoavaliacao", "ERROR");
                    }
                    // caso o PRONAC esteja cadastrado
                    else {
                        // busca o comprovante anterior caso seja um comprovante substitu�do
                        $resultComprovanteSubstituido = ComprovanteExecucaoFisicaDAO::buscarUltimoComprovanteAprovado($resultPronac[0]->IdPRONAC, $doc, $resultComprovante[0]->idComprovanteAnterior);
                        $this->view->buscarComprovanteSubstituido = $resultComprovanteSubstituido;

                        $this->view->buscarPronac = $resultPronac;
                        $this->view->buscarDoc    = $resultComprovante;
                    }
                } // fecha else

                // valida o parecer
                if (empty($parecer) || $parecer == 'Digite o parecer...') {
                    throw new Exception("Por favor, informe o parecer!");
                } elseif (strlen($parecer) < 20 || strlen($parecer) > 500) {
                    throw new Exception("O Parecer � inv�lido! S�o permitidos entre 20 e 500 caracteres!");
                } else {
                    // atualiza o status para 'Em aprovação'
                    $dadosComprovante = array(
                        'dsParecerComprovante'   => $parecer,
                        'stParecerComprovante'   => 'EA',
                        'dtParecer'              => new Zend_Db_Expr('GETDATE()'),
                        'idAvaliadorComprovante' => 9998); // ========== ALTERAR ID AVALIADOR ==========

                    $alterarComprovante = ComprovanteExecucaoFisicaDAO::alterar($dadosComprovante, $doc);
                    if ($alterarComprovante) {
                        parent::message("Comprovante avaliado com sucesso!", "execucaofisicadoprojeto/comprovantesemavaliacao?pronac=$pronac", "CONFIRM");
                    } else {
                        parent::message("Erro ao avaliar comprovante!", "execucaofisicadoprojeto/comprovantesemavaliacao?pronac=$pronac", "ERROR");
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                $this->view->message      = $e->getMessage();
                $this->view->message_type = "ERROR";
                $this->view->parecer      = $parecer;
            }
        } // fecha if
        // quando a página � aberta
        else {
            // recebe os dados via get
            $get    = Zend_Registry::get("get");
            $pronac = $get->pronac;
            $doc    = (int) $get->doc;

            try {
                // verifica se o pronac ou o comprovante veio vazio
                if (empty($pronac) || empty($doc)) {
                    throw new Exception("Por favor, informe o PRONAC e o Comprovante!");
                } else {
                    // integra��o MODELO e VIS�O

                    // busca o PRONAC no banco
                    $resultPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante Não estejam cadastrados
                    if (!$resultPronac || !$resultComprovante) {
                        throw new Exception("Registro Não encontrado!");
                    }
                    // caso o PRONAC esteja cadastrado
                    else {
                        // assim que o t�cnico clica em 'Avaliar', o status � alterado para 'Em Avalia��o'
                        $dadosComprovante   = array('stParecerComprovante' => 'AV');
                        $alterarComprovante = ComprovanteExecucaoFisicaDAO::alterar($dadosComprovante, $doc);

                        // busca o comprovante anterior caso seja um comprovante substitu�do
                        $resultComprovanteSubstituido = ComprovanteExecucaoFisicaDAO::buscarUltimoComprovanteAprovado($resultPronac[0]->IdPRONAC, $doc, $resultComprovante[0]->idComprovanteAnterior);
                        $this->view->buscarComprovanteSubstituido = $resultComprovanteSubstituido;

                        $this->view->buscarPronac = $resultPronac;
                        $this->view->buscarDoc    = $resultComprovante;
                    }
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message      = $e->getMessage();
                $this->view->message_type = "ERROR";
            }
        } // fecha else
    } // fecha avaliarcomprovanteAction()





    /**
     * ====================
     * COORDENADOR
     * ====================
     */



    /**
     * M�todo para buscar projetos com comprovantes aguardando aprovação
     * @access public
     * @param void
     * @return void
     */
    public function aguardandoaprovacaoAction()
    {
        // autentica��o e permiss�es zend (AMBIENTE MINC)
        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // caso o formul�rio seja enviado via post
        // realiza a busca de acordo com os par�metros enviados
        if ($this->getRequest()->isPost()) {
            // recebe o pronac via post
            $post      = Zend_Registry::get('post');
            $pronac    = ($post->pronac == 'Digite o Pronac...' ? '' : $post->pronac);
            $status    = $post->status;

            if ($post->dt_inicio == '00/00/0000') {
                $post->dt_inicio = "";
            }
            if ($post->dt_fim == '00/00/0000') {
                $post->dt_fim = "";
            }
            $dt_inicio = (!empty($post->dt_inicio)) ? (Data::dataAmericana($post->dt_inicio) . " 00:00:00") : $post->dt_inicio;
            $dt_fim    = (!empty($post->dt_fim))    ? (Data::dataAmericana($post->dt_fim) . " 23:59:59")    : $post->dt_fim;

            // data a ser validada
            $dt_begin = $dt_inicio;
            $dt_end   = $dt_fim;
            $dt_begin = explode(" ", $dt_begin);
            $dt_end   = explode(" ", $dt_end);

            try {
                // valida o n�mero do pronac
                if (!empty($pronac) && strlen($pronac) > 20) {
                    throw new Exception("O N� do PRONAC � inv�lido!");
                }
                // valida as datas
                elseif (!empty($dt_inicio) && !Data::validarData(Data::dataBrasileira($dt_begin[0]))) {
                    throw new Exception("A data inicial � inv�lida!");
                } elseif (!empty($dt_fim) && !Data::validarData(Data::dataBrasileira($dt_end[0]))) {
                    throw new Exception("A data final � inv�lida!");
                } else {
                    // busca os projetos com comprovantes
                    $resultado = ComprovanteExecucaoFisicaDAO::buscarProjetos($pronac, $status, $dt_inicio, $dt_fim);
                }
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "execucaofisicadoprojeto/aguardandoaprovacao", "ERROR");
            }
        } // fecha if
        // busca todos os pronac
        else {
            $resultado = ComprovanteExecucaoFisicaDAO::buscarProjetos();
        } // fecha else


        // ========== INÍCIO PAGINAÇÂO ==========
        //criando a pagina�ao
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($resultado); // dados a serem paginados

        // página atual e quantidade de �tens por página
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
        // ========== FIM PAGINAÇÂO ==========


        // manda para a vis�o
        $this->view->paginacao = $paginator;
        $this->view->qtd       = count($resultado); // quantidade
    } // fecha aguardandoaprovacaoAction()



    /**
     * M�todo para buscar os documentos (comprovantes) do PRONAC 'Em Avalia��o'
     * @access public
     * @param void
     * @return void
     */
    public function comprovantesemaprovacaoAction()
    {
        // autentica��o e permiss�es zend (AMBIENTE MINC)
        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // recebe o pronac via get
        $get    = Zend_Registry::get('get');
        $pronac = $get->pronac;

        try {
            // verifica se o pronac veio vazio
            if (empty($pronac)) {
                throw new Exception("Por favor, informe o PRONAC!");
            }
            // valida o n�mero do pronac
            elseif (strlen($pronac) > 20) {
                throw new Exception("O N� do PRONAC � inv�lido!");
            } else {
                // integra��o MODELO e VIS�O

                // busca o PRONAC de acordo com o id no banco
                $resultPronac = ProjetoDAO::buscar($pronac);

                // caso o PRONAC Não esteja cadastrado
                if (!$resultPronac) {
                    throw new Exception("Registro Não encontrado!");
                }
                // caso o PRONAC esteja cadastrado, vai para a página de busca
                // dos seus documentos (comprovantes)
                else {
                    // manda o pronac para a vis�o
                    $this->view->buscarPronac = $resultPronac;

                    // pega o id do pronac
                    $idPronac = $resultPronac[0]->IdPRONAC;

                    // busca os documentos (comprovantes) do pronac
                    // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PAR�METRO ==========
                    $resultComprovantes = ComprovanteExecucaoFisicaDAO::buscarDocumentos($idPronac);

                    // caso Não existam comprovantes cadastrados
                    if (!$resultComprovantes) {
                        $this->view->message      = "Nenhum comprovante cadastrado para o PRONAC N� " . $pronac . "!";
                        $this->view->message_type = "ALERT";
                    } else {
                        // busca o hist�rico dos comprovantes
                        for ($i = 0; $i < sizeof($resultComprovantes); $i++) :
                            // adiciona os comprovantes no novo array
                            $arrayComprovantes[$i] = array('comprovantes' => $resultComprovantes[$i]);

                        // hist�rico
                        $resultHistorico = ComprovanteExecucaoFisicaDAO::buscarHistorico(
                                $resultComprovantes[$i]->idComprovante,
                                $resultComprovantes[$i]->idComprovanteAnterior
                            );
                        // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PAR�METRO ==========

                        // adiciona o hist�rico no seu respectivo comprovante
                        if (sizeof($resultHistorico) > 0) :
                                array_push($arrayComprovantes[$i], array('historicos' => $resultHistorico));
                        endif;
                        endfor;


                        // ========== INÍCIO PAGINAÇÂO ==========
                        //criando a pagina�ao
                        Zend_Paginator::setDefaultScrollingStyle('Sliding');
                        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
                        $paginator = Zend_Paginator::factory($arrayComprovantes); // dados a serem paginados

                        // página atual e quantidade de �tens por página
                        $currentPage = $this->_getParam('page', 1);
                        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
                        // ========== FIM PAGINAÇÂO ==========


                        // manda os comprovantes e seu hist�rico para a vis�o
                        //$this->view->buscarComprovantes = $arrayComprovantes;
                        $this->view->paginacao          = $paginator;
                        $this->view->qtdComprovantes    = count($resultComprovantes); // quantidade de comprovantes
                    }
                }
            } // fecha else
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "execucaofisicadoprojeto/aguardandoaprovacao", "ERROR");
        }
    } // fecha m�todo comprovantesemaprovacaoAction()



    /**
     * M�todo para aprovar (deferir ou indeferir) os comprovantes
     * @access public
     * @param void
     * @return void
     */
    public function aprovarcomprovanteAction()
    {
        // autentica��o e permiss�es zend (AMBIENTE MINC)
        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post                  = Zend_Registry::get("post");
            $pronac                = $post->pronac;
            $idPronac              = (int) $post->idPronac;
            $doc                   = (int) $post->doc;
            $idComprovanteAnterior = (int) $post->idComprovanteAnterior;
            $parecer               = $post->parecer;
            $status                = $post->status;

            try {
                // verifica se o pronac ou o comprovante veio vazio
                if (empty($pronac) || empty($doc)) {
                    throw new Exception("Por favor, informe o PRONAC e o Comprovante!");
                } else {
                    // integra��o MODELO e VIS�O

                    // busca o PRONAC no banco
                    $resultPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante Não estejam cadastrados
                    if (!$resultPronac || !$resultComprovante) {
                        parent::message("Registro Não encontrado!", "execucaofisicadoprojeto/aguardandoaprovacao", "ERROR");
                    }
                    // caso o PRONAC esteja cadastrado
                    else {
                        // busca o comprovante anterior caso seja um comprovante substitu�do
                        $resultComprovanteSubstituido = ComprovanteExecucaoFisicaDAO::buscarUltimoComprovanteAprovado($resultPronac[0]->IdPRONAC, $doc, $resultComprovante[0]->idComprovanteAnterior);
                        $this->view->buscarComprovanteSubstituido = $resultComprovanteSubstituido;

                        $this->view->buscarPronac = $resultPronac;
                        $this->view->buscarDoc    = $resultComprovante;
                    }
                }

                // valida o parecer
                if (empty($parecer) || $parecer == 'Digite a justificativa...') {
                    throw new Exception("Por favor, informe a justificativa!");
                } elseif (strlen($parecer) < 20 || strlen($parecer) > 500) {
                    throw new Exception("A Justificativa � inv�lida! S�o permitidos entre 20 e 500 caracteres!");
                } else {
                    // caso o comprovante seja DEFERIDO,
                    // coloca o �ltimo aprovado como deferido
                    if ($status == 'AD') {
                        $buscarUltimoAprovado = ComprovanteExecucaoFisicaDAO::buscarUltimoComprovanteAprovado($resultPronac[0]->IdPRONAC, $doc, $idComprovanteAnterior);
                        $dadosStatus          = array('stParecerComprovante' => 'CS');
                        foreach ($buscarUltimoAprovado as $b):
                            $alterarStatus = ComprovanteExecucaoFisicaDAO::alterar($dadosStatus, $b->idComprovante);
                        endforeach;
                    }

                    // atualiza o status para 'Avaliado - Deferido' ou 'Avaliado - Indeferido'
                    $dadosComprovante   = array(
                        'dsJustificativaCoordenador'  => $parecer,
                        'stParecerComprovante'        => $status,
                        'dtJustificativaCoordenador'  => new Zend_Db_Expr('GETDATE()'),
                        'idCoordenador'               => 9999); // ========== ALTERAR ID COORDENADOR ==========

                    $alterarComprovante = ComprovanteExecucaoFisicaDAO::alterar($dadosComprovante, $doc);

                    $msgStatus = ($status == 'AD') ? 'deferido' : 'indeferido';
                    if ($alterarComprovante) {
                        parent::message("Comprovante {$msgStatus} com sucesso!", "execucaofisicadoprojeto/comprovantesemaprovacao?pronac=$pronac", "CONFIRM");
                    } else {
                        parent::message("Erro ao {$msgStatus} comprovante!", "execucaofisicadoprojeto/comprovantesemaprovacao?pronac=$pronac", "ERROR");
                    }
                }
            } catch (Exception $e) {
                $this->view->message      = $e->getMessage();
                $this->view->message_type = "ERROR";
                $this->view->parecer      = $parecer;
                $this->view->status       = $status;
            }
        } else {
            // recebe os dados via get
            $get = Zend_Registry::get("get");
            $pronac = $get->pronac;
            $doc    = (int) $get->doc;

            try {
                // verifica se o pronac ou o comprovante veio vazio
                if (empty($pronac) || empty($doc)) {
                    throw new Exception("Por favor, informe o PRONAC e o Comprovante!");
                } else {
                    // integra��o MODELO e VIS�O

                    // busca o PRONAC de acordo com o id no banco
                    $resultPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante Não estejam cadastrados
                    if (!$resultPronac || !$resultComprovante) {
                        throw new Exception("Registro Não encontrado!");
                    }
                    // caso o PRONAC esteja cadastrado
                    else {
                        // busca o comprovante anterior caso seja um comprovante substitu�do
                        $resultComprovanteSubstituido = ComprovanteExecucaoFisicaDAO::buscarUltimoComprovanteAprovado($resultPronac[0]->IdPRONAC, $doc, $resultComprovante[0]->idComprovanteAnterior);
                        $this->view->buscarComprovanteSubstituido = $resultComprovanteSubstituido;

                        $this->view->buscarPronac = $resultPronac;
                        $this->view->buscarDoc    = $resultComprovante;
                    }
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message      = $e->getMessage();
                $this->view->message_type = "ERROR";
            }
        } // fecha else
    } // fecha aprovarcomprovanteAction()
} // fecha class
