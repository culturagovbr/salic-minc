<?php

class Arquivo_Model_TbTipoDocumento extends MinC_Db_Model
{
    protected $_idTipoDocumento;
    protected $_dsTipoDocumento;

    const TIPO_DOCUMENTO_ARQUIVO = 24;

    /**
     * @return mixed
     */
    public function getIdTipoDocumento()
    {
        return $this->_idTipoDocumento;
    }

    /**
     * @param mixed $idTipoDocumento
     */
    public function setIdTipoDocumento($idTipoDocumento)
    {
        $this->_idTipoDocumento = $idTipoDocumento;
    }

    /**
     * @return mixed
     */
    public function getDsTipoDocumento()
    {
        return $this->_dsTipoDocumento;
    }

    /**
     * @param mixed $dsTipoDocumento
     */
    public function setDsTipoDocumento($dsTipoDocumento)
    {
        $this->_dsTipoDocumento = $dsTipoDocumento;
    }



}
