<?php

class CaptacaoController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        //recupera ID do pre projeto (proposta)
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // t�tulo da p�gina
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario = new UsuarioDAO(); // objeto usu�rio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usu�rio esteja autenticado
            // verifica as permiss�es
            $PermissoesGrupo = array();

            $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
            $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
            $PermissoesGrupo[] = 121; // Coordenador de Acompanhamento

            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org�os e grupos do usu�rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a vis�o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o
        } else {
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    public function indexAction()
    {
        // $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
    }

    public function dadosprojetoAction()
    {
        $busca = new CaptacaoDAO();
        $projeto = $busca->buscarProjetos($this->_request->get('pronac'));
        if (count($projeto) > 0) {
            $this->view->projeto = $projeto;
        } else {
            parent::message("Projeto sem autoriza&ccedil;&atilde;o para captar!", "captacao/index", "ALERT");
        }
    }

    public function pesquisarprojetoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $this->_helper->viewRenderer->setNoRender(true);
        $get = Zend_Registry::get('post');
        $pronac = $get->pronac;

        $busca = new ProjetoDAO();
        $projeto = $busca->buscarDadosProjeto($pronac);

        if ($projeto) {
            $situacoesDoProjeto = array('E10', 'E12', 'E13');
            if (in_array($projeto['Situacao'], $situacoesDoProjeto)) {
                if (1 == $projeto['Mecanismo']) {
                    $this->_helper->json(array('resposta' => true));
                } else {
                    $string = utf8_encode('<b>Favor informar o n�mero de PRONAC do tipo mecenato!</b>');
                    $this->_helper->json(array('resposta' => false, 'conteudo' => $string));
                }
            } else {
                $string = utf8_encode('<b>Apenas os projetos que est�o na fase ' . implode(' ou ', $situacoesDoProjeto) . ' podem realizar capta��o!</b>');
                $this->_helper->json(array('resposta' => false, 'conteudo' => $string));
            }
        } else {
            $string = '<b>Pronac Inexistente!</b>';
            $this->_helper->json(array('resposta' => false, 'conteudo' => $string));
        }
    }

    public function localizarInteressadoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $get = Zend_Registry::get('get');
        $cpf = $get->cpf;

        //		$busca = new Interessado();
        $busca = new Agente_Model_DbTable_Agentes();
        $agente = $busca->BuscaAgente($cpf)->toArray();

        $buscar = new Interessado();
        //$interessado = $buscar->buscar(array('CgcCpf = ?'=>$cpf))->toArray();

        if ($agente) {
            $busca = new Nomes();
            $interessado = $busca->buscar(array('idAgente = ?' => $agente[0]['idAgente']))->toArray();

            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $dadosInteressado['Nome'] = utf8_encode($interessado[0]['Descricao']);
            $jsonEncode = json_encode($dadosInteressado);
            //echo $jsonEncode;
            $this->_helper->json(array('resposta' => "true", 'conteudo' => $dadosInteressado));
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->_helper->json(array('resposta' => false));
        }
    }

    public function realizarCaptacaoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();


        $post = Zend_Registry::get('post');
        $interessadoModel = new Interessado();
        $interessados = $interessadoModel->buscar(array('CgcCpf = ?' => $post->cpf));

        if (!$interessados->count()) {
            $agenteModel = new Agente_Model_DbTable_Agentes();
            $agentes = $agenteModel->buscar(array('CNPJCPF = ?' => $post->cpf));
            if (!$agentes->count()) {
                parent::message("CNPJ/CPF n�o existe na tabela Interessado!", "captacao/index", "ALERT");
            }

            $agente = $agentes->current();
            $nomeModel = new Nomes();

            $tipoPessoa = $agente->TipoPessoa;
            if (!$tipoPessoa) {
                $tipoPessoa = (11 == strlen($agente->CNPJCPF)) ? 1 : 2;
            }

            $agenteNome = $nomeModel->buscar(array('idAgente = ?' => $agente->idAgente))->current();
            $interessadoModel->inserir(array(
                'CgcCpf' => $agente->CNPJCPF,
                'tipoPessoa' => $tipoPessoa,
                'Nome' => $agenteNome->Descricao,
                "Endereco" => "0",
                "Cidade" => "",
                "Uf" => "",
                "Cep" => "",
                "Responsavel" => "",
                "Grupo" => 1,
            ));
        }

        $pronac = $post->anoProjeto.$post->sequencial;

        $result = new Projetos();
        $idprojeto = $result->buscarIdPronac($pronac);

        $insert = new Captacao();
        $captado = $insert->inserir(array(
            'IdProjeto' => $idprojeto->IdPRONAC,
            'AnoProjeto' => $post->anoProjeto,
            'Sequencial' => $post->sequencial,
            'isBemServico' => $this->_request->get('isBemServico'),
            'NumeroRecibo' => $post->NumeroRecibo,
            'logon' => $post->logon,
            'CgcCpfMecena' => $post->cpf,
            'DtRecibo' => data::dataAmericana($post->dt_recibo),
            'DtChegadaRecibo' => data::dataAmericana($post->dt_minc),
            'TipoApoio' => $post->tpApoio,
            'CaptacaoReal' => $post->valor,
            'CaptacaoUfir' => $post->valor
        ));

        $responseMessage = 'Projeto captado com sucesso!';
        $responseUrl = "captacao/index/pronac/{$this->_request->getParam('anoProjeto')}{$this->_request->getParam('sequencial')}";
        $responseType = 'CONFIRM';
        if (!$captado) {
            $responseMessage = 'Erro ao captar o projeto!';
            $responseType = 'ALERT';
        }
        parent::message($responseMessage, $responseUrl, $responseType);
    }
}
