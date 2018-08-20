<?php

class Projeto_IncentivoController extends Projeto_GenericController
{
    protected $projeto;
    protected $idPronac;

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
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

    public function obterProjetoAjaxAction()
    {
        $httpCode = 200;
        $data = [];
        try {

            if (empty($this->idPronac)) {
                throw new Exception('Pronac &eacute; obrigat&oacute;rio!');
            }

            $permissao = $this->verificarPermissaoAcesso(false, true, false, true);

            if (!$permissao['status']) {
                $data['permissao'] = false;
                $httpCode = 203;
                throw new Exception('Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar este projeto');
            }

            $dbTableProjetos = new Projeto_Model_DbTable_Projetos();
            $projetoCompleto = $dbTableProjetos->obterProjetoIncentivoCompleto($this->idPronac);

            $tbPreProjetoMeta = new Proposta_Model_PreProjetoMapper();
            $planilhaOriginal = $tbPreProjetoMeta->obterValorTotalPlanilhaPropostaCongelada($projetoCompleto->idPreProjeto);

            $data = array_map('utf8_encode', $projetoCompleto->toArray());
            $data['vlSolicitadoOriginal'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlSolicitadoOriginal'] : $data['vlSolicitadoOriginal'];
            $data['vlOutrasFontesPropostaOriginal'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlOutrasFontesPropostaOriginal'] : $data['vlOutrasFontesPropostaOriginal'];
            $data['vlTotalPropostaOriginal'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlTotalPropostaOriginal'] : $data['vlTotalPropostaOriginal'];

            $data['vlAutorizado'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlSolicitadoOriginal'] : $data['vlAutorizado'];
            $data['vlAutorizadoOutrasFontes'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlOutrasFontesPropostaOriginal'] : $data['vlAutorizadoOutrasFontes'];
            $data['vlTotalAutorizado'] = !empty($planilhaOriginal) ? $planilhaOriginal['vlTotalPropostaOriginal'] : $data['vlTotalAutorizado'];

            $data['permissao'] = true;
            $dbTableInabilitado = new Inabilitado();
            $proponenteInabilitado = $dbTableInabilitado->BuscarInabilitado($projetoCompleto->CgcCPf, null, null, true);;

            $Parecer = new Parecer();
            $parecerAnaliseCNIC = $Parecer->verificaProjSituacaoCNIC($projetoCompleto->Pronac);

            $data['ProponenteInabilitado'] = !empty($proponenteInabilitado);
            $data['EmAnaliseNaCNIC'] = (count($parecerAnaliseCNIC) > 0) ? true : false;
            $data['idUsuarioExterno'] = !empty($this->idUsuarioExterno) ? $this->idUsuarioExterno : false;

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
