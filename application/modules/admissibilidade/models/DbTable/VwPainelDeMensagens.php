<?php

class Admissibilidade_Model_DbTable_VwPainelDeMensagens extends MinC_Db_Table_Abstract
{
    protected $_schema    = 'SAC';
    protected $_name      = 'vwPainelDeMensagens';
    protected $_primary   = 'IdPRONAC';

    public function carregarPerguntasSemResposta($intIdUsuario, $intIdOrgao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            $this->_name,
                array(
                    'IdPRONAC',
                    'PRONAC',
                    'NomeProjeto',
                    'CodArea',
                    'Area',
                    'CodSegmento',
                    'Segmento',
                    'Enquadramento',
                    'VlSolicitado',
                    'idMensagemProjeto',
                    'dtMensagem' => new Zend_Db_Expr($this->getExpressionToChar($this->_name . '.dtMensagem') . $this->getExpressionConcat() . " ' ' " . $this->getExpressionConcat()  .  $this->getExpressionToChar($this->_name . '.dtMensagem', 108)),
                    'QtdeDias',
                    'dsMensagem',
                    'cdTipoMensagem',
                    'idRemetente',
                    'idRemetenteUnidade',
                    'idDestinatario',
                    'idDestinatarioUnidade',
                    'idMensagemOrigem',
                    'stAtivo'
                ),
                $this->_schema
        )
            ->joinLeft(
                array('tbMensagemProjetoResposta' => 'tbMensagemProjeto'),
                $this->_name . '.idMensagemProjeto = tbMensagemProjetoResposta.idMensagemOrigem',
                array(
                    'tbMensagemProjetoResposta.dtMensagem as dtResposta',
                    'tbMensagemProjetoResposta.dsMensagem as dsResposta',
                ),
                $this->getSchema('bdcorporativo.scsac')
            )
            ->joinLeft(
                array('usuariosDestinatario' => 'usuarios'),
                $this->_name . '.idDestinatario = usuariosDestinatario.usu_codigo',
                'usu_nome as usu_nome_destinatario',
                $this->getSchema('tabelas')
            )
            ->joinLeft(
                array('usuariosRemetente' => 'usuarios'),
                $this->_name . '.idRemetente = usuariosRemetente.usu_codigo',
                'usu_nome as usu_nome_remetente',
                $this->getSchema('tabelas')
            )
            ->joinLeft(
                array('OrgaosRemetente' => 'Orgaos'),
                $this->_name . '.idRemetenteUnidade = OrgaosRemetente.Codigo',
                'Sigla as remetenteUnidadeNome',
                $this->getSchema('sac')
            )
            ->joinLeft(
                array('OrgaosDestinatario' => 'Orgaos'),
                $this->_name . '.idDestinatarioUnidade = OrgaosDestinatario.Codigo',
                'Sigla as destinatarioUnidadeNome',
                $this->getSchema('sac')
            );
        $select->where('vwPainelDeMensagens.idMensagemOrigem IS NULL');
        $select->where('vwPainelDeMensagens.stAtivo = ?', 1);
        $select->where('tbMensagemProjetoResposta.dsMensagem IS NULL');
        $select->where("vwPainelDeMensagens.idDestinatario = {$intIdUsuario} OR (vwPainelDeMensagens.idDestinatario IS NULL AND vwPainelDeMensagens.idDestinatarioUnidade = {$intIdOrgao})");

        $arrResult = ($arrResult = $this->fetchAll($select))? $arrResult->toArray() : array();
        return $arrResult;
    }

    public function findAllCustom($arrWhere = array(), $arrOrWhere = array())
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                $this->_name,
                array(
                    'IdPRONAC',
                    'PRONAC',
                    'NomeProjeto',
                    'CodArea',
                    'Area',
                    'CodSegmento',
                    'Segmento',
                    'Enquadramento',
                    'VlSolicitado',
                    'idMensagemProjeto',
                    'dtMensagem' => $this->getExpressionToChar($this->_name . '.dtMensagem') . $this->getExpressionConcat() . " ' ' " . $this->getExpressionConcat()  .  $this->getExpressionToChar($this->_name . '.dtMensagem', 108),
                    'QtdeDias',
                    'dsMensagem',
                    'cdTipoMensagem',
                    'idRemetente',
                    'idRemetenteUnidade',
                    'idDestinatario',
                    'idDestinatarioUnidade',
                    'idMensagemOrigem',
                    'stAtivo'
                ),
                $this->_schema
            )
            ->joinLeft(
                array('tbMensagemProjetoResposta' => 'tbMensagemProjeto'),
                $this->_name . '.idMensagemProjeto = tbMensagemProjetoResposta.idMensagemOrigem',
                array(
                    'tbMensagemProjetoResposta.dtMensagem as dtResposta',
                    'tbMensagemProjetoResposta.dsMensagem as dsResposta',
                ),
                $this->getSchema('bdcorporativo.scsac')
            )
            ->joinLeft(
                array('usuariosDestinatario' => 'usuarios'),
                $this->_name . '.idDestinatario = usuariosDestinatario.usu_codigo',
                'usu_nome as usu_nome_destinatario',
                $this->getSchema('tabelas')
            )
            ->joinLeft(
                array('usuariosRemetente' => 'usuarios'),
                $this->_name . '.idRemetente = usuariosRemetente.usu_codigo',
                'usu_nome as usu_nome_remetente',
                $this->getSchema('tabelas')
            )
            ->joinLeft(
                array('OrgaosRemetente' => 'Orgaos'),
                $this->_name . '.idRemetenteUnidade = OrgaosRemetente.Codigo',
                'Sigla as remetenteUnidadeNome',
                $this->getSchema('sac')
            )
            ->joinLeft(
                array('OrgaosDestinatario' => 'Orgaos'),
                $this->_name . '.idDestinatarioUnidade = OrgaosDestinatario.Codigo',
                'Sigla as destinatarioUnidadeNome',
                $this->getSchema('sac')
            );
        parent::setWhere($select, $arrWhere);
        parent::setWhere($select, $arrOrWhere, 'orWhere');

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
