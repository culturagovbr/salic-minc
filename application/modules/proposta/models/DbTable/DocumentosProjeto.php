<?php

/**
 * Class Proposta_Model_DbTable_DocumentosProjeto
 *
 * @name Proposta_Model_DbTable_DocumentosProjeto
 * @package Modules/Proposta
 * @subpackage Models/DbTable
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 02/10/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Proposta_Model_DbTable_DocumentosProjeto extends MinC_Db_Table_Abstract
{
    protected $_banco = "sac";
    protected $_schema = "sac";
    protected $_name = 'documentosprojeto';

    public function documentosAnexados($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('a1' => 'tbDocumentosAgentes'),
            array("idDocumentosAgentes as idDocumento",
                "NoArquivo as nome",
                new Zend_Db_Expr("'antigo-agente' as tipoDocumento"),
                new Zend_Db_Expr("'Anexado pelo Proponente' as Classificacao")
            ),
            'SAC.dbo'
        );
        $a->joinInner(
            array('ag' => 'Agentes'),
            "ag.idAgente = a1.idAgente",
            array(),
            'AGENTES.dbo'
        );
        $a->joinInner(
            array('pr' => 'Projetos'),
            "pr.CgcCpf = ag.CNPJCPF",
            array("IdPRONAC as idpronac"),
            'SAC.dbo'
        );
        $a->where('pr.IdPRONAC = ?', $idPronac);
        $a->where('a1.NoArquivo != ?', '');


        $b = $this->select();
        $b->setIntegrityCheck(false);
        $b->from(
            array('a2' => 'tbDocumentosPreProjeto'),
            array("idDocumentosPreprojetos as idDocumento",
                "NoArquivo as nome",
                new Zend_Db_Expr("'antigo-preprojeto' as tipoDocumento"),
                new Zend_Db_Expr("'Anexado pelo Proponente' as Classificacao")
            ),
            'SAC.dbo'
        );
        $b->joinInner(
            array('pr' => 'Projetos'),
            "pr.idProjeto = a2.idProjeto",
            array("IdPRONAC as idpronac"),
            'SAC.dbo'
        );
        $b->where('pr.IdPRONAC = ?', $idPronac);
        $b->where('a2.NoArquivo != ?', '');


        $c = $this->select();
        $c->setIntegrityCheck(false);
        $c->from(
            array('a3' => 'tbDocumento'),
            array("idDocumento",
                "NoArquivo as nome",
                new Zend_Db_Expr("'docProp' as tipoDocumento"),
                new Zend_Db_Expr("'Anexado pelo Proponente' as Classificacao"),
                "idPronac as idpronac"
            ),
            'SAC.dbo'
        );
        $c->where('a3.idPronac = ?', $idPronac);
        $c->where('a3.NoArquivo != ?', '');


        $d = $this->select();
        $d->setIntegrityCheck(false);
        $d->from(
            array('a4' => 'tbDocumento'),
            array("idDocumento",
                new Zend_Db_Expr("'antigo-preprojeto' as tipoDocumento"),
                new Zend_Db_Expr("'Anexado pelo Proponente' as Classificacao")
            ),
            'BDCORPORATIVO.scCorp'
        );
        $d->joinInner(
            array('a5' => 'tbArquivo'),
            "a4.idArquivo = a5.idArquivo",
            array("nmArquivo as nome"),
            'BDCORPORATIVO.scCorp'
        );
        $d->joinInner(
            array('tap' => 'tbDocumentoProjeto'),
            "tap.idDocumento = a4.idDocumento",
            array("idpronac"),
            'BDCORPORATIVO.scCorp'
        );
        $d->where('tap.idpronac = ?', $idPronac);
        $d->where('a5.nmArquivo != ?', '');

        $slctUnion = $this->select()
            ->union(array('(' . $a . ')', '(' . $b . ')', '(' . $c . ')', '(' . $d . ')'))
            ->order('3', '1');

        return $this->fetchAll($slctUnion);
    }
}
