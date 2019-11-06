<?php

class VerificarReadequacaoDeProjetoController extends MinC_Controller_Action_Abstract
{
    private $getIdUsuario = 0;
    private $getIdOrgao = 0;
    private $_idPedidoAlteracao = 0;

    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento
        $PermissoesGrupo[] = 128; // Tecnico de Portaria
        parent::perfil(1, $PermissoesGrupo);

        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $this->view->agente = $agente['idAgente'];
        $this->getIdUsuario = $agente['idAgente'];
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->getIdOrgao = $GrupoAtivo->codOrgao;
        $this->codGrupo = $GrupoAtivo->codGrupo;
        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha m�todo init()


    /**
     * M�doto para pegar o idPedidoAlteracao
     * @access public
     * @param $idPronac int
     * @return $idPedidoAlteracao int
     */
    public function pegarIdPedidoAlteracao($idPronac)
    {
        $tbPedidoAlteracaoProjeto = new tbPedidoAlteracaoProjeto();

        // busca os id do �ltimo pedido de readequa��o n�o finalizado
        $wherePedido                    = array('IdPRONAC = ?' => $idPronac, 'siVerificacao IN (?)' => array(0, 1), 'stPedidoAlteracao = ?' => 'I');
        $orderPedido                    = array('idPedidoAlteracao DESC');
        $buscarPedidoAlteracao          = $tbPedidoAlteracaoProjeto->buscar($wherePedido, $orderPedido)->current();
        $this->_idPedidoAlteracao       = count($buscarPedidoAlteracao) > 0 ? $buscarPedidoAlteracao['idPedidoAlteracao'] : 0;
        return $this->_idPedidoAlteracao;
    } // fecha fun��o pegarIdPedidoAlteracao()


    /**************************************************************************************************************************
     * FUN��O PARA GERAR OS HIST�RICOS
     * ************************************************************************************************************************/
    public function historicoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $idavaliacao = $_POST['idavaliacao'];
        $ListaRegistros =  ReadequacaoProjetos::retornaSQLHistoricoLista($idavaliacao);
        $this->view->ListaRegistros = $db->fetchAll($ListaRegistros);
    }

    /**************************************************************************************************************************
     * Fun��o que chama a view verificarreadequacaodeprojeto - Tela Coordenador de Acompanhemento
     * ************************************************************************************************************************/

    public function verificarreadequacaodeprojetocoordacompanhamentoAction()
    {
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
                $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento
                parent::perfil(1, $PermissoesGrupo);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        if (isset($_POST['verifica']) and $_POST['verifica'] == 'a') {
            $idorgao = $_POST['idorgao'];
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $AgentesOrgao =  ReadequacaoProjetos::dadosAgentesOrgaoA($idorgao);
            $AgentesOrgao = $db->fetchAll($AgentesOrgao);
            $a = 0;
            if (is_array($AgentesOrgao) and count($AgentesOrgao)>0) {
                foreach ($AgentesOrgao as $agentes) {
                    $dadosAgente[$a]['usu_codigo'] = $agentes->usu_codigo;
                    $dadosAgente[$a]['usu_nome'] = utf8_encode($agentes->usu_nome);
                    $dadosAgente[$a]['Perfil'] = utf8_encode($agentes->Perfil);
                    $dadosAgente[$a]['idperfil'] = $agentes->idVerificacao;
                    $dadosAgente[$a]['idAgente'] = utf8_encode($agentes->idAgente);
                    $a++;
                }
                $jsonEncode = json_encode($dadosAgente);

                //echo $jsonEncode;
                $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosAgente));
            } else {
                $this->_helper->json(array('resposta'=>false));
            }
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['verifica']) and $_POST['verifica'] == 'b') {
            $idorgao = $_POST['idorgao'];
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $AgentesOrgao =  ReadequacaoProjetos::dadosAgentesOrgaoB($idorgao);
            $AgentesOrgao = $db->fetchAll($AgentesOrgao);
            $a = 0;
            if (is_array($AgentesOrgao) and count($AgentesOrgao)>0) {
                foreach ($AgentesOrgao as $agentes) {
                    $dadosAgente[$a]['usu_codigo'] = $agentes->usu_codigo;
                    $dadosAgente[$a]['usu_nome'] = utf8_encode($agentes->usu_nome);
                    $dadosAgente[$a]['Perfil'] = utf8_encode($agentes->Perfil);
                    $dadosAgente[$a]['idperfil'] = $agentes->idVerificacao;
                    $dadosAgente[$a]['idAgente'] = utf8_encode($agentes->idAgente);
                    $a++;
                }
                $jsonEncode = json_encode($dadosAgente);

                //echo $jsonEncode;
                $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosAgente));
            } else {
                $this->_helper->json(array('resposta'=>false));
            }
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['verifica2']) and $_POST['verifica2'] == 'x') {
            $idagente = $_POST['idagente'];
            if ($idagente != '') {
                $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
                $AgentesPerfil =  ReadequacaoProjetos::dadosAgentesPerfil($idagente);
                $AgentesPerfil = $db->fetchAll($AgentesPerfil);
                $idperfil = $AgentesPerfil[0]->idVerificacao;
                echo $idperfil;
            } else {
                echo "";
            }
            $this->_helper->viewRenderer->setNoRender(true);
        }

        // Chama o SQL da lista de Entidades Vinculadas - T�cnico
        $sqllistasDeEntidadesVinculadas = ReadequacaoProjetos::retornaSQLlista("listasDeEntidadesVinculadas", $this->getIdOrgao);
        $listaEntidades = $db->fetchAll($sqllistasDeEntidadesVinculadas);

        // Chama o SQL da lista de Entidades Vinculadas - Parecerista
        $sqllistasDeEntidadesVinculadasPar = ReadequacaoProjetos::retornaSQLlista("listasDeEntidadesVinculadasPar", "NULL");
        $listaEntidadesPar = $db->fetchAll($sqllistasDeEntidadesVinculadasPar);

        // Chama o SQL Desejado e monta a lista
        $sqlAnaliseGeral = ReadequacaoProjetos::retornaSQL("sqlAnaliseGeral", "");
        $AnaliseGeral = $db->fetchAll($sqlAnaliseGeral);

        $sqlAnaliseGeralDev = ReadequacaoProjetos::retornaSQL("sqlAnaliseGeralDev", "");
        $AnaliseGeralDev = $db->fetchAll($sqlAnaliseGeralDev);


        //LISTA O HIST�RICO
        $sqlListarHistorico = ReadequacaoProjetos::retornaSQLHistorico("sqlListarHistorico");
        $Historico = $db->fetchAll($sqlListarHistorico);

        $sqlListarHistoricoUnico = ReadequacaoProjetos::retornaSQLHistorico("sqlListarHistoricoUnico");
        $HistoricoUnico = $db->fetchAll($sqlListarHistoricoUnico);

        if (count($HistoricoUnico) != 0) {
            $idOrgao = $HistoricoUnico[0]->idOrgao;
        } else {
            $idOrgao = 0;
        }

        // Chama o SQL da lista os agentes
        //		$sqllistasDeEncaminhamento = ReadequacaoProjetos::retornaSQLlista("listasDeEncaminhamento",$idOrgao);
        //		$listaParecerista = $db->fetchAll($sqllistasDeEncaminhamento);



        // ========== DEFINE QUAIS PROJETOS DETERMINADOS PERFIS PODER�O VISUALIZAR ==========
        // S� quem visualiza os Projetos s�o os Coordenadores de Acompanhamento da SAV/CGAV/CAP e da SEFIC/GEAR/SACAV.
        // Caso o �rg�o logado seja SAV/CGAV/CAP (166) pega somente os projetos da �rea de Audiovisual (2).
        // Sen�o, quando o �rg�o for SEFIC/GEAR/SACAV (272), busca os Projetos das �reas que n�o seja a de Audiovisual.
        // O �rg�o/unidade � passada atrav�s de $this->getIdOrgao
        $unidade_autorizada = ($this->getIdOrgao == 166 || $this->getIdOrgao == 272) ? $this->getIdOrgao : 0;



        //LISTAS - POR TIPO DE ALTERA��O -- AGUARDANDO AN�LISE

        $sqlAguardAnalise1 = ReadequacaoProjetos::retornaSQL("sqlCoordAcomp", 1, $unidade_autorizada); //Nome do proponente
        $AguardAnalise1 = $db->fetchAll($sqlAguardAnalise1);

        $sqlAguardAnalise2 = ReadequacaoProjetos::retornaSQL("sqlCoordAcomp", 2, $unidade_autorizada); //Troca de Agente
        $AguardAnalise2 = $db->fetchAll($sqlAguardAnalise2);

        $sqlAguardAnalise3 = ReadequacaoProjetos::retornaSQL("sqlCoordAcomp", 3, $unidade_autorizada); //Ficha t�cnica
        $AguardAnalise3 = $db->fetchAll($sqlAguardAnalise3);

        $sqlAguardAnalise4 = ReadequacaoProjetos::retornaSQL("sqlCoordAcomp", 4, $unidade_autorizada); //Local de realiza��o
        $AguardAnalise4 = $db->fetchAll($sqlAguardAnalise4);

        $sqlAguardAnalise5 = ReadequacaoProjetos::retornaSQL("sqlCoordAcomp", 5, $unidade_autorizada); //Nome do projeto
        $AguardAnalise5 = $db->fetchAll($sqlAguardAnalise5);

        $sqlAguardAnalise6 = ReadequacaoProjetos::retornaSQL("sqlCoordAcomp", 6, $unidade_autorizada); //Proposta Pedag�gica
        $AguardAnalise6 = $db->fetchAll($sqlAguardAnalise6);

        $sqlAguardAnalise7 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompProdutos", 7, $unidade_autorizada); //Produtos
        $AguardAnalise7 = $db->fetchAll($sqlAguardAnalise7);

        $sqlAguardAnalise8 = ReadequacaoProjetos::retornaSQL("sqlCoordAcomp", 8, $unidade_autorizada); //Prorroga��o de prazo de capta��o
        $AguardAnalise8 = $db->fetchAll($sqlAguardAnalise8);

        $sqlAguardAnalise9 = ReadequacaoProjetos::retornaSQL("sqlCoordAcomp", 9, $unidade_autorizada); //Prorroga��o de prazo de execu��o
        $AguardAnalise9 = $db->fetchAll($sqlAguardAnalise9);

        $sqlAguardAnalise10 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompItensProdutos", 10, $unidade_autorizada); //Itens de Custo
        $AguardAnalise10 = $db->fetchAll($sqlAguardAnalise10);

        $sqlUFs = ReadequacaoProjetos::retornaSQL("sqlUFs", ""); //Lista de UFs para listar no painel (caso haja v�rias cidades para o mesmo ID_PRONAC)
        $UFs = $db->fetchAll($sqlUFs);

        $AguardAnaliseQNTD = count($AguardAnalise1)+count($AguardAnalise2)+count($AguardAnalise3)+count($AguardAnalise4)+count($AguardAnalise5)+count($AguardAnalise6)+count($AguardAnalise7)+count($AguardAnalise8)+count($AguardAnalise9)+count($AguardAnalise10);

        //LISTAS - POR TIPO DE ALTERA��O -- DEVOLVIDOS AP�S AN�LISE

        $sqlDevolvAnalise1 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDev", 1, $unidade_autorizada); //Nome do proponente
        $DevolvAnalise1 = $db->fetchAll($sqlDevolvAnalise1);

        $sqlDevolvAnalise2 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDev", 2, $unidade_autorizada); //Raz�o social
        $DevolvAnalise2 = $db->fetchAll($sqlDevolvAnalise2);

        $sqlDevolvAnalise3 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDev", 3, $unidade_autorizada); //Ficha t�cnica
        $DevolvAnalise3 = $db->fetchAll($sqlDevolvAnalise3);

        $sqlDevolvAnalise4 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDev", 4, $unidade_autorizada); //Local de realiza��o
        $DevolvAnalise4 = $db->fetchAll($sqlDevolvAnalise4);

        $sqlDevolvAnalise5 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDev", 5, $unidade_autorizada); //Nome do projeto
        $DevolvAnalise5 = $db->fetchAll($sqlDevolvAnalise5);

        $sqlDevolvAnalise6 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDev", 6, $unidade_autorizada); //Proposta Pedag�gica
        $DevolvAnalise6 = $db->fetchAll($sqlDevolvAnalise6);

        $sqlDevolvAnalise7 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDevProdutos", 7, $unidade_autorizada); //Produtos
        $DevolvAnalise7 = $db->fetchAll($sqlDevolvAnalise7);

        $sqlDevolvAnalise8 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDev", 8, $unidade_autorizada); //Prorroga��o de prazo de capta��o
        $DevolvAnalise8 = $db->fetchAll($sqlDevolvAnalise8);

        $sqlDevolvAnalise9 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDev", 9, $unidade_autorizada); //Prorroga��o de prazo de execu��o
        $DevolvAnalise9 = $db->fetchAll($sqlDevolvAnalise9);

        $sqlDevolvAnalise10 = ReadequacaoProjetos::retornaSQL("sqlCoordAcompDevItens", 10, $unidade_autorizada); //Itens de Custo
        $DevolvAnalise10 = $db->fetchAll($sqlDevolvAnalise10);

        $DevolvAnaliseQNTD = count($DevolvAnalise1)+count($DevolvAnalise2)+count($DevolvAnalise3)+count($DevolvAnalise4)+count($DevolvAnalise5)+count($DevolvAnalise6)+count($DevolvAnalise7)+count($DevolvAnalise8)+count($DevolvAnalise9)+count($DevolvAnalise10);

        //PASSANDO VALORES PARA A VIEW
        $this->view->listaEntidades = $listaEntidades;
        $this->view->listaEntidadesPar = $listaEntidadesPar;
        $this->view->listaParecerista = '';
        $this->view->AnaliseGeral = $AnaliseGeral;
        $this->view->AnaliseGeralDev = $AnaliseGeralDev;
        $this->view->UFs = $UFs;
        $this->view->Historico = $Historico;
        $this->view->HistoricoUnico = $HistoricoUnico;

        $this->view->AguardAnalise1 = $AguardAnalise1;
        $this->view->AguardAnalise2 = $AguardAnalise2;
        $this->view->AguardAnalise3 = $AguardAnalise3;
        $this->view->AguardAnalise4 = $AguardAnalise4;
        $this->view->AguardAnalise5 = $AguardAnalise5;
        $this->view->AguardAnalise6 = $AguardAnalise6;
        $this->view->AguardAnalise7 = $AguardAnalise7;
        $this->view->AguardAnalise8 = $AguardAnalise8;
        $this->view->AguardAnalise9 = $AguardAnalise9;
        $this->view->AguardAnalise10 = $AguardAnalise10;
        $this->view->AguardAnaliseQNTD = $AguardAnaliseQNTD;

        $this->view->DevolvAnalise1 = $DevolvAnalise1;
        $this->view->DevolvAnalise2 = $DevolvAnalise2;
        $this->view->DevolvAnalise3 = $DevolvAnalise3;
        $this->view->DevolvAnalise4 = $DevolvAnalise4;
        $this->view->DevolvAnalise5 = $DevolvAnalise5;
        $this->view->DevolvAnalise6 = $DevolvAnalise6;
        $this->view->DevolvAnalise7 = $DevolvAnalise7;
        $this->view->DevolvAnalise8 = $DevolvAnalise8;
        $this->view->DevolvAnalise9 = $DevolvAnalise9;
        $this->view->DevolvAnalise10 = $DevolvAnalise10;
        $this->view->DevolvAnaliseQNTD = $DevolvAnaliseQNTD;
    }

    /**************************************************************************************************************************
    * Fun��o que chama a view verificarreadequacaodeprojeto - Tela Coordenador de Parecerista
    * ************************************************************************************************************************/

    public function verificarreadequacaodeprojetocoordpareceristaAction()
    {
        $PermissoesGrupo[] = 93; // Coordenador de Parecerista
        parent::perfil(1, $PermissoesGrupo);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL Desejado e monta a lista
        $sqlCoordPareceristaGeral = ReadequacaoProjetos::retornaSQLCP("sqlCoordPareceristaGeral", "", $this->getIdUsuario);
        $AnaliseGeral = $db->fetchAll($sqlCoordPareceristaGeral);

        //LISTA O HIST�RICO
        $sqlListarHistorico = ReadequacaoProjetos::retornaSQLHistorico("sqlListarHistorico");
        $Historico = $db->fetchAll($sqlListarHistorico);

        $sqlListarHistoricoUnico = ReadequacaoProjetos::retornaSQLHistorico("sqlListarHistoricoUnico");
        $HistoricoUnico = $db->fetchAll($sqlListarHistoricoUnico);
        //$idOrgao = $HistoricoUnico[0]->idOrgao;

        if (count($HistoricoUnico) != 0) {
            $idOrgao = $HistoricoUnico[0]->idOrgao;
        } else {
            $idOrgao = 0;
        }

        // Chama o SQL da lista dos Pareceristas
        //$sqllistasDeEncaminhamento = ReadequacaoProjetos::retornaSQLlista("listasDeEncaminhamento",$idOrgao);
        $sqllistasDeEncaminhamento = ReadequacaoProjetos::retornaSQLlista("listasDeEncaminhamento", $this->getIdOrgao);
        $listaParecerista = $db->fetchAll($sqllistasDeEncaminhamento);

        //LISTAS - POR TIPO DE ALTERA��O
        $sqlAguardAnalise6 = ReadequacaoProjetos::retornaSQLCP("sqlCoordParecerista", 6, $this->getIdUsuario); //Proposta Pedag�gica
        $AguardAnalise6 = $db->fetchAll($sqlAguardAnalise6);

        $sqlAguardAnalise10 = ReadequacaoProjetos::retornaSQLCP("sqlCoordParecerista", 7, $this->getIdUsuario, $this->getIdOrgao); //Produtos
        $AguardAnalise10 = $db->fetchAll($sqlAguardAnalise10);

        $sqlUFs = ReadequacaoProjetos::retornaSQL("sqlUFs", ""); //Lista de UFs para listar no painel (caso haja v�rias cidades para o mesmo ID_PRONAC)
        $UFs = $db->fetchAll($sqlUFs);

        $AguardAnaliseQNTD = /*count($AguardAnalise6)+*/count($AguardAnalise10);

        //LISTAS - POR TIPO DE ALTERA��O -- DEVOLVIDOS AP�S AN�LISE
        $sqlDevolvAnalise6 = ReadequacaoProjetos::retornaSQLCP("sqlCoordPareceristaDev", 6, $this->getIdUsuario); //Proposta Pedag�gica
        $DevolvAnalise6 = $db->fetchAll($sqlDevolvAnalise6);

        $sqlDevolvAnalise10 = ReadequacaoProjetos::retornaSQLCP("sqlCoordPareceristaDev", 7, $this->getIdUsuario, $this->getIdOrgao); //Produtos
        $DevolvAnalise10 = $db->fetchAll($sqlDevolvAnalise10);

        $DevolvAnaliseQNTD = /*count($DevolvAnalise6)+*/count($DevolvAnalise10);


        //PASSANDO VALORES PARA A VIEW
        $this->view->listaParecerista = $listaParecerista;
        $this->view->AnaliseGeral = $AnaliseGeral;
        $this->view->Historico = $Historico;
        $this->view->HistoricoUnico = $HistoricoUnico;
        $this->view->UFs = $UFs;

        $this->view->AguardAnalise6 = $AguardAnalise6;
        $this->view->AguardAnalise10 = $AguardAnalise10;
        $this->view->AguardAnaliseQNTD = $AguardAnaliseQNTD;

        $this->view->DevolvAnalise6 = $DevolvAnalise6;
        $this->view->DevolvAnalise10 = $DevolvAnalise10;
        $this->view->DevolvAnaliseQNTD = $DevolvAnaliseQNTD;
    }

    /**************************************************************************************************************************
    * Fun��o que chama a view verificarreadequacaodeprojeto - Tela de Parecerista
    * ************************************************************************************************************************/

    public function verificarreadequacaodeprojetopareceristaAction()
    {
        $PermissoesGrupo[] = 94; // Parecerista
        parent::perfil(1, $PermissoesGrupo);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL Desejado e monta a lista
        $sqlPareceristaGeral = ReadequacaoProjetos::retornaSQLPar("sqlPareceristaGeral", "");
        $AnaliseGeral = $db->fetchAll($sqlPareceristaGeral);

        //LISTA O HIST�RICO
        $sqlListarHistorico = ReadequacaoProjetos::retornaSQLHistorico("sqlListarHistorico");
        $Historico = $db->fetchAll($sqlListarHistorico);

        $sqlListarHistoricoUnico = ReadequacaoProjetos::retornaSQLHistorico("sqlListarHistoricoUnico");
        $HistoricoUnico = $db->fetchAll($sqlListarHistoricoUnico);

        //LISTAS - POR TIPO DE ALTERA��O
        //		$sqlAguardAnalise6 = ReadequacaoProjetos::retornaSQLPar("sqlParecerista",6); //Proposta Pedag�gica
        //		$AguardAnalise6 = $db->fetchAll($sqlAguardAnalise6);

        $sqlAguardAnalise10 = ReadequacaoProjetos::retornaSQLPar("sqlParecerista", 7, $this->getIdOrgao, null); //Produtos //$this->getIdUsuario
        $AguardAnalise10 = $db->fetchAll($sqlAguardAnalise10);

        $sqlUFs = ReadequacaoProjetos::retornaSQL("sqlUFs", ""); //Lista de UFs para listar no painel (caso haja v�rias cidades para o mesmo ID_PRONAC)
        $UFs = $db->fetchAll($sqlUFs);

        $AguardAnaliseQNTD = /*count($AguardAnalise6)+*/count($AguardAnalise10);


        //PASSANDO VALORES PARA A VIEW
        $this->view->AnaliseGeral = $AnaliseGeral;
        $this->view->Historico = $Historico;
        $this->view->HistoricoUnico = $HistoricoUnico;
        $this->view->UFs = $UFs;

        //		$this->view->AguardAnalise6 = $AguardAnalise6;
        $this->view->AguardAnalise10 = $AguardAnalise10;
        $this->view->AguardAnaliseQNTD = $AguardAnaliseQNTD;
    }

    /**************************************************************************************************************************
    * Fun��o que chama a view verificarreadequacaodeprojeto - Tela de T�cnico de Acompanhamento
    * ************************************************************************************************************************/

    public function verificarreadequacaodeprojetotecnicoAction()
    {
        $PermissoesGrupo[] = 121; // T�cnico
                $PermissoesGrupo[] = 129; // T�cnico
                parent::perfil(1, $PermissoesGrupo);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL Desejado e monta a lista
        $sqlTecnicoGeral = ReadequacaoProjetos::retornaSQLTec("sqlTecnicoGeral", "", $this->getIdUsuario, $this->getIdOrgao);

        $AnaliseGeral = $db->fetchAll($sqlTecnicoGeral);


        //LISTA O HIST�RICO
        $sqlListarHistorico = ReadequacaoProjetos::retornaSQLHistorico("sqlListarHistorico");
        $Historico = $db->fetchAll($sqlListarHistorico);

        $sqlListarHistoricoUnico = ReadequacaoProjetos::retornaSQLHistorico("sqlListarHistoricoUnico");
        $HistoricoUnico = $db->fetchAll($sqlListarHistoricoUnico);

        //LISTAS - POR TIPO DE ALTERA��O

        $sqlAguardAnalise1 = ReadequacaoProjetos::retornaSQLTec("sqlTecnico", 1, $this->getIdUsuario, $this->getIdOrgao); //Nome do proponente
        $AguardAnalise1 = $db->fetchAll($sqlAguardAnalise1);



        $sqlAguardAnalise2 = ReadequacaoProjetos::retornaSQLTec("sqlTecnico", 2, $this->getIdUsuario, $this->getIdOrgao); //Raz�o social
        $AguardAnalise2 = $db->fetchAll($sqlAguardAnalise2);


        $sqlAguardAnalise3 = ReadequacaoProjetos::retornaSQLTec("sqlTecnico", 3, $this->getIdUsuario, $this->getIdOrgao); //Ficha t�cnica
        $AguardAnalise3 = $db->fetchAll($sqlAguardAnalise3);

        $sqlAguardAnalise4 = ReadequacaoProjetos::retornaSQLTec("sqlTecnico", 4, $this->getIdUsuario, $this->getIdOrgao); //Local de realiza��o
        $AguardAnalise4 = $db->fetchAll($sqlAguardAnalise4);

        $sqlAguardAnalise5 = ReadequacaoProjetos::retornaSQLTec("sqlTecnico", 5, $this->getIdUsuario, $this->getIdOrgao); //Nome do projeto
        $AguardAnalise5 = $db->fetchAll($sqlAguardAnalise5);

        $sqlAguardAnalise6 = ReadequacaoProjetos::retornaSQLTec("sqlTecnico", 6, $this->getIdUsuario, $this->getIdOrgao); //Proposta Pedag�gica
        $AguardAnalise6 = $db->fetchAll($sqlAguardAnalise6);

        $sqlAguardAnalise7 = ReadequacaoProjetos::retornaSQLTec("sqlTecnico", 7, $this->getIdUsuario, $this->getIdOrgao); //Produtos
        $AguardAnalise7 = $db->fetchAll($sqlAguardAnalise7);

        $sqlAguardAnalise8 = ReadequacaoProjetos::retornaSQLTec("sqlTecnico", 8, $this->getIdUsuario, $this->getIdOrgao); //Prorroga��o de prazo de capta��o
        $AguardAnalise8 = $db->fetchAll($sqlAguardAnalise8);

        $sqlAguardAnalise9 = ReadequacaoProjetos::retornaSQLTec("sqlTecnico", 9, $this->getIdUsuario, $this->getIdOrgao); //Prorroga��o de prazo de execu��o
        $AguardAnalise9 = $db->fetchAll($sqlAguardAnalise9);

        $sqlUFs = ReadequacaoProjetos::retornaSQL("sqlUFs", ""); //Lista de UFs para listar no painel (caso haja v�rias cidades para o mesmo ID_PRONAC)
        $UFs = $db->fetchAll($sqlUFs);

        $AguardAnaliseQNTD = count($AguardAnalise1)+count($AguardAnalise2)+count($AguardAnalise3)+count($AguardAnalise4)+count($AguardAnalise5)+count($AguardAnalise6)+count($AguardAnalise7)+count($AguardAnalise8)+count($AguardAnalise9);


        //PASSANDO VALORES PARA A VIEW
        $this->view->AnaliseGeral = $AnaliseGeral;
        $this->view->Historico = $Historico;
        $this->view->HistoricoUnico = $HistoricoUnico;
        $this->view->UFs = $UFs;

        $this->view->AguardAnalise1 = $AguardAnalise1;
        $this->view->AguardAnalise2 = $AguardAnalise2;
        $this->view->AguardAnalise3 = $AguardAnalise3;
        $this->view->AguardAnalise4 = $AguardAnalise4;
        $this->view->AguardAnalise5 = $AguardAnalise5;
        $this->view->AguardAnalise6 = $AguardAnalise6;
        $this->view->AguardAnalise7 = $AguardAnalise7;
        $this->view->AguardAnalise8 = $AguardAnalise8;
        $this->view->AguardAnalise9 = $AguardAnalise9;
        $this->view->AguardAnaliseQNTD = $AguardAnaliseQNTD;
    }


    /**************************************************************************************************************************
    * Fun��o que chama a view Proposta Pedag�giga - VISUALIZA��O (perfil coordenador de acompanhamento - AGUARDANDO AN�LISE)
    * ************************************************************************************************************************/
    public function propostapedagogicaAction()
    {
        $id_Pronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlproposta", $id_Pronac);
        $dados = $db->fetchAll($sqlproposta);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dados[0]->PRONAC;
        $dadosTopo['NomeProjeto'] = $dados[0]->NomeProjeto;
        $dadosTopo['CNPJCPF'] = $dados[0]->CNPJCPF;
        $dadosTopo['NomeProponente'] = $dados[0]->proponente;

        $this->view->dadosTopo = $dadosTopo;
        $this->view->dados = $dados;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $id_Pronac;
        $this->view->menumsg = 'true';
        //****************************************************
    }

    /**************************************************************************************************************************
    * Fun��o que chama a view Proposta Pedag�giga - VISUALIZA��O (perfil coordenador de acompanhamento - DEVOLVIDOS AP�S AN�LISE)
    * ************************************************************************************************************************/
    public function propostapedagogicadevAction()
    {
        $id_Pronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // BUSCA O ID DO PEDIDO DE ALTERA��O
        $resultadoDadosProposta = PedidoAlteracaoDAO::buscarAlteracaoPropostaPedagogica($id_Pronac);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlpropostadev", $id_Pronac, null, null, $resultadoDadosProposta['idPedidoAlteracao']);
        $dados = $db->fetchAll($sqlproposta);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dados[0]['PRONAC'];
        $dadosTopo['NomeProjeto'] = $dados[0]['NomeProjeto'];
        $dadosTopo['CNPJCPF'] = $dados[0]['CNPJCPF'];
        $dadosTopo['NomeProponente'] = $dados[0]['proponente'];
        $this->view->dadosTopo = $dadosTopo;

        $this->view->dados = $dados;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $id_Pronac;
        $this->view->menumsg = 'true';
        //****************************************************
    }

    /**************************************************************************************************************************
    * Fun��o que chama a view Proposta Pedag�giga - EDITAR (perfil t�cnico)
    * ************************************************************************************************************************/
    public function propostapedagogicaeditarAction()
    {
        $id_Pronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // BUSCA O ID DO PEDIDO DE ALTERA��O
        $resultadoDadosProposta = PedidoAlteracaoDAO::buscarAlteracaoPropostaPedagogica($id_Pronac);

        // Chama o SQL
        $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlpropostaeditar", $id_Pronac, null, null, $resultadoDadosProposta['idPedidoAlteracao']);
        $dados = $db->fetchAll($sqlproposta);
        $idPedidoAlt = $dados[0]['idAvaliacao'];
        //VERIFICA O STATUS DA SOLICITA��O
        $sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);
        $stResult = $db->fetchAll($sqlStatusReadequacao);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dados[0]['PRONAC'];
        $dadosTopo['NomeProjeto'] = $dados[0]['NomeProjeto'];
        $dadosTopo['CNPJCPF'] = $dados[0]['CNPJCPF'];
        $dadosTopo['NomeProponente'] = $dados[0]['proponente'];

        $this->view->dadosTopo = $dadosTopo;
        $this->view->dados = $dados;
        $this->view->stResult = $stResult;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $id_Pronac;
        $this->view->menumsg = 'true';
        //****************************************************

        $this->view->dados = $dados;
    }

    /**************************************************************************************************************************
     * Fun��o para diligenciar Proposta Pedag�giga - EDITAR (perfil t�cnico)
     * ************************************************************************************************************************/
    public function propostapedagogicadiligenciarAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $AgenteLogin = $agente['idAgente'];

        $IdPronac = $_POST['IdPronac'];
        $solicitacao = $_POST['solicitacao'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            // Chama o SQL
            $sqlDiligenciarproposta = ReadequacaoProjetos::diligenciarProposta($IdPronac, $solicitacao, $AgenteLogin);
            $dados = $db->fetchAll($sqlDiligenciarproposta);
            parent::message("Dilig�ncia enviada com sucesso!", "verificarreadequacaodeprojeto/propostapedagogicaeditar?id=$IdPronac", "CONFIRM");
        } catch (Zend_Exception $e) {
            parent::message("Erro ao diligenciar a solicita��o", "verificarreadequacaodeprojeto/propostapedagogicaeditar?id=$IdPronac", "ERROR");
        }
    }


    /**************************************************************************************************************************
     * Fun��o que altera o status da solcita��o na view de Proposta Pedag�gica
     * ************************************************************************************************************************/
    public function stpropostapedAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $AgenteLogin = $agente['idAgente'];

        $idAvaliacao = $_GET['id'];
        $idPronac = $_GET['idPronac'];
        $opcao = $_GET['opcao'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            if ($opcao == 1) {
                // Chama o SQL
                $sqlstProposta = ReadequacaoProjetos::stPropostaInicio("readequacaoEA", $idAvaliacao, $AgenteLogin);
                $dados = $db->fetchAll($sqlstProposta);

                //SQL PARA ALTERAR O STATUS DO CAMPO stVerificacao da tabela tbPedidoAlteracaoXTipoAlteracao
                $registro2 = ReadequacaoProjetos::PropostaAltCampo($idAvaliacao);
                $reg2 = $db->fetchAll($registro2);
            } elseif ($opcao == 2) {
                // Chama o SQL
                $sqlstProposta = ReadequacaoProjetos::stPropostaInicio("readequacaoAP", $idAvaliacao, $AgenteLogin);
                $dados = $db->fetchAll($sqlstProposta);
            } elseif ($opcao == 3) {
                // Chama o SQL
                $sqlstProposta = ReadequacaoProjetos::stPropostaInicio("readequacaoIN", $idAvaliacao, $AgenteLogin);
                $dados = $db->fetchAll($sqlstProposta);
            }

            parent::message("Situa��o alterada com sucesso!", "verificarreadequacaodeprojeto/propostapedagogicaeditar?id=$idPronac", "CONFIRM");
        } catch (Zend_Exception $e) {
            parent::message("Erro ao alterar o status da solicita��o", "verificarreadequacaodeprojeto/propostapedagogicaeditar?id=$idPronac", "ERROR");
        }
    }


    /**************************************************************************************************************************
    * FUN��O QUE SALVA A AN�LISE DA PROPOSTA PEDAG�GICA
    * ************************************************************************************************************************/
    // 	public function salvaproppedagAction(){
