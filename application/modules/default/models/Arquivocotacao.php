<?php
/**
 * Description of Arquivocotacao
 *
 * @author 01610881125
 */
class Arquivocotacao extends MinC_Db_Table_Abstract
{
    protected $_name    = 'tbArquivoCotacao';
    protected $_schema  = 'BDCORPORATIVO';

    public function buscarArquivos($idcotacao)
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
        $select->where('ac.idCotacao = ?', $idcotacao);

        return $this->fetchAll($select);
    }
}
