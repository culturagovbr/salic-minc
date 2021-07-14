<?php

class Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos extends MinC_Db_Table_Abstract
{
    protected $_schema  = "sac";
    protected $_primary = "idSolicitacaoTransferenciaRecursos";
    protected $_name    = "tbSolicitacaoTransferenciaRecursos";
    
    /**
     * Método para buscar projetos recebedores de uma readequação ou pronac
     * @access public
     * @param integer $idReadequacao
     * @param integer $idPronacRecebedor
     * @return integer
     */
    public function obterProjetosRecebedores(
        $idReadequacao = '',
        $idPronacRecebedor = '')
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr("a.idSolicitacaoTransferenciaRecursos AS idSolicitacao, a.idReadequacao, a.tpTransferencia, a.idPronacRecebedor, a.vlRecebido, a.siAnaliseTecnica, a.siAnaliseComissao, a.stEstado")
            )
        );
        
        $select->joinInner(
            array('b' => 'projetos'),
            'a.idPronacRecebedor = b.IdPRONAC',
            array(
                new Zend_Db_Expr("
                    b.NomeProjeto,
                    b.AnoProjeto + b.Sequencial AS pronac,
                    b.CgcCpf
                    "
                )
            ),
            'SAC.dbo'
        );

        if ($idReadequacao) {
            $select->where('idReadequacao = ?', $idReadequacao);
        }
        if ($idPronacRecebedor) {
            $select->where('idPronacRecebedor = ?', $idPronacRecebedor);
        }
        
        return $this->fetchAll($select);
    }
}
