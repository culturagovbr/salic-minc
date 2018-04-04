<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatusLocalRealizacaoProjeto
 *
 * @author 01129075125
 */
class Zend_View_Helper_StatusLocalRealizacaoProjeto
{
    public function StatusLocalRealizacaoProjeto($status)
    {
        $status = $status == 'I' ? 'Inclusão' : 'Exclusão';

        return $status;
    }
}
