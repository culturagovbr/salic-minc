<?php

namespace Application\Modules\Navegacao\Service;

class Comunicados
{
    private $request;
    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function obterComunicados()
    {
        $auth = \Zend_Auth::getInstance();
        $usuarioAtivo = $auth->getIdentity();
        $arrAuth = array_change_key_case((array)$usuarioAtivo);

        if (!$arrAuth['usu_codigo']) {
            return [];
        }

        $tbComunicados = new \tbComunicados();

        $where['stEstado = ?'] = 1;
        $where['stOpcao = ?'] = 0;
        $ordem = ['dtiniciovigencia desc', 'idComunicado desc'];

        return $tbComunicados->listarComunicados($where, $ordem)->toArray();
    }

}
