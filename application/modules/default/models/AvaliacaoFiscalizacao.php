<?php
/**
 * Description of Projetos
 *
 * @author Andr� Nogueira Pereira
 */
class AvaliacaoFiscalizacao extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbAvaliacaoFiscalizacao';
    protected $_schema = 'SAC';
    protected $_banco = 'SAC';

    public function buscaAvaliacaoFiscalizacao($idRelatorioFiscalizacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('af' => $this->_name),
                array('af.idAvaliacaoFiscalizacao'
                ,'af.idRelatorioFiscalizacao'
                ,'af.idAvaliador'
                ,'af.dtAvaliacaoFiscalizacao'
                      , new Zend_Db_Expr('CAST(af.dsParecer AS TEXT) as dsParecer'))
        );

        $select->where('af.idRelatorioFiscalizacao = ?', $idRelatorioFiscalizacao);

        $retorno = $this->fetchRow($select);

        if (count($this->fetchRow($select)) == 0) {
            $retorno = 0;
        }

        return $retorno;
    }

    public function insereAvaliacaoFiscalizacao($dados)
    {
        return $this->insert($dados);
    }

    public function alteraAvaliacaoFiscalizacao($dados, $where)
    {
        try {
            return $this->update($dados, $where);
        } catch (Zend_Db_Table_Exception $e) {
            return 'AvaliacaoFiscalizacao -> alteraAvaliacaoFiscalizacao. Erro:' . $e->getMessage();
        }
    }
}
