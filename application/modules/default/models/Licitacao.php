<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Licitacao
 *
 * @author guilherme
 */
class Licitacao extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbLicitacao';
    protected $_schema  = 'bdcorporativo.scSAC';

    public function inserirLicitacao($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarLicitacao($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    /**
     * @todo remover query montada manualment quando pude setar banco.schema em _schema
     */
    public function deletarLicitacao($where)
    {
        $query = "DELETE FROM {$this->_banco}.{$this->_schema}.{$this->_name} WHERE idLicitacao = ?";
        return $this->getAdapter()->query($query, array((int)$where));
    }

    public function buscarLicitacaoProjeto($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('lic'=>$this->_name), array('lic.idLicitacao','lic.nrLicitacao','lic.tpModalidade','lic.dtAberturaLicitacao'));
        $select->joinInner(array('lpa'=>'tbLicitacaoxPlanilhaAprovacao'), 'lic.idLicitacao = lpa.idLicitacao', array(), 'BDCORPORATIVO.scSAC');
        $select->joinInner(array('pa'=>'tbPlanilhaAprovacao'), 'lpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao', array('pa.IdPRONAC'), 'SAC.dbo');
        $select->where('pa.IdPRONAC = ?', $idpronac);
        $select->order('lic.dtAberturaLicitacao');
        $select->group(array('lic.idLicitacao','lic.nrLicitacao','lic.tpModalidade','lic.dtAberturaLicitacao','pa.IdPRONAC'));
        return $this->fetchAll($select);
    }


    public function buscarLicitacao($idLicitacao)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('lic'=>$this->_name),
                    array('idLicitacao',
                          'tpCompra',
                          'tpModalidade',
                          'tpLicitacao',
                          'nrProcesso',
                          'nrLicitacao',
                        new Zend_Db_Expr('CAST(dsObjeto as TEXT) as dsObjeto'),
                          'dsFundamentoLegal',
                          'dtPublicacaoEdital',
                          'dtAberturaLicitacao',
                          'dtEncerramentoLicitacao',
                          'vlLicitacao',
                          'dtHomologacao',
                          'cdMunicipio',
                          'UF',
                        new Zend_Db_Expr('CAST(dsJustificativa AS TEXT) as dsJustificativa')
                        )
                   );
        $slct->joinInner(
                    array('u'=>'UF'),
                    'lic.UF = u.idUF',
                    array('Descricao as dsEstado'),
                    'AGENTES.dbo'
               );
        $slct->joinInner(
                    array('m'=>'Municipios'),
                    'lic.cdMunicipio = m.idMunicipioIBGE',
                    array('Descricao as dsMunicipio'),
                    'AGENTES.dbo'
               );
        $slct->where('idlicitacao = ?', $idLicitacao);

        return $this->fetchAll($slct);
    }
}
