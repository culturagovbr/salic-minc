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

        $idAgente = (int)$this->_request->getParam('idAgente');
        $cpf = (int)$this->_request->getParam('cpf');

        $dados = [];
        $matriz = [];

        $whereAgente = [];

        if (!empty($idAgente)) {
            $whereAgente['a.idAgente = ?'] = $idAgente;
        }

        if (!empty($cpf)) {
            $whereAgente['a.cnpjcpf = ?'] = $cpf;
        }

        try {
            $tbAgentes = new Agente_Model_DbTable_Agentes();
            $dados['identificacao'] = array_change_key_case(array_map('utf8_encode', $tbAgentes->buscarAgenteENome($whereAgente)->current()->toArray()));

            $idAgente = $dados['identificacao']['idagente'];

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
