<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of StatusAvalicacaoPedidoAlteracao
 *
 * @author 01129075125
 */
class Zend_View_Helper_StatusAvalicacaoPedidoAlteracao
{
    public static function StatusAvalicacaoPedidoAlteracao($idpedidoalteracao, $tipo=null)
    {
        $resultadobusca = tbPedidoAlteracaoProjetoCoordDAO::buscarDadosPedidoAlteracao($idpedidoalteracao);
        if (empty($resultadobusca[0]->dtParecerTecnico) and empty($resultadobusca[0]->dsParecerTecnico) and empty($resultadobusca[0]->idTecnico)) {
            if ($tipo) {
                $resposta = 1;
            } else {
                $resposta = "Aguardando Parecer";
            }
        } else {
            if (!empty($resultadobusca[0]->dtParecerTecnico) and !empty($resultadobusca[0]->dsParecerTecnico) and !empty($resultadobusca[0]->idTecnico) and empty($resultadobusca[0]->stDeferimentoAvaliacao) and empty($resultadobusca[0]->dtRetornoCoordenador)) {
                if ($tipo) {
                    $resposta = 2;
                } else {
                    $resposta = "Aguardando Aprovação";
                }
            } else {
                if (!empty($resultadobusca[0]->dsRetornoCoordenador) and !empty($resultadobusca[0]->dtRetornoCoordenador)
                        and !empty($resultadobusca[0]->idCoordenador) and !empty($resultadobusca[0]->dtParecerTecnico) and !empty($resultadobusca[0]->dsParecerTecnico) and !empty($resultadobusca[0]->idTecnico)) {
                    if ($tipo) {
                        $resposta = 3;
                    } else {
                        $resposta = "Retorno ao Técnico";
                    }
                } else {
                    if (!empty($resultadobusca[0]->dsJustificativaAvaliacao) and !empty($resultadobusca[0]->dtAvaliacao) and !empty($resultadobusca[0]->idAvaliador) and $resultadobusca[0]->stDeferimentoAvaliacao=='I') {
                        if ($tipo) {
                            $resposta = 4;
                        } else {
                            $resposta = "Não Aprovado/Indeferido";
                        }
                    }
                    if (!empty($resultadobusca[0]->dsJustificativaAvaliacao) and !empty($resultadobusca[0]->dtAvaliacao) and !empty($resultadobusca[0]->idAvaliador) and $resultadobusca[0]->stDeferimentoAvaliacao=='D') {
                        if ($tipo) {
                            $resposta = 5;
                        } else {
                            $resposta = "Aprovado";
                        }
                        $resposta = "Aprovado";
                    }
                }
            }
        }
        return $resposta;
    }
}
