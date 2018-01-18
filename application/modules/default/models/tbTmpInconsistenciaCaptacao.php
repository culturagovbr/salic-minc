<?php

/**
 * DAO tbTmpInconsistenciaCaptacao
 * @author emanuel.sampaio - Politec
 * @since 17/02/2011
 * @version 1.0
 * @link http://www.cultura.gov.br
 */
class tbTmpInconsistenciaCaptacao extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbTmpInconsistenciaCaptacao";
    protected $_primary = "idTipoInconsistencia";

    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }


    /**
     * M�todo para excluir
     * @access public
     * @param integer $where
     * @return integer (quantidade de registros exclu�dos)
     */
    public function excluirDados($where)
    {
        $where = "idTmpCaptacao = " . $where;
        return $this->delete($where);
    }

    /**
     *
     */
    public function buscarInconsistenciasPorCaptacao($idCaptacao)
    {
        $sqlClone = $this->select()->setIntegrityCheck(false)->from($this->_name, array(new Zend_Db_Expr('MAX(idTipoInconsistencia)')))
          ->where('idTmpCaptacao = ?', $idCaptacao)
          ->where('idTipoInconsistencia in (?)', array(TipoInconsistenciaBancariaModel::PROPONENTE_INCENTIVADOR_IGUAIS, TipoInconsistenciaBancariaModel::SEM_VISAO_INCENTIVADOR));

        $tipoInconsistenciaTable = new tbTipoInconsistencia();
        $selectTipoInconsistencia = $tipoInconsistenciaTable
          ->select()
          ->setIntegrityCheck(false)
          ->from($tipoInconsistenciaTable->info(Zend_Db_Table::NAME), array('idTipoInconsistencia'))
          ->where('idTipoInconsistencia not in (?)', array(TipoInconsistenciaBancariaModel::PROPONENTE_INCENTIVADOR_IGUAIS, TipoInconsistenciaBancariaModel::SEM_VISAO_INCENTIVADOR));

        $sql = $this->select()->setIntegrityCheck(false)->from(array('inc' => $this->_name))
          ->join(array('i' => 'tbTipoInconsistencia'), "i.idTipoInconsistencia = inc.idTipoInconsistencia")
          ->where('inc.idTmpCaptacao = ?', $idCaptacao)
          ->where('(inc.idTipoInconsistencia in (?)', $selectTipoInconsistencia)
          ->orWhere('inc.idTipoInconsistencia = ?)', $sqlClone);

        return $this->fetchAll($sql);
    }
}
