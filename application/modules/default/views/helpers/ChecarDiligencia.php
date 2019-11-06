<?php
/**
 * Helper para verificar o status da diligência do projeto
 * @author Equipe RUP - Politec
 * @since 11/10/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_ChecarDiligencia
{
    /**
     * Método para verificar o status da diligencia do projeto
     * @access public
     * @param integer $idPronac
     * @param integer $idProduto
     * @param array $idTipoDiligencia
     * @return string
     */
    public function checarDiligencia($idPronac = null, $idProduto = null, $idTipoDiligencia = array())
    {
        if (isset($idPronac) && !empty($idPronac)) :

            // objetos
            $tbDiligencia        = new tbDiligencia();
        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();


        // busca a diligência
        $where = array('idPronac = ?' => $idPronac);
        $order = array('DtSolicitacao DESC');

        // filtra pelo id do produto
        if (!empty($idProduto)) :
                $where = array_merge($where, array('idProduto = ?' => $idProduto));
        endif;

        // filtra por tipos de diligências
        if (count($idTipoDiligencia) > 0) :
                $where = array_merge($where, array('idTipoDiligencia IN (?)' => $idTipoDiligencia));
        endif;

        $buscarDiligencia = $tbDiligencia->buscar($where, $order);


        // busca o parecer distribuido
        $whereParecer  = array('idPRONAC = ?' => $idPronac, 'DtDistribuicao ?' => new Zend_Db_Expr('IS NOT NULL'), 'DtDevolucao ?' => new Zend_Db_Expr('IS NULL'), 'stEstado = ?' => 0, 'TipoAnalise = ?' => 3);

        $buscarParecer = $tbDistribuirParecer->buscar($whereParecer);


        if (count($buscarDiligencia) > 0) :
                $DtSolicitacao = $buscarDiligencia[0]->DtSolicitacao;
        $DtResposta    = $buscarDiligencia[0]->DtResposta;
        $stEnviado     = trim($buscarDiligencia[0]->stEnviado);

        $buscarTmpFimDiligencia = $tbDiligencia->buscar(array('idPronac = ?' => $idPronac, 'stProrrogacao = ?' => 'N'), array('DtSolicitacao DESC'));
        if (count($buscarTmpFimDiligencia) > 0) :
                    $TmpFimDiligencia = 20; else :
                    $TmpFimDiligencia = 40;
        endif;

        if (count($buscarParecer) > 0) :
                    $DtDistribuicao = $buscarParecer[0]->$DtDistribuicao; else :
                    $DtDistribuicao = null;
        endif; else :
                $DtSolicitacao    = null;
        $DtResposta       = null;
        $TmpFimDiligencia = null;
        $DtDistribuicao   = null;
        $stEnviado        = null;
        endif;

        $TmpDtDistribuicao = !empty($DtDistribuicao) ? (round(Data::CompararDatas($DtDistribuicao))) : 0;

        if ($DtSolicitacao != null && $TmpDtDistribuicao > $TmpFimDiligencia) :
                return 3; // Diligência não respondida
        elseif ($DtSolicitacao != null && $DtResposta != null) :
                return 2; // Diligência respondida
        elseif ($DtSolicitacao != null && $DtResposta == null && $stEnviado == 'S') :
                return 1; // Diligenciado
        else :
                return 0; // A diligenciar
        endif; else :
            return 0; // A diligenciar
        endif;
    } // fecha método checarDiligencia()
} // fecha class
