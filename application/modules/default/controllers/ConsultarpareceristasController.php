<?php

class ConsultarpareceristasController extends MinC_Controller_Action_Abstract
{
    public static $perfil = array(93, 94, 137);
    public static $codPerfil = 0;
    public static $perfilAtual = '';
    public static $titulo = '';
    public static $codOrgao;
    public static $usu_identificacao;

    //93  Coordenador de Parecerista
    //137 SEFIC - Coordenador PRONAC
    //94  Parecerista

    public function init()
    {
        if (!empty($_REQUEST['idPreProjeto'])) {
            $this->idPreProjeto = $_REQUEST['idPreProjeto'];
        }
        $auth = Zend_Auth::getInstance();
        self::$usu_identificacao = $auth->getIdentity()->usu_identificacao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        self::$codOrgao = $GrupoAtivo->codOrgao;

        parent::perfil(1, self::$perfil);
        parent::init();
        self::$codPerfil = $_SESSION['GrupoAtivo']['codGrupo'];

        switch (self::$codPerfil) {
            case 93:
                self::$titulo = 'Consultar dados do pareceristas';
                self::$perfilAtual = 'CoordenadorParecerista';
                break;
            case 137:
                self::$titulo = "Consultar pareceristas";
                self::$perfilAtual = 'CoordenadorPRONAC';
                break;
            case 94:
                self::$titulo = "Consultar dados do parecerista";
                self::$perfilAtual = 'Parecerista';
                break;
        }
    }

    public function indexAction()
    {
        $this->view->titulo = self::$titulo;
        $this->view->perfilAtual = self::$perfilAtual;
        $this->view->codOrgao = self::$codOrgao;
        $this->view->usu_identificacao = self::$usu_identificacao;
    }

    public function consultardadospareceristasAction()
    {
        $idOrgao = self::$codOrgao;
        $codGrupo = self::$codPerfil;
        $produtoDAO = new Produto();
        $OrgaosDAO = new Orgaos();
        $AgentesDAO = new Agente_Model_DbTable_Agentes();
        $AreaDAO = new Area();
        $SegmentoDAO = new Segmento();

        if (self::$perfilAtual == 'CoordenadorParecerista') {
            $this->view->Orgaos = $OrgaosDAO->buscar(array('Status = ?' => 0));
            $this->view->Areas = $AreaDAO->buscar();
            $this->view->Segmentos = $SegmentoDAO->buscar(array('stEstado = ?' => 1));
            $this->view->titulo = self::$titulo;
        }
        if (self::$perfilAtual == 'CoordenadorParecerista') {
            $this->view->pareceristas = $AgentesDAO->consultaPareceristasDoOrgao($idOrgao);
        }
        if (self::$perfilAtual == 'CoordenadorPRONAC') {
//        	$this->view->Orgaos = $OrgaosDAO->buscar(array('Status = ?' => 0));
//        	$this->view->Areas = $AreaDAO->buscar();
            $this->view->Segmentos = $SegmentoDAO->buscar(array('stEstado = ?' => 1));
            $this->view->titulo = self::$titulo;
            $this->view->pareceristas = $AgentesDAO->consultaPareceristasDoOrgao(null);
        }

        if (self::$perfilAtual == 'Parecerista') {
            $pagamentos = array();
            $pagamentos[0]['codigo'] = 2;
            $pagamentos[0]['descricao'] = "Todos";
            $pagamentos[1]['codigo'] = 1;
            $pagamentos[1]['descricao'] = "Efetuados";
            $pagamentos[2]['codigo'] = 0;
            $pagamentos[2]['descricao'] = "Pendentes";
            $this->view->Pagamentos = $pagamentos;

            $this->view->Produtos = $produtoDAO->buscar(array('stEstado = ?' => 0));
            $this->view->titulo = self::$titulo;
        }

        $this->view->perfilAtual = self::$perfilAtual;
        $this->view->codPerfil = self::$codPerfil;
    }

