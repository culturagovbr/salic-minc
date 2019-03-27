<?php

abstract class Projeto_GenericController extends MinC_Controller_Action_Abstract
{
    protected $idUsuarioExterno = 0;
    protected $idUsuarioInterno = 0;
//    protected $idAgente = 0;
    protected $cpfLogado;
    protected $agente;
    protected $isProponente = false;
    protected $usuarioExterno;
    protected $usuarioInterno;
    protected $usuario;
    protected $autenticacao;

    public function init()
    {
        parent::init();

        $this->autenticacao = array_change_key_case((array)Zend_Auth::getInstance()->getIdentity());

        $this->cpfLogado = isset($this->autenticacao['usu_codigo']) ? $this->autenticacao['usu_identificacao'] : $this->autenticacao['cpf'];

        if (is_null($this->cpfLogado)) {
            $this->redirect('/');
        }

        if (isset($this->autenticacao['idusuario'])) {
            $this->idUsuarioExterno = $this->autenticacao['idusuario'];

            // Busca na SGCAcesso
            $modelSgcAcesso = new Autenticacao_Model_Sgcacesso();
            $this->usuarioExterno = array_change_key_case($modelSgcAcesso->findBy(array('cpf' => $this->cpfLogado)));

            // Busca na Agentes
            $tableAgentes = new Agente_Model_DbTable_Agentes();
            $this->agente = array_change_key_case($tableAgentes->findBy(array('cnpjcpf' => trim($this->cpfLogado))));

            if ($this->agente) {
//                $this->idAgente = $this->agente['idagente'];
                $this->view->idAgente = $this->idAgente;
                $this->isProponente = true;
                $this->view->isProponente = $this->isProponente;
            }
        }

        if (isset($this->autenticacao['usu_codigo'])) {
            $this->idUsuarioInterno = $this->autenticacao['usu_codigo'];

            // Busca na Usuarios
            $usuarioDAO = new Autenticacao_Model_DbTable_Usuario();
            $this->usuarioInterno = array_change_key_case($usuarioDAO->findBy(array('usu_identificacao' => $this->cpfLogado)));
        }

        $this->view->usuario = !empty($this->usuarioExterno) ? $this->usuarioExterno : $this->usuarioInterno;
        $this->view->idUsuario = !empty($this->idUsuarioExterno) ? $this->idUsuarioExterno : $this->idUsuarioInterno;

        $PermissoesGrupo = [];
        if (!empty($this->idUsuarioInterno)) {
            $Usuario = new Autenticacao_Model_DbTable_Usuario();
            $grupos = $usuarioDAO->buscarUnidades($this->idUsuarioInterno, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
        }

        !empty($this->idUsuarioInterno) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }
}
