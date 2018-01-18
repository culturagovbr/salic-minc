<?php
class tbRetirarDePauta extends MinC_Db_Table_Abstract
{
    protected $_banco   = "BDCORPORATIVO";
    protected $_schema  = "BDCORPORATIVO.scSAC";
    protected $_name    = "tbRetirarDePauta";

    public function buscarDados($where=array(), $order=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this, array('idRetirarDePauta'
                    ,'MotivoRetirada'
                    ,'CAST(dsJustificativa AS TEXT) AS dsJustificativa'
                    ,'idPronac'
                    ,'idAgenteEnvio'
                    ,'CONVERT(CHAR(10), dtEnvio, 103) AS dtEnvio'
                    ,'idAgenteAnalise'
                    ,'CONVERT(CHAR(10), dtAnalise, 103) AS dtAnalise'
                    ,'tpAcao'
                    ,'stAtivo'));

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        // adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    }
}
