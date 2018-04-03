<?php

class Arquivo_Model_TbDocumento extends MinC_Db_Model
{
    protected $_idTipoDocumento;
    protected $_idDocumento;
    protected $_idArquivo;
    protected $_dsDocumento;
    protected $_dtEmissaoDocumento;
    protected $_dtValidadeDocumento;
    protected $_idTipoEventoOrigem;
    protected $_nmTitulo;
    protected $_nrDocumento;

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
    public function getIdDocumento()
    {
        return $this->_idDocumento;
    }

    /**
     * @param mixed $idDocumento
     */
    public function setIdDocumento($idDocumento)
    {
        $this->_idDocumento = $idDocumento;
    }

    /**
     * @return mixed
     */
    public function getIdArquivo()
    {
        return $this->_idArquivo;
    }

    /**
     * @param mixed $idArquivo
     */
    public function setIdArquivo($idArquivo)
    {
        $this->_idArquivo = $idArquivo;
    }

    /**
     * @return mixed
     */
    public function getDsDocumento()
    {
        return $this->_dsDocumento;
    }

    /**
     * @param mixed $dsDocumento
     */
    public function setDsDocumento($dsDocumento)
    {
        $this->_dsDocumento = $dsDocumento;
    }

    /**
     * @return mixed
     */
    public function getDtEmissaoDocumento()
    {
        return $this->_dtEmissaoDocumento;
    }

    /**
     * @param mixed $dtEmissaoDocumento
     */
    public function setDtEmissaoDocumento($dtEmissaoDocumento)
    {
        $this->_dtEmissaoDocumento = $dtEmissaoDocumento;
    }

    /**
     * @return mixed
     */
    public function getDtValidadeDocumento()
    {
        return $this->_dtValidadeDocumento;
    }

    /**
     * @param mixed $dtValidadeDocumento
     */
    public function setDtValidadeDocumento($dtValidadeDocumento)
    {
        $this->_dtValidadeDocumento = $dtValidadeDocumento;
    }

    /**
     * @return mixed
     */
    public function getIdTipoEventoOrigem()
    {
        return $this->_idTipoEventoOrigem;
    }

    /**
     * @param mixed $idTipoEventoOrigem
     */
    public function setIdTipoEventoOrigem($idTipoEventoOrigem)
    {
        $this->_idTipoEventoOrigem = $idTipoEventoOrigem;
    }

    /**
     * @return mixed
     */
    public function getNmTitulo()
    {
        return $this->_nmTitulo;
    }

    /**
     * @param mixed $nmTitulo
     */
    public function setNmTitulo($nmTitulo)
    {
        $this->_nmTitulo = $nmTitulo;
    }

    /**
     * @return mixed
     */
    public function getNrDocumento()
    {
        return $this->_nrDocumento;
    }

    /**
     * @param mixed $nrDocumento
     */
    public function setNrDocumento($nrDocumento)
    {
        $this->_nrDocumento = $nrDocumento;
    }


}