    public function consultarpagamentospareceristasAction()
    {
        $idOrgao = self::$codOrgao;
        $OrgaosDAO = new Orgaos();
        $codGrupo = self::$codPerfil;
        $AgentesDAO = new Agente_Model_DbTable_Agentes();
        $AreaDAO = new Area();
        $SegmentoDAO = new Segmento();
        $this->view->Orgaos = $OrgaosDAO->buscar(array('Status = ?' => 0));
        $this->view->Areas = $AreaDAO->buscar();
        $this->view->Segmentos = $SegmentoDAO->buscar(array('stEstado = ?' => 1));
//        $this->view->pareceristas = $NomesDAO->buscarPareceristas($idOrgao)->toArray();

        if (self::$codPerfil == 137) {
            $idOrgao = null;
        }

        $this->view->pareceristas = $AgentesDAO->consultaPareceristasDoOrgao($idOrgao)->toArray();
        $this->view->titulo = 'Consultar Pagamento';
        $this->view->perfilAtual = self::$perfilAtual;
    }

    public function relatoriomensaldepagamentoAction()
    {
        $idOrgao = self::$codOrgao;
        $AgentesDAO = new Agente_Model_DbTable_Agentes();

        if (self::$codPerfil == 137) {
            $idOrgao = null;
        }

        $this->view->pareceristas = $AgentesDAO->consultaPareceristasDoOrgao($idOrgao)->toArray();
        $this->view->perfilAtual = self::$perfilAtual;
        $this->view->titulo = "Relat&oacute;rio Mensal de Pagamento";
    }

    public function consultarprodutospareceristasAction()
    {
        $AgentesDAO = new Agente_Model_DbTable_Agentes();
        $logado = $AgentesDAO->buscar(array('CNPJCPF = ?'=>self::$usu_identificacao))->toArray();
        $idAgente = $logado[0]['idAgente'];

        if (self::$codPerfil == 137) {
            $idOrgao = null;
        }

        $produtos = ConsultarPareceristasDAO::buscarProdutosPareceristas($idAgente);
        $this->view->produtos = $produtos;

        $this->view->titulo = 'Consultar Produtos do Parecerista';
        $this->view->perfilAtual = self::$perfilAtual;
    }

