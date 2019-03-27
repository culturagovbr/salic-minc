<?php
class Zend_View_Helper_PrazoRespostaDiligencia
{
    /**
     * Método que retorna as informacoes sobre o prazo de resposta da diligencia, incluindo o icone que deve ser apresentado na tela
     * @access public
     * @param integer $idPronac (id do Projeto que deseja saber informacoes sobre a diligencia)
     * @param integer $idTipoDiligencia (id do tipo de diligencia, cada modulo possui um codigo especifico)
     * @param integer $idDiligencia (id da diligencia que deseja saber as informacoes)
     * @param boolean $blnPrazoPadrao (se true retorna apenas o prazo padrao de resposta da diligencia para o modulo em questao)
     * @param boolean $blnPrazoResposta (se true retorna apenas o prazo que o proponente tem para responder a diligencia)
     * @return string
     */
    public function prazoRespostaDiligencia($idPronac = null, $idTipoDiligencia = null, $idDiligencia = null, $blnPrazoPadrao=false, $blnPrazoResposta=false)
    {
        //inicializando vetores
        $arrRetorno= array();
        $arrRetorno['prazoPadrao']   = null;
        $arrRetorno['prazoRespostaCrescente'] = null;
        $arrRetorno['prazoRespostaDecrescente'] = null;
        $arrIcones= array();
        $arrIcones['icone'] = "notice1.png";
        $arrIcones['title']  = "A Diligenciar";
        $arrRetorno['iconeDiligencia']   = $arrIcones;

        if (isset($idPronac) && !empty($idPronac)) :

            $tbDiligencia = new tbDiligencia();

        $arrBusca = array();
        $arrBusca['IdPRONAC = ?'] = $idPronac;
        if (!empty($idTipoDiligencia)) {
            $arrBusca['idTipoDiligencia = ?'] = $idTipoDiligencia;
        }
        if (!empty($idDiligencia)) {
            $arrBusca['idDiligencia = ?'] = $idDiligencia;
        }

        // busca a situação do projeto
        $rsDiligencia = $tbDiligencia->buscar($arrBusca, array('DtSolicitacao DESC'))->current();

        if (!empty($rsDiligencia)):
                            $prazoPadrao    = $this->prazoPadrao($rsDiligencia->idTipoDiligencia, $rsDiligencia->stProrrogacao);
        $prazoResposta  = $this->prazoParaResposta($rsDiligencia->DtSolicitacao, $prazoPadrao);
        $prazoRespostaCresc = $this->prazoParaResposta($rsDiligencia->DtSolicitacao, $prazoPadrao);
        $prazoRespostaDesc  = $this->prazoParaResposta($rsDiligencia->DtSolicitacao, $prazoPadrao, true);

        //retorna apenas o prazo padrao do sistema
        if ($blnPrazoPadrao) {
            return $prazoPadrao;
        }
        //retorna apenas o prazo de resposta do proponente
        if ($blnPrazoResposta) {
            return ($prazoRespostaDesc == '-1') ? $prazoPadrao : $prazoRespostaDesc;
        }
        //retorna os dois prazos

        $arrRetorno['prazoPadrao']   = $prazoPadrao;
        $arrRetorno['prazoRespostaCrescente'] = ($prazoRespostaCresc == '-1') ? $prazoPadrao : $prazoRespostaCresc;
        $arrRetorno['prazoRespostaDecrescente'] = ($prazoRespostaDesc == '-1') ? $prazoPadrao : $prazoRespostaDesc;

        //verifica os icones a serem utilizados
        $arrIcones = $this->iconeDiligencia($rsDiligencia, $prazoPadrao, $prazoResposta);

        $arrRetorno['iconeDiligencia']   = $arrIcones;
        return $arrRetorno; else:
                            return $arrRetorno;
        endif; else :
            return $arrRetorno;
        endif;
    }

    //retorna o prazo padrao de resposta definido pelo sistema
    public function prazoPadrao($idTipoDiligencia = null, $stProrrogacao = null)
    {
        return 40;
    }

    //retorna o prazo que o proponente tem para responder a diligencia
    public function prazoParaResposta($dtSolicitacao = null, $prazoPadrao = null, $bln_decrescente=false)
    {
        if (!empty($dtSolicitacao)):
                $prazo = round(Data::CompararDatas($dtSolicitacao));
        if ($bln_decrescente) {
            $prazo = ((int)$prazoPadrao)-((int)$prazo); //caso a logica de contagem for regressiva
        }
        if ($prazo > '0') {      //prazo positivo
            return $prazo;
        } elseif ($prazo <= '0') {//prazo negativo
            return '0';
        } else {                  //prazo de resposta igual ao prazo padrao
            return '-1';
        } else:
                return '0';
        endif;
    }

    //retorna o icone que deve ser utilizado na view
    public function iconeDiligencia($rsDiligencia, $prazoPadrao, $prazoResposta)
    {
        //$prazoPadrao    = $this->prazoPadrao($rsDiligencia->idTipoDiligencia, $rsDiligencia->stProrrogacao);
        //$prazoResposta  = $this->prazoParaResposta($rsDiligencia->DtSolicitacao, $prazoPadrao);
        $arrIcones = array();

        //diligenciado
        if ($rsDiligencia->DtSolicitacao && $rsDiligencia->DtResposta == null 
            && $prazoResposta <= $prazoPadrao && $rsDiligencia->stEnviado == 'S') {
            $arrIcones['icone'] = "notice.png";
            $arrIcones['title'] = "Diligenciado";
        }
        //diligencia nao respondida
        elseif ($rsDiligencia->DtSolicitacao && $rsDiligencia->DtResposta == null && $prazoResposta > $prazoPadrao) {
            $arrIcones['icone'] = "notice2.png";
            $arrIcones['title'] = "Dilig&ecirc;ncia n&atilde;o respondida";
        }
        //diligencia respondida
        elseif ($rsDiligencia->DtSolicitacao && $rsDiligencia->DtResposta != null) {
            //se respondeu mais nao enviou a resposta
            if ($rsDiligencia->stEnviado == 'N' && $prazoResposta > $prazoPadrao) {
                $arrIcones['icone'] = "notice2.png";
                $arrIcones['title'] = "Dilig&ecirc;ncia n&atilde;o respondida";
            } elseif ($rsDiligencia->stEnviado == 'N' && $prazoResposta <= $prazoPadrao) {
                $arrIcones['icone'] = "notice.png";
                $arrIcones['title'] = "Diligenciado";
            } else {
                $arrIcones['icone'] = "notice3.png";
                $arrIcones['title']  = "Dilig&ecirc;ncia respondida";
            }
        }
        //a diligenciar
        else {
            $arrIcones['icone'] = "notice1.png";
            $arrIcones['title']  = "A Diligenciar";
        }
        return $arrIcones;
    }
}
