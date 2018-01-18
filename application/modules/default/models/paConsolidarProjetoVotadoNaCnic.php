<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of paConsolidarProjetoVotadoNaCnic
 */
class paConsolidarProjetoVotadoNaCnic extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'paConsolidarProjetoVotadoNaCnic';

//    	idpronac
    //	idNrReuniao
    //	NrReuniao
//      tpresultadovotacao (verificar se � igual a 3)
    //	resultadovotacao (AS)
    //	parecerconsolidado
    //	bln_readequacao (1 = true ou 0 = false)
    //	situacao (poder ser null)

    public function consolidarVotacaoProjeto($idPronac=null, $idNrReuniao=null, $nrReuniao=null, $tpResultadoVotacao=null, $resultadoVotacao=null, $dsParecerConsolidado=null, $blnReadequacao=0, $situacao="NUL", $tpConsolidacaoVotacao, $idTipoReadequacao = null)
    {
        try {
            if (!empty($idPronac) && !empty($idNrReuniao) && !empty($tpResultadoVotacao) && !empty($resultadoVotacao) && !empty($dsParecerConsolidado) && !empty($situacao)) {
                $sql = "exec " . $this->_banco .".". $this->_name . ' ' . (int)$idPronac .",". (int)$idNrReuniao .",". "'".$nrReuniao."'" .",". (int)$tpResultadoVotacao .",". "'".$resultadoVotacao."'" .",". "'".$dsParecerConsolidado."'" .",". (int)$blnReadequacao .",". "'".$situacao."'".",". "'".$tpConsolidacaoVotacao."'".",". "'".$idTipoReadequacao."'";
                //return  $this->getAdapter()->query($sql);
                
                $db = Zend_Db_Table::getDefaultAdapter();
                $db->setFetchMode(Zend_DB :: FETCH_OBJ);
                return $db->fetchAll($sql);
            } else {
                $ex = new Exception("Parametros obrigatorios nao informados");
                return  $ex->getMessage();
            }
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }
}
