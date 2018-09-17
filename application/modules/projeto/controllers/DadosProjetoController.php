<?php
class Projeto_DadosProjetoController extends Projeto_GenericController
{

    protected $idPronac;

    public function init()
    {
        parent::init();

        $this->idPronac = $this->_request->getParam("idPronac");

        if (strlen($this->idPronac) > 7) {
            $this->idPronac = Seguranca::dencrypt($this->idPronac);
        }

        if ($this->idPronac) {
            $this->view->idPronac = $this->idPronac;
            $this->view->idPronacHash = Seguranca::encrypt($this->idPronac);
            $this->view->urlMenu = [
                'module' => 'projeto',
                'controller' => 'menu',
                'action' => 'obter-menu-ajax',
                'idPronac' => $this->view->idPronacHash
            ];

            if ($this->idUsuarioExterno) {
                $proj = new Projetos();
                $this->projeto = $proj->buscar(array('IdPRONAC = ?' => $this->idPronac))->current();

                if (empty($this->projeto)) {
                    parent::message("Nenhum projeto encontrado com o n&uacute;mero de Pronac informado.", "listarprojetos/listarprojetos", "ERROR");
                }
            }
        }
    }

    public function getAction()
    {
        $httpCode = 200;
        $data = [];
        try {

            if (empty($this->idPronac)) {
                throw new Exception('Pronac &eacute; obrigat&oacute;rio!');
            }

            $permissao = $this->verificarPermissaoAcesso(false, true, false, true);

            $data['permissao'] = true;
            if (!$permissao['status']) {
                $data['permissao'] = false;
                $httpCode = 203;
                throw new Exception('Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar este projeto');
            }

            $dbTableProjetos = new Projeto_Model_TbProjetosMapper();
            $projeto = $dbTableProjetos->obterProjetoCompleto($this->idPronac);

            $data = array_merge($data, $projeto);

            $this->autenticacao = array_change_key_case((array)\Zend_Auth::getInstance()->getIdentity());

            $data['isProponente'] = isset($this->autenticacao['usu_codigo']) ? false : true;

            $data = array_map('utf8_encode', $data);

            $this->getResponse()->setHttpResponseCode($httpCode);
            $this->_helper->json(
                [
                    'data' => $data,
                    'success' => 'true'
                ]
            );
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode($httpCode);
            $this->_helper->json(array('data' => $data, 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }
}
