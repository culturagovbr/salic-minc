<?php

class Proposta_Model_PlanoDistribuicaoProduto extends MinC_Db_Model
{
    protected $_idPlanoDistribuicao;
    protected $_idProjeto;
    protected $_idProduto;
    protected $_Area;
    protected $_Segmento;
    protected $_idPosicaoDaLogo;
    protected $_QtdeProduzida;
    protected $_QtdePatrocinador;
    protected $_QtdeProponente;
    protected $_QtdeOutros;
    protected $_QtdeVendaNormal;
    protected $_QtdeVendaPromocional;
    protected $_PrecoUnitarioNormal;
    protected $_PrecoUnitarioPromocional;
    protected $_stPrincipal;
    protected $_Usuario;
    protected $_dsJustificativaPosicaoLogo;
    protected $_stPlanoDistribuicaoProduto;
    protected $_QtdeVendaPopularNormal;
    protected $_QtdeVendaPopularPromocional;
    protected $_vlUnitarioPopularNormal;
    protected $_ReceitaPopularPromocional;
    protected $_ReceitaPopularNormal;
    protected $_vlUnitarioNormal;
    protected $_vlReceitaTotalPrevista;

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
    public function getIdProjeto()
    {
        return $this->_idProjeto;
    }

    /**
     * @param mixed $idProjeto
     */
    public function setIdProjeto($idProjeto)
    {
        $this->_idProjeto = $idProjeto;
    }

    /**
     * @return mixed
     */
    public function getIdProduto()
    {
        return $this->_idProduto;
    }

    /**
     * @param mixed $idProduto
     */
    public function setIdProduto($idProduto)
    {
        $this->_idProduto = $idProduto;
    }

    /**
     * @return mixed
     */
    public function getArea()
    {
        return $this->_Area;
    }

    /**
     * @param mixed $Area
     */
    public function setArea($Area)
    {
        $this->_Area = $Area;
    }

    /**
     * @return mixed
     */
    public function getSegmento()
    {
        return $this->_Segmento;
    }

    /**
     * @param mixed $Segmento
     */
    public function setSegmento($Segmento)
    {
        $this->_Segmento = $Segmento;
    }

    /**
     * @return mixed
     */
    public function getIdPosicaoDaLogo()
    {
        return $this->_idPosicaoDaLogo;
    }

    /**
     * @param mixed $idPosicaoDaLogo
     */
    public function setIdPosicaoDaLogo($idPosicaoDaLogo)
    {
        $this->_idPosicaoDaLogo = $idPosicaoDaLogo;
    }

    /**
     * @return mixed
     */
    public function getQtdeProduzida()
    {
        return $this->_QtdeProduzida;
    }

    /**
     * @param mixed $QtdeProduzida
     */
    public function setQtdeProduzida($QtdeProduzida)
    {
        $this->_QtdeProduzida = $QtdeProduzida;
    }

    /**
     * @return mixed
     */
    public function getQtdePatrocinador()
    {
        return $this->_QtdePatrocinador;
    }

    /**
     * @param mixed $QtdePatrocinador
     */
    public function setQtdePatrocinador($QtdePatrocinador)
    {
        $this->_QtdePatrocinador = $QtdePatrocinador;
    }

    /**
     * @return mixed
     */
    public function getQtdeProponente()
    {
        return $this->_QtdeProponente;
    }

    /**
     * @param mixed $QtdeProponente
     */
    public function setQtdeProponente($QtdeProponente)
    {
        $this->_QtdeProponente = $QtdeProponente;
    }

    /**
     * @return mixed
     */
    public function getQtdeOutros()
    {
        return $this->_QtdeOutros;
    }

    /**
     * @param mixed $QtdeOutros
     */
    public function setQtdeOutros($QtdeOutros)
    {
        $this->_QtdeOutros = $QtdeOutros;
    }

    /**
     * @return mixed
     */
    public function getQtdeVendaNormal()
    {
        return $this->_QtdeVendaNormal;
    }

    /**
     * @param mixed $QtdeVendaNormal
     */
    public function setQtdeVendaNormal($QtdeVendaNormal)
    {
        $this->_QtdeVendaNormal = $QtdeVendaNormal;
    }

    /**
     * @return mixed
     */
    public function getQtdeVendaPromocional()
    {
        return $this->_QtdeVendaPromocional;
    }

    /**
     * @param mixed $QtdeVendaPromocional
     */
    public function setQtdeVendaPromocional($QtdeVendaPromocional)
    {
        $this->_QtdeVendaPromocional = $QtdeVendaPromocional;
    }

    /**
     * @return mixed
     */
    public function getPrecoUnitarioNormal()
    {
        return $this->_PrecoUnitarioNormal;
    }

