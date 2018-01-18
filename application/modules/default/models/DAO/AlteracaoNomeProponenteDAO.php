<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AlteracaoNomeProponenteDAO
 *
 * @author 01129075125
 */
class AlteracaoNomeProponenteDAO extends Zend_Db_Table
{
    public static function buscarProjPorProp($cgccpf)
    {
        $sql = "select
                    prop.Sequencial+prop.AnoProjeto as IdPRONAC,
                    prop.NomeProjeto,
                    sit.codigo+' - '+sit.descricao as situacao
                from
                    SAC.dbo.Projetos prop
                    inner join SAC.dbo.Situacao sit on sit.codigo =  prop.situacao
                where
                    CgcCpf = '{$cgccpf}'
                    ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }


    public static function buscarDadosParecerTecnico($idpedidoalteracao)
    {
        $sql = "select
                    aipa.dsAvaliacao as dsparecertecnico,
                    aipa.dtFimAvaliacao as dtparecertecnico,
                    nom.Descricao as nometecnico
                from
                    BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao aipa
                    inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao pt on pt.idPedidoAlteracao = aipa.idPedidoAlteracao
                    inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto pap on pap.idPedidoAlteracao = pt.idPedidoAlteracao
                    inner join AGENTES.dbo.Nomes nom on nom.idAgente = aipa.idAgenteAvaliador
                where
                    pap.IdPRONAC = {$idpedidoalteracao} and pt.tpAlteracaoProjeto = 1
                ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
}
