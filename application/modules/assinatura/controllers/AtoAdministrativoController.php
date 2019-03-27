<?php

class Assinatura_AtoAdministrativoController extends Assinatura_GenericController
{
    private $grupoAtivo;

    public function init()
    {
        parent::init();

        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        parent::perfil(1, [ 97 ]);

        $this->cod_usuario = $this->auth->getIdentity()->usu_codigo;
    }

    public function indexAction()
    {
        $this->redirect("/{$this->moduleName}/ato-administrativo/gerir-atos-administrativos");
    }

    public function gerirAtosAdministrativosAction()
    {
        $objAtosAdministrativos = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $this->view->atosAdministrativos = $objAtosAdministrativos->obterAtoAdministrativoDetalhado();
    }

    public function removerAjaxAction()
    {
        try {
            $post = $this->getRequest()->getPost();

            $objModelAtoAdministrativo = new Assinatura_Model_TbAtoAdministrativo($post);
            if (!$objModelAtoAdministrativo->getIdAtoAdministrativo()) {
                throw new Exception("Identificador do ato administrativo n&atilde;o informado.");
            }

            $objAssinaturaDbTable = new Assinatura_Model_DbTable_TbAssinatura();
            $assinaturas = $objAssinaturaDbTable->findBy([
                'idAtoAdministrativo' => $objModelAtoAdministrativo->getIdAtoAdministrativo()
            ]);

            if ($assinaturas) {
                throw new Exception("Transa&ccedil;&atilde;o cancelada, Ato administrativo j&aacute; vinculado a um documento.");
            }

            $objAtoAdministrativoMapper = new Assinatura_Model_TbAtoAdministrativoMapper();
            if (!is_null($objAtoAdministrativoMapper->delete($objModelAtoAdministrativo->getIdAtoAdministrativo()))) {
                $this->_helper->json(['status' => 1]);
            }
        } catch (Exception $objException) {
            $this->_helper->json(['status' => 0, 'message' => $objException->getMessage()]);
        }
    }

    public function obterTiposDeAtosAdministrativosAjaxAction()
    {
        $objAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $arrayTiposAtosAdministrativos = $objAtoAdministrativo->obterTiposDeAtosAdministrativosAtivos();
        foreach ($arrayTiposAtosAdministrativos as $indice => $tipoAtoAdministrativo) {
            $arrayTiposAtosAdministrativos[$indice]['descricao'] = utf8_encode($tipoAtoAdministrativo['descricao']);
        }

        $this->_helper->json(
            ['resultado' => $arrayTiposAtosAdministrativos]
        );
    }

    public function obterCargosDoAssinanteAjaxAction()
    {
        $objAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $arrayResultado = $objAtoAdministrativo->obterCargosDoAssinante();
        foreach ($arrayResultado as $indice => $tipoAtoAdministrativo) {
            $arrayResultado[$indice]['descricao'] = utf8_encode($tipoAtoAdministrativo['descricao']);
        }

        $this->_helper->json(
            ['resultado' => $arrayResultado]
        );
    }

    public function obterOrgaosSuperioresAjaxAction()
    {
        $objAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $arrayResultado = $objAtoAdministrativo->obterOrgaosSuperiores();
        foreach ($arrayResultado as $indice => $tipoAtoAdministrativo) {
            $arrayResultado[$indice]['descricao'] = utf8_encode($tipoAtoAdministrativo['descricao']);
        }

        $this->_helper->json(
            ['resultado' => $arrayResultado]
        );
    }

    public function obterOrgaosDoAssinanteAjaxAction()
    {
        $arrayResultado = [];
        $get = $this->getRequest()->getParams();
        if ($get['idOrgaoSuperiorDoAssinante']) {
            $objAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $arrayResultado = $objAtoAdministrativo->obterOrgaos($get['idOrgaoSuperiorDoAssinante']);
            foreach ($arrayResultado as $indice => $tipoAtoAdministrativo) {
                $arrayResultado[$indice]['descricao'] = utf8_encode($tipoAtoAdministrativo['descricao']);
            }
        }
        $this->_helper->json(
            ['resultado' => $arrayResultado]
        );
    }

    public function obterPerfisDoAssinanteAjaxAction()
    {
        $objAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $arrayResultado = $objAtoAdministrativo->obterPerfisDoAssinante();
        foreach ($arrayResultado as $indice => $tipoAtoAdministrativo) {
            $arrayResultado[$indice]['descricao'] = utf8_encode($tipoAtoAdministrativo['descricao']);
        }

        $this->_helper->json(
            ['resultado' => $arrayResultado]
        );
    }

