<?php
class CepController extends MinC_Controller_Action_Abstract
{
    /**
     * Metodo para buscar o endereuo de acordo com o cep informado
     * @access public
     * @param void
     * @return void
     */
    public function cepAction()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $strCharset = $config->resources->db->params->charset;
        $this->view->charset = $strCharset;
        header('Content-type: text/html; charset=' . $strCharset);
        $this->_helper->layout->disableLayout();

        // recebe o cep sem mascara vindo via ajax
        $get = Zend_Registry::get('get');
        $cep = Mascara::delMaskCEP(Seguranca::tratarVarAjaxUFT8($get->cep));

        $cepObj = new Cep();
        $resultado = $cepObj->buscarCEP($cep);

        if ($resultado) { // caso encontre o cep
            $_end         = $resultado['logradouro'];
            $_complemento = $resultado['tipo_logradouro'];
            $_bairro      = $resultado['bairro'];
            $_uf          = $resultado['uf'];

            // atribuica da cidade
            if (empty($resultado['idcidademunicipios']) || empty($resultado['dscidademunicipios'])) {
                // caso a cidade nao exista na tabela de municipios (tabela associada aos agentes)
                // pega a primeira cidade do estado
                $_cod_cidade = $resultado['idcidadeuf'];
                $_cidade     = $resultado['dscidadeuf'];
            } else {
                // caso a cidade exista na tabela de municipios (tabela associada aos agentes)
                // pega a cidade da tabela de municipios
                $_cod_cidade = $resultado['idcidademunicipios'];
                $_cidade     = $resultado['dscidademunicipios'];
            }


            $buscarCEP = $_end . ":" . $_complemento . ":" . $_bairro . ":" . $_cod_cidade . ":" . $_cidade . ":" . $_uf . ";";
        } // fecha if
        else { // caso nao ache o cep
            $buscarCEP = "";
        }


        $this->view->cep = $buscarCEP;
    }

    public function cepAjaxAction()
    {
        $this->_helper->layout->disableLayout();

        // recebe o cep sem mascara vindo via ajax
        $get = Zend_Registry::get('get');
        $cep = Mascara::delMaskCEP(Seguranca::tratarVarAjaxUFT8($get->cep));

        $cepObj = new Cep();
        $resultado = $cepObj->buscarCEP($cep);

        if ($resultado) { // caso encontre o cep
            // atribuica da cidade
            if (empty($resultado['idcidademunicipios']) || empty($resultado['dscidademunicipios'])) {
                // caso a cidade nao exista na tabela de municipios (tabela associada aos agentes)
                // pega a primeira cidade do estado
                $resultado['idCidade'] = $resultado['idcidadeuf'];
                $resultado['cidade']  = $resultado['dscidadeuf'];
            } else {
                // caso a cidade exista na tabela de municipios (tabela associada aos agentes)
                // pega a cidade da tabela de municipios
                $resultado['idCidade'] = $resultado['idcidademunicipios'];
                $resultado['cidade'] = $resultado['dscidademunicipios'];
            }
            $resultado['status'] = true;
            $resultado['tipoLogradouro'] = utf8_encode($resultado['tipo_logradouro']);
            $resultado['tipo_logradouro'] = utf8_encode($resultado['tipo_logradouro']);
            $resultado['cidade'] = utf8_encode($resultado['cidade']);
            $resultado['logradouro'] = utf8_encode($resultado['logradouro']);
            $resultado['dscidademunicipios'] = utf8_encode($resultado['dscidademunicipios']);
            $resultado['dscidadeuf'] = utf8_encode($resultado['dscidadeuf']);
            $resultado['bairro'] = utf8_encode($resultado['bairro']);
        } // fecha if
        else { // caso nao ache o cep
            $resultado['status'] = false;
        }

        $this->_helper->json($resultado);
        die;
    }
}
