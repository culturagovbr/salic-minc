<?php

class VerificarAlteracaoTecnicoController extends MinC_Controller_Action_Abstract
{


    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        /* $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista*/
        $PermissoesGrupo[] = 129; // T�cnico
        $PermissoesGrupo[] = 121; // T�cnico
        /*$PermissoesGrupo[] = 122; // Coordenador de Acompanhamento*/
        parent::perfil(1, $PermissoesGrupo);

        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $this->view->agente = $agente['idAgente'];
        
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->codGrupo = $GrupoAtivo->codGrupo;

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    // fecha m�todo init()



    public function indexAction()
    {
        $resultadobusca = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao();
//        echo '<pre>';print_r($resultadobusca);die;
        $Result['AltNmProp']     = array();
        $Result['AltRaz']        = array();
        $Result['FicTec']        = array();
        $Result['LocRel']        = array();
        $Result['AltNomProj']    = array();
        $Result['AltProrPrazC']  = array();
        $Result['AltProrPrazE']  = array();
        $validardata             = array();
        foreach ($resultadobusca as $ResultAltBusca) {
            switch ($ResultAltBusca->tpAlteracaoProjeto) {
                case 1:
                    {
                        $Result['AltNmProp'][]                  = $ResultAltBusca;
                        break;
                    }
                case 2:
                    {
                        $Result['AltRaz'][]                     = $ResultAltBusca;
                        break;
                    }
                case 3:
                    {
                        $Result['FicTec'][]                     = $ResultAltBusca;
                        break;
                    }
                case 4:
                    {
                        $Result['LocRel'][]                     = $ResultAltBusca;
                        break;
                    }
                case 5:
                    {
                        $Result['AltNomProj'][]                  = $ResultAltBusca;
                        break;
                    }
                case 6:
                    {
                        if ($ResultAltBusca->tpProrrogacao == 'C') {
                            $Result['AltProrPrazC'][]                  = $ResultAltBusca;
                        } else {
                            $Result['AltProrPrazE'][]                  = $ResultAltBusca;
                        }
                        break;
                    }
                default: break;
            }
        }
        $Total['AltNmProp']          = count($Result['AltNmProp']);
        $Total['AltRaz']             = count($Result['AltRaz']);
        $Total['FicTec']             = count($Result['FicTec']);
        $Total['LocRel']             = count($Result['LocRel']);
        $Total['AltNomProj']         = count($Result['AltNomProj']);
        $Total['AltProrPrazC']       = count($Result['AltProrPrazC']);
        $Total['AltProrPrazE']       = count($Result['AltProrPrazE']);

        $this->view->resultBusca   = $Result;
        $this->view->resultTotal   = $Total;
    }
    /*
    *  View: Solicita��o de Altera��o do Nome do Projeto
    */
    public function solaltnomprojAction()
    {
        if ($_POST) {
            $recebidoPost = Zend_Registry::get('post');
            
            if ($recebidoPost->solicitacao) {
                if (self::PropostaDiligenciar()) {
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltnomproj?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                } else {
                    //parent::message("Erro ao diligenciar a solicita��o", "verificaralteracaotecnico/solaltnomproj?idpedidoalteracao=$recebidoPost->IdPronac" ,"ERROR");
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltnomproj?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                }
            }
        }

        if (isset($_GET['opcao'])) {
            $idPedidoAlteracao = $_GET['id']; //idPedido Altera��o � o idAvaliacaoItemPedidoAlteracao da tabela tbAvaliacaoItemPedidoAlteracao
            $opcao = $_GET['opcao']; //op��o escolhida no select - APROVADO, INDEFERIDO ou EM AN�LISE
            $IdPronac = $_GET['idpedidoalteracao'];

            self::streadequacaoprodutosAction($idPedidoAlteracao, $opcao, $IdPronac, 'solaltnomproj');
        }


        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao = $recebidoGet->idpedidoalteracao;
        $resultadoDadosAlteracaoNomeProjeto = PedidoAlteracaoDAO::buscarAlteracaoNomeProjeto($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $resultadoDadosAlteracaoNomeProjeto['idPedidoAlteracao']);
        $arquivos = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 5, $resultadoDadosAlteracaoNomeProjeto['idPedidoAlteracao']);
        $this->view->resultArquivo = $arquivos;
        $this->view->resultAlteracaoNomeProjeto = $resultadoDadosAlteracaoNomeProjeto;
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->idpedidoalteracao = $idpedidoalteracao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpedidoalteracao;
        $this->view->menumsg = 'true';
        //****************************************************

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaNomeProjEditar", $idpedidoalteracao, 5, null, $resultadoDadosAlteracaoNomeProjeto['idPedidoAlteracao']);
        $dados = $db->fetchAll($sqlproposta);
        if ($dados) {
            $this->view->dados = $dados[0];
            $idPedidoAlt = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //VERIFICA O STATUS DA SOLICITA��O
            $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);
       