//
    // 		$estrategia = $_POST['editor1'];
    // 		$especificacao = $_POST['editor2'];
    // 		$IdPRONAC = $_POST['IdPRONAC'];
    // 		$idAcao = $_POST['idAcao'];
    // 		$idAvaliacao = $_POST['idAvaliacao'];
    // 		$idPedidoAlteracao = $_POST['idPedidoAlteracao'];
    // 		$tpAlteracaoProjeto = $_POST['tpAlteracaoProjeto'];
    // 		$IdProposta = $_POST['IdProposta'];
    // 		$idOrgao = $_POST['idOrgao'];
//
    // 		$db = Zend_Db_Table::getDefaultAdapter();
    //		$db->setFetchMode(Zend_DB :: FETCH_OBJ);
//
//                try{
//                    //UPDATE - CAMPOS: dsEstrategiaExecucao E dsEspecificacaoTecnica NA TABELA SAC.dbo.tbProposta
//                    $sqlfinalproped = ReadequacaoProjetos::retornaSQLfinalprop($estrategia,$especificacao,$IdProposta);
//                    $finalproped = $db->fetchAll($sqlfinalproped);
//
//                    parent::message("A proposta foi salva com sucesso!", "verificarreadequacaodeprojeto/propostapedagogicaeditar?id=$IdPRONAC" ,"CONFIRM");
//
//                } catch (Zend_Exception $e){
//                    parent::message("Erro ao salvar a proposta.", "verificarreadequacaodeprojeto/propostapedagogicaeditar?id=$IdPRONAC" ,"ERROR");
//
//                }
    // 	}


    /**************************************************************************************************************************
    * FUN��O QUE FINALIZA A AN�LISE DA PROPOSTA PEDAG�GICA
    * ************************************************************************************************************************/
    public function finalizaproppedagAction()
    {
        $justificativaTecnico = $_POST['justificativaTecnico'];
        $estrategia = $_POST['justificativaTecnico'];
        $IdPRONAC = $_POST['IdPRONAC'];
        $idAcao = $_POST['idAcao'];
        $idAvaliacao = $_POST['idAvaliacao'];
        $idPedidoAlteracao = $_POST['idPedidoAlteracao'];
        $tpAlteracaoProjeto = $_POST['tpAlteracaoProjeto'];
        $IdProposta = $_POST['IdProposta'];
        $idOrgao = $_POST['idOrgao'];
        $parecer = $_POST['status'];

        if ($parecer == 2) {
            $status = "AP";
        } else {
            $status = "IN";
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        try {
            $db->beginTransaction();
            //UPDATE - CAMPOS: dsAvaliacao NA TABELA BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
//                    $sqlfinalproped = ReadequacaoProjetos::retornaSQLfinalprop($estrategia,$especificacao,$IdProposta);
//                    $finalproped = $db->fetchAll($sqlfinalproped);

            //UPDATE - CAMPO: stVerificacao NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlfinalproped1 = ReadequacaoProjetos::retornaSQLfinalprop1($idPedidoAlteracao, $tpAlteracaoProjeto);
            $finalproped1 = $db->fetchAll($sqlfinalproped1);

            //UPDATE - CAMPO: dtFimAvaliacao NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlfinalproped2 = ReadequacaoProjetos::retornaSQLfinalprop2($idAvaliacao, $justificativaTecnico, $status);
            $finalproped2 = $db->fetchAll($sqlfinalproped2);

            //UPDATE - CAMPO: stAtivo NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfinalproped3 = ReadequacaoProjetos::retornaSQLfinalprop3($idAcao);
            $finalproped3 = $db->fetchAll($sqlfinalproped3);

            //INSERT NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfinalproped4 = ReadequacaoProjetos::retornaSQLfinalprop4($idAvaliacao, $idOrgao);
            $finalproped4 = $db->fetchAll($sqlfinalproped4);

            $db->commit();

            parent::message("Projeto finalizado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao finalizar projeto.", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "ERROR");
        }
    }

    /**************************************************************************************************************************
    * Fun��o que chama a view Readequa��o de Produtos
    * ************************************************************************************************************************/
    public function consultareadequacaoprodutosAction()
    {
        $id_Pronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $this->_idPedidoAlteracao = $this->pegarIdPedidoAlteracao($id_Pronac);

        // Chama o SQL
        $sql = ReadequacaoProjetos::listarProdutosReadequacao("sqlCoordAcompAguardAnalise", $id_Pronac, $this->_idPedidoAlteracao);
        $dados = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dados = $db->fetchAll($sql);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dados[0]->PRONAC;
        $dadosTopo['NomeProjeto'] = $dados[0]->NomeProjeto;
        $dadosTopo['CNPJCPF'] = $dados[0]->CNPJCPF;
        $dadosTopo['NomeProponente'] = $dados[0]->proponente;

        $produtosTpAcao = array();
        foreach ($dados as $value) {
            $listaProdutos[$value->Produto][] = $value;
            if ($value->tpPlanoDistribuicao == 'SR') {
                $produtosTpAcao[$value->idProduto] = $value->tpAcao;
            }
        }
        $this->view->dados = $listaProdutos;
        $this->view->dadosTopo = $dadosTopo;
        $this->view->produtosTpAcao = $produtosTpAcao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $id_Pronac;
        $this->view->menumsg = 'true';
        //****************************************************
    }

    /**************************************************************************************************************************
    * Fun��o que chama a view Readequa��o de Itens de Custo
    * ************************************************************************************************************************/
    public function consultareadequacaoitensdecustoAction()
    {
        $idPronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $this->_idPedidoAlteracao = $this->pegarIdPedidoAlteracao($idPronac);

        $sql = ReadequacaoProjetos::dadosDoProjeto($idPronac);
        $dadosPrincipais = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dadosPrincipais = $db->fetchAll($sql);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dadosPrincipais[0]->PRONAC;
        $dadosTopo['NomeProjeto'] = $dadosPrincipais[0]->NomeProjeto;
        $dadosTopo['CNPJCPF'] = $dadosPrincipais[0]->CGCCPF;
        $dadosTopo['NomeProponente'] = $dadosPrincipais[0]->Proponente;

        // Chama o SQL
        $sql = ReadequacaoProjetos::listarProdutosReadequacao("sqlCoordAcompAguardAnalise", $idPronac, $this->_idPedidoAlteracao);
        $dados = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dados = $db->fetchAll($sql);

        $produtosTpAcao = array();
        $listaProdutos = array();
        foreach ($dados as $value) {
            $listaProdutos[$value->Produto][] = $value;
            if ($value->tpPlanoDistribuicao == 'SR') {
                $produtosTpAcao[$value->idProduto] = $value->tpAcao;
            }
        }
        $this->view->dados = $listaProdutos;
        $this->view->dadosTopo = $dadosTopo;
        $this->view->produtosTpAcao = $produtosTpAcao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idPronac;
        $this->view->menumsg = 'true';
        //****************************************************

        $buscaprojeto = new ReadequacaoProjetos();
        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;

        $orderPlanilha       = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereAP             = array('PAP.tpPlanilha = ?' => 'CO', 'PAP.stAtivo = ?' => 'S', 'PAP.IdPRONAC = ?' => $idPronac);
        $tbPlanilhaAprovacao = new PlanilhaAprovacao();
        $buscarAP            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereAP, $orderPlanilha);

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

        // manda as informa��es para a vis�o
        $this->view->planAP = $planAP;



        $pedidoAlteracao = new tbPedidoAlteracaoProjeto();
        $result = $pedidoAlteracao->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        //monta a planilha Solicitada
        $orderPlanilhaSR     = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereSR             = array('PAP.tpPlanilha = ?' => 'SR', 'PAP.stAtivo = ?' => 'N', 'PAP.IdPRONAC = ?' => $idPronac, 'PAP.idPedidoAlteracao = ?' => $result->idPedidoAlteracao, 'PAP.tpAcao != ?' => 'N');
        $buscarSR            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereSR, $orderPlanilhaSR);

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

        // manda as informa��es para a vis�o
        $this->view->planSR = $planSR;
    }

    public function solaltproditensAction()
    {
        $idPronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $this->_idPedidoAlteracao = $this->pegarIdPedidoAlteracao($idPronac);

        $sql = ReadequacaoProjetos::dadosDoProjeto($idPronac);
        $dadosPrincipais = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dadosPrincipais = $db->fetchAll($sql);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dadosPrincipais[0]->PRONAC;
        $dadosTopo['NomeProjeto'] = $dadosPrincipais[0]->NomeProjeto;
        $dadosTopo['CNPJCPF'] = $dadosPrincipais[0]->CGCCPF;
        $dadosTopo['NomeProponente'] = $dadosPrincipais[0]->Proponente;

        // Chama o SQL
        $sql = ReadequacaoProjetos::listarProdutosReadequacao("sqlCoordAcompAguardAnalise", $idPronac, $this->_idPedidoAlteracao);
        $dados = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dados = $db->fetchAll($sql);

        $produtosTpAcao = array();
        $listaProdutos = array();
        foreach ($dados as $value) {
            $listaProdutos[$value->Produto][] = $value;
            if ($value->tpPlanoDistribuicao == 'SR') {
                $produtosTpAcao[$value->idProduto] = $value->tpAcao;
            }
        }
        $this->view->dados = $listaProdutos;
        $this->view->dadosTopo = $dadosTopo;
        $this->view->produtosTpAcao = $produtosTpAcao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idPronac;
        $this->view->menumsg = 'true';
        //****************************************************

        $buscaprojeto = new ReadequacaoProjetos();
        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;

        $orderPlanilha       = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereAP             = array('PAP.tpPlanilha = ?' => 'CO', 'PAP.stAtivo = ?' => 'S', 'PAP.IdPRONAC = ?' => $idPronac);
        $tbPlanilhaAprovacao = new PlanilhaAprovacao();
        $buscarAP            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereAP, $orderPlanilha);

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

        // manda as informa��es para a vis�o
        $this->view->planAP = $planAP;



        $pedidoAlteracao = new tbPedidoAlteracaoProjeto();
        $result = $pedidoAlteracao->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        //monta a planilha Solicitada
        $orderPlanilhaSR     = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereSR             = array('PAP.tpPlanilha = ?' => 'SR', 'PAP.stAtivo = ?' => 'N', 'PAP.IdPRONAC = ?' => $idPronac, 'PAP.idPedidoAlteracao = ?' => $result->idPedidoAlteracao, 'PAP.tpAcao != ?' => 'N');
        $buscarSR            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereSR, $orderPlanilhaSR);

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

        // manda as informa��es para a vis�o
        $this->view->planSR = $planSR;
    }

    /**************************************************************************************************************************
    * Fun��o que chama a view Readequa��o de Produtos
    * ************************************************************************************************************************/
    public function readequacaoprodutosAction()
    {
        $id_Pronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $this->_idPedidoAlteracao = $this->pegarIdPedidoAlteracao($id_Pronac);

        // Chama o SQL
        $sql = ReadequacaoProjetos::listarProdutosReadequacao("sqlCoordAcompAposAnalise", $id_Pronac, $this->_idPedidoAlteracao);
        $dados = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dados = $db->fetchAll($sql);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dados[0]->PRONAC;
        $dadosTopo['NomeProjeto'] = $dados[0]->NomeProjeto;
        $dadosTopo['CNPJCPF'] = $dados[0]->CNPJCPF;
        $dadosTopo['NomeProponente'] = $dados[0]->proponente;

        $tbAvaliacao = new tbAvaliacaoSubItemPlanoDistribuicao();
        foreach ($dados as $key => $p) {
            $rs = $tbAvaliacao->buscarAvaliacao($p->idPlano, $p->idAvaliacaoItemPedidoAlteracao);
            $dados[$key]->idAvaliacaoSubItem = isset($rs->idAvaliacaoSubItem) ? $rs->idAvaliacaoSubItem : null;
            $dados[$key]->stAvaliacao = isset($rs->avaliacao) ? $rs->avaliacao : null;
            $dados[$key]->dsAvaliacao = isset($rs->descricao) ? $rs->descricao : null;
        }

        $produtosTpAcao = array();
        foreach ($dados as $value) {
            $listaProdutos[$value->Produto][] = $value;
            if ($value->tpPlanoDistribuicao == 'SR') {
                $produtosTpAcao[$value->idProduto] = $value->tpAcao;
            }
        }
        $this->view->dados = $listaProdutos;
        $this->view->dadosTopo = $dadosTopo;
        $this->view->produtosTpAcao = $produtosTpAcao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $id_Pronac;
        $this->view->menumsg = 'true';
        //****************************************************

        //VERIFICA O STATUS DA SOLICITA��O
        $sqlStatusReadequacao = new tbAvaliacaoItemPedidoAlteracao();
        $stResult = $sqlStatusReadequacao->buscar(array('idPedidoAlteracao = ?'=> $this->_idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 7, 'idAvaliacaoItemPedidoAlteracao = ?' => $dados[0]->idAvaliacaoItemPedidoAlteracao));
        $this->view->stResult = $stResult;
    }

    /**************************************************************************************************************************
    * Fun��o que chama a view Readequa��o de Itens de Custo
    * ************************************************************************************************************************/
    public function readequacaoitensdecustoAction()
    {
        $idPronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $this->_idPedidoAlteracao = $this->pegarIdPedidoAlteracao($idPronac);

        $sql = ReadequacaoProjetos::dadosDoProjeto($idPronac);
        $dadosPrincipais = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dadosPrincipais = $db->fetchAll($sql);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dadosPrincipais[0]->PRONAC;
        $dadosTopo['NomeProjeto'] = $dadosPrincipais[0]->NomeProjeto;
        $dadosTopo['CNPJCPF'] = $dadosPrincipais[0]->CGCCPF;
        $dadosTopo['NomeProponente'] = $dadosPrincipais[0]->Proponente;

        // Chama o SQL
        $sql = ReadequacaoProjetos::listarProdutosReadequacao("sqlCoordAcompAposAnaliseItens", $idPronac, $this->_idPedidoAlteracao);
        $dados = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dados = $db->fetchAll($sql);

        $listaProdutos = array();
        $tbAvaliacao = new tbAvaliacaoSubItemPlanoDistribuicao() ;
        foreach ($dados as $key => $p) {
            $rs = $tbAvaliacao->buscarAvaliacao($p->idPlano, $p->idAvaliacaoItemPedidoAlteracao);
            $dados[$key]->idAvaliacaoSubItem = isset($rs->idAvaliacaoSubItem) ? $rs->idAvaliacaoSubItem : null;
            $dados[$key]->stAvaliacao = isset($rs->avaliacao) ? $rs->avaliacao : null;
            $dados[$key]->dsAvaliacao = isset($rs->descricao) ? $rs->descricao : null;
        }

        $produtosTpAcao = array();
        foreach ($dados as $value) {
            $listaProdutos[$value->Produto][] = $value;
            if ($value->tpPlanoDistribuicao == 'SR') {
                $produtosTpAcao[$value->idProduto] = $value->tpAcao;
            }
        }
        $this->view->dados = $listaProdutos;
        $this->view->dadosTopo = $dadosTopo;
        $this->view->produtosTpAcao = $produtosTpAcao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idPronac;
        $this->view->menumsg = 'true';

        //VERIFICA O STATUS DA SOLICITA��O
        $sqlStatusReadequacao = new tbAvaliacaoItemPedidoAlteracao();
        $stResult = $sqlStatusReadequacao->buscar(array('idPedidoAlteracao = ?'=> $this->_idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 7, 'idAvaliacaoItemPedidoAlteracao = ?' => $dados[0]->idAvaliacaoItemPedidoAlteracao));
        $this->view->stResult = $stResult;
        //****************************************************

        $buscaprojeto = new ReadequacaoProjetos();
        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;

        $orderPlanilha       = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereAP             = array('PAP.tpPlanilha = ?' => 'CO', 'PAP.stAtivo = ?' => 'S', 'PAP.IdPRONAC = ?' => $idPronac);
        $tbPlanilhaAprovacao = new PlanilhaAprovacao();
        $buscarAP            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereAP, $orderPlanilha);

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

        // manda as informa��es para a vis�o
        $this->view->planAP = $planAP;



        $pedidoAlteracao = new tbPedidoAlteracaoProjeto();
        $result = $pedidoAlteracao->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        //monta a planilha Solicitada
        $orderPlanilhaSR     = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereSR             = array('PAP.tpPlanilha = ?' => 'SR', 'PAP.stAtivo = ?' => 'N', 'PAP.IdPRONAC = ?' => $idPronac, 'PAP.idPedidoAlteracao = ?' => $result->idPedidoAlteracao, 'PAP.tpAcao != ?' => 'N');
        $buscarSR            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereSR, $orderPlanilhaSR);

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
        $cont++;
        endforeach;

        // manda as informa��es para a vis�o
        $this->view->planSR = $planSR;
    }


    /**************************************************************************************************************************
     * SALVA A READEQUA��O
     * ************************************************************************************************************************/
    public function salvarreadequacaoAction()
    {
        $idPedidoAlteracao = $_GET['idPedidoAlteracao'];
        $IdPRONAC = $_GET['IdPRONAC'];

        //CONSULTA OS PEDIDOS NA TABELA tbPlanoDistribuicao
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // CHAMA O SQL
        $sqllistaidplano = ReadequacaoProjetos::listaSQLidPlano($idPedidoAlteracao);
        $ids = $db->fetchAll($sqllistaidplano);
        $this->ids = $ids;

        try {
            $db->beginTransaction();
            foreach ($this->ids as $ids) :
                            $CodArea = "cdArea = ".$_POST['CodArea'.$ids->idPlano].",";
            $CodSegmento = "cdSegmento = ".$_POST['CodSegmento'.$ids->idPlano].",";
            $Patrocinador = "qtPatrocinador = ".$_POST['Patrocinador'.$ids->idPlano].",";
            $Divulgacao = "qtProduzida = ".$_POST['Beneficiarios'.$ids->idPlano].",";
            $Beneficiarios = "qtOutros = ".$_POST['Divulgacao'.$ids->idPlano].",";
            $NormalTV = "qtVendaNormal = ".$_POST['NormalTV'.$ids->idPlano].",";
            $PromocionalTV = "qtVendaPromocional = ".$_POST['PromocionalTV'.$ids->idPlano].",";
            $NormalPU = "vlUnitarioNormal = ".$_POST['NormalPU'.$ids->idPlano].",";
            $PromocionalPU = "vlUnitarioPromocional = ".$_POST['PromocionalPU'.$ids->idPlano]."";

            $sqldados = $CodArea."".$CodSegmento."".$Patrocinador."".$Divulgacao."".$Beneficiarios."".$NormalTV."".$PromocionalTV."".$NormalPU."".$PromocionalPU;
            $updateFrom = "UPDATE SAC.dbo.tbPlanoDistribuicao SET ";
            $where = "WHERE idPedidoAlteracao = ".$idPedidoAlteracao;
            $and1 = "AND idPlano = ".$ids->idPlano;

            // SALVA OS DADOS NO BANCO
            $sqlsalvareadequacao = ReadequacaoProjetos::sqlsalvareadequacao($updateFrom, $sqldados, $where, $and1);
            $db->query($sqlsalvareadequacao);
            // select insert delete update -> query
            // fetchAll usar ap�s uma query;
            endforeach;
            $db->commit();
            parent::message("Dados salvos com sucesso!", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$IdPRONAC", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao salvar os dados do projeto", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$IdPRONAC", "ERROR");
            /* Try _ Catch, � utilizado para tratamento de erros.
             * o $e->getMessage(), � utilizado para saber qual o tipo de erro que retornou.
            */
        }
    }


    /**************************************************************************************************************************
    * FINALIZA A READEQUA��O
    * ************************************************************************************************************************/
    public function finalizarreadequacaoAction()
    {
        $idPedidoAlteracao = $_GET['idPedidoAlteracao'];
        $idPronac = $_GET['idPRONAC'];
        $stparecer = $_GET['situacao'];

        if ($stparecer == 2) {
            $situacao = 'AP';
        } else {
            $situacao = 'IN';
        }

        //CONSULTA OS PEDIDOS NA TABELA tbPlanoDistribuicao
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        // CHAMA O SQL
        $sqllistaidplano = ReadequacaoProjetos::listaSQLidPlano($idPronac);
        $ids = $db->fetchAll($sqllistaidplano);
        $this->ids = $ids;

        try {
            //inicia uma transa�ao
            $db->beginTransaction();
            $justificativa = '';
            foreach ($this->ids as $ids) :
                            $CodArea = "cdArea = ".$_POST['CodArea'.$ids->idPlano].",";
            $CodSegmento = "cdSegmento = ".$_POST['CodSegmento'.$ids->idPlano].",";
            $Patrocinador = "qtPatrocinador = ".$_POST['Patrocinador'.$ids->idPlano].",";
            $Divulgacao = "qtProduzida = ".$_POST['Beneficiarios'.$ids->idPlano].",";
            $Beneficiarios = "qtOutros = ".$_POST['Divulgacao'.$ids->idPlano].",";
            $NormalTV = "qtVendaNormal = ".$_POST['NormalTV'.$ids->idPlano].",";
            $PromocionalTV = "qtVendaPromocional = ".$_POST['PromocionalTV'.$ids->idPlano].",";
            $NormalPU = "vlUnitarioNormal = ".$_POST['NormalPU'.$ids->idPlano].",";
            $PromocionalPU = "vlUnitarioPromocional = ".$_POST['PromocionalPU'.$ids->idPlano]."";
            $justificativa2 = $_POST['justificativaPropRead'.$ids->idPlano]."";
            if ($justificativa2 == '') {
                $justificativa2 = '';
            }

            $sqldados = $CodArea."".$CodSegmento."".$Patrocinador."".$Divulgacao."".$Beneficiarios."".$NormalTV."".$PromocionalTV."".$NormalPU."".$PromocionalPU;
            $updateFrom = "UPDATE SAC.dbo.tbPlanoDistribuicao SET ";
            $where = "WHERE idPedidoAlteracao = ".$idPedidoAlteracao;
            $and1 = "AND idPlano = ".$ids->idPlano;
            $justificativa .= $justificativa2."<br/><br/>";

            // SALVA OS DADOS NO BANCO
            $sqlsalvareadequacao = ReadequacaoProjetos::sqlsalvareadequacao($updateFrom, $sqldados, $where, $and1);
            $db->query($sqlsalvareadequacao);
            // select insert delete update -> query
            // fetchAll usar ap�s uma query;
            endforeach;

            // Chama o SQL
            $sqlFinalizarPar = ReadequacaoProjetos::retornaSQLfinalizarPar($idPedidoAlteracao, $situacao, $justificativa);
            $dados = $db->fetchAll($sqlFinalizarPar);

            //RETORNA EM VARI�VEIS OS DADOS DO LOG ANTERIOR
            $sqlFinalizarPar2 = ReadequacaoProjetos::retornaSQLfinalizarPar2($idPedidoAlteracao);
            $dados = $db->fetchAll($sqlFinalizarPar2);
            $idAvaliacaoItemPedidoAlteracao = $dados[0]->idAvaliacaoItemPedidoAlteracao;
            $idAgenteAvaliador = $dados[0]->idAgenteAvaliador;
            $idOrgao = $dados[0]->idOrgao;

            //ATUALIZAR A SITUA��O DO REGISTRO
            $sqlFinalizarParST = ReadequacaoProjetos::retornaSQLfinalizarParST($idAvaliacaoItemPedidoAlteracao);
            $dados2 = $db->fetchAll($sqlFinalizarParST);
            $idPedidoAlteracao = $dados2[0]->idPedidoAlteracao;
            $tpAlteracaoProjeto = $dados2[0]->tpAlteracaoProjeto;

            $sqlFinalizarParST2 = ReadequacaoProjetos::retornaSQLfinalizarParST2($idPedidoAlteracao, $tpAlteracaoProjeto);
            $dados3 = $db->fetchAll($sqlFinalizarParST2);

            //ATUALIZAR A SITUA��O DO REGISTRO
            $sqlFinalizarPar3 = ReadequacaoProjetos::retornaSQLfinalizarPar3($idAvaliacaoItemPedidoAlteracao);
            $dados = $db->fetchAll($sqlFinalizarPar3);

            //INCLUIR NOVO REGISTRO
            $sqlFinalizarPar4 = ReadequacaoProjetos::retornaSQLfinalizarPar4($idAvaliacaoItemPedidoAlteracao, $idAgenteAvaliador, $idOrgao, $this->getIdUsuario, $this->codGrupo);
            $dados = $db->fetchAll($sqlFinalizarPar4);
            //salva os dados na base caso esteja tudo ok.
            $db->commit();

            parent::message("Projeto finalizado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "CONFIRM");
        } catch (Zend_Exception $e) {
            //Exce�ao pois houve erro ao tentar inserir ou atualizar dados na base.
            $db->rollBack();
            parent::message("Erro ao encaminhar Projeto", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "ERROR");
            /* Try _ Catch, � utilizado para tratamento de erros.
             * o $e->getMessage(), � utilizado para saber qual o tipo de erro que retornou.
            */
        }
    }


    /**************************************************************************************************************************
    * FINALIZA O ITEM DE CUSTO
    * ************************************************************************************************************************/
    public function finalizaritemdecustoAction()
    {
        $idPedidoAlteracao = $_POST['idPedidoAlteracao'];
        $idPronac = $_GET['IdPRONAC'];
        $stparecer = $_POST['status'];

        if ($stparecer == 2) {
            $situacao = 'AP';
        } else {
            $situacao = 'IN';
        }

        //CONSULTA OS PEDIDOS NA TABELA tbPlanoDistribuicao
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        // CHAMA O SQL
        $sqllistaidplano = ReadequacaoProjetos::listaSQLidPlano($idPronac);
        $ids = $db->fetchAll($sqllistaidplano);
        $this->ids = $ids;


        try {
            //inicia uma transa�ao
            $db->beginTransaction();
            $justificativa = '';
            foreach ($this->ids as $ids) :
                            $CodArea = "cdArea = ".$_POST['CodArea'.$ids->idPlano].",";
            $CodSegmento = "cdSegmento = ".$_POST['CodSegmento'.$ids->idPlano].",";
            $Patrocinador = "qtPatrocinador = ".$_POST['Patrocinador'.$ids->idPlano].",";
            $Divulgacao = "qtProduzida = ".$_POST['Beneficiarios'.$ids->idPlano].",";
            $Beneficiarios = "qtOutros = ".$_POST['Divulgacao'.$ids->idPlano].",";
            $NormalTV = "qtVendaNormal = ".$_POST['NormalTV'.$ids->idPlano].",";
            $PromocionalTV = "qtVendaPromocional = ".$_POST['PromocionalTV'.$ids->idPlano].",";
            $NormalPU = "vlUnitarioNormal = ".$_POST['NormalPU'.$ids->idPlano].",";
            $PromocionalPU = "vlUnitarioPromocional = ".$_POST['PromocionalPU'.$ids->idPlano]."";
            $justificativa2 = $_POST['justificativaPropRead'.$ids->idPlano]."";
            if ($justificativa2 == '') {
                $justificativa2 = '';
            }

            $sqldados = $CodArea."".$CodSegmento."".$Patrocinador."".$Divulgacao."".$Beneficiarios."".$NormalTV."".$PromocionalTV."".$NormalPU."".$PromocionalPU;
            $updateFrom = "UPDATE SAC.dbo.tbPlanoDistribuicao SET ";
            $where = "WHERE idPedidoAlteracao = ".$idPedidoAlteracao;
            $and1 = "AND idPlano = ".$ids->idPlano;
            $justificativa .= $justificativa2."<br/><br/>";

            // SALVA OS DADOS NO BANCO
            $sqlsalvareadequacao = ReadequacaoProjetos::sqlsalvareadequacao($updateFrom, $sqldados, $where, $and1);
            $db->query($sqlsalvareadequacao);
            // select insert delete update -> query
            // fetchAll usar ap�s uma query;
            endforeach;

            // Chama o SQL
            $sqlFinalizarPar = ReadequacaoProjetos::retornaSQLfinalizarPar($idPedidoAlteracao, $situacao, $justificativa);
            $dados = $db->fetchAll($sqlFinalizarPar);

            //RETORNA EM VARI�VEIS OS DADOS DO LOG ANTERIOR
            $sqlFinalizarPar2 = ReadequacaoProjetos::retornaSQLfinalizarPar2($idPedidoAlteracao);
            $dados = $db->fetchAll($sqlFinalizarPar2);
            $idAvaliacaoItemPedidoAlteracao = $dados[0]->idAvaliacaoItemPedidoAlteracao;
            $idAgenteAvaliador = $dados[0]->idAgenteAvaliador;
            $idOrgao = $dados[0]->idOrgao;

            //ATUALIZAR A SITUA��O DO REGISTRO
            $sqlFinalizarParST = ReadequacaoProjetos::retornaSQLfinalizarParST($idAvaliacaoItemPedidoAlteracao);
            $dados2 = $db->fetchAll($sqlFinalizarParST);
            $idPedidoAlteracao = $dados2[0]->idPedidoAlteracao;
            $tpAlteracaoProjeto = $dados2[0]->tpAlteracaoProjeto;

            $sqlFinalizarParST2 = ReadequacaoProjetos::retornaSQLfinalizarParST2($idPedidoAlteracao, $tpAlteracaoProjeto);
            $dados3 = $db->fetchAll($sqlFinalizarParST2);

            //ATUALIZAR A SITUA��O DO REGISTRO
            $sqlFinalizarPar3 = ReadequacaoProjetos::retornaSQLfinalizarPar3($idAvaliacaoItemPedidoAlteracao);
            $dados = $db->fetchAll($sqlFinalizarPar3);

            //INCLUIR NOVO REGISTRO
            $sqlFinalizarPar4 = ReadequacaoProjetos::retornaSQLfinalizarPar4IC($idAvaliacaoItemPedidoAlteracao, $idAgenteAvaliador, $idOrgao, $this->getIdUsuario, $this->codGrupo);
            $dados = $db->fetchAll($sqlFinalizarPar4);
            //salva os dados na base caso esteja tudo ok.
            $db->commit();

            parent::message("Projeto finalizado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetoparecerista", "CONFIRM");
        } catch (Zend_Exception $e) {
            //Exce�ao pois houve erro ao tentar inserir ou atualizar dados na base.
            $db->rollBack();
            parent::message("Erro ao encaminhar Projeto", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetoparecerista", "ERROR");
            /* Try _ Catch, � utilizado para tratamento de erros.
             * o $e->getMessage(), � utilizado para saber qual o tipo de erro que retornou.
            */
        }
    }


    /**************************************************************************************************************************
    * Fun��o que altera o status da solicita��o na view Readequa��o de Produtos
    * ************************************************************************************************************************/
    public function readequacaoprodutoseditarAction()
    {
        $id_Pronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $this->_idPedidoAlteracao = $this->pegarIdPedidoAlteracao($id_Pronac);

        // Chama o SQL
        $sql = ReadequacaoProjetos::listarProdutosReadequacao("sqlTecAcomp", $id_Pronac, $this->_idPedidoAlteracao);
        $dados = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dados = $db->fetchAll($sql);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dados[0]->PRONAC;
        $dadosTopo['NomeProjeto'] = $dados[0]->NomeProjeto;
        $dadosTopo['CNPJCPF'] = $dados[0]->CNPJCPF;
        $dadosTopo['NomeProponente'] = $dados[0]->proponente;

        $tbAvaliacao = new tbAvaliacaoSubItemPlanoDistribuicao();
        foreach ($dados as $key => $p) {
            $rs = $tbAvaliacao->buscarAvaliacao($p->idPlano, $p->idAvaliacaoItemPedidoAlteracao);
            $dados[$key]->idAvaliacaoSubItem = isset($rs->idAvaliacaoSubItem) ? $rs->idAvaliacaoSubItem : null;
            $dados[$key]->stAvaliacao = isset($rs->avaliacao) ? $rs->avaliacao : null;
            $dados[$key]->dsAvaliacao = isset($rs->descricao) ? $rs->descricao : null;
        }

        $produtosTpAcao = array();
        foreach ($dados as $value) {
            $listaProdutos[$value->Produto][] = $value;
            if ($value->tpPlanoDistribuicao == 'SR') {
                $produtosTpAcao[$value->idProduto] = $value->tpAcao;
            }
        }
        $this->view->dados = $listaProdutos;
        $this->view->dadosTopo = $dadosTopo;
        $this->view->produtosTpAcao = $produtosTpAcao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $id_Pronac;
        $this->view->menumsg = 'true';
        //****************************************************

        //VERIFICA O STATUS DA SOLICITA��O
        $sqlStatusReadequacao = new tbAvaliacaoItemPedidoAlteracao();
        $stResult = $sqlStatusReadequacao->buscar(array('idPedidoAlteracao = ?'=> $this->_idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 7));
        $this->view->stResult = $stResult;
    }


    /**************************************************************************************************************************
    * Fun��o que altera o status da solicita��o na view Readequa��o de Produtos
    * ************************************************************************************************************************/
    public function readequacaoitensdecustoeditarAction()
    {
        $idPronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $this->_idPedidoAlteracao = $this->pegarIdPedidoAlteracao($idPronac);
        $this->view->idPronac = $idPronac;
        $this->view->idPedidoAlteracao = $this->_idPedidoAlteracao;

        $sql = ReadequacaoProjetos::dadosDoProjeto($idPronac);
        $dadosPrincipais = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dadosPrincipais = $db->fetchAll($sql);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dadosPrincipais[0]->PRONAC;
        $dadosTopo['NomeProjeto'] = $dadosPrincipais[0]->NomeProjeto;
        $dadosTopo['CNPJCPF'] = $dadosPrincipais[0]->CGCCPF;
        $dadosTopo['NomeProponente'] = $dadosPrincipais[0]->Proponente;

        // Chama o SQL
        $sql = ReadequacaoProjetos::listarProdutosReadequacao("sqlPareceristaAtual", $idPronac, $this->_idPedidoAlteracao);
        $dados = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dados = $db->fetchAll($sql);

        $tbAvaliacao = new tbAvaliacaoSubItemPlanoDistribuicao();
        foreach ($dados as $key => $p) {
            $rs = $tbAvaliacao->buscarAvaliacao($p->idPlano, $p->idAvaliacaoItemPedidoAlteracao);
            $dados[$key]->idAvaliacaoSubItem = isset($rs->idAvaliacaoSubItem) ? $rs->idAvaliacaoSubItem : null;
            $dados[$key]->stAvaliacao = isset($rs->avaliacao) ? $rs->avaliacao : null;
            $dados[$key]->dsAvaliacao = isset($rs->descricao) ? $rs->descricao : null;
        }

        $listaProdutos = array();
        $produtosTpAcao = array();
        foreach ($dados as $value) {
            $listaProdutos[$value->Produto][] = $value;
            if ($value->tpPlanoDistribuicao == 'SR') {
                $produtosTpAcao[$value->idProduto] = $value->tpAcao;
            }
        }
        $this->view->dados = $listaProdutos;
        $this->view->dadosTopo = $dadosTopo;
        $this->view->produtosTpAcao = $produtosTpAcao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->menumsg = 'true';
        //****************************************************

        //VERIFICA O STATUS DA SOLICITA��O
        $sqlStatusReadequacao = new tbAvaliacaoItemPedidoAlteracao();
        $stResult = $sqlStatusReadequacao->buscar(array('idPedidoAlteracao = ?'=> $this->_idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 7, 'stAvaliacaoItemPedidoAlteracao != ?' => 'AP'));
        $this->view->stResult = $stResult;

        $buscaprojeto = new ReadequacaoProjetos();
        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;

        $orderPlanilha       = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereAP             = array('PAP.tpPlanilha = ?' => 'CO', 'PAP.stAtivo = ?' => 'S', 'PAP.IdPRONAC = ?' => $idPronac);
        $tbPlanilhaAprovacao = new PlanilhaAprovacao();
        $buscarAP            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereAP, $orderPlanilha);

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

        // manda as informa��es para a vis�o
        $this->view->planAP = $planAP;

        $pedidoAlteracao = new tbPedidoAlteracaoProjeto();
        $result = $pedidoAlteracao->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        //monta a planilha Solicitada
        $orderPlanilhaSR     = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereSR             = array('PAP.tpPlanilha = ?' => 'SR', 'PAP.stAtivo = ?' => 'N', 'PAP.IdPRONAC = ?' => $idPronac, 'PAP.idPedidoAlteracao = ?' => $result->idPedidoAlteracao, 'PAP.tpAcao != ?' => 'N');
        $buscarSR            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereSR, $orderPlanilhaSR);

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

        // manda as informa��es para a vis�o
        $this->view->planSR = $planSR;


//                $verificaPedidoAlteracaoProjetoProduto = ReadequacaoProjetos::verificaPedidoAlteracaoProjetoProduto($id_Pronac);
//                $ListaPedidoAlteracaoProjetoProduto = $db->fetchAll($verificaPedidoAlteracaoProjetoProduto);
//
//                if ( empty ( $ListaPedidoAlteracaoProjetoProduto[0]->idPedidoAlteracao ) )
//                {
//
//                        $idpronac = $_GET['id'];
//                        $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO;
//                        $verificaIdPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::verificaPedidoAlteracao($idpronac);
//
//                        $idpedidoalteracao = $verificaIdPedidoAlteracao[0]->idPedidoAlteracao;
//                        $dados = array('stAvaliacaoItemPedidoAlteracao' => 'AG');
//                        $where = " idpedidoalteracao = $idpedidoalteracao";
//                        //$avaliacao = $buscaInformacoes->atualizarAvaliacaopedido($dados, $where);
//
//                        //parent::message("N&atilde;o h�  readequa�&otilde;es para produto, por favor verifique os itens de custo", "/verificarsolicitacaodereadequacoes/planilhasolicitada?idPronac=$id_Pronac", "ALERT");
//                        $this->_redirect('verificarsolicitacaodereadequacoes/planilhasolicitada?idPronac='.$id_Pronac);
//                }
//
//		//LISTA COMBO DE "�REA" NA VIEW
//		$sqlListaArea = ReadequacaoProjetos::retornaSQLproposta("sqlListaArea","NULL");
//		$ListaArea = $db->fetchAll($sqlListaArea);
//
//		//LISTA COMBO DE "SEGMENTO" NA VIEW
//		$sqlListaSegmento = ReadequacaoProjetos::retornaSQLproposta("sqlListaSegmento","NULL");
//		$ListaSegmento = $db->fetchAll($sqlListaSegmento);
//
//		// Chama o SQL
//                // verifica se o registro do Parecerista/T�cnico existe
//		$sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaReadequacaoEditarParecerista", $id_Pronac, null, true);
//
//		$dados = $db->fetchAll($sqlproposta);
//
//                $sqlproposta2 = ReadequacaoProjetos::retornaSQLproposta("sqlConsultaReadequacaoParecerista", $id_Pronac, null, true);
//		$dados2 = $db->fetchAll($sqlproposta2);
//
//		$idPedidoAlt = isset($dados[0]->idAvaliacaoItemPedidoAlteracao) ? $dados[0]->idAvaliacaoItemPedidoAlteracao : 0;
//		$iframe      = isset($dados[0]->CNPJCPF) ? $dados[0]->CNPJCPF : 0;
//
//		//VERIFICA O STATUS DA SOLICITA��O
//
//		$sqlStatusReadequacao = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlt);
//		$stResult = $db->fetchAll($sqlStatusReadequacao);
//
//		$this->view->ListaArea = $ListaArea;
//		$this->view->ListaSegmento = $ListaSegmento;
//		$this->view->dados = $dados;
//                $this->view->dados2 = $dados2;
//                $this->view->iframe = $iframe;
//                $this->view->stResult = $stResult;
//		$this->view->IdPronac = $id_Pronac;
//
//                if( isset ( $_GET['fim'] ) )
//                {
//
//
//
//                    $idpronac = $_GET['id'];
//                    $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO;
//                    $verificaIdPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::verificaPedidoAlteracao($idpronac);
//
//                    $idpedidoalteracao = $verificaIdPedidoAlteracao[0]->idPedidoAlteracao;
//                    $dados = array('stAvaliacaoItemPedidoAlteracao' => 'AG');
//                    $where = " idpedidoalteracao = $idpedidoalteracao";
//                    $avaliacao = $buscaInformacoes->atualizarAvaliacaopedido($dados, $where);
//                }
    }
    public function readequacaoitensdecustoconcluidoAction()
    {
        $idPronac = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $this->_idPedidoAlteracao = $this->pegarIdPedidoAlteracao($idPronac);
        $this->view->idPronac = $idPronac;
        $this->view->idPedidoAlteracao = $this->_idPedidoAlteracao;

        $sql = ReadequacaoProjetos::dadosDoProjeto($idPronac);
        $dadosPrincipais = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dadosPrincipais = $db->fetchAll($sql);

        $dadosTopo = array();
        $dadosTopo['Pronac'] = $dadosPrincipais[0]->PRONAC;
        $dadosTopo['NomeProjeto'] = $dadosPrincipais[0]->NomeProjeto;
        $dadosTopo['CNPJCPF'] = $dadosPrincipais[0]->CGCCPF;
        $dadosTopo['NomeProponente'] = $dadosPrincipais[0]->Proponente;

        // Chama o SQL
        $sql = ReadequacaoProjetos::listarProdutosReadequacao("sqlCoordAcompanhamentoConcluido", $idPronac, $this->_idPedidoAlteracao);
        $dados = $db->fetchAll('SET TEXTSIZE 2147483647;');
        $dados = $db->fetchAll($sql);

        $tbAvaliacao = new tbAvaliacaoSubItemPlanoDistribuicao();
        foreach ($dados as $key => $p) {
            $rs = $tbAvaliacao->buscarAvaliacao($p->idPlano, $p->idAvaliacaoItemPedidoAlteracao);
            $dados[$key]->idAvaliacaoSubItem = isset($rs->idAvaliacaoSubItem) ? $rs->idAvaliacaoSubItem : null;
            $dados[$key]->stAvaliacao = isset($rs->avaliacao) ? $rs->avaliacao : null;
            $dados[$key]->dsAvaliacao = isset($rs->descricao) ? $rs->descricao : null;
        }

        $listaProdutos = array();
        $produtosTpAcao = array();
        foreach ($dados as $value) {
            $listaProdutos[$value->Produto][] = $value;
            if ($value->tpPlanoDistribuicao == 'SR') {
                $produtosTpAcao[$value->idProduto] = $value->tpAcao;
            }
        }
        $this->view->dados = $listaProdutos;
        $this->view->dadosTopo = $dadosTopo;
        $this->view->produtosTpAcao = $produtosTpAcao;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->menumsg = 'true';
        //****************************************************

        //VERIFICA O STATUS DA SOLICITA��O
        $sqlStatusReadequacao = new tbAvaliacaoItemPedidoAlteracao();
        $stResult = $sqlStatusReadequacao->buscar(array('idPedidoAlteracao = ?'=> $this->_idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 7, 'stAvaliacaoItemPedidoAlteracao not in (?)' => array('AG','EA')));
        $this->view->stResult = $stResult;

        $buscaprojeto = new ReadequacaoProjetos();
        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;

        $orderPlanilha       = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereAP             = array('PAP.tpPlanilha = ?' => 'CO', 'PAP.stAtivo = ?' => 'S', 'PAP.IdPRONAC = ?' => $idPronac);
        $tbPlanilhaAprovacao = new PlanilhaAprovacao();
        $buscarAP            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereAP, $orderPlanilha);

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

        // manda as informa��es para a vis�o
        $this->view->planAP = $planAP;

        $pedidoAlteracao = new tbPedidoAlteracaoProjeto();
        $result = $pedidoAlteracao->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        //monta a planilha Solicitada
        $orderPlanilhaSR = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereSR         = array('PAP.tpPlanilha = ?' => 'SR', 'PAP.stAtivo = ?' => 'N', 'PAP.IdPRONAC = ?' => $idPronac, 'PAP.idPedidoAlteracao = ?' => $result->idPedidoAlteracao, 'PAP.tpAcao != ?' => 'N');
        $buscarSR        = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereSR, $orderPlanilhaSR);

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

        // manda as informa��es para a vis�o
        $this->view->planSR = $planSR;
    }


    /**************************************************************************************************************************
    * Fun��o que altera o status da solcita��o na view de Readequa��o de Produtos
    * ************************************************************************************************************************/
    public function streadequacaoprodutosAction()
    {
        $IdPronac = $_GET['IdPronac'];
        if (!isset($IdPronac) || empty($IdPronac)) {
            parent::message("Projeto n�o encontrado.", "principal", "ERROR");
        }
        if ($_GET['opcao'] != '1') {
            parent::message("Erro ao alterar o status da solicita��o", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$IdPronac", "ERROR");
        }

        $verificaIdPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::verificaPedidoAlteracao($IdPronac);
        $idpedidoalteracao = $verificaIdPedidoAlteracao[0]->idPedidoAlteracao;

        $resultadoItem = VerificarSolicitacaodeReadequacoesDAO::verificaPlanilhaAprovacao($IdPronac);
        if (empty($resultadoItem)) {
            $inserirCopiaPlanilha = VerificarSolicitacaodeReadequacoesDAO::inserirCopiaPlanilha($IdPronac, $idpedidoalteracao);
        }

        //retorna o id do agente logado
            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idAgente = $agente['idAgente'];

        $idPedidoAlteracao = $_GET['id']; //idPedido Altera��o � o idAvaliacaoItemPedidoAlteracao da tabela tbAvaliacaoItemPedidoAlteracao
            $opcao = $_GET['opcao']; //op��o escolhida no select - APROVADO, INDEFERIDO ou EM AN�LISE

            $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $registro = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlteracao);
        $reg = $db->fetchAll($registro);

        $this->tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $whereProduto = array('idPedidoAlteracao = ?' => $reg[0]->idPedidoAlteracao, 'tpPlanoDistribuicao = ?' => 'SR');
        $listaProdutosSR = $this->tbPlanoDistribuicao->buscar($whereProduto);

        $whereProdutoAT = array('idPedidoAlteracao = ?' => $reg[0]->idPedidoAlteracao, 'tpPlanoDistribuicao = ?' => 'AT');
        $listaProdutosAT = $this->tbPlanoDistribuicao->buscar($whereProdutoAT);

        //VERIFICA SE J� POSSUI PLANO DE DISTRIBUI��O DO TIPO AT PARA N�O GERAR OUTRA C�PIA DE AN�LISE T�CNICA
        if (count($listaProdutosAT) <= 0) {
            foreach ($listaProdutosSR as $d) {
                if ($d->tpAcao != 'N') {
                    $dadosCopia = array(
                                'idPlanoDistribuicao'    => $d->idPlanoDistribuicao
                                ,'cdArea'                => $d->cdArea
                                ,'cdSegmento'            => $d->cdSegmento
                                ,'idPedidoAlteracao'     => $reg[0]->idPedidoAlteracao
                                ,'idProduto'             => $d->idProduto
                                ,'idPosicaoLogo'         => $d->idPosicaoLogo
                                ,'qtPatrocinador'        => $d->qtPatrocinador
                                ,'qtProduzida'           => $d->qtProduzida
                                ,'qtOutros'              => $d->qtOutros
                                ,'qtVendaNormal'         => $d->qtVendaNormal
                                ,'qtVendaPromocional'    => $d->qtVendaPromocional
                                ,'vlUnitarioNormal'      => $d->vlUnitarioNormal
                                ,'vlUnitarioPromocional' => $d->vlUnitarioPromocional
                                ,'stPrincipal'           => $d->stPrincipal
                                ,'tpAcao'                => $d->tpAcao
                                ,'tpPlanoDistribuicao'   => 'AT'
                                ,'dtPlanoDistribuicao'   => new Zend_Db_Expr('GETDATE()')
                        );
                    //INSERE UMA C�PIA QUE SER� ALTERADA PELO T�CNICO DE ACOMPANHAMENTO - AT
                    $this->tbPlanoDistribuicao->inserir($dadosCopia);
                }
            }
        }

        //REGISTRO EM QUEST�O
        $idPedido = $reg[0]->idAvaliacaoItemPedidoAlteracao;
        try {
            if ($opcao == 1) {
                // Chama o SQL
                $sqlstReadequacao = ReadequacaoProjetos::stReadequacaoInicio("readequacaoEA", $idPedidoAlteracao, $idAgente);
                $db->fetchAll($sqlstReadequacao);

                //SQL PARA ALTERAR O STATUS DO CAMPO stVerificacao da tabela tbPedidoAlteracaoXTipoAlteracao
                $registro2 = ReadequacaoProjetos::readequacaoAltCampo($idPedido);
                $db->fetchAll($registro2);
            } elseif ($opcao == 2) {
                // Chama o SQL
                $sqlstReadequacao = ReadequacaoProjetos::stReadequacaoInicio("readequacaoAP", $idPedidoAlteracao, $idAgente);
                $db->fetchAll($sqlstReadequacao);
            } elseif ($opcao == 3) {
                // Chama o SQL
                $sqlstReadequacao = ReadequacaoProjetos::stReadequacaoInicio("readequacaoIN", $idPedidoAlteracao, $idAgente);
                $db->fetchAll($sqlstReadequacao);
            }

            if (isset($_GET['itemDeCusto']) && $_GET['itemDeCusto']) {
                parent::message("Situa��o alterada com sucesso!", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$IdPronac", "CONFIRM");
            } else {
                parent::message("Situa��o alterada com sucesso!", "verificarreadequacaodeprojeto/readequacaoprodutoseditar?id=$IdPronac", "CONFIRM");
            }
        } catch (Zend_Exception $e) {
            if (isset($_GET['itemDeCusto']) && $_GET['itemDeCusto']) {
                parent::message("Erro ao alterar o status da solicita��o", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$IdPronac", "ERROR");
            } else {
                parent::message("Erro ao alterar o status da solicita��o", "verificarreadequacaodeprojeto/readequacaoprodutoseditar?id=$IdPronac", "ERROR");
            }
        }
    }

    /**************************************************************************************************************************
    * Fun��o que altera o status da solcita��o na view de Readequa��o de Itens de Custo
    * ************************************************************************************************************************/
    public function streadequacaoitensdecustoAction()
    {

        //retorna o id do agente logado
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idAgente = $agente['idAgente'];

        $idPedidoAlteracao = $_GET['id']; //idPedido Altera��o � o idAvaliacaoItemPedidoAlteracao da tabela tbAvaliacaoItemPedidoAlteracao
        $opcao = $_GET['opcao']; //op��o escolhida no select - APROVADO, INDEFERIDO ou EM AN�LISE
        $IdPronac = $_GET['IdPronac'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        //SQL PARA TRAZER OD DADOS DO REGISTRO EM QUEST�O
        $registro = ReadequacaoProjetos::alteraStatusReadequacao($idPedidoAlteracao);
        $reg = $db->fetchAll($registro);
        $idPedido = $reg[0]->idAvaliacaoItemPedidoAlteracao;
        try {
            if ($opcao == 1) {
                // Chama o SQL
                $sqlstReadequacao = ReadequacaoProjetos::stReadequacaoInicio("readequacaoEA", $idPedidoAlteracao, $idAgente);
                $db->fetchAll($sqlstReadequacao);

                //SQL PARA ALTERAR O STATUS DO CAMPO stVerificacao da tabela tbPedidoAlteracaoXTipoAlteracao
                $registro2 = ReadequacaoProjetos::readequacaoAltCampo($idPedido);
                $db->fetchAll($registro2);
            } elseif ($opcao == 2) {
                // Chama o SQL
                $sqlstReadequacao = ReadequacaoProjetos::stReadequacaoInicio("readequacaoAP", $idPedidoAlteracao, $idAgente);
                $db->fetchAll($sqlstReadequacao);
            } elseif ($opcao == 3) {
                // Chama o SQL
                $sqlstReadequacao = ReadequacaoProjetos::stReadequacaoInicio("readequacaoIN", $idPedidoAlteracao, $idAgente);
                $db->fetchAll($sqlstReadequacao);
            }

            parent::message("Situa��o alterada com sucesso!", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$IdPronac", "CONFIRM");
        } catch (Zend_Exception $e) {
            parent::message("Erro ao alterar o status da solicita��o", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$IdPronac", "ERROR");
        }
    }


    /**************************************************************************************************************************
 * Fun��o que devolve o pedido para a fila do coordenador de acompanhamento
 * ************************************************************************************************************************/
    public function devolverpedidoAction()
    {

                //retorna o id do agente logado
        $idAgenteRemetente = $this->getIdUsuario;
        $idPerfilRemetente = $this->codGrupo;

        $idAcao = $_GET['id'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        try {
            $db->beginTransaction();
            //ATUALIZA O CAMPO stAtivo NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqldev = ReadequacaoProjetos::retornaSQLdevolverMinc($idAcao);
            $dados = $db->fetchAll($sqldev);

            //BUSCA OS REGISTROS DA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqldev2 = ReadequacaoProjetos::retornaSQLdevolverMinc2($idAcao);
            $dados = $db->fetchAll($sqldev2);
            $id = $dados[0]->idAvaliacaoItemPedidoAlteracao;
            $idOrgao = $dados[0]->idOrgao;

            //BUSCA OS REGISTROS DOS CAMPOS idPedidoAlteracao E tpAlteracaoProjeto DA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqldev3 = ReadequacaoProjetos::retornaSQLdevolverMinc3($id);
            $dados = $db->fetchAll($sqldev3);
            $idPedidoAlt = $dados[0]->idPedidoAlteracao;
            $tpAlt = $dados[0]->tpAlteracaoProjeto;

            //ATUALIZA O CAMPO stVerificacao NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqldev4 = ReadequacaoProjetos::retornaSQLdevolverMinc4($idPedidoAlt, $tpAlt);
            $dados = $db->fetchAll($sqldev4);

            //CRIAR NOVO REGISTRO DE ENCAMINHAMENTO NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqldev5 = ReadequacaoProjetos::retornaSQLdevolverMinc5($id, $idOrgao, $idAgenteRemetente, $idPerfilRemetente);
            $dados = $db->fetchAll($sqldev5);

            $db->commit();
            parent::message("Devolu&ccedil;&atilde;o da solicita&ccedil;&atilde;o feita com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordparecerista", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro na devolu&ccedil;&atilde;o da solicita&ccedil;&atilde;o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordparecerista", "ERROR");
        }
    }


    /**************************************************************************************************************************
 * FUN��O QUE FINALIZA A SOLICITA��O (GERAL - TELA DE COORDENADOR DE ACOMPANHAMENTO)
 * ************************************************************************************************************************/
    public function finalizageralAction()
    {
        $tbAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        //idAcaoAvaliacaoItemPedidoAlteracao da Tabela BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
        $idAcao = $_GET['id'];

//            $new = new tbProposta();
//            $ss = $new->finalizarReadequacaoDeProposta('119720');


        //retorna o id do agente logado
        $idAgenteRemetente = $this->getIdUsuario;
        $idPerfilRemetente = $this->codGrupo;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();
            //ATUALIZA OS CAMPOS stAtivo e stVerificacao NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfin = ReadequacaoProjetos::retornaSQLfinalizaGeral($idAcao);
            $dados = $db->fetchAll($sqlfin);

            //BUSCA OS REGISTROS DA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfin2 = ReadequacaoProjetos::retornaSQLfinalizaGeral2($idAcao);
            $dados = $db->fetchAll($sqlfin2);
            $id = $dados[0]->idAvaliacaoItemPedidoAlteracao;
            $idOrgao = $dados[0]->idOrgao;

            //BUSCA OS REGISTROS DOS CAMPOS idPedidoAlteracao E tpAlteracaoProjeto DA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlfin3 = ReadequacaoProjetos::retornaSQLfinalizaGeral3($id);
            $dados = $db->fetchAll($sqlfin3);
            $idPedidoAlt = $dados[0]->idPedidoAlteracao;
            $tpAlt = $dados[0]->tpAlteracaoProjeto;
            $stAvaliacaoItem = $dados[0]->stAvaliacaoItemPedidoAlteracao;

            //ATUALIZA O CAMPO stVerificacao NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlfin4 = ReadequacaoProjetos::retornaSQLfinalizaGeral4($idPedidoAlt, $tpAlt);
            $dados = $db->fetchAll($sqlfin4);

            //CRIAR NOVO REGISTRO DE ENCAMINHAMENTO NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            if (!isset($_GET['checklist'])) {
                $sqlfin5 = ReadequacaoProjetos::retornaSQLfinalizaGeral5($id, $idOrgao, $idAgenteRemetente, $idPerfilRemetente);
                $dados = $db->fetchAll($sqlfin5);
            }

            //BUSCA O IDPRONAC DA TABELA tbPedidoAlteracaoProjeto
            $sqlfin6 = ReadequacaoProjetos::retornaSQLfinalizaGeral6($idPedidoAlt);
            $dados = $db->fetchAll($sqlfin6);
            $idPronac = $dados[0]->IdPRONAC;

            //Verifica se possui item de custo NA TABELA tbPedidoAlteracaoXTipoAlteracao
            if ($tpAlt == 7) {
                $sqlfin7 = ReadequacaoProjetos::retornaSQLfinalizaGeral7($idPedidoAlt);
                $itens = $db->fetchAll($sqlfin7);
                if (count($itens) == 2) {
                    $tpAlt = 10;
                }
            }

            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
            $idagente = $agente['idAgente'];

            if ($stAvaliacaoItem == 'AP') {
                if ($tpAlt == 1 && isset($_GET['checklist'])) {
                    //NOME DO PROPONENTE
                    $NomeProponenteSolicitado = PedidoAlteracaoDAO::buscarAlteracaoNomeProponente($idPronac);

                    $proponente = new Interessado();
                    $dados = array(
                            'Nome' => mb_convert_case(strtolower($NomeProponenteSolicitado['proponente']), MB_CASE_TITLE, "ISO-8859-1")
                        );
                    $proponente->alterar($dados, array('CgcCpf = ?' => $NomeProponenteSolicitado['CgcCpf']));
                } elseif ($tpAlt == 2 && isset($_GET['checklist'])) {
                    //TROCA DE PROPONENTE
                    $trocaProponenteAtual = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idPronac);
                    $NomeAtual = $trocaProponenteAtual['proponente'];
                    $CpfCnpjAtual = $trocaProponenteAtual['CgcCpf'];
                    $idNome = $trocaProponenteAtual['idNome'];
                    $trocaProponenteSolicitada = PedidoAlteracaoDAO::buscarAlteracaoRazaoSocial($idPronac);
                    $NomeSolicitado = $trocaProponenteSolicitada['nmRazaoSocial'];
                    $CpfCnpjSolicitado = $trocaProponenteSolicitada['CgcCpf'];

                    // altera o cpf do proponente
                    $_Projetos = new Projetos();
                    $_alterarProponente = $_Projetos->alterar(array('CgcCpf' => $CpfCnpjSolicitado), array('IdPRONAC = ?' => $idPronac));

                    // altera o nome do proponente
                    $_Nomes = new Nomes();
                    $_alterarNome = $_Nomes->alterar(array('Descricao' => $NomeSolicitado), array('idNome = ?' => $idNome));

                    $proponente = new Interessado();
                    $dados = array(
                                'Nome' => mb_convert_case(strtolower($NomeSolicitado), MB_CASE_TITLE, "ISO-8859-1")
                            );
                    $proponente->alterar($dados, array('CgcCpf = ?' => $CpfCnpjSolicitado));


                    /**
                     * ==============================================================
                     * INICIO DA ATUALIZACAO DO VINCULO DO PROPONENTE
                     * ==============================================================
                     */
                    $Projetos          = new Projetos();
                    $Agentes           = new Agente_Model_DbTable_Agentes();
                    $Visao             = new Visao();
                    $tbVinculo         = new Agente_Model_DbTable_TbVinculo();
                    $tbVinculoProposta = new tbVinculoProposta();

                    /* ========== BUSCA OS DADOS DO PROPONENTE ANTIGO ========== */
                    $buscarCpfProponenteAntigo = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac));
                    $cpfProponenteAntigo       = count($buscarCpfProponenteAntigo) > 0 ? $buscarCpfProponenteAntigo[0]->CgcCpf : 0;
                    $buscarIdProponenteAntigo  = $Agentes->buscar(array('CNPJCPF = ?' => $cpfProponenteAntigo));
                    $idProponenteAntigo        = count($buscarIdProponenteAntigo) > 0 ? $buscarIdProponenteAntigo[0]->idAgente : 0;
                    $idPreProjetoVinculo       = count($buscarCpfProponenteAntigo) > 0 ? $buscarCpfProponenteAntigo[0]->idProjeto : 0;

                    /* ========== BUSCA OS DADOS DO NOVO PROPONENTE ========== */
                    $buscarNovoProponente = $Agentes->buscar(array('CNPJCPF = ?' => $CpfCnpjSolicitado));
                    $idNovoProponente     = count($buscarNovoProponente) > 0 ? $buscarNovoProponente[0]->idAgente : 0;
                    $buscarVisao          = $Visao->buscar(array('Visao = ?' => 144, 'idAgente = ?' => $idNovoProponente));

                    /* ========== BUSCA OS DADOS DA PROPOSTA VINCULADA ========== */
                    $idVinculo = $tbVinculoProposta->buscar(array('idPreProjeto = ?' => $idPreProjetoVinculo));

                    /* ========== ATUALIZA O VINCULO DO PROPONENTE ========== */
                    if (count($buscarVisao) > 0 && count($idVinculo) > 0) :

                                    $whereVinculo = array('idVinculo = ?' => $idVinculo[0]->idVinculo);

                    $dadosVinculo = array(
                                            'idAgenteProponente' => $idNovoProponente
                                            ,'dtVinculo'         => new Zend_Db_Expr('GETDATE()'));

                    $tbVinculo->alterar($dadosVinculo, $whereVinculo); else :
                                    parent::message("O usu�rio informado n�o � Proponente ou o Projeto n�o est� vinculado a uma Proposta!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
                    endif;

                    /**
                     * ==============================================================
                     * FIM DA ATUALIZACAO DO VINCULO DO PROPONENTE
                     * ==============================================================
                     */
                } elseif ($tpAlt == 3) {
                    //FICHA T�CNICA
                    $fichatecAtual = FichaTecnicaDAO::buscarFichaTecnicaFinal($idPronac, $idPedidoAlt);
                    $Atual = $fichatecAtual[0]->FichaTecnica;
                    $idPreProjeto = $fichatecAtual[0]->idPreProjeto;

                    $fichatecSolicitada = PedidoAlteracaoDAO::buscarAlteracaoFichaTecnicaFinal($idPronac, $idPedidoAlt);
                    $Solicitada = $fichatecSolicitada[0]['dsFichaTecnica'];

                    $avaliacao = ReadequacaoProjetos::finalizacaoCoordAcomp("SAC.dbo.PreProjeto", "FichaTecnica", $Solicitada, "idPreProjeto", $idPreProjeto);
                    $result = $db->fetchAll($avaliacao);
                } elseif ($tpAlt == 4) {
                    //LOCAL DE REALIZA��O
                    $local = ProjetoDAO::buscarPronac($idPronac);
                    $idProjeto = $local['idProjeto'];

                    $dadosTbAbran = tbAbrangenciaDAO::buscarDadosTbAbrangencia(null, $id);

                    foreach ($dadosTbAbran as $x):
                            if (trim($x->tpAcao) == 'I') {
                                $dados = array(
                                    'idProjeto'         => $idProjeto,
                                    'idPais'            => $x->idPais,
                                    'idUF'              => $x->idUF,
                                    'idMunicipioIBGE'   => $x->idMunicipioIBGE,
                                    'Usuario'           => $idagente,
                                    'stAbrangencia'     => '1'
                                );

                                //if (count(AbrangenciaDAO::verificarLocalRealizacao($idProjeto, $x->idMunicipioIBGE)) <= 0) :
                                $local = $tbAbrangencia->cadastrar($dados);
                                //endif;
                                //print_r($local);
                            } elseif (trim($x->tpAcao) == 'E') {
                                // altera o status dos locais exclu�dos
                                $Abrangencia = new Proposta_Model_DbTable_Abrangencia();
                                $Abrangencia->update(array('stAbrangencia' => 0), array('idAbrangencia = ?' => $x->idAbrangenciaAntiga));
                                //$_local = AbrangenciaDAO::buscarAbrangenciasAtuais($idProjeto, $x->idPais, $x->idUF, $x->idMunicipioIBGE);
                                //$__local = AbrangenciaDAO::excluir($_local[0]->idAbrangencia);
                            }
                    endforeach;
                } elseif ($tpAlt == 5 && isset($_GET['checklist'])) {
                    //NOME DO PROJETO
                    $Projetos = new Projetos();
                    $DadosAlteracaoNomeProjeto = PedidoAlteracaoDAO::buscarAlteracaoNomeProjeto($idPronac);
                    $dados = array(
                                'NomeProjeto' => $DadosAlteracaoNomeProjeto['nmProjeto']
                            );
                    $Projetos->alterar($dados, array('IdPRONAC = ?' => $idPronac));
                } elseif ($tpAlt == 6) {

                        //PROPOSTA PEDAG�GICA
                    $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlpropostafinalizar", $idPronac);
                    $dadosSolicitado = $db->fetchAll($sqlproposta);

                    $Projeto = new Projetos();
                    $DadosProj = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac));

                    if (count($DadosProj) > 0 && !empty($DadosProj[0]->idProjeto)) {
                        $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                        $dados = array(
                                'EstrategiadeExecucao' => $dadosSolicitado[0]['dsEstrategiaExecucao'],
                                'EspecificacaoTecnica' => $dadosSolicitado[0]['dsEspecificacaoSolicitacao']
                            );
                        PreProjeto::alterarDados($dados, array('idPreProjeto = ?' => $DadosProj[0]->idProjeto));
                    }
                } elseif ($tpAlt == 7) {
                    $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
                    $produtosAnalisadosDeferidos = $tbPlanoDistribuicao->produtosAvaliadosReadequacao($idPedidoAlt, $id);

                    foreach ($produtosAnalisadosDeferidos as $valores) {
                        $Projeto = new Projetos();
                        $DadosProj = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac));

                        $dadosProduto = array(
                                    'idPlanoDistribuicao'           => $valores->idPlanoDistribuicao
                                    ,'idProjeto'                    => $DadosProj[0]->idProjeto
                                    ,'idProduto'                    => $valores->idProduto
                                    ,'Area'                         => $valores->cdArea
                                    ,'Segmento'                     => $valores->cdSegmento
                                    ,'idPosicaoDaLogo'              => $valores->idPosicaoLogo
                                    ,'QtdeProduzida'                => $valores->qtProduzida
                                    ,'QtdePatrocinador'             => $valores->qtPatrocinador
                                    ,'QtdeProponente'               => null
                                    ,'QtdeOutros'                   => $valores->qtOutros
                                    ,'QtdeVendaNormal'              => $valores->qtVendaNormal
                                    ,'QtdeVendaPromocional'         => $valores->qtVendaPromocional
                                    ,'PrecoUnitarioNormal'          => $valores->vlUnitarioNormal
                                    ,'PrecoUnitarioPromocional'     => $valores->vlUnitarioPromocional
                                    ,'stPrincipal'                  => $valores->stPrincipal
                                    ,'stPlanoDistribuicaoProduto'   => 1
                            );

                        //ALTERA OU INSERE O PLANO DE DISTRIBUICAO
                        $PlanoDistribuicao = new PlanoDistribuicao();
                        $x = $PlanoDistribuicao->salvar($dadosProduto);
                    }
                } elseif ($tpAlt == 8 && isset($_GET['checklist'])) {

                        //PRORROGACAO DE PRAZOS - CAPTACAO
                    $datas = PedidoAlteracaoDAO::buscarAlteracaoPrazoCaptacao($idPronac);
                    $Projeto = new Projetos();
                    $DadosProj = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac));
                    $Aprovacao = new Aprovacao();
                    $registro = $Aprovacao->buscar(array('AnoProjeto = ?' => $DadosProj[0]->AnoProjeto, 'Sequencial = ?' => $DadosProj[0]->Sequencial ));
                    $dados = array(
                            'IdPRONAC' => $idPronac,
                            'AnoProjeto' => $DadosProj[0]->AnoProjeto,
                            'Sequencial' => $DadosProj[0]->Sequencial,
                            'TipoAprovacao' => 3,
                            'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                            // 'ResumoAprovacao' => 'Solicita��o de Readequa��o',
                            'DtInicioCaptacao' => $datas['dtInicioNovoPrazo'],
                            'DtFimCaptacao' => $datas['dtFimNovoPrazo'],
                            'Logon' => $idagente
                        );
                    $Aprovacao->inserir($dados);
                } elseif ($tpAlt == 9 && isset($_GET['checklist'])) {
                    //PRORROGACAO DE PRAZOS - EXECUCAO
                    $datas = PedidoAlteracaoDAO::buscarAlteracaoPrazoExecucao($idPronac);
                    $projetos = new Projetos();
                    $dados = array(
                                'DtInicioExecucao' => $datas['dtInicioNovoPrazo'],
                                'DtFimExecucao' => $datas['dtFimNovoPrazo']
                            );
                    $projetos->alterar($dados, array('IdPRONAC = ?' => $idPronac));
                } elseif ($tpAlt == 10) {
                    $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
                    $produtosAnalisadosDeferidos = $tbPlanoDistribuicao->produtosAvaliadosReadequacao($idPedidoAlt, $id);

                    foreach ($produtosAnalisadosDeferidos as $valores) {
                        $Projeto = new Projetos();
                        $DadosProj = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac));
                        $dadosProduto = array(
                                    'idPlanoDistribuicao'           => $valores->idPlanoDistribuicao
                                    ,'idProjeto'                    => $DadosProj[0]->idProjeto
                                    ,'idProduto'                    => $valores->idProduto
                                    ,'Area'                         => $valores->cdArea
                                    ,'Segmento'                     => $valores->cdSegmento
                                    ,'idPosicaoDaLogo'              => $valores->idPosicaoLogo
                                    ,'QtdeProduzida'                => $valores->qtProduzida
                                    ,'QtdePatrocinador'             => $valores->qtPatrocinador
                                    ,'QtdeProponente'               => null
                                    ,'QtdeOutros'                   => $valores->qtOutros
                                    ,'QtdeVendaNormal'              => $valores->qtVendaNormal
                                    ,'QtdeVendaPromocional'         => $valores->qtVendaPromocional
                                    ,'PrecoUnitarioNormal'          => $valores->vlUnitarioNormal
                                    ,'PrecoUnitarioPromocional'     => $valores->vlUnitarioPromocional
                                    ,'stPrincipal'                  => $valores->stPrincipal
                                    ,'stPlanoDistribuicaoProduto'   => 1
                            );
                        //ALTERA OU INSERE O PLANO DE DISTRIBUICAO
                        $PlanoDistribuicao = new PlanoDistribuicao();
                        $x = $PlanoDistribuicao->salvar($dadosProduto);
                    }


                    // PRODUTO + ITEN DE CUSTO
                    $planilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                    $planilhaProjeto  = new PlanilhaProjeto();
                    $DeParaPlanilhaAprovacao = new DeParaPlanilhaAprovacao();
                    $Projetos = new Projetos();
                    $planilha = new PlanilhaAprovacao();
                    $PlanilhasSolicitadas = $planilha->buscar(array('IdPRONAC = ?' => $idPronac, 'tpPlanilha = ?' => 'PA'));
                    $buscarProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac));

                    foreach ($PlanilhasSolicitadas as $dadosP) {
                        if (!empty($dadosP->idPedidoAlteracao)) {
                            // busca a a��o a ser executada conforme solicita��o de readequa��o
                            //$_idPlanilhaProjeto      = empty($dadosP->idPlanilhaProjeto) ? ('idPlanilhaProjeto ? ' => new Zend_Db_Expr('IS NULL')) : ('idPlanilhaProjeto = ? ' => $dadosP->idPlanilhaProjeto);
                            //$_idPlanilhaProposta     = empty($dadosP->idPlanilhaProposta) ? ('idPlanilhaProposta ? ' => new Zend_Db_Expr('IS NULL')) : ('idPlanilhaProposta = ? ' => $dadosP->idPlanilhaProposta);
                            //$_idPlanilhaAprovacaoPai = empty($dadosP->idPlanilhaAprovacaoPai) ? ('idPlanilhaAprovacaoPai ? ' => new Zend_Db_Expr('IS NULL')) : ('idPlanilhaAprovacaoPai = ? ' => $dadosP->idPlanilhaAprovacaoPai);
                            $_dados = array('IdPRONAC = ?' => $idPronac
                                            , 'tpPlanilha = ?' => 'SR'
                                            , 'idPedidoAlteracao = ? ' => $dadosP->idPedidoAlteracao);

                            $buscarTpAcaoSR = $planilha->buscar($_dados);

                            if (count($buscarTpAcaoSR) > 0 && !empty($buscarProjeto[0]->idProjeto)) {
                                // EXCLUS�O
                                if ($buscarTpAcaoSR[0]->tpAcao == 'E') :
                                                                                    // planilha antiga
                                                $idProjeto = $buscarProjeto[0]->idProjeto;
                                $dadosAprovados = $planilhaProposta->buscar(array('idProjeto = ?' => $idProjeto, 'idProduto = ?' => $dadosP->idProduto, 'idEtapa = ?' => $dadosP->idEtapa, 'idPlanilhaItem = ?' => $dadosP->idPlanilhaItem));
                                foreach ($dadosAprovados as $dadosExculsao) :
                                                    $buscarDeParaPlanilhaAprovacao = $DeParaPlanilhaAprovacao->buscarPlanilhaProposta($dadosExculsao->idPlanilhaProposta);
                                foreach ($buscarDeParaPlanilhaAprovacao as $b) :
                                                            $DeParaPlanilhaAprovacao->delete(array('idPlanilhaAprovacao = ?' => $b->idPlanilhaAprovacao));
                                endforeach;
                                $planilha->delete(array('idPlanilhaProposta = ?' => $dadosExculsao->idPlanilhaProposta));
                                $planilhaProjeto->delete(array('idPlanilhaProposta = ?' => $dadosExculsao->idPlanilhaProposta));
                                $planilhaProposta->delete(array('idPlanilhaProposta = ?' => $dadosExculsao->idPlanilhaProposta));
                                endforeach;

                                // ALTERA��O
                                elseif ($buscarTpAcaoSR[0]->tpAcao == 'A') :
                                                                                    // planilha antiga
                                                $idProjeto = $buscarProjeto[0]->idProjeto;
                                $dadosAprovados = $planilhaProposta->buscar(array('idProjeto = ?' => $idProjeto, 'idProduto = ?' => $dadosP->idProduto, 'idEtapa = ?' => $dadosP->idEtapa, 'idPlanilhaItem = ?' => $dadosP->idPlanilhaItem));
                                foreach ($dadosAprovados as $dadosAlteracao) :
                                                    $where = array('idPlanilhaProposta = ?' => $dadosAlteracao->idPlanilhaProposta);
                                $dados = array(
                                                            'idProduto' => $dadosP->idProduto,
                                                            'idEtapa' => $dadosP->idEtapa,
                                                            'idPlanilhaItem' => $dadosP->idPlanilhaItem,
                                                            'Descricao' => $dadosP->dsItem,
                                                            'Unidade' => $dadosP->idUnidade,
                                                            'Quantidade' => $dadosP->qtItem,
                                                            'Ocorrencia' => $dadosP->nrOcorrencia,
                                                            'ValorUnitario' => $dadosP->vlUnitario,
                                                            'QtdeDias' => $dadosP->qtDias,
                                                            'TipoDespesa' => $dadosP->tpDespesa,
                                                            'TipoPessoa' => $dadosP->tpPessoa,
                                                            'Contrapartida' => $dadosP->nrContraPartida,
                                                            'FonteRecurso' => $dadosP->nrFonteRecurso,
                                                            'UfDespesa' => $dadosP->idUFDespesa,
                                                            'MunicipioDespesa' => $dadosP->idMunicipioDespesa,
                                                            'idUsuario' => $dadosP->idAgente,
                                                            'dsJustificativa' => $dadosP->dsJustificativa
                                                        );
                                $planilhaProposta->alterar($dados, $where);
                                endforeach;

                                $planilha->update(array('tpPlanilha' => 'CO' , 'stAtivo' => 'N'), array('idPlanilhaAprovacao = ? ' => $dadosP->idPlanilhaAprovacao));
                                $planilha->update(array('tpPlanilha' => 'CO' , 'stAtivo' => 'N'), array('idPlanilhaAprovacao = ? ' => $buscarTpAcaoSR[0]->idPlanilhaAprovacao));
                                // INCLUS�O
                                elseif ($buscarTpAcaoSR[0]->tpAcao == 'I') :
                                                                                    // planilha antiga
                                                $ReplicaDados = array(
                                                    'idProjeto' => $buscarProjeto[0]->idProjeto,
                                                    'idProduto' => $dadosP->idProduto,
                                                    'idEtapa' => $dadosP->idEtapa,
                                                    'idPlanilhaItem' => $dadosP->idPlanilhaItem,
                                                    'Descricao' => $dadosP->dsItem,
                                                    'Unidade' => $dadosP->idUnidade,
                                                    'Quantidade' => $dadosP->qtItem,
                                                    'Ocorrencia' => $dadosP->nrOcorrencia,
                                                    'ValorUnitario' => $dadosP->vlUnitario,
                                                    'QtdeDias' => $dadosP->qtDias,
                                                    'TipoDespesa' => $dadosP->tpDespesa,
                                                    'TipoPessoa' => $dadosP->tpPessoa,
                                                    'Contrapartida' => $dadosP->nrContraPartida,
                                                    'FonteRecurso' => $dadosP->nrFonteRecurso,
                                                    'UfDespesa' => $dadosP->idUFDespesa,
                                                    'MunicipioDespesa' => $dadosP->idMunicipioDespesa,
                                                    'idUsuario' => $dadosP->idAgente,
                                                    'dsJustificativa' => $dadosP->dsJustificativa
                                                );
                                $planilhaProposta->inserir($ReplicaDados);

                                $planilha->update(array('tpPlanilha' => 'CO', 'stAtivo' => 'N'), array('idPlanilhaAprovacao = ? ' => $dadosP->idPlanilhaAprovacao));
                                $planilha->update(array('tpPlanilha' => 'CO' , 'stAtivo' => 'N'), array('idPlanilhaAprovacao = ? ' => $buscarTpAcaoSR[0]->idPlanilhaAprovacao));
                                endif;
                            }
                        } // fecha if
                    }
                }
            }

            $db->commit();

            //CASO SEJA O �LTIMO ITEM DO PEDIDO DE ALTERA��O, FINALIZA O STATUS DA MESMA
            $tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $verificarPedidosAtivos = $tbPedidoAlteracaoXTipoAlteracao->buscar(array('idPedidoAlteracao = ?' => $idPedidoAlt, 'stVerificacao <> ?' => 4));
            $arrBusca = array();
            $arrBusca['p.siVerificacao IN (?)'] = array('1');
            $arrBusca['p.IdPRONAC = ?'] = $idPronac;
            $arrBusca['x.tpAlteracaoProjeto IN (?)'] = array('1', '2', '5', '7', '8', '9', '10');
            $arrBusca['a.stAvaliacaoItemPedidoAlteracao IN (?)'] = array('AP');
            $arrBusca['c.stVerificacao NOT IN (?)'] = array('4');

            $buscaChecklist = $tbPedidoAlteracaoXTipoAlteracao->buscarPedidoChecklist($arrBusca);
            if (count($verificarPedidosAtivos) == 0 && count($buscaChecklist) == 0) :
                                            $tbPedidoAlteracaoProjeto = new tbPedidoAlteracaoProjeto();
            $tbPedidoAlteracaoProjeto->alterar(array('siVerificacao' => 2), array('idPedidoAlteracao = ?' => $idPedidoAlt));
            endif;

            if (isset($_GET['checklist'])) {
                parent::message("Portaria publicada com sucesso!", "publicacaodou/index", "CONFIRM");
            } else {
                parent::message("Projeto finalizado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
            }
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro na devolu&ccedil;&atilde;o da solicita&ccedil;&atilde;o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
        }
    }


    /**
     * Metodo responsavel por enviar um Projeto para um Componente da Comissao
     * @param void
     * @return void
     */
    public function encaminhacomponentecomissaoAction()
    {
        // recebe os dados via get
            $idPronac_Get = $this->_request->getParam("idpronac"); // pega o id do pronac via get
            $idAcao       = $this->_request->getParam("idacao"); // pega o idAcaoAvaliacaoPedidoAlteracao via get

            $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            // ATUALIZA OS CAMPOS stAtivo e stVerificacao NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfin = ReadequacaoProjetos::retornaSQLfinalizaGeral($idAcao);
            $dados  = $db->fetchAll($sqlfin);

            // BUSCA OS REGISTROS DA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfin2 = ReadequacaoProjetos::retornaSQLfinalizaGeral2($idAcao);
            $dados   = $db->fetchAll($sqlfin2);
            $id      = $dados[0]->idAvaliacaoItemPedidoAlteracao;
            $idOrgao = $dados[0]->idOrgao;

            // pega a justificativa final e o id do Parecerista
            $sqlJustProp       = ReadequacaoProjetos::buscarJustificativaFinalParecerista($id);
            $dados             = $db->fetchAll($sqlJustProp);
            $dsObservacao      = $dados[0]->dsObservacao;
            $idAgenteRemetente = $dados[0]->idAgenteRemetente;

            // BUSCA OS REGISTROS DOS CAMPOS idPedidoAlteracao E tpAlteracaoProjeto DA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlfin3            = ReadequacaoProjetos::retornaSQLfinalizaGeral3($id);
            $dados              = $db->fetchAll($sqlfin3);
            $idPedidoAlt        = $dados[0]->idPedidoAlteracao;
            $tpAlt              = $dados[0]->tpAlteracaoProjeto;
            $stAvaliacaoItem    = $dados[0]->stAvaliacaoItemPedidoAlteracao;
            $idAgenteAvaliador  = $dados[0]->idAgenteAvaliador;
            $stParecerFavoravel = (trim($stAvaliacaoItem) == 'AP') ? 1 : 2; // 1 => favor�vel; 2 => desfavor�vel

            // ATUALIZA O CAMPO stVerificacao NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlfin4 = ReadequacaoProjetos::retornaSQLfinalizaGeral4($idPedidoAlt, $tpAlt);
            $dados   = $db->fetchAll($sqlfin4);

            // CRIAR NOVO REGISTRO DE ENCAMINHAMENTO NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfin5 = ReadequacaoProjetos::retornaSQLfinalizaGeral5($id, $idOrgao, $this->getIdUsuario, 118);
            $dados   = $db->fetchAll($sqlfin5);

            // BUSCA O IDPRONAC DA TABELA tbPedidoAlteracaoProjeto
            $sqlfin6  = ReadequacaoProjetos::retornaSQLfinalizaGeral6($idPedidoAlt);
            $dados    = $db->fetchAll($sqlfin6);
            $idPronac = $dados[0]->IdPRONAC;

            // copia as tabelas
            $planilhaProjeto      = new PlanilhaProjeto();
            $planilhaAprovacao    = new PlanilhaAprovacao();
            $analiseConteudo      = new Parecer_Model_DbTable_TbAnaliseDeConteudo();
            $analiseaprovacao     = new AnaliseAprovacao();
            $projetos             = new Projetos();
            $Distribuicao         = new DistribuicaoProjetoComissao();
            $titulacaoConselheiro = new TitulacaoConselheiro();
            $Rplanilhaprojeto = $planilhaAprovacao->buscar(array('idPRONAC = ?'=> $idPronac_Get, 'tpPlanilha = ?'=> 'PA', 'stAtivo = ?' => 'N'));
            foreach ($Rplanilhaprojeto as $resu) {
                $data = array(
                        'tpPlanilha'             => 'CO'
                        ,'dtPlanilha'            => new Zend_Db_Expr('GETDATE()')
                        ,'idPlanilhaProjeto'     => $resu->idPlanilhaProjeto
                        ,'idPlanilhaProposta'    => $resu->idPlanilhaProposta
                        ,'IdPRONAC'              => $resu->IdPRONAC
                        ,'idProduto'             => $resu->idProduto
                        ,'idEtapa'               => $resu->idEtapa
                        ,'idPlanilhaItem'        => $resu->idPlanilhaItem
                        ,'dsItem'                => ''
                        ,'idUnidade'             => $resu->idUnidade
                        ,'qtItem'                => $resu->qtItem
                        ,'nrOcorrencia'          => $resu->nrOcorrencia
                        ,'vlUnitario'            => $resu->vlUnitario
                        ,'qtDias'                => $resu->qtDias
                        ,'tpDespesa'             => $resu->tpDespesa
                        ,'tpPessoa'              => $resu->tpPessoa
                        ,'nrContraPartida'       => $resu->nrContraPartida
                        ,'nrFonteRecurso'        => $resu->nrFonteRecurso
                        ,'idUFDespesa'           => $resu->idUFDespesa
                        ,'idMunicipioDespesa'    => $resu->idMunicipioDespesa
                        ,'idPlanilhaAprovacaoPai' => $resu->idPlanilhaAprovacao
                        ,'idPedidoAlteracao'      => $idPedidoAlt
                        ,'dsJustificativa'       => null
                        ,'stAtivo'               => 'N');
                $inserirPlanilhaAprovacao = $planilhaAprovacao->InserirPlanilhaAprovacao($data);
            }

            // chama a fun��o para fazer o balanceamento
            $areaProjeto = $projetos->BuscarAreaSegmentoProjetos($idPronac_Get);
            $Rtitulacao  = $titulacaoConselheiro->buscarComponenteBalanceamento($areaProjeto['area']);
            $Distribuicao->alterar(array('stDistribuicao' => 'I'), array('idPRONAC = ?'=>$idPronac_Get));
            $dados = array(
                        'idPRONAC'        => $idPronac_Get
                        ,'idAgente'       => $Rtitulacao[0]['idAgente']
                        ,'dtDistribuicao' => new Zend_Db_Expr('GETDATE()')
                        ,'stDistribuicao' => 'A'
                        ,'idResponsavel'  => 0);
            $Distribuicao->inserir($dados);

            // chama a fun��o para alterar a situa��o do projeto - Padr�o C10
            $data  = array('Situacao' => 'C10');
            $where = "IdPRONAC = $idPronac_Get";
            $projetos->alterarProjetos($data, $where);

            // busca a planilha PA
            $arrWhereSomaPlanilhaPA                         = array();
            $arrWhereSomaPlanilhaPA['idPronac = ?']         = $idPronac_Get;
            //$arrWhereSomaPlanilhaPA['idPlanilhaItem <> ?']  = '206'; //elaboracao e agenciamento
            //$arrWhereSomaPlanilhaPA['NrFonteRecurso = ?']   = '109';
            $arrWhereSomaPlanilhaPA['stAtivo = ?']          = 'N';
            $arrWhereSomaPlanilhaPA['tpPlanilha = ?']       = 'PA';
            $somaPA                                         = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilhaPA);

            // busca a planilha CO
            $arrWhereSomaPlanilhaCO                         = array();
            $arrWhereSomaPlanilhaCO['idPronac = ?']         = $idPronac_Get;
            //$arrWhereSomaPlanilhaCO['idPlanilhaItem <> ?']  = '206'; //elaboracao e agenciamento
            //$arrWhereSomaPlanilhaCO['NrFonteRecurso = ?']   = '109';
            $arrWhereSomaPlanilhaCO['stAtivo = ?']          = 'S';
            $arrWhereSomaPlanilhaCO['tpPlanilha = ?']       = 'CO';
            $somaCO                                         = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilhaCO);

            // define o tipo de parecer (tipo 2 => complementa��o; tipo 4 => redu��o)
            $tipoParecer = 2;
            if ($somaPA < $somaCO) :
                        $tipoParecer = 4;
            endif;

            // cadastra na tabela parecer
            $tbParecer       = new Parecer_Model_DbTable_Parecer();
            $buscarPareceres = $tbParecer->buscar(array('IdPRONAC = ?' => $idPronac_Get), array('DtParecer DESC')); // busca os pareceres do Projeto
                foreach ($buscarPareceres as $p) : // desabilita os pareceres antigos
                        $idparecer = isset($p->IdParecer) ? $p->IdParecer : $p->idParecer;
            $tbParecer->alterar(array('stAtivo' => 0), array('idParecer = ?' => $idparecer));
            endforeach;
            $dadosParecer = array(
                        'IdPRONAC'             => $buscarPareceres[0]->IdPRONAC
                        ,'idEnquadramento'     => $buscarPareceres[0]->idEnquadramento
                        ,'AnoProjeto'          => $buscarPareceres[0]->AnoProjeto
                        ,'Sequencial'          => $buscarPareceres[0]->Sequencial
                        ,'TipoParecer'         => $tipoParecer
                        ,'ParecerFavoravel'    => $stParecerFavoravel
                        ,'DtParecer'           => new Zend_Db_Expr('GETDATE()')
                        ,'Parecerista'         => $idAgenteRemetente
                        ,'Conselheiro'         => null
                        ,'NumeroReuniao'       => null
                        ,'ResumoParecer'       => $dsObservacao
                        ,'SugeridoUfir'        => 0
                        ,'SugeridoReal'        => $somaPA['soma']
                        ,'SugeridoCusteioReal' => 0
                        ,'SugeridoCapitalReal' => 0
                        ,'Atendimento'         => $buscarPareceres[0]->Atendimento
                        ,'Logon'               => $this->getIdUsuario
                        ,'stAtivo'             => 1
                        ,'idTipoAgente'        => 1);
            $tbParecer->inserir($dadosParecer);

            $db->commit();

            parent::message("Projeto finalizado com sucesso!", "manterreadequacao?tipoFiltro=7:d", "CONFIRM");
        } // fecha try

        catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro na devolu&ccedil;&atilde;o da solicita&ccedil;&atilde;o", "manterreadequacao?tipoFiltro=7:d", "ERROR");
        }
    } // fecha m�todo encaminhacomponentecomissaoAction()



    /**************************************************************************************************************************
    * Fun��o para encaminhar projeto - Coordenador de Acompanhamento
    * ************************************************************************************************************************/
    public function encaminhacoordacompanhamentoAction()
    {

        //retorna o id do agente logado
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idAgenteEncaminhar = $agente['idAgente'];

        //echo "<pre>"; print_r($_POST); $this->_helper->viewRenderer->setNoRender(TRUE);
        $idAgenteReceber = $_POST['AgenteId'];
        $Orgao = $_POST['passaValor'];
        $AgentePerfil = $_POST['AgentePerfil'];
        $PRONAC = $_POST['PRONAC'];
        $NomeProjeto = $_POST['NomeProjeto'];
        $ID_PRONAC = $_POST['ID_PRONAC'];
        $idPedidoAlteracao = $_POST['idPedidoAlteracao'];
        $tpAlteracaoProjeto = $_POST['tpAlteracaoProjeto'];
        $justificativa = $_POST['justificativa'];
        if ($justificativa == 'Digite a justificativa...') {
            $justificativa = '';
        }

        $idAgenteRemetente = $this->getIdUsuario;
        $idPerfilRemetente = $this->codGrupo;

        xd($_POST);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            //ALTERA O STATUS DE '0' PARA '1' NA TABELA tbPedidoAlteracaoProjeto
            $sqlAlteraVariavelAltProj = ReadequacaoProjetos::retornaSQLencaminhar("sqlAlteraVariavelAltProj", $ID_PRONAC, $idPedidoAlteracao, $tpAlteracaoProjeto, $justificativa, $Orgao, $idAgenteReceber);
            $db->fetchAll($sqlAlteraVariavelAltProj);

            //ALTERA O STATUS DE '0' PARA '1' NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlAlteraVariavelTipoAlt = ReadequacaoProjetos::retornaSQLencaminhar("sqlAlteraVariavelTipoAlt", $ID_PRONAC, $idPedidoAlteracao, $tpAlteracaoProjeto, $justificativa, $Orgao, $idAgenteReceber);
            $db->fetchAll($sqlAlteraVariavelTipoAlt);
            if ($tpAlteracaoProjeto == 7) {
                $sqlAlteraVariavelTipoAlt = ReadequacaoProjetos::retornaSQLencaminhar("sqlAlteraVariavelTipoAlt", $ID_PRONAC, $idPedidoAlteracao, 10, $justificativa, $Orgao, $idAgenteReceber);
                $db->fetchAll($sqlAlteraVariavelTipoAlt);
            }

            // INSERE OS VALORES NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlEncaminhar = ReadequacaoProjetos::retornaSQLencaminhar("sqlCoordAcompEncaminhar", $ID_PRONAC, $idPedidoAlteracao, $tpAlteracaoProjeto, $justificativa, $Orgao, $idAgenteReceber);
            $db->fetchAll($sqlEncaminhar);

            //RETORNA EM VARI�VEIS OS DADOS DO LOG ANTERIOR PARA INSERIR NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlproposta = ReadequacaoProjetos::retornaSQLencaminhar("sqlRecuperarRegistro", $ID_PRONAC, $idPedidoAlteracao, $tpAlteracaoProjeto, $justificativa, $Orgao, $idAgenteReceber);
            $dados = $db->fetchAll($sqlproposta);
            $idAvaliacaoItemPedidoAlteracao = $dados[0]->idAvaliacaoItemPedidoAlteracao;


            //122 = Coord Acompanhamento
            //93 = Coord Parecerista
            //94 = Parecerista
            //129 = Tecnico

            if ($AgentePerfil == 122) {
                $tipoAg = '3';
            } elseif ($AgentePerfil == 93) {
                $tipoAg = '2';
            } elseif ($AgentePerfil == 94) {
                $tipoAg = '1';
            } elseif ($AgentePerfil == 121 or $AgentePerfil == 129) {
                $tipoAg = '5';
            }

            // INSERE OS VALORES NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlEncaminhar2 = ReadequacaoProjetos::retornaSQLtbAcao($idAvaliacaoItemPedidoAlteracao, $justificativa, $tipoAg, $Orgao, $idAgenteReceber, $idAgenteRemetente, $idPerfilRemetente);
            $db->fetchAll($sqlEncaminhar2);

            $db->commit();
            parent::message("Projeto encaminhado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao encaminhar Projeto", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
        }
    }

    /**************************************************************************************************************************
    * Fun��o para Reencaminhar projeto - Coordenador de Acompanhamento
    * ************************************************************************************************************************/
    public function reencaminhacoordacompanhamentoAction()
    {
        $idAcaoAtual = $_POST['idAcao'];
        $idPedidoAlteracao = $_POST['idPedidoAlteracao'];
        $tpAlteracaoProjeto = $_POST['tpAlteracaoProjeto'];
        $justificativa = $_POST['justificativa'];
        $idAgente = $_POST['AgenteId'];
        $Orgao = $_POST['Orgao'];
        $AgentePerfil = $_POST['AgentePerfil'];

        if ($_POST['AgentePerfil'] == '121' || $_POST['AgentePerfil'] == '129') {
            $idPerfil = 5;
        } else {
            $idPerfil = 2;
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            //ALTERA OS DADOS DO REGISTRO NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlAlteraVar = ReadequacaoProjetos::retornaSQLReencaminharPar($idPedidoAlteracao, $tpAlteracaoProjeto);
            $db->fetchAll($sqlAlteraVar);

            //INSERE UM NOVO REGISTRO NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlAlteraVariavel = ReadequacaoProjetos::reencaminharPar($idPedidoAlteracao, $tpAlteracaoProjeto);
            $db->fetchAll($sqlAlteraVariavel);

            //ATUALIZA O CAMPO stAtivo ATUAL NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlAlteraVariavel1 = ReadequacaoProjetos::reencaminharPar1($idAcaoAtual);
            $db->fetchAll($sqlAlteraVariavel1);

            //RETORNA O idAvaliacaoItemPedidoAlteracao DO REGISTRO GERADO NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlAlteraVariavel2 = ReadequacaoProjetos::reencaminharPar2($idPedidoAlteracao, $tpAlteracaoProjeto);
            $dados = $db->fetchAll($sqlAlteraVariavel2);
            $idAcao = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //INSERE NOVO REGISTRO
            $sqlAlteraVariavel3 = ReadequacaoProjetos::reencaminharPar5($idAcao, $this->getIdUsuario, $justificativa, $Orgao, $idPerfil, $idAgente, $_POST['AgentePerfil']);
            $db->fetchAll($sqlAlteraVariavel3);

            $db->commit();
            parent::message("Projeto reencaminhado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao reencaminhar Projeto", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
        }
    }



    /**************************************************************************************************************************
    * Fun��o para encaminhar projeto - Coordenador de Parecerista
    * ************************************************************************************************************************/
    public function encaminhacoordpareceristaAction()
    {

                //retorna o id do agente logado
        $idAgenteRemetente = $this->getIdUsuario;
        $idPerfilRemetente = $this->codGrupo;

        $idAvaliacaoItemPedidoAlteracao = $_POST['idAvaliacaoItemPedidoAlteracao'];
        $idAcao = $_POST['idAcao'];
        $stAcao = $_POST['stAcao'];
        $justificativa = $_POST['justificativa'];
        $agenteNovo = $_POST['agenteNovo'];
        $Orgao = $_POST['Orgao'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            //ALTERA OS DADOS DO LOG ATUAL ANTES DE CRIAR OUTRO REGISTRO
            $sqlAlteraVariavel = ReadequacaoProjetos::retornaSQLencaminharParecerista("sqlAlteraVariavel", $idAvaliacaoItemPedidoAlteracao, $idAcao, $stAcao, $justificativa, $agenteNovo, $Orgao);
            $db->fetchAll($sqlAlteraVariavel);

            //INSERE NOVO REGISTRO
            $sqlEncaminhar = ReadequacaoProjetos::retornaSQLencaminharParecerista("sqlCoordPareceristaEncaminhar", $idAvaliacaoItemPedidoAlteracao, $idAcao, $stAcao, $justificativa, $agenteNovo, $Orgao, $idAgenteRemetente, $idPerfilRemetente);
            $db->fetchAll($sqlEncaminhar);

            $db->commit();
            parent::message("Projeto encaminhado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordparecerista", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao encaminhar Projeto", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordparecerista", "ERROR");
        }
    }

    public function reencaminhacoordpareceristaAction()
    {

// 		$agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        //		$this->view->agente = $agente['idAgente'];
        $idAcao = $_POST['idAcao'];
        $idPedidoAlteracao = $_POST['idPedidoAlteracao'];
        $tpAlteracaoProjeto = $_POST['tpAlteracaoProjeto'];
        $justificativa = $_POST['justificativa'];
        $idOrgao = $_POST['idOrgao'];
        $idAgente = $_POST['agenteNovo'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            //ALTERA OS DADOS DO REGISTRO NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlAlteraVar = ReadequacaoProjetos::retornaSQLReencaminharPar($idPedidoAlteracao, $tpAlteracaoProjeto);
            $dados = $db->fetchAll($sqlAlteraVar);

            //INSERE UM NOVO REGISTRO NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlAlteraVariavel = ReadequacaoProjetos::reencaminharPar($idPedidoAlteracao, $tpAlteracaoProjeto);
            $dados = $db->fetchAll($sqlAlteraVariavel);

            //ATUALIZA O CAMPO stAtivo ATUAL NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlAlteraVariavel1 = ReadequacaoProjetos::reencaminharPar1($idAcao);
            $dados = $db->fetchAll($sqlAlteraVariavel1);

            //RETORNA O idAvaliacaoItemPedidoAlteracao DO REGISTRO GERADO NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlAlteraVariavel2 = ReadequacaoProjetos::reencaminharPar2($idPedidoAlteracao, $tpAlteracaoProjeto);
            $dados = $db->fetchAll($sqlAlteraVariavel2);
            $idAvaliacaoItemPedidoAlteracao = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //INSERE NOVO REGISTRO
            $sqlAlteraVariavel3 = ReadequacaoProjetos::reencaminharPar3($idAvaliacaoItemPedidoAlteracao, $idAgente, $justificativa, $idOrgao, $this->getIdUsuario, $this->codGrupo);
            $dados = $db->fetchAll($sqlAlteraVariavel3);

            $db->commit();
            parent::message("Projeto encaminhado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordparecerista", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao encaminhar Projeto", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordparecerista", "ERROR");
        }
    }

    /**************************************************************************************************************************
    * Fun��o que chama a fun��o de encaminha projeto para outro componente ou para a lista de balanceamento
    * ************************************************************************************************************************/

    public function encaminharprojetoAction()
    {
        $filter = new Zend_Filter_StripTags();

        //Tela de Dados
        $idPronac        = $filter->filter($this->_request->getPost('idPronac'));
        $justificativa        = $filter->filter($this->_request->getPost('justificativa'));
        $agenteAtual        = $filter->filter($this->_request->getPost('agenteAtual'));
        $agenteNovo        = $filter->filter($this->_request->getPost('agenteNovo'));
        $data        = $filter->filter($this->_request->getPost('data'));

        $dados = ProjetosGerenciarDAO::encaminharProjeto($idPronac, $data, $justificativa, $agenteAtual, $agenteNovo);

        if ($dados) {
            parent::message("O Projeto cultural foi encaminhado com sucesso!", "projetosgerenciar/projetosgerenciar", "CONFIRM");
        } else {
            parent::message("Erro ao encaminhar Projeto", "projetosgerenciar/projetosgerenciar", "ERROR");
        }
    }

    /**************************************************************************************************************************
     * Fun��o que desabilita o componente da comiss�o para receber projetos
     * e faz o rebalanceamento de todos os projetos do mesmo quando ativos
     * ************************************************************************************************************************/

    public function desabilitarcomponenteAction()
    {
        $filter = new Zend_Filter_StripTags();

        //Tela de Dados
        $justificativa        = $filter->filter($this->_request->getPost('justificativa'));
        $idAgente        = $filter->filter($this->_request->getPost('idAgente'));

        $dados = ProjetosGerenciarDAO::desativarComponente($idAgente, $justificativa);

        if ($dados) {
            parent::message("O Componente da Comiss�o foi desabilitado com sucesso!", "projetosgerenciar/projetosgerenciar", "CONFIRM");
        } else {
            parent::message("Erro ao desabilitar o Componente da Comiss�o", "projetosgerenciar/projetosgerenciar", "ERROR");
        }
    }


    /**************************************************************************************************************************
     * Fun��o que cria o select para escolha entre entidade vinculada ou t�cnico de acompanhamento
     * ************************************************************************************************************************/

    public function selectcoordacompAction()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL da lista de Entidades Vinculadas - T�cnicos
        $sqllistasDeEntidadesVinculadas = ReadequacaoProjetos::retornaSQLlista("listasDeEntidadesVinculadas", "NULL");
        $listaEntidades = $db->fetchAll($sqllistasDeEntidadesVinculadas);

        // Chama o SQL da lista dos Pareceristas
        $sqllistasDeEncaminhamento = ReadequacaoProjetos::retornaSQLlista("listasDeEncaminhamento", 203);
        $listaParecerista = $db->fetchAll($sqllistasDeEncaminhamento);

        // Chama o SQL da lista das Entidades - Parecerista
        $sqllistasDeEntidadesVinculadasPar = ReadequacaoProjetos::retornaSQLlista("listasDeEntidadesVinculadasPar", "NULL");
        $listaEntidadesTec = $db->fetchAll($sqllistasDeEntidadesVinculadasPar);


        $opcao = $_POST["opcao"];

        if ($opcao == 0) {
            echo "<option value='0'>&nbsp;</option>";
        }
        if ($opcao == 1) {
            foreach ($listaEntidades as $lista) {
                echo "<option value='$lista->Codigo'>".$lista->Sigla."</option>";
            }
        }
        if ($opcao == 2) {
            foreach ($listaParecerista as $lista) {
                echo "<option value='$lista->idAgente'>".$lista->Nome." - ".$lista->Perfil."</option>";
            }
        }
        if ($opcao == 3) {
            foreach ($listaEntidadesTec as $lista) {
                echo "<option value='$lista->idAgente'>".$lista->Nome." - ".$lista->Perfil."</option>";
            }
        }

        $this->_helper->viewRenderer->setNoRender(true);
    }


    /**************************************************************************************************************************
     * Fun��o que habilita o componente da comiss�o para receber projetos
     * ************************************************************************************************************************/

    public function habilitarcomponenteAction()
    {
        $filter = new Zend_Filter_StripTags();

        //Tela de Dados
        $justificativa        = $filter->filter($this->_request->getPost('justificativa'));
        $idAgente        = $filter->filter($this->_request->getPost('idAgente'));

        /*echo 'Agente = '.$idAgente.'<br /> Jus '.$justificativa;
        $this->_helper->viewRenderer->setNoRender(TRUE);*/


        $dados = ProjetosGerenciarDAO::ativarComponente($idAgente, $justificativa);


        if ($dados) {
            parent::message("O Componente da Comiss�o foi habilitado com sucesso!", "projetosgerenciar/projetosgerenciar", "CONFIRM");
        } else {
            parent::message("Erro ao habilitar o Componente da Comiss�o", "projetosgerenciar/projetosgerenciar", "ERROR");
        }
    }



    public function avaliaritemdecustoAction()
    {
        try {
            // recebe os dados do formul�rio
            $idPronac        = $_POST['idPRONAC'];
            $idPlano         = $_POST['idPlano'];
            $idProduto       = $_POST['idProduto'];
            $avaliacao       = $_POST['avaliacaoProduto'];
            $dsJustificativa = $_POST['justificativaPropRead'];


            // ========== IN�CIO PLANO DE DISTRIBUI��O ==========
            // busca o Plano de Distribui��o do Proponente
            $b = PlanoDistribuicaoDAO::buscar($idPlano);

            $dados = array(
                    'idPedidoAlteracao'     => $b[0]->idPedidoAlteracao
                    ,'idPlanoDistribuicao'  => $b[0]->idPlanoDistribuicao
                    ,'idProduto'            => $b[0]->idProduto
                    ,'cdArea'               => $b[0]->cdArea
                    ,'cdSegmento'           => $b[0]->cdSegmento
                    ,'idPosicaoLogo'        => $b[0]->idPosicaoLogo
                    ,'qtProduzida'          => $b[0]->qtProduzida
                    ,'qtPatrocinador'       => $b[0]->qtPatrocinador
                    ,'qtOutros'             => $b[0]->qtOutros
                    ,'qtVendaNormal'        => $b[0]->qtVendaNormal
                    ,'qtVendaPromocional'   => $b[0]->qtVendaPromocional
                    ,'vlUnitarioNormal'     => $b[0]->vlUnitarioNormal
                    ,'vlUnitarioPromocional'=> $b[0]->vlUnitarioPromocional
                    ,'stPrincipal'          => $b[0]->stPrincipal
                    ,'tpAcao'               => $b[0]->tpAcao
                    ,'tpPlanoDistribuicao'  => 'O'
                    ,'dtPlanoDistribuicao'  => new Zend_Db_Expr('GETDATE()')
                    ,'dsjustificativa'      => $b[0]->dsjustificativa
                );

            // faz a c�pia dos dados do proponente para o parecerista/t�cnico (tipifica��o)
            $cadastrar = PlanoDistribuicaoDAO::cadastrar($dados);

            // pega o �ltimo idPlano inserido (registro do parecerista/t�cnico com tipo = 'O')
            $ultimoIdPlano = PlanoDistribuicaoDAO::buscarUltimo();
            $ultimoIdPlano = $ultimoIdPlano[0]->id;

            // se for deferido, realiza a altera��o na tabela tbPlanoDistribuicao com tipifica��o 'O' (altera��o de dados do t�cnico)
            if ($avaliacao == "D") {
                $dados = array(
                        'cdArea'                 => $_POST['CodArea']
                        ,'cdSegmento'            => $_POST['CodSegmento']
                        ,'qtPatrocinador'        => $_POST['Patrocinador']
                        ,'qtProduzida'           => $_POST['Beneficiarios']
                        ,'qtOutros'              => $_POST['Divulgacao']
                        ,'qtVendaNormal'         => $_POST['NormalTV']
                        ,'qtVendaPromocional'    => $_POST['PromocionalTV']
                        ,'vlUnitarioNormal'      => $_POST['NormalPU']
                        ,'vlUnitarioPromocional' => $_POST['PromocionalPU']
                    );
                $alterar = PlanoDistribuicaoDAO::alterar($dados, $ultimoIdPlano);
            } // fecha if
            // ========== FIM PLANO DE DISTRIBUI��O ==========


            // ========== IN�CIO: cadastro de avalia��o do produto ==========
            $dados_produtos = array(
                    'idAvaliacaoItemPedidoAlteracao'     => $_POST['idAvaliacaoItemPedidoAlteracao']
                    ,'stAvaliacaoSubItemPedidoAlteracao' => $avaliacao
                    ,'dsAvaliacaoSubItemPedidoAlteracao' => $dsJustificativa);
            $cadastrar_avaliacao = AvaliacaoSubItemPedidoAlteracaoDAO::cadastrar($dados_produtos);

            // pega o �ltimo id inserido
            $ultimo = AvaliacaoSubItemPedidoAlteracaoDAO::buscarUltimo();
            $ultimo = $ultimo[0]->id;

            // vincula o plano de distribui��o
            $dados_plano_Distribuicao = array(
                    'idAvaliacaoItemPedidoAlteracao'     => $_POST['idAvaliacaoItemPedidoAlteracao']
                    ,'idAvaliacaoSubItemPedidoAlteracao' => $ultimo
                    ,'idPlano'                           => $ultimoIdPlano);
            $cadastrar_plano_distribuicao = AvaliacaoSubItemPlanoDistribuicaoDAO::cadastrar($dados_plano_Distribuicao);
            // ========== FIM: cadastro de avalia��o do produto ==========


            if (!$cadastrar_avaliacao) {
                throw new Exception("Erro ao tentar avaliar o Produto!");
            } else {
                parent::message("Solicita��o enviada com sucesso!", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=".$idPronac, "CONFIRM");
            }
        } // fecha try
        catch (Exception $e) {
            parent::message("Erro ao avaliar item de custo", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=".$idPronac, "ERROR");
        }
    } // fecha m�todo avaliaritemdecustoAction()



    public function finalizarprodutosAction()
    {
        $idPedidoAlteracao = $_POST['idPedidoAlteracao'];
        $idPronac = $_POST['idPronac'];
        $situacao = $_POST['deferimentoSolic'];
        $analisetecnica = $_POST['analisetecnica'];
        $observacoes = $_POST['observacoes'];

        //CONSULTA OS PEDIDOS NA TABELA tbPlanoDistribuicao
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            //inicia uma transa�ao
            $db->beginTransaction();

            // Chama o SQL
            $sqlFinalizarTec = ReadequacaoProjetos::retornaSQLfinalizarTec($idPedidoAlteracao, $situacao, $analisetecnica);
            $dados = $db->fetchAll($sqlFinalizarTec);

            //RETORNA EM VARI�VEIS OS DADOS DO LOG ANTERIOR
            $sqlFinalizarTec2 = ReadequacaoProjetos::retornaSQLfinalizarTec2($idPedidoAlteracao);
            $dados = $db->fetchAll($sqlFinalizarTec2);
            $idAvaliacaoItemPedidoAlteracao = $dados[0]->idAvaliacaoItemPedidoAlteracao;
            $idAgenteAvaliador = $dados[0]->idAgenteAvaliador;
            $idOrgao = $dados[0]->idOrgao;

            //ATUALIZAR A SITUA��O DO REGISTRO
            $sqlFinalizarPar3 = ReadequacaoProjetos::retornaSQLfinalizarTec3($idPedidoAlteracao, 7);
            $dados3 = $db->fetchAll($sqlFinalizarPar3);

            //ATUALIZAR A SITUA��O DO REGISTRO
            $sqlFinalizarPar4 = ReadequacaoProjetos::retornaSQLfinalizarTec4($idAvaliacaoItemPedidoAlteracao);
            $dados = $db->fetchAll($sqlFinalizarPar4);

            //INCLUIR NOVO REGISTRO
            $sqlFinalizarPar5 = ReadequacaoProjetos::retornaSQLfinalizarTec5($idAvaliacaoItemPedidoAlteracao, $idAgenteAvaliador, $observacoes, $idOrgao, $this->getIdUsuario, $this->codGrupo);
            $dados = $db->fetchAll($sqlFinalizarPar5);
            //salva os dados na base caso esteja tudo ok.
            $db->commit();

            parent::message("Projeto finalizado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "CONFIRM");
        } catch (Zend_Exception $e) {
            //Exce�ao pois houve erro ao tentar inserir ou atualizar dados na base.
            $db->rollBack();
            parent::message("Erro ao encaminhar Projeto", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "ERROR");
            /* Try _ Catch, � utilizado para tratamento de erros.
             * o $e->getMessage(), � utilizado para saber qual o tipo de erro que retornou.
            */
        }
    }

    public function finalizarprodutositensAction()
    {
        $idPedidoAlteracao = $_POST['idPedidoAlteracao'];
        $idPronac = $_POST['idPronac'];
        $situacao = $_POST['deferimentoSolic'];
        $analisetecnica = $_POST['analisetecnica'];

        //CONSULTA OS PEDIDOS NA TABELA tbPlanoDistribuicao
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            //inicia uma transa�ao
            $db->beginTransaction();

            // Chama o SQL
            $sqlFinalizarTec = ReadequacaoProjetos::retornaSQLfinalizarTec($idPedidoAlteracao, $situacao, $analisetecnica);
            $dados = $db->fetchAll($sqlFinalizarTec);

            //RETORNA EM VARI�VEIS OS DADOS DO LOG ANTERIOR
            $sqlFinalizarTec2 = ReadequacaoProjetos::retornaSQLfinalizarTec2($idPedidoAlteracao);
            $dados = $db->fetchAll($sqlFinalizarTec2);
            $idAvaliacaoItemPedidoAlteracao = $dados[0]->idAvaliacaoItemPedidoAlteracao;
            $idAgenteAvaliador = $dados[0]->idAgenteAvaliador;
            $idOrgao = $dados[0]->idOrgao;

            //INCLUIR NOVO REGISTRO
//                $retornaSQLInclusaoItem = ReadequacaoProjetos::retornaSQLInclusaoItem($idPedidoAlteracao,$idAgenteAvaliador);
//                $dados = $db->fetchAll($retornaSQLInclusaoItem);

//                $retornaSQLInclusaoItemId = ReadequacaoProjetos::retornaSQLInclusaoItemId($idPedidoAlteracao);
//                $dados = $db->fetchRow($retornaSQLInclusaoItemId);

            $sqlAtualizarSituacao = ReadequacaoProjetos::retornaSQLAtualizaUltimoPedidoParecerista($idAvaliacaoItemPedidoAlteracao);
            $db->fetchAll($sqlAtualizarSituacao);

            //INCLUIR NOVO REGISTRO
            $sqlFinalizarPar5 = ReadequacaoProjetos::retornaSQLInclusaoPar($idAvaliacaoItemPedidoAlteracao, $idAgenteAvaliador, '', $idOrgao, $this->getIdUsuario, $this->codGrupo);
            $dados = $db->fetchAll($sqlFinalizarPar5);
            //salva os dados na base caso esteja tudo ok.

            $db->commit();
            parent::message("Projeto finalizado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetoparecerista", "CONFIRM");
        } catch (Zend_Exception $e) {
            //Exce�ao pois houve erro ao tentar inserir ou atualizar dados na base.
            $db->rollBack();
            parent::message("Erro ao finalizar a an&aacute;lise dos produtos.", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$idPronac", "ERROR");
            /* Try _ Catch, � utilizado para tratamento de erros.
             * o $e->getMessage(), � utilizado para saber qual o tipo de erro que retornou.
            */
        }
    }


    public function avaliarprodutoAction()
    {
        try {
            // recebe os dados do formul�rio
            $idPronac        = $_POST['idPRONAC'];
            $idPlano         = $_POST['idPlano'];
            $idProduto       = $_POST['idProduto'];
            $avaliacao       = $_POST['avaliacaoDoItem'];
            $dsJustificativa = $_POST['justificativaPropRead'];

            // ALTERA OS DADOS MODIFICADOS PELO T�CNICO NO REGISTRO DO TIPO AT
            if ($avaliacao == "D") {
                $dados = array(
//                        'cdArea'                 => $_POST['CodArea']
//                        ,'cdSegmento'            => $_POST['CodSegmento']
                        'qtPatrocinador'        => str_replace(",", "", str_replace(".", "", $_POST['Patrocinador']))
                        ,'qtProduzida'           => str_replace(",", "", str_replace(".", "", $_POST['Beneficiarios']))
                        ,'qtOutros'              => str_replace(",", "", str_replace(".", "", $_POST['Divulgacao']))
                        ,'qtVendaNormal'         => str_replace(",", "", str_replace(".", "", $_POST['NormalTV']))
                        ,'qtVendaPromocional'    => str_replace(",", "", str_replace(".", "", $_POST['PromocionalTV']))
                        ,'vlUnitarioNormal'      => str_replace("R$ ", "", str_replace(",", ".", str_replace(".", "", $_POST['NormalPU'])))
                        ,'vlUnitarioPromocional' => str_replace("R$ ", "", str_replace(",", ".", str_replace(".", "", $_POST['PromocionalPU'])))
                    );
                $alterar = PlanoDistribuicaoDAO::alterar($dados, $idPlano);
            } // fecha if


            // ========== IN�CIO: cadastro de avalia��o do produto ==========
            $dados_produtos = array(
                    'idAvaliacaoItemPedidoAlteracao'     => $_POST['idAvaliacaoItemPedidoAlteracao']
                    ,'stAvaliacaoSubItemPedidoAlteracao' => $avaliacao
                    ,'dsAvaliacaoSubItemPedidoAlteracao' => $dsJustificativa);
            if (isset($_POST['idAvaliacaoSubItem']) && !empty($_POST['idAvaliacaoSubItem'])) {
                $cadastrar_avaliacao = AvaliacaoSubItemPedidoAlteracaoDAO::alterar($dados_produtos, $_POST['idAvaliacaoSubItem']);
                $ultimo = $_POST['idAvaliacaoSubItem'];
            } else {
                $cadastrar_avaliacao = AvaliacaoSubItemPedidoAlteracaoDAO::cadastrar($dados_produtos);
                // pega o �ltimo id inserido
                $ultimo = AvaliacaoSubItemPedidoAlteracaoDAO::buscarUltimo();
                $ultimo = $ultimo[0]->id;
            }

            // vincula o plano de distribui��o
            $dados_plano_Distribuicao = array(
                    'idAvaliacaoItemPedidoAlteracao'     => $_POST['idAvaliacaoItemPedidoAlteracao']
                    ,'idAvaliacaoSubItemPedidoAlteracao' => $ultimo
                    ,'idPlano'                           => $idPlano);

            if (!isset($_POST['idAvaliacaoSubItem']) || empty($_POST['idAvaliacaoSubItem'])) {
                $cadastrar_plano_distribuicao = AvaliacaoSubItemPlanoDistribuicaoDAO::cadastrar($dados_plano_Distribuicao);
            }
            // ========== FIM: cadastro de avalia��o do produto ==========


            if (!$cadastrar_avaliacao) {
                throw new Exception("Erro ao tentar avaliar o Produto!");
            } else {
                if (isset($_GET['itemDeCusto']) && $_GET['itemDeCusto']) {
                    parent::message("Solicita��o enviada com sucesso!", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$idPronac", "CONFIRM");
                } else {
                    parent::message("Solicita��o enviada com sucesso!", "verificarreadequacaodeprojeto/readequacaoprodutoseditar?id=$idPronac", "CONFIRM");
                }
            }
        } // fecha try
        catch (Exception $e) {
            if (isset($_GET['itemDeCusto']) && $_GET['itemDeCusto']) {
                parent::message("Erro ao avaliar o Produto!", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=".$idPronac, "ERROR");
            } else {
                parent::message("Erro ao avaliar o Produto!", "verificarreadequacaodeprojeto/readequacaoprodutoseditar?id=".$idPronac, "ERROR");
            }
        }
    } // fecha m�todo avaliarprodutoAction()


    public function finalizarprojetosprodutosAction()
    {
        // recebe os dados do formul�rio
        $idPronac  = $_POST['idPronac'];

        // VERIFICA��O DO STATUS GERAL
            $statusGeral = 3; // indeferido

            // cadastra somente os itens deferidos
        $i = 0;
        foreach ($_POST['arrayAvaliacao'] as $arrayAvaliacao) :

                if (trim($arrayAvaliacao) == "D") :

                    $statusGeral = 2; // deferido

        // busca o idPlanoDistribuicao (vincula��o entre a tabela original e a solicitada)
        $buscar = PlanoDistribuicaoDAO::buscar($_POST['arrayPlanos'][$i]);
        $idPedidoAlteracao = $buscar[0]->idPedidoAlteracao;
        //Zend_Debug::dump($buscar);$this->_helper->viewRenderer->setNoRender(TRUE);

        foreach ($buscar as $b) :

                        $array_plano = array(
                            'idProjeto'                => $_POST['arrayIdProjeto'][$i]
                            ,'idProduto'               => $b->idProduto
                            ,'Area'                    => $b->cdArea
                            ,'Segmento'                => $b->cdSegmento
                            ,'idPosicaoDaLogo'         => $b->idPosicaoLogo
                            ,'QtdeProduzida'           => $b->qtProduzida
                            ,'QtdePatrocinador'        => $b->qtPatrocinador
                            //,'QtdeProponente'          => $b->
                            ,'QtdeOutros'              => $b->qtOutros
                            ,'QtdeVendaNormal'         => $b->qtVendaNormal
                            ,'QtdeVendaPromocional'    => $b->qtVendaPromocional
                            ,'PrecoUnitarioNormal'     => $b->vlUnitarioNormal
                            ,'PrecoUnitarioPromocional'=> $b->vlUnitarioPromocional
                            ,'stPrincipal'             => $b->stPrincipal
                            ,'Usuario'                 => $this->getIdUsuario
                            ,'stPlanoDistribuicaoProduto'=>1
                        );

        // altera��o de produto j� existente
        if (!empty($b->idPlanoDistribuicao)) {
            $alterar = PlanoDistribuicaoProdutoDAO::alterar($array_plano, $b->idPlanoDistribuicao);
        }
        // inclus�o de novo produto
        else {
            $cadastrar = PlanoDistribuicaoProdutoDAO::cadastrar($array_plano);
        }
        endforeach;

        endif;

        $i++;
        endforeach;


        //FINALIZAR O PROJETO E ENVIAR PARA O COORDENADOR DE ACOMPANHAMENTO

        if ($statusGeral == 2) {
            $status = 'AP';
        } else {
            $status = 'IN';
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // busca o idPlanoDistribuicao (vincula��o entre a tabela original e a solicitada)
        $buscar = PlanoDistribuicaoDAO::buscar($_POST['arrayPlanos'][0]);
        $idPedidoAlteracao = $buscar[0]->idPedidoAlteracao;
        //Zend_Debug::dump($buscar);$this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            $db->beginTransaction();

            /*//UPDATE - CAMPOS: dsEstrategiaExecucao E dsEspecificacaoTecnica NA TABELA SAC.dbo.tbProposta
            $sqlfinalproped = ReadequacaoProjetos::retornaSQLfinalprop($estrategia,$especificacao,$IdProposta);
            $finalproped = $db->fetchAll($sqlfinalproped);*/

            //UPDATE - CAMPO: stVerificacao NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlfinalproped1 = ReadequacaoProjetos::retornaSQLfinalprop1($idPedidoAlteracao, 7);
            $db->fetchAll($sqlfinalproped1);

            $consultarIdAvaliacao = ReadequacaoProjetos::consultarIdAvaliacao($idPedidoAlteracao);
            $resultado = $db->fetchAll($consultarIdAvaliacao);
            $idAvaliacaoPedidoAlteracao = $resultado[0]->idAvaliacaoItemPedidoAlteracao;

            //UPDATE - CAMPO: dtFimAvaliacao NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlfinalproped2 = ReadequacaoProjetos::retornaSQLfinalprop2($idAvaliacaoPedidoAlteracao, " ", $status);
            $db->fetchAll($sqlfinalproped2);

            $consultarIdAcaoAvaliacao = ReadequacaoProjetos::consultarIdAcaoAvaliacao($idAvaliacaoPedidoAlteracao);
            $resultado2 = $db->fetchAll($consultarIdAcaoAvaliacao);
            $idAcaoAvaliacao = $resultado2[0]->idAcaoAvaliacao;
            $idOrgao = $resultado2[0]->idOrgao;


            //UPDATE - CAMPO: stAtivo NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfinalproped3 = ReadequacaoProjetos::retornaSQLfinalprop3($idAcaoAvaliacao);
            $db->fetchAll($sqlfinalproped3);

            //INSERT NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfinalproped4 = ReadequacaoProjetos::retornaSQLfinalprop4($idAvaliacaoPedidoAlteracao, $idOrgao);
            $db->fetchAll($sqlfinalproped4);

            $db->commit();

            parent::message("Projeto finalizado com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao finalizar projeto", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetotecnico", "ERROR");
        }
    }

    public function planilhasolicitadaAction()
    {
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
                $atualizapedidotipo = $buscaInformacoes->atualizarTipoAlteracao($dadosTipo, $where);

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
                $this->_helper->json(array('error' => false));
                $this->_helper->viewRenderer->setNoRender(true);
            } catch (Exception $e) {
                $this->_helper->json(array('error' => true, 'Descricao' => $e->getMessage()));
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

        //var_dump($resultadoProduto);$this->_helper->viewRenderer->setNoRender(TRUE);

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
}
