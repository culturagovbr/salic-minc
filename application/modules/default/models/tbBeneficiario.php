<?php
/**
 * DAO tbBeneficiario
 * @since 16/03/2011
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbBeneficiario extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbBeneficiario";

    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarDados()


    /**
     * M�todo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idBeneficiario = " . $where;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()

    public function buscarUsandoCAST($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name),
                    array('a.idRelatorio', 'CAST(a.dsBeneficiario AS TEXT) AS dsBeneficiario', 'a.tpBeneficiario', 'tpBeneficiario', 'a.nrCNPJ', 'a.nrCPF', 'CAST(a.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo', 'CAST(a.dsEntrega AS TEXT) AS dsEntrega')
            );
        $select->where('a.idRelatorio = ?', $idRelatorio);
    
        return $this->fetchAll($select);
    }

    public function buscarJustificativaCAST($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name),
                    array(
                            'a.idRelatorio',
                            'a.nrCNPJ',
                            'a.nrCPF',
                            'a.dsBeneficiario',
                            'CAST(a.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo',
                            'CAST(a.dsEntrega AS TEXT) AS dsEntrega',
                            'a.tpBeneficiario',
                            'a.stCNPJ',
                            'a.stCPF',
                            'a.stPublicoAlvo',
                            'CAST(a.dsJustificativaAcompanhamento AS TEXT) AS dsJustificativaAcompanhamento',
                        )
            );
        $select->where('a.idRelatorio = ?', $idRelatorio);
    
        return $this->fetchAll($select);
    }
}
