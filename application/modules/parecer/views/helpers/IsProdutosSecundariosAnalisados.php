<?php
/**
 * Helper para verificar se há produtos secundários analisados
 */

class Zend_View_Helper_IsProdutosSecundariosAnalisados
{
    /**
     * Método para verificar se há produtos secundários analisados
     * @access public
     * @param integer $idPronac
     * @param integer $idTipoDoAtoAdministrativo
     * @return string
     */
    public function IsProdutosSecundariosAnalisados($idPronac)
    {

//        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
//        return $tbDistribuirParecer->checarValidacaoProdutosSecundarios($idPronac);

        $tbDistribuirParecerDAO = new Parecer_Model_DbTable_TbDistribuirParecer();
        $dadosWhere["t.stEstado = ?"] = 0;
        $dadosWhere["t.FecharAnalise = ?"] = 0;
        $dadosWhere["t.TipoAnalise = ?"] = 3;
        $dadosWhere["p.Situacao IN ('B11', 'B14')"] = '';
        $dadosWhere["p.IdPRONAC = ?"] = $idPronac;
        $dadosWhere["t.stPrincipal = ?"] = 0;
        $dadosWhere["t.DtDevolucao is null"] = '';

        $SecundariosAtivos = $tbDistribuirParecerDAO->dadosParaDistribuir($dadosWhere);
        $secundariosCount = count($SecundariosAtivos);

        if ($secundariosCount > 0) {
            return false;
        } else {
            return true;
        }
    }
}