    public function obterOrdemAssinaturaAjaxAction()
    {
        $arrayResultado = [
            0 => [
                'codigo' => 1,
                'descricao' => 1
            ]
        ];
        $get = $this->getRequest()->getParams();
        if ($get['idTipoDoAto'] && $get['idOrgaoSuperiorDoAssinante']) {
            $objAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $objModelAtoAdministrativo = new Assinatura_Model_TbAtoAdministrativo();
            $objModelAtoAdministrativo->setIdTipoDoAto($get['idTipoDoAto']);
            $objModelAtoAdministrativo->setIdOrgaoSuperiorDoAssinante($get['idOrgaoSuperiorDoAssinante']);

            if ($get['idOrdemDaAssinatura']) {
                $objModelAtoAdministrativo->setIdOrdemDaAssinatura($get['idOrdemDaAssinatura']);
            }
            $arrayResultadoOrdens = $objAtoAdministrativo->obterOrdensAssinaturaDisponiveis($objModelAtoAdministrativo);
            if (count($arrayResultadoOrdens) > 1) {
                $arrayResultado = $arrayResultadoOrdens;
            }
        }
        $this->_helper->json(
            ['resultado' => $arrayResultado]
        );
    }

    public function alterarAjaxAction()
    {
        try {
            $post = $this->getRequest()->getPost();
            $objModelAtoAdministrativo = new Assinatura_Model_TbAtoAdministrativo($post);

            if (empty($objModelAtoAdministrativo->getIdTipoDoAto())
                || empty($objModelAtoAdministrativo->getIdOrgaoSuperiorDoAssinante())
                || empty($objModelAtoAdministrativo->getIdCargoDoAssinante())
                || empty($objModelAtoAdministrativo->getIdPerfilDoAssinante())
                || empty($objModelAtoAdministrativo->getIdOrgaoDoAssinante())
            ) {
                throw new Exception("Preencha todos os campos.");
            }

            $objAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $objAtoAdministrativoMapper = new Assinatura_Model_TbAtoAdministrativoMapper();

            $atoAdministrativoAtual = $objAtoAdministrativo->findBy([
                'idTipoDoAto' => $objModelAtoAdministrativo->getIdTipoDoAto(),
                'idOrgaoSuperiorDoAssinante' => $objModelAtoAdministrativo->getIdOrgaoSuperiorDoAssinante(),
                'idOrdemDaAssinatura' => $objModelAtoAdministrativo->getIdOrdemDaAssinatura()
            ]);
            $atoAdministrativoSubstituto = $objAtoAdministrativo->findBy([
                'idAtoAdministrativo' => $objModelAtoAdministrativo->getIdAtoAdministrativo()
            ]);

            if ($atoAdministrativoAtual) {
                $objModelAtoAdministrativoAtual = new Assinatura_Model_TbAtoAdministrativo($atoAdministrativoAtual);
                $objModelAtoAdministrativoAtual->setIdOrdemDaAssinatura($atoAdministrativoSubstituto['idOrdemDaAssinatura']);
                $objAtoAdministrativoMapper->save($objModelAtoAdministrativoAtual);
            }

            if (!is_null($objAtoAdministrativoMapper->save($objModelAtoAdministrativo))) {
                $this->_helper->json(['status' => 1]);
            }
        } catch (Exception $objException) {
            $this->_helper->json(['status' => 0, 'message' => $objException->getMessage()]);
        }
    }

    public function cadastrarAjaxAction()
    {
        try {
            $post = $this->getRequest()->getPost();

            if (!$post['idTipoDoAto'] || !$post['idOrgaoSuperiorDoAssinante'] || !$post['idCargoDoAssinante'] ||
                !$post['idPerfilDoAssinante'] || !$post['idOrgaoDoAssinante']) {
                throw new Exception("Preencha todos os campos.");
            }
            $objModelAtoAdministrativo = new Assinatura_Model_TbAtoAdministrativo($post);
            $objAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $servicoAtoAdministrativo = new \Application\Modules\Assinatura\Service\Assinatura\AtoAdministrativo();
            $grupo = $servicoAtoAdministrativo->obterGrupoAtoAdministrativoAtual(
                $post['idTipoDoAto'],
                $post['idPerfilDoAssinante'],
                $post['idOrgaoDoAssinante'],
                $post['idOrgaoSuperiorDoAssinante']
            );
            $objModelAtoAdministrativo->setGrupo($grupo);
            $proximaOrdemAssinatura = $objAtoAdministrativo->obterProximaOrdemDeAssinatura($objModelAtoAdministrativo);
            $objModelAtoAdministrativo->setIdOrdemDaAssinatura($proximaOrdemAssinatura);

            $objAtoAdministrativoMapper = new Assinatura_Model_TbAtoAdministrativoMapper();
            if (!is_null($objAtoAdministrativoMapper->save($objModelAtoAdministrativo))) {
                $this->_helper->json(['status' => 1]);
            }
        } catch (Exception $objException) {
            $this->_helper->json(['status' => 0, 'message' => $objException->getMessage()]);
        }
    }
}
