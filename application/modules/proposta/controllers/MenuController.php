<?php


class Proposta_MenuController extends Proposta_GenericController
{

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->isEditarProposta = $this->isEditarProposta($this->idPreProjeto);
        $this->isEditarProjeto = $this->isEditarProjeto($this->idPreProjeto);
        $this->isEditavel = $this->isEditavel($this->idPreProjeto);
    }

    public function menuAction()
    {

        $this->view->arrMenuProponente = self::gerarArrayMenu($this->idPreProjeto);

    }

    private function gerarArrayMenu($idPreProjeto)
    {

        $arrMenuProponente = array();
        $arrMenuProponente['principal'] = array(
            'id' => 'informacaoinicial',
            'label' => 'Informa&ccedil;&otilde;es iniciais',
            'title' => '',
            'link' => '',
            'menu' => array(),
            'grupo' => array()
        );
        $arrMenuProponente['principal']['menu'][] = array('label' => 'Identifica&ccedil&atilde;o da proposta', 'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'identificacaodaproposta', 'idPreProjeto' => $idPreProjeto),
            'grupo' => array()
        );
        $arrMenuProponente['principal']['menu'][] = array('label' => 'Responsabilidade Social', 'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'responsabilidadesocial', 'idPreProjeto' => $idPreProjeto),
            'grupo' => array()
        );
        $arrMenuProponente['principal']['menu'][] = array('label' => 'Detalhes t&eacute;cnicos', 'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'detalhestecnicos', 'idPreProjeto' => $idPreProjeto),
            'grupo' => array()
        );
        $arrMenuProponente['principal']['menu'][] = array('label' => 'Outras informa&ccedil;&otilde;es', 'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'outrasinformacoes', 'idPreProjeto' => $idPreProjeto),
            'grupo' => array()
        );

        $arrMenuProponente['localderealizacoes'] = array(
            'id' => 'localderealizacoes',
            'label' => 'Local de realiza&ccedil;&atilde;o',
            'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'localderealizacao', 'action' => 'index', 'idPreProjeto' => $idPreProjeto),
            'menu' => array(),
            'grupo' => array()
        );

        $arrMenuProponente['planodedistribuicao'] = array(
            'id' => 'planodedistribuicao',
            'label' => 'Plano de distribui&ccedil;&atilde;o',
            'title' => 'Ir para plano de distribui&ccedil;&atilde;o',
            'link' => array('module' => 'proposta', 'controller' => 'plano-distribuicao', 'action' => 'index', 'idPreProjeto' => $idPreProjeto),
            'menu' => array(),
            'grupo' => array()
        );

        $arrMenuProponente['planilhaorcamentaria'] = array(
            'id' => 'planodedistribuicao',
            'label' => 'Or&ccedil;amento do projeto',
            'title' => '',
            'link' => '',
            'menu' => array(),
            'grupo' => array()
        );
        $arrMenuProponente['planilhaorcamentaria']['menu'][] = array('label' => 'Custos Vinculados', 'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'manterorcamento', 'action' => 'custosvinculados', 'idPreProjeto' => $idPreProjeto),
            'grupo' => array()
        );
        $arrMenuProponente['planilhaorcamentaria']['menu'][] = array('label' => 'Custos por produtos', 'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'manterorcamento', 'action' => 'produtoscadastrados', 'idPreProjeto' => $idPreProjeto),
            'grupo' => array()
        );
        $arrMenuProponente['planilhaorcamentaria']['menu'][] = array('label' => 'Visualizar planilha', 'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'manterorcamento', 'action' => 'planilhaorcamentariageral', 'idPreProjeto' => $idPreProjeto),
            'grupo' => array()
        );

        $arrMenuProponente['itensorcamentario'] = array(
            'id' => 'itensorcamentario',
            'label' => 'Itens or&ccedil;ament&aacute;rios',
            'title' => '',
            'link' => '',
            'menu' => array(),
            'grupo' => array()
        );
        $arrMenuProponente['itensorcamentario']['menu'][] = array('label' => 'Solicitar inclus&atilde;o de itens', 'title' => 'Ir para Solicitar inclus&atilde;o de itens',
            'link' => array('module' => 'proposta', 'controller' => 'mantertabelaitens', 'action' => 'index', 'idPreProjeto' => $idPreProjeto),
            'grupo' => array()
        );
        $arrMenuProponente['itensorcamentario']['menu'][] = array('label' => 'Minhas solicita&ccedil;&otilde;es', 'title' => 'Ir para Minhas solicita&ccedil;&otilde;es',
            'link' => array('module' => 'proposta', 'controller' => 'mantertabelaitens', 'action' => 'minhas-solicitacoes', 'idPreProjeto' => $idPreProjeto, 'tipoFiltro' => 'solicitado'),
            'grupo' => array()
        );

        $arrMenuProponente['anexardocumentos'] = array(
            'id' => 'anexardocumentos',
            'label' => 'Anexar documentos',
            'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'manterpropostaedital', 'action' => 'enviararquivoedital', 'idPreProjeto' => $idPreProjeto),
            'menu' => array(),
            'grupo' => array()
        );

        $arrMenuProponente['imprimir'] = array(
            'id' => 'imprimir',
            'label' => 'Gerar PDF',
            'title' => '',
            'link' => array('module' => 'admissibilidade', 'controller' => 'admissibilidade', 'action' => 'imprimirpropostacultural', 'idPreProjeto' => $idPreProjeto),
            'menu' => array(),
            'grupo' => array()
        );

        $arrMenuProponente['mensagensenviadas'] = array(
            'id' => 'mensagensenviadas',
            'label' => 'Dilig&ecirc;ncias',
            'title' => '',
            'link' => array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaproponente', 'idPreProjeto' => $idPreProjeto),
            'menu' => array(),
            'grupo' => array()
        );

        $arrMenuProponente['solicitacoes'] = array(
            'id' => 'minhassolicitacoes',
            'label' => 'Minhas solicita&ccedil;&otilde;es',
            'title' => '',
            'link' => array('module' => 'solicitacao', 'controller' => 'mensagem', 'action' => 'index', 'idPreProjeto' => $idPreProjeto),
            'menu' => array(),
            'grupo' => array()
        );

        if ($this->isEditavel) {

            if (!$this->isEditarProjeto) {

                $arrMenuProponente['excluirproposta'] = array(
                    'id' => 'excluirproposta',
                    'label' => 'Excluir proposta',
                    'title' => '',
                    'link' => '',
                    'menu' => array(),
                    'grupo' => array()
                );

                $arrMenuProponente['enviarproposta'] = array(
                    'id' => 'enviarproposta',
                    'label' => 'Enviar proposta ao MinC',
                    'title' => '',
                    'link' => array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'enviar-proposta', 'idPreProjeto' => $idPreProjeto),
                    'menu' => array(),
                    'grupo' => array()
                );
            } else {
                $arrMenuProponente['encaminharprojetoaominc'] = array(
                    'id' => 'encaminharprojetoaominc',
                    'label' => 'Encaminhar projeto ao MinC',
                    'title' => '',
                    'link' => array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'encaminharprojetoaominc', 'idPreProjeto' => $idPreProjeto),
                    'menu' => array(),
                    'grupo' => array()
                );
            }

        }

        return $arrMenuProponente;
    }

    public function contagemRegressivaSegundos($datainicial = null, $prazo = null)
    {
        $datafinal = "NOW";

        $datainicial = strtotime($datainicial . "+ " . $prazo . " day");
        $datafinal = strtotime($datafinal) + 24 * 3600;
        $segundos = $datainicial - $datafinal;

        return $segundos;
    }


}