<?php
/**
 * Helper para verificar a uf do projeto atraves do proponente do mesmo
 * @author Equipe RUP - Politec
 * @since 24/11/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_UfProjeto
{
    /**
     * M�todo para verificar a uf do projeto
     * @access public
     * @param string $cnpjcpf
     * @return string
     */
    public function ufProjeto($cnpjcpf)
    {
        $Agentes = new Agente_Model_DbTable_Agentes();
        $buscarUF = $Agentes->buscarUfMunicioAgente(array('a.CNPJCPF = ?' => $cnpjcpf));
        return $buscarUF[0]['Sigla'];
    } // fecha m�todo ufProjeto()
} // fecha class
