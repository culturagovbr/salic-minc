<?php
/**
 * DAO tbTmpRelatorioConsolidado
 * @since 16/03/2011
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbTmpRelatorioConsolidado extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbTmpRelatorioConsolidado";
    protected $_primary = "idPronac";


    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function buscarDados($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name),
                    array(
                        'idPronac',
                        'CAST(a.dsObjetivosMetas AS TEXT) AS dsObjetivosMetas',
                        'CAST(a.dsEstrategiaAcao AS TEXT) AS dsEstrategiaAcao',
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
                        'CAST(a.dsDestinacaoProduto AS TEXT) AS dsDestinacaoProduto',
                        'stFinsLucrativos',
                        'dsBeneficiario',
                        'nrCNPJ',
                        'nrCPF',
                        'CAST(a.dsReceptorProduto AS TEXT) AS dsReceptorProduto',
                        'CAST(a.dsAcessoAcessibilidade AS TEXT) AS dsAcessoAcessibilidade',
                        'qtPessoaAcessibilidade',
                        'CAST(a.dsPublicoAlvoAcessibilidade AS TEXT) AS dsPublicoAlvoAcessibilidade',
                        'CAST(a.dsLocalAcessibilidade AS TEXT) AS dsLocalAcessibilidade',
                        'CAST(a.dsEstruturaSolucaoAcessibilidade AS TEXT) AS dsEstruturaSolucaoAcessibilidade',
                        'CAST(a.dsAcessoDemocratizacao AS TEXT) AS dsAcessoDemocratizacao',
                        'qtPessoaDemocratizacao',
                        'CAST(a.dsPublicoAlvoDemocratizacao AS TEXT) AS dsPublicoAlvoDemocratizacao',
                        'CAST(a.dsLocalDemocratizacao AS TEXT) AS dsLocalDemocratizacao',
                        'CAST(a.dsEstruturaSolucaoDemocratizacao AS TEXT) AS dsEstruturaSolucaoDemocratizacao',
                        'dsProduto',
                        'CAST(a.dsRepercussao AS TEXT) AS dsRepercussao',
                        'CAST(a.dsImpactoAmbiental AS TEXT) AS dsImpactoAmbiental',
                        'CAST(a.dsImpactoCultural AS TEXT) AS dsImpactoCultural',
                        'CAST(a.dsImpactoEconomico AS TEXT) AS dsImpactoEconomico',
                        'CAST(a.dsImpactoSocial AS TEXT) AS dsImpactoSocial',
                        'stPrevisaoProjeto',
                        'CAST(a.dsTermoProjeto AS TEXT) AS dsTermoProjeto',
                        'idDocumentoAceiteObra',
                        'CAST(a.dsCronogramaFisico AS TEXT) AS dsCronogramaFisico'
                        )
            );

        $select->where('a.idPronac = ?', $idpronac);

        return $this->fetchAll($select);
    } // fecha m�todo cadastrarDados()
}
