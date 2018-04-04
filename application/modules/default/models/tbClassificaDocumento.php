<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tbClassificaDocumento
 *
 * @author tisomar
 */
class tbClassificaDocumento extends MinC_Db_Table_Abstract
{
    protected $_banco  = "BDCORPORATIVO";
    protected $_schema = "BDCORPORATIVO.scSAC";
    protected $_name   = "tbClassificaDocumento";


    public function fundoSetorialXClassificacao($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->distinct();
        $slct->from(
                    array("c"=>$this->_name),
                    array("idClassificaDocumento", "dsClassificaDocumento"),
                    "BDCORPORATIVO.scSAC"
                    );
        $slct->joinInner(
                        array("f"=>"tbFormDocumento"),
                        "c.idClassificaDocumento = f.idClassificaDocumento",
                        array(),
                        "BDCORPORATIVO.scQuiz"
                        );
        $slct->joinInner(
                        array("e"=>"Edital"),
                        "f.idEdital =e.idEdital",
                        array(),
                        "SAC.dbo"
                        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        //$this->_totalRegistros = $this->fetchAll($slct)->count();
        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        
        return $this->fetchAll($slct);
    }
}
