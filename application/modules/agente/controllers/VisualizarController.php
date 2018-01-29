<?php
class Agente_VisualizarController extends MinC_Controller_Action_Abstract
{

    public function init()
    {
        parent::init();
    }

    public function obterDadosProponenteAction()
    {
        $this->_helper->layout->disableLayout();

        $idAgente = $this->_request->getParam('idAgente');

        $dados = [];
        $matriz = [];

        try {
            $tbAgentes = new Agente_Model_DbTable_Agentes();
            $dados['identificacao'] = array_change_key_case(array_map('utf8_encode', $tbAgentes->buscarAgenteENome(['a.idAgente = ?' => $idAgente])->current()->toArray()));

            $tbNatureza = new Agente_Model_DbTable_Natureza();
            $dados['natureza'] = array_change_key_case(array_map('utf8_encode', $tbNatureza->findBy(['idAgente = ?' => $idAgente])));

            $tbEndereco = new Agente_Model_DbTable_EnderecoNacional();
            $matriz['enderecos'] = $tbEndereco->buscarEnderecos($idAgente)->toArray();

            $tbInternet = new Agente_Model_DbTable_Internet();
            $matriz['emails'] = $tbInternet->buscarEmails($idAgente)->toArray();

            $tbTelefones = new Agente_Model_DbTable_Telefones();
            $matriz['telefones'] = $tbTelefones->buscarFones($idAgente)->toArray();

            $matriz['dirigentes'] = [];

            if (strlen($dados['identificacao']['cnpjcpf']) > 11) {
                $matriz['dirigentes'] = $tbAgentes->buscarDirigentes(array('v.idVinculoPrincipal = ?' => $idAgente, 'n.Status = ?' => 0), array('n.Descricao ASC'))->toArray();
            }

            foreach ($matriz as $key => $array) {
                foreach ($array as $key2 => $dado) {
                    $dados[$key][$key2] = array_change_key_case(array_map('utf8_encode', $dado));
                }
            }

            $this->_helper->json(array('data' => $dados, 'success' => 'true'));

        } catch (Exception $e) {
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }
}