            $this->view->stResult = $db->fetchAll($sqlStatusReadequacao);
        }

        /*$recebidoGet  = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $resultadoBuscaPedidoAlteracao = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultArquivo  = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
        $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
    }

    /*
    *  View: Solicita��o de Altera��o Raz�o Social
    *  @abstract
    */
    public function solaltrazsocAction()
    {
        if ($_POST) {
            $recebidoPost = Zend_Registry::get('post');
            
            if ($recebidoPost->solicitacao) {
                if (self::PropostaDiligenciar()) {
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltrazsoc?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                } else {
                    //parent::message("Erro ao diligenciar a solicita��o", "verificaralteracaotecnico/solaltrazsoc?idpedidoalteracao=$recebidoPost->IdPronac" ,"ERROR");
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltrazsoc?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                }
            }
        }

        if (isset($_GET['opcao'])) {
            $idPedidoAlteracao = $_GET['id']; //idPedido Altera��o � o idAvaliacaoItemPedidoAlteracao da tabela tbAvaliacaoItemPedidoAlteracao
            $opcao = $_GET['opcao']; //op��o escolhida no select - APROVADO, INDEFERIDO ou EM AN�LISE
            $IdPronac = $_GET['idpedidoalteracao'];

            self::streadequacaoprodutosAction($idPedidoAlteracao, $opcao, $IdPronac, 'solaltrazsoc');
        }

        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $resultadoDadosAlteracaoRazaoSocial = PedidoAlteracaoDAO::buscarAlteracaoRazaoSocial($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $resultadoDadosAlteracaoRazaoSocial['idPedidoAlteracao']);
        $arquivos = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 2, $resultadoDadosAlteracaoRazaoSocial['idPedidoAlteracao']);
        $this->view->resultArquivo = $arquivos;
        $this->view->resultAlteracaoRazaoSocial = $resultadoDadosAlteracaoRazaoSocial;
        $this->view->resultProjeto  = AlteracaoNomeProponenteDAO::buscarProjPorProp($resultadoBuscaPedidoAlteracao['CgcCpf']);
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->idpedidoalteracao = $idpedidoalteracao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpedidoalteracao;
        $this->view->menumsg = 'true';
        //****************************************************

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaNomeProjEditar", $idpedidoalteracao, 2, null, $resultadoDadosAlteracaoRazaoSocial['idPedidoAlteracao']);
        $dados = $db->fetchAll($sqlproposta);
        if ($dados) {
            $this->view->dados = $dados[0];
            $idPedidoAlt = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //VERIFICA O STATUS DA SOLICITA��O
            $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);

            $this->view->stResult = $db->fetchAll($sqlStatusReadequacao);
        }

        /*$recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $resultadoBuscaPedidoAlteracao = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultArquivo  = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
        $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
        
        $proponenteHabilitado = true;
        
        $inabilitadoDAO = new Inabilitado();
        $where['CgcCpf 		= ?'] = $resultadoDadosAlteracaoRazaoSocial['CgcCpf'];
        $where['Habilitado 	= ?'] = 'N';
        $busca = $inabilitadoDAO->Localizar($where);

        if (count($busca) > 0) {
            $proponenteHabilitado = false;
        }
        
        $this->view->novoproponentehabilitado = $proponenteHabilitado;
    }

    /*
    *  View: Solicita��o de Altera��o do Nome do Proponente
    */
    public function solaltnomprpAction()
    {
        if ($_POST) {
            $recebidoPost = Zend_Registry::get('post');

            if ($recebidoPost->solicitacao) {
                if (self::PropostaDiligenciar()) {
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltnomprp?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                } else {
                    //parent::message("Erro ao diligenciar a solicita��o", "verificaralteracaotecnico/solaltnomprp?idpedidoalteracao=$recebidoPost->IdPronac" ,"ERROR");
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltnomprp?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                }
            }
        }

        if (isset($_GET['opcao'])) {
            $idPedidoAlteracao = $_GET['id']; //idPedido Altera��o � o idAvaliacaoItemPedidoAlteracao da tabela tbAvaliacaoItemPedidoAlteracao
            $opcao = $_GET['opcao']; //op��o escolhida no select - APROVADO, INDEFERIDO ou EM AN�LISE
            $IdPronac = $_GET['idpedidoalteracao'];

            self::streadequacaoprodutosAction($idPedidoAlteracao, $opcao, $IdPronac, 'solaltnomprp');
        }


        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $resultadoDadosAlteracaoNomeProponente = PedidoAlteracaoDAO::buscarAlteracaoNomeProponente($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $resultadoDadosAlteracaoNomeProponente['idPedidoAlteracao']);
        $arquivos = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 1, $resultadoDadosAlteracaoNomeProponente['idPedidoAlteracao']);
        $this->view->resultArquivo = $arquivos;
        $this->view->resultAlteracaoNomeProponente = $resultadoDadosAlteracaoNomeProponente;
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultProjeto  = AlteracaoNomeProponenteDAO::buscarProjPorProp($resultadoBuscaPedidoAlteracao['CgcCpf']);
        $this->view->idpedidoalteracao = $idpedidoalteracao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpedidoalteracao;
        $this->view->menumsg = 'true';
        //****************************************************
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaNomeProjEditar", $idpedidoalteracao, 1, null, $resultadoDadosAlteracaoNomeProponente['idPedidoAlteracao']);
        $dados = $db->fetchAll($sqlproposta);
        if ($dados) {
            $this->view->dados = $dados[0];
            $idPedidoAlt = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //VERIFICA O STATUS DA SOLICITA��O
            $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);

            $this->view->stResult = $db->fetchAll($sqlStatusReadequacao);
        }

        /*$recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $resultadoBuscaPedidoAlteracao = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultArquivo  = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
        $this->view->resultProjeto  = tbalteracaonomeproponenteDAO::buscarProjPorProp($resultadoBuscaPedidoAlteracao[0]->CgcCpf);
        $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
    }
    /*
    *  View: Solicita��o de Altera��o do Local de Realiza��o
    */
    public function solaltlocrelAction()
    {
        $tbAbrangencia = new Proposta_Model_DbTable_Abrangencia();

        if ($_POST) {
            $recebidoPost = Zend_Registry::get('post');
            
            if ($recebidoPost->solicitacao) {
                if (self::PropostaDiligenciar()) {
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltlocrel?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                } else {
                    //parent::message("Erro ao diligenciar a solicita��o", "verificaralteracaotecnico/solaltlocrel?idpedidoalteracao=$recebidoPost->IdPronac" ,"ERROR");
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltlocrel?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                }
            }
        }

        if (isset($_GET['opcao'])) {
            $idPedidoAlteracao = $_GET['id']; //idPedido Altera��o � o idAvaliacaoItemPedidoAlteracao da tabela tbAvaliacaoItemPedidoAlteracao
            $opcao = $_GET['opcao']; //op��o escolhida no select - APROVADO, INDEFERIDO ou EM AN�LISE
            $IdPronac = $_GET['idpedidoalteracao'];

            self::streadequacaoprodutosAction($idPedidoAlteracao, $opcao, $IdPronac, 'solaltlocrel');
        }

        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $buscaAb = $tbAbrangencia->buscarDadosAbrangenciaSolicitadaLocal($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $buscaAb[0]->idPedidoAlteracao);

        if (AvaliacaoSubItemPedidoAlteracaoDAO::buscar($resultadoBuscaPedidoAlteracao['idAvaliacao'])) {
            $resultadoDadosAlteracaoLocalRealizacao = $tbAbrangencia->buscarDadosAbrangenciaAlteracao($idpedidoalteracao, 'COM_AVALIACAO');
        } else {
            $resultadoDadosAlteracaoLocalRealizacao = $tbAbrangencia->buscarDadosAbrangenciaAlteracao($idpedidoalteracao, 'SEM_AVALIACAO');
        }

        $arquivos = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 4, $buscaAb[0]->idPedidoAlteracao);
        $this->view->resultLocalRel     = $tbAbrangencia->buscarDadosAbrangencia($idpedidoalteracao);
        $this->view->resultArquivo = $arquivos;
        $this->view->resultAbrangencia = $resultadoDadosAlteracaoLocalRealizacao;
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->idpedidoalteracao = $idpedidoalteracao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpedidoalteracao;
        $this->view->menumsg = 'true';
        //****************************************************
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaNomeProjEditar", $idpedidoalteracao, 4, null, $buscaAb[0]->idPedidoAlteracao);
        $dados = $db->fetchAll($sqlproposta);
        if ($dados) {
            $this->view->dados = $dados[0];
            $idPedidoAlt = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //VERIFICA O STATUS DA SOLICITA��O
            $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);

            $this->view->stResult = $db->fetchAll($sqlStatusReadequacao);
        }

        /* $recebidoGet = Zend_Registry::get('get');
         $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
         $resultadoBuscaPedidoAlteracao  = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
         $this->view->resultConsulta     = $resultadoBuscaPedidoAlteracao;
         $this->view->resultAbrangencia  = tbAbrangenciaDAO::buscarDadosAbrangencia($resultadoBuscaPedidoAlteracao[0]->idprojeto);
         $this->view->resultLocalRel     = tbalteracaolocalrealizacaoDAO::buscarDadosAltLocRel($idpedidoalteracao);
         $this->view->resultArquivo      = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
         $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
    }

    /*
    *  View: Solicita��o de Altera��o da Ficha t�cnica
    */
    public function solaltfictecAction()
    {
        if ($_POST) {
            $recebidoPost = Zend_Registry::get('post');
            
            if ($recebidoPost->solicitacao) {
                if (self::PropostaDiligenciar()) {
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltfictec?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                } else {
                    //parent::message("Erro ao diligenciar a solicita��o", "verificaralteracaotecnico/solaltfictec?idpedidoalteracao=$recebidoPost->IdPronac" ,"ERROR");
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltfictec?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                }
            }
        }

        if (isset($_GET['opcao'])) {
            $idPedidoAlteracao = $_GET['id']; //idPedido Altera��o � o idAvaliacaoItemPedidoAlteracao da tabela tbAvaliacaoItemPedidoAlteracao
            $opcao = $_GET['opcao']; //op��o escolhida no select - APROVADO, INDEFERIDO ou EM AN�LISE
            $IdPronac = $_GET['idpedidoalteracao'];

            self::streadequacaoprodutosAction($idPedidoAlteracao, $opcao, $IdPronac, 'solaltfictec');
        }

        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao                       = $recebidoGet->idpedidoalteracao;
        $resultadoDadosAlteracaoFichaTecnica     = PedidoAlteracaoDAO::buscarAlteracaoFichaTecnica($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao           = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $resultadoDadosAlteracaoFichaTecnica['idPedidoAlteracao']);
        $fichatecnica                            = FichaTecnicaDAO::buscarFichaTecnica($idpedidoalteracao, $resultadoDadosAlteracaoFichaTecnica['idPedidoAlteracao']);
        $arquivos                                = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 3, $resultadoDadosAlteracaoFichaTecnica['idPedidoAlteracao']);
        $this->view->resultArquivo               = $arquivos;
        $this->view->fichaTecnica                = $fichatecnica;
        $this->view->resultAlteracaoFichaTecnica = $resultadoDadosAlteracaoFichaTecnica;
        $this->view->resultConsulta              = $resultadoBuscaPedidoAlteracao;
        $this->view->idpedidoalteracao = $idpedidoalteracao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpedidoalteracao;
        $this->view->menumsg = 'true';
        //****************************************************

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaNomeProjEditar", $idpedidoalteracao, 3, null, $resultadoDadosAlteracaoFichaTecnica['idPedidoAlteracao']);
        $dados = $db->fetchAll($sqlproposta);
        if ($dados) {
            $this->view->dados = $dados[0];
            $idPedidoAlt = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //VERIFICA O STATUS DA SOLICITA��O
            $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);

            $this->view->stResult = $db->fetchAll($sqlStatusReadequacao);
        }

        /*$recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $resultadoBuscaPedidoAlteracao = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultArquivo  = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
        $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
    }

    /*
    *  View: Solicita��o de Prorrogacao de Prazos - Capta��o
    */
    public function solaltprogprazcapAction()
    {
        if ($_POST) {
            $recebidoPost = Zend_Registry::get('post');
            
            if ($recebidoPost->solicitacao) {
                if (self::PropostaDiligenciar()) {
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltprogprazcap?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                } else {
                    //parent::message("Erro ao diligenciar a solicita��o", "verificaralteracaotecnico/solaltprogprazcap?idpedidoalteracao=$recebidoPost->IdPronac" ,"ERROR");
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltprogprazcap?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                }
            }
        }

        if (isset($_GET['opcao'])) {
            $idPedidoAlteracao = $_GET['id']; //idPedido Altera��o � o idAvaliacaoItemPedidoAlteracao da tabela tbAvaliacaoItemPedidoAlteracao
            $opcao = $_GET['opcao']; //op��o escolhida no select - APROVADO, INDEFERIDO ou EM AN�LISE
            $IdPronac = $_GET['idpedidoalteracao'];

            self::streadequacaoprodutosAction($idPedidoAlteracao, $opcao, $IdPronac, 'solaltprogprazcap');
        }

        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao                        = $recebidoGet->idpedidoalteracao;
        $resultadoDadosAlteracaoPrazoCaptacao     = PedidoAlteracaoDAO::buscarAlteracaoPrazoCaptacao($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao            = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $resultadoDadosAlteracaoPrazoCaptacao['idPedidoAlteracao']);
        $arquivos                                 = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 8, $resultadoDadosAlteracaoPrazoCaptacao['idPedidoAlteracao']);
        $porcentagem                              = porcentagemCaptacaoDao::buscarDadosProrrogacaoPrazo($resultadoBuscaPedidoAlteracao['ano'], $resultadoBuscaPedidoAlteracao['seq']);
        $this->view->resultDadosBanc              = ContaBancariaDAO::buscarDadosContaBancaria($resultadoBuscaPedidoAlteracao['pronac']);
        $this->view->porcentagem                  = ($porcentagem[0]->computed == '')?'0%':$porcentagem[0]->computed.'%';
        $this->view->resultArquivo                = $arquivos;
        $this->view->resultAlteracaoPrazoCaptacao = $resultadoDadosAlteracaoPrazoCaptacao;
        $this->view->resultConsulta               = $resultadoBuscaPedidoAlteracao;
        $this->view->idpedidoalteracao = $idpedidoalteracao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpedidoalteracao;
        $this->view->menumsg = 'true';
        //****************************************************

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaNomeProjEditar", $idpedidoalteracao, 8, null, $resultadoDadosAlteracaoPrazoCaptacao['idPedidoAlteracao']);
        $dados = $db->fetchAll($sqlproposta);
        if ($dados) {
            $this->view->dados = $dados[0];
            $idPedidoAlt = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //VERIFICA O STATUS DA SOLICITA��O
            $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);

            $this->view->stResult = $db->fetchAll($sqlStatusReadequacao);
        }


        /*$recebidoGet                    = Zend_Registry::get('get');
        $idpedidoalteracao              = $recebidoGet->idpedidoalteracao;
        $resultadoBuscaPedidoAlteracao  = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultConsulta     = $resultadoBuscaPedidoAlteracao;
        $this->view->resultDadosBanc    = tbcontabancariaDao::buscarDadosContaBancaria($resultadoBuscaPedidoAlteracao[0]->idPRONAC);
        $this->view->resultArquivo      = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
        $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
    }

    /*
    *  View: Solicita��o de Prorrogacao de Prazos - Execu��o
    */
    public function solaltprogprazexecAction()
    {
        if ($_POST) {
            $recebidoPost = Zend_Registry::get('post');

            if ($recebidoPost->solicitacao) {
                if (self::PropostaDiligenciar()) {
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltprogprazexec?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                } else {
                    //parent::message("Erro ao diligenciar a solicita��o", "verificaralteracaotecnico/solaltprogprazexec?idpedidoalteracao=$recebidoPost->IdPronac" ,"ERROR");
                    parent::message("Dilig�ncia enviada com sucesso!", "verificaralteracaotecnico/solaltprogprazexec?idpedidoalteracao=$recebidoPost->IdPronac", "CONFIRM");
                }
            }
        }

        if (isset($_GET['opcao'])) {
            $idPedidoAlteracao = $_GET['id']; //idPedido Altera��o � o idAvaliacaoItemPedidoAlteracao da tabela tbAvaliacaoItemPedidoAlteracao
            $opcao = $_GET['opcao']; //op��o escolhida no select - APROVADO, INDEFERIDO ou EM AN�LISE
            $IdPronac = $_GET['idpedidoalteracao'];

            self::streadequacaoprodutosAction($idPedidoAlteracao, $opcao, $IdPronac, 'solaltprogprazexec');
        }


        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao                      = $recebidoGet->idpedidoalteracao;
        $resultadoDadosAlteracaoexecucao        = PedidoAlteracaoDAO::buscarAlteracaoPrazoExecucao($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao          = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $resultadoDadosAlteracaoexecucao['idPedidoAlteracao']);
        $arquivos                               = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 9, $resultadoDadosAlteracaoexecucao['idPedidoAlteracao']);
        $this->view->resultDadosBanc            = ContaBancariaDAO::buscarDadosContaBancaria($resultadoBuscaPedidoAlteracao['pronac']);
        $porcentagem                            = porcentagemCaptacaoDao::buscarDadosProrrogacaoPrazo($resultadoBuscaPedidoAlteracao['ano'], $resultadoBuscaPedidoAlteracao['seq']);
        $this->view->porcentagem                = ($porcentagem[0]->computed == '')?'0%':$porcentagem[0]->computed.'%';
        $this->view->resultArquivo              = $arquivos;
        $this->view->resultAlteracaoExecucao    = $resultadoDadosAlteracaoexecucao;
        $this->view->resultConsulta             = $resultadoBuscaPedidoAlteracao;
        $this->view->idpedidoalteracao = $idpedidoalteracao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpedidoalteracao;
        $this->view->menumsg = 'true';
        //****************************************************

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaNomeProjEditar", $idpedidoalteracao, 9, null, $resultadoDadosAlteracaoexecucao['idPedidoAlteracao']);
        $dados = $db->fetchAll($sqlproposta);
        if ($dados) {
            $this->view->dados = $dados[0];
            $idPedidoAlt = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //VERIFICA O STATUS DA SOLICITA��O
            $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);

            $this->view->stResult = $db->fetchAll($sqlStatusReadequacao);
        }

        /*$recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao   = $recebidoGet->idpedidoalteracao;
        $resultadoBuscaPedidoAlteracao = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultDadosBanc    = tbcontabancariaDao::buscarDadosContaBancaria($resultadoBuscaPedidoAlteracao[0]->idPRONAC);
        $this->view->resultArquivo  = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
        $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
    }
    
    public function InserirAvalizacao($post, $action)
    {
        $idpedidoalteracao = $post->idpedidoalteracao;
        $dsParecerTecnico  = TratarString::escapeString($post->editor1);

        $parecerTecnico = array(
                "idpedidoalteracao"  => $idpedidoalteracao,
                "dsParecerTecnico"   => $dsParecerTecnico);
        $query = PedidoAlteracaoDAO::BuscaDsEspecificacaoTecnica($parecerTecnico);

        if ($query) {
            $this->_redirect('verificaralteracaotecnico/'.$action);
        }
    }
    public static function VerificarCpfCnpj($dado)
    {
        $retorno = '';
        $qtdcarecteres = strlen($dado);
        switch ($qtdcarecteres) {
            case 11:
                 $retorno = Mascara::addMaskCPF($dado);
                 break;

            case 14:
                $retorno = Mascara::addMaskCNPJ($dado);
                break;
        }
        return  $retorno;
    }

    public static function porcentagemcaptacao($ano, $seq)
    {
        $resultado = porcentagemCaptacaoDao::buscarDadosProrrogacaoPrazo($ano, $seq);
        if ($resultado[0]->computed) {
            return $resultado[0]->computed."%";
        } else {
            return "0%";
        }
    }

    public static function BuscarDadosTabelasAlt($idpedidoalteracao, $tpalteracao)
    {
        switch ($tpalteracao) {
            case 1:
                {
                    $nomProp = tbalteracaonomeproponenteDAO::buscarDadosAltNomProp($idpedidoalteracao);
                    return $nomProp[0];
                }
            case 2:
                {
                    $altRazSoc = tbalteracaoaltrazDAO::buscarDadosAltRaz($idpedidoalteracao);
                    return $altRazSoc[0];
                }
            case 3:
                {
                    $altFicTec = tbalteracaofictecDAO::buscarDadosFicTec($idpedidoalteracao);
                    return $altFicTec[0];
                }
            case 4:
                {
                    $altLolRel = tbalteracaolocalrealizacaoDAO::buscarDadosAltLocRel($idpedidoalteracao);
                    return $altLolRel[0];
                }
            case 5:
                {
                    $altNomProj = tbalteracaonomeprojetoDAO::buscarDadosNmProj($idpedidoalteracao);
                    return $altNomProj[0];
                }
            case 6:
                {
                    $progpraz = tbprorrogacaoprazoDao::buscarDadosProrrogacaoPrazo($idpedidoalteracao);
                    return $progpraz[0];
                }
        }
    }

    private function streadequacaoprodutosAction($idPedidoAlteracao, $opcao, $IdPronac, $action)
    {
        //retorna o id do agente logado
                $auth = Zend_Auth::getInstance(); // pega a autentica��o
                $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idAgente = $agente['idAgente'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        //SQL PARA TRAZER OD DADOS DO REGISTRO EM QUEST�O
        $registro = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlteracao);
        $reg = $db->fetchAll($registro);
        $idPedido = $reg[0]->idAvaliacaoItemPedidoAlteracao;

        if ($opcao == 1) {
            // Chama o SQL
            $sqlstReadequacao = ReadequacaoProjetos::stReadequacaoInicio("readequacaoEA", $idPedidoAlteracao, $idAgente);
            $dados = $db->fetchAll($sqlstReadequacao);

            //SQL PARA ALTERAR O STATUS DO CAMPO stVerificacao da tabela tbPedidoAlteracaoXTipoAlteracao
            $registro2 = ReadequacaoProjetos::readequacaoAltCampo($idPedido);
            $reg2 = $db->fetchAll($registro2);
        } elseif ($opcao == 2) {
            // Chama o SQL
            $sqlstReadequacao = ReadequacaoProjetos::stReadequacaoInicio("readequacaoAP", $idPedidoAlteracao, $idAgente);
            $dados = $db->fetchAll($sqlstReadequacao);
        } elseif ($opcao == 3) {
            // Chama o SQL
            $sqlstReadequacao = ReadequacaoProjetos::stReadequacaoInicio("readequacaoIN", $idPedidoAlteracao, $idAgente);
            $dados = $db->fetchAll($sqlstReadequacao);
        }

        if ($sqlstReadequacao != "") {
            parent::message("Situa��o alterada com sucesso!", "verificaralteracaotecnico/".$action."?idpedidoalteracao=$IdPronac", "CONFIRM");
        } else {
            parent::message("Erro ao alterar o status da solicita��o", "verificaralteracaotecnico/".$action."?idpedidoalteracao=$IdPronac", "ERROR");
        }
    }


    public function finalizapropAction()
    {
        //retorna o id do agente logado
         $auth = Zend_Auth::getInstance(); // pega a autentica��o
         $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idAgenteRemetente = $agente['idAgente'];
        $idPerfilRemetente = $this->codGrupo;

        $especificacao = $_POST['editor1'];
        $IdPRONAC = $_POST['idPronac'];
        $idAcao = $_POST['idAcao'];
        $idAvaliacao = $_POST['idAvaliacao'];
        $idPedidoAlteracao = $_POST['idPedidoAlteracao'];
        $tpAlteracaoProjeto = $_POST['tpAlteracaoProjeto'];
        $IdProposta = $_POST['IdProposta'];
        $idOrgao = $_POST['idOrgao'];
        $parecer = $_POST['status'];
        if ($parecer == 2) {
            $status = 'AP';
        } else {
            $status = 'IN';
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        try {
            $db->beginTransaction();

            /*//UPDATE - CAMPOS: dsEstrategiaExecucao E dsEspecificacaoTecnica NA TABELA SAC.dbo.tbProposta
            $sqlfinalproped = ReadequacaoProjetos::retornaSQLfinalprop($estrategia,$especificacao,$IdProposta);
            $finalproped = $db->fetchAll($sqlfinalproped);*/

            //UPDATE - CAMPO: stVerificacao NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlfinalproped1 = ReadequacaoProjetos::retornaSQLfinalprop1($idPedidoAlteracao, $tpAlteracaoProjeto);
            $db->fetchAll($sqlfinalproped1);

            //UPDATE - CAMPO: dtFimAvaliacao NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlfinalproped2 = ReadequacaoProjetos::retornaSQLfinalprop2($idAvaliacao, $especificacao, $status, $tpAlteracaoProjeto);
            $db->fetchAll($sqlfinalproped2);

            //UPDATE - CAMPO: stAtivo NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfinalproped3 = ReadequacaoProjetos::retornaSQLfinalprop3($idAcao);
            $db->fetchAll($sqlfinalproped3);

            //INSERT NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfinalproped4 = ReadequacaoProjetos::retornaSQLfinalprop4($idAvaliacao, $idOrgao, $idAgenteRemetente, $idPerfilRemetente);
            $db->fetchAll($sqlfinalproped4);

            $db->commit();
            parent::message("Projeto finalizado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao finalizar projeto", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "ERROR");
        }
        /*if ( $sqlfinalproped1 != "" && $sqlfinalproped2 != "" && $sqlfinalproped3 != "" && $sqlfinalproped4 != "" ){
               parent::message("A proposta foi finalizada com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico" ,"CONFIRM");
           }
            else{
               parent::message("Erro ao finalizar proposta.", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico" ,"ERROR");
           }*/
    }

    /**************************************************************************************************************************
      * Fun��o para diligenciar  - EDITAR (perfil t�cnico)
      * ************************************************************************************************************************/
    public function PropostaDiligenciar()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $AgenteLogin = $agente['idAgente'];
        //Zend_Debug::dump($AgenteLogin);exit;
        $IdPronac = $_POST['IdPronac'];
        $solicitacao = $_POST['solicitacao'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlDiligenciarproposta = ReadequacaoProjetos::diligenciarProposta($IdPronac, $solicitacao, $AgenteLogin);
        try {
            $db->fetchAll($sqlDiligenciarproposta);
            return true;
        } catch (Zend_Exception $e) {
            return false;
        }
    }



    /**
     * M�todo para avalia��o dos locais de realiza��o
     * @param void
     * @return void
     */
    public function avaliarlocalrealizacaoAction()
    {
        $tbAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        // recebe os dados do formul�rio
        $post                           = Zend_Registry::get('post');
        $idPronac                       = $post->idPronac;
        $idAbrangencia                  = $post->idAbrangencia;
        $idAvaliacaoItemPedidoAlteracao = $post->idAvaliacaoItemPedidoAlteracao;
        $avaliacao                      = $post->avaliacao;
        $dsAvaliacao                    = $post->dsAvaliacao;


        try {
            // valida os campos
            if (empty($idPronac) || empty($idAbrangencia) || empty($idAvaliacaoItemPedidoAlteracao) || empty($avaliacao) || empty($dsAvaliacao)) {
                throw new Exception("As informa��es abaixo s�o obrigat�rias:
					<br />- Pronac
					<br />- Abrang�ncia
					<br />- C�digo da Avaliacao do Item de Pedido de Alteracao
					<br />- A avalia��o (Deferido / Indeferido)
					<br />- A justificativa da avalia��o");
            }
            // envia pro banco
            else {
                // monta o array com os dados
                $dados = array(
                    'idAvaliacaoItemPedidoAlteracao'     => $idAvaliacaoItemPedidoAlteracao
                    //,'idAvaliacaoSubItemPedidoAlteracao' => $idAbrangencia
                    ,'stAvaliacaoSubItemPedidoAlteracao' => $avaliacao
                    ,'dsAvaliacaoSubItemPedidoAlteracao' => $dsAvaliacao);

                // cadastra a avalia��o
                $dao = $tbAbrangencia->avaliarLocalRealizacao($dados);

                // pega o �ltimo idAvaliacaoSubItemPedidoAlteracao inserido
                $ultimoId = AvaliacaoSubItemPedidoAlteracaoDAO::buscarUltimo();
                $ultimoId = $ultimoId[0]->id;

                // vincula a abrangencia
                $dados_abrangencia = array(
                                    'idAvaliacaoItemPedidoAlteracao'     => $idAvaliacaoItemPedidoAlteracao
                                    ,'idAvaliacaoSubItemPedidoAlteracao' => $ultimoId
                                    ,'idAbrangencia'                     => $idAbrangencia);
                $cadastrar_abrangencia = AvaliacaoSubItemAbrangenciaDAO::cadastrar($dados_abrangencia);

                // caso seja cadastrado
                if ($dao && $dados_abrangencia) {
                    parent::message("Avalia��o efetuada com sucesso!", "verificaralteracaotecnico/solaltlocrel?idpedidoalteracao=" . $idPronac, "CONFIRM");
                } else {
                    throw new Exception("Erro ao tentar efetuar avalia��o!");
                }
            } // fecha else
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "verificaralteracaotecnico/solaltlocrel?idpedidoalteracao=" . $idPronac, "ERROR");
        }
    } // fecha m�todo avaliarlocalrealizacaoAction()
    
    
    public function planilhasolicitadaAction()
    {
        if (isset($_GET['v']) && $_GET['v'] == 'fim') :
            $this->_helper->viewRenderer->setNoRender(true);
        endif;
        
        $idPronac = isset($_POST['idpronac']) ? $_POST['idpronac'] : '';
        $auth = Zend_Auth::getInstance();

        if (empty($_POST)) {
            $resultadoItem = VerificarSolicitacaodeReadequacoesDAO::verificaPlanilhaAprovacao($idPronac);

            if (empty($resultadoItem)) {
                $inserirCopiaPlanilha = VerificarSolicitacaodeReadequacoesDAO::inserirCopiaPlanilha($idPronac);
            }
        }

        $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO;
        if (isset($_POST['finaliza'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $idpronac = $_POST['idpronac'];
            $dsObservacao = $_POST['dsObservacao'];
            try {
                $verificaIdPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::verificaPedidoAlteracao($idpronac);
                $idpedidoalteracao = $verificaIdPedidoAlteracao[0]->idPedidoAlteracao;
                $where = " idPedidoAlteracao = $idpedidoalteracao";
                $dadosPedido = array('siVerificacao' => 1);
                $atualizapedido = $buscaInformacoes->atualizarPedido($dadosPedido, $where);
                $dadosTipo = array('stVerificacao' => 2);
                $atualizapedidotipo = $buscaInformacoes->atualizarTipoAlteracao($dadosTipo, array('idPedidoAlteracao = ?' => $idpedidoalteracao, 'tpAlteracaoProjeto = ?' => 4));

                $idAvaliacaoItemPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::buscaIdAvaliacaoItemPedidoAlteracao($idpedidoalteracao);
                $idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao['0']->idAvaliacaoItemPedidoAlteracao;

                $dadosAvaliacao = array('stAvaliacaoItemPedidoAlteracao' => 'AP', 'dtFimAvaliacao' => date('Y-m-d H:i:s'));
                $avaliacao = $buscaInformacoes->atualizarAvaliacaopedido($dadosAvaliacao, $where);
                $where = " idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao and dtEncaminhamento in (select max(dtEncaminhamento) from BDCORPORATIVO.scSac.tbAcaoAvaliacaoItemPedidoAlteracao where idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao )";
                $dadosAcao = array('stAtivo' => '1', 'dtEncaminhamento' => date('Y-m-d H:i:s'));
                $atualizapedidotipo = $buscaInformacoes->atualizarAvaliacaoAcao($dadosAcao, $where);

                $verificaridorgao = $buscaInformacoes->buscarOrgao($idAvaliacaoItemPedidoAlteracao);
                $orgao = $verificaridorgao['idorgao'];

                //retorna o id do agente logado
                $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
                $idAgenteRemetente = $agente['idAgente'];
                $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
                $idPerfilRemetente = $GrupoAtivo->codGrupo;

                $dadosinserir = array(
                    'idAvaliacaoItemPedidoAlteracao' => $idAvaliacaoItemPedidoAlteracao,
                    'idAgenteAcionado' => 0,
                    'dsObservacao' => $dsObservacao,
                    'idTipoAgente' => 2,
                    'idOrgao' => $orgao,
                    'stAtivo' => 0,
                    'stVerificacao' => 2,
                    'dtEncaminhamento' => date('Y-m-d H:i:s'),
                    'idAgenteRemetente' => $idAgenteRemetente,
                    'idPerfilRemetente' => $idPerfilRemetente,
                );
                $inserir = $buscaInformacoes->insertAvaliacaoAcao($dadosinserir);
                $where = " and  stAvaliacaoSubItemPedidoAlteracao  = 'AP'";
                $condicao = VerificarSolicitacaodeReadequacoesDAO::verificaSubItem($idAvaliacaoItemPedidoAlteracao, $where);
                if (count($condicao) > 0) {
                    $dados = array('stAvaliacaoItemPedidoAlteracao' => 'AP');
                    $where = " idpedidoalteracao = $idpedidoalteracao";
                    $alterarStatus = $buscaInformacoes->atualizarStatus($dados, $where);
                } else {
                    $dados = array('stAvaliacaoItemPedidoAlteracao' => 'IN');
                    $where = " idpedidoalteracao = $idpedidoalteracao";
                    $alterarStatus = $buscaInformacoes->atualizarStatus($dados, $where);
                }
                $this->_helper->json(array('Ok' => true));
                $this->_helper->viewRenderer->setNoRender(true);
            } catch (Exception $e) {
                $this->_helper->json(array('error' => false, 'Descricao' => $e->getMessage()));
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }
        
        $resultadoOrcamento = $buscaInformacoes->verificaMudancaOrcamentaria($idPronac);
        $this->view->buscaorcamento = $resultadoOrcamento;

        //$idSolicitante = $auth->getIdentity()->usu_codigo;
        $buscaprojeto = new ReadequacaoProjetos();
        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;


        $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO();
        $SolicitarReadequacaoCustoDAO = new SolicitarReadequacaoCustoDAO();
        $resultadoEtapa = $buscaInformacoes->buscarEtapa();
        $this->view->buscaetapa = $resultadoEtapa;

        $resultadoProduto = $SolicitarReadequacaoCustoDAO->buscarProdutos($idPronac)->toArray();
        
        if (empty($resultadoProduto)) {
            $resultadoProduto = $SolicitarReadequacaoCustoDAO->buscarProdutosAprovados($idPronac);
        } else {
            $resultadoProduto = $SolicitarReadequacaoCustoDAO->buscarProdutos($idPronac);
        }


        $this->view->buscaproduto = $resultadoProduto;

        //var_dump($resultadoProduto);die;

        foreach ($resultadoProduto as $idProduto) {
            foreach ($resultadoEtapa as $idEtapa) {
                $resultadoProdutosItens = $buscaInformacoes->buscarProdutosItens($idPronac, $idEtapa->idPlanilhaEtapa, null, "N", $idProduto->idProduto);
                $valorProduto[$idProduto->idProduto][$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItens;
                $resultadoProdutosItensAdm = $buscaInformacoes->buscarProdutosItensSemProduto($idPronac, $idEtapa->idPlanilhaEtapa, null, "N");
                $valorProdutoAdm[$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItensAdm;
            }
        }
        $this->view->buscaprodutositens = $valorProduto;
        $this->view->buscaprodutositensadm = $valorProdutoAdm;




        $verificaIdPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::verificaPedidoAlteracao($idPronac);
        $idPedidoAlteracao = $verificaIdPedidoAlteracao[0]->idPedidoAlteracao;

        $verificaStatus = VerificarSolicitacaodeReadequacoesDAO::verificaStatus($idPedidoAlteracao);
        $idAvaliacaoItemPedidoAlteracao = $verificaStatus[0]->stAvaliacaoItemPedidoAlteracao;

        if ($idAvaliacaoItemPedidoAlteracao == "EA") {
            $this->view->status = "EA";
        }
        if ($idAvaliacaoItemPedidoAlteracao == "AP") {
            $this->view->status = "AP";
        }
        if ($idAvaliacaoItemPedidoAlteracao == "IN") {
            $this->view->status = "IN";
        }

        $verificaIdPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::verificaPedidoAlteracao($idPronac);
        $idpedidoalteracao = $verificaIdPedidoAlteracao[0]->idPedidoAlteracao;
        $buscaIdAvaliacaoItemPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::buscaIdAvaliacaoItemPedidoAlteracao($idPedidoAlteracao);
        foreach ($buscaIdAvaliacaoItemPedidoAlteracao as $itemAvaliacaoItemPedido) {
            $idItemAvaliacaoItemPedidoAlteracao = $itemAvaliacaoItemPedido->idAvaliacaoItemPedidoAlteracao;
        }

        $verificaSubItemPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::verificaStatusFinal($idPedidoAlteracao);
        $stAvaliacaoSubItemPedidoAlteracao = $verificaSubItemPedidoAlteracao[0]->stAvaliacao;

        if ($stAvaliacaoSubItemPedidoAlteracao == "AG") {
            $this->view->statusAnalise = "Aguardando An�lise";
        }
        if ($stAvaliacaoSubItemPedidoAlteracao == "EA") {
            $this->view->statusAnalise = "Em An�lise";
        }
        if ($stAvaliacaoSubItemPedidoAlteracao == "AP") {
            $this->view->statusAnalise = "Aprovado";
        }
        if ($stAvaliacaoSubItemPedidoAlteracao == "IN") {
            $this->view->statusAnalise = "Indeferido";
        }

        $resultadoAvaliacaoAnalise = $buscaInformacoes->verificaAvaliacaoAnalise();
        $this->view->AvaliacaoAnalise = $resultadoAvaliacaoAnalise;
    }
} // fecha class
