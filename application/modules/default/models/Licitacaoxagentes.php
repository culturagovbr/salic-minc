<?php

/**
 * Description of Licitacaoxagentes
 *
 * @author 01610881125
 */
class Licitacaoxagentes extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbLicitacaoxAgentes';
    protected $_schema  = 'scSAC';

    public function inserirLicitacaoxAgentes($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarLicitacaoxAgentes($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarLicitacaoxAgentes($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function buscarFornecedoresLicitacao($idLicitacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('lxa'=>$this->_name),
                        array(
                                'lxa.idAgente','lxa.stVencedor'
                              )
                      );

        $select->joinInner(
                            array('ag'=>'Agentes'),
                            'lxa.idAgente = ag.idAgente',
                            array('ag.CNPJCPF'),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('nm'=>'Nomes'),
                            'ag.idAgente = nm.idAgente',
                            array('nm.Descricao'),
                            'AGENTES.dbo'
                           );
        $select->where('lxa.idLicitacao = ?', $idLicitacao);

        return $this->fetchAll($select);
    }
}
