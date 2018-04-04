<?php

class GerarRelatoriosController extends MinC_Controller_Action_Abstract
{
    /**
     * @var integer (vari�vel com o id do usu�rio logado)
     * @access private
     */
    private $getIdUsuario = 0;


    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o


        // define as permiss�es
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

        parent::perfil(4, $PermissoesGrupo);

        // pega o idAgente do usu�rio logado
        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            if ($this->getIdUsuario) {
                $this->getIdUsuario = $this->getIdUsuario["idAgente"];
            } else {
                $this->getIdUsuario = 0;
            }
        } else {
            $this->getIdUsuario = $auth->getIdentity()->IdUsuario;
        }

        parent::init();

        $this->view->comboestados          = Estado::buscar();
        $this->view->combofundos           = GerarRelatoriosDAO::consultaFundos();
        $this->view->comboclassificacoes   = GerarRelatoriosDAO::consultaClassificacoes();
        $this->view->comboeditais   	   = GerarRelatoriosDAO::consultaEditais();
    }


    public function indexAction()
    {
    }

    public function buscaAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $relatorio    		= $this->_request->getParam("consulta");
        $idEdital    		= $this->_request->getParam("edital");
        $idUf    			= $this->_request->getParam("uf");
        $idMunicipio    	= $this->_request->getParam("cidade");
        $idFundo    		= $this->_request->getParam("fundo");
        $idClassificacao    = $this->_request->getParam("classificacao");



        $this->view->relatorio = $relatorio;
        $this->view->nome = 'PROPOSTA';

        if ($relatorio == 1) {
            $consulta = GerarRelatoriosDAO::relatorio1($idEdital, $idUf, $idMunicipio, $idFundo, $idClassificacao);
        } elseif ($relatorio == 2) {
            $consulta = GerarRelatoriosDAO::relatorio2($idEdital, $idUf, $idMunicipio, $idFundo, $idClassificacao);
        } elseif ($relatorio == 3) {
            $this->view->nome = 'PROJETO';
            $consulta = GerarRelatoriosDAO::relatorio3($idEdital, $idUf, $idMunicipio, $idFundo, $idClassificacao);
        } elseif ($relatorio == 4) {
            $consulta = GerarRelatoriosDAO::relatorio4($idEdital, $idUf, $idMunicipio, $idFundo, $idClassificacao);
        }

        $this->view->busca = $consulta;
    }


    public function exportxlsAction()
    {
        $this->_helper->layout->disableLayout();
    }


    public function comboeditalAction()
    {
        $this->_helper->layout->disableLayout();

        $valores = $this->_request->getParam("valores");

        $v = explode(":", $valores);

        $idf = $v[0];
        $idc = $v[1];

        // integra��o MODELO e VIS�O
        $this->view->comboeditais   = GerarRelatoriosDAO::consultaEditais($idf, $idc);
    }

    public function exportxls2Action()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if ($_POST) {
            header("Content-type: application/msexcel");
            header("Content-Disposition: attachment; filename=documentos.ods");
            echo $_POST['htmlxls'];
        }
    }

    public function listarselecionadosAction()
    {
        $this->_helper->layout->disableLayout();
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : 0;
        $idorgao   = isset($auth->getIdentity()->usu_orgao)  ? $auth->getIdentity()->usu_orgao  : 0;
        
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codGrupo = $codGrupo;
        $this->view->codOrgao = $codOrgao;
        /*         * *************************************************************** */

        $Orgao = new Orgaos();
        $NomeOrgao = $Orgao->pesquisarNomeOrgao($codOrgao);
        $this->view->nomeOrgao = $NomeOrgao[0]->Codigo;

        foreach ($NomeOrgao as $idOrgao) {
            $idOrgao->Codigo = $idOrgao->Codigo;
        }

        /*Fixo:  $idOrgaos: 363 -- $idusuario: 2623*/
        $edital = new tbEditalXtbFaseEdital();
        $dadosEdital = $edital->buscaEditalFormDocumentoLista($codOrgao); //Depois colocar dados din�micos


        $this->view->numeroEdital = $dadosEdital;
        foreach ($dadosEdital as $dados) {
            $idEdital = $dados->idEdital;
            $projetoEdital = ListareditaisDAO::buscaProjetosEdital($idEdital);
            //x($projetoEdital);
        }

        if (isset($_POST['msg']) and $_POST['msg'] == 'ok') {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

            $post = Zend_Registry::get('post');
            $idEdital2 = $post->idEdital;

            $projetoEdital = ListareditaisDAO::buscaProjetosEdital($idEdital2);

            $x = 0;
            if (is_array($projetoEdital) and count($projetoEdital) > 0) {
                foreach ($projetoEdital as $projeto) {
                    $dadosProjeto[$x]['PRONAC'] = utf8_encode($projeto->PRONAC);
                    $dadosProjeto[$x]['nmProjeto'] = utf8_encode($projeto->nmProjeto);
                    $dadosProjeto[$x]['nrNotaFinal'] = utf8_encode($projeto->nrNotaFinal);
                    $x++;
                }
                $jsonEncode = json_encode($dadosProjeto);

                $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosProjeto));
            } else {
                $this->_helper->json(array('resposta'=>false));
            }

            $this->_helper->viewRenderer->setNoRender(true);
        }
    }
} // fecha class
