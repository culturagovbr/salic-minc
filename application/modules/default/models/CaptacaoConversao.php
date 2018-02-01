<?php
class CaptacaoConversao extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "CaptacaoConversao";



    public function buscarCaptacaoConversao()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this);
        return $this->fetchAll($select);
    } // fecha m�todo buscarCaptacaoConversao()

    public function BuscarTotalCaptacaoConversao($retornaSelect = false, $where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array('AnoProjeto'
                ,'Sequencial'
                ,'Conv' => new Zend_Db_Expr('ISNULL(sum(valor), 0)')
            )
        );

        $select->group('AnoProjeto');
        $select->group('Sequencial');

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($retornaSelect == true) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }
}
