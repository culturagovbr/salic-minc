<?php
/**
 * DAO vwDespacharProjeto
 * @since 01/03/2013
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2013 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class vwDespacharProjeto extends MinC_Db_Table_Abstract
{

    /* dados da tabela */
    protected $_banco  = 'SAC';
    protected $_schema = 'SAC';
    protected $_name   = 'vwDespacharProjeto';

    public function inserirTramitacao($idPronac, $idUnidade, $idUsuarioEmissor, $meDespacho)
    {
        $sql = "INSERT INTO ".$this->_banco.".dbo.".$this->_name."
                (idPronac,idUnidade,idUsuarioEmissor,meDespacho)
                VALUES ('$idPronac',$idUnidade,$idUsuarioEmissor,'$meDespacho')";
//
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
} // fecha class
