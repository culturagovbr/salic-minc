<?php
class Proposta_Model_TbDetalhaPlanoDistribuicao extends MinC_Db_Model
{

    protected $_idDetalhaPlanoDistribuicao;
    protected $_idPlanoDistribuicao;
    protected $_idUF;
    protected $_idMunicipio;
    protected $_dsProduto;
    protected $_qtExemplares;
    protected $_qtGratuitaDivulgacao;
    protected $_qtGratuitaPatrocinador;
    protected $_qtGratuitaPopulacao;
    protected $_qtPopularIntegral;
    protected $_qtPopularParcial;
    protected $_vlUnitarioPopularIntegral;
    protected $_vlReceitaPopularIntegral;
    protected $_vlReceitaPopularParcial;
    protected $_qtProponenteIntegral;
    protected $_qtProponenteParcial;
    protected $_vlUnitarioProponenteIntegral;
    protected $_vlReceitaProponenteIntegral;
    protected $_vlReceitaProponenteParcial;
    protected $_vlReceitaPrevista;
    protected $_tpVenda;
    protected $_tpLocal;
    protected $_tpEspaco;

    /**
     * @return mixed
     */
    public function getIdDetalhaPlanoDistribuicao()
    {
        return $this->_idDetalhaPlanoDistribuicao;
    }

    /**
     * @param mixed $idDetalhaPlanoDistribuicao
     */
    public function setIdDetalhaPlanoDistribuicao($idDetalhaPlanoDistribuicao)
    {
        $this->_idDetalhaPlanoDistribuicao = $idDetalhaPlanoDistribuicao;
    }

    /**
     * @return mixed
     */
    public function getIdPlanoDistribuicao()
    {
        return $this->_idPlanoDistribuicao;
    }

    /**
     * @param mixed $idPlanoDistribuicao
     */
    public function setIdPlanoDistribuicao($idPlanoDistribuicao)
    {
        $this->_idPlanoDistribuicao = $idPlanoDistribuicao;
    }

    /**
     * @return mixed
     */
    public function getIdUF()
    {
        return $this->_idUF;
    }

    /**
     * @param mixed $idUF
     */
    public function setIdUF($idUF)
    {
        $this->_idUF = $idUF;
    }

    /**
     * @return mixed
     */
    public function getIdMunicipio()
    {
        return $this->_idMunicipio;
    }

    /**
     * @param mixed $idMunicipio
     */
    public function setIdMunicipio($idMunicipio)
    {
        $this->_idMunicipio = $idMunicipio;
    }

    /**
     * @return mixed
     */
    public function getDsProduto()
    {
        return $this->_dsProduto;
    }

    /**
     * @param mixed $dsProduto
     */
    public function setDsProduto($dsProduto)
    {
        $this->_dsProduto = $dsProduto;
    }

    /**
     * @return mixed
     */
    public function getQtExemplares()
    {
        return $this->_qtExemplares;
    }

    /**
     * @param mixed $qtExemplares
     */
    public function setQtExemplares($qtExemplares)
    {
        $this->_qtExemplares = $qtExemplares;
    }

    /**
     * @return mixed
     */
    public function getQtGratuitaDivulgacao()
    {
        return $this->_qtGratuitaDivulgacao;
    }

    /**
     * @param mixed $qtGratuitaDivulgacao
     */
    public function setQtGratuitaDivulgacao($qtGratuitaDivulgacao)
    {
        $this->_qtGratuitaDivulgacao = $qtGratuitaDivulgacao;
    }

    /**
     * @return mixed
     */
    public function getQtGratuitaPatrocinador()
    {
        return $this->_qtGratuitaPatrocinador;
    }

    /**
     * @param mixed $qtGratuitaPatrocinador
     */
    public function setQtGratuitaPatrocinador($qtGratuitaPatrocinador)
    {
        $this->_qtGratuitaPatrocinador = $qtGratuitaPatrocinador;
    }

    /**
     * @return mixed
     */
    public function getQtGratuitaPopulacao()
    {
        return $this->_qtGratuitaPopulacao;
    }

    /**
     * @param mixed $qtGratuitaPopulacao
     */
    public function setQtGratuitaPopulacao($qtGratuitaPopulacao)
    {
        $this->_qtGratuitaPopulacao = $qtGratuitaPopulacao;
    }

    /**
     * @return mixed
     */
    public function getQtPopularIntegral()
    {
        return $this->_qtPopularIntegral;
    }

    /**
     * @param mixed $qtPopularIntegral
     */
    public function setQtPopularIntegral($qtPopularIntegral)
    {
        $this->_qtPopularIntegral = $qtPopularIntegral;
    }

