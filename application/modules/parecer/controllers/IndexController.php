<?php

class Parecer_IndexController extends MinC_Controller_Action_Abstract implements MinC_Assinatura_Controller_IDocumentoAssinaturaController
{
    private $idPronac;
    /**
     * @var MinC_Assinatura_Documento_IDocumentoAssinatura $servicoDocumentoAssinatura
     */
    private $servicoDocumentoAssinatura;
    
    public function init()
    {
        parent::perfil();
        parent::init();
        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
    }

    /**
     * @return Parecer_DocumentoAssinaturaController
     */
    public function obterServicoDocumentoAssinatura()
    {
    }

    public function indexAction()
    {
        switch ($this->grupoAtivo->codGrupo) {
        case Autenticacao_Model_Grupos::PARECERISTA:
            $this->redirect("/{$this->moduleName}/analise-inicial");
            break;
        case Autenticacao_Model_Grupos::COORDENADOR_DE_PARECERISTA:
            $this->redirect("/{$this->moduleName}/gerenciar-parecer/index");
            break;
        }
    }

    public function gerenciarAssinaturasAction()
    {
        switch ($this->grupoAtivo->codGrupo) {
        case Autenticacao_Model_Grupos::PARECERISTA:
            $this->redirect("/{$this->moduleName}/analise-inicial");
            break;
        case Autenticacao_Model_Grupos::COORDENADOR_DE_PARECERISTA:
            $this->redirect("/{$this->moduleName}/gerenciar-parecer/index");
            break;
        }
    }


    public function encaminharAssinaturaAction()
    {
    }
}
