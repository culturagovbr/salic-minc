<?php

class vwDespacharProjeto extends MinC_Db_Table_Abstract
{

    /* dados da tabela */
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'vwDespacharProjeto';

    public function inserirTramitacao($idPronac, $idUnidade, $idUsuarioEmissor, $meDespacho)
    {
        $sql = "INSERT INTO " . $this->_schema . "." . $this->_name . "
                (idPronac,idUnidade,idUsuarioEmissor,meDespacho)
                VALUES ('$idPronac',$idUnidade,$idUsuarioEmissor,'$meDespacho')";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
