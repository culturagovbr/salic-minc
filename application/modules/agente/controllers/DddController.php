<?php
/**
 * DddController
 * @author Equipe RUP - Politec
 * @author wouerner <wouerner@gmail.com>
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Agente_DddController extends Zend_Controller_Action
{
    /**
     * M�todo para buscar os ddds de um estado
     * @param void
     * @return void
     */
    public function dddAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe o id via post
        $post = Zend_Registry::get('post');
        $id = (int) $post->id;

        // integra��o MODELO e VIS�O
        $this->view->ddds = Ddd::buscar($id);
    }



    /**
     * M�todo para buscar os ddds de um estado
     * Busca como XML para o AJAX
     * @access public
     * @param void
     * @return void
     */
    public function comboAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe o id via post
        $post = Zend_Registry::get('post');
        $id = (int) $post->id;

        $dddMapper = new Agente_Model_DDDMapper();
        $this->view->comboddds = $dddMapper->fetchPairs('Codigo', 'Codigo', array('idUF' => $id));
    }


    /**
     * M�todo para buscar os ddds de um estado
     * Busca como XML para o AJAX
     * @access public
     * @param void
     * @return void
     */
    public function comboSalvarTelefoneAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe o id via post
        $post = Zend_Registry::get('post');
        $id = (int) $post->id;

        // integra��o MODELO e VIS�O
        $arrayDados = Ddd::buscar($id);
        $dados = array();
        $i = 0;
        if (count($arrayDados) > 0) {
            foreach ($arrayDados as $value) {
                $dados[$i]['id'] = $value->id;
                $dados[$i]['descricao'] = $value->descricao;
                $i++;
            }
            $jsonEncode = json_encode($dados);
            $this->_helper->json(array('resposta'=>true,'conteudo'=>$dados));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