    /**
     * @return mixed
     */
    public function getQtPopularParcial()
    {
        return $this->_qtPopularParcial;
    }

    /**
     * @param mixed $qtPopularParcial
     */
    public function setQtPopularParcial($qtPopularParcial)
    {
        $this->_qtPopularParcial = $qtPopularParcial;
    }

    /**
     * @return mixed
     */
    public function getVlUnitarioPopularIntegral()
    {
        return $this->_vlUnitarioPopularIntegral;
    }

    /**
     * @param mixed $vlUnitarioPopularIntegral
     */
    public function setVlUnitarioPopularIntegral($vlUnitarioPopularIntegral)
    {
        $this->_vlUnitarioPopularIntegral = $vlUnitarioPopularIntegral;
    }

    /**
     * @return mixed
     */
    public function getVlReceitaPopularIntegral()
    {
        return $this->_vlReceitaPopularIntegral;
    }

    /**
     * @param mixed $vlReceitaPopularIntegral
     */
    public function setVlReceitaPopularIntegral($vlReceitaPopularIntegral)
    {
        $this->_vlReceitaPopularIntegral = $vlReceitaPopularIntegral;
    }

    /**
     * @return mixed
     */
    public function getVlReceitaPopularParcial()
    {
        return $this->_vlReceitaPopularParcial;
    }

    /**
     * @param mixed $vlReceitaPopularParcial
     */
    public function setVlReceitaPopularParcial($vlReceitaPopularParcial)
    {
        $this->_vlReceitaPopularParcial = $vlReceitaPopularParcial;
    }

    /**
     * @return mixed
     */
    public function getQtProponenteIntegral()
    {
        return $this->_qtProponenteIntegral;
    }

    /**
     * @param mixed $qtProponenteIntegral
     */
    public function setQtProponenteIntegral($qtProponenteIntegral)
    {
        $this->_qtProponenteIntegral = $qtProponenteIntegral;
    }

    /**
     * @return mixed
     */
    public function getQtProponenteParcial()
    {
        return $this->_qtProponenteParcial;
    }

    /**
     * @param mixed $qtProponenteParcial
     */
    public function setQtProponenteParcial($qtProponenteParcial)
    {
        $this->_qtProponenteParcial = $qtProponenteParcial;
    }

    /**
     * @return mixed
     */
    public function getVlUnitarioProponenteIntegral()
    {
        return $this->_vlUnitarioProponenteIntegral;
    }

    /**
     * @param mixed $vlUnitarioProponenteIntegral
     */
    public function setVlUnitarioProponenteIntegral($vlUnitarioProponenteIntegral)
    {
        $this->_vlUnitarioProponenteIntegral = $vlUnitarioProponenteIntegral;
    }

    /**
     * @return mixed
     */
    public function getVlReceitaProponenteIntegral()
    {
        return $this->_vlReceitaProponenteIntegral;
    }

    /**
     * @param mixed $vlReceitaProponenteIntegral
     */
    public function setVlReceitaProponenteIntegral($vlReceitaProponenteIntegral)
    {
        $this->_vlReceitaProponenteIntegral = $vlReceitaProponenteIntegral;
    }

    /**
     * @return mixed
     */
    public function getVlReceitaProponenteParcial()
    {
        return $this->_vlReceitaProponenteParcial;
    }

    /**
     * @param mixed $vlReceitaProponenteParcial
     */
    public function setVlReceitaProponenteParcial($vlReceitaProponenteParcial)
    {
        $this->_vlReceitaProponenteParcial = $vlReceitaProponenteParcial;
    }

    /**
     * @return mixed
     */
    public function getVlReceitaPrevista()
    {
        return $this->_vlReceitaPrevista;
    }

    /**
     * @param mixed $vlReceitaPrevista
     */
    public function setVlReceitaPrevista($vlReceitaPrevista)
    {
        $this->_vlReceitaPrevista = $vlReceitaPrevista;
    }

    /**
     * @return mixed
     */
    public function getTpVenda()
    {
        return $this->_tpVenda;
    }

    /**
     * @param mixed $tpVenda
     */
    public function setTpVenda($tpVenda)
    {
        $this->_tpVenda = $tpVenda;
    }

    /**
     * @return mixed
     */
    public function getTpLocal()
    {
        return $this->_tpLocal;
    }

    /**
     * @param mixed $tpLocal
     */
    public function setTpLocal($tpLocal)
    {
        $this->_tpLocal = $tpLocal;
    }

    /**
     * @return mixed
     */
    public function getTpEspaco()
    {
        return $this->_tpEspaco;
    }

    /**
     * @param mixed $tpEspaco
     */
    public function setTpEspaco($tpEspaco)
    {
        $this->_tpEspaco = $tpEspaco;
    }



}
