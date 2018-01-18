<?php
/**
 * Description of ArquivoPagamentoParecerista
 *
 * @author Tarcisio Angelo
 */
class PagarParecerista extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbPagarParecerista';
    protected $_schema = 'SAC';
    protected $_banco = 'SAC';

    public function buscarPagamentos($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('pp'=>$this->_name), array('pp.idPagarParecerista', 'pp.vlPagamento'));

        $select->joinInner(
            array('o'=> 'Orgaos'),
            "pp.idUnidadeAnalise = o.Codigo",
                            array('o.Sigla as Vinculada')
        );

        $select->joinInner(
            array('ag'=> 'Agentes'),
            "pp.idParecerista = ag.idAgente",
                            array('ag.CNPJCPF','idAgente as idParecerista'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('nm'=> 'Nomes'),
            "ag.idAgente = nm.idAgente",
                            array('nm.Descricao as nmParecerista'),
            'AGENTES.dbo'
        );

        $select->joinLeft(
            array('af'=> 'tbAgenteFisico'),
            "pp.idParecerista = af.idAgente",
                            array('af.nrIdentificadorProcessual'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('pro'=> 'Projetos'),
            "pp.idPronac = pro.idPRONAC",
                            array('pronac' => new Zend_Db_Expr('pro.AnoProjeto + pro.Sequencial'),
                                  'pro.idpronac',
                                  'pro.NomeProjeto',
                                  'pro.UnidadeAnalise',
                                  'pro.Situacao')
        );

        $select->joinInner(
            array('prod'=> 'Produto'),
            "pp.idProduto = prod.Codigo",
                            array('prod.Descricao as Produto',
                                  'prod.stEstado')
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        //x($select->assemble());

        return $this->fetchAll($select);
    }

    public function vlTotalPagamento($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pp'=>$this->_name),
                        array('sum(vlPagamento) as vlTotalPagamento')
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }



        return $this->fetchAll($select);
    }


    public function buscarPareceristas($where = array())
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pp'=>$this->_name),
                        array('pp.idParecerista')
        );

        $select->joinInner(
            array('ag'=> 'Agentes'),
            "pp.idParecerista = ag.idAgente",
                            array('ag.CNPJCPF'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('nm'=> 'Nomes'),
            "ag.idAgente = nm.idAgente",
                            array('nm.Descricao as nmParecerista'),
            'AGENTES.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }


        $select->order('nm.Descricao');

        return $this->fetchAll($select);
    }
}
