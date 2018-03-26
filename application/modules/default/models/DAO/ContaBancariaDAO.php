<?php
class ContaBancariaDao extends Zend_Db_Table
{
    public static function buscarDadosContaBancaria($idPronac)
    {
        $sql = "select
                    cb.Banco,
                    cb.Agencia,
                    cb.ContaBloqueada,
                    cb.ContaLivre,
                    DtLoteRemessaCB,
                    DtLoteRemessaCL
                from
                    SAC.dbo.ContaBancaria cb
                where
                        (cb.AnoProjeto+cb.Sequencial) = {$idPronac}";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }
}
