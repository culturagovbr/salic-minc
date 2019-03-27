<?php

class Assinatura_Model_TbAtoAdministrativoMetodoEncaminhamento extends MinC_Db_Model
{
    protected $_idAtoAdministrativoMetodoEncaminhamento;
    protected $_idTipoDoAto;
    protected $_encaminhaProjeto;
    
    /**
     * @return mixed
     */
    public function getIdTipoDoAto()
    {
        return $this->_idTipoDoAto;
    }

    /**
     * @return Assinatura_Model_TbAtoAdministrativoMetodoEncaminhamento
     */
    public function setIdTipoDoAto($idTipoDoAto)
    {
        $this->_idTipoDoAto = $idTipoDoAto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEncaminhaProjeto()
    {
        return $this->_encaminhaProjeto;
    }    
    
    /**
     * @return Assinatura_Model_TbAtoAdministrativoMetodoEncaminhamento
     */
    public function setEncaminhaProjeto($encaminhaProjeto)
    {
        $this->_encaminhaProjeto = $encaminhaProjeto;
        return $this;
    }    
}
