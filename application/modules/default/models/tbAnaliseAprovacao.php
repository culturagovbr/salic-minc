<?php
/**
 * DAO tbAnaliseAprovacao
 * @author jefferson.silva - XTI
 * @since 18/09/2013
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbAnaliseAprovacao extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbAnaliseAprovacao";

    public function copiandoPlanilhaRecurso($idPronac)
    {
        $sql = "INSERT INTO SAC.dbo.tbAnaliseAprovacao
                     (tpAnalise,dtAnalise,idAnaliseConteudo,IdPRONAC,idProduto,stLei8313,stArtigo3,nrIncisoArtigo3,dsAlineaArt3,stArtigo18,dsAlineaArtigo18,stArtigo26,stLei5761,
                      stArtigo27,stIncisoArtigo27_I,stIncisoArtigo27_II,stIncisoArtigo27_III,stIncisoArtigo27_IV,stAvaliacao,dsAvaliacao,idAgente,idAnaliseAprovacaoPai)
              SELECT 'CO',GETDATE(),idAnaliseDeConteudo,'$idPronac',idProduto,Lei8313,Artigo3,IncisoArtigo3,AlineaArtigo3,Artigo18,AlineaArtigo18,Artigo26,Lei5761,
                    Artigo27,IncisoArtigo27_I, IncisoArtigo27_II, IncisoArtigo27_III, IncisoArtigo27_IV, ParecerFavoravel, ParecerDeConteudo,NULL,NULL
                FROM SAC.dbo.tbAnaliseDeConteudo WHERE IdPRONAC='$idPronac'

        ";
//
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // retornando os registros conforme objeto select
        return $db->fetchAll($sql);
    }
}
