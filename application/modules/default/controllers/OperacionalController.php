<?php
class OperacionalController extends MinC_Controller_Action_Abstract
{
    protected $idUsuario = null;
    private $codOrgaoSuperior = null;
    private $intTamPag = 10;
    private $idPerfil = 0;
    private $idOrgao = 0;

    public function init()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        if (empty($auth->getIdentity()->usu_codigo)) {
            $script = "
                <script>window.location.href = '".Zend_Controller_Front::getInstance()->getBaseUrl()."';</script>
            ";
            die($script);
        }

        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 90; // Protocolo - Documento
        $PermissoesGrupo[] = 91; // Protocolo - Recebimento
        $PermissoesGrupo[] = 92; // Tec. de Admissibilidade
        $PermissoesGrupo[] = 93; // Coordenador - Geral de An�lise (Ministro)
        $PermissoesGrupo[] = 94; // Parecerista
        $PermissoesGrupo[] = 96;  // Consulta Gerencial
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 103; // Coord. de Analise
        $PermissoesGrupo[] = 104; // Protocolo - Envio / Recebimento
        $PermissoesGrupo[] = 110; // Tec. de Analise
        $PermissoesGrupo[] = 114; // Coord. de Editais
        $PermissoesGrupo[] = 115; // Atendimento Representacoes
        $PermissoesGrupo[] = 119; // Presidente da CNIC
        $PermissoesGrupo[] = 120; // Coordenador da CNIC
        $PermissoesGrupo[] = 121; // Tec. de Acompanhamento
        $PermissoesGrupo[] = 122; // Coord. de Acompanhamento
        $PermissoesGrupo[] = 123; // Coord. Geral de Acompanhamento
        $PermissoesGrupo[] = 124; // Tec. de Presta��o de Contas
        $PermissoesGrupo[] = 125; // Coord. de Presta��o de Contas
        $PermissoesGrupo[] = 126; // Coord. Geral de Presta��o de Contas
        $PermissoesGrupo[] = 127; // Coord. Geral de An�lise
        $PermissoesGrupo[] = 128; // Tec. de Portaria
        $PermissoesGrupo[] = 131; // Coord. de Admissibilidade
        $PermissoesGrupo[] = 132; // Chefe de Divis�o
        $PermissoesGrupo[] = 135; // Tec. De Fiscaliza��o
        $PermissoesGrupo[] = 138; // Coord. de Avalia��o
        $PermissoesGrupo[] = 139; // Tec. de Avalia��o
        $PermissoesGrupo[] = 148; // Coord. de Avalia��o
        $PermissoesGrupo[] = 151; // Tec. de Avalia��o

        parent::perfil(1, $PermissoesGrupo);
        parent::init();

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->idPerfil = $GrupoAtivo->codGrupo;
        $this->idOrgao = $GrupoAtivo->codOrgao;

