<?php
/**
 * Description of Projetos
 *
 * @author Andr� Nogueira Pereira
 */
class Fiscalizacao extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbFiscalizacao';
    protected $_schema = 'SAC';
    protected $_banco = 'SAC';

    public function buscaFiscalizacao($idFiscalizacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('Fisc' => $this->_name),
                array('Fisc.idFiscalizacao'
                      ,'Fisc.IdPRONAC'
                      ,'Fisc.dtInicioFiscalizacaoProjeto'
                      ,'Fisc.dtFimFiscalizacaoProjeto'
                      ,'Fisc.dtRespostaSolicitada'
                      ,'CAST(Fisc.dsFiscalizacaoProjeto AS TEXT) as dsFiscalizacaoProjeto'
                      ,'Fisc.tpDemandante'
                      ,'Fisc.stFiscalizacaoProjeto'
                      ,'Fisc.idAgente'
                      ,'Fisc.idSolicitante')
        );
        $select->joinInner(
                array('ar' => 'Area'),
                'ar.Codigo = Fisc.Area',
                array('ar.Codigo as area')
        );
        $select->joinLeft(
            array('sg' => 'Segmento'),
                'sg.Codigo = Fisc.Segmento',
                array('sg.Codigo as segmento')
        );
        $select->where('Fisc.idFiscalizacao = ? ', $idFiscalizacao);

        return $this->fetchRow($select);
    }
    public function alteraSituacaoProjeto($situacao, $idFiscalizacao)
    {
        try {
            $dados = array('stFiscalizacaoProjeto'=>$situacao);
            $where = array('idFiscalizacao = ?'=>$idFiscalizacao);

            return $this->update($dados, $where);
        } catch (Zend_Db_Table_Exception $e) {
            return 'RelatorioFiscalizacao -> alteraRelatorio. Erro:' . $e->getMessage();
        }
    }
    public function filtroFiscalizacao($retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array($this->_name),
                array('IdPRONAC')
        );
        //$select->where("tbFiscalizacao.stFiscalizacaoProjeto = 'S'");
        $select->Where("tbFiscalizacao.stFiscalizacaoProjeto = '0'");
        $select->orWhere("tbFiscalizacao.stFiscalizacaoProjeto = '1'");

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }
}
