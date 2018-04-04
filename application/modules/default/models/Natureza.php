<?php

class Natureza extends MinC_Db_Table_Abstract
{
    //protected $_name = 'AGENTE.dbo.Natureza';
    protected $_banco = 'AGENTES';
    protected $_name  = 'Natureza';
    protected $_schema = 'AGENTES';

    /**
     * Retorna registros do banco de dados referente a Agentes(Proponente)
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscar($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from($this->_name);

        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }

    /**
     * M�todo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $idNatureza)
    {
        $where = "idNatureza = " . $idNatureza;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()

    public function pesquisaCEPIM($cnpjcpf)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => $this->_name),
                array('*')
        );

        $select->joinInner(
            array('b' => 'Agentes'),
            'b.idAgente = a.idAgente',
            array(''),
            'AGENTES.dbo'
        );
        
        $select->where('b.CNPJCPF = ?', $cnpjcpf);
        $select->where('a.direito = ?', 35);

        
        return $this->fetchAll($select);
    }
}
