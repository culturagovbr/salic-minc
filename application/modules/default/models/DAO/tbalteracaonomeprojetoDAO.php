<?php

class tbalteracaonomeprojetoDAO extends Zend_Db_Table
{
    protected $_name = "BDCORPORATIVO.scSAC.tbalteracaonomeprojeto";

    public static function buscarDadosNmProj($idpedidoalteracao)
    {
        $sql = "       select tba.nmprojeto,
                       tba.dsjustificativa,
                       tap.idPRONAC
                       from BDCORPORATIVO.scSAC.tbalteracaonomeprojeto tba
                       join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tap on tap.idPedidoAlteracao = tba.idPedidoAlteracao
                       where tba.idpedidoalteracao= ".$idpedidoalteracao;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarDadosParecerTecnico($idpedidoalteracao)
    {
        $sql = "select dtparecertecnico,
                       dsparecertecnico,
                       nom.Descricao as nometecnico
                       from BDCORPORATIVO.scSAC.tbavaliacaopedidoalteracao apa
                       join AGENTES.dbo.Nomes nom on nom.idAgente =  apa.idTecnico
                       where apa.idpedidoalteracao= ".$idpedidoalteracao;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function alterarNomeProjeto($dados, $idpronac)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "idpronac = ".$idpronac;
        $alterar = $db->update("SAC.dbo.projetos", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }
}
