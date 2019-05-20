<?php

class AjaxController extends MinC_Controller_Action_Abstract
{
    public function municipioAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idUf = $_POST['iduf'];
        try {

            if (empty($idUf)) {
               throw new Exception("UF &eacute; obrigat&oacute;rio") ;
            }

            $municipio = new Municipios();
            $buscarmunicipio = $municipio->buscar($idUf);
            $municipio = array();
            $cont = 0;
            foreach ($buscarmunicipio as $dadosmunicipio) {
                $municipio[$cont]['idmun'] = $dadosmunicipio->id;
                $municipio[$cont]['descmun'] = utf8_encode($dadosmunicipio->descricao);
                $cont++;
            }
            $this->_helper->json($municipio);
            $this->_helper->viewRenderer->setNoRender(true);
        } catch (Zend_Exception $e) {
            $this->_helper->json(array('error' => 'true'));
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function agentesAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idOrgao = $_POST['idorgao'];

        $u = new Usuariosorgaosgrupos();
        $buscarAgentes = $u->buscardadosAgentes($idOrgao, 121);

        $dadosagente = array();
        $cont = 0;
        foreach ($buscarAgentes as $dados) {
            $dadosagente[$cont]['usu_codigo'] = $dados->usu_codigo;
            $dadosagente[$cont]['usu_nome'] = utf8_encode($dados->usu_nome);
            $dadosagente[$cont]['perfil'] = utf8_encode($dados->perfil);
            $dadosagente[$cont]['gru_codigo'] = utf8_encode($dados->gru_codigo);
            $dadosagente[$cont]['idAgente'] = $dados->idAgente;
            $cont++;
        }
        if (count($dadosagente) > 0) {
            $this->_helper->json($dadosagente);
        } else {
            $this->_helper->json(array('error'=>true));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function fundoClassificacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $id = $_POST["id"];
        $tbl = new tbClassificaDocumento();
        $rs = $tbl->fundoSetorialXClassificacao(array("f.stModalidadeDocumento is not null"=>"", "cdTipoFundo = ?"=>$id), array("dsClassificaDocumento ASC"));

        try {
            $dados = array();
            $cont = 0;
            foreach ($rs as $dado) {
                $dados[$cont]['id'] = $dado->idClassificaDocumento;
                $dados[$cont]['desc'] = utf8_encode($dado->dsClassificaDocumento);
                $cont++;
            }
            
            $this->_helper->json($dados);
            $this->_helper->viewRenderer->setNoRender(true);
        } catch (Zend_Exception $e) {
            $this->_helper->json(array('error' => 'true'));
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function classificacaoEditalAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $id = $_POST["id"];
        $tbl = new tbFormDocumento();
        $rs = $tbl->buscar(array("idClassificaDocumento = ?"=>$id), array("nmFormDocumento ASC"));

        try {
            $dados = array();
            $cont = 0;
            foreach ($rs as $dado) {
                $dados[$cont]['id'] = $dado->idEdital;
                $dados[$cont]['desc'] = utf8_encode($dado->nmFormDocumento);
                $cont++;
            }
            
            //$dados = array_unique($dados);
            $this->_helper->json($dados);
            $this->_helper->viewRenderer->setNoRender(true);
        } catch (Zend_Exception $e) {
            $this->_helper->json(array('error' => 'true'));
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }
}
