<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cotacaoxagentes
 *
 * @author 01610881125
 */
class Cotacaoxagentes extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbCotacaoxAgentes';
    protected $_schema  = 'scSAC';

    public function inserirCotacaoxAgentes($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarCotacaoxAgentes($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarCotacaoxAgentes($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function buscarAgentes($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('cxa'=>$this->_name),
                        array('cxa.idAgente', 'vlCotacao')
                      );

        $select->joinInner(
                            array('ag'=>'Agentes'),
                            'cxa.idAgente = ag.idAgente',
                            array('ag.CNPJCPF','ag.TipoPessoa'),
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
        return $this->fetchAll($select);
    }

    public function verificarAgenteXCotacao($idcotacao, $idAgente)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('cxa'=>$this->_name),
            array('cxa.idAgente')
        );

        $select->where('idCotacao = ?', $idcotacao);
        $select->where('idAgente = ?', $idAgente);
        return $this->fetchRow($select);
    }
}
