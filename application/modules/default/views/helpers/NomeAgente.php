<?php
/**
 * Classe para pegar o nome do agente
 * @author Equipe RUP - Politec
 * @since 08/08/2011
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_NomeAgente
{
    /**
     * Nome do agente
     * @access public
     * @param string $valor
     * @return void
     */
    public function nomeAgente($idAgente)
    {
        if (!empty($idAgente)) {
            $Nome = new Nomes();
            $buscar = $Nome->buscar(array('idAgente = ?' => $idAgente));
            if (count($buscar) > 0) {
                return $buscar[0]->Descricao;
            } else {
                return 'Usuário não encontrado.';
            }
        } else {
            return ;
        }
    } // fecha método formatarMilhar()
} // fecha class
