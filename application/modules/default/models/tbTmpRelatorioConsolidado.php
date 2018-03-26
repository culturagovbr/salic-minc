<?php

class tbTmpRelatorioConsolidado extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbTmpRelatorioConsolidado";
    protected $_primary = "idPronac";

    public function buscarDados($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name),
                    array(
                        'idPronac',
                        new Zend_Db_Expr('CAST(a.dsObjetivosMetas AS TEXT) AS dsObjetivosMetas'),
                        new Zend_Db_Expr('CAST(a.dsEstrategiaAcao AS TEXT) AS dsEstrategiaAcao'),
                        'vlLeiIncentivoFiscal',
                        'vlLeiIncentivoEstadual',
                        'vlLeiIncentivoMunicipal',
                        'vlRecursosProprios',
                        'vlRendimentoFinanceiro',
                        'idDcumentoFNC',
                        'idPlanoDistribuicao',
                        'idDocumentoPlanoDistribuicao',
                        'tpImovel',
                        'vlImovel',
                        'dsImovel',
                        'nmCartorio',
                        'nrRegistro',
                        'nrFolha',
                        'idDocumentoComprovanteExecucao',
                        new Zend_Db_Expr('CAST(a.dsDestinacaoProduto AS TEXT) AS dsDestinacaoProduto'),
                        'stFinsLucrativos',
                        'dsBeneficiario',
                        'nrCNPJ',
                        'nrCPF',
                        new Zend_Db_Expr('CAST(a.dsReceptorProduto AS TEXT) AS dsReceptorProduto'),
                        'qtPessoaAcessibilidade',
                        new Zend_Db_Expr('CAST(a.dsAcessoAcessibilidade AS TEXT) AS dsAcessoAcessibilidade'),
                        new Zend_Db_Expr('CAST(a.dsPublicoAlvoAcessibilidade AS TEXT) AS dsPublicoAlvoAcessibilidade'),
                        new Zend_Db_Expr('CAST(a.dsLocalAcessibilidade AS TEXT) AS dsLocalAcessibilidade'),
                        new Zend_Db_Expr('CAST(a.dsEstruturaSolucaoAcessibilidade AS TEXT) AS dsEstruturaSolucaoAcessibilidade'),
                        new Zend_Db_Expr('CAST(a.dsAcessoDemocratizacao AS TEXT) AS dsAcessoDemocratizacao'),
                        'qtPessoaDemocratizacao',
                        new Zend_Db_Expr('CAST(a.dsPublicoAlvoDemocratizacao AS TEXT) AS dsPublicoAlvoDemocratizacao'),
                        new Zend_Db_Expr('CAST(a.dsLocalDemocratizacao AS TEXT) AS dsLocalDemocratizacao'),
                        new Zend_Db_Expr('CAST(a.dsEstruturaSolucaoDemocratizacao AS TEXT) AS dsEstruturaSolucaoDemocratizacao'),
                        'dsProduto',
                        new Zend_Db_Expr('CAST(a.dsRepercussao AS TEXT) AS dsRepercussao'),
                        new Zend_Db_Expr('CAST(a.dsImpactoAmbiental AS TEXT) AS dsImpactoAmbiental'),
                        new Zend_Db_Expr('CAST(a.dsImpactoCultural AS TEXT) AS dsImpactoCultural'),
                        new Zend_Db_Expr('CAST(a.dsImpactoEconomico AS TEXT) AS dsImpactoEconomico'),
                        new Zend_Db_Expr('CAST(a.dsImpactoSocial AS TEXT) AS dsImpactoSocial'),
                        'stPrevisaoProjeto',
                        new Zend_Db_Expr('CAST(a.dsTermoProjeto AS TEXT) AS dsTermoProjeto'),
                        'idDocumentoAceiteObra',
                        new Zend_Db_Expr('CAST(a.dsCronogramaFisico AS TEXT) AS dsCronogramaFisico')
                        )
            );

        $select->where('a.idPronac = ?', $idpronac);

        return $this->fetchAll($select);
    }
}