        $this->idUsuario = $auth->getIdentity()->usu_codigo;
        $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior))?$auth->getIdentity()->usu_org_max_superior:$auth->getIdentity()->usu_orgao;
    }


    /**
     * Metodo que monta grid de locais de realizacao
     * @param void
     * @return objeto
     */
    public function indexAction()
    {
        //RECUPERA OS LOCAIS DE REALIZACAO CADASTRADOS
        $this->forward("diagnostico");
        //        $arrDados = array();
        //        $this->montaTela("operacional/diagnostico.phtml", $arrDados);
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
                parent::message('Por favor, informe o campo CPF/CNPJ!', 'operacional/regularidade-proponente', 'ALERT');
            }
            if ($this->proponente == "PF" && !Validacao::validarCPF($cnpjcpf)) {
                parent::message('Por favor, informe um CPF v&aacute;lido!', 'operacional/regularidade-proponente', 'ALERT');
            }
            if ($this->proponente == "PJ" && !Validacao::validarCNPJ($cnpjcpf)) {
                parent::message('Por favor, informe um CNPJ v&aacute;lido!', 'operacional/regularidade-proponente', 'ALERT');
            }

            $this->view->cgccpf = $cnpjcpf;
            $agentes = new Agente_Model_DbTable_Agentes();
            $interessados = new Interessado();
            $buscaAgentes = $agentes->buscar(array('CNPJCPF = ?' => $cnpjcpf));

            $buscaInteressados = $interessados->buscar(array('CgcCpf = ?' => $cnpjcpf));

            if (!$buscaAgentes[0] or !$buscaInteressados[0]) {
                parent::message("O Agente n&atilde;o est&aacute; cadastrado!", 'operacional/regularidade-proponente', "ERROR");
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
            parent::message("Por favor, informe o campo CPF/CNPJ!", 'operacional/regularidade-proponente', "ERROR");
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
                parent::message('Por favor, informe o campo CPF/CNPJ!', 'operacional/regularidade-proponente', 'ALERT');
            }
            if ($this->proponente == "PF" && !Validacao::validarCPF($cnpjcpf)) {
                parent::message('Por favor, informe um CPF v&aacute;lido!', 'operacional/regularidade-proponente', 'ALERT');
            }
            if ($this->proponente == "PJ" && !Validacao::validarCNPJ($cnpjcpf)) {
                parent::message('Por favor, informe um CNPJ v&aacute;lido!', 'operacional/regularidade-proponente', 'ALERT');
            }

            $this->view->cgccpf = $cnpjcpf;
            $agentes = new Agente_Model_DbTable_Agentes();
            $interessados = new Interessado();
            $buscaAgentes = $agentes->buscar(array('CNPJCPF = ?' => $cnpjcpf));

            $buscaInteressados = $interessados->buscar(array('CgcCpf = ?' => $cnpjcpf));

            if (!$buscaAgentes[0] or !$buscaInteressados[0]) {
                parent::message("O Agente n&atilde;o est&aacute; cadastrado!", 'operacional/regularidade-proponente', "ERROR");
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
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        } else {
            parent::message("Por favor, informe o campo CPF/CNPJ!", 'operacional/regularidade-proponente', "ERROR");
        }
    }

    /**
     * Metodo que mostra tela de consulta
     * @param void
     * @return objeto
     */
    public function tabelasAction()
    {
        $get = Zend_Registry::get("get");

        if (!empty($get->consulta)) {
            //header("Content-Type: text/html; charset=ISO-8859-1");
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();

            $post = Zend_Registry::get("post");

            if ($get->consulta == "itens") {
                $tbl = new Produto();

                if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
                    //buscando os registros no banco de dados
                    $tamanho = -1;
                    $inicio = -1;
                    $pag = 0;
                    $totalPag = 0;
                    $fim = 0;
                    $rs = $tbl->buscar(array("stEstado = ?"=>0), array(), $tamanho, $inicio);

                    $this->forward(
                                            'preparar-xls-pdf',
                                            null,
                                            null,
                                            array(
                                                                                                'dados'=>$rs,
                                                                                                'view'=>'operacional/preparar-xls-pdf-itensproduto.phtml',
                                                                                                'tipo'=> $post->tipo
                                                                                                )
                                        );
                } else {
                    //controlando a paginacao
                    $this->intTamPag = 10;
                    $pag             = 1;
                    if (isset($post->pag)) {
                        $pag = $post->pag;
                    }
                    if (isset($post->tamPag)) {
                        $this->intTamPag = $post->tamPag;
                    }

                    $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
                    $fim    = $inicio + $this->intTamPag;

                    //Varifica se foi solicitado a ordena��o
                    if (!empty($post->ordenacao)) {
                        $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
                    } else {
                        $ordem = array('1 ASC');
                    }

                    $rs = $tbl->buscar(array("stEstado = ?"=>0), $ordem, null, null);

                    $total = count($rs);
                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
                    $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
                    if ($fim>$total) {
                        $fim = $total;
                    }

                    $rs2 = $tbl->buscar(array("stEstado = ?"=>0), $ordem, $tamanho, $inicio);

                    $arrDados = array(
                                            "dados"           => $rs2,
                                            "pag"             => $pag,
                                            "total" 		  => $total,
                                            "inicio" 		  => ($inicio+1),
                                            "fim"             => $fim,
                                            "totalPag"        => $totalPag,
                                            "parametrosBusca" => $_POST,
                                            "urlPaginacao"    => $this->_urlPadrao."/operacional/tabelas"

                                    );
                    $this->montaTela("operacional/itensproduto.phtml", $arrDados);
                }
            }

            if ($get->consulta == "documentos") {
                $tbl = new DocumentosExigidos();


                if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
                    //buscando os registros no banco de dados
                    $tamanho = -1;
                    $inicio = -1;
                    $pag = 0;
                    $totalPag = 0;
                    $fim = 0;
                    $arrBusca = array();
                    $arrBusca['Codigo > ?'] = 0;
                    $rs = $tbl->buscar($arrBusca, array(), $tamanho, $inicio);

                    $this->forward(
                                            'preparar-xls-pdf',
                                            null,
                                            null,
                                            array(
                                                                                                'dados'=>$rs,
                                                                                                'view'=>'operacional/preparar-xls-pdf-documentosexigidos.phtml',
                                                                                                'tipo'=> $post->tipo
                                                                                                )
                                        );
                } else {
                    //controlando a paginacao
                    $this->intTamPag = 10;
                    $pag             = 1;
                    if (isset($post->pag)) {
                        $pag = $post->pag;
                    }
                    if (isset($post->tamPag)) {
                        $this->intTamPag = $post->tamPag;
                    }

                    $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
                    $fim    = $inicio + $this->intTamPag;

                    //Varifica se foi solicitado a ordena��o
                    if (!empty($post->ordenacao)) {
                        $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
                    } else {
                        $ordem = array('1 ASC');
                    }

                    $rs    = $tbl->buscar();
                    $total = count($rs);

                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
                    $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $arrBusca = array();
                    $arrBusca['Codigo > ?'] = 0;
                    $rs2 = $tbl->buscar($arrBusca, $ordem, $tamanho, $inicio);

                    if ($rs2->count() > 0) {
                        foreach ($rs2 as $area) {
                            $idsAreas[] = $area->Area;
                        }
                        $tblArea = new Area();
                        $rsArea = $tblArea->buscar(array("Codigo IN (?)"=>$idsAreas));
                        $arrAreas = array(0=>array("NomeArea"=>"Todas as &Aacute;reas"));
                        foreach ($rsArea as $area) {
                            $arrAreas[$area->Codigo]["NomeArea"] = $area->Descricao;
                        }
                    }

                    $arrDados = array(
                        "dados"			  => $rs2,
                        "areas"			  => $arrAreas,
                                            "pag"             => $pag,
                                            "total" 		  => $total,
                                            "inicio" 		  => ($inicio+1),
                                            "fim"             => $fim,
                                            "totalPag"        => $totalPag,
                                            "parametrosBusca" => $_POST,
                                            "urlPaginacao"    => $this->_urlPadrao."/operacional/tabelas"

                                    );
                    $this->montaTela("operacional/documentosexigidos.phtml", $arrDados);
                }
            }

            if ($get->consulta == "produtos") {
                $tbl = new Produto();

                if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
                    //buscando os registros no banco de dados
                    $tamanho = -1;
                    $inicio = -1;
                    $pag = 0;
                    $totalPag = 0;
                    $fim = 0;
                    $arrBusca = array();
                    $rs = $tbl->buscar($arrBusca, array(), $tamanho, $inicio);

                    if ($rs->count() > 0) {
                        foreach ($rs as $produto) {
                            $idsOrgaos[] = $produto->Idorgao;
                        }
                        $tblOrgao = new Orgaos();
                        $rsOrgao = $tblOrgao->buscar(array("Codigo IN (?)"=>$idsOrgaos));
                        $arrOrgaos = array();
                        foreach ($rsOrgao as $orgao) {
                            $arrOrgaos[$orgao->Codigo]["SiglaOrgao"] = $orgao->Sigla;
                        }
                    }

                    $this->forward(
                                            'preparar-xls-pdf',
                                            null,
                                            null,
                                            array(
                                                                                                'dados'     =>$rs,
                                                                                                'view'      =>'operacional/preparar-xls-pdf-produtos.phtml',
                                                                                                'tipo'      => $post->tipo,
                                                                                                'orgaos'    => $arrOrgaos,
                                                                                                )
                                        );
                } else {
                    //controlando a paginacao
                    $this->intTamPag = 10;
                    $pag             = 1;
                    if (isset($post->pag)) {
                        $pag = $post->pag;
                    }
                    if (isset($post->tamPag)) {
                        $this->intTamPag = $post->tamPag;
                    }

                    $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
                    $fim    = $inicio + $this->intTamPag;

                    //Varifica se foi solicitado a ordena��o
                    if (!empty($post->ordenacao)) {
                        $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
                    } else {
                        $ordem = array('1 ASC');
                    }

                    $rs    = $tbl->buscar();
                    $total = count($rs);

                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
                    $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $arrBusca = array();
                    $rs2 = $tbl->buscar($arrBusca, $ordem, $tamanho, $inicio);

                    if ($rs2->count() > 0) {
                        foreach ($rs2 as $produto) {
                            $idsOrgaos[] = $produto->Idorgao;
                        }
                        $tblOrgao = new Orgaos();
                        $rsOrgao = $tblOrgao->buscar(array("Codigo IN (?)"=>$idsOrgaos));
                        $arrOrgaos = array();
                        foreach ($rsOrgao as $orgao) {
                            $arrOrgaos[$orgao->Codigo]["SiglaOrgao"] = $orgao->Sigla;
                        }
                    }

                    $arrDados = array(
                                            "dados"           => $rs2,
                                            "orgaos"          => $arrOrgaos,
                                            "pag"             => $pag,
                                            "total" 		  => $total,
                                            "inicio" 		  => ($inicio+1),
                                            "fim"             => $fim,
                                            "totalPag"        => $totalPag,
                                            "parametrosBusca" => $_POST,
                                            "urlPaginacao"    => $this->_urlPadrao."/operacional/tabelas"

                                    );
                    $this->montaTela("operacional/produtos.phtml", $arrDados);
                }
            }

            if ($get->consulta == "situacoes") {
                $tbl = new Situacao();
                if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
                    //buscando os registros no banco de dados
                    $tamanho = -1;
                    $inicio = -1;
                    $pag = 0;
                    $totalPag = 0;
                    $fim = 0;
                    $arrBusca = array();
                    $rs = $tbl->buscar($arrBusca, array(), $tamanho, $inicio);

                    $this->forward(
                                            'preparar-xls-pdf',
                                            null,
                                            null,
                                            array(
                                                                                                'dados'=>$rs,
                                                                                                'view'=>'operacional/preparar-xls-pdf-situacao.phtml',
                                                                                                'tipo'=> $post->tipo
                                                                                                )
                                        );
                } else {
                    //controlando a paginacao
                    $this->intTamPag = 10;
                    $pag             = 1;
                    if (isset($post->pag)) {
                        $pag = $post->pag;
                    }
                    if (isset($post->tamPag)) {
                        $this->intTamPag = $post->tamPag;
                    }

                    $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
                    $fim    = $inicio + $this->intTamPag;

                    //Varifica se foi solicitado a ordena��o
                    if (!empty($post->ordenacao)) {
                        $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
                    } else {
                        $ordem = array('1');
                    }

                    $rs    = $tbl->buscar();
                    $total = count($rs);

                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
                    $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $arrBusca = array();
                    $rs2 = $tbl->buscar($arrBusca, $ordem, $tamanho, $inicio);

                    $arrDados = array(
                                            "dados"			  => $rs2,
                                            "pag"             => $pag,
                                            "total" 		  => $total,
                                            "inicio" 		  => ($inicio+1),
                                            "fim"             => $fim,
                                            "totalPag"        => $totalPag,
                                            "parametrosBusca" => $_POST,
                                            "urlPaginacao"    => $this->_urlPadrao."/operacional/tabelas"
                                    );
                    $this->montaTela("operacional/situacoes.phtml", $arrDados);
                }
            }

            if ($get->consulta == "segmentos") {
                $tbl = new Segmento();

                if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
                    //buscando os registros no banco de dados
                    $tamanho = -1;
                    $inicio = -1;
                    $pag = 0;
                    $totalPag = 0;
                    $fim = 0;
                    $arrBusca = array();
                    $rs = $tbl->buscar($arrBusca, array(), $tamanho, $inicio);

                    if ($rs->count() > 0) {
                        foreach ($rs as $segmento) {
                            $idsOrgaos[] = $segmento->idOrgao;
                        }
                        $tblOrgao = new Orgaos();
                        $rsOrgao = $tblOrgao->buscar(array("Codigo IN (?)"=>$idsOrgaos));
                        $arrOrgaos = array();
                        foreach ($rsOrgao as $orgao) {
                            $arrOrgaos[$orgao->Codigo]["SiglaOrgao"] = $orgao->Sigla;
                        }
                    }

                    $this->forward(
                                            'preparar-xls-pdf',
                                            null,
                                            null,
                                            array(
                                                                                                'dados'         =>$rs,
                                                                                                'view'          =>'operacional/preparar-xls-pdf-segmentos.phtml',
                                                                                                'tipo'          => $post->tipo,
                                                                                                'orgaos'        => $arrOrgaos,
                                                                                                )
                                        );
                } else {
                    //controlando a paginacao
                    $this->intTamPag = 10;
                    $pag             = 1;
                    if (isset($post->pag)) {
                        $pag = $post->pag;
                    }
                    if (isset($post->tamPag)) {
                        $this->intTamPag = $post->tamPag;
                    }

                    $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
                    $fim    = $inicio + $this->intTamPag;

                    //Varifica se foi solicitado a ordena��o
                    if (!empty($post->ordenacao)) {
                        $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
                    } else {
                        $ordem = array('1 ASC');
                    }

                    $rs    = $tbl->buscar();
                    $total = count($rs);

                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
                    $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $arrBusca = array();
                    $rs2 = $tbl->buscar($arrBusca, $ordem, $tamanho, $inicio);

                    if ($rs2->count() > 0) {
                        foreach ($rs2 as $segmento) {
                            $idsOrgaos[] = $segmento->idOrgao;
                        }
                        $tblOrgao = new Orgaos();
                        $rsOrgao = $tblOrgao->buscar(array("Codigo IN (?)"=>$idsOrgaos));
                        $arrOrgaos = array();
                        foreach ($rsOrgao as $orgao) {
                            $arrOrgaos[$orgao->Codigo]["SiglaOrgao"] = $orgao->Sigla;
                        }
                    }

                    $arrDados = array(
                                            "dados"           => $rs2,
                                            "orgaos"          => $arrOrgaos,
                                            "pag"             => $pag,
                                            "total" 		  => $total,
                                            "inicio" 		  => ($inicio+1),
                                            "fim"             => $fim,
                                            "totalPag"        => $totalPag,
                                            "parametrosBusca" => $_POST,
                                            "urlPaginacao"    => $this->_urlPadrao."/operacional/tabelas"

                                    );
                    $this->montaTela("operacional/segmentos.phtml", $arrDados);
                }
            }

            if ($get->consulta == "tiposdocumento") {
                $tbl = new tbTipoDocumento();

                if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
                    //buscando os registros no banco de dados
                    $tamanho = -1;
                    $inicio = -1;
                    $pag = 0;
                    $totalPag = 0;
                    $fim = 0;
                    $arrBusca = array();
                    $rs = $tbl->buscar($arrBusca, array(), $tamanho, $inicio);

                    $this->forward(
                                            'preparar-xls-pdf',
                                            null,
                                            null,
                                            array(
                                                                                                'dados'=>$rs,
                                                                                                'view'=>'operacional/preparar-xls-pdf-tiposdocumento.phtml',
                                                                                                'tipo'=> $post->tipo
                                                                                                )
                                        );
                } else {
                    //controlando a paginacao
                    $this->intTamPag = 10;
                    $pag             = 1;
                    if (isset($post->pag)) {
                        $pag = $post->pag;
                    }
                    if (isset($post->tamPag)) {
                        $this->intTamPag = $post->tamPag;
                    }

                    $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
                    $fim    = $inicio + $this->intTamPag;

                    //Varifica se foi solicitado a ordena��o
                    if (!empty($post->ordenacao)) {
                        $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
                    } else {
                        $ordem = array('2 ASC');
                    }

                    $rs    = $tbl->buscar();
                    $total = count($rs);

                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
                    $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $arrBusca = array();
                    $rs2 = $tbl->buscar($arrBusca, $ordem, $tamanho, $inicio);

                    $arrDados = array(
                                            "dados"           => $rs2,
                                            "pag"             => $pag,
                                            "total" 		  => $total,
                                            "inicio" 		  => ($inicio+1),
                                            "fim"             => $fim,
                                            "totalPag"        => $totalPag,
                                            "parametrosBusca" => $_POST,
                                            "urlPaginacao"    => $this->_urlPadrao."/operacional/tabelas"

                                    );
                    $this->montaTela("operacional/tiposdocumento.phtml", $arrDados);
                }
            }

            if ($get->consulta == "pecasdivulgacao") {
                $tbl = new Verificacao();

                if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
                    //buscando os registros no banco de dados
                    $tamanho = -1;
                    $inicio = -1;
                    $pag = 0;
                    $totalPag = 0;
                    $fim = 0;
                    $arrBusca = array();
                    $arrBusca = array("idTipo = ?"=>1, "stEstado = ?"=>1);
                    $rs = $tbl->buscar($arrBusca, array(), $tamanho, $inicio);

                    $this->forward(
                                            'preparar-xls-pdf',
                                            null,
                                            null,
                                            array(
                                                                                                'dados'=>$rs,
                                                                                                'view'=>'operacional/preparar-xls-pdf-pecasdivulgacao.phtml',
                                                                                                'tipo'=> $post->tipo
                                                                                                )
                                        );
                } else {
                    //controlando a paginacao
                    $this->intTamPag = 10;
                    $pag             = 1;
                    if (isset($post->pag)) {
                        $pag = $post->pag;
                    }
                    if (isset($post->tamPag)) {
                        $this->intTamPag = $post->tamPag;
                    }

                    $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
                    $fim    = $inicio + $this->intTamPag;

                    //Varifica se foi solicitado a ordena��o
                    if (!empty($post->ordenacao)) {
                        $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
                    } else {
                        $ordem = array('1 ASC');
                    }

                    $rs    = $tbl->buscar(array("idTipo = ?"=>1, "stEstado = ?"=>1));
                    $total = count($rs);

                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
                    $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $arrBusca = array("idTipo = ?"=>1, "stEstado = ?"=>1);
                    $rs2 = $tbl->buscar($arrBusca, $ordem, $tamanho, $inicio);

                    $arrDados = array(
                                            "dados"		=> $rs2,
                                            "pag"               => $pag,
                                            "total"             => $total,
                                            "inicio" 		=> ($inicio+1),
                                            "fim"               => $fim,
                                            "totalPag"          => $totalPag,
                                            "parametrosBusca"   => $_POST,
                                            "urlPaginacao"      => $this->_urlPadrao."/operacional/tabelas"

                                    );
                    $this->montaTela("operacional/pecasdivulgacao.phtml", $arrDados);
                }
            }

            if ($get->consulta == "veiculosdivulgacao") {
                $tbl = new Verificacao();

                if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
                    //buscando os registros no banco de dados
                    $tamanho = -1;
                    $inicio = -1;
                    $pag = 0;
                    $totalPag = 0;
                    $fim = 0;
                    $arrBusca = array();
                    $arrBusca = array("idTipo = ?"=>2, "stEstado = ?"=>1);
                    $rs = $tbl->buscar($arrBusca, array(), $tamanho, $inicio);

                    $this->forward(
                                            'preparar-xls-pdf',
                                            null,
                                            null,
                                            array(
                                                                                                'dados'=>$rs,
                                                                                                'view'=>'operacional/preparar-xls-pdf-veiculosdivulgacao.phtml',
                                                                                                'tipo'=> $post->tipo
                                                                                                )
                                        );
                } else {
                    //controlando a paginacao
                    $this->intTamPag = 10;
                    $pag             = 1;
                    if (isset($post->pag)) {
                        $pag = $post->pag;
                    }
                    if (isset($post->tamPag)) {
                        $this->intTamPag = $post->tamPag;
                    }

                    $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
                    $fim    = $inicio + $this->intTamPag;

                    //Varifica se foi solicitado a ordena��o
                    if (!empty($post->ordenacao)) {
                        $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
                    } else {
                        $ordem = array('1 ASC');
                    }

                    $rs    = $tbl->buscar(array("idTipo = ?"=>2, "stEstado = ?"=>1));
                    $total = count($rs);

                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
                    $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
                    if ($fim>$total) {
                        $fim = $total;
                    }
                    $arrBusca = array("idTipo = ?"=>2, "stEstado = ?"=>1);
                    $rs2 = $tbl->buscar($arrBusca, $ordem, $tamanho, $inicio);

                    $arrDados = array(
                                            "dados"		=> $rs2,
                                            "pag"               => $pag,
                                            "total"             => $total,
                                            "inicio" 		=> ($inicio+1),
                                            "fim"               => $fim,
                                            "totalPag"          => $totalPag,
                                            "parametrosBusca"   => $_POST,
                                            "urlPaginacao"      => $this->_urlPadrao."/operacional/tabelas"
                                    );

                    $this->montaTela("operacional/veiculosdivulgacao.phtml", $arrDados);
                }
            }
        }
    }

    /**
     * Metodo que mostra tela de consulta
     * @param void
     * @return objeto
     */
    public function resultadoTabelaAction()
    {
        $produto = $this->_request->getParam('codigo');
        $where['p.idProduto = ?'] = $produto;
        $tbl = new tbItensPlanilhaProduto();
        $rs = $tbl->buscaItemProduto($where);
        $this->view->dados = $rs;
    }

    public function pedidoProrrogacaoAction()
    {
    }

    public function resultadoPedidoProrrogacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $post   = Zend_Registry::get('post');

        $pag = 1;
        //$get = Zend_Registry::get('get');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        if (isset($post->tamPag)) {
            $this->intTamPag = $post->tamPag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;

        $arrBusca = array();
        if (!empty($post->dtPedido) || $post->tpDtPedido != '') {
            if ($post->tpDtPedido == "igual") {
                $arrBusca['DtPedido >= ?'] = ConverteData($post->dtPedido, 13)." 00:00:00";
                $arrBusca['DtPedido <= ?'] = ConverteData($post->dtPedido, 13)." 23:59:59";
            } elseif ($post->tpDtPedido == "maior") {
                $arrBusca['DtPedido >= ?'] = ConverteData($post->dtPedido, 13)." 00:00:00";
            } elseif ($post->tpDtPedido == "menor") {
                $arrBusca['DtPedido <= ?'] = ConverteData($post->dtPedido, 13)." 00:00:00";
            } elseif ($post->tpDtPedido == "OT") {
                $arrBusca['DtPedido = ?'] = date("Y-m-").(date("d")-1)." 00:00:00";
            } elseif ($post->tpDtPedido == "U7") {
                $arrBusca['DtPedido > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $arrBusca['DtPedido < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtPedido == "SP") {
                $arrBusca['DtPedido > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $arrBusca['DtPedido < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtPedido == "MM") {
                $arrBusca['DtPedido > ?'] = date("Y-m-01")." 00:00:00";
                $arrBusca['DtPedido < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtPedido == "UM") {
                $arrBusca['DtPedido > ?'] = date("Y-").(date("m")-1)."-01 00:00:00";
                $arrBusca['DtPedido < ?'] = date("Y-").(date("m")-1)."-31 23:59:59";
            } else {
                $arrBusca['DtPedido > ?'] = ConverteData($post->dtPedido, 13)." 00:00:00";

                if ($post->dtPedido_Final != "") {
                    $arrBusca['DtPedido < ?'] = ConverteData($post->dtPedido_Final, 13)." 23:59:59";
                }
            }
        }

        if (!empty($post->dtInicio) || $post->tpDtInicio != '') {
            if ($post->tpDtInicio == "igual") {
                $arrBusca['DtInicio >= ?'] = ConverteData($post->dtInicio, 13)." 00:00:00";
                $arrBusca['DtInicio <= ?'] = ConverteData($post->dtInicio, 13)." 23:59:59";
            } elseif ($post->tpDtInicio == "maior") {
                $arrBusca['DtInicio >= ?'] = ConverteData($post->dtInicio, 13)." 00:00:00";
            } elseif ($post->tpDtInicio == "menor") {
                $arrBusca['DtInicio <= ?'] = ConverteData($post->dtInicio, 13)." 00:00:00";
            } elseif ($post->tpDtInicio == "OT") {
                $arrBusca['DtInicio = ?'] = date("Y-m-").(date("d")-1)." 00:00:00";
            } elseif ($post->tpDtInicio == "U7") {
                $arrBusca['DtInicio > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $arrBusca['DtInicio < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtInicio == "SP") {
                $arrBusca['DtInicio > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $arrBusca['DtInicio < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtInicio == "MM") {
                $arrBusca['DtInicio > ?'] = date("Y-m-01")." 00:00:00";
                $arrBusca['DtInicio < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtInicio == "UM") {
                $arrBusca['DtInicio > ?'] = date("Y-").(date("m")-1)."-01 00:00:00";
                $arrBusca['DtInicio < ?'] = date("Y-").(date("m")-1)."-31 23:59:59";
            } else {
                $arrBusca['DtInicio > ?'] = ConverteData($post->dtInicio, 13)." 00:00:00";

                if ($post->dtInicio_Final != "") {
                    $arrBusca['DtInicio < ?'] = ConverteData($post->dtInicio_Final, 13)." 23:59:59";
                }
            }
        }

        if (!empty($post->baixado)) {
            $arrBusca["Atendimento = ?"] = $post->baixado;
        }

        if (!empty($post->diligenciado)) {
            $arrBusca["Diligenciado = ?"] = $post->diligenciado;
        }

        $tblProrrogacao = new Prorrogacao();


        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $total = 0;
            $fim = 0;
            $rs = $tblProrrogacao->buscar($arrBusca, array(), $tamanho, $inicio);
            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-pedido-prorrogacao.phtml',
                                                                            'tipo'=> $post->tipo
                                                                            )
                    );
        } else {
            $total = $tblProrrogacao->pegaTotal($arrBusca);
            $total = $total["total"];
            //if ($fim>$total) $fim = $total;
            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            //Varifica se foi solicitado a ordena��o
            if (!empty($post->ordenacao)) {
                $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
            } else {
                $ordem = array('idProrrogacao DESC');
            }

            $rs = $tblProrrogacao->buscar($arrBusca, $ordem, $tamanho, $inicio);
        }
        $this->view->prorrogacoes 	 = $rs;
        $this->view->pag 			 = $pag;
        $this->view->total 			 = $total;
        $this->view->inicio 		 = ($inicio+1);
        $this->view->fim             = $fim;
        $this->view->totalPag        = $totalPag;
        $this->view->parametrosBusca = $_POST;
        $this->view->urlPaginacao    = $this->_urlPadrao."/operacional/pedido-prorrogacao";
    }

    public function agenciaBancariaAction()
    {
        $tblBancos 			= new Bancos();
        $rsBancos  			= $tblBancos->buscar();
        $this->view->bancos = $rsBancos;

        $tblUf 			 = new Uf();
        $rsUf  			 = $tblUf->buscar(array(), array("Sigla ASC"));
        $this->view->ufs = $rsUf;

        $tblMecanismo 			= new Mecanismo();
        $rsMecanismo 			= $tblMecanismo->buscar(array("Status = ?"=>1), array("Descricao ASC"));
        $this->view->mecanismos = $rsMecanismo;

        $tblArea 		   = new Area();
        $rsArea 		   = $tblArea->buscar(array(), array("Descricao ASC"));
        $this->view->areas = $rsArea;
    }

    public function resultadoAgenciaBancariaAction()
    {
        $this->_helper->layout->disableLayout();

        $post     = Zend_Registry::get('post');
        $arrBusca = array();

        if (!empty($post->banco)) {
            $arrBusca["c.Banco = ?"] = $post->banco;
        }

        if (!empty($post->uf)) {
            $tblUf = new Uf();
            $rsUf = $tblUf->buscar(array("idUF = ?"=>$post->uf))->current();
            $arrBusca["a.Uf = ?"] = $rsUf->Sigla;
        }

        if (!empty($post->cidade)) {
            $tblMunicipio = new Municipios();
            $rsMunicipio = $tblMunicipio->buscar(array("idMunicipioIBGE = ?"=>$post->cidade))->current();
            $arrBusca["a.Cidade = ?"] = $rsMunicipio->Descricao;
        }

        if (!empty($post->mecanismo)) {
            $arrBusca["c.Mecanismo = ?"] = $post->mecanismo;
        }

        if (!empty($post->area)) {
            $arrBusca["p.Area = ?"] = $post->area;
        }

        if (!empty($post->tipoPessoa)) {
            $arrBusca["i.tipoPessoa = ?"] = $post->tipoPessoa;
        }

        if (!empty($post->agencia)) {
            $arrBusca["c.Agencia = ?"] = retiraMascara($post->agencia);
        }

        if (!empty($post->dtLoteRemessaCB) || $post->tpDtLoteRemessaCB != '') {
            if ($post->tpDtLoteRemessaCB == "igual") {
                $arrBusca['DtLoteRemessaCB >= ?'] = ConverteData($post->dtLoteRemessaCB, 13)." 00:00:00";
                $arrBusca['DtLoteRemessaCB <= ?'] = ConverteData($post->dtLoteRemessaCB, 13)." 23:59:59";
            } elseif ($post->tpDtLoteRemessaCB == "maior") {
                $arrBusca['DtLoteRemessaCB >= ?'] = ConverteData($post->dtLoteRemessaCB, 13)." 00:00:00";
            } elseif ($post->tpDtLoteRemessaCB == "menor") {
                $arrBusca['DtLoteRemessaCB <= ?'] = ConverteData($post->dtLoteRemessaCB, 13)." 00:00:00";
            } elseif ($post->tpDtLoteRemessaCB == "OT") {
                $arrBusca['DtLoteRemessaCB = ?'] = date("Y-m-").(date("d")-1)." 00:00:00";
            } elseif ($post->tpDtLoteRemessaCB == "U7") {
                $arrBusca['DtLoteRemessaCB > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $arrBusca['DtLoteRemessaCB < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtLoteRemessaCB == "SP") {
                $arrBusca['DtLoteRemessaCB > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $arrBusca['DtLoteRemessaCB < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtLoteRemessaCB == "MM") {
                $arrBusca['DtLoteRemessaCB > ?'] = date("Y-m-01")." 00:00:00";
                $arrBusca['DtLoteRemessaCB < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtLoteRemessaCB == "UM") {
                $arrBusca['DtLoteRemessaCB > ?'] = date("Y-").(date("m")-1)."-01 00:00:00";
                $arrBusca['DtLoteRemessaCB < ?'] = date("Y-").(date("m")-1)."-31 23:59:59";
            } else {
                $arrBusca['DtLoteRemessaCB > ?'] = ConverteData($post->dtLoteRemessaCB, 13)." 00:00:00";

                if ($post->dtLoteRemessaCB_Final != "") {
                    $arrBusca['DtLoteRemessaCB < ?'] = ConverteData($post->dtLoteRemessaCB_Final, 13)." 23:59:59";
                }
            }
        }

        if (!empty($post->dtLoteRemessaCL) || $post->tpDtLoteRemessaCL != '') {
            if ($post->tpDtLoteRemessaCL == "igual") {
                $arrBusca['DtLoteRemessaCL >= ?'] = ConverteData($post->dtLoteRemessaCL, 13)." 00:00:00";
                $arrBusca['DtLoteRemessaCL <= ?'] = ConverteData($post->dtLoteRemessaCL, 13)." 23:59:59";
            } elseif ($post->tpDtLoteRemessaCL == "maior") {
                $arrBusca['DtLoteRemessaCL >= ?'] = ConverteData($post->dtLoteRemessaCL, 13)." 00:00:00";
            } elseif ($post->tpDtLoteRemessaCL == "menor") {
                $arrBusca['DtLoteRemessaCL <= ?'] = ConverteData($post->dtLoteRemessaCL, 13)." 00:00:00";
            } elseif ($post->tpDtLoteRemessaCL == "OT") {
                $arrBusca['DtLoteRemessaCL = ?'] = date("Y-m-").(date("d")-1)." 00:00:00";
            } elseif ($post->tpDtLoteRemessaCL == "U7") {
                $arrBusca['DtLoteRemessaCL > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $arrBusca['DtLoteRemessaCL < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtLoteRemessaCL == "SP") {
                $arrBusca['DtLoteRemessaCL > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $arrBusca['DtLoteRemessaCL < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtLoteRemessaCL == "MM") {
                $arrBusca['DtLoteRemessaCL > ?'] = date("Y-m-01")." 00:00:00";
                $arrBusca['DtLoteRemessaCL < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($post->tpDtLoteRemessaCL == "UM") {
                $arrBusca['DtLoteRemessaCL > ?'] = date("Y-").(date("m")-1)."-01 00:00:00";
                $arrBusca['DtLoteRemessaCL < ?'] = date("Y-").(date("m")-1)."-31 23:59:59";
            } else {
                $arrBusca['DtLoteRemessaCL > ?'] = ConverteData($post->dtLoteRemessaCL, 13)." 00:00:00";

                if ($post->dtLoteRemessaCL_Final != "") {
                    $arrBusca['DtLoteRemessaCL < ?'] = ConverteData($post->dtLoteRemessaCL_Final, 13)." 23:59:59";
                }
            }
        }
        $tbl   = new ContaBancaria();
        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $total = 0;
            $fim = 0;
            $rs = $tbl->buscar($arrBusca, array(), $tamanho, $inicio);
            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-agencia-bancaria.phtml',
                                                                            'tipo'=> $post->tipo
                                                                            )
                    );
        } else {
            $pag = 1;
            //$get = Zend_Registry::get('get');
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim    = $inicio + $this->intTamPag;

            $total = $tbl->pegaTotal($arrBusca);
            $total = $total["total"];

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            //Varifica se foi solicitado a ordena��o
            if (!empty($post->ordenacao)) {
                $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
            } else {
                $ordem = array('Descricao ASC');
            }

            $rs = $tbl->buscar($arrBusca, $ordem, $tamanho, $inicio);
        }
        $this->view->contasBancarias = $rs;
        $this->view->pag 			 = $pag;
        $this->view->total 			 = $total;
        $this->view->inicio 		 = ($inicio+1);
        $this->view->fim 			 = $fim;
        $this->view->totalPag 		 = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function extratoPautaReuniaoCnicAction()
    {
        $tblOrgao 			= new Orgaos();
        $rsOrgao 			= $tblOrgao->buscar(array(), array("Sigla ASC"));
        $this->view->orgaos = $rsOrgao;

        $tblArea 		   = new Area();
        $rsArea 		   = $tblArea->buscar(array(), array("Descricao ASC"));
        $this->view->areas = $rsArea;

        $tblSituacao 		   = new Situacao();
        $rsSituacao 		   = $tblSituacao->buscar(array("AreaAtuacao = ?"=>"R", "StatusProjeto = ?"=>1), array("Descricao ASC"));
        $this->view->situacoes = $rsSituacao;
    }

    public function resultadoExtratoPautaReuniaoCnicAction()
    {
        $this->_helper->layout->disableLayout();

        $post     = Zend_Registry::get('post');
        $arrBusca = array("Mecanismo = ?"=>1);

        if (!empty($post->banco)) {
            $arrBusca["c.Banco = ?"] = $post->banco;
        }
        if (!empty($post->nrReuniao)) {
            $arrBusca["pa.NumeroReuniao = ?"] = $post->nrReuniao;
        }
        if (!empty($post->orgao)) {
            $arrBusca["o.Codigo = ?"] = $post->orgao;
        }
        if (!empty($post->tipoParecer)) {
            $arrBusca["pa.TipoParecer = ?"] = $post->tipoParecer;
        }
        if (!empty($post->area)) {
            $arrBusca["a.Codigo = ?"] = $post->area;
        }
        if (!empty($post->situacao)) {
            $arrBusca["s.Codigo = ?"] = $post->situacao;
        }

        foreach ($_POST["visaoAgente"] as $campo) {
            $arrCampos = explode("_", $campo);
            if ($arrCampos[0] == "cmpsOrd") {
                if (substr($arrCampos[1], 0, 1) != "#") {
                    $campos[] = $arrCampos[1];
                }
            }
        }
        //        foreach($_POST["visaoAgente"] as $campo){
        //            $arrCampos = explode("_", $campo);
        //            if($arrCampos[0] == "cmpsOrd"){
        //                if(substr($arrCampos[1], 0, 1) != "#"){
        //                    $campos[] = $arrCampos[1];
        //                }
        //            }
        //        }
        //$campos = implode(", ", $campos);
        $tbl   = new Projetos();

        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $total = 0;
            $fim = 0;
            $rs = $tbl->buscarProjetosPautaReuniao($arrBusca, array(new Zend_Db_expr("Area ASC")), $tamanho, $inicio);

            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-extrato-pauta-reuniao-cnic.phtml',
                                                                            'tipo'=> $post->tipo
                                                                            )
                    );
        } else {
            $pag = 1;
            //$get = Zend_Registry::get('get');
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }

            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim    = $inicio + $this->intTamPag;


            $total = $tbl->pegaTotalProjetosPautaReuniao($arrBusca);
            $total = $total["total"];

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            $rs = $tbl->buscarProjetosPautaReuniao($arrBusca, array(new Zend_Db_expr("Area ASC")), $tamanho, $inicio);
        }
        $this->view->projetos 		 = $rs;
        $this->view->pag 		 = $pag;
        $this->view->total 		 = $total;
        $this->view->inicio 		 = ($inicio+1);
        $this->view->fim 		 = $fim;
        $this->view->totalPag 		 = $totalPag;
        $this->view->parametrosBusca     = $_POST;
    }

    public function tramitacaoAction()
    {
        $tblOrgao = new Orgaos();
        $rsOrgao  = $tblOrgao->buscar(array(), array("Sigla ASC"));

        $this->view->orgaos = $rsOrgao;
    }

    public function resultadoTramitacaoDocumentosAction()
    {
        $this->_helper->layout->disableLayout();

        $post = Zend_Registry::get('post');

        //recuperando filtros do POST
        //		$arrBusca = array("hd.idDocumento <> ?"=>0, "hd.stEstado = ?"=>1, "hd.idDocumento is NOT NULL"=>'');
        $arrBusca = array();
        if (!empty($post->origem)) {
            if ($post->formaorigem == "1") {
                $arrBusca["oo.Codigo = ?"] = $post->origem;
            } else {
                $arrBusca["oo.Codigo <> ?"] = $post->origem;
            }
        }
        if (!empty($post->destino)) {
            if ($post->formadestino == "1") {
                $arrBusca["od.Codigo = ?"] = $post->destino;
            } else {
                $arrBusca["od.Codigo <> ?"] = $post->destino;
            }
        }
        if (!empty($post->lote)) {
            $arrBusca["hd.idLote = ?"] = $post->lote;
        }
        if (!empty($post->situacao)) {
            if ($post->formasituacao == "1") {
                $arrBusca["hd.Acao = ?"] = $post->situacao;
            } else {
                $arrBusca["hd.Acao <> ?"] = $post->situacao;
            }
        }
        if (!empty($post->correio)) {
            $arrBusca["d.CodigoCorreio = ?"] = $post->correio;
        }

        //Varifica se foi solicitado a ordena��o
        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        } else {
            $ordem = array('7');
        }

        //montando parametros de busca dos campos de data
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtDocumento", "dtDocumento", "d.dtDocumento", "dtDocumento_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtEnvio", "dtEnvio", "hd.dtTramitacaoEnvio", "dtEnvio_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtRecebido", "dtRecebido", "hd.dtTramitacaoRecebida", "dtRecebido_Final", $arrBusca);

        //instanciando modelo referente a tabela tbHistoricoDocumento
        $tblTbHistoricoDocumento = new tbHistoricoDocumento();

        //pegando o total de registros na tabela, considerando os filtros passados
        $total = $tblTbHistoricoDocumento->pegaTotalTramitacaoDocumento($arrBusca)->current()->toArray();
        $total = $total["total"];


        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $fim = 0;
            $rs = $tblTbHistoricoDocumento->buscarTramitacaoDocumento($arrBusca, $ordem, $tamanho, $inicio);
            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-documentos.phtml',
                                                                            'tipo'=> $post->tipo
                                                                            )
                    );
        } else {
            //controlando a paginacao
            $pag = 1;
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim = $inicio + $this->intTamPag;

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            //buscando os registros no banco de dados
            $rs = $tblTbHistoricoDocumento->buscarTramitacaoDocumento($arrBusca, $ordem, $tamanho, $inicio);
        }


        //mandando variaveis para a view
        $this->view->tramitacoes 	 = $rs;
        $this->view->pag 			 = $pag;
        $this->view->total 			 = $total;
        $this->view->inicio 		 = ($inicio+1);
        $this->view->fim 			 = $fim;
        $this->view->totalPag 		 = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function resultadoTramitacaoProjetosAction()
    {
        $this->_helper->layout->disableLayout();

        $post = Zend_Registry::get('post');

        //recuperando filtros do POST
        $arrBusca = array("hd.stEstado = ?"=>1, "((hd.idDocumento is NULL) OR (hd.idDocumento = ?))"=>0);
        if (!empty($post->origem)) {
            if ($post->formaorigem == "1") {
                $arrBusca["oo.Codigo = ?"] = $post->origem;
            } else {
                $arrBusca["oo.Codigo <> ?"] = $post->origem;
            }
        }
        if (!empty($post->destino)) {
            if ($post->formadestino == "1") {
                $arrBusca["od.Codigo = ?"] = $post->destino;
            } else {
                $arrBusca["od.Codigo <> ?"] = $post->destino;
            }
        }
        if (!empty($post->lote)) {
            $arrBusca["hd.idLote = ?"] = $post->lote;
        }
        if (!empty($post->situacao)) {
            if ($post->formasituacao == "1") {
                $arrBusca["hd.Acao = ?"] = $post->situacao;
            } else {
                $arrBusca["hd.Acao <> ?"] = $post->situacao;
            }
        }
        if (!empty($post->correio)) {
            $arrBusca["d.CodigoCorreio = ?"] = $post->correio;
        }

        //Varifica se foi solicitado a ordena��o
        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        } else {
            $ordem = array('7');
        }

        //montando parametros de busca dos campos de data
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtDocumento", "dtDocumento", "d.dtDocumento", "dtDocumento_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtEnvio", "dtEnvio", "hd.dtTramitacaoEnvio", "dtEnvio_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtRecebido", "dtRecebido", "hd.dtTramitacaoRecebida", "dtRecebido_Final", $arrBusca);

        //instanciando modelo referente a tabela tbHistoricoDocumento
        $tblTbHistoricoDocumento = new tbHistoricoDocumento();

        //pegando o total de registros na tabela, considerando os filtros passados
        $total = $tblTbHistoricoDocumento->pegaTotalCompleto($arrBusca)->current()->toArray();
        $total = $total["total"];

        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $fim = 0;
            $rs = $tblTbHistoricoDocumento->buscarCompleto($arrBusca, $ordem, $tamanho, $inicio);
            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-projetos.phtml',
                                                                            'tipo'=> $post->tipo
                                                                            )
                    );
        } else {
            //controlando a paginacao
            $pag = 1;
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }

            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim    = $inicio + $this->intTamPag;

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            //buscando os registros no banco de dados
            $rs = $tblTbHistoricoDocumento->buscarCompleto($arrBusca, $ordem, $tamanho, $inicio);
        }
        //mandando variaveis para a view
        $this->view->tramitacoes 	 = $rs;
        $this->view->pag 			 = $pag;
        $this->view->total 			 = $total;
        $this->view->inicio 		 = ($inicio+1);
        $this->view->fim 			 = $fim;
        $this->view->totalPag        = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function editaisMincAction()
    {
        $tblOrgao = new Orgaos();
        $rsOrgao  = $tblOrgao->buscar(array(), array("Sigla ASC"));
        $this->view->orgaos = $rsOrgao;

        $tblClassificacao = new tbClassificaDocumento();
        $rsClassificacao  = $tblClassificacao->buscar(array(), array("dsClassificaDocumento ASC"));
        $this->view->classificacoes = $rsClassificacao;

        $tblAvaliacao = new Verificacao();
        $rsAvaliacao  = $tblAvaliacao->buscar(array("idTipo = ?"=>4));
        $this->view->avaliacoes = $rsAvaliacao;

        $tblFundoSetorial = new Verificacao();
        $rsFundoSetorial  = $tblFundoSetorial->buscar(array("idTipo = ?"=>15));
        $this->view->fundossetoriais = $rsFundoSetorial;

        $tblUf = new Uf();
        $rsUf  = $tblUf->buscar(array(), array("Descricao ASC"));
        $this->view->ufs = $rsUf;

        $arrRegioes = array();
        foreach ($rsUf as $item) {
            $arrRegioes[] = $item->Regiao;
        }
        $arrRegioes = array_unique($arrRegioes);
        $this->view->regioes = $arrRegioes;
    }

    public function resultadoEditaisMincAction()
    {
        $this->_helper->layout->disableLayout();
        $post   = Zend_Registry::get('post');

        //recuperando filtros do POST
        $arrBusca = array();
        if ($post->fundo != "") {
            $arrBusca["vr2.idVerificacao = ?"] = $post->fundo;
        }
        if ($post->classificacao != "") {
            $arrBusca["cl.idClassificaDocumento = ?"] = $post->classificacao;
        }
        if ($post->edital != "") {
            $arrBusca["e.idEdital = ?"] = $post->edital;
        }
        if ($post->unidade != "") {
            $arrBusca["o.Codigo = ?"] = $post->unidade;
        }
        if ($post->estado != "") {
            $arrBusca["x.ConformidadeOK = ?"] = $post->estado;
        }
        if ($post->avaliacao != "") {
            $arrBusca["mv.movimentacao = ?"] = $post->avaliacao;
        }
        if ($post->uf != "") {
            $arrBusca["uf.idUF = ?"] = $post->uf;
        }
        if ($post->uf == "" && $post->regiao != "") {
            $arrBusca["uf.Regiao = ?"] = $post->regiao;
        }

        //montando parametros de busca dos campos de data
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtCadastro", "dtCadastro", "p.dtAceite", "dtCadastro_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtEnvio", "dtEnvio", "x1.DtEnvio", "dtEnvio_Final", $arrBusca);

        //instanciando modelo referente a tabela PreProjeto
        $tbl = new Proposta_Model_DbTable_PreProjeto();

        //pegando o total de registros na tabela, considerando os filtros passados
        $total = $tbl->propostasPorEdital($arrBusca, array(), null, null, true);

        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $fim = 0;
            $ordem = array("25 ASC", "18 ASC", "15 ASC", "21 ASC", "24 ASC");
            $rs = $tbl->propostasPorEdital($arrBusca, $ordem, $tamanho, $inicio);

            $arr = array();
            foreach ($rs as $item) {
                $arr[$item->FundoNome][$item->dsClassificaDocumento][$item->Edital][] = $item;
            }

            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-resultado-editais-minc.phtml',
                                                                            'tipo'=> $post->tipo
                                                                            )
                    );
        } else {
            //controlando a paginacao
            $this->intTamPag = 10;
            $pag = 1;
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }

            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim 	= $inicio + $this->intTamPag;

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            $ordem = array("25 ASC", "18 ASC", "15 ASC", "21 ASC", "24 ASC");
            //        if($inicio > 0){
            //            $ordem = array("dsClassificaDocumento ASC", "Edital ASC");
            //        }
            $rs = $tbl->propostasPorEdital($arrBusca, $ordem, $tamanho, $inicio);

            $arr = array();
            foreach ($rs as $item) {
                $arr[$item->FundoNome][$item->dsClassificaDocumento][$item->Edital][] = $item;
            }
        }
        $this->view->registros 		 = $arr;
        $this->view->pag 			 = $pag;
        $this->view->total 			 = $total;
        $this->view->inicio 		 = ($inicio+1);
        $this->view->fim 			 = $fim;
        $this->view->totalPag 		 = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function diagnosticoAction()
    {
        $tblOrgao = new Orgaos();
        $rsOrgao  = $tblOrgao->buscar(array(), array("Sigla ASC"));
        $this->view->orgaos = $rsOrgao;

        $tblSituacao = new Situacao();
        $rsSituacao  = $tblSituacao->buscar(array(), array("Codigo ASC"));
        $this->view->situacoes = $rsSituacao;
    }


    public function resultadoDiagnosticoAction()
    {
        $this->_helper->layout->disableLayout();

        $tbl = new Projetos();
        $post = Zend_Registry::get('post');

        // verifica se foi solicitado a ordena��o
        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        } else {
            $ordem = array('1 ASC');
        }

        if (isset($post->gerarPdfTotal) && !empty($post->gerarPdfTotal) && $post->gerarPdfTotal == 'PdfTotal') {
            $arrBusca = array();
            if ($post->orgao != "") {
                $arrBusca["pr.Orgao = ?"] = $post->orgao;
            }
            if ($post->situacao != "") {
                $arrBusca["pr.Situacao = ?"] = $post->situacao;
            }

            $rs    = $tbl->diagnostico($arrBusca, $ordem);
            $this->forward('gerar-pdf-xls-diagnostico', null, null, array('valores'=>$rs,'gerar'=>'html'));
        } elseif (isset($post->xls) && !empty($post->xls) && $post->xls == 'xls') {
            $arrBusca = array();
            if ($post->orgao != "") {
                $arrBusca["pr.Orgao = ?"] = $post->orgao;
            }
            if ($post->situacao != "") {
                $arrBusca["pr.Situacao = ?"] = $post->situacao;
            }

            $rs    = $tbl->diagnostico($arrBusca, $ordem);
            $this->forward('gerar-pdf-xls-diagnostico', null, null, array('valores'=>$rs,'gerar'=>'xls'));
        }
        //$arrBusca = array("o.Status = ?"=>0, "s.StatusProjeto <> ?"=>0, "o.idSecretaria = ?"=>$this->codOrgaoSuperior);
        $arrBusca = array();
        if ($post->orgao != "") {
            $arrBusca["pr.Orgao = ?"] = $post->orgao;
        }
        if ($post->situacao != "") {
            $arrBusca["pr.Situacao = ?"] = $post->situacao;
        }

        //controlando a paginacao
