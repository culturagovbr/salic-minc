<?php

class ExecucaofisicadoprojetoController extends MinC_Controller_Action_Abstract
{
    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio às Leis de Incentivo à Cultura"; // título da página

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
        $this->_forward("buscardocumentos");
    }



    /**
     * Método com o formulário para buscar o PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function buscarpronacAction()
    {
        // autenticação scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // caso o formulário seja enviado via post
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
                    // integração MODELO e VISÃO

                    // busca de acordo com o pronac no banco
                    $resultado = ProjetoDAO::buscar($pronac);

                    // caso o PRONAC não esteja cadastrado
                    if (!$resultado) {
                        throw new Exception("Registro não encontrado!");
                    }
                    // caso o PRONAC esteja cadastrado,
                    // vai para a página de busca dos documentos (comprovantes) do pronac
                    else {
                        // redireciona o pronac para a página com seus documentos (comprovantes)
                        $this->_redirect("execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac);
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "execucaofisicadoprojeto/buscarpronac", "ERROR");
            }
        } // fecha if
    } // fecha método buscarpronacAction()



    /**
     * Método para buscar os documentos (comprovantes) do PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function buscardocumentosAction()
    {
        // autenticação scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // recebe o pronac via get
        $get    = Zend_Registry::get('get');
        $pronac = $get->pronac;

        try {
            // verifica se o pronac veio vazio
            if (empty($pronac)) {
                throw new Exception("Por favor, informe o PRONAC!");
            }
            // valida o número do pronac
            elseif (strlen($pronac) > 20) {
                throw new Exception("O Nº do PRONAC é inválido!");
            } else {
                // integração MODELO e VISÃO

                // busca de acordo com o pronac no banco
                $resultPronac = ProjetoDAO::buscar($pronac);

                // caso o PRONAC não esteja cadastrado
                if (!$resultPronac) {
                    throw new Exception("Registro não encontrado!");
                }
                // caso o PRONAC esteja cadastrado, vai para a página de busca
                // dos seus documentos (comprovantes)
                else {
                    // manda o pronac para a visão
                    $this->view->buscarPronac = $resultPronac;

                    // pega o id do pronac
                    $idPronac = $resultPronac[0]->IdPRONAC;

                    // busca os documentos (comprovantes) do pronac
                    // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PARÂMETRO ==========
                    $resultComprovantes = ComprovanteExecucaoFisicaDAO::buscarDocumentos($idPronac);

                    // caso não existam comprovantes cadastrados
                    if (!$resultComprovantes) {
                        $this->view->message      = "Nenhum comprovante cadastrado para o PRONAC Nº " . $pronac . "!";
                        $this->view->message_type = "ALERT";
                    } else {
                        // busca o histórico dos comprovantes
                        for ($i = 0; $i < sizeof($resultComprovantes); $i++) :
                            // adiciona os comprovantes no novo array
                            $arrayComprovantes[$i] = array('comprovantes' => $resultComprovantes[$i]);

                        // histórico
                        $resultHistorico = ComprovanteExecucaoFisicaDAO::buscarHistorico(
                                $resultComprovantes[$i]->idComprovante,
                                $resultComprovantes[$i]->idComprovanteAnterior
                            );
                        // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PARÂMETRO ==========

                        // adiciona o histórico no seu respectivo comprovante
                        if (sizeof($resultHistorico) > 0) :
                                array_push($arrayComprovantes[$i], array('historicos' => $resultHistorico));
                        endif;
                        endfor;


                        // ========== INÍCIO PAGINAÇÃO ==========
                        //criando a paginaçao
                        Zend_Paginator::setDefaultScrollingStyle('Sliding');
                        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
                        $paginator = Zend_Paginator::factory($arrayComprovantes); // dados a serem paginados

                        // página atual e quantidade de ítens por página
                        $currentPage = $this->_getParam('page', 1);
                        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
                        // ========== FIM PAGINAÇÃO ==========


                        // manda os comprovantes e seu histórico para a visão
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
    } // fecha método buscardocumentosAction()



    /**
     * Método com o formulário para cadastrar documento do PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function cadastrardocumentosAction()
    {
        // autenticação scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // combo com os tipos de documentos
        $this->view->combotipodocumento = TipoDocumentoDAO::buscar();

        // caso o formulário seja enviado via post
        // cadastra o documento
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post          = Zend_Registry::get('post');
            $pronac        = $post->pronac;
            $idPronac 	   = (int) $post->idPronac;
            $tipoDocumento = $post->tipoDocumento;
            $titulo        = $post->titulo;
            $descricao     = $post->descricao;

            // pega as informações do arquivo
            $arquivoNome     = $_FILES['arquivo']['name']; // nome
            $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome temporário
            $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho
            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensão
                $arquivoBinario  = Upload::setBinario($arquivoTemp); // binário
                $arquivoHash     = Upload::setHash($arquivoTemp); // hash
            }

            try {
                // integração MODELO e VISÃO

                // busca de acordo com o pronac no banco
                $resultado = ProjetoDAO::buscar($pronac);

                // caso o PRONAC não esteja cadastrado
                if (!$resultado) {
                    parent::message("Registro não encontrado!", "execucaofisicadoprojeto/buscarpronac", "ERROR");
                }
                // caso o PRONAC esteja cadastrado, vai para a página de busca
                else {
                    $this->view->buscarPronac = $resultado;
                }

                // valida os campos vazios
                if (empty($tipoDocumento)) {
                    throw new Exception("Por favor, informe o tipo de documento!");
                } elseif (empty($titulo) || $titulo == 'Digite o título do comprovante...') {
                    throw new Exception("Por favor, informe o título do documento!");
                } elseif (strlen($titulo) < 2 || strlen($titulo) > 100) {
                    throw new Exception("O título do documento é inválido! A quantidade mínima é de 2 caracteres!");
                } elseif (empty($descricao) || $descricao == 'Digite o texto do comprovante...') {
                    throw new Exception("Por favor, informe a descrição do documento!");
                } elseif (strlen($descricao) < 20 || strlen($descricao) > 500) {
                    throw new Exception("A descrição do documento é inválida! São permitidos entre 20 e 500 caracteres!");
                } elseif (empty($arquivoTemp)) { // nome do arquivo
                    throw new Exception("Por favor, informe o arquivo!");
                } elseif ($arquivoExtensao == 'exe' || $arquivoExtensao == 'bat' ||
                $arquivoTipo == 'application/exe' || $arquivoTipo == 'application/x-exe' ||
                $arquivoTipo == 'application/dos-exe' || strlen($arquivoExtensao) > 5) { // extensão do arquivo
                    throw new Exception("A extensão do arquivo é inválida!");
                } elseif ($arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                    throw new Exception("O arquivo não pode ser maior do que 10MB!");
                } elseif (ArquivoDAO::verificarHash($arquivoHash)) { // hash do arquivo
                    throw new Exception("O arquivo enviado já está cadastrado na base de dados! Por favor, informe outro!");
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

                    // pega o id do último arquivo cadastrado
                    $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
                    $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

                    // cadastra o binário do arquivo
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

                    // pega o id do último comprovante cadastrado
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
        // quando a página é aberta
        else {
            // recebe o pronac via get
            $get    = Zend_Registry::get('get');
            $pronac = $get->pronac;

            try {
                // verifica se o pronac veio vazio
                if (empty($pronac)) {
                    throw new Exception("Por favor, informe o PRONAC!");
                } else {
                    // integração MODELO e VISÃO

                    // busca o PRONAC no banco
                    $resultado = ProjetoDAO::buscar($pronac);

                    // caso o PRONAC não esteja cadastrado
                    if (!$resultado) {
                        throw new Exception("Regisitro não encontrado!");
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
    } // fecha método cadastrardocumentosAction()



    /**
     * Método com o formulário para alterar documento do PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function alterardocumentosAction()
    {
        // autenticação scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // combo com os tipos de documentos
        $this->view->combotipodocumento = TipoDocumentoDAO::buscar();

        // caso o formulário seja enviado via post
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

            // pega as informações do arquivo
            $arquivoNome     = $_FILES['arquivo']['name']; // nome
            $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome temporário
            $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho
            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensão
                $arquivoBinario  = Upload::setBinario($arquivoTemp); // binário
                $arquivoHash     = Upload::setHash($arquivoTemp); // hash
            }

            try {
                // integração MODELO e VISÃO

                // busca o PRONAC no banco
                $resultadoPronac = ProjetoDAO::buscar($pronac);

                // busca o Comprovante de acordo com o id no banco
                $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                // caso o PRONAC ou o Comprovante não estejam cadastrados
                if (!$resultadoPronac || !$resultadoComprovante) {
                    parent::message("Registro não encontrado!", "execucaofisicadoprojeto/buscarpronac", "ERROR");
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
                    throw new Exception("Por favor, informe o título do documento!");
                } elseif (strlen($titulo) < 2 || strlen($titulo) > 100) {
                    throw new Exception("O título do documento é inválido! A quantidade mínima é de 2 caracteres!");
                } elseif (empty($descricao)) {
                    throw new Exception("Por favor, informe a descrição do documento!");
                } elseif (strlen($descricao) < 20 || strlen($descricao) > 500) {
                    throw new Exception("A descrição do documento é inválida! São permitidos entre 20 e 500 caracteres!");
                } elseif (!empty($arquivoTemp) && ($arquivoExtensao == 'exe' || $arquivoExtensao == 'bat' ||
                $arquivoTipo == 'application/exe' || $arquivoTipo == 'application/x-exe' ||
                $arquivoTipo == 'application/dos-exe' || strlen($arquivoExtensao) > 5)) { // extensão do arquivo
                    throw new Exception("A extensão do arquivo é inválida!");
                } elseif (!empty($arquivoTemp) && $arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                    throw new Exception("O arquivo não pode ser maior do que 10MB!");
                } elseif (!empty($arquivoTemp) && ArquivoDAO::verificarHash($arquivoHash)) { // hash do arquivo
                    throw new Exception("O arquivo enviado já está cadastrado na base de dados! Por favor, informe outro!");
                }
                // faz a alteração no banco de dados
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

                        // altera o binário do arquivo
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
                        parent::message("Alteração realizada com sucesso!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "CONFIRM");
                    } else {
                        parent::message("Erro ao realizar alteração!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "ERROR");
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
        // quando a página é aberta
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
                    // integração MODELO e VISÃO

                    // busca o PRONAC no banco
                    $resultadoPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante não estejam cadastrados
                    if (!$resultadoPronac || !$resultadoComprovante) {
                        throw new Exception("Registro não encontrado!");
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
    } // fecha método alterardocumentosAction()



    /**
     * Método com o formulário para substituir documento do PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function substituirdocumentosAction()
    {
        // autenticação scriptcase (AMBIENTE PROPONENTE)
        parent::perfil(2);



        // combo com os tipos de documentos
        $this->view->combotipodocumento = TipoDocumentoDAO::buscar();

        // caso o formulário seja enviado via post
        // realiza a substituição do documento
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

            // pega as informações do arquivo
            $arquivoNome     = $_FILES['arquivo']['name']; // nome
            $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome temporário
            $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho
            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensão
                $arquivoBinario  = Upload::setBinario($arquivoTemp); // binário
                $arquivoHash     = Upload::setHash($arquivoTemp); // hash
            }

            try {
                // integração MODELO e VISÃO

                // busca o PRONAC no banco
                $resultadoPronac = ProjetoDAO::buscar($pronac);

                // busca o Comprovante de acordo com o id no banco
                $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                // caso o PRONAC ou o Comprovante não estejam cadastrados
                if (!$resultadoPronac || !$resultadoComprovante) {
                    parent::message("Registro não encontrado!", "execucaofisicadoprojeto/buscarpronac", "ERROR");
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
                    throw new Exception("Por favor, informe o título do documento!");
                } elseif (strlen($titulo) < 2 || strlen($titulo) > 100) {
                    throw new Exception("O título do documento é inválido! A quantidade mínima é de 2 caracteres!");
                } elseif (empty($descricao)) {
                    throw new Exception("Por favor, informe a descrição do documento!");
                } elseif (strlen($descricao) < 20 || strlen($descricao) > 500) {
                    throw new Exception("A descrição do documento é inválida! São permitidos entre 20 e 500 caracteres!");
                } elseif (empty($justificativa) || $justificativa == "Digite a justificativa...") {
                    throw new Exception("Por favor, informe a justificativa do documento!");
                } elseif (strlen($justificativa) < 20 || strlen($justificativa) > 500) {
                    throw new Exception("A justificativa do documento é inválida! São permitidos entre 20 e 500 caracteres!");
                } elseif (!empty($arquivoTemp) && ($arquivoExtensao == 'exe' || $arquivoExtensao == 'bat' ||
                $arquivoTipo == 'application/exe' || $arquivoTipo == 'application/x-exe' ||
                $arquivoTipo == 'application/dos-exe' || strlen($arquivoExtensao) > 5)) { // extensão do arquivo
                    throw new Exception("A extensão do arquivo é inválida!");
                } elseif (!empty($arquivoTemp) && $arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                    throw new Exception("O arquivo não pode ser maior do que 10MB!");
                } elseif (!empty($arquivoTemp) && ArquivoDAO::verificarHash($arquivoHash)) { // hash do arquivo
                    throw new Exception("O arquivo enviado já está cadastrado na base de dados! Por favor, informe outro!");
                }
                // faz a inserção (substituição) no banco de dados
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

                        // pega o id do último arquivo cadastrado
                        $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
                        $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

                        // cadastrar o binário do arquivo
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
                    // não cadastra o arquivo
                    // pega a referência do arquivo cadastrado com o comprovante anterior
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
                        parent::message("Solicitação de substituição realizada com sucesso!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "CONFIRM");
                    } else {
                        parent::message("Erro ao realizar solicitação de substituição!", "execucaofisicadoprojeto/buscardocumentos?pronac=" . $pronac, "ERROR");
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
        // quando a página é aberta
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
                    // integração MODELO e VISÃO

                    // busca o PRONAC no banco
                    $resultadoPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante não estejam cadastrados
                    if (!$resultadoPronac || !$resultadoComprovante) {
                        throw new Exception("Registro não encontrado!");
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
    } // fecha método substituirdocumentosAction()



    /**
     * Método com o formulário para visualização de documento agardando avaliação
     * @access public
     * @param void
     * @return void
     */
    public function visualizardocumentosAction()
    {
        // autenticação scriptcase e autenticação/permissão zend (AMBIENTE PROPONENTE E MINC)
        // define as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // Técnico de Acompanhamento
        $PermissoesGrupo[] = 129; // Técnico de Acompanhamento
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
                // integração MODELO e VISÃO

                // busca o PRONAC no banco
                $resultadoPronac = ProjetoDAO::buscar($pronac);

                // busca o Comprovante de acordo com o id no banco
                $resultadoComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultadoPronac[0]->IdPRONAC, $doc);

                // caso o PRONAC ou o Comprovante não estejam cadastrados
                if (!$resultadoPronac || !$resultadoComprovante) {
                    throw new Exception("Registro não encontrado!");
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
    } // fecha método visualizacaodedocumentosAction()





    /**
     * ====================
     * TÉCNICO
     * ====================
     */



    /**
     * Método para buscar projetos com comprovantes aguardando avaliação
     * @access public
     * @param void
     * @return void
     */
    public function aguardandoavaliacaoAction()
    {
        // autenticação e permissões zend (AMBIENTE MINC)
        // define as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // Técnico de Acompanhamento
        $PermissoesGrupo[] = 129; // Técnico de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // caso o formulário seja enviado via post
        // realiza a busca de acordo com os parâmetros enviados
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
                // valida o número do pronac
                if (!empty($pronac) && strlen($pronac) > 20) {
                    throw new Exception("O Nº do PRONAC é inválido!");
                }
                // valida as datas
                elseif (!empty($dt_inicio) && !Data::validarData(Data::dataBrasileira($dt_begin[0]))) {
                    throw new Exception("A data inicial é inválida!");
                } elseif (!empty($dt_fim) && !Data::validarData(Data::dataBrasileira($dt_end[0]))) {
                    throw new Exception("A data final é inválida!");
                } else {
                    // busca os projetos com comprovantes
                    $resultado = ComprovanteExecucaoFisicaDAO::buscarProjetos($pronac, $status, $dt_inicio, $dt_fim);
                }
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "execucaofisicadoprojeto/aguardandoavaliacao", "ERROR");
            }
        } // fecha if
        // busca todos os pronac com status aguardando avaliação
        else {
            $resultado = ComprovanteExecucaoFisicaDAO::buscarProjetos();
        } // fecha else


        // ========== INÍCIO PAGINAÇÃO ==========
        //criando a paginaçao
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($resultado); // dados a serem paginados

        // página atual e quantidade de ítens por página
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
        // ========== FIM PAGINAÇÃO ==========


        // manda para a visão
        $this->view->paginacao = $paginator;
        $this->view->qtd       = count($resultado); // quantidade
    } // fecha método aguardandoavaliacaoAction()



    /**
     * Método para buscar os documentos (comprovantes) do PRONAC 'Em Avaliação'
     * @access public
     * @param void
     * @return void
     */
    public function comprovantesemavaliacaoAction()
    {
        // autenticação e permissões zend (AMBIENTE MINC)
        // define as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // Técnico de Acompanhamento
        $PermissoesGrupo[] = 129; // Técnico de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // recebe o pronac via get
        $get    = Zend_Registry::get('get');
        $pronac = $get->pronac;

        try {
            // verifica se o pronac veio vazio
            if (empty($pronac)) {
                throw new Exception("Por favor, informe o PRONAC!");
            }
            // valida o número do pronac
            elseif (strlen($pronac) > 20) {
                throw new Exception("O Nº do PRONAC é inválido!");
            } else {
                // integração MODELO e VISÃO

                // busca o PRONAC no banco
                $resultPronac = ProjetoDAO::buscar($pronac);

                // caso o PRONAC não esteja cadastrado
                if (!$resultPronac) {
                    throw new Exception("Registro não encontrado!");
                }
                // caso o PRONAC esteja cadastrado, vai para a página de busca
                // dos seus documentos (comprovantes)
                else {
                    // manda o pronac para a visão
                    $this->view->buscarPronac = $resultPronac;

                    // pega o id do pronac
                    $idPronac = $resultPronac[0]->IdPRONAC;

                    // busca os documentos (comprovantes) do pronac
                    // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PARÂMETRO ==========
                    $resultComprovantes = ComprovanteExecucaoFisicaDAO::buscarDocumentos($idPronac);

                    // caso não existam comprovantes cadastrados
                    if (!$resultComprovantes) {
                        $this->view->message      = "Nenhum comprovante cadastrado para o PRONAC Nº " . $pronac . "!";
                        $this->view->message_type = "ALERT";
                    } else {
                        // busca o histórico dos comprovantes
                        for ($i = 0; $i < sizeof($resultComprovantes); $i++) :
                            // adiciona os comprovantes no novo array
                            $arrayComprovantes[$i] = array('comprovantes' => $resultComprovantes[$i]);

                        // histórico
                        $resultHistorico = ComprovanteExecucaoFisicaDAO::buscarHistorico(
                                $resultComprovantes[$i]->idComprovante,
                                $resultComprovantes[$i]->idComprovanteAnterior
                            );
                        // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PARÂMETRO ==========

                        // adiciona o histórico no seu respectivo comprovante
                        if (sizeof($resultHistorico) > 0) :
                                array_push($arrayComprovantes[$i], array('historicos' => $resultHistorico));
                        endif;
                        endfor;


                        // ========== INÍCIO PAGINAÇÃO ==========
                        //criando a paginaçao
                        Zend_Paginator::setDefaultScrollingStyle('Sliding');
                        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
                        $paginator = Zend_Paginator::factory($arrayComprovantes); // dados a serem paginados

                        // página atual e quantidade de ítens por página
                        $currentPage = $this->_getParam('page', 1);
                        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
                        // ========== FIM PAGINAÇÃO ==========


                        // manda os comprovantes e seu histórico para a visão
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
    } // fecha método comprovantesemavaliacaoAction()



    /**
     * Método para avaliar os comprovantes
     * @access public
     * @param void
     * @return void
     */
    public function avaliarcomprovanteAction()
    {
        // autenticação e permissões zend (AMBIENTE MINC)
        // define as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // Técnico de Acompanhamento
        $PermissoesGrupo[] = 129; // Técnico de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // caso o formulário seja enviado via post
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
                    // integração MODELO e VISÃO

                    // busca o PRONAC no banco
                    $resultPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante não estejam cadastrados
                    if (!$resultPronac || !$resultComprovante) {
                        parent::message("Registro não encontrado!", "execucaofisicadoprojeto/aguardandoavaliacao", "ERROR");
                    }
                    // caso o PRONAC esteja cadastrado
                    else {
                        // busca o comprovante anterior caso seja um comprovante substituído
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
                    throw new Exception("O Parecer é inválido! São permitidos entre 20 e 500 caracteres!");
                } else {
                    // atualiza o status para 'Em Aprovação'
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
        // quando a página é aberta
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
                    // integração MODELO e VISÃO

                    // busca o PRONAC no banco
                    $resultPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante não estejam cadastrados
                    if (!$resultPronac || !$resultComprovante) {
                        throw new Exception("Registro não encontrado!");
                    }
                    // caso o PRONAC esteja cadastrado
                    else {
                        // assim que o técnico clica em 'Avaliar', o status é alterado para 'Em Avaliação'
                        $dadosComprovante   = array('stParecerComprovante' => 'AV');
                        $alterarComprovante = ComprovanteExecucaoFisicaDAO::alterar($dadosComprovante, $doc);

                        // busca o comprovante anterior caso seja um comprovante substituído
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
     * Método para buscar projetos com comprovantes aguardando aprovação
     * @access public
     * @param void
     * @return void
     */
    public function aguardandoaprovacaoAction()
    {
        // autenticação e permissões zend (AMBIENTE MINC)
        // define as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // caso o formulário seja enviado via post
        // realiza a busca de acordo com os parâmetros enviados
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
                // valida o número do pronac
                if (!empty($pronac) && strlen($pronac) > 20) {
                    throw new Exception("O Nº do PRONAC é inválido!");
                }
                // valida as datas
                elseif (!empty($dt_inicio) && !Data::validarData(Data::dataBrasileira($dt_begin[0]))) {
                    throw new Exception("A data inicial é inválida!");
                } elseif (!empty($dt_fim) && !Data::validarData(Data::dataBrasileira($dt_end[0]))) {
                    throw new Exception("A data final é inválida!");
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


        // ========== INÍCIO PAGINAÇÃO ==========
        //criando a paginaçao
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($resultado); // dados a serem paginados

        // página atual e quantidade de ítens por página
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
        // ========== FIM PAGINAÇÃO ==========


        // manda para a visão
        $this->view->paginacao = $paginator;
        $this->view->qtd       = count($resultado); // quantidade
    } // fecha aguardandoaprovacaoAction()



    /**
     * Método para buscar os documentos (comprovantes) do PRONAC 'Em Avaliação'
     * @access public
     * @param void
     * @return void
     */
    public function comprovantesemaprovacaoAction()
    {
        // autenticação e permissões zend (AMBIENTE MINC)
        // define as permissões
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
            // valida o número do pronac
            elseif (strlen($pronac) > 20) {
                throw new Exception("O Nº do PRONAC é inválido!");
            } else {
                // integração MODELO e VISÃO

                // busca o PRONAC de acordo com o id no banco
                $resultPronac = ProjetoDAO::buscar($pronac);

                // caso o PRONAC não esteja cadastrado
                if (!$resultPronac) {
                    throw new Exception("Registro não encontrado!");
                }
                // caso o PRONAC esteja cadastrado, vai para a página de busca
                // dos seus documentos (comprovantes)
                else {
                    // manda o pronac para a visão
                    $this->view->buscarPronac = $resultPronac;

                    // pega o id do pronac
                    $idPronac = $resultPronac[0]->IdPRONAC;

                    // busca os documentos (comprovantes) do pronac
                    // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PARÂMETRO ==========
                    $resultComprovantes = ComprovanteExecucaoFisicaDAO::buscarDocumentos($idPronac);

                    // caso não existam comprovantes cadastrados
                    if (!$resultComprovantes) {
                        $this->view->message      = "Nenhum comprovante cadastrado para o PRONAC Nº " . $pronac . "!";
                        $this->view->message_type = "ALERT";
                    } else {
                        // busca o histórico dos comprovantes
                        for ($i = 0; $i < sizeof($resultComprovantes); $i++) :
                            // adiciona os comprovantes no novo array
                            $arrayComprovantes[$i] = array('comprovantes' => $resultComprovantes[$i]);

                        // histórico
                        $resultHistorico = ComprovanteExecucaoFisicaDAO::buscarHistorico(
                                $resultComprovantes[$i]->idComprovante,
                                $resultComprovantes[$i]->idComprovanteAnterior
                            );
                        // ========== LEMBRAR DE PASSAR DEPOIS O ID DO PROPONENTE COMO PARÂMETRO ==========

                        // adiciona o histórico no seu respectivo comprovante
                        if (sizeof($resultHistorico) > 0) :
                                array_push($arrayComprovantes[$i], array('historicos' => $resultHistorico));
                        endif;
                        endfor;


                        // ========== INÍCIO PAGINAÇÃO ==========
                        //criando a paginaçao
                        Zend_Paginator::setDefaultScrollingStyle('Sliding');
                        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
                        $paginator = Zend_Paginator::factory($arrayComprovantes); // dados a serem paginados

                        // página atual e quantidade de ítens por página
                        $currentPage = $this->_getParam('page', 1);
                        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(20);
                        // ========== FIM PAGINAÇÃO ==========


                        // manda os comprovantes e seu histórico para a visão
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
    } // fecha método comprovantesemaprovacaoAction()



    /**
     * Método para aprovar (deferir ou indeferir) os comprovantes
     * @access public
     * @param void
     * @return void
     */
    public function aprovarcomprovanteAction()
    {
        // autenticação e permissões zend (AMBIENTE MINC)
        // define as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);



        // caso o formulário seja enviado via post
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
                    // integração MODELO e VISÃO

                    // busca o PRONAC no banco
                    $resultPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante não estejam cadastrados
                    if (!$resultPronac || !$resultComprovante) {
                        parent::message("Registro não encontrado!", "execucaofisicadoprojeto/aguardandoaprovacao", "ERROR");
                    }
                    // caso o PRONAC esteja cadastrado
                    else {
                        // busca o comprovante anterior caso seja um comprovante substituído
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
                    throw new Exception("A Justificativa é inválida! São permitidos entre 20 e 500 caracteres!");
                } else {
                    // caso o comprovante seja DEFERIDO,
                    // coloca o último aprovado como deferido
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
                    // integração MODELO e VISÃO

                    // busca o PRONAC de acordo com o id no banco
                    $resultPronac = ProjetoDAO::buscar($pronac);

                    // busca o Comprovante de acordo com o id no banco
                    $resultComprovante = ComprovanteExecucaoFisicaDAO::buscar($resultPronac[0]->IdPRONAC, $doc);

                    // caso o PRONAC ou o Comprovante não estejam cadastrados
                    if (!$resultPronac || !$resultComprovante) {
                        throw new Exception("Registro não encontrado!");
                    }
                    // caso o PRONAC esteja cadastrado
                    else {
                        // busca o comprovante anterior caso seja um comprovante substituído
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
