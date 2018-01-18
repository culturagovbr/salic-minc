<?php

/**
 * UploadController
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 */
class AnexospublicosController extends MinC_Controller_Action_Abstract
{
    private $idPreProjeto = null;
    private $idPronac = null;
    private $limiteTamanhoArq = null;
    private $orgaoAutorizado = null;
    private $orgaoLogado = null;
    private $cod = null;
    private $blnProponente = false;
    private $intFaseProjeto = 0;
    private $cpfLogado = 0;
    private $idResponsavel = 0;
    private $idAgente = 0;

    /**
     * Reescreve o m?todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Método para abrir um arquivo anexado
     * @access public
     * @param void
     * @return void
     */
    public function abrirAction()
    {
        // recebe o id do arquivo via get
        $get = Zend_Registry::get('get');
        $id = (int) isset($get->id) ? $get->id : $this->_request->getParam('id');

        // Configuração o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        // busca o arquivo
        $resultado = UploadDAO::abrir($id);

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
            die("N&atilde;o existe o arquivo especificado");
            $this->view->message = 'Não foi possível abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // lê os cabeçalhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $this->getResponse()
                        ->setHeader('Content-Type', $r->dsTipoPadronizado)
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                        //->setHeader("Connection", "close")
                        //->setHeader("Content-transfer-encoding", "binary")
                        //->setHeader("Cache-control", "private")
                        ->setBody($r->biArquivo);
            } // fecha foreach
        } // fecha else
    }


    /**
     * @access public
     * @param void
     * @return void
     */
  
    public function abrirdocumentosanexadosAction()
    {
        // recebe o id do arquivo via get
        $get = Zend_Registry::get('get');
        $id = (int) $get->id;
        $busca = $this->_request->getParam('busca'); //$get->busca;
        // Configuração o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        // busca o arquivo
        $resultado = UploadDAO::abrirdocumentosanexados($id, $busca);
        if (!$resultado) {
            if ($busca == "documentosanexadosminc") {
                $resultado = UploadDAO::abrirdocumentosanexados($id, "documentosanexadosminc");
            } else {
                $resultado = UploadDAO::abrirdocumentosanexados($id, "documentosanexados");
            }
        }

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
            die("N&atilde;o existe o arquivo especificado");
            $this->view->message = 'Não foi possível abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // lê os cabeçalhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $hashArquivo = ($r->biArquivo) ? $r->biArquivo : $r->biArquivo2;

                $this->getResponse()
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                        ->setHeader("Connection", "close")
                        ->setHeader("Content-transfer-encoding", "binary")
                        ->setHeader("Cache-control", "private");

                if ($r->biArquivo2 == 1) {
                    if (strtolower(substr($r->biArquivo, 0, 4)) == '%pdf') {
                        $this->getResponse()->setBody($hashArquivo);
                    } else {
                        $this->getResponse()->setBody(base64_decode($hashArquivo));
                    }
                } else {
                    if ($r->biArquivo2 == null) {
                        $this->getResponse()->setBody(base64_decode($hashArquivo));
                    } else {
                        $this->getResponse()->setBody($hashArquivo);
                    }
                }
                //->setBody(base64_decode($hashArquivo));
            } // fecha foreach
        } // fecha else
    }
}

// fecha class
