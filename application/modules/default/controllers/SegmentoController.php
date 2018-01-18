<?php
/**
 * Class SegmentoController
 *
 * @name SegmentoController
 * @package default
 * @subpackage controllers
 * @version $Id$
 *
 * @author Emanuel Sampaio <emanuelonline@gmail.com>
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 24/04/2012
 *
 * @link http://salic.cultura.gov.br
 */
class SegmentoController extends Zend_Controller_Action
{
    /**
     * Metodo para buscar os segmentos de uma area
     * Busca como XML para o AJAX
     *
     * @name comboAction
     * @param void
     * @return void
     *
     * @access public
     *
     * @since  17/08/2016
     */
    public function comboAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe o idUF via post
        $post = Zend_Registry::get('post');
        $idArea = (int) $post->id;
        $idSegmento = isset($post->segmento) ? $post->segmento : 0;

        // integracao MODELO e VISAO
        $Segmento = new Segmento();
        $resultado = $Segmento->combo(array("a.codigo = '?'" => $idArea), array('s.segmento ASC'));
        if (count($resultado)>0) {
            $html = '<option value=""> - Selecione - </option>';
            foreach ($resultado as $value) {
                if ($idSegmento > 0 && $idSegmento == $value->id) {
                    $html = $html.'<option data-tp_enquadramento="'.$value->tp_enquadramento.'" value="'.$value->id.'" selected="selected">'.($value->descricao).'</option>';
                } else {
                    $html = $html.'<option data-tp_enquadramento="'.$value->tp_enquadramento.'" value="'.$value->id.'">'.utf8_encode($value->descricao).'</option>';
                }
            }
            echo $html;
        } else {
            echo '<option value=""> - Selecione - </option>';
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
