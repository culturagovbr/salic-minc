<?php
/**
 * DAO tbDistribuirParecer
 * @author pedro.gomes - XTI
 * @since 17/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @link http://www.cultura.gov.br
 */


class VerificacaoAGENTES extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "AGENTES";
    protected $_schema  = "AGENTES";
    protected $_name    = "Verificacao";

    /**
         * Retorna registros do banco de dados
         * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
         * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
         * @param int $tamanho - numero de registros que deve retornar
         * @param int $inicio - offset
         * @return Zend_Db_Table_Rowset_Abstract
         */
    public function listarTipo($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("c"=>$this->_name),
                array(  "idVerificacao",
                        "idTipo",
                        "Descricao",
                        "Sistema")
                );
        $slct->where('idTipo = ? ', 5);
        xd($slct->assemble());

        return $this->fetchAll($slct);
    }
}