//        $this->intTamPag = 10;
        //		$pag             = 1;
        //		if (isset($post->pag)) $pag = $post->pag;
        //		if (isset($post->tamPag)) $this->intTamPag = $post->tamPag;

        //		$rs = $tbl->diagnostico($arrBusca);
        //		$total = count($rs);

        //		$inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        //		$fim    = $inicio + $this->intTamPag;

        //		if ($fim>$total) $fim = $total;
        //		$totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        //		$tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        //		if ($fim>$total) $fim = $total;

        //		$rs = $tbl->diagnostico($arrBusca,$ordem, $tamanho, $inicio);
        $rs = $tbl->diagnostico($arrBusca, $ordem);
        $this->view->projetos        = $rs;
        //		$this->view->pag 			 = $pag;
        //		$this->view->total 			 = $total;
        //		$this->view->inicio 		 = ($inicio+1);
        //		$this->view->fim 			 = $fim;
        //		$this->view->totalPag 		 = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function gerarPdfXlsDiagnosticoAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
        $this->_response->clearHeaders();
        $dados = $this->getAllParams();
        $this->view->projetos = $dados;
        $this->view->gerar = $dados['gerar'];
    }

    public function extratoPautaIntercambioAction()
    {
        $tblOrgao = new Orgaos();
        $rsOrgao  = $tblOrgao->buscar(array(), array("Sigla ASC"));
        $this->view->orgaos = $rsOrgao;

        $tblClassificacao = new tbClassificaDocumento();
        $rsClassificacao  = $tblClassificacao->buscar(array(), array("dsClassificaDocumento ASC"));
        $this->view->classificacoes = $rsClassificacao;

        $tblAvaliacao = new Verificacao();
        $rsAvaliacao  = $tblAvaliacao->buscar(array("idTipo = ?"=>4));
        $this->view->avaliacoes = $rsAvaliacao;

        $tblFundoSetorial = new Verificacao();
        $rsFundoSetorial  = $tblFundoSetorial->buscar(array("idTipo = ?"=>15));
        $this->view->fundossetoriais = $rsFundoSetorial;

        $tblUf = new Uf();
        $rsUf  = $tblUf->buscar(array(), array("Descricao ASC"));
        $this->view->ufs = $rsUf;

        $arrRegioes = array();
        foreach ($rsUf as $item) {
            $arrRegioes[] = $item->Regiao;
        }
        $arrRegioes = array_unique($arrRegioes);
        $this->view->regioes = $arrRegioes;
    }

    public function resultadoExtratoPautaIntercambioAction()
    {
        $this->_helper->layout->disableLayout();

        $tbl   = new Projetos();

        $post = Zend_Registry::get('post');

        //recuperando filtros do POST
        $arrBusca = array("pr.Mecanismo = ?"=>2, "pr.Orgao = ?"=>254, "pr.Modalidade = ?"=>"02"/*, "pr.Situacao = ?"=>"C23"*/);
        if ($post->fundo != "") {
            $arrBusca["vr2.idVerificacao = ?"] = $post->fundo;
        }
        if ($post->classificacao != "") {
            $arrBusca["cl.idClassificaDocumento = ?"] = $post->classificacao;
        }
        if ($post->edital != "") {
            $arrBusca["e.idEdital = ?"] = $post->edital;
        }
        if ($post->uf != "") {
            $arrBusca["uf.CodUfIbge = ?"] = $post->uf;
        }

        //Varifica se foi solicitado a ordena��o
        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        } else {
            $ordem = array("18 ASC", "4 ASC");
        }

        //montando parametros de busca dos campos de data
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtEnvio", "dtEnvio", "x1.DtEnvio", "dtEnvio_Final", $arrBusca);

        foreach ($_POST["visaoAgente"] as $campo) {
            $arrCampos = explode("_", $campo);
            if ($arrCampos[0] == "cmpsOrd") {
                if (substr($arrCampos[1], 0, 1) != "#") {
                    $campos[] = $arrCampos[1];
                }
            }
        }
        //        foreach($_POST["visaoAgente"] as $campo){
        //            $arrCampos = explode("_", $campo);
        //            if($arrCampos[0] == "cmpsOrd"){
        //                if(substr($arrCampos[1], 0, 1) != "#"){
        //                    $campos[] = $arrCampos[1];
        //                }
        //            }
        //        }
        //$campos = implode(", ", $campos);
        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $total = 0;
            $fim = 0;
            $rs = $tbl->extratoPautaItercambio($arrBusca, $ordem, $tamanho, $inicio);
            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-extrato-pauta-intercambio.phtml',
                                                                            'tipo'=> $post->tipo
                                                                            )
                    );
        } else {
            $pag = 1;
            //$get = Zend_Registry::get('get');
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }

            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim    = $inicio + $this->intTamPag;


            $total = $tbl->extratoPautaItercambio($arrBusca, $ordem, null, null, true);

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            $rs = $tbl->extratoPautaItercambio($arrBusca, $ordem, $tamanho, $inicio);
        }
        $this->view->projetos 		 = $rs;
        $this->view->pag 			 = $pag;
        $this->view->total 			 = $total;
        $this->view->inicio 		 = ($inicio+1);
        $this->view->fim 			 = $fim;
        $this->view->totalPag 		 = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function resultadoProjetosPorSituacaoAction()
    {
        $this->_helper->layout->disableLayout();

        $get = Zend_Registry::get('get');

        $filtro = $this->_request->getParam('situacao');

        $tbl = new Projetos();
        $rs  = $tbl->buscar(array("Situacao = ?"=>$filtro));

        $this->view->dados = $rs;
    }

    public function exibirresultadoprojetoporsituacaoAction()
    {
        // configura��o o php.ini para 100MB
        @set_time_limit(0);
        @ini_set('mssql.textsize', 10485760000);
        @ini_set('mssql.textlimit', 10485760000);
        @ini_set('mssql.timeout', 10485760000);
        @ini_set('memory_limit', '2048M');
        @ini_set('upload_max_filesize', '100M');

        $filtro = array(
                    'situacao' => $this->_request->getParam('situacao'),
                    'orgao' => $this->_request->getParam('orgao')
                );
        $pag    = $this->_request->getParam('pag');

        $tbl = new Projetos();

        // ========== IN�CIO PAGINA��O ==========
        $get = Zend_Registry::get('get');
        if (isset($pag)) {
            $pagina = $pag;
        } else {
            $pagina = 1;
        }
        $qtPag = 20;
        $rs = $tbl->exibirResultadoProjetoSituacao($filtro, $qtPag, $pagina);
        $total = $tbl->buscar(array('Situacao = ?' => $filtro['situacao']));
        //$buscaAprovados = $aprovacao->buscarAprovados($inicio,$fim);

        // ========== FIM PAGINA��O ==========

        $this->view->dados       = $rs;
        $this->view->qtdRegistro = ceil(count($total)/$qtPag); // quantidade de comprovantes
    }

    public function imprimirresultadoprojetoporsituacaoAction()
    {
        // configura��o o php.ini para 100MB
        @set_time_limit(0);
        @ini_set('mssql.textsize', 10485760000);
        @ini_set('mssql.textlimit', 10485760000);
        @ini_set('mssql.timeout', 10485760000);
        @ini_set('memory_limit', '2048M');
        @ini_set('upload_max_filesize', '100M');

        $filtro = array(
                    'situacao' => $this->_request->getParam('situacao'),
                    'orgao' => $this->_request->getParam('orgao')
                );

        $tbl = new Projetos();

        // ========== IN�CIO PAGINA��O ==========
        $get = Zend_Registry::get('get');
        $rs = $tbl->imprimirResultadoProjetoSituacao($filtro);
        $total = $tbl->buscar(array('Situacao = ?' => $filtro['situacao']));
        // ========== FIM PAGINA��O ==========

        $this->view->dados = $rs;
        $this->_helper->layout->disableLayout();// Desabilita o Zend Layout
    }

    /*===========================================================================*/
    /*====================== ABAIXO - METODOS DA CNIC ===========================*/
    /*===========================================================================*/

    public function projetosEmPautaReuniaoCnicAction()
    {
        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $rsAgentes  = $tblAgentes->BuscarComponente();
        $this->view->agentes = $rsAgentes;

        $tblArea = new Area();
        $rsArea  = $tblArea->buscar(array(), array("Descricao ASC"));
        $this->view->areas = $rsArea;
    }

    public function resultadoProjetosEmPautaReuniaoCnicAction()
    {
        $this->_helper->layout->disableLayout();

        $post = Zend_Registry::get('post');

        $arrBusca = array();
        if (!empty($post->pronac)) {
            $arrBusca["pr.AnoProjeto + pr.Sequencial = ?"] = $post->pronac;
        }

        if (!empty($post->nomeProjeto)) {
            $projeto = utf8_decode($post->nomeProjeto);
            if ($post->tipoPesqNomeProjeto == 'QC') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["pr.NomeProjeto like (?)"] = "%{$projeto}%";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'EIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["pr.NomeProjeto = ?"] = "{$projeto}";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'IIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["pr.NomeProjeto like (?)"] = "{$projeto}%";
                }
            }
        }

        if (!empty($post->componente)) {
            if ($post->tipoPesqComponente == 'EIG') {
                if (!empty($post->componente)) {
                    $arrBusca["dpc.idAgente = ?"] = $post->componente;
                }
            } elseif ($post->tipoPesqComponente == 'DI') {
                if (!empty($post->componente)) {
                    $arrBusca["dpc.idAgente <> ?"] = $post->componente;
                }
            }
        }

        if (!empty($post->area)) {
            if ($post->tipoPesqArea == 'EIG') {
                if (!empty($post->area)) {
                    $arrBusca["ar.Codigo = ?"] = $post->area;
                }
            } elseif ($post->tipoPesqArea == 'DI') {
                if (!empty($post->area)) {
                    $arrBusca["ar.Codigo <> ?"] = $post->area;
                }
            }
        }

        if (!empty($post->segmento)) {
            $arrBusca["pr.Segmento = ?"] = $post->segmento;
        }

        $statusAnalise = null;
        if (!empty($post->statusAnalise)) {
            if ($post->statusAnalise == "SA") {
                $statusAnalise = "Analisado";
            } else {
                $statusAnalise = "N�o analisado";
            }
        }

        //BUSCA PARA DATAS
        //$arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtDistribuicao", "dtDistribuicao", "dpc.DtDistribuicao", "dtDistribuicao_Final", $arrBusca);


        $tbl   = new tbDistribuicaoProjetoComissao();

        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho = -1;
            $inicio = -1;
            $pag = 0;
            $totalPag = 0;
            $total = 0;
            $fim = 0;

            $arrQtdeProjetosAnalisado    = array('total'=>0);
            $arrQtdeProjetosNaoAnalisado = array('total'=>0);
            $qtdeAnalisado 				 = 1;
            $qtdeNaoAnalisado 			 = 1;
            $qtdeNovaAnalisado 			 = 0;
            $qtdeNovaNaoAnalisado 		 = 0;

            $ordem = array("1 ASC", "23 ASC");
            $rs  = $tbl->buscarProjetoEmPauta($arrBusca, $ordem, $tamanho, $inicio, null, $statusAnalise);
            $arr = $tbl->buscarProjetoEmPauta($arrBusca, $ordem)->toArray();

            foreach ($arr as $projetos) {
                $analise = $projetos['Analise'];
                $componente = $projetos['Componente'];

                if ($analise == "Analisado") {
                    $arrQtdeProjetosAnalisado['total'] = $arrQtdeProjetosAnalisado['total'] + 1;

                    if (array_key_exists($componente, $arrQtdeProjetosAnalisado)) {
                        $qtdeNovaAnalisado = $arrQtdeProjetosAnalisado[$componente];
                        $qtdeNovaAnalisado = $qtdeNovaAnalisado + 1;
                        $arrQtdeProjetosAnalisado[$componente] = $qtdeNovaAnalisado;
                    } else {
                        $arrQtdeProjetosAnalisado[$componente] = $qtdeAnalisado;
                    }
                } else {
                    $arrQtdeProjetosNaoAnalisado['total'] = $arrQtdeProjetosNaoAnalisado['total'] + 1;

                    if (array_key_exists($componente, $arrQtdeProjetosNaoAnalisado)) {
                        $qtdeNovaNaoAnalisado = $arrQtdeProjetosNaoAnalisado[$componente];
                        $qtdeNovaNaoAnalisado = $qtdeNovaNaoAnalisado + 1;
                        $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNovaNaoAnalisado;
                    } else {
                        $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNaoAnalisado;
                    }
                }
            }

            $landscape = (sizeof($post->visaoAgente) > 4)?true:false;

            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-projetos-em-pauta-reuniao-cnic.phtml',
                                                                            'tipo'=> $post->tipo,
                                                                            'orientacao'=>$landscape
                                                                            )
                    );
        } else {
            $pag = 1;
            //$get = Zend_Registry::get('get');
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }

            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim    = $inicio + $this->intTamPag;


            $total = $tbl->buscarProjetoEmPauta($arrBusca, array(), null, null, true, $statusAnalise);

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            $ordem = array("1 ASC", "23 ASC");
            if (!empty($post->ordenacao)) {
                $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
            }

            $rs  = $tbl->buscarProjetoEmPauta($arrBusca, $ordem, $tamanho, $inicio, null, $statusAnalise);
            $arr = $tbl->buscarProjetoEmPauta($arrBusca, $ordem)->toArray();

            $arrQtdeProjetosAnalisado    = array('total'=>0);
            $arrQtdeProjetosNaoAnalisado = array('total'=>0);
            $qtdeAnalisado 				 = 1;
            $qtdeNaoAnalisado 			 = 1;
            $qtdeNovaAnalisado 			 = 0;
            $qtdeNovaNaoAnalisado 		 = 0;

            //UTIL PARA GERACAO DO GRAFICO
            foreach ($arr as $projetos) {
                $analise = $projetos['Analise'];
                $componente = $projetos['Componente'];

                if ($analise == "Analisado") {
                    $arrQtdeProjetosAnalisado['total'] = $arrQtdeProjetosAnalisado['total'] + 1;

                    if (array_key_exists($componente, $arrQtdeProjetosAnalisado)) {
                        $qtdeNovaAnalisado = $arrQtdeProjetosAnalisado[$componente];
                        $qtdeNovaAnalisado = $qtdeNovaAnalisado + 1;
                        $arrQtdeProjetosAnalisado[$componente] = $qtdeNovaAnalisado;
                    } else {
                        $arrQtdeProjetosAnalisado[$componente] = $qtdeAnalisado;
                    }
                } else {
                    $arrQtdeProjetosNaoAnalisado['total'] = $arrQtdeProjetosNaoAnalisado['total'] + 1;

                    if (array_key_exists($componente, $arrQtdeProjetosNaoAnalisado)) {
                        $qtdeNovaNaoAnalisado = $arrQtdeProjetosNaoAnalisado[$componente];
                        $qtdeNovaNaoAnalisado = $qtdeNovaNaoAnalisado + 1;
                        $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNovaNaoAnalisado;
                    } else {
                        $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNaoAnalisado;
                    }
                }
            }
        }


        if (isset($post->gerarResumo)) {
            $rs = $tbl->buscarProjetoEmPauta($arrBusca, $ordem);
            $this->resumoProjetosAvaliadosCnic($rs);
        }

        $this->view->projetos                     = $rs;
        $this->view->arrQtdeProjetosAnalisados    = $arrQtdeProjetosAnalisado;
        $this->view->arrQtdeProjetosNaoAnalisados = $arrQtdeProjetosNaoAnalisado;
        $this->view->pag                          = $pag;
        $this->view->total 			  = $total;
        $this->view->inicio                       = ($inicio+1);
        $this->view->fim 			  = $fim;
        $this->view->totalPag 			  = $totalPag;
        $this->view->parametrosBusca 		  = $_POST;
    }

    public function resumoProjetosEmPautaReuniaoCnic($recordset)
    {
        $arrQtdeProjetosAnalisado    = array('total'=>0);
        $arrQtdeProjetosNaoAnalisado = array('total'=>0);
        $qtdeAnalisado 				 = 1;
        $qtdeNaoAnalisado 			 = 1;
        $qtdeNovaAnalisado 			 = 0;
        $qtdeNovaNaoAnalisado 		 = 0;
        foreach ($recordset as $projetos) {
            $analise = $projetos['Analise'];
            $componente = $projetos['Componente'];

            if ($analise == "Analisado") {
                $arrQtdeProjetosAnalisado['total'] = $arrQtdeProjetosAnalisado['total'] + 1;

                if (array_key_exists($componente, $arrQtdeProjetosAnalisado)) {
                    $qtdeNovaAnalisado = $arrQtdeProjetosAnalisado[$componente];
                    $qtdeNovaAnalisado = $qtdeNovaAnalisado + 1;
                    $arrQtdeProjetosAnalisado[$componente] = $qtdeNovaAnalisado;
                } else {
                    $arrQtdeProjetosAnalisado[$componente] = $qtdeAnalisado;
                }
            } else {
                $arrQtdeProjetosNaoAnalisado['total'] = $arrQtdeProjetosNaoAnalisado['total'] + 1;

                if (array_key_exists($componente, $arrQtdeProjetosNaoAnalisado)) {
                    $qtdeNovaNaoAnalisado = $arrQtdeProjetosNaoAnalisado[$componente];
                    $qtdeNovaNaoAnalisado = $qtdeNovaNaoAnalisado + 1;
                    $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNovaNaoAnalisado;
                } else {
                    $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNaoAnalisado;
                }
            }
        }

        $arrDados = array(
                            "projetosAnalisados"=>$arrQtdeProjetosAnalisado,
                            "projetosNaoAnalisados"=>$arrQtdeProjetosNaoAnalisado,
                            "urlGerarGrafico"=>$this->_urlPadrao."/operacional/grafico-projetos-em-pauta-reuniao-cnic"
                            );
        $this->montaTela("operacional/resumo-projetos-em-pauta-reuniao-cnic.phtml", $arrDados);
        return;
    }

    public function graficoProjetosEmPautaReuniaoCnicAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $grafico = new Grafico($_POST["cgTipoGrafico"]);
        $grafico->setTituloGrafico("Projetos em pauta");
        $grafico->setTituloEixoXY("Componente da Comiss&atilde;o", "Registros");
        $grafico->configurar($_POST);

        /*======== INICIO PREPARA OS TITULOS DO ARRAY - GRAFICO ==========*/
        if (isset($_POST["todos"]) || isset($_POST["Analisados"])) {
            $aux = array();
            foreach ($_POST as $chave=>$valor) {
                $aux = explode("gValA_", $chave);
                if (isset($aux[1]) && $aux[1] != "total") {
                    $componente = $aux[1];
                    $componente = str_replace("_", " ", utf8_decode($componente));
                    $titulos[] = $componente;
                }
            }
        }

        if (isset($_POST["todos"]) || isset($_POST["NaoAnalisados"])) {
            $aux = array();
            foreach ($_POST as $chave=>$valor) {
                $aux = explode("gValNA_", $chave);
                if (isset($aux[1]) && $aux[1] != "total") {
                    $componente = $aux[1];
                    $componente = str_replace("_", " ", utf8_decode($componente));
                    $titulos[] = $componente;
                }
            }
        }
        /*======= FIM PREPARA OS TITULOS DO ARRAY - GRAFICO ==========*/

        /*======== INICIO PREPARA VALORES DO ARRAY - GRAFICO ==========*/
        foreach ($_POST as $chave => $valor) {
            $aux = explode("gValA_", $chave);
            $nome = @str_replace("_", " ", utf8_decode($aux[1]));
            $arrAvaliados[$nome] = $valor;
        }

        foreach ($_POST as $chave => $valor) {
            $aux = explode("gValNA_", $chave);
            $nome = @str_replace("_", " ", utf8_decode($aux[1]));
            $arrNaoAvaliados[$nome] = $valor;
        }

        //RETIRA NOMES REPETIDOS
        $titulos = array_unique($titulos);

        /*======== FIM PREPARA VALORES DO ARRAY - GRAFICO ==========*/


        foreach ($titulos as $titulo) {
            if (isset($_POST["todos"]) || isset($_POST["Analisados"])) {
                if (key_exists($titulo, $arrAvaliados)) {
                    $arrAvaliadosFinal[]=$arrAvaliados[$titulo];
                } else {
                    $arrAvaliadosFinal[]=0;
                }
            }
            if (isset($_POST["todos"]) || isset($_POST["NaoAnalisados"])) {
                if (key_exists($titulo, $arrNaoAvaliados)) {
                    $arrNaoAvaliadosFinal[]=$arrNaoAvaliados[$titulo];
                } else {
                    $arrNaoAvaliadosFinal[]=0;
                }
            }
        }

        $grafico = new Grafico($_POST["cgTipoGrafico"]);
        $grafico->setTituloGrafico("Registros");
        $grafico->setTituloEixoXY("Avaliacao", "Registros");
        @$grafico->configurar($_POST);

        if (isset($_POST["todos"]) || isset($_POST["Analisados"])) {
            $grafico->addDados($arrAvaliadosFinal, "Analisados");
        }
        if (isset($_POST["todos"]) || isset($_POST["NaoAnalisados"])) {
            $grafico->addDados($arrNaoAvaliadosFinal, "Nao analisados");
        }
        $grafico->setTituloItens($titulos);
        @$grafico->gerar();
        $this->_helper->viewRenderer->setNoRender(true);
    }


    public function projetosAvaliadosCnicAction()
    {
        $tblTbReuniao = new tbreuniao();
        $rsTbReuniao  = $tblTbReuniao->buscar(array("NrReuniao >= ?"=>184), array("NrReuniao DESC"));
        $this->view->reunioes = $rsTbReuniao;

        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $rsAgentes  = $tblAgentes->BuscarComponente();
        $this->view->agentes = $rsAgentes;

        $tblArea = new Area();
        $rsArea  = $tblArea->buscar(array(), array("Descricao ASC"));
        $this->view->areas = $rsArea;

        $tblSituacao = new Situacao();
        $rsSituacao  = $tblSituacao->buscar(array("StatusProjeto = ?"=>1), array("Descricao ASC"));
        $this->view->situacoes = $rsSituacao;

        $tblOrgao = new Orgaos();
        $rsOrgao  = $tblOrgao->buscar(array("Vinculo = ?"=>1, "Status = ?"=>0));
        $this->view->orgaos = $rsOrgao;
    }

    public function resultadoProjetosAvaliadosCnicAction()
    {
        $this->_helper->layout->disableLayout();

        $post = Zend_Registry::get('post');
        $this->intTamPag = 30;

        $arrBusca = array("d.stPrincipal = ?"=>1, "d.stEstado = ?"=>0, "z.stDistribuicao = ?"=>"A");
        if (!empty($post->nrReuniao)) {
            $arrBusca["t.idNrReuniao = ?"] = $post->nrReuniao;
        }
        if (!empty($post->pronac)) {
            $arrBusca["p.AnoProjeto + p.Sequencial = ?"] = $post->pronac;
        }
        if (!empty($post->nomeProjeto)) {
            $projeto = utf8_decode($post->nomeProjeto);
            if ($post->tipoPesqNomeProjeto == 'QC') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["p.NomeProjeto like (?)"] = "%{$projeto}%";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'EIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["p.NomeProjeto = ?"] = "{$projeto}";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'IIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["p.NomeProjeto like (?)"] = "{$projeto}%";
                }
            }
        }

        if (!empty($post->area)) {
            if ($post->tipoPesqArea == 'EIG') {
                if (!empty($post->area)) {
                    $arrBusca["a.Codigo = ?"] = $post->area;
                }
            } elseif ($post->tipoPesqArea == 'DI') {
                if (!empty($post->area)) {
                    $arrBusca["a.Codigo <> ?"] = $post->area;
                }
            }
        }

        if (!empty($post->segmento)) {
            $arrBusca["p.Segmento = ?"] = $post->segmento;
        }
        if (!empty($post->situacao)) {
            $arrBusca["s.Codigo = ?"] = $post->situacao;
        }
        if (!empty($post->resultadoAvaliacao)) {
            $arrBusca["stAnalise = ?"] = $post->resultadoAvaliacao;
        }

        if (!empty($post->proponente)) {
            $proponente = utf8_decode($post->proponente);
            if ($post->tipoPesqProponente == 'QC') {
                if (!empty($post->proponente)) {
                    $arrBusca["n.Descricao like (?)"] = "%{$proponente}%";
                }
            } elseif ($post->tipoPesqProponente == 'EIG') {
                if (!empty($post->proponente)) {
                    $arrBusca["n.Descricao = ?"] = "{$proponente}";
                }
            } elseif ($post->tipoPesqProponente == 'IIG') {
                if (!empty($post->proponente)) {
                    $arrBusca["n.Descricao like (?)"] = "{$proponente}%";
                }
            }
        }

        if (!empty($post->componente)) {
            if ($post->tipoPesqComponente == 'EIG') {
                if (!empty($post->componente)) {
                    $arrBusca["z.idAgente = ?"] = $post->componente;
                }
            } elseif ($post->tipoPesqComponente == 'DI') {
                if (!empty($post->componente)) {
                    $arrBusca["z.idAgente <> ?"] = $post->componente;
                }
            }
        }

        if (!empty($post->orgao)) {
            if ($post->tipoPesqEntidade == 'EIG') {
                if (!empty($post->orgao)) {
                    $arrBusca["d.idOrgao = ?"] = $post->orgao;
                }
            } elseif ($post->tipoPesqEntidade == 'DI') {
                if (!empty($post->orgao)) {
                    $arrBusca["d.idOrgao <> ?"] = $post->orgao;
                }
            }
        }

        $tbl   = new tbPauta();

        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho    = -1;
            $inicio     = -1;
            $pag        = 0;
            $totalPag   = 0;
            $total      = 0;
            $fim        = 0;

            $ordem      = array("12 ASC");
            $rs         = $tbl->buscarProjetosAvaliados($arrBusca, $ordem, $tamanho, $inicio);


            $arr                = $tbl->buscarProjetosAvaliados($arrBusca, $ordem);
            $arrQtdeRegistros   = array();
            $qtde 		= 1;
            foreach ($arr as $registros) {
                $situacao = $registros['DescSituacao'];

                if (array_key_exists($situacao, $arrQtdeRegistros)) {
                    $qtdeNova = $arrQtdeRegistros[$situacao];
                    $qtdeNova = $qtdeNova + 1;
                    $arrQtdeRegistros[$situacao] = $qtdeNova;
                } else {
                    $arrQtdeRegistros[$situacao] = $qtde;
                }
            }

            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-projetos-avaliados-cnic.phtml',
                                                                            'tipo'=> $post->tipo,
                                                                            )
                    );
        } else {
            $pag = 1;
            //$get = Zend_Registry::get('get');
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }

            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim    = $inicio + $this->intTamPag;


            $total = $tbl->buscarProjetosAvaliados($arrBusca, array(), null, null, true);

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            $ordem = array("12 ASC");
            if (!empty($post->ordenacao)) {
                $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
            }

            $rs = $tbl->buscarProjetosAvaliados($arrBusca, $ordem, $tamanho, $inicio);

            //UTIL PARA GERACAO DO GRAFICO
            $arr 			  = $tbl->buscarProjetosAvaliados($arrBusca, $ordem);
            $arrQtdeRegistros = array();
            $qtde 			  = 1;
            foreach ($arr as $registros) {
                $situacao = $registros['DescSituacao'];

                if (array_key_exists($situacao, $arrQtdeRegistros)) {
                    $qtdeNova = $arrQtdeRegistros[$situacao];
                    $qtdeNova = $qtdeNova + 1;
                    $arrQtdeRegistros[$situacao] = $qtdeNova;
                } else {
                    $arrQtdeRegistros[$situacao] = $qtde;
                }
            }
        }
        //CHAMA METODO DE QUE IRA GERAR TELA DE RESUMO
        if (isset($post->gerarResumo)) {
            $rs = $tbl->buscarProjetosAvaliados($arrBusca, $ordem);
            $this->resumoProjetosAvaliadosCnic($rs);
        }

        $this->view->registros 		  = $rs;
        $this->view->arrQtdeRegistros = $arrQtdeRegistros;
        $this->view->pag 			  = $pag;
        $this->view->total 			  = $total;
        $this->view->inicio 		  = ($inicio+1);
        $this->view->fim 			  = $fim;
        $this->view->totalPag 		  = $totalPag;
        $this->view->parametrosBusca  = $_POST;
    }

    public function resumoProjetosAvaliadosCnic($recordset)
    {
        $arrQtdeRegistros = array();
        $qtde = 1;

        //UTIL PARA GERACAO DO GRAFICO
        foreach ($recordset as $registros) {
            $situacao = $registros['Situacao'];

            if (array_key_exists($situacao, $arrQtdeRegistros)) {
                $qtdeNova = $arrQtdeRegistros[$situacao];
                $qtdeNova = $qtdeNova + 1;
                $arrQtdeRegistros[$situacao] = $qtdeNova;
            } else {
                $arrQtdeRegistros[$situacao] = $qtde;
            }
        }

        $arrDados = array(
                            "registros"=>$arrQtdeRegistros,
                            "urlGerarGrafico"=>$this->_urlPadrao."/operacional/grafico-projetos-avaliados-cnic"
                            );
        $this->montaTela("operacional/resumo-projetos-avaliados-cnic.phtml", $arrDados);
    }

    public function graficoProjetosAvaliadosCnicAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $grafico = new Grafico($_POST["cgTipoGrafico"]);
        $grafico->setTituloGrafico("Projetos avaliados na CNIC");
        $grafico->setTituloEixoXY("Situacao", "Registros");
        $grafico->configurar($_POST);

        $aux = array();
        $valores = array();
        foreach ($_POST as $chave=>$valor) {
            $aux = explode("gVal_", $chave);
            if (isset($aux[1])) {
                $situacao = $aux[1];
                $situacao = str_replace("_", " ", utf8_decode($situacao));
                $titulos[] = $situacao;
                $valores[] = $valor;
            }
        }

        if (count($valores)>0) {
            $grafico->addDados($valores);
            $grafico->setTituloItens($titulos);
            $grafico->gerar();
        } else {
            echo "Nenhum dado encontrado gera&ccedil;&atilde;o de Gr�fico.";
        }
    }

    public function projetosVotoAlteradoAction()
    {
        $tblTbReuniao = new tbreuniao();
        $rsTbReuniao  = $tblTbReuniao->buscar(array("NrReuniao >= ?"=>184), array("NrReuniao DESC"));
        $this->view->reunioes = $rsTbReuniao;

        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $rsAgentes  = $tblAgentes->BuscarComponente();
        $this->view->agentes = $rsAgentes;

        $tblArea = new Area();
        $rsArea  = $tblArea->buscar(array(), array("Descricao ASC"));
        $this->view->areas = $rsArea;

        $tblSituacao = new Situacao();
        $rsSituacao  = $tblSituacao->buscar(array("StatusProjeto = ?"=>1), array("Descricao ASC"));
        $this->view->situacoes = $rsSituacao;

        $tblOrgao = new Orgaos();
        $rsOrgao  = $tblOrgao->buscar(array("Vinculo = ?"=>1, "Status = ?"=>0));
        $this->view->orgaos = $rsOrgao;
    }

    public function resultadoProjetosVotoAlteradoAction()
    {
        $this->_helper->layout->disableLayout();

        $post = Zend_Registry::get('post');
        $this->intTamPag = 30;

        $arrBusca = array();
        if (!empty($post->nrReuniao)) {
            $arrBusca["t.idNrReuniao = ?"] = $post->nrReuniao;
        }
        if (!empty($post->pronac)) {
            $arrBusca["p.AnoProjeto + p.Sequencial = ?"] = $post->pronac;
        }

        if (!empty($post->nomeProjeto)) {
            $projeto = utf8_decode($post->nomeProjeto);
            if ($post->tipoPesqNomeProjeto == 'QC') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["p.NomeProjeto like (?)"] = "%{$projeto}%";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'EIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["p.NomeProjeto = ?"] = "{$projeto}";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'IIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["p.NomeProjeto like (?)"] = "{$projeto}%";
                }
            }
        }

        if (!empty($post->area)) {
            if ($post->tipoPesqArea == 'EIG') {
                if (!empty($post->area)) {
                    $arrBusca["a.Codigo = ?"] = $post->area;
                }
            } elseif ($post->tipoPesqArea == 'DI') {
                if (!empty($post->area)) {
                    $arrBusca["a.Codigo <> ?"] = $post->area;
                }
            }
        }

        if (!empty($post->segmento)) {
            $arrBusca["p.Segmento = ?"] = $post->segmento;
        }
        if (!empty($post->situacao)) {
            $arrBusca["s.Codigo = ?"] = $post->situacao;
        }
        if (!empty($post->resultadoAvaliacao)) {
            $arrBusca["stAnalise = ?"] = $post->resultadoAvaliacao;
        }

        if (!empty($post->proponente)) {
            $proponente = utf8_decode($post->proponente);
            if ($post->tipoPesqProponente == 'QC') {
                if (!empty($post->proponente)) {
                    $arrBusca["n.Descricao like (?)"] = "%{$proponente}%";
                }
            } elseif ($post->tipoPesqProponente == 'EIG') {
                if (!empty($post->proponente)) {
                    $arrBusca["n.Descricao = ?"] = "{$proponente}";
                }
            } elseif ($post->tipoPesqProponente == 'IIG') {
                if (!empty($post->proponente)) {
                    $arrBusca["n.Descricao like (?)"] = "{$proponente}%";
                }
            }
        }

        if (!empty($post->componente)) {
            $arrBusca["z.idAgente = ?"] = $post->componente;
        }
        if (!empty($post->orgao)) {
            $arrBusca["d.idOrgao = ?"] = $post->orgao;
        }

        $tbl   = new tbPauta();

        if ($post->tipo == 'xls' || $post->tipo == 'pdf') {
            //buscando os registros no banco de dados
            $tamanho    = -1;
            $inicio     = -1;
            $pag        = 0;
            $totalPag   = 0;
            $total      = 0;
            $fim        = 0;

            $ordem = array("10 ASC", "13 ASC");
            if (!empty($post->ordenacao)) {
                $ordem = array("10 ASC");
                $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
            }
            $rs = $tbl->buscarProjetosVotoAlterado($arrBusca, $ordem, $tamanho, $inicio);

            //UTIL PARA GERACAO DO GRAFICO
            $arr = $tbl->buscarProjetosVotoAlterado($arrBusca, $ordem);
            $arrQtdeRegistros = array();
            $qtde = 1;
            foreach ($arr as $registros) {
                $situacao = $registros['DescSituacao'];

                if (array_key_exists($situacao, $arrQtdeRegistros)) {
                    $qtdeNova = $arrQtdeRegistros[$situacao];
                    $qtdeNova = $qtdeNova + 1;
                    $arrQtdeRegistros[$situacao] = $qtdeNova;
                } else {
                    $arrQtdeRegistros[$situacao] = $qtde;
                }
            }

            $this->forward(
                        'preparar-xls-pdf',
                        null,
                        null,
                        array(
                                                                            'dados'=>$rs,
                                                                            'view'=>'operacional/preparar-xls-pdf-projetos-voto-alterado.phtml',
                                                                            'tipo'=> $post->tipo
                                                                            )
                            );
        } else {
            $pag = 1;
            //$get = Zend_Registry::get('get');
            if (isset($post->pag)) {
                $pag = $post->pag;
            }
            if (isset($post->tamPag)) {
                $this->intTamPag = $post->tamPag;
            }

            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $fim    = $inicio + $this->intTamPag;


            $total = $tbl->buscarProjetosVotoAlterado($arrBusca, array(), null, null, true);

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            if ($fim>$total) {
                $fim = $total;
            }

            $ordem = array("10 ASC", "13 ASC");
            if (!empty($post->ordenacao)) {
                $ordem = array("10 ASC");
                $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
            }

            $rs = $tbl->buscarProjetosVotoAlterado($arrBusca, $ordem, $tamanho, $inicio);

            //UTIL PARA GERACAO DO GRAFICO
            $arr = $tbl->buscarProjetosVotoAlterado($arrBusca, $ordem);
            $arrQtdeRegistros = array();
            $qtde = 1;
            foreach ($arr as $registros) {
                $situacao = $registros['DescSituacao'];

                if (array_key_exists($situacao, $arrQtdeRegistros)) {
                    $qtdeNova = $arrQtdeRegistros[$situacao];
                    $qtdeNova = $qtdeNova + 1;
                    $arrQtdeRegistros[$situacao] = $qtdeNova;
                } else {
                    $arrQtdeRegistros[$situacao] = $qtde;
                }
            }
        }
        //CHAMA METODO DE QUE IRA GERAR TELA DE RESUMO
        if (isset($post->gerarResumo)) {
            $rs = $tbl->buscarProjetosVotoAlterado($arrBusca, $ordem);
            $this->resumoProjetosVotoAlterado($rs);
        }

        $this->view->registros 		  = $rs;
        $this->view->arrQtdeRegistros = $arrQtdeRegistros;
        $this->view->pag 			  = $pag;
        $this->view->total 			  = $total;
        $this->view->inicio 		  = ($inicio+1);
        $this->view->fim 			  = $fim;
        $this->view->totalPag 		  = $totalPag;
        $this->view->parametrosBusca  = $_POST;
    }

    public function resumoProjetosVotoAlterado($recordset)
    {
        $arrQtdeRegistros = array();
        $qtde = 1;

        //UTIL PARA GERACAO DO GRAFICO
        foreach ($recordset as $registros) {
            $situacao = $registros['DescSituacao'];

            if (array_key_exists($situacao, $arrQtdeRegistros)) {
                $qtdeNova = $arrQtdeRegistros[$situacao];
                $qtdeNova = $qtdeNova + 1;
                $arrQtdeRegistros[$situacao] = $qtdeNova;
            } else {
                $arrQtdeRegistros[$situacao] = $qtde;
            }
        }

        $arrDados = array(
                            "registros"=>$arrQtdeRegistros,
                            "urlGerarGrafico"=>$this->_urlPadrao."/operacional/grafico-projetos-voto-alterado"
                            );
        $this->montaTela("operacional/resumo-projetos-voto-alterado.phtml", $arrDados);
    }

    public function graficoProjetosVotoAlteradoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $grafico = new Grafico($_POST["cgTipoGrafico"]);
        $grafico->setTituloGrafico("Projetos com voto alterado na CNIC");
        $grafico->setTituloEixoXY("Situacao", "Registros");
        $grafico->configurar($_POST);

        $aux = array();
        $valores = array();
        foreach ($_POST as $chave=>$valor) {
            $aux = explode("gVal_", $chave);
            if (isset($aux[1])) {
                $situacao = $aux[1];
                $situacao = str_replace("_", " ", utf8_decode($situacao));
                $titulos[] = $situacao;
                $valores[] = $valor;
            }
        }

        if (count($valores)>0) {
            $grafico->addDados($valores);
            $grafico->setTituloItens($titulos);
            $grafico->gerar();
        } else {
            echo "Nenhum dado encontrado gera&ccedil;&atilde;o de Gr�fico.";
        }
    }

    /*====== NOVOS RELAROTIO =====*/
    public function projetosEmPautaReuniaoCnicSemQuebraAction()
    {
        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $rsAgentes  = $tblAgentes->BuscarComponente();
        $this->view->agentes = $rsAgentes;

        $tblArea = new Area();
        $rsArea  = $tblArea->buscar(array(), array("Descricao ASC"));
        $this->view->areas = $rsArea;
    }

    public function resultadoProjetosEmPautaReuniaoCnicSemQuebraAction()
    {
        $this->_helper->layout->disableLayout();
        $post = Zend_Registry::get('post');

        $arrBusca = array();
        if (!empty($post->pronac)) {
            $arrBusca["p.AnoProjeto + p.Sequencial = ?"] = $post->pronac;
        }
        if (!empty($post->nomeProjeto)) {
            $projeto = utf8_decode($post->nomeProjeto);
            if ($post->tipoPesqNomeProjeto == 'QC') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["p.NomeProjeto like (?)"] = "%{$projeto}%";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'EIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["p.NomeProjeto = ?"] = "{$projeto}";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'IIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["p.NomeProjeto like (?)"] = "{$projeto}%";
                }
            }
        }

        if (!empty($post->componente)) {
            if ($post->tipoPesqComponente == 'EIG') {
                if (!empty($post->componente)) {
                    $arrBusca["z.idAgente = ?"] = $post->componente;
                }
            } elseif ($post->tipoPesqComponente == 'DI') {
                if (!empty($post->componente)) {
                    $arrBusca["z.idAgente <> ?"] = $post->componente;
                }
            }
        }

        if (!empty($post->area)) {
            if ($post->tipoPesqArea == 'EIG') {
                if (!empty($post->area)) {
                    $arrBusca["a.Codigo = ?"] = $post->area;
                }
            } elseif ($post->tipoPesqArea == 'DI') {
                if (!empty($post->area)) {
                    $arrBusca["a.Codigo <> ?"] = $post->area;
                }
            }
        }

        if (!empty($post->segmento)) {
            $arrBusca["p.Segmento = ?"] = $post->segmento;
        }
        $statusAnalise = null;
        if (!empty($post->statusAnalise)) {
            if ($post->statusAnalise == "SA") {
                $statusAnalise = 2; //Analisados
            } else {
                $statusAnalise = 1; //N�o Analisados
            }
        }


        $pag = 1;
        //$get = Zend_Registry::get('get');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        if (isset($post->tamPag)) {
            $this->intTamPag = $post->tamPag;
        }

        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;

        $tbl = new tbDistribuicaoProjetoComissao();
        $total = $tbl->buscaProjetosEmPauta($arrBusca, array(), null, null, true, $statusAnalise);

        $totalPag = (int) (($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim > $total) {
            $fim = $total;
        }

        $ordem = array("5 ASC");
        if (!empty($post->ordenacao)) {
            $ordem = array("{$post->ordenacao} {$post->tipoOrdenacao}");
        }

        $rs = $tbl->buscaProjetosEmPauta($arrBusca, $ordem, $tamanho, $inicio, null, $statusAnalise);
        $arr = $tbl->buscaProjetosEmPauta($arrBusca, $ordem)->toArray();

        $arrQtdeProjetosAnalisado = array('total' => 0);
        $arrQtdeProjetosNaoAnalisado = array('total' => 0);
        $qtdeAnalisado = 1;
        $qtdeNaoAnalisado = 1;
        $qtdeNovaAnalisado = 0;
        $qtdeNovaNaoAnalisado = 0;

        //UTIL PARA GERACAO DO GRAFICO
        foreach ($arr as $projetos) {
            $analise = $projetos['Analise'];
            $componente = $projetos['Componente'];

            if ($analise == "Analisado") {
                $arrQtdeProjetosAnalisado['total'] = $arrQtdeProjetosAnalisado['total'] + 1;

                if (array_key_exists($componente, $arrQtdeProjetosAnalisado)) {
                    $qtdeNovaAnalisado = $arrQtdeProjetosAnalisado[$componente];
                    $qtdeNovaAnalisado = $qtdeNovaAnalisado + 1;
                    $arrQtdeProjetosAnalisado[$componente] = $qtdeNovaAnalisado;
                } else {
                    $arrQtdeProjetosAnalisado[$componente] = $qtdeAnalisado;
                }
            } else {
                $arrQtdeProjetosNaoAnalisado['total'] = $arrQtdeProjetosNaoAnalisado['total'] + 1;

                if (array_key_exists($componente, $arrQtdeProjetosNaoAnalisado)) {
                    $qtdeNovaNaoAnalisado = $arrQtdeProjetosNaoAnalisado[$componente];
                    $qtdeNovaNaoAnalisado = $qtdeNovaNaoAnalisado + 1;
                    $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNovaNaoAnalisado;
                } else {
                    $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNaoAnalisado;
                }
            }
        }

        //CHAMA METODO DE QUE IRA GERAR TELA DE RESUMO
        if (isset($post->gerarResumo)) {
            $rs = $tbl->buscaProjetosEmPauta($arrBusca, $ordem, null, null, null, $statusAnalise);
            $this->resumoProjetosEmPautaReuniaoCnicSemQuebra($rs);
        }

        //CHAMA METODO DE QUE IRA GERAR TELA DE IMPRESS�O HTML OU XLS
        if (isset($post->imprimirResumo) && $post->imprimirResumo == 'html') {
            $rs2 = $tbl->buscaProjetosEmPauta($arrBusca, $ordem, null, null, null, $statusAnalise);
            //Envia os par�metros para outra fun��o sem a necessidade de criar uma tela .phtml
            $this->forward(
                'gerar-xls-html-projetos-em-pauta-reuniao-cnic-sem-quebra',
                null,
                null,
                array(
                'valores' => $rs2,
                'gerar' => 'html')
            );
        }

        if (isset($post->gerarXls) && $post->gerarXls == 'xls') {
            $post->gerarPdf = null;
            Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
//            $rs2 = $tbl->buscaProjetosEmPauta($arrBusca, $ordem, null, null, null, $statusAnalise);
            $rs2 = $tbl->buscaProjetosEmPautaTeste($arrBusca, $ordem, null, null, null, $statusAnalise);

            //Envia os par�metros para outra fun��o sem a necessidade de criar uma tela .phtml
            $this->forward(
                'gerar-xls-html-projetos-em-pauta-reuniao-cnic-sem-quebra',
                null,
                null,
                array(
                'valores' => $rs2,
                'gerar' => 'xls')
            );
        }

        if (isset($post->gerarPdf)) {
            $rs2 = $tbl->buscaProjetosEmPauta($arrBusca, $ordem, null, null, null, $statusAnalise);
            $this->forward('gerar-xls-html-projetos-em-pauta-reuniao-cnic-sem-quebra', null, null, array('valores' => $rs2, 'gerar' => 'pdf'));
        }


        $this->view->projetos = $rs;
        $this->view->arrQtdeProjetosAnalisados = $arrQtdeProjetosAnalisado;
        $this->view->arrQtdeProjetosNaoAnalisados = $arrQtdeProjetosNaoAnalisado;
        $this->view->pag = $pag;
        $this->view->total = $total;
        $this->view->inicio = ($inicio + 1);
        $this->view->fim = $fim;
        $this->view->totalPag = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    public function gerarXlsHtmlProjetosEmPautaReuniaoCnicSemQuebraAction()
    {
        $this->_helper->layout->disableLayout();
        $post = Zend_Registry::get('post');

        $arrBusca = array();
        if (!empty($post->pronac)) {
            $arrBusca["pronac"] = "p.AnoProjeto + p.Sequencial = ".$post->pronac;
        }
        if (!empty($post->nomeProjeto)) {
            $projeto = utf8_decode($post->nomeProjeto);
            if ($post->tipoPesqNomeProjeto == 'QC') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["NomeProjeto"] = "p.NomeProjeto like '%{$projeto}%'";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'EIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["NomeProjeto"] = "p.NomeProjeto = {$projeto}";
                }
            } elseif ($post->tipoPesqNomeProjeto == 'IIG') {
                if (!empty($post->nomeProjeto)) {
                    $arrBusca["NomeProjeto"] = "p.NomeProjeto like '{$projeto}%'";
                }
            }
        }

        if (!empty($post->componente)) {
            if ($post->tipoPesqComponente == 'EIG') {
                if (!empty($post->componente)) {
                    $arrBusca["idAgente"] = "z.idAgente = ".$post->componente;
                }
            } elseif ($post->tipoPesqComponente == 'DI') {
                if (!empty($post->componente)) {
                    $arrBusca["idAgente"] = "z.idAgente <> ".$post->componente;
                }
            }
        }

        if (!empty($post->area)) {
            if ($post->tipoPesqArea == 'EIG') {
                if (!empty($post->area)) {
                    $arrBusca["Codigo"] = "a.Codigo = '$post->area'";
                }
            } elseif ($post->tipoPesqArea == 'DI') {
                if (!empty($post->area)) {
                    $arrBusca["Codigo"] = "a.Codigo <> '$post->area'";
                }
            }
        }

        if (!empty($post->segmento)) {
            $arrBusca["Segmento"] = "p.Segmento = '$post->segmento'";
        }

        $arrBusca["status"] = 0;
        if (!empty($post->statusAnalise)) {
            if ($post->statusAnalise == "SA") {
                $arrBusca["status"] = 2; //Analisados
            } else {
                $arrBusca["status"] = 1; //N�o Analisados
            }
        }

        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
