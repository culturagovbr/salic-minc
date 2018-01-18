<?php
/*Teste*/
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tbEncaminhamentoPrestacaoContas
 *
 * @author Emerson Silva
 */
class EncaminhamentoPrestacaoContas extends MinC_Db_Table_Abstract
{
    protected $_name   = 'tbEncaminhamentoPrestacaoContas';
    protected $_schema = 'BDCORPORATIVO.scSAC';
    protected $_banco  = 'BDCORPORATIVO';



    /*
     * INSERT INTO [BDCORPORATIVO].[scSAC].[tbEncaminhamentoPrestacaoContas]
                    ([idPronac],[idAgenteOrigem],[dtInicioEncaminhamento],[dsJustificativa]
                    ,[idOrgao],[idAgenteDestino],[idTipoAgente],[dtFimEncaminhamento]
                    ,[stAtivo])
        VALUES (0611188,170,'2010-01-17','Modelo de Encaminhamento de Presta��o de Conta para o t�cnico',
                    5,115,9,'2010-04-18',0)
     **/


    /*   public function InsertEncaminhamentoPrestacaoContas($idPronac) {
           $id = $this->insert(array('idPronac'=>$idPronac,'idAgenteOrigem'=>'idAgenteOrigem',
                                     'dtInicioEncaminhamento'=>'dtInicioEncaminhamento',
                                     'dsJustificativa'=>'dsJustificativa','idOrgao'=>'idorgao',
                                     'idAgenteDestino'=>'idAgenteDestino','idTipoAgente'=>'idTipoAgente',
                                     'dtFimEncaminhamento'=>'dtFimEncaminhamento','stAtivo'=>'stAtivo' ));

           return $this->fetchRow($id);
       }

      */

    public function tbEncaminhamentoPrestacaoContas($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('tbepc'=>$this->_name),
                        array(
                                'tbepc.idAgenteDestino','tbepc.idAgenteOrigem',
                                'tbepc.dtInicioEncaminhamento','p.NomeProjeto',
                                'tbepc.idOrgao','u.usu_nome','uu.usu_nome','o.org_sigla'
                              )
                      );

        /*
                $select->joinInner(
                                    array('a'=>'idAgente'),
                                    'a.idAgente = tbepc.idAgenteDestino',
                                    array('u.usu_nome'),
                                    'SAC.dbo'
                                   );
            	$select->joinInner(
                                    array('a'=>'Area'),
                                    'p.Area = a.Codigo',
                                    array('a.Descricao as Area'),
                                    'SAC.dbo'
                                   );
        
                $select->joinInner(
                                    array('s'=>'Segmento'),
                                    'p.Segmento = s.Codigo',
                                    array('s.Descricao as Segmento'),
                                    'SAC.dbo'
                                   );
        */

        $select->where('tbepc.idPronac = ?', '093855');

        return $this->fetchAll($select);
    }
}
