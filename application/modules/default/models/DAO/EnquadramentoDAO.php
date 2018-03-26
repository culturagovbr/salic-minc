<?php
class EnquadramentoDAO extends Zend_Db_Table
{
    public static function AlterarEnquadramento($dados, $idpronac)
    {
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $where = "idpronac = $idpronac";
            $alterar = $db->update("SAC.dbo.Enquadramento", $dados, $where);
        } catch (Exception $e) {
            $this->message->view = "ERRO" . $e->getMessage();
        }
    }
}
