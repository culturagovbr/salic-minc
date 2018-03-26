<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VincularresponsavelDAO
 *
 * @author tisomar
 */
class VincularresponsavelDAO extends Zend_Db_Table
{
    public static function buscaragentes($proponente=null, $cnpjcpfsuperior=null)
    {
        $sql = "SELECT a.CNPJCPF,n.Descricao AS NomeAgente, a.idAgente
                       FROM Agentes.dbo.Agentes as a
                           INNER JOIN Agentes.dbo.Nomes as n on (a.idAgente = n.idAgente) 
                           ";

        if (!empty($proponenteFinal)) {
            $sql .= " WHERE n.Descricao = '$proponenteFinal'";
        } else {
            $sql .= " WHERE a.CNPJCPF =  '$cnpjcpfsuperior' ";
        }


//               $slct = $this->select();
//                $slct->setIntegrityCheck(false);
//                $slct->from(array('a' => 'Agentes'),
//                                array('CNPJCPF',
//                                      'idAgente'
//                                     )
//                        )
//                ->joinInner(
//                        array('n' => 'Nomes'),
//                        'a.idAgente = n.idAgente',
//                        array('Descricao'),
//                        'AGENTES.dbo'
//                )
//
//                ->where('pr.IdPRONAC= ?', $idPronac)
//                ->where('tpd.idProduto IS NOT NULL')
//                ->where('pd.Descricao IS NOT NULL');
//



        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
}
