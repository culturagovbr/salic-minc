<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CadastraUsuariosDAO
 *
 * @author tisomar
 */
class CadastraUsuariosDAO extends Zend_Db_Table
{
    public static function cadastraUsuario($idUsuario, $idPessoa, $cpf, $nome, $nomeUsuario, $orgao)
    {
        $sql =
            "SET ANSI_NULLS ON;
             SET ANSI_WARNINGS ON;
            INSERT INTO TABELAS.dbo.Usuarios
            (usu_codigo,usu_identificacao,usu_nome,usu_pessoa,usu_orgao,usu_sala,usu_ramal,usu_nivel,usu_exibicao,usu_SQL_login,usu_SQL_senha,
            usu_duracao_senha,usu_data_validade,usu_limite_utilizacao,usu_senha,usu_validacao, usu_status,usu_seguranca,usu_data_atualizacao,usu_conta_nt,
            usu_dica_intranet,usu_localizacao,usu_andar,usu_telefone)
            VALUES ($idUsuario,'$cpf','$nome', $idPessoa,$orgao,'0',0,9,'S','$nomeUsuario','B',40,2011-02-15,2011-02-15,'~XqkT@X3','~XqkT@',
            0,'~Xqkg',2011-02-15,0,0,1,'','')";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $rs = $db->query($sql);
            return $rs;
        } catch (Exception $e) {
            parent::message($e->getMessage(), "principal", "ERROR");
        }
    }
}
