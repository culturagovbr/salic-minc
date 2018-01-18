<?php
class VerificarAlteracaoCoordenadorController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        /* $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
         $PermissoesGrupo[] = 94;  // Parecerista
         $PermissoesGrupo[] = 121; // T�cnico*/
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);

        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $this->view->agente = $agente['idAgente'];

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    public function indexAction()
    {
        $resultadobusca = tbPedidoAlteracaoProjetoCoordDAO::buscarDadosPedidoAlteracao();
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
            $recebidoPost  = Zend_Registry::get('post');
            $dados['Solicitacao'] = $recebidoPost->editor1;
            $dados['idPronac'] = $recebidoPost->idPronac;

            // manda os dados para a vis�o
            /*$this->view->usuario = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o*/
            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
            $idagente = $agente['idAgente'];

            $dados['idSolicitante'] =  $idagente;

            if (PedidoAlteracaoDAO::salvarComentarioAlteracaoProj($dados)) {
                parent::message("Os dados foram salvos com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
            } else {
                parent::message("Erro na opera��o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
            }
            /*if($recebidoPost->stAprovacao == 'RT')
            {
                $this->RetornoTecnico($_POST);
            }
            else
            {
                if($recebidoPost->stAprovacao == 'D')
                {
//                    Zend_Debug::dump($_POST);exit;
                    $recDadosParaAlteracao = tbalteracaonomeprojetoDAO::buscarDadosNmProj($_POST['idpedidoalteracao']);
                    $dadosalterar = array("nomeProjeto"=>$recDadosParaAlteracao[0]->nmprojeto);
                    tbalteracaonomeprojetoDAO::alterarNomeProjeto($dadosalterar, $recDadosParaAlteracao[0]->idPRONAC);
                }
                $this->InserirStatusAvaliacaoProjeto($_POST);
            }*/
        }

        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao = $recebidoGet->idpedidoalteracao;
        $resultadoDadosAlteracaoNomeProjeto = PedidoAlteracaoDAO::buscarAlteracaoNomeProjeto($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $resultadoDadosAlteracaoNomeProjeto['idPedidoAlteracao']);
        $arquivos = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 5, $resultadoDadosAlteracaoNomeProjeto['idPedidoAlteracao']);
        $this->view->resultArquivo = $arquivos;
        $this->view->resultAlteracaoNomeProjeto = $resultadoDadosAlteracaoNomeProjeto;
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultParecerTecnico   = VerificarAlteracaoProjetoDAO::buscarDadosParecerTecnico($idpedidoalteracao, 5, $resultadoDadosAlteracaoNomeProjeto['idPedidoAlteracao']);

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
        } else {
            $dados['stAvaliacaoItemPedidoAlteracao'] = null;
            $this->view->dados = (object) $dados;
        }


        /*$recebidoGet  = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $resultadoBuscaPedidoAlteracao = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultConsulta         = $resultadoBuscaPedidoAlteracao;
        $this->view->resultArquivo          = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
        $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
    }

    /*
    *  View: Solicita��o de Altera��o Raz�o Social
    */
    public function solaltrazsocAction()
    {
        if ($_POST) {
            /*$recebidoPost  = Zend_Registry::get('post');
            if($recebidoPost->stAprovacao == 'RT')
            {
                $this->RetornoTecnico($_POST);
            }
            else
            {
                if($recebidoPost->stAprovacao == 'D')
                {
                    $recDadosParaAlteracao = tbalteracaoaltrazDAO::buscarDadosAltRaz($_POST['idpedidoalteracao']);
                    $dadosalterar = array("descricao"=>$recDadosParaAlteracao[0]->nmrazaosocial);
                    tbalteracaoaltrazDAO::alterarRazaoSocialProjeto($dadosalterar, $recDadosParaAlteracao[0]->idAgente);
                }
                $this->InserirStatusAvaliacaoProjeto($_POST);
            }*/

            $recebidoPost  = Zend_Registry::get('post');
            $dados['Solicitacao'] = $recebidoPost->editor1;
            $dados['idPronac'] = $recebidoPost->idPronac;

            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
            $idagente = $agente['idAgente'];

            $dados['idSolicitante'] =  $idagente;

            if (PedidoAlteracaoDAO::salvarComentarioAlteracaoProj($dados)) {
                parent::message("Os dados foram salvos com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
            } else {
                parent::message("Erro na opera��o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
            }
        }

        $recebidoGet = Zend_Registry::get('get');
        $idPronac = $recebidoGet->id;
        $idpedidoalteracao = $recebidoGet->idpedidoalteracao;

        $resultadoBuscaPedidoAlteracao = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idPronac, $idpedidoalteracao);
        $resultadoDadosAlteracaoRazaoSocial = PedidoAlteracaoDAO::buscarAlteracaoRazaoSocial($idPronac);
        $arquivos = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idPronac, 2, $idpedidoalteracao);
        $this->view->resultArquivo = $arquivos;
        $this->view->resultAlteracaoRazaoSocial = $resultadoDadosAlteracaoRazaoSocial;
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultProjeto  = AlteracaoNomeProponenteDAO::buscarProjPorProp($resultadoBuscaPedidoAlteracao['CgcCpf']);
        $this->view->resultParecerTecnico   = VerificarAlteracaoProjetoDAO::buscarDadosParecerTecnico($idPronac, 2, $idpedidoalteracao);

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpedidoalteracao;
        $this->view->menumsg = 'true';
        //****************************************************

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaNomeProjEditar", $idpedidoalteracao, 2);
        $dados = $db->fetchAll($sqlproposta);
        if ($dados) {
            $this->view->dados = $dados[0];
            $idPedidoAlt = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //VERIFICA O STATUS DA SOLICITA��O
            $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);

            $this->view->stResult = $db->fetchAll($sqlStatusReadequacao);
        } else {
            $dados['stAvaliacaoItemPedidoAlteracao'] = null;
            $this->view->dados = (object) $dados;
        }


        /*$resultadoBuscaPedidoAlteracao = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultArquivo  = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
        $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
    }

    /*
    *  View: Solicita��o de Altera��o do Nome do Proponente
    */
    public function solaltnomprpAction()
    {
        if ($_POST) {
            $recebidoPost  = Zend_Registry::get('post');
            $dados['Solicitacao'] = $recebidoPost->editor1;
            $dados['idPronac'] = $recebidoPost->idPronac;

            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
            $idagente = $agente['idAgente'];

            $dados['idSolicitante'] =  $idagente;

            if (PedidoAlteracaoDAO::salvarComentarioAlteracaoProj($dados)) {
                parent::message("Os dados foram salvos com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
            } else {
                parent::message("Erro na opera��o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
            }
            
            
            /*$recebidoPost  = Zend_Registry::get('post');
            if($recebidoPost->stAprovacao == 'RT')
            {
                $this->RetornoTecnico($_POST);
            }
            else
            {
                if($recebidoPost->stAprovacao == 'D')
                {
                    $recDadosParaAlteracao = tbalteracaonomeproponenteDAO::buscarDadosAltNomProp($_POST['idpedidoalteracao']);
                    $dadosalterar = array("cgccpf"=>$recDadosParaAlteracao[0]->nrCNPJCPF);
                    tbalteracaonomeproponenteDAO::alterarNomeProponente($dadosalterar, $recDadosParaAlteracao[0]->idPRONAC);
                }
                $this->InserirStatusAvaliacaoProjeto($_POST);
            }*/
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
        $this->view->resultParecerTecnico   = VerificarAlteracaoProjetoDAO::buscarDadosParecerTecnico($idpedidoalteracao, 1, $resultadoDadosAlteracaoNomeProponente['idPedidoAlteracao']);

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
        } else {
            $dados['stAvaliacaoItemPedidoAlteracao'] = null;
            $this->view->dados = (object) $dados;
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
            /*$recebidoPost  = Zend_Registry::get('post');
            if($recebidoPost->stAprovacao == 'RT')
            {
                $this->RetornoTecnico($_POST);
            }
            else
            {
                if($recebidoPost->stAprovacao == 'D')
                {
                    $recDadosParaAlteracaoAltLocalRel = tbalteracaolocalrealizacaoDAO::buscarDadosAltLocRel($_POST['idpedidoalteracao']);
                    foreach($recDadosParaAlteracaoAltLocalRel as $dados)
                    {

                    }
                }
                else
                {
                    $this->InserirStatusAvaliacaoProjeto($_POST);
                }
            }*/

            $recebidoPost  = Zend_Registry::get('post');
            $dados['Solicitacao'] = $recebidoPost->editor1;
            $dados['idPronac'] = $recebidoPost->idPronac;

            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
            $idagente = $agente['idAgente'];

            $dados['idSolicitante'] =  $idagente;

            if (PedidoAlteracaoDAO::salvarComentarioAlteracaoProj($dados)) {
                parent::message("Os dados foram salvos com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
            } else {
                parent::message("Erro na opera��o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
            }
        }


        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $buscaAb = $tbAbrangencia->buscarDadosAbrangenciaSolicitadaLocal($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $buscaAb[0]->idPedidoAlteracao);
        //$resultadoDadosAlteracaoLocalRealizacao = AbrangenciaDAO::buscarDadosAbrangenciaAlteracao($idpedidoalteracao);
        if (AvaliacaoSubItemPedidoAlteracaoDAO::buscar($resultadoBuscaPedidoAlteracao['idAvaliacao'])) {
            $resultadoDadosAlteracaoLocalRealizacao = $tbAbrangencia->buscarDadosAbrangenciaAlteracaoCoord($idpedidoalteracao, 'COM_AVALIACAO');
        } else {
            $resultadoDadosAlteracaoLocalRealizacao = $tbAbrangencia->buscarDadosAbrangenciaAlteracaoCoord($idpedidoalteracao, 'SEM_AVALIACAO');
        }

        $arquivos = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 4, $buscaAb[0]->idPedidoAlteracao);
        $this->view->resultLocalRel     = $tbAbrangencia->buscarDadosAbrangenciaSolicitadaLocal($idpedidoalteracao, 'N');
        $this->view->resultArquivo = $arquivos;
        $this->view->resultAbrangencia = $resultadoDadosAlteracaoLocalRealizacao;
        $this->view->resultConsulta = $resultadoBuscaPedidoAlteracao;
        $this->view->resultParecerTecnico   = VerificarAlteracaoProjetoDAO::buscarDadosParecerTecnico($idpedidoalteracao, 4, $buscaAb[0]->idPedidoAlteracao);

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpedidoalteracao;
        $this->view->menumsg = 'true';
        //****************************************************

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaNomeProjEditar", $idpedidoalteracao, 4, null, $buscaAb[0]->idPedidoAlteracao);
        $dados = $db->fetchAll($sqlproposta);
        //  Zend_Debug::dump($dados);exit;
        if ($dados) {
            $this->view->dados = $dados[0];
            $idPedidoAlt = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //VERIFICA O STATUS DA SOLICITA��O
            $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);

            $this->view->stResult = $db->fetchAll($sqlStatusReadequacao);
        } else {
            $dados['stAvaliacaoItemPedidoAlteracao'] = null;
            $this->view->dados = (object) $dados;
        }

        /*$recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao    = $recebidoGet->idpedidoalteracao;
        $resultadoBuscaPedidoAlteracao  = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultAbrangencia  = tbAbrangenciaDAO::buscarDadosAbrangencia($resultadoBuscaPedidoAlteracao[0]->idprojeto);
        $this->view->resultConsulta     = $resultadoBuscaPedidoAlteracao;
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
            /*$recebidoPost  = Zend_Registry::get('post');
            if($recebidoPost->stAprovacao == 'RT')
            {
                $this->RetornoTecnico($_POST);
            }
            else
            {
                $this->InserirStatusAvaliacaoProjeto($_POST);
            }*/

            $recebidoPost  = Zend_Registry::get('post');
            $dados['Solicitacao'] = $recebidoPost->editor1;
            $dados['idPronac'] = $recebidoPost->idPronac;

            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
            $idagente = $agente['idAgente'];

            $dados['idSolicitante'] =  $idagente;

            if (PedidoAlteracaoDAO::salvarComentarioAlteracaoProj($dados)) {
                parent::message("Os dados foram salvos com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
            } else {
                parent::message("Erro na opera��o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
            }
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
        $this->view->resultParecerTecnico        = VerificarAlteracaoProjetoDAO::buscarDadosParecerTecnico($idpedidoalteracao, 3, $resultadoDadosAlteracaoFichaTecnica['idPedidoAlteracao']);

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
        } else {
            $dados['stAvaliacaoItemPedidoAlteracao'] = null;
            $this->view->dados = (object) $dados;
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
            /*$recebidoPost  = Zend_Registry::get('post');
            if($recebidoPost->stAprovacao == 'RT')
            {
                $this->RetornoTecnico($_POST);
            }
            else
            {
                if($recebidoPost->stAprovacao == 'D')
                {
                    $recDadosParaAlteracao = tbprorrogacaoprazoDao::buscarDadosProrrogacaoPrazo($_POST['idpedidoalteracao']);
                    $datainicioprazo = Data::tratarDataZend($recDadosParaAlteracao[0]->dtinicioprazo, 'americano');
                    $datafimprazo    = Data::tratarDataZend($recDadosParaAlteracao[0]->dtfimprazo, 'americano');
                    $dadosalterar = array("dtiniciocaptacao"=>$datainicioprazo, "dtfimcaptacao"=>$datafimprazo);
                    $result = tbprorrogacaoprazoDao::alterarProrrogracaoPrazoCap($dadosalterar, $recDadosParaAlteracao[0]->idPRONAC);
                    if($result)
                    {
                        $this->InserirStatusAvaliacaoProjeto($_POST);
                    };
                }
                else
                {
                    $this->InserirStatusAvaliacaoProjeto($_POST);
                }
            }*/

            $recebidoPost  = Zend_Registry::get('post');
            $dados['Solicitacao'] = $recebidoPost->editor1;
            $dados['idPronac'] = $recebidoPost->idPronac;

            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
            $idagente = $agente['idAgente'];

            $dados['idSolicitante'] =  $idagente;

            if (PedidoAlteracaoDAO::salvarComentarioAlteracaoProj($dados)) {
                parent::message("Os dados foram salvos com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
            } else {
                parent::message("Erro na opera��o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
            }
        }

        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao                        = $recebidoGet->idpedidoalteracao;
        $resultadoDadosAlteracaoPrazoCaptacao     = PedidoAlteracaoDAO::buscarAlteracaoPrazoCaptacao($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao            = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $resultadoDadosAlteracaoPrazoCaptacao['idPedidoAlteracao']);
        $arquivos                                 = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 8, $resultadoDadosAlteracaoPrazoCaptacao['idPedidoAlteracao']);
        $porcentagem                              = porcentagemCaptacaoDao::buscarDadosProrrogacaoPrazo($resultadoBuscaPedidoAlteracao['ano'], $resultadoBuscaPedidoAlteracao['seq']);

        $contaBancaria = new ContaBancaria();
        $this->view->resultDadosBanc              = $contaBancaria->buscarDadosBancarios($resultadoBuscaPedidoAlteracao['pronac']);
        $this->view->porcentagem                  = ($porcentagem[0]->computed == '')?'0%':$porcentagem[0]->computed.'%';
        $this->view->resultArquivo                = $arquivos;
        $this->view->resultAlteracaoPrazoCaptacao = $resultadoDadosAlteracaoPrazoCaptacao;
        $this->view->resultConsulta               = $resultadoBuscaPedidoAlteracao;
        $this->view->resultParecerTecnico         = VerificarAlteracaoProjetoDAO::buscarDadosParecerTecnico($idpedidoalteracao, 8, $resultadoDadosAlteracaoPrazoCaptacao['idPedidoAlteracao']);

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
        } else {
            $dados['stAvaliacaoItemPedidoAlteracao'] = null;
            $this->view->dados = (object) $dados;
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
            /*$recebidoPost  = Zend_Registry::get('post');
            if($recebidoPost->stAprovacao == 'RT')
            {
                $this->RetornoTecnico($_POST);
            }
            else
            {
                if($recebidoPost->stAprovacao == 'D')
                {
                    $recDadosParaAlteracao = tbprorrogacaoprazoDao::buscarDadosProrrogacaoPrazo($_POST['idpedidoalteracao']);
                    $datainicioprazo = Data::tratarDataZend($recDadosParaAlteracao[0]->dtinicioprazo, 'americano');
                    $datafimprazo    = Data::tratarDataZend($recDadosParaAlteracao[0]->dtfimprazo, 'americano');
                    $dadosalterar = array("dtinicioexecucao"=>$datainicioprazo, "dtfimexecucao"=>$datafimprazo);
                    tbprorrogacaoprazoDao::alterarProrrogracaoPrazoExec($dadosalterar, $recDadosParaAlteracao[0]->idPRONAC);
                    if($result)
                    {
                        $this->InserirStatusAvaliacaoProjeto($_POST);
                    };
                }
                else
                {
                    $this->InserirStatusAvaliacaoProjeto($_POST);
                }
            }*/

            $recebidoPost           = Zend_Registry::get('post');
            $dados['Solicitacao']   = $recebidoPost->editor1;
            $dados['idPronac']      = $recebidoPost->idPronac;

            $auth                   = Zend_Auth::getInstance(); // pega a autentica��o
            $agente                 = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
            $idagente               = $agente['idAgente'];

            $dados['idSolicitante'] =  $idagente;

            if (PedidoAlteracaoDAO::salvarComentarioAlteracaoProj($dados)) {
                parent::message("Os dados foram salvos com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
            } else {
                parent::message("Erro na opera��o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
            }
        }

        $recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao                      = $recebidoGet->idpedidoalteracao;
        $resultadoDadosAlteracaoexecucao        = PedidoAlteracaoDAO::buscarAlteracaoPrazoExecucao($idpedidoalteracao);
        $resultadoBuscaPedidoAlteracao          = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idpedidoalteracao, $resultadoDadosAlteracaoexecucao['idPedidoAlteracao']);
        $arquivos                               = VerificarAlteracaoProjetoDAO::buscarArquivosSolicitacao($idpedidoalteracao, 9, $resultadoDadosAlteracaoexecucao['idPedidoAlteracao']);

        $contaBancaria = new ContaBancaria();
        $this->view->resultDadosBanc            = $contaBancaria->buscarDadosBancarios($resultadoBuscaPedidoAlteracao['pronac']);
//        $this->view->resultDadosBanc            = ContaBancariaDAO::buscarDadosContaBancaria($resultadoBuscaPedidoAlteracao['pronac']);
        $porcentagem                            = porcentagemCaptacaoDao::buscarDadosProrrogacaoPrazo($resultadoBuscaPedidoAlteracao['ano'], $resultadoBuscaPedidoAlteracao['seq']);
        $this->view->porcentagem                = ($porcentagem[0]->computed == '')?'0%':$porcentagem[0]->computed.'%';
        $this->view->resultArquivo              = $arquivos;
        $this->view->resultAlteracaoExecucao    = $resultadoDadosAlteracaoexecucao;
        $this->view->resultConsulta             = $resultadoBuscaPedidoAlteracao;
        $this->view->resultParecerTecnico       = VerificarAlteracaoProjetoDAO::buscarDadosParecerTecnico($idpedidoalteracao, 9, $resultadoDadosAlteracaoexecucao['idPedidoAlteracao']);

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
        } else {
            $dados['stAvaliacaoItemPedidoAlteracao'] = null;
            $this->view->dados = (object) $dados;
        }
       

        /*$recebidoGet = Zend_Registry::get('get');
        $idpedidoalteracao   = $recebidoGet->idpedidoalteracao;
        $resultadoBuscaPedidoAlteracao      = tbPedidoAlteracaoProjetoDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        $this->view->resultConsulta         = $resultadoBuscaPedidoAlteracao;
        $this->view->resultArquivo          = tbpedidoaltprojetoxarquivoDAO::buscarArquivos($idpedidoalteracao);
        $this->view->resultDadosBanc        = tbcontabancariaDao::buscarDadosContaBancaria($resultadoBuscaPedidoAlteracao[0]->idPRONAC);
        $this->view->resultParecerTecnico   = tbalteracaonomeprojetoDAO::buscarDadosParecerTecnico($idpedidoalteracao);*/
    }

    public function InserirStatusAvaliacaoProjeto($post)
    {
        $idpedidoalteracao          = $post['idpedidoalteracao'];
        $dsJustificativaAvaliacao   = $post['dsJustificativaAvaliacao'];
        $stDeferimentoAvaliacao     = $post['stAprovacao'];

        $parecerCoordenador = array(
                "dtAvaliacao"                    => date('Y-m-d H:i:s'),
                "idAvaliador"                    => 3998,
                "dsJustificativaAvaliacao"       => $dsJustificativaAvaliacao,
                "stDeferimentoAvaliacao"         => $stDeferimentoAvaliacao);

        $query = tbPedidoAlteracaoProjetoCoordDAO::updateDadosProjeto($parecerCoordenador, $idpedidoalteracao);
        if ($query) {
            $this->_redirect('verificaralteracaocoordenador/');
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function RetornoTecnico($post)
    {
        $idpedidoalteracao          = $post['idpedidoalteracao'];
        $dtparecertecnico           = $post['dtparecertecnico'];
        $dsJustificativaAvaliacao   = TratarString::escapeString($post['dsJustificativaAvaliacao']);
        $dtparecertecnico           = date('Y-m-d H:i:00', strtotime($post['dtparecertecnico']));
        $parecerCoordenador = array(
                "dtretornocoordenador"           => date('Y-m-d H:i:s'),
                "idcoordenador"                  => 3998,
                "dsretornocoordenador"           => $dsJustificativaAvaliacao);

        $query = tbPedidoAlteracaoProjetoCoordDAO::UpdateAvaliacaoProjeto($parecerCoordenador, $idpedidoalteracao, $dtparecertecnico);

        if ($query) {
            $this->_redirect('verificaralteracaocoordenador/');
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public static function VerificarCpfCnpj($dado)
    {
        $qtdcarecteres = strlen($dado);
        switch ($qtdcarecteres) {
            case 11:
                {
                    $retorno = Mascara::addMaskCPF($dado);
                }
            case 14:
                {
                    $retorno = Mascara::addMaskCNPJ($dado);
                }
        }
        return  $retorno;
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
        }
    }
}
