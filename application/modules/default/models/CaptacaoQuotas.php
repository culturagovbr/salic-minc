<?php
/**
 * Description of Sgcacesso
 *
 * @author augusto
 */

class CaptacaoQuotas extends MinC_Db_Table_Abstract
{
    protected $_banco   = "SAC";
    protected $_schema  = "SAC";
    protected $_name    = "CaptacaoQuotas";

    public function buscarCaptacaoQuotas()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this);
        return $this->fetchAll($select);
    } // fecha m�todo buscarCaptacaoQuotas()

    public function BuscarTotalCaptadoQuotas($retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array($this->_name),
                array(
                'CaptacaoQuotas.AnoProjeto',
                'CaptacaoQuotas.Sequencial',
                'Art1'=> 'isnull(SUM(CaptacaoQuotas.QtdQuotasIntegr*QuotasCav.VlQuota),0)'
                )
        );
        $select->joinInner(
                array('QuotasCav'),
                "CaptacaoQuotas.AnoProjeto = QuotasCav.AnoProjeto and CaptacaoQuotas.Sequencial = QuotasCav.Sequencial and CaptacaoQuotas.AnoCav = QuotasCav.AnoCav
                and CaptacaoQuotas.SequencialCav = QuotasCav.SequencialCav",
                array()
        );
        $select->where('QuotasCav.InclusCancel = ?', 1);

        $select->group('CaptacaoQuotas.AnoProjeto');
        $select->group('CaptacaoQuotas.Sequencial');

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }


    public function CapitacaoArt1($AnoProjeto, $Sequencial)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('c'=>$this->_name),
                        array('Art1' => new Zend_Db_Expr("SUM(c.QtdQuotasIntegr*q.VlQuota)"))
                      );
        $select->joinInner(
                array("q"=>"QuotasCav"),
                                "c.AnoProjeto = q.AnoProjeto and
                                 c.Sequencial = q.Sequencial and
                                 c.AnoCav = q.AnoCav and
                                 c.SequencialCav = q.SequencialCav",
                array(""),
                "SAC.dbo"
            );

        $select->where('c.AnoProjeto = ?', $AnoProjeto, 'c.Sequencial = ?', $Sequencial);

            
        return $this->fetchAll($select);
    } // fecha m�todo listasituacao()
} // fecha class
