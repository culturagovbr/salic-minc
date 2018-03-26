<?php
/**
 * Helper para verificar a diligencia do projeto
 * @author XTI
 * @since 19/03/2012
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_BuscarTextoTermoDecisao
{
    public function buscarTextoTermoDecisao()
    {
        return $this;
    }
        
    public function buscarDadosTermoDecisao($idPronac = null, $idOrgao = null, $idTipoTermo = null, $idTipoParecer = null, $arrDados=array())
    {
        $tipoTermo   = $this->retornarCodigoTipoTermo($idTipoTermo);
        $tipoParecer = $this->retornarCodigoTipoParecer($idTipoParecer);

        $textoTermo = null;
            
        if (!empty($idOrgao) && !empty($idTipoTermo) && !empty($idTipoParecer)) {
            $tbTermoDecisao = new tbModeloTermoDecisao();
            $arrBusca['idVerificacao = ?']          = $tipoTermo;
            $arrBusca['stModeloTermoDecisao = ?']   = $tipoParecer;
            $arrBusca['idOrgao = ?']                = $idOrgao;
            $rsTermo = $tbTermoDecisao->buscarTermoDecisao($arrBusca)->current();
            $textoTermo = (!empty($rsTermo->meModeloTermoDecisao)) ? $this->parseTag($rsTermo->meModeloTermoDecisao, $arrDados) : "";
        }
            
        return $textoTermo;
    }
        
    //retorna codigo do termo no banco de dados
    public function parseTag($textoTermo, $arrDados)
    {
        $prefixoSugestaoPlenaria = "<b>c) Sugestão da Plenária da CNIC</b><br><br>";
        $sugestaoPlenaria = (!empty($arrDados['parecerCNIC'])) ? $prefixoSugestaoPlenaria.$arrDados['parecerCNIC'] : "";
            
        $arr1 = array("{@SUGESTAO_PARECER@}",//====== 1
                          "{@NUMERO_PLENARIA@}",//======= 2
                          "{@DIA_INICIO_PLENARIA@}",//=== 3
                          "{@DT_FIM_PLENARIA@}",//======= 4
                          "{@NUMERO_PRONAC@}",//========= 5
                          "{@NOME_PROJETO@}",//========== 6
                          "{@SUGESTAO_MEMBRO_RELATOR@}",//== 7
                          "{@SUGESTAO_PLENARIA@}",//======== 8
                          "{@NOME_SECRETARIO@}",//========== 9
                          "{@CARGO_SECRETARIO@}",//========= 10
                          "{@DATA_ASS_TERMO@}",//=============== 11
                        );
            
        $arr2 = array($arrDados['parecerParecerista'],//===== 1
                          $arrDados['numReuniao'], //============ 2
                          $arrDados['diaInicioReuniao'],//======= 3
                          $arrDados['dtFinalReuniao'], //======== 4
                          '<b>'.$arrDados['PRONAC'].'</b>', //================ 5
                          '<b>'.$arrDados['NomeProjeto'].'</b>',//============ 6
                          $arrDados['parecerComponente'],//====== 7
                          $sugestaoPlenaria,//============ 8
                          $arrDados['nomeSecretario'],//========= 9
                          $arrDados['cargoSecretario'],//======== 10
                          $arrDados['dtAssinatura']//============ 11
                        );
            
        $textoTermoAlterado = str_replace($arr1, $arr2, $textoTermo);

        return $textoTermoAlterado;
    }
        
    //retorna codigo do termo no banco de dados
    public function retornarCodigoTipoTermo($idTipoTermo)
    {
        switch ($idTipoTermo) { //ANALISE INICIAL / READEQUACAO / RECURSO
                case('AN'): //ANALISE INICIAL
                {
                    return Constantes::cteIdVerificacaoTipoTermoAnaliseInicial;
                }
                case('AR'): //ANALISE READEQUACAO
                {
                    return Constantes::cteIdVerificacaoTipoTermoReadequacao;
                }
                case('RE'): //RECURSO
                {
                    return Constantes::cteIdVerificacaoTipoRecurso;
                }
                default: //
                    return 0;
            }
    }
        
    //retorna codigo do termo no banco de dados
    public function retornarCodigoTipoParecer($idTipoParecer)
    {
        switch ($idTipoParecer) { //APROVADO / INDEFERIDO
                case('A'): //APROVADO
                {
                    return '1';
                }
                case('I'): //INDEFERIDO
                {
                    return '0';
                }
                default: //
                    return 0;
            }
    }
} // fecha class
