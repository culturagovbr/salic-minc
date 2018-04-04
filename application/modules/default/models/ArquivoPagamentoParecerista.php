<?php
/**
 * Description of ArquivoPagamentoParecerista
 *
 * @author Tarcisio Angelo
 */
class ArquivoPagamentoParecerista extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbPagamentoPareceristaXArquivo';
    protected $_schema = 'SAC';
//    protected $_banco = 'SAC';
    protected $_primary = 'idArquivo';

    public function inserirArquivodePagamento($idGerarPagamentoParecerista, $idArquivo, $siArquivo)
    {
        $sql = "INSERT INTO ".$this->_schema.".".$this->_name." (idGerarPagamentoParecerista,idArquivo,siArquivo)
                values ($idGerarPagamentoParecerista, $idArquivo,'$siArquivo')";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }


    public function buscarArquivo($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('arqpa'=>$this->_name),
                        array('arqpa.idGerarPagamentoParecerista','arqpa.siArquivo')
        );

        $select->joinInner(
            array('arq'=>'tbArquivo'),
            "arq.idArquivo = arqpa.idArquivo",
                            array('arq.idArquivo',
                                  'arq.nmArquivo',
                                  'arq.sgExtensao',
                                  'arq.dtEnvio',
                                  'nrTamanho',
                                  'arq.dsTipoPadronizado'),
            'BDCORPORATIVO.scCorp'
        );

        $select->joinInner(
            array('aim'=>'tbArquivoImagem'),
            "arq.idArquivo = aim.idArquivo",
                            array('aim.biArquivo'),
            'BDCORPORATIVO.scCorp'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('arq.dtEnvio');

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->fetchAll('SET TEXTSIZE 2147483647');
        return $db->fetchAll($select);
    }
}
