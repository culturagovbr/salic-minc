<?php

class MensagemRestController extends Minc_Controller_AbstractRest
{
    public function init()
    {
        $this->setPublicMethod('post');
        $this->setPublicMethod('index');
        $this->setPublicMethod('get');
        $this->setPublicMethod('put');
        $this->setPublicMethod('delete');
        parent::init();
    }

    public function postAction()
    {
    }
    
    public function indexAction()
    {
        $next = $this->_request->getParam('next');
        $offset = $this->_request->getParam('offset');
        $total = $this->_request->getParam('total');
        $idRegistration = $this->registrationId;
        $new = $this->_request->getParam('new');
        
        $listaMensagens = array();
        $modelMensagem = new Mensagem();
        $objParam = (object) array(
            'next' => $next,
            'offset' => $offset,
            'idRegistration' => $idRegistration,
            'new' => $new);
        # Verifica se existe necessidade de buscar o número total de registros da consulta
        if (!$total) {
            $total = $modelMensagem->buscarTotalListarDeDispositivo($objParam);
        }
        # Busca os dados da lista
        $objListaRs = $modelMensagem->listarDeDispositivo($objParam);
        if ($objListaRs) {
            $listaMensagens = $this->formatarUtf8Mensagens($objListaRs->toArray());
        }
        # Resposta do serviço.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode(
            array(
            'list' => $listaMensagens,
            'total' => (int)$total)
        ));
    }
    
    public function getAction()
    {
    }

    public function putAction()
    {
        $idMensagem = $this->_request->getParam('idMensagem');
        $result = new stdClass();
        if ($idMensagem) {
            $modelMensagem = new Mensagem();
            $mensagem = $modelMensagem->find($idMensagem)->current();
            if (!$mensagem->dtAcesso) {
                $mensagem->dtAcesso = new Zend_Db_Expr('GETDATE()');
                $mensagem->save();
            }
            $result = (object)$mensagem->toArray();
        }
        
        # Resposta do serviço.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($result));
    }

    public function deleteAction()
    {
        $idMensagem = $this->_request->getParam('idMensagem');
        $result = new stdClass();
        if ($idMensagem) {
            $modelMensagem = new Mensagem();
            $mensagem = $modelMensagem->find($idMensagem)->current();
            if (!$mensagem->dtExclusao) {
                $mensagem->dtExclusao = new Zend_Db_Expr('GETDATE()');
                $mensagem->save();
            }
            $result = (object)$mensagem->toArray();
        }
        
        # Resposta do serviço.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($result));
    }

    /**
     * Formata lista de mensagens para o formato utf8.
     *
     * @param array $arrListaRs
     * @return array $listaMensagens
     */
    private function formatarUtf8Mensagens(array $arrListaRs = null)
    {
        $listaMensagens = array();
        if ($arrListaRs) {
            foreach ($arrListaRs as $mensagem) {
                $mensagem['titulo'] = utf8_encode($mensagem['titulo']);
                $mensagem['descricao'] = utf8_encode($mensagem['descricao']);
                $listaMensagens[] = (object)$mensagem;
            }
        }
        
        return $listaMensagens;
    }
}
