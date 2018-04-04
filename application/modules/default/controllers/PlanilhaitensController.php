<?php

class PlanilhaitensController extends Zend_Controller_Action
{
    /**
     * Método para buscar os itens de uma etapa
     * Busca como XML para o AJAX
     * @access public
     * @param void
     * @return void
     */
    public function comboAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe o idUF via post
        $post = Zend_Registry::get('post');
        $id = (int) $post->id;

        // integração MODELO e VISÃO
        $tbPlanilhaItens = new PlanilhaItens();
        $this->view->comboplanilha = $tbPlanilhaItens->combo(array('tipp.idPlanilhaEtapa = ?' => $id), array('tpi.Descricao ASC'));
    } // fecha comboAction()

    public function comboComIdProdutoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe o idUF via post
        $post = Zend_Registry::get('post');
        $ids = explode(':', $post->id);
        $idPlanilhaEtapa = (int) $ids[0];
        $idProduto = (int) $ids[1];
        
        // integração MODELO e VISÃO
        $tbPlanilhaItens = new PlanilhaItens();
        $this->view->comboplanilha = $tbPlanilhaItens->combo(array('tipp.idPlanilhaEtapa = ?' => $idPlanilhaEtapa), array('tpi.Descricao ASC'));
    } // fecha comboAction()
}
