<?php
class Projeto_ProjetoController extends Proposta_GenericController
{
    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');

        $arrIdentity = array_change_key_case((array) Zend_Auth::getInstance()->getIdentity());
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo');

        // verifica as permissoes
        //$PermissoesGrupo = array();
        //$PermissoesGrupo[] = 97;  // Gestor do SALIC
        //$PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        //if (isset($arrIdentity['usu_codigo'])) {
        //parent::perfil(1, $PermissoesGrupo);
        //} else {
        //parent::perfil(4, $PermissoesGrupo);
        //}

        /*********************************************************************************************************/

        $cpf = isset($arrIdentity['usu_codigo']) ? $arrIdentity['usu_identificacao'] : $arrIdentity['cpf'];

        // Busca na SGCAcesso
        $modelSgcAcesso 	 = new Autenticacao_Model_Sgcacesso();
        $arrAcesso = $modelSgcAcesso->findBy(array('cpf' => $cpf));

        // Busca na Usuarios
        //Excluir ProposteExcluir Proposto
        $usuarioDAO   = new Autenticacao_Model_Usuario();
        $arrUsuario = $usuarioDAO->findBy(array('usu_identificacao' => $cpf));

        // Busca na Agentes
        $tableAgentes  = new Agente_Model_DbTable_Agentes();
        $arrAgente = $tableAgentes->findBy(array('cnpjcpf' => trim($cpf)));

        if ($arrAcesso) {
            $this->idResponsavel = $arrAcesso['idusuario'];
        }
        if ($arrAgente) {
            $this->idAgente 	  = $arrAgente['idagente'];
        }
        if ($arrUsuario) {
            $this->idUsuario     = $arrUsuario['usu_codigo'];
        }
        if ($this->idAgente != 0) {
            $this->usuarioProponente = "S";
        }
        $this->cpfLogado = $cpf;

        $this->_helper->contextSwitch()
            ->addActionContext('identificacao', array('json'))
            ->initContext();

        parent::init();
    }

    public function indexAction()
    {
    }

    public function verificarIn2017Action()
    {
        $idPronac = $this->getRequest()->getParam('idPronac');

        $tbProjetos = new Projeto_Model_DbTable_Projetos();
        $IN2017 = $tbProjetos->verificarIN2017($idPronac);

        $this->_helper->json(['idPronac' => $idPronac, 'IN2017' => $IN2017]);
    }

    public function identificacaoAction()
    {
        $idPronac = $this->getRequest()->getParam('idPronac');

        $projeto = new Projeto_Model_DbTable_Projetos();
        $dados = $projeto->identificacao($idPronac);

        $resultado['nome'] = utf8_encode($dados['NomeProjeto']);
        $resultado['PRONAC'] = $dados['AnoProjeto'] . $dados['Sequencial'];
        /* $resultado[''] = $dados['AnoProjeto'] . $dados['Sequencial']; */
        $resultado['descricao'] = $dados['Descricao'];
        $resultado['nomeAgente'] = $dados['NomeAgente'];
        $resultado['situacao'] = utf8_encode($dados['situacao']);

        return $this->_helper->json->sendJson($resultado);
    }
}
