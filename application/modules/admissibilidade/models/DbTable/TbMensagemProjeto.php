<?php

/**
 * @name Admissibilidade_Model_DbTable_Gerenciarparecertecnico
 * @package modules/admissibilidade
 * @subpackage models/DbTable
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 05/12/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Admissibilidade_Model_DbTable_TbMensagemProjeto extends MinC_Db_Table_Abstract
{
    protected $_schema    = 'bdcorporativo.scsac';
    protected $_name      = 'tbMensagemProjeto';
    protected $_primary   = 'idMensagemProjeto';
    public function getAllBy($where = array(), $orWhere = array())
    {
//        $selectSub = $this->select()
//            ->setIntegrityCheck(false)->from(array('tmp2' => $this->_name),
//                array('count(tmp2.idMensagemOrigem)'),
//                $this->_schema)->where("tmp2.idMensagemOrigem = {$this->_name}.idMensagemProjeto");
        $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(
                        $this->_name,
                        array('idMensagemProjeto',
                              'dtMensagem',
                              'dtMensagemTreated' => $this->getExpressionToChar($this->_name . '.dtMensagem') . $this->getExpressionConcat() . " ' ' " . $this->getExpressionConcat()  .  $this->getExpressionToChar($this->_name . '.dtMensagem', 108),
                              'dsMensagem',
                              'stAtivo',
                              'cdTipoMensagem',
                              'idDestinatario',
                              'idRemetente',
                              'IdPRONAC',
                              'qtdResposta' => "(SELECT count(tmp2.idMensagemOrigem) FROM {$this->_schema}.{$this->_name} tmp2 WHERE tmp2.idMensagemOrigem = {$this->_name}.idMensagemProjeto)",
                              'idMensagemOrigem'),
                        $this->_schema
                    )
                    ->joinLeft(
                        array('usuariosDestinatario' => 'usuarios'),
                        'tbMensagemProjeto.idDestinatario = usuariosDestinatario.usu_codigo',
                        'usu_nome as usu_nome_destinatario',
                        $this->getSchema('tabelas')
                    )
                    ->joinLeft(
                        array('usuariosRemetente' => 'usuarios'),
                        'tbMensagemProjeto.idRemetente = usuariosRemetente.usu_codigo',
                        'usu_nome as usu_nome_remetente',
                        $this->getSchema('tabelas')
                    )
                    ->joinLeft(
                        array('tbMensagemProjetoResposta' => $this->_name),
                        'tbMensagemProjeto.idMensagemProjeto = tbMensagemProjetoResposta.idMensagemOrigem',
                        array(
                            'tbMensagemProjetoResposta.dtMensagem as dtResposta',
                            'tbMensagemProjetoResposta.dsMensagem as dsResposta',
                        ),
                        $this->_schema
                    )
                    ->joinLeft(
                        array('OrgaosRemetente' => 'Orgaos'),
                        'tbMensagemProjeto.idRemetenteUnidade = OrgaosRemetente.Codigo',
                        'Sigla as remetenteUnidadeNome',
                        $this->getSchema('sac')
                    )
                    ->joinLeft(
                        array('OrgaosDestinatario' => 'Orgaos'),
                        'tbMensagemProjeto.idDestinatarioUnidade = OrgaosDestinatario.Codigo',
                        'Sigla as destinatarioUnidadeNome',
                        $this->getSchema('sac')
                    );
        parent::setWhere($select, $where);
        parent::setWhere($select, $orWhere, 'orWhere');
        $select->order('dtMensagem DESC');

        $arrResult = ($arrResult = $this->fetchAll($select))? $arrResult->toArray() : array();
        foreach ($arrResult as &$arrValue) {
            $arrValue['dsMensagem'] = strip_tags($arrValue['dsMensagem']);
            $arrValue['dsResposta'] = strip_tags($arrValue['dsResposta']);
            if (strlen($arrValue['dsMensagem']) > 50) {
                $arrValue['dsMensagem'] = substr($arrValue['dsMensagem'], 0, 50) . '...';
            }
            if (strlen($arrValue['dsResposta']) > 50) {
                $arrValue['dsResposta'] = substr($arrValue['dsResposta'], 0, 50) . '...';
            }
            $date = new DateTime($arrValue['dtMensagem']);
            if ($arrValue['dtResposta']) {
                $date2 = new DateTime($arrValue['dtResposta']);
            } else {
                $date2 = new DateTime();
            }
            $arrValue['tempoResposta'] = $date->diff($date2)->days;
//            $arrValue['dtResposta'] = strip_tags($arrValue['dsMensagem']);
        }

        return $arrResult;
    }
}
