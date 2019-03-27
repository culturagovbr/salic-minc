<?php

class AvaliacaoProposta extends MinC_Db_Table_Abstract
{
    const CONFORMIDADE_OK_REPROVADO = 0;
    const CONFORMIDADE_OK_APROVADO = 1;
    const CONFORMIDADE_OK_PRE_ENVIADO = 9;

    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbAvaliacaoProposta";

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscar($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        // adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slctUnidade->limit($tamanho, $tmpInicio);
        }

        // retornando os registros conforme objeto select
        return $this->fetchAll($slct);
    }

    public function diligenciasNaoRespondidas($retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array(
                'idPronac'
            )
        );

        $select->where(new Zend_Db_Expr('((DATEDIFF(day, DtAvaliacao, GETDATE()) > 20'));
        $select->where("stProrrogacao =?)", 'N');

        $select->orWhere(new Zend_Db_Expr('(DATEDIFF(day, DtAvaliacao, GETDATE()) > 40'));
        $select->where("stProrrogacao =?))", 'S');
        $select->where('stEnviado =?', 'N');

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }
}
