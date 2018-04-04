<?php

abstract class Analise_GenericController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::init();

        # pega a autenticacao
        $auth = Zend_Auth::getInstance();

        # define as permisseos
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 103;  // Coordenador de Analise
        $PermissoesGrupo[] = 110;  // Tecnico de Anelise

        parent::perfil(1, $PermissoesGrupo);

        $this->getIdUsuario = $auth->getIdentity()->usu_codigo;
    }
}
