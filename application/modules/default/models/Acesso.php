<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of Acesso
 *
 * @author 01129075125
 */
class Acesso extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name  = 'tbAcesso';

    public function consultarAcessoPronac($idPronac, $tpAcesso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>$this->_name),
                array('a.idAcesso',
                'CAST(a.dsAcesso AS TEXT) AS dsAcesso',
                'CAST(a.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo',
                'a.qtPessoa',
                'CAST(a.dsLocal AS TEXT) AS dsLocal',
                'CAST(a.dsEstruturaSolucao AS TEXT) AS dsEstruturaSolucao',
                'a.tpAcesso',
                'a.idRelatorio',
                'a.stAcesso',
                'a.stQtPessoa',
                'a.stPublicoAlvo',
                'a.stLocal',
                'a.stEstrutura',
                'CAST(a.dsJustificativaAcesso AS TEXT) AS dsJustificativaAcesso'
                )
        );
        $select->joinLeft(
                array('b'=>'tbRelatorio'),
                "b.idRelatorio = a.idRelatorio",
                array('*')
        );
        $select->joinInner(
                array('c'=>'tbRelatorioConsolidado'),
                "b.idRelatorio = c.idRelatorio",
                array('*')
        );

        $select->where("b.idPRONAC = ?", $idPronac);
        $select->where("a.tpAcesso = ?", $tpAcesso);

        return $this->fetchAll($select);
    }

    public function buscarUsandoCAST($idRelatorio, $tpAcesso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => $this->_name),
                array('a.idAcesso', 'a.idRelatorio', 'CAST(a.dsAcesso AS TEXT) AS dsAcesso', 'CAST(a.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo', 'a.qtPessoa', 'CAST(a.dsLocal AS TEXT) AS dsLocal', 'CAST(a.dsEstruturaSolucao AS TEXT) AS dsEstruturaSolucao', 'a.tpAcesso', 'a.stAcesso', 'a.stQtPessoa', 'a.stPublicoAlvo', 'a.stLocal', 'a.stEstrutura', 'CAST(a.dsJustificativaAcesso AS TEXT) AS dsJustificativaAcesso')
        );
        $select->where('a.idRelatorio = ?', $idRelatorio);
        $select->where('a.tpAcesso = ?', $tpAcesso);

        return $this->fetchAll($select);
    }
}
