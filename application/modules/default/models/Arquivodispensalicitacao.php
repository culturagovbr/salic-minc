<?php
/**
 * Description of Arquivodispensalicitacao
 *
 * @author 01610881125
 */
class Arquivodispensalicitacao extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'BDCORPORATIVO';
    protected $_name    = 'tbArquivoDispensaLicitacao';
    protected $_schema  = 'BDCORPORATIVO.scSAC';

    public function buscarArquivos($iddispensalicitacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('adl'=>$this->_name),
                        array(
                                'adl.idArquivo'
                              )
                      );

        $select->joinInner(
                            array('arq'=>'tbArquivo'),
                            'arq.idArquivo = adl.idArquivo',
                            array('arq.nmArquivo','arq.sgExtensao','arq.nrTamanho'),
                            'BDCORPORATIVO.scCorp'
                           );
        $select->where('adl.idDispensaLicitacao = ?', $iddispensalicitacao);

        return $this->fetchAll($select);
    }
}