//            $rs2 = $tbl->buscaProjetosEmPauta($arrBusca, $ordem, null, null, null, $statusAnalise);
        $tbl = new tbDistribuicaoProjetoComissao();

        $rs2 = $tbl->buscaProjetosEmPautaXLS($arrBusca);

        //Envia os par�metros para outra fun��o sem a necessidade de criar uma tela .phtml
//        $this->_forward('gerar-xls-html-projetos-em-pauta-reuniao-cnic-sem-quebra', null, null, array(
//            'valores' => $rs2,
//            'gerar' => 'xls')
//        );

        $this->view->excel = $rs2;
        $this->view->gerar = array(
            'gerar' => 'xls',
            'statusAnalise' => '',
            'pronac' => '',
            'tipoPesqNomeProjeto' => 'QC',
            'nomeProjeto' => '',
            'tipoPesqArea' => 'EIG',
            'area' => '',
            'segmento' => '',
            'tipoPesqComponente' => 'EIG',
            'componente' => '',
            'pag' => '1',
            'ordenacao' => '',
            'tipoOrdenacao' => '',
            'gerarXls' => 'xls'
        );

        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
        ini_set('max_execution_time', 900);
        $this->_response->clearHeaders();

//        [gerar] => xls
//        [statusAnalise] =>
//        [pronac] =>
//        [tipoPesqNomeProjeto] => QC
//        [nomeProjeto] =>
//        [tipoPesqArea] => EIG
//        [area] =>
//        [segmento] =>
//        [tipoPesqComponente] => EIG
//        [componente] =>
//        [pag] => 1
//        [ordenacao] =>
//        [tipoOrdenacao] =>
//        [gerarXls] => xls
    }

    /*
     * Recebe os dados de outra fun��o pelo m�todo _forward
     */
    //	public function gerarXlsHtmlProjetosEmPautaReuniaoCnicSemQuebraAction(){
    //		Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
    //		ini_set('max_execution_time', 900);
    //		$this->_response->clearHeaders();
    //		$dados = $this->_getAllParams();
    //		$this->view->projetos 	= $dados;
    //		$this->view->gerar 		= $dados;
    //	}

    public function resumoProjetosEmPautaReuniaoCnicSemQuebra($recordset)
    {
        $arrQtdeProjetosAnalisado    = array('total'=>0);
        $arrQtdeProjetosNaoAnalisado = array('total'=>0);
        $qtdeAnalisado 				 = 1;
        $qtdeNaoAnalisado 			 = 1;
        $qtdeNovaAnalisado 			 = 0;
        $qtdeNovaNaoAnalisado 		 = 0;
        foreach ($recordset as $projetos) {
            $analise = $projetos['Analise'];
            $componente = $projetos['Componente'];

            if ($analise == "Analisado") {
                $arrQtdeProjetosAnalisado['total'] = $arrQtdeProjetosAnalisado['total'] + 1;

                if (array_key_exists($componente, $arrQtdeProjetosAnalisado)) {
                    $qtdeNovaAnalisado = $arrQtdeProjetosAnalisado[$componente];
                    $qtdeNovaAnalisado = $qtdeNovaAnalisado + 1;
                    $arrQtdeProjetosAnalisado[$componente] = $qtdeNovaAnalisado;
                } else {
                    $arrQtdeProjetosAnalisado[$componente] = $qtdeAnalisado;
                }
            } else {
                $arrQtdeProjetosNaoAnalisado['total'] = $arrQtdeProjetosNaoAnalisado['total'] + 1;

                if (array_key_exists($componente, $arrQtdeProjetosNaoAnalisado)) {
                    $qtdeNovaNaoAnalisado = $arrQtdeProjetosNaoAnalisado[$componente];
                    $qtdeNovaNaoAnalisado = $qtdeNovaNaoAnalisado + 1;
                    $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNovaNaoAnalisado;
                } else {
                    $arrQtdeProjetosNaoAnalisado[$componente] = $qtdeNaoAnalisado;
                }
            }
        }

        $arrDados = array(
                            "projetosAnalisados"=>$arrQtdeProjetosAnalisado,
                            "projetosNaoAnalisados"=>$arrQtdeProjetosNaoAnalisado,
                            "urlGerarGrafico"=>$this->_urlPadrao."/operacional/grafico-projetos-em-pauta-reuniao-cnic-sem-quebra"
                            );
        $this->montaTela("operacional/resumo-projetos-em-pauta-reuniao-cnic-sem-quebra.phtml", $arrDados);
        return;
    }

    public function graficoProjetosEmPautaReuniaoCnicSemQuebraAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $grafico = new Grafico($_POST["cgTipoGrafico"]);
        $grafico->setTituloGrafico("Projetos em pauta");
        $grafico->setTituloEixoXY("Componente da Comiss&atilde;o", "Registros");
        $grafico->configurar($_POST);

        /*======== INICIO PREPARA OS TITULOS DO ARRAY - GRAFICO ==========*/
        if (isset($_POST["todos"]) || isset($_POST["Analisados"])) {
            $aux = array();
            foreach ($_POST as $chave=>$valor) {
                $aux = explode("gValA_", $chave);
                if (isset($aux[1]) && $aux[1] != "total") {
                    $componente = $aux[1];
                    $componente = str_replace("_", " ", utf8_decode($componente));
                    $titulos[] = $componente;
                }
            }
        }

        if (isset($_POST["todos"]) || isset($_POST["NaoAnalisados"])) {
            $aux = array();
            foreach ($_POST as $chave=>$valor) {
                $aux = explode("gValNA_", $chave);
                if (isset($aux[1]) && $aux[1] != "total") {
                    $componente = $aux[1];
                    $componente = str_replace("_", " ", utf8_decode($componente));
                    $titulos[] = $componente;
                }
            }
        }
        /*======= FIM PREPARA OS TITULOS DO ARRAY - GRAFICO ==========*/

        /*======== INICIO PREPARA VALORES DO ARRAY - GRAFICO ==========*/
        foreach ($_POST as $chave => $valor) {
            $aux = explode("gValA_", $chave);
            $nome = @str_replace("_", " ", utf8_decode($aux[1]));
            $arrAvaliados[$nome] = $valor;
        }

        foreach ($_POST as $chave => $valor) {
            $aux = explode("gValNA_", $chave);
            $nome = @str_replace("_", " ", utf8_decode($aux[1]));
            $arrNaoAvaliados[$nome] = $valor;
        }

        //RETIRA NOMES REPETIDOS
        $titulos = array_unique($titulos);

        /*======== FIM PREPARA VALORES DO ARRAY - GRAFICO ==========*/


        foreach ($titulos as $titulo) {
            if (isset($_POST["todos"]) || isset($_POST["Analisados"])) {
                if (key_exists($titulo, $arrAvaliados)) {
                    $arrAvaliadosFinal[]=$arrAvaliados[$titulo];
                } else {
                    $arrAvaliadosFinal[]=0;
                }
            }
            if (isset($_POST["todos"]) || isset($_POST["NaoAnalisados"])) {
                if (key_exists($titulo, $arrNaoAvaliados)) {
                    $arrNaoAvaliadosFinal[]=$arrNaoAvaliados[$titulo];
                } else {
                    $arrNaoAvaliadosFinal[]=0;
                }
            }
        }

        $grafico = new Grafico($_POST["cgTipoGrafico"]);
        $grafico->setTituloGrafico("Registros");
        $grafico->setTituloEixoXY("Avaliacao", "Registros");
        @$grafico->configurar($_POST);

        if (isset($_POST["todos"]) || isset($_POST["Analisados"])) {
            $grafico->addDados($arrAvaliadosFinal, "Analisados");
        }
        if (isset($_POST["todos"]) || isset($_POST["NaoAnalisados"])) {
            $grafico->addDados($arrNaoAvaliadosFinal, "Nao analisados");
        }
        $grafico->setTituloItens($titulos);
        @$grafico->gerar();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function demonstrativoCaptacaoRecursoAction()
    {
        $tblTbReuniao = new tbreuniao();
        $rsTbReuniao  = $tblTbReuniao->buscar(array("NrReuniao >= ?"=>184), array("NrReuniao DESC"));
        $this->view->reunioes = $rsTbReuniao;

        $tblArea = new Area();
        $rsArea  = $tblArea->buscar(array(), array("Descricao ASC"));
        $this->view->areas = $rsArea;

        $tblUf = new Uf();
        $rsUf  = $tblUf->buscar(array(), array("Descricao ASC"));

        $this->view->ufs = $rsUf;

        $arrRegioes = array();
        foreach ($rsUf as $item) {
            $arrRegioes[] = $item->Regiao;
        }
        $arrRegioes = array_unique($arrRegioes);
        $this->view->regioes = $arrRegioes;
    }

    public function resultadoDemonstrativoCaptacaoRecursoAction()
    {
        $this->_helper->layout->disableLayout();

        $tbl             = new Captacao();
        $tetoRenuncia    = new TetoRenuncia();
        $post            = Zend_Registry::get('post');
        $this->intTamPag = 10;

        $arrBusca = array();
        //if(!empty($post->nrReuniao)){ $arrBusca["t.idNrReuniao = ?"] = $post->nrReuniao; }

        //Valida se o pronac foi passado
        if (!empty($post->pronac)) {
            $arrBusca["p.AnoProjeto + p.Sequencial = ?"] = $post->pronac;
        }

        //Valida se a �rea foi passada
        if (!empty($post->area)) {
            if ($post->tipoPesqArea == 'EIG') {
                if (!empty($post->area)) {
                    $arrBusca["a.Codigo = ?"] = $post->area;
                }
            } elseif ($post->tipoPesqArea == 'DI') {
                if (!empty($post->area)) {
                    $arrBusca["a.Codigo <> ?"] = $post->area;
                }
            }
        }

        //Valida se o segmento foi passao
        if (!empty($post->segmento)) {
            $arrBusca["p.Segmento = ?"] = $post->segmento;
        }

        //Valida se a regi�o e/ou estado foi passado
        if (!empty($post->regiao) && empty($post->uf)) {
            $arrBusca["uf.Regiao = ?"] = $post->regiao;
        } elseif (!empty($post->regiao) && !empty($post->uf)) {
            $arrBusca["p.UfProjeto = ?"] = $post->uf;
        }

        //if(!empty($post->regiao)){ $arrBusca["uf.Regiao = ?"] = $post->regiao; }

        //Valida se valor inicial e valor final foi passado
        $arrBuscaValor = array();
        if (!empty($post->vlInicio) && ($post->vlInicio!="0,00")) {
            $vlInicio = str_replace(",", ".", str_replace(".", "", $post->vlInicio));
            $vlFim    = str_replace(",", ".", str_replace(".", "", $post->vlFim));
            $arrBuscaValor['vlAutorizado > ?'] = $vlInicio;
            $arrBuscaValor['vlAutorizado < ?'] = $vlFim;
        }

        $arrBusca['n.Status = ?']='0';

        //montando parametros de busca dos campos de data
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtCaptacao", "dtCaptacao", "ca.DtRecibo", "dtCaptacao_Final", $arrBusca);
        $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtExecucao", "dtExecucao", "p.DtInicioExecucao", "dtExecucao_Final", $arrBusca);

        //Dados para pagina��o
        $pag = 1;
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        if (isset($post->tamPag)) {
            $this->intTamPag = $post->tamPag;
        }

        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim    = $inicio + $this->intTamPag;

        $total = $tbl->buscarDemonstrativoDeCaptacao($arrBusca, array(), null, null, true, $arrBuscaValor);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho  = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        if ($fim>$total) {
            $fim = $total;
        }

        //Varifica se foi solicitado a ordena��o
        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        } else {
            $ordem = array('6');
        }

        //Valida se est� na �ltima p�gina para passar os somat�rios
        //if($totalPag == $pag){
        $rsSomatorioAutorizado = $tbl->buscarDemonstrativoDeCaptacaoSomatorioValorAutorizado($arrBusca, $arrBuscaValor);
        $rsSomatorioCaptado    = $tbl->buscarDemonstrativoDeCaptacaoSomatorioValorCaptado($arrBusca, $arrBuscaValor);
        if (empty($post->dtCaptacao) && empty($post->dtCaptacao_Final)) {
            //montando parametros de busca dos campos de data
            $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtCaptacao", "dtCaptacao", "ca.DtRecibo", "dtCaptacao_Final", $arrBusca);
            $arrBusca = MinC_Controller_Action_Abstract::montaBuscaData($post, "tpDtExecucao", "dtExecucao", "p.DtInicioExecucao", "dtExecucao_Final", $arrBusca);

            if ($post->tpDtCaptacao == 'OT') {
                $arrData['Ano >= ?'] = substr($arrBusca['ca.DtRecibo = ?'], 2, 2);
                $teto = $tetoRenuncia->buscarAnoTetoCaptacao($arrData);
            } else {
                $arrData['Ano >= ?'] = substr($arrBusca['ca.DtRecibo >= ?'], 2, 2);
                $arrData['Ano <= ?']  = substr($arrBusca['ca.DtRecibo <= ?'], 2, 2);
                $teto = $tetoRenuncia->buscarAnoTetoCaptacao($arrData);
            }
        } elseif (!empty($post->dtCaptacao) && empty($post->dtCaptacao_Final)) {
            $arrData['Ano >= ?'] = substr($post->dtCaptacao, 8, 2);
            $teto = $tetoRenuncia->buscarAnoTetoCaptacao($arrData);
        } elseif (!empty($post->dtCaptacao) && !empty($post->dtCaptacao_Final)) {
            $arrData['Ano >= ?'] = substr($post->dtCaptacao, 8, 2);
            $arrData['Ano <= ?']  = substr($post->dtCaptacao_Final, 8, 2);
            $teto = $tetoRenuncia->buscarAnoTetoCaptacao($arrData);
        }

        foreach ($teto as $valorRenuncia) {
            $vlRenunciaTotal = $valorRenuncia->somatorioAnoBusca;
        }
        foreach ($rsSomatorioCaptado as $valorCaptado) {
            $vlCaptado = $valorCaptado->somatorioVlCaptado;
        }

        $this->view->valorAltorizado     = $rsSomatorioAutorizado;
        $this->view->valorCaptado        = $rsSomatorioCaptado;
        $this->view->ValorTetoRenuncia   = $teto;
        $this->view->valorRenunciaFiscal = $vlRenunciaTotal - $vlCaptado;
        //}


        //CHAMA METODO DE QUE IRA GERAR TELA DE IMPRESS�O HTML OU XLS
        if (isset($post->imprimirResumo) && $post->imprimirResumo == 'html') {
            Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));

            //Refaz a busca para envia via _forward
            $rs    = $tbl->buscarDemonstrativoDeCaptacao($arrBusca, $ordem, null, null, null, $arrBuscaValor);
            $i     = 0;
            $lista = array();
            //Envia a lista ap�s buscar o valor total captado
            foreach ($rs as $d) {
                //$lista[$i]['DtRecibo'] 			= $d->DtRecibo;
                $lista[$i]['CaptacaoReal'] 			= $d->CaptacaoReal;
                $lista[$i]['vlAutorizado'] 			= $d->vlAutorizado;
                $lista[$i]['vlCaptado'] 			= $d->CaptacaoReal;//$tbl->valorTotal('captado', $d->AnoProjeto, $d->Sequencial);
                $lista[$i]['PRONAC'] 				= $d->PRONAC;
                $lista[$i]['IdPRONAC'] 				= $d->IdPRONAC;
                $lista[$i]['NomeProjeto'] 			= $d->NomeProjeto;
                $lista[$i]['CNPJCPFProponente'] 	= $d->CNPJCPFProponente;
                $lista[$i]['Proponente'] 			= $d->Proponente;
                $lista[$i]['DescArea'] 				= $d->DescArea;
                $lista[$i]['DescSegmento'] 			= $d->DescSegmento;
                $lista[$i]['Sigla'] 				= $d->Sigla;
                $i++;
            }

            $rsSomatorioAutorizado = $tbl->buscarDemonstrativoDeCaptacaoSomatorioValorAutorizado($arrBusca, $arrBuscaValor);
            $rsSomatorioCaptado    = $tbl->buscarDemonstrativoDeCaptacaoSomatorioValorCaptado($arrBusca, $arrBuscaValor);

            if (!empty($post->dtCaptacao) && empty($post->dtCaptacao_Final)) {
                $arrData['Ano >= ?'] = substr($post->dtCaptacao, 8, 2);
                $teto = $tetoRenuncia->buscarAnoTetoCaptacao($arrData);
            } elseif (!empty($post->dtCaptacao) && !empty($post->dtCaptacao_Final)) {
                $arrData['Ano >= ?'] = substr($post->dtCaptacao, 8, 2);
                $arrData['Ano <= ?'] = substr($post->dtCaptacao_Final, 8, 2);
                $teto = $tetoRenuncia->buscarAnoTetoCaptacao($arrData);
            }

            foreach ($teto as $valorRenuncia) {
                $vlRenunciaTotal = $valorRenuncia->somatorioAnoBusca;
            }
            foreach ($rsSomatorioCaptado as $valorCaptado) {
                $vlCaptado = $valorCaptado->somatorioVlCaptado;
            }

            $valorRenunciaFiscal = $vlRenunciaTotal - $vlCaptado;

            //Envia os par�metros para outra fun��o sem a necessidade de criar uma tela .phtml
            $this->forward('gerar-tela-xls-html', null, null, array('valores'=>$lista,
                                                                  'SmAu'=>$rsSomatorioAutorizado,
                                                                  'SmCp'=>$rsSomatorioCaptado,
                                                                  'teto'=>$teto,
                                                                  'renuncia'=>$valorRenunciaFiscal,
                                                                  'gerar'=>'html'));
        }

        if (isset($post->gerarXls) && $post->gerarXls == 'xls') {
            Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));

            //Refaz a busca para envia via _forward
            $rs    = $tbl->buscarDemonstrativoDeCaptacao($arrBusca, $ordem, null, null, null, $arrBuscaValor);
            $i 	   = 0;
            $lista = array();

            //Envia a lista ap�s buscar o valor total captado
            foreach ($rs as $d) {
                //$lista[$i]['DtRecibo'] 			= $d->DtRecibo;
                $lista[$i]['CaptacaoReal'] 			= $d->CaptacaoReal;
                $lista[$i]['vlAutorizado'] 			= $d->vlAutorizado;
                $lista[$i]['vlCaptado'] 			= $d->CaptacaoReal;//$tbl->valorTotal('captado', $d->AnoProjeto, $d->Sequencial);
                $lista[$i]['PRONAC'] 				= $d->PRONAC;
                $lista[$i]['IdPRONAC'] 				= $d->IdPRONAC;
                $lista[$i]['NomeProjeto'] 			= $d->NomeProjeto;
                $lista[$i]['CNPJCPFProponente'] 	= $d->CNPJCPFProponente;
                $lista[$i]['Proponente'] 			= $d->Proponente;
                $lista[$i]['DescArea'] 				= $d->DescArea;
                $lista[$i]['DescSegmento'] 			= $d->DescSegmento;
                $lista[$i]['Sigla'] 				= $d->Sigla;
                $i++;
            }

            $rsSomatorioAutorizado = $tbl->buscarDemonstrativoDeCaptacaoSomatorioValorAutorizado($arrBusca, $arrBuscaValor);
            $rsSomatorioCaptado    = $tbl->buscarDemonstrativoDeCaptacaoSomatorioValorCaptado($arrBusca, $arrBuscaValor);

            if (!empty($post->dtCaptacao) && empty($post->dtCaptacao_Final)) {
                $arrData['Ano >= ?'] = substr($post->dtCaptacao, 8, 2);
                $teto = $tetoRenuncia->buscarAnoTetoCaptacao($arrData);
            } elseif (!empty($post->dtCaptacao) && !empty($post->dtCaptacao_Final)) {
                $arrData['Ano >= ?'] = substr($post->dtCaptacao, 8, 2);
                $arrData['Ano <= ?']  = substr($post->dtCaptacao_Final, 8, 2);
                $teto = $tetoRenuncia->buscarAnoTetoCaptacao($arrData);
            }

            foreach ($teto as $valorRenuncia) {
                $vlRenunciaTotal = $valorRenuncia->somatorioAnoBusca;
            }
            foreach ($rsSomatorioCaptado as $valorCaptado) {
                $vlCaptado = $valorCaptado->somatorioVlCaptado;
            }

            $valorRenunciaFiscal = $vlRenunciaTotal - $vlCaptado;
            //Envia os par�metros para outra fun��o sem a necessidade de criar uma tela .phtml
            $this->forward('gerar-tela-xls-html', null, null, array('valores'=>$lista,
                                                                  'SmAu'=>$rsSomatorioAutorizado,
                                                                  'SmCp'=>$rsSomatorioCaptado,
                                                                  'teto'=>$teto,
                                                                  'renuncia'=>$valorRenunciaFiscal,
                                                                  'gerar'=>'xls'));
        }

        //Passa os valores para a view
        $rs = $tbl->buscarDemonstrativoDeCaptacao($arrBusca, $ordem, $tamanho, $inicio, null, $arrBuscaValor);

        $i     = 0;
        $lista = array();
        //Envia a lista ap�s buscar o valor total captado
        foreach ($rs as $d) {
            //$lista[$i]['DtRecibo'] 			= $d->DtRecibo;
            $lista[$i]['CaptacaoReal'] 			= $d->CaptacaoReal;
            $lista[$i]['vlAutorizado'] 			= $d->vlAutorizado;
            $lista[$i]['vlCaptado'] 			= $d->CaptacaoReal;//$tbl->valorTotal('captado', $d->AnoProjeto, $d->Sequencial);
            $lista[$i]['PRONAC'] 				= $d->PRONAC;
            $lista[$i]['IdPRONAC'] 				= $d->IdPRONAC;
            $lista[$i]['NomeProjeto'] 			= $d->NomeProjeto;
            $lista[$i]['CNPJCPFProponente'] 	= $d->CNPJCPFProponente;
            $lista[$i]['Proponente'] 			= $d->Proponente;
            $lista[$i]['DescArea'] 				= $d->DescArea;
            $lista[$i]['DescSegmento'] 			= $d->DescSegmento;
            $lista[$i]['Sigla'] 				= $d->Sigla;
            $i++;
        }

        //Dados para view e para a pagina��o
        $this->view->registros       = $lista;
        $this->view->pag             = $pag;
        $this->view->total           = $total;
        $this->view->inicio          = ($inicio+1);
        $this->view->fim             = $fim;
        $this->view->totalPag        = $totalPag;
        $this->view->parametrosBusca = $_POST;
        //$this->view->arrQtdeRegistros = $arrQtdeRegistros;
    }

    /*
     * Gera PDF pelo PDFCreator
     */
    public function gerarPdfTotalAction()
    {
        $this->_helper->layout->disableLayout();
        ini_set('max_execution_time', 900);

        $this->_helper->viewRenderer->setNoRender();

        //$post = Zend_Registry::get('post');
        $pdf = new PDFCreator($_POST['html'], 'L');

        $pdf->gerarPdf();
    }

    /*
     * Recebe os dados de outra fun��o pelo m�todo _forward
     */
    public function gerarTelaXlsHtmlAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
        ini_set('max_execution_time', 900);
        $this->_response->clearHeaders();
        $teste = $this->getAllParams();
        $this->view->registros 			 = $teste;
        $this->view->valorAltorizado     = $teste;
        $this->view->valorCaptado        = $teste;
        $this->view->ValorTetoRenuncia   = $teste;
        $this->view->valorRenunciaFiscal = $teste;
        $this->view->gerar 				 = $teste;
    }

    public function resumoDemonstrativoCaptacaoRecurso($recordset)
    {
        $arrQtdeRegistros = array();
        $qtde = 1;

        //UTIL PARA GERACAO DO GRAFICO
        foreach ($recordset as $registros) {
            $pronac  	  = $registros['PRONAC'];
            $vlautorizado = $registros['vlAutorizado'];
            $vlcaptado 	  = $registros['vlCaptado'];

            if (array_key_exists($pronac, $arrQtdeRegistros)) {
                //$arrQtdeRegistros[$pronac]  = $pronac;
                $arrQtdeRegistros[$pronac] = $registros['vlAutorizado'] + $registros['vlAutorizado'];
            } else {
                $arrQtdeRegistros[$pronac] = $registros['vlAutorizado'];
            }
        }
        $arrDados = array(
                            "registros"=>$arrQtdeRegistros,
                            "urlGerarGrafico"=>$this->_urlPadrao."/operacional/grafico-demonstrativo-captacao-recurso"
                            );
        $this->montaTela("operacional/resumo-demonstrativo-captacao-recurso.phtml", $arrDados);
    }

    public function graficoDemonstrativoCaptacaoRecursoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $grafico = new Grafico($_POST["cgTipoGrafico"]);
        $grafico->setTituloGrafico("Demonstrativo de capta��o de recursos");
        $grafico->setTituloEixoXY("PRONAC", "VLAUTORIZADO");
        $grafico->configurar($_POST);

        $aux = array();
        $valores = array();
        foreach ($_POST as $chave=>$valor) {
            $aux = explode("gVal_", $chave);
            if (isset($aux[1])) {
                $situacao = $aux[1];
                $situacao = str_replace("_", " ", utf8_decode($situacao));
                $titulos[] = $situacao;
                $valores[] = $valor;
            }
        }

        if (count($valores)>0) {
            $grafico->addDados($valores);
            $grafico->setTituloItens($titulos);
            $grafico->setTamanho(800, 800);
            $grafico->gerar();
        } else {
            echo "Nenhum dado encontrado gera&ccedil;&atilde;o de Gr�fico.";
        }
    }

    public function contaBancariaAction()
    {
        //FUN��O ACESSADA SOMENTE PELO TEC., COORD. E COORD. GERAL DE ACOMPANHAMENTO
        if ($this->idPerfil != 121 && $this->idPerfil != 122 && $this->idPerfil != 123) {
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }
    }

    public function resultadoContaBancariaAction()
    {
        //FUN��O ACESSADA SOMENTE PELO TEC., COORD. E COORD. GERAL DE ACOMPANHAMENTO
        if ($this->idPerfil != 121 && $this->idPerfil != 122 && $this->idPerfil != 123) {
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //idPronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();

        if (!empty($_GET['pronac'])) {
            $this->view->pronac = $_GET['pronac'];
            $where["(p.AnoProjeto+p.Sequencial) = ?"] = $_GET['pronac'];
        }

        if (!empty($_GET['tpPessoa']) || $_GET['tpPessoa'] == '0') {
            $this->view->tpPessoa = $_GET['tpPessoa'];
            $where["a.TipoPessoa = ?"] = $_GET['tpPessoa'];
        }

        if (!empty($_GET['ocorrencia']) && $_GET['ocorrencia'] != '000') {
            $this->view->ocorrencia = $_GET['ocorrencia'];
            $where["c.OcorrenciaCB = ?"] = $_GET['ocorrencia'];
        }

        if (!empty($_GET['estadoConta'])) {
            $this->view->estadoConta = $_GET['estadoConta'];
            if ($_GET['estadoConta'] == 1) {
                $where["c.ContaBloqueada = ?"] = '000000000000';
            } else {
                $where["c.ContaBloqueada <> ?"] = '000000000000';
            }
        }

        if (!empty($_GET['tpDtLtCap'])) {

            //SE O USUARIO N�O INFORMAR A DATA CORRETAMENTE, O SISTEMA RETORNA A MSG.
            if (empty($_GET['dtInicioLtCap'])) {
                parent::message("Faltou informar a data para a realizarmos a pesquisa!", "operacional/conta-bancaria", "ALERT");
            } else {
                $d1 = Data::dataAmericana($_GET['dtInicioLtCap']);
                $this->view->dtInicioLtCap = $_GET['dtInicioLtCap'];

                //SE O USUARIO INFORMAR QUE DESEJA FAZER UMA ANALISE ENTRE DUAS DATAS E NAO INFORMAR A SEGUNDA DATA, O SISTEMA RETORNA A MSG.
                if ($_GET['tpDtLtCap'] == "entre" && empty($_GET['dtFimLtCap'])) {
                    parent::message("Faltou informar a data final para a realizarmos a pesquisa!", "operacional/conta-bancaria", "ALERT");
                }

                if ($_GET['tpDtLtCap'] == "igual") {
                    $where["c.DtLoteRemessaCL BETWEEN '$d1 00:00:00' AND '$d1 23:59:59.999'"] = '';
                } elseif ($_GET['tpDtLtCap'] == "entre") {
                    $d2 = Data::dataAmericana($_GET['dtFimLtCap']);
                    $this->view->dtFimLtCap = $_GET['dtFimLtCap'];
                    $where["c.DtLoteRemessaCL BETWEEN '$d1 00:00:00' AND '$d2 23:59:59.999'"] = '';
                } elseif ($_GET['tpDtLtCap'] == "maior") {
                    $where["c.DtLoteRemessaCL >= ?"] = $d1.' 00:00:00';
                } elseif ($_GET['tpDtLtCap'] == "menor") {
                    $where["c.DtLoteRemessaCL <= ?"] = $d1.' 23:59:59.999';
                }
                $this->view->tpDtLtCap = $_GET['tpDtLtCap'];
            }
        }

        $Orgaos = new Orgaos();
        $idSecretaria = $Orgaos->buscar(array('codigo = ?'=>$this->idOrgao))->current();

        if (isset($idSecretaria) && !empty($idSecretaria)) {
            if ($idSecretaria->idSecretaria == Orgaos::ORGAO_SUPERIOR_SAV) {
                $where['p.Area = ?'] = 2;
            } else {
                $where['p.Area <> ?'] = 2;
            }
        }

        $ContaBancaria = new ContaBancaria();
        $total = $ContaBancaria->painelContasBancarias($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $ContaBancaria->painelContasBancarias($where, $order, $tamanho, $inicio);
        $paginacao = array(
            "pag"=>$pag,
            "qtde"=>$this->intTamPag,
            "campo"=>$campo,
            "ordem"=>$ordem,
            "ordenacao"=>$ordenacao,
            "novaOrdem"=>$novaOrdem,
            "total"=>$total,
            "inicio"=>($inicio+1),
            "fim"=>$fim,
            "totalPag"=>$totalPag,
            "Itenspag"=>$this->intTamPag,
            "tamanho"=>$tamanho
        );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function imprimirResultadoContaBancariaAction()
    {
        //FUN��O ACESSADA SOMENTE PELO TEC., COORD. E COORD. GERAL DE ACOMPANHAMENTO
        if ($this->idPerfil != 121 && $this->idPerfil != 122 && $this->idPerfil != 123) {
            parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal", "ALERT");
        }

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //idPronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('post');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();

        if (!empty($_POST['pronac'])) {
            $this->view->pronac = $_POST['pronac'];
            $where["(p.AnoProjeto+p.Sequencial) = ?"] = $_POST['pronac'];
        }

        if (!empty($_POST['tpPessoa']) || $_POST['tpPessoa'] == '0') {
            $this->view->tpPessoa = $_POST['tpPessoa'];
            $where["a.TipoPessoa = ?"] = $_POST['tpPessoa'];
        }

        if (!empty($_POST['ocorrencia']) && $_POST['ocorrencia'] != '000') {
            $this->view->ocorrencia = $_POST['ocorrencia'];
            $where["c.OcorrenciaCB = ?"] = $_POST['ocorrencia'];
        }

        if (!empty($_POST['estadoConta'])) {
            $this->view->estadoConta = $_POST['estadoConta'];
            if ($_POST['estadoConta'] == 1) {
                $where["c.ContaBloqueada = ?"] = '000000000000';
            } else {
                $where["c.ContaBloqueada <> ?"] = '000000000000';
            }
        }

        if (!empty($_POST['tpDtLtCap'])) {

            //SE O USUARIO N�O INFORMAR A DATA CORRETAMENTE, O SISTEMA RETORNA A MSG.
            if (empty($_POST['dtInicioLtCap'])) {
                parent::message("Faltou informar a data para a realizarmos a pesquisa!", "operacional/conta-bancaria", "ALERT");
            } else {
                $d1 = Data::dataAmericana($_POST['dtInicioLtCap']);
                $this->view->dtInicioLtCap = $_POST['dtInicioLtCap'];

                //SE O USUARIO INFORMAR QUE DESEJA FAZER UMA ANALISE ENTRE DUAS DATAS E NAO INFORMAR A SEGUNDA DATA, O SISTEMA RETORNA A MSG.
                if ($_POST['tpDtLtCap'] == "entre" && empty($_POST['dtFimLtCap'])) {
                    parent::message("Faltou informar a data final para a realizarmos a pesquisa!", "operacional/conta-bancaria", "ALERT");
                }

                if ($_POST['tpDtLtCap'] == "igual") {
                    $where["c.DtLoteRemessaCL BETWEEN '$d1 00:00:00' AND '$d1 23:59:59.999'"] = '';
                } elseif ($_POST['tpDtLtCap'] == "entre") {
                    $d2 = Data::dataAmericana($_POST['dtFimLtCap']);
                    $this->view->dtFimLtCap = $_POST['dtFimLtCap'];
                    $where["c.DtLoteRemessaCL BETWEEN '$d1 00:00:00' AND '$d2 23:59:59.999'"] = '';
                } elseif ($_POST['tpDtLtCap'] == "maior") {
                    $where["c.DtLoteRemessaCL >= ?"] = $d1.' 00:00:00';
                } elseif ($_POST['tpDtLtCap'] == "menor") {
                    $where["c.DtLoteRemessaCL <= ?"] = $d1.' 23:59:59.999';
                }
                $this->view->tpDtLtCap = $_POST['tpDtLtCap'];
            }
        }

        $Orgaos = new Orgaos();
        $idSecretaria = $Orgaos->buscar(array('codigo = ?'=>$this->idOrgao))->current();

        if (isset($idSecretaria) && !empty($idSecretaria)) {
            if ($idSecretaria->idSecretaria == Orgaos::ORGAO_SUPERIOR_SAV) {
                $where['p.Area = ?'] = 2;
            } else {
                $where['p.Area <> ?'] = 2;
            }
        }

        $ContaBancaria = new ContaBancaria();
        $total = $ContaBancaria->painelContasBancarias($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $ContaBancaria->painelContasBancarias($where, $order, $tamanho, $inicio);

        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }
}
