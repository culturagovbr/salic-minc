<?php
class PrestacaoContas_FornecedorController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $PermissoesGrupo = [
            Autenticacao_Model_Grupos::PROPONENTE,
        ];

        $auth = Zend_Auth::getInstance();

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        isset($auth->getIdentity()->usu_codigo) ? $this->idUsuario = $auth->getIdentity()->usu_codigo : $this->idUsuario = $auth->getIdentity()->IdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->codGrupo = $GrupoAtivo->codGrupo;
            $this->codOrgao = $GrupoAtivo->codOrgao;
            $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : null;
        }

        parent::init();
    }

    public function indexAction()
    {
        $this->view->cpfcnpj = $this->_request->getParam("cpfcnpj");
    }

    private function retornaJson($dados) {

        $data = [];

        foreach ($dados as $index => $dado) {
            if(is_array($dado)){
                $data[$index]['id'] = utf8_encode($dado['idVerificacao']);
                $data[$index]['descricao'] = utf8_encode($dado['Descricao']);
            } elseif (is_object($dado)){
                $data[$index]['id'] = utf8_encode($dado->id);
                $data[$index]['descricao'] = utf8_encode($dado->descricao);
            }elseif(!is_array($dado) and !is_object($dado)){
                $data[$index]['id'] = utf8_encode($index);
                $data[$index]['descricao'] = utf8_encode($dado);
            }
        };

        return  $this->_helper->json($data);
    }

    public function ufAction() {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $estados = new Agente_Model_DbTable_UF();
        $this->retornaJson($estados->listar());
    }

    public function cidadeAction() {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $id = $this->_request->getParam("id");
        $cidade = new Cidade();
        $this->retornaJson($cidade->buscar($id));
    }

    public function fornecedoresTipoAction(){
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $consulta = new Agente_Model_DbTable_Visao();
        $this->retornaJson($consulta->buscarVisoes());
    }

    public function logradouroTipoAction(){
        $mapperVerificacao = new Agente_Model_VerificacaoMapper();
        $tipos = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 13));
        $this->retornaJson($tipos);
    }

    public function enderecoTipoAction(){
        $mapperVerificacao = new Agente_Model_VerificacaoMapper();
        $endereco = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 2));
        $this->retornaJson($endereco);
    }

    public function buscarCepAction(){
       $cep = $this->_request->getParam("cep");
       $logradouro = new PrestacaoContas_Model_DbTable_Enderecos();
       $this -> retornaJson($logradouro->buscarCep($cep));
    }
}
