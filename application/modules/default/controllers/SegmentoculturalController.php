<?php
/**
 * SegmentoculturalController
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class SegmentoculturalController extends Zend_Controller_Action
{
    /**
     * M�todo para buscar os segmentos culturais de uma �rea
     * @param void
     * @return void
     */
    public function segmentoculturalAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe o id via post
        $post = Zend_Registry::get('post');
        $id = (int) $post->id;

        $objSegmentocultural = new Segmentocultural();
        $this->view->segmentosculturais = $objSegmentocultural->buscarSegmento($id);
    }



    /**
     * M�todo para buscar os segmentos culturais de uma �rea
     * Busca como XML para o AJAX
     * @access public
     * @param void
     * @return void
     */
    public function comboAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $id = (int) $post->id;

        $objSegmentocultural = new Segmentocultural();
        $this->view->combosegmentosculturais = $objSegmentocultural->buscarSegmento($id);
    }

    /**
     * M�todo para buscar os segmentos culturais de uma �rea
     * Busca como XML para o AJAX
     * @access public
     * @param void
     * @return void
     */
    public function comboZAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe o id via post
        $post = Zend_Registry::get('post');
        $id = (int) $post->id;

        // integra��o MODELO e VIS�O
        $Segmento = new Segmento();
        $this->view->combosegmentosculturais = $Segmento->combo(array('a.Codigo = ?' => $id), array('s.Descricao ASC'));
    } // fecha comboAction()
}
