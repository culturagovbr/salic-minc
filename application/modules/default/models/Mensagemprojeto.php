<?php
/**
 * Description of Mensagemprojeto
 *
 * @author augusto
 */
class Mensagemprojeto extends MinC_Db_Table_Abstract
{
    protected $_banco = 'BDCORPORATIVO';
    protected $_name = 'BDCORPORATIVO.tbmensagemprojeto';
    protected $_schema = 'scSAC';

    public function buscarMensagemProjeto($where = array(), $orwhere=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('mp' => $this->_name),
                array(
                    'mp.idMensagemProjeto',
                    'mp.IdPRONAC',
                    'mp.dtMensagem',
                    'mp.dsMensagem',
                    'mp.idDestinatario'
                )
        );
        $select->joinInner(
                array('nmd' => 'Nomes'),
                "nmd.idAgente = mp.idDestinatario",
                array(
                    'nmd.Descricao as nomeDestinatario',
                ),
                'Agentes.dbo'
        );
        $select->joinInner(
                array('nmr' => 'Nomes'),
                "nmr.idAgente = mp.idRemetente",
                array('nmr.Descricao as nomeRemetente'),
                'Agentes.dbo'
        );
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        foreach ($orwhere as $coluna => $valor) {
            $select->orwhere($coluna, $valor);
        }
        return $this->fetchAll($select);
    }

    public function inserirMensagemProjeto($dados)
    {
        try {
            $inserir = $this->insert($dados);
            return $inserir;
        } catch (Zend_Db_Adapter_Exception $e) {
            return 'Mensagemprojeto->inserirMensagemProjeto . Erro:' . $e->getChainedException();
        }
    }

    public function alterarMensagemProjeto($dados, $where)
    {
        try {
            $update = $this->update($dados, $where);
        } catch (Zend_Db_Adapter_Exception $e) {
            return 'Mensagemprojeto->alterarMensagemProjeto . Erro:' . $e->getChainedException();
        }
    }
}