    /**
     * @param mixed $PrecoUnitarioNormal
     */
    public function setPrecoUnitarioNormal($PrecoUnitarioNormal)
    {
        $this->_PrecoUnitarioNormal = $PrecoUnitarioNormal;
    }

    /**
     * @return mixed
     */
    public function getPrecoUnitarioPromocional()
    {
        return $this->_PrecoUnitarioPromocional;
    }

    /**
     * @param mixed $PrecoUnitarioPromocional
     */
    public function setPrecoUnitarioPromocional($PrecoUnitarioPromocional)
    {
        $this->_PrecoUnitarioPromocional = $PrecoUnitarioPromocional;
    }

    /**
     * @return mixed
     */
    public function getStPrincipal()
    {
        return $this->_stPrincipal;
    }

    /**
     * @param mixed $stPrincipal
     */
    public function setStPrincipal($stPrincipal)
    {
        $this->_stPrincipal = $stPrincipal;
    }

    /**
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->_Usuario;
    }

    /**
     * @param mixed $Usuario
     */
    public function setUsuario($Usuario)
    {
        $this->_Usuario = $Usuario;
    }

    /**
     * @return mixed
     */
    public function getDsJustificativaPosicaoLogo()
    {
        return $this->_dsJustificativaPosicaoLogo;
    }

    /**
     * @param mixed $dsJustificativaPosicaoLogo
     */
    public function setDsJustificativaPosicaoLogo($dsJustificativaPosicaoLogo)
    {
        $this->_dsJustificativaPosicaoLogo = $dsJustificativaPosicaoLogo;
    }

    /**
     * @return mixed
     */
    public function getStPlanoDistribuicaoProduto()
    {
        return $this->_stPlanoDistribuicaoProduto;
    }

    /**
     * @param mixed $stPlanoDistribuicaoProduto
     */
    public function setStPlanoDistribuicaoProduto($stPlanoDistribuicaoProduto)
    {
        $this->_stPlanoDistribuicaoProduto = $stPlanoDistribuicaoProduto;
    }

    /**
     * @return mixed
     */
    public function getQtdeVendaPopularNormal()
    {
        return $this->_QtdeVendaPopularNormal;
    }

    /**
     * @param mixed $QtdeVendaPopularNormal
     */
    public function setQtdeVendaPopularNormal($QtdeVendaPopularNormal)
    {
        $this->_QtdeVendaPopularNormal = $QtdeVendaPopularNormal;
    }

    /**
     * @return mixed
     */
    public function getQtdeVendaPopularPromocional()
    {
        return $this->_QtdeVendaPopularPromocional;
    }

    /**
     * @param mixed $QtdeVendaPopularPromocional
     */
    public function setQtdeVendaPopularPromocional($QtdeVendaPopularPromocional)
    {
        $this->_QtdeVendaPopularPromocional = $QtdeVendaPopularPromocional;
    }

    /**
     * @return mixed
     */
    public function getVlUnitarioPopularNormal()
    {
        return $this->_vlUnitarioPopularNormal;
    }

    /**
     * @param mixed $vlUnitarioPopularNormal
     */
    public function setVlUnitarioPopularNormal($vlUnitarioPopularNormal)
    {
        $this->_vlUnitarioPopularNormal = $vlUnitarioPopularNormal;
    }

    /**
     * @return mixed
     */
    public function getReceitaPopularPromocional()
    {
        return $this->_ReceitaPopularPromocional;
    }

    /**
     * @param mixed $ReceitaPopularPromocional
     */
    public function setReceitaPopularPromocional($ReceitaPopularPromocional)
    {
        $this->_ReceitaPopularPromocional = $ReceitaPopularPromocional;
    }

    /**
     * @return mixed
     */
    public function getReceitaPopularNormal()
    {
        return $this->_ReceitaPopularNormal;
    }

    /**
     * @param mixed $ReceitaPopularNormal
     */
    public function setReceitaPopularNormal($ReceitaPopularNormal)
    {
        $this->_ReceitaPopularNormal = $ReceitaPopularNormal;
    }

    /**
     * @return mixed
     */
    public function getVlUnitarioNormal()
    {
        return $this->_vlUnitarioNormal;
    }

    /**
     * @param mixed $vlUnitarioNormal
     */
    public function setVlUnitarioNormal($vlUnitarioNormal)
    {
        $this->_vlUnitarioNormal = $vlUnitarioNormal;
    }

    /**
     * @return mixed
     */
    public function getVlReceitaTotalPrevista()
    {
        return $this->_vlReceitaTotalPrevista;
    }

    /**
     * @param mixed $vlReceitaTotalPrevista
     */
    public function setVlReceitaTotalPrevista($vlReceitaTotalPrevista)
    {
        $this->_vlReceitaTotalPrevista = $vlReceitaTotalPrevista;
    }

}