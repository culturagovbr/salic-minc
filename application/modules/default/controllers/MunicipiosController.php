<?php

class MunicipiosController extends Zend_Controller_Action
{
    /**
     * M�todo para buscar as cidades de um estado
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

        // integra��o MODELO e VIS�O
        $Municipios = new Municipios();
        $this->view->combocidades = $Municipios->combo(array('idUFIBGE = ?' => $id));
    } // fecha comboAction()


    public function comboAjaxAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        // recebe o idUF via post
        $post = Zend_Registry::get('post');
        $id = (int) $post->id;

        // integra��o MODELO e VIS�O
        $Municipios = new Municipios();
        $result = $Municipios->combo(array('idUFIBGE = ?' => $id));

        $a = 0;
        if (count($result) > 0) {
            foreach ($result as $registro) {
                $arrayMunicipios[$a]['id'] = $registro['id'];
                $arrayMunicipios[$a]['descricao'] = utf8_encode($registro['descricao']);
                $a++;
            }
            $jsonEncode = json_encode($arrayMunicipios);
            $this->_helper->json(array('resposta'=>true,'conteudo'=>$arrayMunicipios));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    } // fecha comboAction()
}
