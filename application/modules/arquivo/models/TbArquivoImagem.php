<?php

class Arquivo_Model_TbArquivoImagem extends MinC_Db_Model
{
    protected $_idArquivo;
    protected $_biArquivo;
    protected $_idArquivoImagem;
    
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
    public function getBiArquivo()
    {
        return $this->_biArquivo;
    }

    /**
     * @param mixed $biArquivo
     */
    public function setBiArquivo($biArquivo)
    {
        $this->_biArquivo = $biArquivo;
    }


}
