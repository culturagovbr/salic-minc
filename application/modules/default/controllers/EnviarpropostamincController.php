<?php
class EnviarpropostamincController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 97;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 121; // T&eacute;cnico
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);

        parent::init();
    }

    public function indexAction()
    {
    }
}
