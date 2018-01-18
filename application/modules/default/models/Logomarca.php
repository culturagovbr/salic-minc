<?php
/**
 * Logomarca
 *
 * @uses GenericModel
 * @author  wouerner <wouerner@gmail.com>
 */
class Logomarca extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "tblogomarca";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscar($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();

        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        $this->fetchAll($slct);

        return $this->fetchAll($slct);
    }
}
