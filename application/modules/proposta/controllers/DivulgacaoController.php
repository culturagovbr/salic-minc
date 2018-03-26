<?php

/**
 * @name Proposta_DivulgacaoController
 * @package proposta
 * @subpackage controller
 * @link http://www.cultura.gov.br
 *
 * @author Equipe RUP - Politec
 * @author wouerner <wouerner@gmail.com>
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 15/12/2010
 */
class Proposta_DivulgacaoController extends Proposta_GenericController
{
    private $idUsuario = null;

    /**
     * @var Proposta_Model_DbTable_PlanoDeDivulgacao
     */
    public $table;

    /**
     * Reescreve o metodo init()
     *
     * @name init
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  21/09/2016
     * @deprecated Plano de divulgação foi removido da proposta em 23/11/2016
     */
    public function init()
    {
        parent::init();

        $this->table = new Proposta_Model_DbTable_PlanoDeDivulgacao();
    }

    /**
     * Redireciona para o fluxo inicial do sistema
     *
     * @name indexAction
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  21/09/2016
     * @deprecated Plano de divulgação foi removido da proposta em 23/11/2016
     */
    public function indexAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);
        $this->_redirect("/proposta/divulgacao/planodivulgacao?idPreProjeto=" . $this->idPreProjeto);
    }

    /**
     * @name planodivulgacaoAction
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  21/09/2016
     */
    public function planodivulgacaoAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);
        $table = new Proposta_Model_DbTable_PlanoDeDivulgacao();
        $this->view->itensDivulgacao = $table->buscar(array("pd.idprojeto = ?" => $this->idPreProjeto));
        $this->view->idPreProjeto = $this->idPreProjeto;
    }

    /**
     * @name consultarcomponenteAction
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  21/09/2016
     * @deprecated Plano de divulgação foi removido da proposta em 23/11/2016
     */
    public function consultarcomponenteAction()
    {
        $get = Zend_Registry::get('get');
        $idProjeto = $get->idPreProjeto;
        $this->_helper->layout->disableLayout();
        if (!empty($idProjeto) || $idProjeto == '0') {
            $dados = $this->table->buscarDigulgacao($idProjeto);
            $this->view->itensDivulgacao = $dados;
        } else {
            return false;
        }
    }

    /**
     * @name editardivulgacaoAction
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  21/09/2016
     * @deprecated Plano de divulgação foi removido da proposta em 23/11/2016
     */
    public function editardivulgacaoAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);
        $tableVerificacao = new Proposta_Model_DbTable_Verificacao();
        $idPlanoDivulgacao = $this->getRequest()->getParam('cod');
        $arrDivulgacao = $this->table->findBy($idPlanoDivulgacao);
        $this->view->itensplano = $tableVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idTipo' => 1), array('descricao'));
        $this->view->idpeca = $arrDivulgacao['idPeca'];
        $this->view->veiculo = $this->table->consultarVeiculo($arrDivulgacao['idPeca']);
        $this->view->idveiculo = $arrDivulgacao['idVeiculo'];
        $this->view->idDivulgacao = $idPlanoDivulgacao;
        $this->view->idPreProjeto = $this->idPreProjeto;
    }

    /**
     * @name novodivulgacaoAction
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  21/09/2016
     * @deprecated Plano de divulgação foi removido da proposta em 23/11/2016
     */
    public function novodivulgacaoAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);
        $tableVerificacao = new Proposta_Model_DbTable_Verificacao();
        $this->view->itensplano = $tableVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 1), array('descricao'));
        $this->view->veiculo = array();
        $this->view->idPreProjeto = $this->idPreProjeto;
    }

    /**
     * @name veiculoAction
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  21/09/2016
     * @deprecated Plano de divulgação foi removido da proposta em 23/11/2016
     */
    public function veiculoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $get = Zend_Registry::get('get');
        $idOpcao = $get->idOpcao;
        $veiculo = $get->veiculo;
        $options = $this->table->consultarVeiculo($idOpcao);
        $htmlOptions = "<option value=''> - Selecione - </option>";
        foreach ($options as $option) {
            $option = array_change_key_case($option);
            $htmlOptions .= "<option value='{$option['idverificacaoveiculo']}'";
            if ($veiculo == $option['idverificacaoveiculo']) {
                $htmlOptions .= ' selected="selected" ';
            }
            $htmlOptions .= ">" . $option['veiculodescicao'] . "</option>";
        }
        echo $htmlOptions;
    }

    /**
     * @name excluirdivulgacaoAction
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  15/08/2016
     * @deprecated Plano de divulgação foi removido da proposta em 23/11/2016
     */
    public function excluirdivulgacaoAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);
        $get = Zend_Registry::get('get');
        $idPreProjeto = $get->idPreProjeto;
        $idPlanoDivulgacao = $get->cod;
        $mapper = new Proposta_Model_PlanoDeDivulgacaoMapper();
        $mapper->delete($idPlanoDivulgacao);
        parent::message("Opera&ccedil;&atilde;o realizada com sucesso", "/proposta/divulgacao/planodivulgacao?idPreProjeto=" . $idPreProjeto, "CONFIRM");
    }

    /**
     * @name updatedivulgacaoAction
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  15/08/2016
     * @deprecated Plano de divulgação foi removido da proposta em 23/11/2016
     */
    public function updatedivulgacaoAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);
        $post = Zend_Registry::get('post');
        $idPreProjeto = $post->idPreProjeto;
        $peca = $post->peca;
        $veiculo = $post->veiculo;
        $idPlanoDivulgacao = $post->idDivulgacao;

        $dados = array(
            'idplanodivulgacao <> ?' => $idPlanoDivulgacao,
            'idprojeto' => $idPreProjeto,
            'idpeca' => $peca,
            'idveiculo' => $veiculo
        );

        $verifica = $this->table->findAll($dados);
        if ($verifica) {
            parent::message("Registro j&aacute; cadastrado, transa&ccedil;&atilde;o Cancelada!", "/proposta/divulgacao/planodivulgacao?idPreProjeto=" . $idPreProjeto, "ERROR");
        } else {
            $dados = array('idplanodivulgacao' => $idPlanoDivulgacao, 'idpeca' => $peca, 'idveiculo' => $veiculo);
            $mapper = new Proposta_Model_PlanoDeDivulgacaoMapper();
            $mapper->save(new Proposta_Model_PlanoDeDivulgacao($dados));
            parent::message("Opera&ccedil;&atilde;o realizada com sucesso", "/proposta/divulgacao/planodivulgacao?idPreProjeto=" . $idPreProjeto, "CONFIRM");
        }
    }

    /**
     * @name incluirdivulgacaoAction
     * @access public
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  15/08/2016
     * @deprecated Plano de divulgação foi removido da proposta em 23/11/2016
     */
    public function incluirdivulgacaoAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);
        $post = Zend_Registry::get('post');
        $idPreProjeto = $post->idPreProjeto;
        $idPeca = $post->peca;
        $idveiculo = $post->veiculo;
        $usuario = $this->idUsuario;
        $dados = array('idprojeto' => $idPreProjeto, 'idpeca' => $idPeca, 'idveiculo' => $idveiculo);
        $verifica = $this->table->findAll($dados);
        if ($verifica) {
            parent::message("Registro j&aacute; cadastrado, transa&ccedil;&atilde;o Cancelada!", "/proposta/divulgacao/planodivulgacao?idPreProjeto=" . $idPreProjeto, "ERROR");
        } else {
            $dados = array('idprojeto' => $idPreProjeto, 'idpeca' => $idPeca, 'idveiculo' => $idveiculo, 'usuario' => $usuario);
            $mapper = new Proposta_Model_PlanoDeDivulgacaoMapper();
            $mapper->save(new Proposta_Model_PlanoDeDivulgacao($dados));
            parent::message("Opera&ccedil;&atilde;o realizada com sucesso", "/proposta/divulgacao/planodivulgacao?idPreProjeto=" . $idPreProjeto, "CONFIRM");
        }
    }
}
