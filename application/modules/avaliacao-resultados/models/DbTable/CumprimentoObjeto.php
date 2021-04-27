<?php

class AvaliacaoResultados_Model_DbTable_CumprimentoObjeto extends MinC_Db_Table_Abstract
{
    protected $_name = "tbCumprimentoObjeto";
    protected $_schema = "SAC";
    protected $_primary = "IdPronac";

    public function buscarObjeto($id)
    {
        $sql = " SELECT DtEnvioDaPrestacaoContas, stResultadoAvaliacao,
                    CASE
                        WHEN stResultadoAvaliacao = 'A' THEN 'APROVADO'
                        WHEN stResultadoAvaliacao = 'P' THEN 'APROVADO COM RESSALVAS'
                        WHEN stResultadoAvaliacao = 'R' THEN 'REPROVADO'
                    END AS dsManifestacaoObjeto,
                    '<b>PARECER DE AVALIA&Ccedil;&Atilde;O T&Eacute;CNICA DO CUMPRIMENTO DO OBJETO</B><br/><br/>'                                            + dsInformacaoAdicional +
                    '<b>ORIENTA&Ccedil;&Otilde;ES</B><br/><br/>'                                                                                     + dsOrientacao +
                    '<b>CONCLUS&Atilde;O DO PARECER DE AVALIA&Ccedil;&Atilde;O T&Eacute;CNICA QUANTO &Agrave; EXECU&Ccedil;&Atilde;O DO OBJETO E DOS OBJETIVOS DO PROJETO</B><br/><br/>' + dsConclusao as dsParecerDeCumprimentoDoObjeto
                    
                  FROM SAC.dbo.tbCumprimentoObjeto where IdPRONAC ='$id'
        ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);

        return $db->fetchRow($sql);
    }
}