    public function tratardadosrelatorioAction()
    {
        switch (self::$perfilAtual) {
            case 'CoordenadorParecerista':
                $this->view->perfilAtual = self::$perfilAtual;
                $titulo = $_REQUEST['titulo'];
                $idAgente = $_REQUEST['filtro']['parecerista'];
                $idArea = $_REQUEST['filtro']['area'];
                $idSegmento = $_POST['filtro']['segmento'];
                $stPrincipal = $_POST['filtro']['produto'];
                $idTipoAusencia = $_POST['filtro']['statusParecerista'];;
                $dataInicio = $_POST['filtro']['periodo']['dataInicio'];
                $dataFim = $_POST['filtro']['periodo']['datafim'];
                $dias = $_POST['dias'];

                $NomesDAO = new Nomes();
                $OrgaosDAO = new Orgaos();
                $AreaDAO = new Area();
                $SegmentoDAO = new Segmento();
                //$codOrgao = self::$codOrgao;
                $codOrgao = $_POST['filtro']['orgao'];

                $parecerista = $NomesDAO->buscarNomePorCPFCNPJ(null, $_REQUEST['filtro']['parecerista']);
                $area = $AreaDAO->buscar(array('Codigo = ?' => $idArea));
                $segmento = $SegmentoDAO->buscar(array('Codigo = ?' => $idSegmento));

                if ($idAgente == 0) {
                    parent::message("Dados obrigat�rios n�o informados!", "/consultarpareceristas/consultardadospareceristas", "ALERT");
                }
                $this->view->histFerias = 1;
                $this->view->feriasAgend = 1;
                $this->view->atestados = 1;
                $this->view->projetos = 1;
                $histFerias = null;
                $feriasAgend = null;
                $atestados = null;
                $produtos = null;

                /*-------------- AUS�NCIAS  --------------*/
                    /* 2 - Historico de Ferias*/
                    if ($idTipoAusencia == 2) {
                        $histFerias = ConsultarPareceristasDAO::buscarAusencias($idTipoAusencia, 1, $idAgente, $dataInicio, $dataFim);
                        $this->view->histFerias = $histFerias;
                    }
                    //x($histFerias);
                    /*-------------------*/

                    /* 2 - Ferias Agendadas*/
                    if ($idTipoAusencia == 2) {
                        $feriasAgend = ConsultarPareceristasDAO::buscarAusencias($idTipoAusencia, 2, $idAgente, $dataInicio, $dataFim);
                        $this->view->feriasAgend = $feriasAgend;
                    }
                    //x($feriasAgend);
                    /*-------------------*/

                    /* 1 - Atestados Medicos*/
                    if ($idTipoAusencia == 1) {
                        $atestados = ConsultarPareceristasDAO::buscarAusencias($idTipoAusencia, 3, $idAgente, $dataInicio, $dataFim);
                        $this->view->atestados = $atestados;
                    }
                    /*-------------------*/

                    /* 3 - Todos */
                    if ($idTipoAusencia == 3) {
                        $histFerias = ConsultarPareceristasDAO::buscarAusencias(2, 1, $idAgente, $dataInicio, $dataFim);
                        $this->view->histFerias = $histFerias;

                        $feriasAgend = ConsultarPareceristasDAO::buscarAusencias(2, 2, $idAgente, $dataInicio, $dataFim);
                        $this->view->feriasAgend = $feriasAgend;

                        $atestados = ConsultarPareceristasDAO::buscarAusencias(1, 3, $idAgente, $dataInicio, $dataFim);
                        $this->view->atestados = $atestados;
                    }
                    /*-------------------*/
                /*--------------- FIM AUSENCIAS --------------*/

                /*-------------- PRODUTOS  --------------*/
                       $cont = 0;
                       $produtos = ConsultarPareceristasDAO::buscarProdutos($idAgente, $stPrincipal, $codOrgao, $idArea, $idSegmento, $dias);
                       $dados = array();
                        if ($produtos) {
                            foreach ($produtos as $prod) {
                                if ($cont == 0) {
                                    $idPronac = $prod->IdPRONAC;
                                    $pronacs[] = ConsultarPareceristasDAO::buscarPronacs($idPronac);
                                } else {
                                    $idPronac = $prod->IdPRONAC;
                                    if ($idPronac_ant != $idPronac) {
                                        $pronacs[] = ConsultarPareceristasDAO::buscarPronacs($idPronac);
                                    }
                                }
                                $idPronac_ant = $idPronac;
                                $cont++;
                            }
                            if ($pronacs) {
                                foreach ($pronacs as $p) {
                                    if ($p) {
                                        $dados[] = array(
                                            'IdPRONAC'		=> $p[0]->IdPRONAC,
                                            'Pronac'		=> $p[0]->Pronac,
                                            'Area'			=> $p[0]->Area,
                                            'Segmento'		=> $p[0]->Segmento,
                                            'NomeProjeto'	=> $p[0]->NomeProjeto,
                                            'Situacao'		=> $p[0]->Situacao,
                                            'DtAnalise'		=> $p[0]->DtAnalise,
                                        );
                                    }
                                }
                            }
                        }
                        $this->view->projetos = $dados;
                           $this->view->produtos = $produtos;

                /*---------------------------------------*/
                if ((!$histFerias) && (!$feriasAgend) && (!$atestados) && (!$produtos)) {
                    parent::message("Dados n�o localizados!", "/consultarpareceristas/consultardadospareceristas", "ALERT");
                }
                $this->view->titulo = $titulo;
                  $this->view->parecerista = strtoupper($parecerista[0]['Nome']);

                break;

            case 'CoordenadorPRONAC':
                    $this->view->perfilAtual = self::$perfilAtual;

//					$orgao = $_REQUEST['filtro']['orgao'];
//					$area = $_REQUEST['filtro']['area'];
//					$segmento = $_REQUEST['filtro']['segmento'];
                    $idAgente = $_REQUEST['filtro']['parecerista'];
                    $parecer = $_REQUEST['filtro']['parecer'];
                    $dataInicio = $_REQUEST['filtro']['periodo']['dataInicio'];
                    $dataFim = $_REQUEST['filtro']['periodo']['datafim'];

                    if ($parecer == 'pago') {
                        $parecer = 4;
                    } elseif ($parecer == 'liberado') {
                        $parecer = 1;
                    } elseif ($parecer == 'todos') {
                        $parecer = 5;
                    }

                    $NomesDAO = new Nomes();
                    $parecerista = $NomesDAO->buscarNomePorCPFCNPJ(null, $_REQUEST['filtro']['parecerista']);
                    $this->view->parecerista = strtoupper($parecerista[0]['Nome']);
                    if ($idAgente == 0) {
                        parent::message("Dados obrigat�rios n�o informados!", "/consultarpareceristas/consultardadospareceristas", "ALERT");
                    }

                    $produtos = null;
                    $orgaos = null;
                     /*-------------- PRODUTOS  --------------*/
                        $liberados = null;
                        $pagos = null;
                        $produtos = ConsultarPareceristasDAO::buscarProdutosPareceristas($idAgente, null, $dataInicio, $dataFim, $parecer);
//		                if($orgao){
//		                	foreach ($produtos as $p){
//		                		$idPronac = $p->idPronac;
//		                		$orgaos[] = ConsultarPareceristasDAO::buscarOrgaos($idAgente, $orgao, $idPronac);
//		                	}
//		                }
                               $dados = array();
                               $dadosPagos = array();
                               $dadosLiberados = array();
                            if ($produtos) {
                                foreach ($produtos as $prod) {
                                    $idPronac = $prod->idPronac;
                                    $pronacs[] = ConsultarPareceristasDAO::buscarPronacs($idPronac);

                                    if ($prod->TipoParecer == 4) {
                                        $pagos[] = ConsultarPareceristasDAO::buscarPronacs($idPronac);
                                    } elseif ($prod->TipoParecer != 4) {
                                        $liberados[] = ConsultarPareceristasDAO::buscarPronacs($idPronac);
                                    }
                                }
                                if ($pronacs) {
                                    if ($pagos) {
                                        foreach ($pagos as $p) {
                                            if ($p) {
                                                $dadosPagos[] = array(
                                                    'IdPRONAC'		=> $p[0]->IdPRONAC,
                                                    'Pronac'		=> $p[0]->Pronac,
                                                    'Area'			=> $p[0]->Area,
                                                    'Segmento'		=> $p[0]->Segmento,
                                                    'NomeProjeto'	=> $p[0]->NomeProjeto,
                                                    'Situacao'		=> $p[0]->Situacao,
                                                    'DtAnalise'		=> $p[0]->DtAnalise
                                                );
                                            }
                                        }
                                    }

                                    if ($liberados) {
                                        foreach ($liberados as $l) {
                                            if ($l) {
                                                $dadosLiberados[] = array(
                                                    'IdPRONAC'		=> $l[0]->IdPRONAC,
                                                    'Pronac'		=> $l[0]->Pronac,
                                                    'Area'			=> $l[0]->Area,
                                                    'Segmento'		=> $l[0]->Segmento,
                                                    'NomeProjeto'	=> $l[0]->NomeProjeto,
                                                    'Situacao'		=> $l[0]->Situacao,
                                                    'DtAnalise'		=> $l[0]->DtAnalise
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        $this->view->projetosPagos = $dadosPagos;
                        $this->view->projetosLiberados = $dadosLiberados;
                        $this->view->produtos = $produtos;
                    /*---------------------------------------*/
                    if (!$produtos) {
                        parent::message("Dados n�o localizados!", "/consultarpareceristas/consultardadospareceristas", "ALERT");
                    }

                break;
            case 'Parecerista':
                $this->view->perfilAtual = self::$perfilAtual;

                if (isset($_POST['prod'])) {
                    $this->view->prod = $_POST['prod'];
                    $pronac = $_POST['pronac'];
                    $produto = $_POST['filtro']['produto'];
                    $tipo_produto = $_POST['filtro']['tipo_produto'];
                    $tipo_pagamento = $_POST['filtro']['pagamento'];
                    $data_inicio = $_POST['filtro']['periodo']['dataInicio'];
                    $data_fim = $_POST['filtro']['periodo']['datafim'];
                    $idPronac = '';
                    $AgentesDAO = new Agente_Model_DbTable_Agentes();
                    $logado = $AgentesDAO->buscar(array('CNPJCPF = ?'=>self::$usu_identificacao))->toArray();
                    $idAgente = $logado[0]['idAgente'];

                    if ($pronac) {
                        $ProjetosDAO = ProjetoDAO::buscar($pronac);

                        if ($ProjetosDAO) {
                            $idPronac = $ProjetosDAO[0]->IdPRONAC;
                        } else {
                            parent::message("Pronac Inexistente", "consultarpareceristas/consultarprodutospareceristas", "ALERT");
                        }
                    }

                    /*-------------- PRODUTOS  --------------*/
                    $produtos = ConsultarPareceristasDAO::buscarProdutosPareceristas($idAgente, $tipo_produto, $data_inicio, $data_fim, null, $idPronac, $tipo_pagamento);
                    $dados = array();
                    if ($produtos) {
                        foreach ($produtos as $prod) {
                            $idPronac = $prod->idPronac;
                            $pronacs[] = ConsultarPareceristasDAO::buscarPronacs($idPronac, $area, $segmento);
                        }
                        if ($pronacs) {
                            foreach ($pronacs as $p) {
                                if ($p) {
                                    $dados[] = array(
                                        'IdPRONAC'		=> $p[0]->IdPRONAC,
                                        'Pronac'		=> $p[0]->Pronac,
                                        'Area'			=> $p[0]->Area,
                                        'Segmento'		=> $p[0]->Segmento,
                                        'NomeProjeto'	=> $p[0]->NomeProjeto,
                                        'Situacao'		=> $p[0]->Situacao,
                                        'DtAnalise'		=> $p[0]->DtAnalise,
                                        'vlPagamento'	=> $p[0]->vlPagamento,
                                        'memorando'		=> $p[0]->memorando
                                    );
                                }
                            }
                        }
                    }

                    $this->view->projetos = $dados;
                    $this->view->produtos = $produtos;
                    /*---------------------------------------*/
                    if (!$produtos) {
                        parent::message("Dados n�o localizados!", "/consultarpareceristas/consultardadospareceristas", "ALERT");
                    }
                } else {
                    if (isset($_POST['filtro']['produto'])) {
                        $produto = $_POST['filtro']['produto'];
                    } else {
                        $produto = 0;
                    }
                    if (isset($_POST['filtro']['pagamento'])) {
                        $tipo_pagamento = $_POST['filtro']['pagamento'];
                    } else {
                        $tipo_pagamento = 2;
                    }

//            		$status_pagamento = $_POST['filtro']['statusPagamento'];
                    if (isset($_POST['filtro']['periodo']['dataInicio'])) {
                        $data_inicio = $_POST['filtro']['periodo']['dataInicio'];
                    } else {
                        $data_inicio = null;
                    }
                    if (isset($_POST['filtro']['periodo']['datafim'])) {
                        $data_fim = $_POST['filtro']['periodo']['datafim'];
                    } else {
                        $data_fim = null;
                    }
                    $pronac = $_POST['pronac'];

                    $AgentesDAO = new Agente_Model_DbTable_Agentes();
                    $logado = $AgentesDAO->buscar(array('CNPJCPF = ?'=>self::$usu_identificacao))->toArray();
                    $idAgente = $logado[0]['idAgente'];

                    $NomesDAO = new Nomes();
                    $parecerista = $NomesDAO->buscarNomePorCPFCNPJ(null, $idAgente);
                    $this->view->parecerista = strtoupper($parecerista[0]['Nome']);

                    /*-------------- PRODUTOS  --------------*/
                    $produtos = ConsultarPareceristasDAO::buscarProdutosPareceristas($idAgente, $produto, $data_inicio, $data_fim, null, null, $tipo_pagamento, $pronac);
                    $dados = array();
                    if ($produtos) {
                        foreach ($produtos as $prod) {
                            $idPronac = $prod->idPronac;
                            $pronacs[] = ConsultarPareceristasDAO::buscarPronacs($idPronac);
                        }
                        if ($pronacs) {
                            foreach ($pronacs as $p) {
                                if ($p) {
                                    $dados[] = array(
                                        'IdPRONAC'		=> $p[0]->IdPRONAC,
                                        'Pronac'		=> $p[0]->Pronac,
                                        'Area'			=> $p[0]->Area,
                                        'Segmento'		=> $p[0]->Segmento,
                                        'NomeProjeto'	=> $p[0]->NomeProjeto,
                                        'Situacao'		=> $p[0]->Situacao,
                                        'DtAnalise'		=> $p[0]->DtAnalise,
                                    );
                                }
                            }
                        }
                    }

                    $this->view->projetos = $dados;
                    $this->view->produtos = $produtos;
                    /*---------------------------------------*/
                    if (!$produtos) {
                        parent::message("Dados n�o localizados!", "/consultarpareceristas/consultardadospareceristas", "ALERT");
                    }
                }
                break;
        }
    }

    public function tratardadosrelatoriopagamentoAction()
    {
        if (self::$perfilAtual == 'CoordenadorPRONAC') {
            $this->view->perfilAtual = self::$perfilAtual;
            if (isset($_POST['prod'])) {
                $this->view->prod = $_POST['prod'];
                //					$orgao = $_REQUEST['filtro']['orgao'];
                //					$area = $_REQUEST['filtro']['area'];
                //					$segmento = $_REQUEST['filtro']['segmento'];
                $idAgente = $_REQUEST['filtro']['parecerista'];
                $parecer = $_REQUEST['filtro']['parecer'];
                $dataInicio = $_REQUEST['filtro']['periodo']['dataInicio'];
                $dataFim = $_REQUEST['filtro']['periodo']['datafim'];

                if ($parecer == 'pago') {
                    $parecer = 4;
                } elseif ($parecer == 'liberado') {
                    $parecer = 1;
                } elseif ($parecer == 'todos') {
                    $parecer = 5;
                }

                $NomesDAO = new Nomes();
                $parecerista = $NomesDAO->buscarNomePorCPFCNPJ(null, $_REQUEST['filtro']['parecerista']);
                $this->view->parecerista = strtoupper($parecerista[0]['Nome']);

                if ($idAgente == 0) {
                    parent::message("Dados obrigat�rios n�o informados!", "/consultarpareceristas/relatoriomensaldepagamento", "ALERT");
                }
                /*-------------- PRODUTOS  --------------*/
                $produtos = ConsultarPareceristasDAO::buscarProdutosPareceristas($idAgente, null, $dataInicio, $dataFim, $parecer);
                $dados = array();
                $orgaos = 1;
//        				if($orgao){
                //		                	foreach ($produtos as $p){
                //		                		$idPronac = $p->idPronac;
                //		                		$orgaos[] = ConsultarPareceristasDAO::buscarOrgaos($idAgente, $orgao, $idPronac);
                //		                	}
//        				}
                if ($produtos) {
                    foreach ($produtos as $prod) {
                        $idPronac = $prod->idPronac;
                        $pronacs[] = ConsultarPareceristasDAO::buscarPronacs($idPronac);
                    }
                    if ($pronacs) {
                        foreach ($pronacs as $p) {
                            if ($p) {
                                $dados[] = array(
                                                'IdPRONAC'		=> $p[0]->IdPRONAC,
                                                'Pronac'		=> $p[0]->Pronac,
                                                'Area'			=> $p[0]->Area,
                                                'Segmento'		=> $p[0]->Segmento,
                                                'NomeProjeto'	=> $p[0]->NomeProjeto,
                                                'Situacao'		=> $p[0]->Situacao,
                                                'DtAnalise'		=> $p[0]->DtAnalise
                                            );
                            }
                        }
                    }
                }

                $this->view->projetos = $dados;
                $this->view->produtos = $produtos;
                /*---------------------------------------*/
                if (!$produtos) {
                    parent::message("Dados n�o localizados!", "/consultarpareceristas/relatoriomensaldepagamento", "ALERT");
                }
            } else {
                $this->view->perfilAtual = self::$perfilAtual;
                $NomesDAO = new Nomes();
                $parecerista = $NomesDAO->buscarNomePorCPFCNPJ(null, $_REQUEST['filtro']['parecerista']);
                $this->view->parecerista = strtoupper($parecerista[0]['Nome']);
                $titulo = $_REQUEST['titulo'];
                $idAgente = $_REQUEST['filtro']['parecerista'];
                $orgao = $_REQUEST['filtro']['orgao'];
                $area = $_REQUEST['filtro']['area'];
                $segmento = $_POST['filtro']['segmento'];
                $dataInicio = $_POST['filtro']['periodo']['dataInicio'];
                $dataFim = $_POST['filtro']['periodo']['datafim'];

                /*-------------- PRODUTOS  --------------*/
                $produtos = ConsultarPareceristasDAO::buscarProdutosPareceristas($idAgente, null, $dataInicio, $dataFim);
                //$produtos = ConsultarPareceristasDAO::buscarProdutosTeste($idAgente);
                $dados = array();
                if ($produtos) {
                    foreach ($produtos as $prod) {
                        $idPronac = $prod->idPronac;
                        $pronacs[] = ConsultarPareceristasDAO::buscarPronacs($idPronac, $area, $segmento);
                    }
                    if ($pronacs) {
                        foreach ($pronacs as $p) {
                            if ($p) {
                                $dados[] = array(
                                            'IdPRONAC'		=> $p[0]->IdPRONAC,
                                            'Pronac'		=> $p[0]->Pronac,
                                            'Area'			=> $p[0]->Area,
                                            'Segmento'		=> $p[0]->Segmento,
                                            'NomeProjeto'	=> $p[0]->NomeProjeto,
                                            'Situacao'		=> $p[0]->Situacao,
                                            'DtAnalise'		=> $p[0]->DtAnalise
                                        );
                            }
                        }
                    }
                }

                $this->view->projetos = $dados;
                $this->view->produtos = $produtos;
                if (!$produtos) {
                    parent::message("Dados n�o localizados!", "/consultarpareceristas/consultarpagamentospareceristas", "ALERT");
                }
                /*---------------------------------------*/
            }
        }
        if (self::$perfilAtual == 'Parecerista') {
            if ($_POST) {
                $this->view->prod = $_POST['prod'];
                $this->view->parecerista = '';
                $pronac = $_POST['pronac'];
                $produto = $_POST['filtro']['produto'];
                $tipo_produto = $_POST['filtro']['tipo_produto'];
                $tipo_pagamento = $_POST['filtro']['pagamento'];
                $data_inicio = $_POST['filtro']['periodo']['dataInicio'];
                $data_fim = $_POST['filtro']['periodo']['datafim'];
                $idPronac = '';
                $AgentesDAO = new Agente_Model_DbTable_Agentes();
                $logado = $AgentesDAO->buscar(array('CNPJCPF = ?'=>self::$usu_identificacao))->toArray();
                $idAgente = $logado[0]['idAgente'];

                if (!empty($pronac)) {
                    $ProjetosDAO = ProjetoDAO::buscar($pronac);

                    if (!empty($ProjetosDAO)) {
                        $idPronac = $ProjetosDAO[0]->IdPRONAC;
                    } else {
                        parent::message("Pronac Inexistente", "consultarpareceristas/consultarprodutospareceristas", "ALERT");
                    }
                }

                /*-------------- PRODUTOS  --------------*/
                $produtos = ConsultarPareceristasDAO::buscarProdutosPareceristas($idAgente, $tipo_produto, $data_inicio, $data_fim, null, $idPronac, $tipo_pagamento);
                $dados = array();
                if ($produtos) {
                    foreach ($produtos as $prod) {
                        $idPronac = $prod->idPronac;
                        $pronacs[] = ConsultarPareceristasDAO::buscarPronacs($idPronac);
                    }
                    if ($pronacs) {
                        foreach ($pronacs as $p) {
                            if ($p) {
                                $dados[] = array(
                                        'IdPRONAC'		=> $p[0]->IdPRONAC,
                                        'Pronac'		=> $p[0]->Pronac,
                                        'Area'			=> $p[0]->Area,
                                        'Segmento'		=> $p[0]->Segmento,
                                        'NomeProjeto'	=> $p[0]->NomeProjeto,
                                        'Situacao'		=> $p[0]->Situacao,
                                        'DtAnalise'		=> $p[0]->DtAnalise,
                                    );
                            }
                        }
                    }
                }

                $this->view->projetos = $dados;
                $this->view->produtos = $produtos;
                /*---------------------------------------*/
                if (count($produtos) <= 0) {
                    parent::message("Dados n�o localizados!", "/consultarpareceristas/consultarprodutospareceristas", "ALERT");
                }
            }
        }
    }

    public function carregarhistoricoAction()
    {
        $this->view->perfilAtual = self::$perfilAtual;

        $Pronac = $_GET['Pronac'];
        $idPronac = $_GET['idPronac'];

        $projeto = new ProjetoDAO();
        $dadosProjeto = $projeto->buscar($Pronac);

        $this->view->PRONAC = $dadosProjeto[0]->pronac;
        $this->view->nomeProjeto = $dadosProjeto[0]->NomeProjeto;

        $buscaHistorico = new Parecer_Model_DbTable_TbDistribuirParecer();
        $historico = $buscaHistorico->buscarHistoricoDeAnalise($idPronac, self::$codOrgao);

        $this->view->Historico = $historico;
    }

    public function ajaxcarregarsegmentosAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        if ($_POST['codigo'] != '') {
            $segmentos = new Segmentocultural();
            $dados = $segmentos->carregarSegmentosArea($_POST['codigo']);
            $html = '';
            if ($dados) {
                foreach ($dados as $dado) {
                    $html .= "<option value={$dado->codigo}>" . utf8_encode($dado->descricao) . "</option>";
                }
            }
            echo $html;
        } else {
            $segmentos = new Segmentocultural();
            $dados = $segmentos->carregarSegmentosArea();
            $html = '';
            if ($dados) {
                foreach ($dados as $dado) {
                    $html .= "<option value={$dado->codigo}>" . utf8_encode($dado->descricao) . "</option>";
                }
            }
            echo $html;
        }
    }

    public function ajaxcarregarpareceristasAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        if ($_REQUEST['idOrgao'] != '') {
            $agentes = new Agente_Model_DbTable_Agentes();
            $dados = $agentes->consultaPareceristasDoOrgao($_REQUEST['idOrgao']);
            $html = '';
            if ($dados) {
                foreach ($dados as $dado) {
                    $html .= "<option value={$dado->idParecerista}>" . utf8_encode($dado->Nome) . "</option>";
                }
            }
            echo $html;
        } else {
            $agentes = new Agente_Model_DbTable_Agentes();
            $dados = $agentes->consultaPareceristasDoOrgao();
            $html = '';
            if ($dados) {
                foreach ($dados as $dado) {
                    $html .= "<option value={$dado->idParecerista}>" . utf8_encode($dado->Nome) . "</option>";
                }
            }
            echo $html;
        }
    }

    public function buscaprojetoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $pronac = $post->pronac;

        $Projeto = new Projetos();
        $buscaProjeto = $Projeto->buscar(array('(AnoProjeto+Sequencial) = ?' => $pronac))->toArray();

        if (count($buscaProjeto) > 0) {
            echo utf8_encode($buscaProjeto[0]['NomeProjeto']);
        } else {
            echo '<span style="color:red">'. utf8_encode('O Pronac '. $pronac . ' � inexistente!') . '</span>';
        }
    }
}
