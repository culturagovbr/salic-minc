<?php
/**
 * Description of Arquivocontrato
 *
 * @author 01610881125
 */
class Arquivocontrato extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'BDCORPORATIVO';
    protected $_name    = 'tbArquivoContrato';
    protected $_schema  = 'BDCORPORATIVO.scSAC';

    public function buscarArquivos($idcontrato)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('ac'=>$this->_name),
                        array(
                                'ac.idArquivo'
                              )
                      );

        $select->joinInner(
                            array('arq'=>'tbArquivo'),
                            'arq.idArquivo = ac.idArquivo',
                            array('arq.nmArquivo','arq.sgExtensao','arq.nrTamanho'),
                            'BDCORPORATIVO.scCorp'
                           );
        $select->where('ac.idContrato = ?', $idcontrato);

        return $this->fetchAll($select);
    }
}
