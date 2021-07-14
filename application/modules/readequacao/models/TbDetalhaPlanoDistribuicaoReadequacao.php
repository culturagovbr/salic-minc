<?php
class Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacao extends MinC_Db_Model
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
    protected $_idReadequacao;
    protected $_tpSolicitacao;
    protected $_stAtivo;
    protected $_idPronac;
    protected $_idDetalhaOriginal;

    const TP_SOLICITACAO_ATUALIZAR = 'A';
    const TP_SOLICITACAO_INCLUIR = 'I';
    const TP_SOLICITACAO_EXCLUIR = 'E';
    const TP_SOLICITACAO_NAO_ALTERADO = 'N';

    public function getIdDetalhaPlanoDistribuicao()
    {
        return $this->_idDetalhaPlanoDistribuicao;
    }

    public function setIdDetalhaPlanoDistribuicao($idDetalhaPlanoDistribuicao)
    {
        $this->_idDetalhaPlanoDistribuicao = $idDetalhaPlanoDistribuicao;
    }

    public function getIdPlanoDistribuicao()
    {
        return $this->_idPlanoDistribuicao;
    }

    public function setIdPlanoDistribuicao($idPlanoDistribuicao)
    {
        $this->_idPlanoDistribuicao = $idPlanoDistribuicao;
    }

    public function getIdUF()
    {
        return $this->_idUF;
    }

    public function setIdUF($idUF)
    {
        $this->_idUF = $idUF;
    }

    public function getIdMunicipio()
    {
        return $this->_idMunicipio;
    }

    public function setIdMunicipio($idMunicipio)
    {
        $this->_idMunicipio = $idMunicipio;
    }

    public function getDsProduto()
    {
        return $this->_dsProduto;
    }

    public function setDsProduto($dsProduto)
    {
        $this->_dsProduto = $dsProduto;
    }

    public function getQtExemplares()
    {
        return $this->_qtExemplares;
    }

    public function setQtExemplares($qtExemplares)
    {
        $this->_qtExemplares = $qtExemplares;
    }

    public function getQtGratuitaDivulgacao()
    {
        return $this->_qtGratuitaDivulgacao;
    }

    public function setQtGratuitaDivulgacao($qtGratuitaDivulgacao)
    {
        $this->_qtGratuitaDivulgacao = $qtGratuitaDivulgacao;
    }

    public function getQtGratuitaPatrocinador()
    {
        return $this->_qtGratuitaPatrocinador;
    }

    public function setQtGratuitaPatrocinador($qtGratuitaPatrocinador)
    {
        $this->_qtGratuitaPatrocinador = $qtGratuitaPatrocinador;
    }

    public function getQtGratuitaPopulacao()
    {
        return $this->_qtGratuitaPopulacao;
    }

    public function setQtGratuitaPopulacao($qtGratuitaPopulacao)
    {
        $this->_qtGratuitaPopulacao = $qtGratuitaPopulacao;
    }

    public function getQtPopularIntegral()
    {
        return $this->_qtPopularIntegral;
    }

    public function setQtPopularIntegral($qtPopularIntegral)
    {
        $this->_qtPopularIntegral = $qtPopularIntegral;
    }

    public function getQtPopularParcial()
    {
        return $this->_qtPopularParcial;
    }

    public function setQtPopularParcial($qtPopularParcial)
    {
        $this->_qtPopularParcial = $qtPopularParcial;
    }

    public function getVlUnitarioPopularIntegral()
    {
        return $this->_vlUnitarioPopularIntegral;
    }

    public function setVlUnitarioPopularIntegral($vlUnitarioPopularIntegral)
    {
        $this->_vlUnitarioPopularIntegral = $vlUnitarioPopularIntegral;
    }

    public function getVlReceitaPopularIntegral()
    {
        return $this->_vlReceitaPopularIntegral;
    }

    public function setVlReceitaPopularIntegral($vlReceitaPopularIntegral)
    {
        $this->_vlReceitaPopularIntegral = $vlReceitaPopularIntegral;
    }

    public function getVlReceitaPopularParcial()
    {
        return $this->_vlReceitaPopularParcial;
    }

    public function setVlReceitaPopularParcial($vlReceitaPopularParcial)
    {
        $this->_vlReceitaPopularParcial = $vlReceitaPopularParcial;
    }

    public function getQtProponenteIntegral()
    {
        return $this->_qtProponenteIntegral;
    }

    public function setQtProponenteIntegral($qtProponenteIntegral)
    {
        $this->_qtProponenteIntegral = $qtProponenteIntegral;
    }

    public function getQtProponenteParcial()
    {
        return $this->_qtProponenteParcial;
    }

    public function setQtProponenteParcial($qtProponenteParcial)
    {
        $this->_qtProponenteParcial = $qtProponenteParcial;
    }

    public function getVlUnitarioProponenteIntegral()
    {
        return $this->_vlUnitarioProponenteIntegral;
    }

    public function setVlUnitarioProponenteIntegral($vlUnitarioProponenteIntegral)
    {
        $this->_vlUnitarioProponenteIntegral = $vlUnitarioProponenteIntegral;
    }

    public function getVlReceitaProponenteIntegral()
    {
        return $this->_vlReceitaProponenteIntegral;
    }

    public function setVlReceitaProponenteIntegral($vlReceitaProponenteIntegral)
    {
        $this->_vlReceitaProponenteIntegral = $vlReceitaProponenteIntegral;
    }

    public function getVlReceitaProponenteParcial()
    {
        return $this->_vlReceitaProponenteParcial;
    }

    public function setVlReceitaProponenteParcial($vlReceitaProponenteParcial)
    {
        $this->_vlReceitaProponenteParcial = $vlReceitaProponenteParcial;
    }

    public function getVlReceitaPrevista()
    {
        return $this->_vlReceitaPrevista;
    }

    public function setVlReceitaPrevista($vlReceitaPrevista)
    {
        $this->_vlReceitaPrevista = $vlReceitaPrevista;
    }

    public function getTpVenda()
    {
        return $this->_tpVenda;
    }

    public function setTpVenda($tpVenda)
    {
        $this->_tpVenda = $tpVenda;
    }

    public function getTpLocal()
    {
        return $this->_tpLocal;
    }

    public function setTpLocal($tpLocal)
    {
        $this->_tpLocal = $tpLocal;
    }

    public function getTpEspaco()
    {
        return $this->_tpEspaco;
    }

    public function setTpEspaco($tpEspaco)
    {
        $this->_tpEspaco = $tpEspaco;
    }

    public function getIdReadequacao()
    {
        return $this->_idReadequacao;
    }

    public function setIdReadequacao($idReadequacao)
    {
        $this->_idReadequacao = $idReadequacao;
    }

    public function getTpSolicitacao()
    {
        return $this->_tpSolicitacao;
    }
    public function setTpSolicitacao($tpSolicitacao)
    {
        $this->_tpSolicitacao = $tpSolicitacao;
    }

    public function getStAtivo()
    {
        return $this->_stAtivo;
    }

    public function setStAtivo($stAtivo)
    {
        $this->_stAtivo = $stAtivo;
    }

    public function getIdPronac()
    {
        return $this->_idPronac;
    }

    public function setIdPronac($idPronac)
    {
        $this->_idPronac = $idPronac;
    }

    public function getIdDetalhaOriginal()
    {
        return $this->_idDetalhaOriginal;
    }

    public function setIdDetalhaOriginal($idDetalhaOriginal)
    {
        $this->_idDetalhaOriginal = $idDetalhaOriginal;
    }

}
