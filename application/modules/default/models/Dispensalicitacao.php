<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dispensalicitacao.php
 *
 * @author guilherme
 */
class Dispensalicitacao extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbDispensaLicitacao';
    protected $_schema  = 'bdcorporativo.scSAC';

    public function inserirDispensaLicitacao($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarDispensaLicitacao($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarDispensaLicitacao($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function buscarDispensaProjeto($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('dis'=>$this->_name),
                            array('dis.idDispensaLicitacao','dis.nrDispensaLicitacao','CAST(dis.dsDispensaLicitacao as TEXT) as dsDispensaLicitacao','dis.dtContrato')
                          );

        $select->joinInner(
                            array('dlpa'=>'tbDispensaLicitacaoxPlanilhaAprovacao'),
                            'dis.idDispensaLicitacao = dlpa.idDispensaLicitacao',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        $select->joinInner(
                            array('pa'=>'tbPlanilhaAprovacao'),
                            'dlpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
                            array('pa.IdPRONAC'),
                            'SAC.dbo'
                           );

        $select->where('pa.IdPRONAC = ?', $idpronac);
        $select->order('dis.dtContrato');
        $select->group(array(
                        'dis.idDispensaLicitacao','dis.nrDispensaLicitacao','dis.dsDispensaLicitacao','dis.dtContrato','pa.IdPRONAC'
                      ));

        return $this->fetchAll($select);
    }

    public function buscarDispensaLicitacao($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('dis'=>$this->_name),
                        array(
                                'dis.idDispensaLicitacao','dis.nrDispensaLicitacao','CAST(dis.dsDispensaLicitacao as TEXT) as dsDispensaLicitacao','dis.dtContrato','dis.vlContratado'
                              )
                      );

        $select->joinInner(
                            array('ag'=>'Agentes'),
                            'dis.idAgente = ag.idAgente',
                            array('ag.idAgente','ag.CNPJCPF','ag.TipoPessoa'),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('nm'=>'Nomes'),
                            'nm.idAgente = ag.idAgente',
                            array('nm.Descricao'),
                            'AGENTES.dbo'
                           );
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('dis.dtContrato');

        return $this->fetchAll($select);
    }
}
