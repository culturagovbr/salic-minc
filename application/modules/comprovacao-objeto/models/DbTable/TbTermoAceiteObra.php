<?php

class ComprovacaoObjeto_Model_DbTable_TbTermoAceiteObra extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbTermoAceiteObra";

    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }

    public function alterarDados($dados, $where)
    {
        $where = "idTermoAceiteObra = " . $where;
        return $this->update($dados, $where);
    }

    public function buscarTermoAceiteObra($where, $all = false, $order = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(
                $this->_name,
                array('idTermoAceiteObra', 'idPronac', 'dtCadastroTermo',
                    new Zend_Db_Expr('CAST(dsDescricaoTermoAceite AS TEXT) AS dsDescricaoTermoAceite'),
                    'idDocumentoTermo','idUsuarioCadastrador','stConstrucaoCriacaoRestauro')
        );

        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        if ($all) {
            return $this->fetchAll($slct);
        } else {
            return $this->fetchRow($slct);
        }
    }

    public function buscarTermoAceiteObraArquivos($where, $all = false, $order = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(
                array('a' => $this->_name),
                array('idTermoAceiteObra', 'idPronac', 'dtCadastroTermo',
                    new Zend_Db_Expr('CAST(dsDescricaoTermoAceite AS TEXT) AS dsDescricaoTermoAceite'),
                    'idDocumentoTermo','idUsuarioCadastrador','stConstrucaoCriacaoRestauro')
        );

        $slct->joinLeft(
                array('b' => 'tbDocumento'),
            "a.idDocumentoTermo = b.idDocumento",
                array(''),
            'BDCORPORATIVO.scCorp'
        );
        $slct->joinLeft(
                array('c' => 'tbArquivo'),
            "b.idArquivo = c.idArquivo",
                array('idArquivo','nmArquivo','sgExtensao','dtEnvio'),
            'BDCORPORATIVO.scCorp'
        );

        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        if ($all) {
            return $this->fetchAll($slct);
        } else {
            return $this->fetchRow($slct);
        }
    }
}
