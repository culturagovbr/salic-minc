<?php
/**
 * Description of ArquivoFiscalizacao
 *
 * @author 01610881125
 */
class ArquivoFiscalizacao extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbArquivoFiscalizacao';
    protected $_schema = 'SAC';
    protected $_banco = 'SAC';

    public function buscarArquivo($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('arqfis'=>$this->_name),
            array('arqfis.idArquivoFiscalizacao')
        );
        $select->joinInner(
                array('arq'=>'tbArquivo'),
                "arq.idArquivo = arqfis.idArquivo",
                array('arq.idArquivo','arq.nmArquivo','arq.sgExtensao','dtEnvio','nrTamanho'),
                'BDCORPORATIVO.scCorp'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        return $this->fetchAll($select);
    }
}
