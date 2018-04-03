<?php

/**
 * @todo Mover todas os métodos e alterar todas as referências para da antiga classe para essa.
 */
class Projeto_Model_DbTable_VwPainelDeHomologacaoDeProjetos extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'vwPainelDeHomologacaoDeProjetos';
    protected $_primary = 'IdPRONAC';
    //
    // public function alterarOrgao($orgao, $idPronac) {
    //     $this->update(
    //         array(
    //             'Orgao' => $orgao
    //         ),
    //         array('IdPRONAC = ?' => $idPronac)
    //     );
    // }
    //
    // public function obterValoresProjeto($idPronac) {
    //     $objQuery = $this->select();
    //     $objQuery->setIntegrityCheck(false);
    //     $objQuery->from(
    //         array(
    //             'projetos' => $this->_name
    //         )
    //         ,array(
    //             "ValorProposta" => new Zend_Db_Expr("sac.dbo.fnValorSolicitado(projetos.AnoProjeto,projetos.Sequencial)"),
    //             "ValorSolicitado" => new Zend_Db_Expr("sac.dbo.fnValorSolicitado(projetos.AnoProjeto,projetos.Sequencial)") ,
    //             "OutrasFontes" => new Zend_Db_Expr("sac.dbo.fnOutrasFontes(projetos.idPronac)"),
    //             "ValorAprovado" => new Zend_Db_Expr(
    //                 "case when projetos.Mecanismo ='2' or projetos.Mecanismo ='6'
    //                     then sac.dbo.fnValorAprovadoConvenio(projetos.AnoProjeto,projetos.Sequencial)
    //                  else
    //                     sac.dbo.fnValorAprovado(projetos.AnoProjeto,projetos.Sequencial)
    //                  end"
    //             ),
    //             "ValorProjeto" => new Zend_Db_Expr(
    //                 "case when projetos.Mecanismo ='2' or projetos.Mecanismo ='6'
    //                  then sac.dbo.fnValorAprovadoConvenio(projetos.AnoProjeto,projetos.Sequencial)
    //                  else sac.dbo.fnValorAprovado(projetos.AnoProjeto,projetos.Sequencial) + sac.dbo.fnOutrasFontes(projetos.idPronac)
    //                   end "
    //             ),
    //             "ValorCaptado" => new Zend_Db_Expr("sac.dbo.fnCustoProjeto (projetos.AnoProjeto,projetos.Sequencial)"),
    //         )
    //     );
    //     $objQuery->where('projetos.IdPRONAC = ?', $idPronac);
    //
    //     return $this->_db->fetchRow($objQuery);
    // }
    //
    // public function verificarIN2017($idPronac)
    // {
    //     $select = $this->select();
    //     $select->setIntegrityCheck(false);
    //     $select->from(
    //         array('projetos' => $this->_name),
    //         'idPRONAC',
    //         $this->_schema
    //     );
    //     $select->where("CONVERT(CHAR(10), (DtProtocolo),112) >= '20170320' AND idPronac = ?", $idPronac);
    //
    //     return $this->_db->fetchRow($select);
    // }
    //
    // /*
    // * Verifica se existe uma proposta relacionada ao projeto
    // * Projetos mais antigos eram impressos e não possuiam propostas
    // *
    // * Substitui a fnVerificarExistenciaDaProposta
    // */
    // public function verificarSeProjetoPossuiProposta($idPronac)
    // {
    //     $db = Zend_Db_Table::getDefaultAdapter();
    //     $db->setFetchMode(Zend_DB::FETCH_OBJ);
    //
    //     $sql = $db->select()
    //         ->from(array('p' => $this->_name), array(), $this->_schema)
    //         ->join(array('pre' => 'preprojeto'), 'pre.idPreProjeto = p.idProjeto', array('pre.idPreProjeto'), $this->_schema)
    //         ->where('p.idPronac = ?', $idPronac);
    //
    //     return $db->fetchOne($sql);
    // }
    //
    // public function fnChecarLiberacaoDaAdequacaoDoProjeto($idPronac) {
    //
    //     $exec = new Zend_Db_Expr("SELECT dbo.fnChecarLiberacaoDaAdequacaoDoProjeto({$idPronac})");
    //
    //     try {
    //         $db= Zend_Db_Table::getDefaultAdapter();
    //     } catch (Zend_Exception_Db $e) {
    //         $this->view->message = $e->getMessage();
    //     }
    //     return $db->fetchOne($exec);
    // }
    //
    // public function spClonarProjeto($idPronac, $usuarioLogado) {
    //
    //     $exec = new Zend_Db_Expr("EXEC SAC.dbo.spClonarProjeto {$idPronac}, {$usuarioLogado}");
    //
    //     try {
    //         $db= Zend_Db_Table::getDefaultAdapter();
    //     } catch (Zend_Exception_Db $e) {
    //         $this->view->message = $e->getMessage();
    //     }
    //     return $db->fetchRow($exec);
    // }

}
