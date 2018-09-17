<?php
class Projeto_ProjetoController extends Projeto_GenericController
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

            $dbTableProjetos = new Projeto_Model_DbTable_Projetos();
            $projeto = $dbTableProjetos->buscarProjeto(['p.IdPRONAC = ?' => $this->idPronac]);

            $projeto = count($projeto) > 0 ? $projeto->current()->toArray() : [];

            $data = array_map('utf8_encode', $projeto);

            $data['permissao'] = true;
            if (!$permissao['status']) {
                $data['permissao'] = false;
                $httpCode = 203;
                throw new Exception('Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar este projeto');
            }

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

    public function indexAction()
    {
    }

    public function verificarIn2017Action()
    {
        $idPronac = $this->getRequest()->getParam('idPronac');

        $tbProjetos = new Projeto_Model_DbTable_Projetos();
        $IN2017 = $tbProjetos->verificarIN2017($idPronac);

        $this->_helper->json(['idPronac' => $idPronac, 'IN2017' => $IN2017]);
    }
}
